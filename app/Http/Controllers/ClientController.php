<?php

namespace App\Http\Controllers;

use App\Helpers\TableParamsHelper;
use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Models\Locale;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{

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

        return view('pages.clients.clients-list', [
            'clients' => $clients,
            'searchTerm' => $tableParams->search_term
        ]);
    }

    public function add()
    {
        return view('pages.clients.single-client', [
            'locales' => Locale::LOCALES_NAMES,
            'modulesNames' => Module::MODULES_PAGE_NAMES,
        ]);
    }

    public function edit(int $id)
    {
        $client = Client::where([
            'id' => $id,
        ])->first();

        if (!$client) {
            abort(404);
        }
        return view('pages.clients.single-client', [
            'client' => $client,
            'modulesNames' => Module::MODULES_PAGE_NAMES,
            'locales' => Locale::LOCALES_NAMES
        ]);
    }

    public function create(ClientRequest $request): RedirectResponse
    {
        $data = $request->all();
        if (!count($data['available_locales'])) {
            $data['available_locales'][] = $data['locale'];
        }
        $client = new Client();
        $client->fill($data);
        $client->slug = $data['slug'];
        $client->modules = $data['modules'];
        $client->save();

        return redirect()->route('clients');
    }

    public function update(ClientRequest $request, int $id): RedirectResponse
    {
        $client = Client::where([
            'id' => $id,
        ])->first();

        if (!$client) {
            abort(404);
        }
        $data = $request->all();
        $data['available_locales']=[];
        if (!count($data['available_locales'])) {
            $data['available_locales'][] = $data['locale'];
        }
        $client->fill($data);
        if (!Auth::user()->client->is_super_admin && $request->get('modules')) {
            $client->modules = $request->get('modules');
        }

        $client->save();
        return redirect()->route('clients');
    }

    public function destroy(int $id): RedirectResponse
    {
        $client = Client::where([
            'id' => $id,
        ])->first();

        if (!$client) {
            abort(404);
        }
        if (!Auth::user()->client->is_super_admin) {
            abort(404);
        }

        $client->delete();
        return redirect()->route('clients');
    }
}
