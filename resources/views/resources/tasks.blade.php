@extends('layouts.master')

@section('title')
Task
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') AMID @endslot
@slot('title') Resources @endslot
@endcomponent



<!-- display resources -->
<div class="row">
    <div class="col-lg-12">
        <div class="card border border-primary">
            <div class="card-header bg-transparent border-primary">
                <h5 class="my-0 text-primary"><i class="uil uil-file-plus"></i> @lang('app.docs')</b></h5>
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
                                <th>@lang('app.title')</th>
                                <th>@lang('app.description')</th>
                                <th>@lang('app.media_type')</th>
                                <th class="text-center align-middle">@lang('app.download')</th>
                                <th class="text-center align-middle">@lang('app.upload_task')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resources as $resource)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $resource->title ?? ''}}</td>
                                    <td>{{ $resource->description ?? ''}}</td>
                                    <td>{{ $resource->type ?? ''}}</td>
                                    <td class="text-center align-middle"> <a class="px-3 text-danger"
                                            href="{{ $resource->url }}" target="_blank" class="me-2"
                                            style="font-size: 22px; text-decoration: none;">
                                            <i class="uil uil-file-plus"></i>
                                        </a></td>
                                    <td class="text-center align-middle">
                                        <a href="{{ url('/task/' . $resource->id . '/edit') }}" class="px-3 text-primary"><i
                                                class="uil uil-upload font-size-18"></i></a>
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
