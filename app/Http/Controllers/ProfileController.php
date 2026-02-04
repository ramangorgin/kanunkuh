<?php

/**
 * User profile management and onboarding flow.
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;



/**
 * Handles profile creation, updates, and related onboarding redirects.
 */
class ProfileController extends Controller
{
    /**
     * Display the current user's profile page.
     */
    public function show()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new Profile();
        return view('user.myProfile', compact('user', 'profile'));
    }

    /**
     * Store a new profile or update the existing one.
     */
    public function store(Request $request)
    {
    
        $user = Auth::user();

        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'father_name' => 'nullable|string',
            'gender' => 'nullable|string',
            'birth_date' => 'required|string',
            'national_id' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'phone' => 'nullable|string',
            'province' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'job' => 'nullable|string',
            'referrer' => 'nullable|string',
            'blood_type' => 'nullable|string',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'medications' => 'nullable|string',
            'had_surgery' => 'nullable|boolean',
            'emergency_phone' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_relation' => 'nullable|string',
        ];

        
        $validated = $request->validate($rules);

        $birthDateEnglish = $this->convertNumbersToEnglish($validated['birth_date']);

        $birthDateRaw = $validated['birth_date'] ?? null;

        if ($birthDateRaw && strlen(trim($birthDateRaw)) >= 8) {
            try {
                $birthDateEnglish = $this->convertNumbersToEnglish($birthDateRaw);

                $validated['birth_date'] = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $birthDateEnglish)
                    ->toCarbon()
                    ->format('Y-m-d'); 
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['birth_date' => 'تاریخ تولد معتبر نیست.'])->withInput();
            }
        } else {
            $validated['birth_date'] = null;
        }


        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }

        if ($request->hasFile('national_card')) {
            $validated['national_card'] = $request->file('national_card')->store('national_cards', 'public');
        }

        $profile = $user->profile;
        if ($profile) {
            $profile->update($validated);
        } else {
            $user->profile()->create($validated);
        }

        return redirect()->back()->with('success', 'مشخصات با موفقیت ذخیره شد.');
    }


    /**
     * Update the authenticated user's profile data.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->id != $id) {
            abort(403, 'شما مجاز به ویرایش این پروفایل نیستید.');
        }

        $profile = $user->profile;

        $rules = [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'father_name' => 'nullable|string',
            'id_number' => 'nullable|string',
            'id_place' => 'nullable|string',
            'birth_date' => 'nullable|string',
            'national_id' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'national_card' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'marital_status' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
            'referrer' => 'nullable|string',
            'education' => 'nullable|string',
            'job' => 'nullable|string',
            'home_address' => 'nullable|string',
            'work_address' => 'nullable|string',
        ];

        if (!$profile) {
            $rules['photo'] = 'required|image|max:2048';
            $rules['national_card'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:4096';
            $rules['national_id'] = 'required|string|size:10';
        }

        $validated = $request->validate($rules);

        foreach (['national_id','id_number','emergency_phone'] as $numField) {
            if (!empty($validated[$numField])) {
                $validated[$numField] = en_digits($validated[$numField]);
            }
        }

        if (!empty($validated['birth_date'])) {
            $validated['birth_date'] = en_digits($validated['birth_date']);
            try {
                $validated['birth_date'] = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $validated['birth_date'])
                    ->toCarbon()
                    ->format('Y-m-d');
            } catch (\Throwable $e) {
                return redirect()->back()->withErrors(['birth_date' => 'تاریخ تولد معتبر نیست.'])->withInput();
            }
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }

        if ($request->hasFile('national_card')) {
            $validated['national_card'] = $request->file('national_card')->store('national_cards', 'public');
        }

        if (!$profile) {
            $validated['membership_id'] = method_exists(Profile::class, 'generateMembershipId')
                ? Profile::generateMembershipId()
                : (int) (time() . rand(100, 999));
            $profile = $user->profile()->create($validated);
        } else {
            $profile->update($validated);
        }

        if (session('onboarding') || !auth()->user()->medicalRecord) {
            return redirect()
                ->route('dashboard.medicalRecord.edit')
                ->with('onboarding', true)
                ->with('success', 'مشخصات با موفقیت ذخیره شد. لطفاً پرونده پزشکی را تکمیل کنید.');
        }

        return redirect()->back()->with('success', 'مشخصات با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Convert Persian digits to ASCII digits.
     */
    private function convertNumbersToEnglish($string)
    {
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];
        return str_replace($persian, $english, $string);
    }

    /**
     * Redirect to the medical record step if required by onboarding.
     */
    public function updateMedicalRecord(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->hasMedicalRecord()) {
            return redirect()->route('dashboard.medicalRecord.edit');
        }
        return redirect()->route('dashboard.index')->with('success', 'اطلاعات ذخیره شد');
    }
    /**
     * Show the profile edit form for the authenticated user.
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new Profile();
        return view('user.myProfile', compact('user', 'profile'));
    }

}
