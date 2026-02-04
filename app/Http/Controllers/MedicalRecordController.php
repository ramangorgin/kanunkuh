<?php

/**
 * User medical record display and update handling.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;
use App\Models\MedicalRecord;

/**
 * Manages creation and updates of the user's medical record.
 */
class MedicalRecordController extends Controller
{
    /**
     * Show the current user's medical record.
     */
    public function show()
    {
        $user = Auth::user();
        $medical = $user->medicalRecord; 
        return view('user.myMedicalRecord', compact('medical'));
    }

    /**
     * Validate and persist medical record updates.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $medical = $user->medicalRecord;

        $validated = $request->validate([
            'insurance_issue_date' => ['nullable', 'string'],
            'insurance_expiry_date'=> ['nullable', 'string'],
            'insurance_file'       => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'blood_type'           => ['nullable', 'in:O+,O-,A+,A-,B+,B-,AB+,AB-'],
            'height'               => ['nullable', 'integer', 'min:50', 'max:250'],
            'weight'               => ['nullable', 'integer', 'min:20', 'max:250'],
            'commitment_signed'    => ['required', 'boolean'],
        ]);

        if ($request->insurance_issue_date) {
            $date = $this->fixPersianNumbers($request->insurance_issue_date);
            try {
                $validated['insurance_issue_date'] =
                    Jalalian::fromFormat('Y/m/d', $date)->toCarbon()->format('Y-m-d');
            } catch (\Exception $e) {
                $validated['insurance_issue_date'] = null;
            }
        }

        if ($request->insurance_expiry_date) {
            $date = $this->fixPersianNumbers($request->insurance_expiry_date);
            try {
                $validated['insurance_expiry_date'] =
                    Jalalian::fromFormat('Y/m/d', $date)->toCarbon()->format('Y-m-d');
            } catch (\Exception $e) {
                $validated['insurance_expiry_date'] = null;
            }
        }

        if ($request->hasFile('insurance_file')) {
            $validated['insurance_file'] = $request->file('insurance_file')->store('insurance', 'public');
        }

        $validated = array_merge(
            $validated,
            $request->except(['insurance_file', '_token', '_method', 'insurance_issue_date', 'insurance_expiry_date'])
        );

        if (!$medical) {
            $medical = new MedicalRecord($validated);
            $user->medicalRecord()->save($medical);
        } else {
            $medical->update($validated);
        }


        if (session('onboarding') || !auth()->user()->educationalHistories()->exists()) {
            return redirect()
                ->route('dashboard.educationalHistory.index')
                ->with('onboarding', true)
                ->with('success', 'پرونده پزشکی با موفقیت ذخیره شد. لطفاً سوابق آموزشی را تکمیل کنید.');
        }

        return redirect()->back()->with('success', 'پرونده پزشکی با موفقیت به‌روزرسانی شد.');

    }

    /**
     * Convert Persian digits to ASCII digits.
     */
    private function fixPersianNumbers($string)
    {
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];
        return str_replace($persian, $english, $string);
    }

}
