<?php

namespace App\Http\Controllers;

use App\Helpers\TableParamsHelper;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Contact::class, 'contact');
    }

    public function index(Request $request)
    {
        $tableParams = new TableParamsHelper($request);

        $contacts = Contact::where('client_id', Auth::user()->client_id);
        if ($tableParams->search_term) {
            $contacts->where(function ($q) use ($tableParams) {
                $q->where('first_name', 'ilike', "%$tableParams->search_term%")
                    ->orWhere('last_name', 'ilike', "%$tableParams->search_term%")
                    ->orWhere(DB::raw("CONCAT('first_name', ' ', 'last_name')"), 'like', "%$tableParams->search_term%")
                    ->orWhere(DB::raw("CONCAT('last_name', ' ', 'first_name')"), 'like', "%$tableParams->search_term%")
                    ->orWhereHas('emailAddresses', function ($emails) use ($tableParams) {
                        $emails->where('value', 'ilike', "%$tableParams->search_term%");
                    })
                    ->orWhereHas('phoneNumbers', function ($numbers) use ($tableParams) {
                        $numbers->where('value', 'ilike', "%$tableParams->search_term%");
                    });

            });
        }
        switch ($tableParams->column) {
            case "fullName":
                $contacts->orderByRaw('UPPER(CONCAT(first_name, last_name)) ' . $tableParams->direction);
                break;
            case "email":
                $contacts->orderByRaw('emailAddresses.value::text ' . $tableParams->direction);
                break;
            default :
                $contacts->orderBy($tableParams->column, $tableParams->direction);

        }

        $per_page = $tableParams->limit != -1 ? $tableParams->limit : $contacts->count();

        $contacts = $contacts->paginate($per_page, ['*'], 'page', $tableParams->page_number);

        foreach ($contacts->items() as $item) {
            TableParamsHelper::filterResponseAttributes($item, $request->get('attributes'), Contact::class);
        }

        return view('pages.contacts.contacts-list', [
            'contacts' => $contacts,
            'searchTerm' => $tableParams->search_term
        ]);
    }

    public function store(ContactRequest $request)
    {
        $contact = new Contact();
        $contact->fill($request->all());
        $contact->client_id = Auth::user()->client_id;
        $contact->save();

        return response(['id' => $contact->id], 201);
    }

    public function show(Contact $contact)
    {
        return response($contact->toArray());
    }

    public function update(ContactRequest $request, Contact $contact)
    {
        if (!Auth::user()->client->is_super_admin && $request->get('modules')) {
            $data = $request->except('modules');
        } else {
            $data = $request->all();
        }

        $contact->fill($data);
        $contact->save();
        return response(null, 200);
    }

    public function destroy(Contact $contact)
    {
        if (!Auth::user()->client->is_super_admin)
            return response(null, 403);

        $contact->delete();
        return response(null, 200);
    }
}
