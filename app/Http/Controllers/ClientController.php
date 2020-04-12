<?php

namespace App\Http\Controllers;

use App\Client;
use App\Helpers\TableParamsHelper;
use App\Http\Requests\ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Client::class, 'client');
    }

    public function index(Request $request)
    {
        $tableParams = new TableParamsHelper($request);

        $clients = Client::query();

        $clients = $clients->orderBy($tableParams->column, $tableParams->direction)
            ->where(function ($q) use ($tableParams) {
                $q->where('name', 'ilike', "%$tableParams->search_term%")
                    ->orWhere('email', 'ilike', "%$tableParams->search_term%");
            });

        $per_page = $tableParams->limit != -1 ? $tableParams->limit : $clients->count();

        $clients = $clients->paginate($per_page, ['*'], 'page', $tableParams->page_number);

        foreach ($clients->items() as $item) {
            TableParamsHelper::filterResponseAttributes($item, $request->get('attributes'), Client::class);
        }

        return response($clients, 200);
    }

    public function store(ClientRequest $request)
    {
        $client = new Client();
        $client->fill($request->all());
        $client->save();

        return response(['id' => $client->id], 201);
    }

    public function show(Client $client)
    {
        return response($client->toArray(), 200);
    }

    public function update(ClientRequest $request, Client $client)
    {
        if (!Auth::user()->client->is_superadmin && $request->get('modules')) {
            $data = $request->except('modules');
        } else {
            $data = $request->all();
        }

        $client->fill($data);
        $client->save();
        return response(null, 200);
    }

    public function destroy(Client $client)
    {
        if (!Auth::user()->client->is_superadmin)
            return response(null, 403);

        $client->delete();
        return response(null, 200);
    }
}
