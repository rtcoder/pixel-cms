<?php

namespace App\Http\Controllers;

use App\Client;
use App\Helpers\TableParamsHelper;
use App\Http\Requests\ClientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth::user()->client->is_superadmin)
            return response(null, 403);

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

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientRequest $request)
    {
        if (!Auth::user()->client->is_superadmin)
            return response(null, 403);

        $client = new Client();
        $client->fill($request->all());
        $client->save();

        return response(['id' => $client->id], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Client $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        return response($client->toArray(), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ClientRequest $request
     * @param Client $client
     * @return \Illuminate\Http\Response
     */
    public function update(ClientRequest $request, Client $client)
    {
        $client->fill($request->all());
        $client->save();
        return response(null, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Client $client
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Client $client)
    {
        if (!Auth::user()->client->is_superadmin)
            return response(null, 403);

        $client->delete();
        return response(null, 200);
    }
}
