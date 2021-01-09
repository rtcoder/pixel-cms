<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $returnTo = $request->get('return-to');
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        $foundUser = User::where('email', $request->get('email'))->select(['is_active'])->first();

        if (!$foundUser) {
            flash()->success(__('auth.failed'));
            return redirect()->back()
                ->withInput($request->input());
        }
        if (!$foundUser->is_active) {
            flash()->success(__('auth.inactive_account'));
            return redirect()->back()
                ->withInput($request->input());
        }

        if (Auth::attempt(['email' => $request->get('email'), 'password' => $request->get('password')])) {

            return $returnTo
                ? redirect($returnTo)
                : redirect()->route('home');
        }
        return view('pages.auth.login');
    }

//    public function register(Request $request)
//    {
//        $returnTo = $request->get('return-to');
//        $this->validate($request, [
//            'email' => 'required|email|unique:users',
//            'password' => 'required|min:3|confirmed',
//        ]);
//
//        $user = new User();
//
//        $user->name = $request->input('name');
//        $user->email = $request->input('email');
//        $user->password = Hash::make($request->input('password'));
//
//        $user->save();
//        return $returnTo
//            ? redirect()->route('login', ['return-to' => $returnTo])
//            : redirect()->route('login');
//    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
