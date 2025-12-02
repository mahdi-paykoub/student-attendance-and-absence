<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{



    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'none',
        ]);

        return redirect()->route('users.index')->with('success', 'کاربر با موفقیت ایجاد شد');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('users.index')->with('success', 'کاربر با موفقیت ویرایش شد.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'کاربر با موفقیت حذف شد.');
    }
    public function makeAdmin(User $user)
    {
        if ($user->role == 'suporter') {
            return redirect()->back()->with('info', 'کاربر پشتیبان به‌دلایل امنیتی نم‌تواند ادمین شود.');
        } elseif ($user->role == 'none') {
            $user->role = 'admin';
            $user->save();
            return redirect()->back()->with('success', 'کاربر به ادمین تبدیل شد.');
        } elseif ($user->role == 'admin') {
            return redirect()->back()->with('info', 'این کاربر قبلاً ادمین است.');
        }
    }

    public function makeSuporter(User $user)
    {
        if ($user->role == 'suporter') {
            return redirect()->back()->with('نقش کاربر از قبل پشتیبان است');
        } elseif ($user->role == 'none') {
            $user->role = 'suporter';
            $user->save();
            return redirect()->back()->with('success', 'کاربر به پشتیبان تبدیل شد.');
        } elseif ($user->role == 'admin') {
            return redirect()->back()->with('info', 'کاربر ادمین به‌دلایل امنیتی نمی‌تواند پشتیبان شود.');
        }
    }
}
