<?php

namespace App\Http\Controllers;

use App\Helpers\TableParamsHelper;
use App\Http\Requests\RoleRequest;
use App\Module;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    public function index(Request $request)
    {
        $auth = Auth::user();
        $tableParams = new TableParamsHelper($request);

        $roles = Role::query();
        if (!$auth->client->is_superadmin || !$tableParams->client_id) {
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

        foreach ($roles->items() as $item) {
            TableParamsHelper::filterResponseAttributes($item, $request->get('attributes'), Role::class);
        }

        return response($roles, 200);
    }

    public function show(Role $role)
    {
        return response($role->toArray(), 200);
    }

    public function store(RoleRequest $request)
    {
        $auth = Auth::user();
        $role = new Role();
        $role->fill($request->all());
        $role->client_id = $auth->client_id;
        $role->save();

        return response(['id' => $role->id], 201);
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role->fill($request->all());
        $role->save();
        return response(null, 200);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response(null, 200);
    }
}
