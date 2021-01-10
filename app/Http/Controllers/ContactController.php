<?php

namespace App\Http\Controllers;

use App\Helpers\TableParamsHelper;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use App\Models\ContactEmailAddress;
use App\Models\ContactPhoneNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{

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

        return view('pages.contacts.contacts-list', [
            'contacts' => $contacts,
            'searchTerm' => $tableParams->search_term
        ]);
    }

    public function add()
    {
        return view('pages.contacts.single-contact');
    }

    public function edit(int $id)
    {
        $contact = $this->getResourceById(Contact::class, $id);
        return view('pages.contacts.single-contact', [
            'contact' => $contact
        ]);
    }

    public function create(ContactRequest $request): RedirectResponse
    {
        $contact = new Contact();
        $contact->fill($request->all());
        $contact->client_id = Auth::user()->client_id;
        $contact->save();

        $this->updateContactPhoneNumbers($contact, $request->get('phone_numbers'));
        $this->updateContactEmailAddresses($contact, $request->get('email_addresses'));

        return redirect()->route('contacts');
    }

    public function update(ContactRequest $request, int $id): RedirectResponse
    {
        $contact = $this->getResourceById(Contact::class, $id);
        $this->updateContactPhoneNumbers($contact, $request->get('phone_numbers'));
        $this->updateContactEmailAddresses($contact, $request->get('email_addresses'));

        $contact->fill($request->all());
        $contact->save();
        return redirect()->route('contacts');
    }

    public function destroy(int $id): RedirectResponse
    {
        $contact = $this->getResourceById(Contact::class, $id);
        $contact->delete();
        return redirect()->route('contacts');
    }

    private function updateContactPhoneNumbers(Contact $contact, array $phoneNumbers)
    {
        $contact->phoneNumbers()->delete();
        $this->updateContactRelation($contact, ContactPhoneNumber::class, $phoneNumbers);
    }

    private function updateContactEmailAddresses(Contact $contact, array $emailAddresses)
    {
        $contact->emailAddresses()->delete();
        $this->updateContactRelation($contact, ContactEmailAddress::class, $emailAddresses);
    }

    private function updateContactRelation(Contact $contact, string $className, array $data)
    {
        $data = array_unique(
            array_map(function ($value) {
                return $value['value'];
            }, $data)
        );

        foreach ($data as $item) {
            if (!$item) {
                continue;
            }
            $newItem = new $className([
                'value' => $item
            ]);
            $newItem->contact_id = $contact->id;
            $newItem->save();
        }
    }
}
