@extends('layouts.master')
@section('title')
AMID
@endsection
@section('css')
<!-- DataTables -->
<link href="{{ asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle')
@lang('app.disciplines')
@endslot
@slot('title')
@endslot
@endcomponent


<div class="row">
    <div class="col-lg-12">
        <div class="card border border-primary">
            <div class="card-header bg-transparent border-primary">
                <h5 class="my-0 text-primary"><i class="fas fa-graduation-cap icon"></i> @lang('app.disciplines')</b>
                </h5>
            </div>
            <div class="card-body">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <img src="assets/images/paypal.png" style="width:64px;"> {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <p> <img src="assets/images/paypal.png" style="width:64px;"> {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>

                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('app.title')</th>
                            <th>@lang('app.modules')</th>
                            <th>@lang('app.price')</th>
                            <th class="text-center align-middle">@lang('app.enroll')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($disciplines as $discipline)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $discipline->title ?? ''}}</td>
                                <td>{{ $discipline->module->name ?? ''}}</td>
                                <td>{{ $discipline->amount ?? ''}}</td>
                                <td class="text-center align-middle"> <a
                                        href="{{ url('/paypal/payment/' . $discipline->id) }}"
                                        class="px-3 text-success"><img src="assets/images/paypal.png" style="width:64px;">
                                        </img></a></td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>
        </div>
    </div> <!-- end col -->
</div> <!-- end row -->
@endsection
@section('script')
<script src="{{ asset('/assets/libs/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('/assets/libs/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('/assets/js/pages/datatables.init.js') }}"></script>

@endsection
