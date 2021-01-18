<?php

namespace App\Http\Controllers;

use App\Helpers\Media\ImageHelper;
use App\Models\Media;
use App\Models\MediaSizes;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function index()
    {
        $client_id = Auth::user()->client_id;
        $media = Media::where('client_id', $client_id)->orderBy('id', 'desc')->get();
        return view('pages.media.media-list', [
            'media' => $media
        ]);
    }

    public function show(Request $request, string $name): BinaryFileResponse
    {
        if (Str::contains($name, '/')) {
            $filepath = public_path() . "/storage/media/" . $name;

            [$name, $subName] = explode('/', $name);

            /**
             * @var Media $media
             */
            $media = Media::where([
                ['filename', 'like', "$name%"],
                ['is_public', true]
            ])->first();

            if (!$media) {
                abort(404);
            }
            $headers = array('Content-Type: ' . $media->type);
            return response()->file($filepath, $headers);
        }
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
                $filepath = ImageHelper::getResizedOrCreate($media, $sizes, false);
            } catch (Exception $exception) {
                $filepath = public_path() . "/storage/media/" . $name;
            }
        }
        $headers = array('Content-Type: ' . $media->type);
        return response()->file($filepath, $headers);
    }

}
