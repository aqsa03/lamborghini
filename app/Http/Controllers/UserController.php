<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Auth::user()->is_root() ? new User : User::noRoot();
        return view('users.index', [
            'total' => $users->count(),
            'users' => $users->paginate(20)
        ])
        ->with('i', (request()->input('page', 1) - 1) * 20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.form', [
            'formType' => 'create',
            'user' => null
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userCreate = new CreateNewUser();
        $requestData = $request->only([
            'name', 'email', 'password', 'password_confirmation', 'is_writer', 'is_editor', 'is_admin', 'is_root'
        ]);
        $requestData['is_writer'] = 1;
        $requestData['is_editor'] = empty($request->is_editor) ? 0 : 1;
        $requestData['is_admin'] = empty($request->is_admin) ? 0 : 1;
        $requestData['is_root'] = empty($request->is_root) ? 0 : 1;
        $newUser = $userCreate->create($requestData);
        if (empty($newUser)) {
            abort(500, "Error while creating a new user");
        } else {
            return redirect()->route('users.index')->with('success','User created successfully.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('users.show', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        Gate::authorize('update-user', $user);

        return view('users.form', [
            'formType' => 'edit',
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        Gate::authorize('update-user', $user);

        $updateProfile = new UpdateUserProfileInformation();
        $updateProfile->update($user, $request->only(['name', 'email']));
        $roletData['is_writer'] = 1;
        $roletData['is_editor'] = empty($request->is_editor) ? 0 : 1;
        $roletData['is_admin'] = empty($request->is_admin) ? 0 : 1;
        $roletData['is_root'] = empty($request->is_root) ? 0 : 1;
        $user->update($roletData);
        return redirect()->route('users.index')->with('success','User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        Gate::authorize('destroy-user', $user);

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }
}
