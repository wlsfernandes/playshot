<!DOCTYPE html>
@extends('layouts.master')

@section('title')
@lang('app.test')
@endsection

@section('content')
@component('common-components.breadcrumb')
@slot('pagetitle') AMID @endslot
@slot('title') Test @endslot
@endcomponent

<!-- add Resource -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">@lang('app.test_instructions')</div>
        </div>
    </div>
</div>
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
                <div class="text-center mb-4">
                    <h5>Time Remaining: <span style="color:orangered" id="timer"></span></h5>
                </div>
                <p class="card-title-desc"></p>

                <form action="{{ route('submitTest') }}" method="POST">
                    @csrf
                    <input type="hidden" name="resource_id" value="{{$resource->id}}" />
                    <input type="hidden" name="test_id" value="{{$test->id}}" />
                    <div class="mb-3 row">
                        <label for="title" class="col-md-2 col-form-label">@lang('app.title'):</label>
                        <div class="col-md-6">
                            <input class="form-control" type="text" value="{{ old('title', $resource->title ?? '') }}"
                                id="title" name="title" required>
                        </div>
                    </div>

                    @if($resource->url)
                        <div class="mb-3 row">
                            <label for="file" class="col-md-2 col-form-label">Click here to download your Test:</label>
                            <div class="col-md-6 d-flex align-items-center">
                                <a href="{{ $resource->url }}" target="_blank" class="me-2"
                                    style="font-size: 18px; text-decoration: none;">
                                    <i class="uil uil-file-plus font-size-24"></i>
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
                            class="btn btn-primary waves-effect waves-light">@lang('app.finish_test')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> <!-- end row -->

@endsection
@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const timerDisplay = document.getElementById('timer');
        const countdownKey = 'countdown_timer';
        const defaultCountdown = 50 * 60; // 50 minutes in seconds

        // Retrieve the countdown value from localStorage or initialize it
        let countdown = localStorage.getItem(countdownKey)
            ? parseInt(localStorage.getItem(countdownKey), 10)
            : defaultCountdown;

        function updateTimer() {
            const minutes = Math.floor(countdown / 60);
            const seconds = countdown % 60;
            timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            countdown--;

            // Save the remaining time to localStorage
            localStorage.setItem(countdownKey, countdown);

            if (countdown < 0) {
                clearInterval(timerInterval);
                localStorage.removeItem(countdownKey); // Clear storage when timer ends
                alert('Time is up!');
                submitForm(); // Auto-submit the form
            }
        }

        function submitForm() {
            const form = document.querySelector('form');
            const formData = new FormData(form);

            fetch(form.action, {
                method: form.method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Form submitted successfully:', data);
                })
                .catch(error => {
                    console.error('Error submitting the form:', error);
                });
        }

        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
    });
</script>

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
