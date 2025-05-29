@extends('backend.app')

@section('title', 'Edit Upcoming Album')

@push('style')
    <!-- Dropify CSS for file input styling -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.css">
    <style>
        .ck-editor__editable[role="textbox"] {
            min-height: 150px;
        }
    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Upcoming Album</h4>
                        <div class="mt-4">
                            <!-- Form to edit the upcoming album -->
                            <form class="forms-sample" action="{{ route('upcomming.slider-image.update', $sliderImage->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- City Input -->
                                <div class="form-group row mb-3">
                                    <div class="col">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror"
                                            id="location" name="location" value="{{ old('location', $sliderImage->location) }}">
                                        @error('location')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Image Input -->
                                <div class="form-group row mb-3">
                                    <div class="col">
                                        <label class="form-label">Image</label>
                                        <input class="form-control dropify @error('image_url') is-invalid @enderror"
                                            type="file"
                                            data-default-file="{{ $sliderImage->image_url ? asset('storage/' . $sliderImage->image_url) : asset('backend/images/placeholder/image_placeholder.png') }}"
                                            name="image_url">
                                        @error('image_url')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Performance Date Input -->
                                <div class="form-group mb-3">
                                    <label class="form-label">Performance Date:</label>
                                    <input type="date" class="form-control @error('performance_date') is-invalid @enderror"
                                        id="performance_date" name="performance_date"
                                        value="{{ old('performance_date', $sliderImage->performance_date) }}">
                                    @error('performance_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit and Cancel Buttons -->
                                <button type="submit" class="btn btn-primary me-2">Update</button>
                                <a href="{{ route('upcomming.slider-image.index') }}" class="btn btn-danger">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Dropify JS -->
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.dropify').dropify(); // Initialize Dropify
        });
    </script>

    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush
