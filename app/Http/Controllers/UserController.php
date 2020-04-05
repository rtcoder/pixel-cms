<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Helpers\TableParamsHelper;
use App\Helpers\UserHelper;
use App\Http\Requests\UserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function __construct()
    {
//        $this->authorizeResource(User::class);
    }

    public function index(Request $request)
    {
        $tableParams = new TableParamsHelper($request);

        $users = User::query();
        if (!Auth::user()->client->is_superadmin || !$tableParams->client_id) {
            $users = $users->where('client_id', Auth::user()->client_id);
        } else {
            $users = $users->where('client_id', $tableParams->client_id);
        }

        $users = $users->orderBy($tableParams->column, $tableParams->direction)
            ->where(function ($q) use ($tableParams) {
                $q->where('name', 'ilike', "%$tableParams->search_term%")
                    ->orWhere('email', 'ilike', "%$tableParams->search_term%");
            });

        $per_page = $tableParams->limit != -1 ? $tableParams->limit : $users->count();

        $users = $users->paginate($per_page, ['*'], 'page', $tableParams->page_number);

        foreach ($users->items() as $item) {
            TableParamsHelper::filterResponseAttributes($item, $request->get('attributes'), User::class);
        }

        return response($users, 200);
    }

    public function show(User $user)
    {
        return response($user->toArray(), 200);
    }

    public function store(UserRequest $request)
    {
        $auth = Auth::user();
        if ($auth->client->plan->users_count <= User::where('client_id', $auth->client_id)->count())
            return response('Users count exceeded. Upgrade your plan to add more users', 400);

        $user = new User();
        $user->fill($request->all());

        $password = UserHelper::generatePassword();
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
        return response(Auth::user()->setAppends([
            "locale",
            "available_locales"
        ])->toArray(), 200);
    }

    public function bulkDestroy(Request $request)
    {
        $auth = Auth::user();
        if (!$auth->role->is_admin)
            return response(null, 403);

        //--Check if request would delete all admins
        if (User::where('client_id', $auth->client_id)->whereIn('id', $request->get('ids'))->whereHas('role', function ($q) {
                $q->whereNull('client_id');
            })->count() === User::where('client_id', $auth->client_id)->whereHas('role', function ($q) {
                $q->whereNull('client_id');
            })->count()) {
            return response(null, 400);
        }

        User::where('client_id', $auth->client_id)->whereIn('id', $request->get('ids'))->delete();
        return response(null, 200);
    }

    public function bulkUpdate(Request $request)
    {
        if (!Auth::user()->client->is_superadmin && !Auth::user()->role->is_admin)
            return response(null, 403);

        User::where('client_id', Auth::user()->client_id)->whereIn('id', $request->get('ids'))->update($request->get('data'));
        return response(null, 200);
    }

    public function impersonate(User $user)
    {
        if (!Auth::user()->client->is_superadmin)
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
