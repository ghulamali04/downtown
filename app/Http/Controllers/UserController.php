<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function get_data(DataTables $dataTables)
    {
        $users = User::where('role', '!=', 'superadmin')
        ->where('id', '!=', Auth::user()->id);
        return $dataTables
        ->eloquent($users)
        ->addColumn('timestamp', function ($user) {
            return date("d/m/Y H:iA", strtotime($user->created_at));
        })
            ->addColumn('action', function ($user) {
                return $user->id;
            })
            ->toJson();
    }

    public function index()
    {
        return view("users.index");
    }

    public function create()
    {
        return view("users.create");
    }

    public function edit(User $user)
    {
        return view("users.edit", compact("user"));
    }

    public function store(Request $request, CreateNewUser $creater)
    {
        $user = $creater->create($request->except('_token'));

        if($user) {
            return redirect()->back()->with('success', "User successfuly created");
        }
        return redirect()->back()->with('error', "Enable to create new user");
    }

    public function update(Request $request, User $user, UpdateUserProfileInformation $updater)
    {
        $updater->update($user, $request->except('_token'));
        return redirect()->back()->with('success', "user successfully update");
    }

    public function change_password(Request $request, User $user)
    {
        Validator::make($request->except('_token'), [
            'password' => ['required', 'string', 'min:6'],
            'password_confirmation' => ['required', 'same:password'],
        ])->validate();

        $user->forceFill([
            'password' => Hash::make($request->input('password'))
        ]);

        return redirect()->back()->with('success', "User password succesfully updated");
    }

    public function destroy($user)
    {
        $user = User::findOrFail($user);
        if($user->role !== 'superadmin') {
            $user->delete();
            return new JsonResponse([
                "status" => "success",
                "message" => "User successfully deleted"
            ], 200);
        }
        return new JsonResponse([
            "status" => "error",
            "message" => "unable to delete user"
        ], 400);
    }
}
