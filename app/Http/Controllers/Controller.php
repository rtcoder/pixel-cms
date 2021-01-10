<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function getResourceById(string $class, int $id, bool $byClient = true)
    {
        $condition = ['id' => $id,];
        if ($byClient) {
            $condition['client_id'] = Auth::user()->client_id;
        }
        $data = (new $class)->where($condition)->first();

        if (!$data) {
            abort(404);
        }
        return $data;
    }
}
