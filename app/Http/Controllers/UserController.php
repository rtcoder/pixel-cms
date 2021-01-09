<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Helpers\TableParamsHelper;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

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

    public function show(User $user)
    {
        return response($user->toArray(), 200);
    }

    public function store(UserRequest $request)
    {
        $auth = Auth::user();
        $user = new User();
        $user->fill($request->all());

        $password = Helpers::generatePassword();
        if ($auth->role->is_admin)
            $user->role_id = $request->get('role_id');
        $user->password = Hash::make($password);
        $user->client_id = $auth->client_id;
        $user->save();

        Mail::send('emails.user_create', [
            'password' => $password
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('ArtSaaS account');
            $message->from(env('MAIL_USERNAME'), 'ArtSaaS');
        });
        return response(['id' => $user->id], 201);
    }

    public function update(UserRequest $request, User $user)
    {
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
        return response(null, 200);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response(null, 200);
    }

    public function current(Request $request)
    {
        return response(Auth::user()->toArray(), 200);
    }

    public function impersonate(User $user)
    {
        if (!Auth::user()->client->is_super_admin)
            return response(null, 403);

        $token = $user->createToken('Impersonate grant token')->accessToken;
        return $token;
    }

    public function requestPasswordReset(Request $request)
    {
        $user = User::where('email', $request->get('email'))->first();

        if (!$user)
            return response(null, 404);

        $user->password_reset_token = Helpers::generatePassword(32);
        $user->save();

        Mail::send('emails.user_request_password_reset', [
            'link' => env('APP_URL') . '/api/users/confirm_password_reset/' . $user->password_reset_token
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('ArtSaaS account password reset request');
            $message->from(env('MAIL_USERNAME'), 'ArtSaaS');
        });

        return response(null, 200);
    }

    public function confirmPasswordReset(string $token)
    {
        $user = User::where('password_reset_token', $token)->first();
        if (!$user || !$token)
            return response(null, 404);

        $password = Helpers::generatePassword();
        $user->password_reset_token = null;
        $user->password = Hash::make($password);
        $user->save();

        Mail::send('emails.user_confirm_password_reset', [
            'password' => $password
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('ArtSaaS account password reset confirmation');
            $message->from(env('MAIL_USERNAME'), 'ArtSaaS');
        });

        return redirect(env('APP_URL') . ':4200');
    }
}
