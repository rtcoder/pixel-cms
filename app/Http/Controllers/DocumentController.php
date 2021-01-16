<?php

namespace App\Http\Controllers;

use App\Helpers\TableParamsHelper;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentController extends Controller
{

    public function index(Request $request)
    {
        $auth = Auth::user();
        $tableParams = new TableParamsHelper($request);

        $documents = Document::query();
        if (!$auth->role->is_super_admin || !$tableParams->client_id) {
            $documents = $documents->where('client_id', $auth->client_id);
        } else {
            $documents = $documents->where('client_id', $tableParams->client_id);
        }

        $documents = $documents->orderBy($tableParams->column, $tableParams->direction)
            ->where('name', 'ilike', "%$tableParams->search_term%");

        $per_page = $tableParams->limit != -1 ? $tableParams->limit : $documents->count();

        $documents = $documents->paginate($per_page, ['*'], 'page', $tableParams->page_number);

        return view('pages.documents.documents-list', [
            'documents' => $documents,
            'searchTerm' => $tableParams->search_term
        ]);
    }

    public function add()
    {
        return view('pages.documents.single-document');
    }

    public function edit(int $id)
    {
        $document = $this->getResourceById(Document::class, $id);
        return view('pages.documents.single-document', [
            'document' => $document,
        ]);
    }

    public function create(DocumentRequest $request): RedirectResponse
    {
        $document = new Document();
        $document->fill($request->all());
        $document->client_id = Auth::user()->client_id;
        $document->save();

        return redirect()->route('documents');
    }

    public function update(DocumentRequest $request, int $id): RedirectResponse
    {
        $document = $this->getResourceById(Document::class, $id);
        $document->fill($request->all());
        $document->save();
        return redirect()->route('documents');
    }

    public function destroy(int $id): RedirectResponse
    {
        $document = $this->getResourceById(Document::class, $id);
        $document->delete();
        return redirect()->route('documents');
    }

    public function saveAsDocx(int $id)
    {

        $document = $this->getResourceById(Document::class, $id);
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($source, 'HTML');
    }

}
