<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\comment;
use App\User;
use App\UsersProfiles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class users extends Controller
{

    static function getUserProfile($username)
    {
        $forResponse = User::where('name', $username)->first()->profile;
        $forResponse['email'] = User::select('email')->where('name', $username)->pluck('email')->first();
        return $forResponse;
    }

    public function login()
    {
        if (Auth::attempt(['name' => request('name'), 'password' => request('password')])) {
            $user = Auth::user();
            $forResponse ['token'] = $user->createToken('blog')->accessToken;
            $forResponse ['username'] = $user->name;
            $forResponse['status'] = User::select('status')->where('id', $user['id'])->pluck('status')->first();
            return response()->json(['response' => $forResponse], 200);
        } else {
            return response()->json(['errorApi' => 'Wrong password or username'], 401);
        }

    }

    public function register(Request $request)
    {
        $this->validate($request, ['name' => 'required|unique:users|max: 32|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'pass_repeat' => 'required|same:password']);
        $inputToUsers = $request->only('name', 'email');
        $inputToUsers['password'] = bcrypt($request['password']);
        $serviceUser = User::create($inputToUsers);
        $inputToProfile = $request->only('last_name', 'about', 'middle_name', 'first_name', 'avatar', 'name');
        $inputToProfile['user_id'] = $serviceUser->id;
        if($request->hasFile('avatar')){
            $avatar = $request->file('avatar');
        }
        $this->updateCreateProfile($inputToProfile, $avatar);
        return (new Response('Registered!', 200))->header('Location', 'http://localhost:4200');

    }

    public function cookiesLogin()
    {
        $user = Auth::user();
        $forResponse ['username'] = $user->name;
        $forResponse['status'] = User::select('status')->where('id', $user['id'])->pluck('status')->first();
        return response()->json($forResponse, 200)->header('Access-Control-Allow-Origin', 'http://localhost:4200');
    }

    public function getUsersAndProfiles(Request $request)
    {
        $offset = $request->pNum;
        $user = Auth::user();
        if ($user['status'] != 'admin') return response('', 401);
        $users = User::select('users.id', 'users.name', 'users.status as status',
            'users_profiles.about as about', 'users_profiles.avatar as avatar', 'users_profiles.first_name as first_name',
            'users.email as email', 'users_profiles.last_name as last_name', 'users_profiles.middle_name as middle_name')
            ->skip(($offset - 1) * 10)
            ->take(10)
            ->leftJoin('users_profiles', 'users.id', '=', 'users_profiles.user_id')
            ->orderBy('users.id', 'asc')
            ->get();
        return response()->json($users, 200);
    }

    protected function updateUser(Request $request)
    {
        $userId = $request->id;
        $this->validate($request, ['name' => ['required', Rule::unique('users')->ignore($userId), 'max: 32', 'min:4'],
            'email' => ['required', Rule::unique('users')->ignore($userId)],
            'password' => 'min:6',
            'status' => 'required'
        ]);
        $toUsers = $request->only('status', 'password', 'name', 'email');
        if ($toUsers['password'] != '') $toUsers['password'] = bcrypt($toUsers['password']);
        else unset($toUsers['password']);
        User::where('id', $userId)->update($toUsers);
        $toProfiles = $request->only('about', 'first_name', 'middle_name', 'last_name');
        $toProfiles['user_id'] = $userId;
        if($request->hasFile('avatar')){
            $avatar = $request->file('avatar');
        }
        $this->updateCreateProfile($toProfiles, $avatar);
        return response('ok', 200);
    }

    protected function deleteUser(Request $request)
    {
        $id = $request->id;
        $user = User::where('id', $id)->first();
        Storage::disk('public')->deleteDirectory('avatars/u' . $user['name']);
        $user->delete();
        $profile = UsersProfiles::where('user_id', $id)->first();
        if ($profile != null) {
            $profile->delete();
        }
        comment::where('user_id', $id)->delete();
    }

    protected function search($term)
    {
        $users = User::select('users.id', 'users.name')
            ->where('name', 'LIKE', $term . '%')
            ->take(10)->get();
        return response()->json($users, 200);
    }

    protected function profileRequestHandler(Request $request)
    {
        $data['user_id'] = Auth::id();
        $this->validate($request, ['email' => ['email', 'required', Rule::unique('users')->ignore(Auth::id())]]);
        $email = $request->email;
        User::where('id', $data['user_id'])->update(['email' => $email]);
        $data = array_merge($data, $request->only('about', 'first_name', 'middle_name', 'last_name'));
        $avatar = $request->file('avatar');
        $this->updateCreateProfile($data, $avatar);
    }

    protected function updateCreateProfile($data, $avatar = null)
    {
        if ($avatar != null) {
            $pathway = Storage::disk('public')->put('avatars/u' . $data['user_id'], $avatar);
            $data['avatar'] = Storage::disk('public')->url($pathway);
            images::avatarCrop($pathway);
        }
        $profile = UsersProfiles::where('user_id', $data['user_id'])->first();
        if ($profile == null) {
            $profile = UsersProfiles::create($data);
        } else $profile->update($data);
        return $profile->id;
    }
}
