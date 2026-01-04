<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\EducationalHistory;
use App\Models\MedicalRecord;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class AdminUserController extends Controller
{
    private function jalaliToGregorian(?string $val): ?string
    {
        if (!$val) return null;
        // Normalize Persian/Arabic digits to English
        $map = ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
                '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9'];
        $val = str_replace(array_keys($map), array_values($map), trim($val));

        // Attempt to parse as Jalali first (format YYYY/MM/DD)
        if (preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $val)) {
            try {
                return \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $val)->toCarbon()->format('Y-m-d');
            } catch (\Throwable $e) {
                // It might be a Gregorian date with slashes, fall through
            }
        }

        // Fallback for other formats (like YYYY-MM-DD or Gregorian with slashes)
        try {
            return \Carbon\Carbon::parse($val)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null; // Return null if parsing fails completely
        }
    }

    private function showJalali(?string $date): string
    {
        if (!$date) return '';
        $date = trim($date);

        // If it already looks like a Jalali date string (e.g., 1383/03/14), return it directly.
        if (preg_match('/^(1[345])\d{2}\/\d{2}\/\d{2}$/', $date)) {
            return $date;
        }

        // Otherwise, assume it's a Gregorian date (e.g., 2004-06-03) and convert it.
        try {
            return \Morilog\Jalali\Jalalian::fromDateTime(\Carbon\Carbon::parse($date))->format('Y/m/d');
        } catch (\Throwable $e) {
            return $date; // If conversion fails, return the original string.
        }
    }

    /** نمایش لیست کاربران **/
    public function index(Request $request)
    {
        $query = User::with('profile');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('profile', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%");
            })->orWhere('phone', 'like', "%$search%");
        }

        $users = $query->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /** فرم ایجاد کاربر **/
    public function create()
    {
        $user = new User();
        $jalali = []; // empty on create
        return view('admin.users.create', compact('user','jalali'));
    }

    /** ذخیره کاربر جدید **/
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|unique:users,phone',
            'role'  => 'nullable|in:member,admin',
            'status'=> 'nullable|in:active,inactive',
            'first_name' => 'required',
            'last_name' => 'required',
            'national_id' => 'required',
            'photo' => 'nullable|image|max:4096',
            'national_card' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'insurance_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        DB::transaction(function () use ($request) {

            // create user
            $user = User::create([
                'phone' => $request->phone,
                'role' => $request->role ?? 'member',
                'status' => $request->status ?? 'active',
            ]);

            $userBase = "users/{$user->id}";

            // build profile data and convert Jalali dates to Gregorian
            $profileData = [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'father_name' => $request->father_name,
                'id_number' => $request->id_number,
                'id_place' => $request->id_place,
                'birth_date' => $this->jalaliToGregorian($request->input('birth_date')),
                'national_id' => $request->national_id,
                'membership_id' => Profile::generateMembershipId(),
                'membership_status' => $request->membership_status ?? 'pending',
                'membership_type' => $request->membership_type,
                'membership_start' => $this->jalaliToGregorian($request->input('membership_start')),
                'membership_expiry' => $this->jalaliToGregorian($request->input('membership_expiry')),
                'leave_date' => $this->jalaliToGregorian($request->input('leave_date')),
                'marital_status' => $request->marital_status,
                'emergency_phone' => $request->emergency_phone,
                'referrer' => $request->referrer,
                'education' => $request->education,
                'job' => $request->job,
                'home_address' => $request->home_address,
                'work_address' => $request->work_address,
            ];

            // files for profile
            if ($request->hasFile('photo')) {
                $profileData['photo'] = $request->file('photo')->store("{$userBase}/profile", 'public');
            }
            if ($request->hasFile('national_card')) {
                $profileData['national_card'] = $request->file('national_card')->store("{$userBase}/profile", 'public');
            }

            $profile = Profile::create($profileData);

            // medical record: collect booleans and details explicitly, convert dates
            $medical = new MedicalRecord();
            $medical->user_id = $user->id;

            $boolFields = [
                'head_injury','eye_ear_problems','seizures','respiratory','heart','blood_pressure',
                'blood_disorders','diabetes_hepatitis','stomach','kidney','mental','addiction',
                'surgery','skin_allergy','drug_allergy','insect_allergy','dust_allergy',
                'medications','bone_joint','hiv','treatment'
            ];
            foreach ($boolFields as $f) {
                $medical->{$f} = $request->has($f) ? 1 : 0;
                // details (if any) are named e.g. head_injury_details
                $medical->{$f . '_details'} = $request->input($f . '_details');
            }

            // other simple fields
            $medical->blood_type = $request->input('blood_type');
            $medical->height = $request->input('height');
            $medical->weight = $request->input('weight');

            // insurance dates -> convert Jalali to Gregorian
            $medical->insurance_issue_date = $this->jalaliToGregorian($request->input('insurance_issue_date'));
            $medical->insurance_expiry_date = $this->jalaliToGregorian($request->input('insurance_expiry_date'));

            // store insurance file
            if ($request->hasFile('insurance_file')) {
                $medical->insurance_file = $request->file('insurance_file')->store("{$userBase}/medical", 'public');
            }

            // other_conditions (always present textarea)
            $medical->other_conditions = $request->input('other_conditions');

            $medical->save();

            // educational histories
            if ($request->has('educations')) {
                $eduFiles = $request->file('educations') ?: [];

                foreach ($request->educations as $index => $edu) {
                    $courseId = $edu['federation_course_id'] ?? null;
                    $customTitle = $edu['custom_course_title'] ?? null;

                    // allow custom course selection
                    if ($courseId === '_custom') {
                        $courseId = null;
                    }

                    // skip empty rows
                    if (!$courseId && !$customTitle) {
                        continue;
                    }

                    $education = new EducationalHistory([
                        'user_id' => $user->id,
                        'federation_course_id' => $courseId,
                        'custom_course_title' => $customTitle,
                        'issue_date' => $this->jalaliToGregorian($edu['issue_date'] ?? null),
                    ]);

                    $certificate = null;
                    if (isset($eduFiles[$index]['certificate_file']) && $eduFiles[$index]['certificate_file'] instanceof \Illuminate\Http\UploadedFile) {
                        $certificate = $eduFiles[$index]['certificate_file']->store("{$userBase}/education/certificates", 'public');
                    } elseif (isset($edu['certificate_file']) && $edu['certificate_file'] instanceof \Illuminate\Http\UploadedFile) {
                        $certificate = $edu['certificate_file']->store("{$userBase}/education/certificates", 'public');
                    }

                    if ($certificate) {
                        $education->certificate_file = $certificate;
                    }

                    $education->save();
                }
            }
        });

        return redirect()->route('admin.users.index')->with('success', 'کاربر جدید با موفقیت ایجاد شد ✅');
    }

    /** فرم ویرایش **/
    public function edit($id)
    {
        $user = User::with(['profile','medicalRecord','educationalHistories'])->findOrFail($id);
        $p = $user->profile ?? new Profile();
        $m = $user->medicalRecord ?? new MedicalRecord();

        $jalali = [
            'birth_date'            => $this->showJalali($p->birth_date),
            'membership_start'      => $this->showJalali($p->membership_start),
            'membership_expiry'     => $this->showJalali($p->membership_expiry),
            'leave_date'            => $this->showJalali($p->leave_date),
            'insurance_issue_date'  => $this->showJalali($m->insurance_issue_date),
            'insurance_expiry_date' => $this->showJalali($m->insurance_expiry_date),
        ];

        return view('admin.users.edit', compact('user','jalali'));
    }

    /** بروزرسانی اطلاعات **/
    public function update(Request $request, $id)
    {
        $user = User::with(['profile', 'medicalRecord', 'educationalHistories'])->findOrFail($id);

        $request->validate([
            'phone' => 'required|unique:users,phone,' . $user->id,
            'role'  => 'nullable|in:member,admin',
            'status'=> 'nullable|in:active,inactive',
            'first_name' => 'required',
            'last_name' => 'required',
            'photo' => 'nullable|image|max:4096',
            'national_card' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
            'insurance_file' => 'nullable|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        DB::transaction(function () use ($request, $user) {

            $user->update([
                'phone' => $request->phone,
                'role' => $request->role ?? $user->role,
                'status' => $request->status ?? $user->status,
            ]);

            $userBase = "users/{$user->id}";

            // profile fields: convert dates if present
            $profileData = $request->only([
                'first_name', 'last_name', 'father_name', 'id_number', 'id_place', 'national_id',
                'membership_status', 'membership_type', 'marital_status', 'emergency_phone',
                'referrer', 'education', 'job', 'home_address', 'work_address'
            ]);

            // handle date conversions explicitly
            $profileData['birth_date'] = $this->jalaliToGregorian($request->input('birth_date'));
            $profileData['membership_start'] = $this->jalaliToGregorian($request->input('membership_start'));
            $profileData['membership_expiry'] = $this->jalaliToGregorian($request->input('membership_expiry'));
            $profileData['leave_date'] = $this->jalaliToGregorian($request->input('leave_date'));

            // handle profile files
            if ($request->hasFile('photo')) {
                $profileData['photo'] = $request->file('photo')->store("{$userBase}/profile", 'public');
            }
            if ($request->hasFile('national_card')) {
                $profileData['national_card'] = $request->file('national_card')->store("{$userBase}/profile", 'public');
            }

            $user->profile->update($profileData);

            // medical record update (ensure booleans and details handled)
            if ($user->medicalRecord) {
                $medical = $user->medicalRecord;

                $boolFields = [
                    'head_injury','eye_ear_problems','seizures','respiratory','heart','blood_pressure',
                    'blood_disorders','diabetes_hepatitis','stomach','kidney','mental','addiction',
                    'surgery','skin_allergy','drug_allergy','insect_allergy','dust_allergy',
                    'medications','bone_joint','hiv','treatment'
                ];
                foreach ($boolFields as $f) {
                    $medical->{$f} = $request->has($f) ? 1 : 0;
                    $medical->{$f . '_details'} = $request->input($f . '_details');
                }

                $medical->blood_type = $request->input('blood_type');
                $medical->height = $request->input('height');
                $medical->weight = $request->input('weight');

                $medical->insurance_issue_date = $this->jalaliToGregorian($request->input('insurance_issue_date'));
                $medical->insurance_expiry_date = $this->jalaliToGregorian($request->input('insurance_expiry_date'));

                if ($request->hasFile('insurance_file')) {
                    $medical->insurance_file = $request->file('insurance_file')->store("{$userBase}/medical", 'public');
                }

                $medical->other_conditions = $request->input('other_conditions');

                $medical->save();
            }

            // rewrite educational histories
            $user->educationalHistories()->delete();
            if ($request->has('educations')) {
                $eduFiles = $request->file('educations') ?: [];

                foreach ($request->educations as $index => $edu) {
                    $courseId = $edu['federation_course_id'] ?? null;
                    $customTitle = $edu['custom_course_title'] ?? null;

                    if ($courseId === '_custom') {
                        $courseId = null;
                    }

                    if (!$courseId && !$customTitle) {
                        continue;
                    }

                    $education = new EducationalHistory([
                        'user_id' => $user->id,
                        'federation_course_id' => $courseId,
                        'custom_course_title' => $customTitle,
                        'issue_date' => $this->jalaliToGregorian($edu['issue_date'] ?? null),
                    ]);

                    $certificate = null;
                    if (isset($eduFiles[$index]['certificate_file']) && $eduFiles[$index]['certificate_file'] instanceof \Illuminate\Http\UploadedFile) {
                        $certificate = $eduFiles[$index]['certificate_file']->store("{$userBase}/education/certificates", 'public');
                    } elseif (isset($edu['certificate_file']) && $edu['certificate_file'] instanceof \Illuminate\Http\UploadedFile) {
                        $certificate = $edu['certificate_file']->store("{$userBase}/education/certificates", 'public');
                    }

                    if ($certificate) {
                        $education->certificate_file = $certificate;
                    }

                    $education->save();
                }
            }
        });

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'اطلاعات کاربر با موفقیت بروزرسانی شد ✅');
    }

    /** حذف کاربر **/
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /** خروجی اکسل **/
    public function export()
    {
        return Excel::download(new UsersExport, 'users.xlsx');
    }

    /** نمایش جزئیات یک کاربر **/
    public function show($id)
    {
        $user = User::with([
            'profile',
            'medicalRecord',
            'educationalHistories',
            'payments', // چون در show.blade.php استفاده شده
        ])->findOrFail($id);

        $recentTickets = Ticket::where('user_id', $user->id)
            ->latest()
            ->with('latestMessage')
            ->take(5)
            ->get();

        return view('admin.users.show', compact('user', 'recentTickets'));
    }

    /** عضویت‌های در انتظار (اختیاری اگر لازم دارید) **/
    public function pendingMemberships()
    {
        $pendingProfiles = Profile::where('membership_status', 'pending')
            ->with('user')
            ->latest()
            ->get(); // or paginate(10) if you prefer

        return view('admin.users.pending', compact('pendingProfiles'));
    }

    /** تایید عضویت **/
    public function approveMembership($profileId)
    {
        $profile = Profile::findOrFail($profileId);
        $profile->membership_status = 'approved';
        $profile->save();

        return response()->json(['success' => true]);
    }

    /** رد عضویت **/
    public function rejectMembership($profileId)
    {
        $profile = Profile::findOrFail($profileId);
        $profile->membership_status = 'rejected';
        $profile->save();

        return response()->json(['success' => true]);
    }
}
