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

                <form method="POST" enctype="multipart/form-data"
                    action="{{ route('resources.update', $resource->id) }}" accept-charset="UTF-8"
                    style="display:inline">
                    @method('PUT')
                    @csrf

                    <div class="mb-3 row">
                        <label for="title" class="col-md-2 col-form-label">@lang('app.title'):</label>
                        <div class="col-md-6">
                            <input class="form-control" type="text" value="{{ old('title', $resource->title ?? '') }}"
                                id="title" name="title" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="description" class="col-md-2 col-form-label">@lang('app.small_description'):</label>
                        <div class="col-md-6">
                            <input class="form-control" type="text"
                                value="{{ old('description', $resource->description ?? '') }}" id="description"
                                name="description" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="resource_type" class="col-md-2 col-form-label">@lang('app.resource_type'):</label>
                        <div class="col-md-6">
                            <select class="form-select" id="resource_type" name="resource_type" required>
                                @foreach ($resource_types as $resource_type)
                                    <option value="{{ $resource_type }}" {{ old('resource_type', $resource->resource_type) === $resource_type ? 'selected' : '' }}>
                                        {{ ucfirst($resource_type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="type" class="col-md-2 col-form-label">@lang('app.media_type'):</label>
                        <div class="col-md-6">
                            <select class="form-select" id="type" name="type" required>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}" {{ old('type', $resource->type) === $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($resource->url)
                        <div class="mb-3 row">
                            <label for="file" class="col-md-2 col-form-label">File Click to download:</label>
                            <div class="col-md-6 d-flex align-items-center">
                                <!-- Icon -->
                                <a href="{{ $resource->url }}" target="_blank" class="me-2"
                                    style="font-size: 18px; text-decoration: none;">
                                    <i class="uil uil-file-plus"></i>
                                </a>
                                <!-- Input -->
                                <input class="form-control" type="url" id="url" name="url" value="{{ $resource->url }}"
                                    disabled>
                            </div>
                        </div>
                    @endif


                    <div class="mb-3 row">
                        <label for="file" class="col-md-2 col-form-label">File:</label>
                        <div class="col-md-6">
                            <input class="form-control" type="file" id="document" name="document">
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-secondary waves-effect waves-light">
                            <a href="{{ url('/course') }}" style="color:white">
                                <i class="bx bx-arrow-back"></i> Go Back
                            </a>
                        </button>
                        <button type="submit"
                            class="btn btn-primary waves-effect waves-light">@lang('app.update_resource')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> <!-- end row -->

@endsection
