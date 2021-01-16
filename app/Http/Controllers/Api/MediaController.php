<?php

namespace App\Http\Controllers\Api;

use App\Helpers\MediaHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MediaRequest;
use App\Models\Media;
use App\Models\MediaSizes;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class MediaController extends Controller
{
    public function store(MediaRequest $request)
    {
        $client_id = Auth::guard('api')->user()->client_id;
        try {
            $media = MediaHelper::storeMedia($request->file('file'), $client_id);

            return response(['url' => $media->url], 201);
        } catch (Throwable $error) {
            return response(['error' => $error->getMessage(), 'trace' => $error->getTrace()], 500);
        }
    }

    public function show(Request $request, string $name): BinaryFileResponse
    {
        /**
         * @var Media $media
         */
        $media = Media::where([
            ['filename', $name],
            ['is_public', true]
        ])->first();

        if (!$media) {
            abort(404);
        }

        $filepath = public_path() . "/storage/media/" . $name;

        if ($request->get('x') || $request->get('y')) {
            $sizes = new MediaSizes($request->get('x'), $request->get('y'));
            try {
                $filepath = MediaHelper::getResizedOrCreate($media, $sizes, false);
            } catch (Exception $exception) {
                $filepath = public_path() . "/storage/media/" . $name;
            }
        }
        $headers = array('Content-Type: ' . $media->type);
        return response()->file($filepath, $headers);
    }

}
