<?php

namespace App\Http\Controllers\Web\Backend\CMS;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\UpcommingAlbumImage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UpcommingAlbumSliderImageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = UpcommingAlbumImage::get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('performance_date', function ($data) {
                    return Carbon::parse($data->performance_date)->format('d F Y');
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                <!-- Edit Button -->
                                <a href="' . route('upcomming.slider-image.edit', $data->id) . '" class="btn btn-primary text-white" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <!-- Delete Button -->
                                <button type="button" class="btn btn-danger text-white" title="Delete" onclick="showDeleteConfirm(' . $data->id . ')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>';
                })
                ->addColumn('image', function ($data) {
                    return "<img width='70px' src='" . asset('storage/' . $data->image_url) . "' ></img>";
                })
                ->rawColumns(['action', 'image', 'performance_date'])
                ->make(true);
        }
        return view('backend.layout.cms.album-image.index');
    }

    public function create()
    {

        return view('backend.layout.cms.album-image.create');
    }

    public function edit($id)
    {

        $sliderImage = UpcommingAlbumImage::findOrFail($id);

        return view('backend.layout.cms.album-image.edit', compact('sliderImage'));
    }

    /**
     * Store a newly created slider image in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            // 'sub_title' => 'nullable|string',
            'location' => 'nullable|string',
            'performance_date' => 'nullable|date',
            'image_url' => 'nullable|image|mimes:jpeg,jpg,png,svg,webp|max:2048',
        ]);

        // Handle image upload
        $image_url = null;

        if ($request->hasFile('image_url')) {
            $image = $request->file('image_url');
            $image_url = uploadImage($image, 'images/cms/upcomming-album'); // Define your uploadImage function
        }

        // Create the gallary image entry
        $data = UpcommingAlbumImage::create([
            'title' => $request->title,
            // 'sub_title' => $request->sub_title,
            'location' => $request->location,
            'performance_date' => $request->performance_date,
            'image_url' => $image_url,
        ]);

        if ($data) {
            return  redirect()->action([self::class, 'index'])->with('t-success', 'Slider image created successfully.');
        } else {
            return redirect()->action([self::class, 'create'])->with('t-error', 'Data update failed!');
        }
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'location' => 'nullable|string',
            'performance_date' => 'nullable|date',
            'image_url' => 'nullable|image|mimes:jpeg,jpg,png,svg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $sliderImage = UpcommingAlbumImage::findOrFail($id);

        // Handle image upload
        if ($request->hasFile('image_url')) {
            // Delete the old image
            if ($sliderImage->image_url) {
                deleteImage($sliderImage->image_url); // Define your deleteImage function
            }

            $image = $request->file('image_url');
            $sliderImage->image_url = uploadImage($image, 'images/cms/upcomming-album'); // Define your uploadImage function
        }

        // Update other fields
        $sliderImage->title = $request->title;
        $sliderImage->location = $request->location;
        $sliderImage->performance_date = $request->performance_date;
        $sliderImage->save();

        return redirect()->action([self::class, 'index'])->with('t-success', 'Slider image updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $data = UpcommingAlbumImage::find($id);

        if (!$data) {
            return response()->json(['t-success' => false, 'message' => 'Data not found.']);
        }
        $data->delete();
        return response()->json(['t-success' => true, 'message' => 'Deleted successfully.']);
    }
}
