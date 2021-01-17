<?php

namespace App\Http\Middleware;

use App\Helpers\PermissionsHelper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModulePermission
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param int $module
     * @param string $action
     * @return mixed
     */
    public function handle(Request $request, Closure $next, int $module, string $action = 'view')
    {
        /**
         * @var User $user
         */
        $user = Auth::user();

        if (PermissionsHelper::roleHasPermission($user, $module, $action)) {
            return $next($request);
        }

        abort(403);
    }
}
