<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class TableParamsHelper
{

    public $column = 'id';
    public $direction = 'asc';
    public $page_number = 1;
    public $limit = 10;
    public $search_term = '';
    public $lang = '';
    public $client_id = null;
    public $user_id = null;

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

        if ($request->get('lang')) {
            $this->lang = $request->get('lang');
        } else {
            $this->lang = App::getLocale();
        }
    }

    public static function filterResponseAttributes($query, $scopedAttributes, $modelClass)
    {
        if ($scopedAttributes) {
            $instance = new $modelClass();
            $alwaysHidden = ['password', 'password_reset_token'];
            foreach ($alwaysHidden as $item) {
                if (($key = array_search($item, $scopedAttributes)) !== false) {
                    unset($scopedAttributes[$key]);
                }
            }
            $allAttributes = array_merge(
                $instance->getFillable(),
                $instance->getGuarded(),
                $instance->getTranslatedAttributes(),
                $instance->getWith(),
                ['translations', 'created_at', 'updated_at', 'id']
            );
            $query->makeHidden($allAttributes)->makeVisible($scopedAttributes);
        }
    }
}
