<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class TableParamsHelper
{
    /**
     * @var string
     */
    public $column = 'id';
    /**
     * @var string
     */
    public $direction = 'asc';
    /**
     * @var int
     */
    public $page_number = 1;
    /**
     * @var int
     */
    public $limit = 10;
    /**
     * @var string
     */
    public $search_term = '';
    /**
     * @var int|null
     */
    public $client_id = null;
    /**
     * @var int|null
     */
    public $user_id = null;

    /**
     * TableParamsHelper constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->column = $request->get('column') ? $request->get('column') : 'id';
        $direction = $request->get('direction');
        $this->direction = $direction && in_array(strtolower($direction), ['asc', 'desc']) ? strtolower($direction) : 'asc';
        $this->page_number = $request->get('pageNumber') ? (int)$request->get('pageNumber') : 1;
        $this->limit = $request->get('limit') ? (int)$request->get('limit') : 10;
        $this->client_id = $request->get('clientId') ? (int)$request->get('clientId') : null;
        $this->user_id = $request->get('userId') ? (int)$request->get('userId') : null;


        if ($request->get('searchTerm')) {
            $this->search_term = $request->get('searchTerm');
        } elseif ($request->get('search')) {
            $this->search_term = $request->get('search');
        }
    }
}
