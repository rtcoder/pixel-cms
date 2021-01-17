<?php

use App\Helpers\PermissionsHelper;
use App\Models\Module;

$user = auth()->user();
$links = [
    [
        'route' => 'home',
        'icon' => 'dashboard',
        'name' => 'pages.dashboard',
        'show' => true
    ],
    [
        'route' => 'users',
        'icon' => 'supervisor_account',
        'name' => 'pages.users',
        'show' => PermissionsHelper::roleHasPermission($user, Module::USERS_MODULE)
    ],
    [
        'route' => 'contacts',
        'icon' => 'contacts',
        'name' => 'pages.contacts',
        'show' => PermissionsHelper::roleHasPermission($user, Module::CONTACTS_MODULE)
    ],
    [
        'route' => 'roles',
        'icon' => 'account_box',
        'name' => 'pages.roles',
        'show' => PermissionsHelper::roleHasPermission($user, Module::ROLES_MODULE)
    ],
    [
        'route' => 'documents',
        'icon' => 'text_snippet',
        'name' => 'pages.documents',
        'show' => PermissionsHelper::roleHasPermission($user, Module::DOCUMENTS_MODULE)
    ],
    [
        'route' => 'media',
        'icon' => 'perm_media',
        'name' => 'pages.media',
        'show' => true
    ],
    [
        'route' => 'clients',
        'icon' => 'supervised_user_circle',
        'name' => 'pages.clients',
        'show' => $user->role->is_super_admin
    ],
    [
        'route' => 'settings',
        'icon' => 'settings',
        'name' => 'pages.settings',
        'show' => true
    ]
]


?>

<div class="sidebar">
    <ul>
        @foreach($links as $link)
            @if($link['show'])
                <li>
                    <a
                        @if(\Illuminate\Support\Facades\Route::currentRouteName() === $link['route'] ? 'active' : '')
                        class="active"
                        @else
                        href="{{ route($link['route']) }}"
                        @endif
                    >
                        <span class="material-icons">{{$link['icon']}}</span>
                        <span>@lang($link['name'])</span>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
