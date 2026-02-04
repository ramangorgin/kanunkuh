<?php

/**
 * User educational history management and certificate handling.
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;
use App\Models\EducationalHistory;
use App\Models\FederationCourse;

/**
 * Manages CRUD operations for user education records.
 */
class EducationalHistoryController extends Controller
{
    /**
     * Display the user's educational history list.
     */
    public function index()
    {
        $user = Auth::user();

        $histories = EducationalHistory::where('user_id', $user->id)
            ->with('federationCourse')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $federationCourses = FederationCourse::orderBy('title', 'asc')->get();

        return view('user.myEducationalHistories', compact('histories', 'federationCourses'));
    }

    /**
        * Create new educational history entries.
     */
    public function store(Request $request)
    {
        // Normalize _custom sentinel to null for validation
        if ($request->has('courses')) {
            $courses = $request->input('courses', []);
            foreach ($courses as $idx => $c) {
                if (($c['federation_course_id'] ?? null) === '_custom') {
                    $courses[$idx]['federation_course_id'] = null;
                }
                if (isset($courses[$idx]['custom_course_title'])) {
                    $courses[$idx]['custom_course_title'] = trim((string)$courses[$idx]['custom_course_title']);
                }
            }
            $request->merge(['courses' => $courses]);
        } else {
            if ($request->input('federation_course_id') === '_custom') {
                $request->merge(['federation_course_id' => null]);
            }
            if ($request->has('custom_course_title')) {
                $request->merge(['custom_course_title' => trim((string)$request->input('custom_course_title'))]);
            }
        }

        $messages = [
            'courses.required' => 'حداقل یک ردیف دوره باید اضافه شود.',
            'courses.*.federation_course_id.exists' => 'دوره انتخاب‌شده معتبر نیست.',
            'courses.*.custom_course_title.required_without' => 'در صورت انتخاب نکردن دوره از لیست، نام دوره سفارشی الزامی است.',
            'courses.*.custom_course_title.max' => 'نام دوره سفارشی نباید بیش از ۲۵۵ کاراکتر باشد.',
            'courses.*.certificate_file.mimes' => 'نوع فایل مدرک مجاز نیست (فقط JPG, PNG, PDF).',
            'courses.*.certificate_file.max' => 'حجم فایل مدرک بیش از حد مجاز است.',

            'federation_course_id.exists' => 'دوره انتخاب‌شده معتبر نیست.',
            'custom_course_title.required_without' => 'در صورت انتخاب نکردن دوره از لیست، نام دوره سفارشی الزامی است.',
            'custom_course_title.max' => 'نام دوره سفارشی نباید بیش از ۲۵۵ کاراکتر باشد.',
            'certificate_file.mimes' => 'نوع فایل مدرک مجاز نیست (فقط JPG, PNG, PDF).',
            'certificate_file.max' => 'حجم فایل مدرک بیش از حد مجاز است.',
        ];

        if ($request->has('courses')) {
            $request->validate([
                'courses' => ['required', 'array', 'min:1'],
                'courses.*.federation_course_id' => 'nullable|exists:federation_courses,id|required_without:courses.*.custom_course_title',
                'courses.*.custom_course_title'  => 'nullable|string|max:255|required_without:courses.*.federation_course_id',
                'courses.*.issue_date'           => 'nullable|string',
                'courses.*.certificate_file'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ], $messages);
        } else {
            $request->validate([
                'federation_course_id' => 'nullable|exists:federation_courses,id|required_without:custom_course_title',
                'custom_course_title'  => 'nullable|string|max:255|required_without:federation_course_id',
                'issue_date'           => 'nullable|string',
                'certificate_file'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ], $messages);
        }

        $user = Auth::user();

        $hadNoHistory = !EducationalHistory::where('user_id', $user->id)->exists();

        if ($request->has('courses')) {
            foreach ($request->courses as $index => $courseData) {
                $issueDate = null;
                if (!empty($courseData['issue_date'])) {
                    try {
                        $issueDate = Jalalian::fromFormat('Y/m/d', $this->convertToEnglish($courseData['issue_date']))->toCarbon()->toDateString();
                    } catch (\Exception $e) {
                        return back()->withErrors(["courses.$index.issue_date" => 'تاریخ وارد شده معتبر نیست. لطفاً تاریخ را به فرمت YYYY/MM/DD وارد کنید.'])->withInput();
                    }
                }
                $filePath = null;
                $file = data_get($request->allFiles(), "courses.$index.certificate_file");

                if ($file) {
                    $filePath = $file->store('educational_certificates', 'public');
                }
   
                EducationalHistory::create([
                    'user_id'             => $user->id,
                    'federation_course_id'=> !empty($courseData['federation_course_id']) && $courseData['federation_course_id'] !== '_custom' ? (int) $courseData['federation_course_id'] : null,
                    'custom_course_title' => !empty($courseData['custom_course_title']) ? trim($courseData['custom_course_title']) : null,
                    'certificate_file'    => $filePath,
                    'issue_date'          => $issueDate,
                ]);
            }
        } else {
            $issueDate = null;
            if ($request->filled('issue_date')) {
                try {
                    $issueDate = Jalalian::fromFormat('Y/m/d', $this->convertToEnglish($request->issue_date))->toCarbon()->toDateString();
                } catch (\Exception $e) {
                    return back()->withErrors(['issue_date' => 'تاریخ وارد شده معتبر نیست. لطفاً تاریخ را به فرمت YYYY/MM/DD وارد کنید.'])->withInput();
                }
            }
            $filePath = null;
            if ($request->hasFile('certificate_file')) {
                $filePath = $request->file('certificate_file')->store('educational_certificates', 'public');
            }
            EducationalHistory::create([
                'user_id'             => $user->id,
                'federation_course_id'=> $request->federation_course_id ?: null,
                'custom_course_title' => $request->custom_course_title ? trim($request->custom_course_title) : null,
                'certificate_file'    => $filePath,
                'issue_date'          => $issueDate,
            ]);
        }

        if (session('onboarding') || $hadNoHistory) {
            session()->forget('onboarding');
            return redirect()
                ->route('dashboard.index')
                ->with('success', 'اطلاعات شما دریافت شد. منتظر تایید مدیر بمانید.');
        }

        return redirect()->back()->with('success', 'سابقه آموزشی با موفقیت ثبت شد.');
    }

    /**
        * Update an existing educational history record.
     */
    public function update(Request $request, $id)
    {
        $history = EducationalHistory::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Normalize _custom sentinel to null for validation
        if ($request->input('federation_course_id') === '_custom') {
            $request->merge(['federation_course_id' => null]);
        }
        if ($request->has('custom_course_title')) {
            $request->merge(['custom_course_title' => trim((string)$request->input('custom_course_title'))]);
        }

        $request->validate([
            'federation_course_id' => 'nullable|exists:federation_courses,id|required_without:custom_course_title',
            'custom_course_title'  => 'nullable|string|max:255|required_without:federation_course_id',
            'issue_date'           => 'nullable|string',
            'certificate_file'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $issueDate = $history->issue_date;
        if ($request->filled('issue_date')) {
            try {
                $issueDate = Jalalian::fromFormat('Y/m/d', $this->convertToEnglish($request->issue_date))->toCarbon()->toDateString();
            } catch (\Exception $e) {
                return back()->withErrors(['issue_date' => 'تاریخ وارد شده معتبر نیست'])->withInput();
            }
        }

        if ($request->hasFile('certificate_file')) {
            if ($history->certificate_file && Storage::disk('public')->exists($history->certificate_file)) {
                Storage::disk('public')->delete($history->certificate_file);
            }

            $filePath = $request->file('certificate_file')->store('educational_certificates', 'public');
            $history->certificate_file = $filePath;
        }

        $history->update([
            'federation_course_id' => !empty($request->federation_course_id) && $request->federation_course_id !== '_custom' ? (int) $request->federation_course_id : null,
            'custom_course_title'  => !empty($request->custom_course_title) ? trim($request->custom_course_title) : null,
            'issue_date'           => $issueDate,
            'certificate_file'     => $filePath ?? $history->certificate_file,
        ]);


        return redirect()->back()->with('success', 'سابقه آموزشی با موفقیت به‌روزرسانی شد.');
    }

    /**
        * Delete an educational history record.
     */
    public function destroy($id)
    {
        $history = EducationalHistory::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($history->certificate_file && Storage::disk('public')->exists($history->certificate_file)) {
            Storage::disk('public')->delete($history->certificate_file);
        }

        $history->delete();

        return redirect()->back()->with('success', 'سابقه آموزشی با موفقیت حذف شد.');
    }

    /**
        * Convert Persian digits to ASCII digits.
     */
    private function convertToEnglish($string)
    {
        return strtr($string, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        ]);
    }
}
