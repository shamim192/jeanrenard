<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use App\Models\FeatureAlbum;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FeatureAlbumController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = FeatureAlbum::latest();
            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('image', function ($data) {
                    return "<img width='70px' src='" . asset('storage/' .$data->image_url) . "' ></img>";
                })

                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                  <a href="#" onclick="showDeleteConfirm(' . $data->id . ')" type="button" class="btn btn-danger text-white" title="Delete">
                                  <i class="bi bi-trash"></i>
                                </a>
                                </div>';
                })
                ->rawColumns(['action','image'])
                ->make(true);
        }
        return view('backend.layout.cms.feature-album.index');
    }

    public function create()
    {
        return view('backend.layout.cms.feature-album.create');
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'image_url' => 'nullable|image|mimes:jpeg,jpg,png,svg,webp|max:2048',
            'audio_url' => 'nullable|file|mimes:mp3,wav,aac,flac,ogg|max:20480',
        ]);

        // Handle image upload
        $image_url = null;

        if ($request->hasFile('image_url')) {
            $image = $request->file('image_url');
            $image_url = uploadImage($image, 'images/cms/feature-album'); // Define your uploadImage function
        }

        // Handle image upload
        $audio_url = null;

        if ($request->hasFile('audio_url')) {
            $audio = $request->file('audio_url');
            $audio_url = uploadImage($audio, 'audio'); // Define your uploadImage function
        }

        // Create the service entry
        $data =  FeatureAlbum::create([
            'title' => $request->title,
            'image_url' => $image_url,
            'audio_url' => $audio_url,
        ]);

        if ($data) {
            return  redirect()->action([self::class, 'index'])->with('t-success', 'Feature Album created successfully.');
        } else {
            return redirect()->action([self::class, 'create'])->with('t-error', 'Data update failed!');
        }
    }

    public function destroy(Request $request, $id)
    {
        $data = FeatureAlbum::find($id);

        if (!$data) {
            return response()->json(['t-success' => false, 'message' => 'Data not found.']);
        }
        $data->delete();
        return response()->json(['t-success' => true, 'message' => 'Deleted successfully.']);
    }
}
