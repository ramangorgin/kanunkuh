<?php

/**
 * Controller for administrating user records and profiles.
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


/**
 * Provides CRUD and listing operations for application users.
 */
class UserController extends Controller
{
    /**
     * Display a paginated list of users with optional search filtering.
     */
    public function index(Request $request)
    {
        $query = User::with('profile');
    
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ["%$search%"]);
            });
        }
    
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
    
        return view('admin.users.index', compact('users'));
    }
    

    /**
     * Show the user creation form for administrators.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Show the edit form for a specific user.
     */
    public function edit($id)
    {
        $user = User::with('profile')->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Remove a user and associated profile photo from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->profile && $user->profile->photo) {
            Storage::disk('public')->delete($user->profile->photo);
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'کاربر حذف شد.');
    }

    /**
     * Display the details for a specific user.
     */
    public function show($id)
    {
        $user = User::with(['profile'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }
}
