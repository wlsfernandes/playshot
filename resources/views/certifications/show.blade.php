@extends('layouts.master')
@section('title')
Certifications
@endsection

@section('css')
<!-- plugin css -->
<link href="{{ asset('/assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/assets/libs/spectrum-colorpicker/spectrum-colorpicker.min.css') }}" rel="stylesheet">
<link href="{{ asset('/assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<link href="{{ asset('/assets/libs/bootstrap-touchspin/bootstrap-touchspin.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('/assets/libs/datepicker/datepicker.min.css') }}">
<link rel="stylesheet" href="{{ asset('/assets/libs/flatpickr/flatpickr.min.css') }}">
@endsection


@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle')
AMID
@endslot
@slot('title')
Certifications
@endslot
@endcomponent


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
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('certifications.update', $certification->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3 row">
                        <label for="name" class="col-md-2 col-form-label">Name:</label>
                        <div class="col-md-6">
                            <input class="form-control" type="text" value="{{ $certification->name ?? ''}}" id="name"
                                name="name" required>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-3">
                        <button type="submit" class="btn btn-secondary waves-effect">
                            <a href="{{ url('/certifications') }}" class="btn btn-secondary waves-effect">
                                <i class="bx bx-arrow-back"></i> Go Back
                            </a>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end col -->
</div>
<!-- end row -->

@endsection
