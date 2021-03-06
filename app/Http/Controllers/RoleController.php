<?php

namespace App\Http\Controllers;

use App\Helpers\TableParamsHelper;
use App\Http\Requests\RoleRequest;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{

    public function index(Request $request)
    {
        $auth = Auth::user();
        $tableParams = new TableParamsHelper($request);

        $roles = Role::query();
        if (!$auth->role->is_super_admin || !$tableParams->client_id) {
            $roles = $roles->where('client_id', $auth->client_id);
        } else {
            $roles = $roles->where('client_id', $tableParams->client_id);
        }

        $roles = $roles->orderBy($tableParams->column, $tableParams->direction)
            ->where(function ($q) use ($tableParams) {
                $q->where('name', 'ilike', "%$tableParams->search_term%");
            });

        $per_page = $tableParams->limit != -1 ? $tableParams->limit : $roles->count();

        $roles = $roles->paginate($per_page, ['*'], 'page', $tableParams->page_number);

        return view('pages.roles.roles-list', [
            'roles' => $roles,
            'searchTerm' => $tableParams->search_term
        ]);
    }

    public function add()
    {
        return view('pages.roles.single-role', [
            'modules' => Module::MODULES,
            'modulesNames' => Module::MODULES_PAGE_NAMES,
            'actions' => Module::ACTIONS,
        ]);
    }

    public function edit(int $id)
    {
        $role = $this->getResourceById(Role::class, $id);

        return view('pages.roles.single-role', [
            'role' => $role,
            'modules' => Module::MODULES,
            'modulesNames' => Module::MODULES_PAGE_NAMES,
            'actions' => Module::ACTIONS,
        ]);
    }

    public function create(RoleRequest $request): RedirectResponse
    {
        $data = $request->all();
        $data['permissions'] = $this->parsePermissions($data['permissions']);
        $role = new Role();
        $role->fill($data);
        $role->client_id = Auth::user()->client_id;
        $role->save();

        return redirect()->route('roles');
    }

    public function update(RoleRequest $request, int $id): RedirectResponse
    {
        $role = $this->getResourceById(Role::class, $id);
        $data = $request->all();
        $data['permissions'] = $this->parsePermissions($data['permissions']);
        $role->fill($data);
        $role->save();
        return redirect()->route('roles');
    }

    public function destroy(int $id): RedirectResponse
    {
        $role = $this->getResourceById(Role::class, $id);
        $role->delete();
        return redirect()->route('roles');
    }

    private function parsePermissions(array $permissions): array
    {
        foreach ($permissions as $module => $permission) {
            $permissions[$module] = array_keys($permission);
        }
        return $permissions;
    }
}
