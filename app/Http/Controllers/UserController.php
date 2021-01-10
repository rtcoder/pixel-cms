<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Helpers\Helpers;
use App\Helpers\TableParamsHelper;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $tableParams = new TableParamsHelper($request);
        $authUser = Auth::user();
        $users = User::query();
        if (!$authUser->client->is_super_admin || !$tableParams->client_id) {
            $users = $users->where('client_id', $authUser->client_id);
        } else {
            $users = $users->where('client_id', $tableParams->client_id);
        }

        $users = $users->orderBy($tableParams->column, $tableParams->direction);

        if ($tableParams->search_term) {
            $users->where(function ($q) use ($tableParams) {
                $q->where('name', 'ilike', "%$tableParams->search_term%")
                    ->orWhere('email', 'ilike', "%$tableParams->search_term%");
            });
        }

        $per_page = $tableParams->limit != -1 ? $tableParams->limit : $users->count();

        $users = $users->paginate($per_page, ['*'], 'page', $tableParams->page_number);

        foreach ($users->items() as $item) {
            TableParamsHelper::filterResponseAttributes($item, $request->get('attributes'), User::class);
        }
//dd($users->toArray());
        return view('pages.users.users-list', [
            'users' => $users,
            'searchTerm' => $tableParams->search_term
        ]);
    }

    public function add()
    {
        $roles = Role::where('client_id', Auth::user()->client_id)->get();
        return view('pages.users.single-user', [
            'roles' => $roles
        ]);
    }

    public function edit(int $id)
    {
        $user = User::where([
            'id' => $id,
            'client_id' => Auth::user()->client_id
        ])->first();

        if (!$user) {
            abort(404);
        }

        $roles = Role::where('client_id', $user->client_id)->get();
        return view('pages.users.single-user', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    public function create(UserRequest $request): RedirectResponse
    {
        $auth = Auth::user();
        $user = new User();
        $user->fill($request->all());

        $password = Helpers::generatePassword();

        if ($auth->role->is_admin) {
            $user->role_id = $request->get('role_id');
        }

        $user->password = Hash::make($password);
        $user->client_id = $auth->client_id;
        $user->save();

        event(new UserCreated($user, $password));

        return redirect()->route('users');
    }

    public function update(UserRequest $request, int $id): RedirectResponse
    {
        $user = User::where([
            'id' => $id,
            'client_id' => Auth::user()->client_id
        ])->first();
        if (!$user) {
            abort(404);
        }
        $auth = Auth::user();
        if ($request->get('password'))
            $user->password = Hash::make($request->get('password'));

        $user->fill($request->all());
        if ($auth->role->is_admin && (!$user->role->is_admin || User::where('client_id', $auth->client_id)->whereHas('role', function ($q) {
                    $q->whereNull('client_id');
                })->count() > 1)) {
            $user->role_id = $request->get('role_id');
        }
        $user->save();
        return redirect()->route('users');
    }

    public function destroy(int $id)
    {
        $auth = Auth::user();
        $user = User::where([
            'id' => $id,
            'client_id' => $auth->client_id
        ])->first();
        if (!$auth->role->is_admin) {
            abort(403);
        }
        if (!$user) {
            abort(404);
        }
        $user->delete();
        return redirect()->route('users');
    }


//    public function requestPasswordReset(Request $request)
//    {
//        $user = User::where('email', $request->get('email'))->first();
//
//        if (!$user)
//            return response(null, 404);
//
//        $user->password_reset_token = Helpers::generatePassword(32);
//        $user->save();
//
//        Mail::send('emails.user_request_password_reset', [
//            'link' => env('APP_URL') . '/api/users/confirm_password_reset/' . $user->password_reset_token
//        ], function ($message) use ($user) {
//            $message->to($user->email)
//                ->subject('ArtSaaS account password reset request');
//            $message->from(env('MAIL_USERNAME'), 'ArtSaaS');
//        });
//
//        return response(null);
//    }

//    public function confirmPasswordReset(string $token)
//    {
//        $user = User::where('password_reset_token', $token)->first();
//        if (!$user || !$token)
//            return response(null, 404);
//
//        $password = Helpers::generatePassword();
//        $user->password_reset_token = null;
//        $user->password = Hash::make($password);
//        $user->save();
//
//        Mail::send('emails.user_confirm_password_reset', [
//            'password' => $password
//        ], function ($message) use ($user) {
//            $message->to($user->email)
//                ->subject('ArtSaaS account password reset confirmation');
//            $message->from(env('MAIL_USERNAME'), 'ArtSaaS');
//        });
//
//        return redirect(env('APP_URL') . ':4200');
//    }
}
