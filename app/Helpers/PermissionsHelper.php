<?php

namespace App\Helpers;

use App\User;

class PermissionsHelper
{

    /**
     * @param User $user
     * @param int $module
     * @param 'view' | 'create' | 'delete' $action
     * @return bool
     */
    public static function roleHasPermission(User $user, int $module, $action = 'view'): bool
    {
        $key = array_search($module, array_column($user->role->permissions, 'module'));
        $roleHasPermission = $key !== false;
        if ($roleHasPermission) {
            $roleHasPermission = $user->role->permissions[$key]['can'][$action];
        }
        return self::clientHasPermission($user, $module) && ($user->role->is_admin || $roleHasPermission);
    }

    public static function clientHasPermission(User $user, int $module): bool
    {
        return $user->client->is_superadmin || in_array($module, $user->client->modules);
    }
}
