<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MediaHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MediaRequest;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ApiMediaController extends Controller
{

    public function index()
    {
        $client_id = Auth::guard('api')->user()->client_id;
        $media = Media::query()->where('client_id', $client_id)->orderBy('id', 'desc')->get();
        return response($media);
    }

    public function store(MediaRequest $request)
    {
        $client_id = Auth::guard('api')->user()->client_id;
        try {
            $media = MediaHelper::storeMedia($request->file('file'), $client_id);

            return response($media, 201);
        } catch (Throwable $error) {
            return response([
                'error' => $error->getMessage(),
                'trace' => $error->getTrace()
            ], 500);
        }
    }
}
