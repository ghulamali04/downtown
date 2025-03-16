<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $user = User::where('id', Auth::user()->id)->first();
        return view("account.index", compact('user'));
    }
    public function update(Request $request, UpdateUserProfileInformation $updater)
    {
        $user = User::where('id', Auth::user()->id)->first();
        $updater->update($user, $request->except('_token'));
        return redirect()->back()->with('success', 'Your profile information successfully updated');
    }
    public function change_password(Request $request, UpdateUserPassword $updater)
    {
        $user = User::where('id', Auth::user()->id)->first();
        $updater->update($user, $request->except('_token'));
        return redirect()->back()->with('success', 'You have successfully changed your password');
    }
}
