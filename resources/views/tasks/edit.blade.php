<!DOCTYPE html>
@extends('layouts.master')

@section('title')
@lang('translation.Form_File_Upload')
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') AMID @endslot
@slot('title') Resource @endslot
@endcomponent

<!-- add Resource -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if (session()->has('success'))
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> <!-- Success icon -->
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li><i class="bx bx-error"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <h4 class="card-title">Upload a resource
                </h4>
                <p class="card-title-desc"></p>

                <form action="{{ route('addTask') }}" method="POST">
                    @csrf
                    <input type="hidden" name="resource_id" value="{{$resource->id}}" />
                    <input type="hidden" name="task_id" value="{{$task->id}}" />
                    <div class="mb-3 row">
                        <label for="title" class="col-md-2 col-form-label">@lang('app.title'):</label>
                        <div class="col-md-6">
                            <input class="form-control" type="text" value="{{ old('title', $resource->title ?? '') }}"
                                id="title" name="title" required>
                        </div>
                    </div>

                    @if($resource->url)
                        <div class="mb-3 row">
                            <label for="file" class="col-md-2 col-form-label">Click here to see the Task:</label>
                            <div class="col-md-6 d-flex align-items-center">
                                <a href="{{ $resource->url }}" target="_blank" class="me-2"
                                    style="font-size: 18px; text-decoration: none;">
                                    <i class="uil uil-file-plus"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    @php
                        $sections = [
                            'answer' => 'Describe your answer',
                        ];
                    @endphp

                    @foreach($sections as $field => $title)
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title">{{ $title }}</h4>
                                        <textarea name="{{ $field }}" id="{{ $field }}Editor">{{ old($field) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach


                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-secondary waves-effect waves-light">
                            <a href="{{ url('/mycourses') }}" style="color:white">
                                <i class="bx bx-arrow-back"></i> Go Back
                            </a>
                        </button>
                        <button type="submit"
                            class="btn btn-primary waves-effect waves-light">@lang('app.update_task')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> <!-- end row -->

@endsection
@section('script')
<script src="{{ asset('/assets/libs/tinymce/tinymce.min.js') }}"></script>
<script>
    const editorConfig = {
        plugins: 'image link lists table paste code',
        toolbar: 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | image link table code',
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.onchange = function () {
                var file = this.files[0];
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function () {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);
                    cb(blobInfo.blobUri(), { title: file.name });
                };
            };
            input.click();
        },
        height: 500,
        menubar: 'file edit view insert format tools table help',
        content_css: 'https://www.tiny.cloud/css/codepen.min.css'
    };

    @foreach($sections as $field => $title)
        tinymce.init(Object.assign({}, editorConfig, { selector: '#{{ $field }}Editor' }));
    @endforeach
</script>
