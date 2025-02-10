@extends('layouts.master')

@section('title')
@lang('app.test')
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') AMID @endslot
@slot('title') Test @endslot
@endcomponent



<!-- display resources -->
<div class="row">
    <div class="col-lg-12">
        <div class="card border border-primary">
            <div class="card-header bg-transparent border-primary">
                <h5 class="my-0 text-primary"><i class="uil uil-stopwatch font-size-24"></i> @lang('app.test')</b></h5>
                <p>@lang('app.test_desc')</p>
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
                                <th class="text-center align-middle">@lang('app.start_test')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resources as $resource)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>{{ $resource->title ?? ''}}</td>
                                    <td>{{ $resource->description ?? ''}}</td>
                                    <td class="text-center align-middle"> <a
                                            href="{{ url('/test/' . $resource->id . '/edit') }}" class="px-3 text-primary"><i
                                                class="uil uil-stopwatch font-size-24"></i></a>

                                        </a></td>

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
