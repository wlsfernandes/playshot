@extends('layouts.master')

@section('title')
@lang('translation.Form_File_Upload')
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') AMID @endslot
@slot('title') Resources @endslot
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
                    action="{{ route('disciplines.addResource', $discipline->id) }}" accept-charset="UTF-8"
                    style="display:inline">
                    @csrf

                    <div class="mb-3 row">
                        <label for="title" class="col-md-2 col-form-label">@lang('app.title'):</label>
                        <div class="col-md-6">
                            <input class="form-control" type="text" value="{{ old('title') }}" id="title" name="title"
                                required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="description" class="col-md-2 col-form-label">@lang('app.small_description'):</label>
                        <div class="col-md-6">
                            <input class="form-control" type="text" value="{{ old('description') }}" id="description"
                                name="description" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="resourcetype" class="col-md-2 col-form-label">@lang('app.resource_type'):</label>
                        <div class="col-md-6">
                            <select class="form-select" id="resource_type" name="resource_type" required>
                                @foreach ($resource_types as $resource_type)
                                    <option value="{{ $resource_type }}" {{ old('resource_type', 'documento') === $resource_type ? 'selected' : '' }}>
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
                                    <option value="{{ $type }}" {{ old('type', 'pdf') === $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

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
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Create Resource</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> <!-- end row -->

<!-- display resources -->
<div class="row">
    <div class="col-lg-12">
        <div class="card border border-primary">
            <div class="card-header bg-transparent border-primary">
                <h5 class="my-0 text-primary"><i class="uil uil-file-plus"></i> @lang('app.resources')</b></h5>
            </div>
            @if ($resources->isEmpty())
                <div class="alert alert-warning" role="alert">
                    @lang('app.there_no_resource')
                </div>
            @else
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('app.resource')</th>
                                <th>@lang('app.title')</th>
                                <th>@lang('app.description')</th>
                                <th>@lang('app.resource_type')</th>
                                <th>@lang('app.media_type')</th>
                                <th class="text-center align-middle">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resources as $resource)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td> <a href="{{ $resource->url }}" target="_blank" class="me-2"
                                            style="font-size: 18px; text-decoration: none;">
                                            <i class="uil uil-file-plus"></i>
                                        </a></td>
                                    <td>{{ $resource->title ?? ''}}</td>
                                    <td>{{ $resource->description ?? ''}}</td>
                                    <td>{{ $resource->resource_type ?? ''}}</td>
                                    <td>{{ $resource->type ?? ''}}</td>

                                    <td class="text-center align-middle">
                                        <a href="{{ url('/resources/' . $resource->id . '/edit') }}"
                                            class="px-3 text-primary"><i class="uil uil-pen font-size-18"></i></a>

                                        <a href="javascript:void(0);" class="px-3 text-danger"
                                            onclick="event.preventDefault(); if(confirm('Confirm delete?')) { document.getElementById('delete-form-{{ $resource->id }}').submit(); }">
                                            <i class="uil uil-trash-alt font-size-18"></i>
                                        </a>

                                        <form id="delete-form-{{ $resource->id }}"
                                            action="{{ url('/resources/' . $resource->id) }}" method="POST"
                                            style="display: none;">
                                            @method('DELETE')
                                            @csrf
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>
            @endif
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection
