@extends('layouts.master')
@section('title') @lang('translation.Dashboard') @endsection
@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') Minible @endslot
@slot('title') Dashboard @endslot
@endcomponent

<div class="container">
    <h1 class="display-3">@lang('welcome.dashboard.welcome')</h1>
    <p>@lang('welcome.dashboard.attention')</p>
    <p>@lang('welcome.dashboard.p1')</p>
    <!-- <p><a class="btn btn-primary btn-lg" href="#" role="button">Learn more »</a></p> -->
</div>

<div class="accordion" id="accordionExample">
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingOne">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                aria-expanded="true" aria-controls="collapseOne">
                @lang('welcome.dashboard.step_1')
            </button>
        </h2>
        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
            data-bs-parent="#accordionExample">
            <div class="accordion-body">
                @lang('welcome.dashboard.p2')
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingTwo">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                @lang('welcome.dashboard.step_2')
            </button>
        </h2>
        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
            data-bs-parent="#accordionExample">
            <div class="accordion-body">
            <p>@lang('welcome.dashboard.p3')</p>
            <p>@lang('welcome.dashboard.p4')</p>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingThree">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                @lang('welcome.dashboard.step_3')
            </button>
        </h2>
        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
            data-bs-parent="#accordionExample">
            <div class="accordion-body">
            <p><strong>@lang('welcome.dashboard.p5')</strong></p>
            <p>@lang('welcome.dashboard.p6')</p>
            <p>@lang('welcome.dashboard.p7')</p>
            <p>@lang('welcome.dashboard.p8')</p>
            <p>@lang('welcome.dashboard.p9')</p>
         
            </div>
        </div>
    </div>
</div>

<!--
<div class="container" style="margin-top:50px;">
    <div class="row">
        <div class="col-md-4">
            <h2>1.</h2>
            <p>@lang('welcome.dashboard.p2')</p>
            <p><a class="btn btn-secondary" href="#" role="button">@lang('welcome.dashboard.view_details')</a></p>
        </div>
        <div class="col-md-4">
            <h2>2.</h2>
            <p></p>
            <p><a class="btn btn-secondary" href="#" role="button">@lang('welcome.dashboard.view_details')</a></p>
        </div>
        <div class="col-md-4">
            <h2>3.</h2>
            <p></p>
            <p><a class="btn btn-secondary" href="#" role="button">@lang('welcome.dashboard.view_details')</a></p>
        </div>
    </div>

    <hr>

</div>-->

@endsection