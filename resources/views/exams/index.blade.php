@extends('exams.layouts.app', ['nav_transparent' => false])

@section('title')
الرئيسية - الإختبارات
@endsection

@section('css')
<style>
    body {
        background-color: #eee;
    }
</style>
@endsection

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-lg-7 col-md-8 col-12 mx-auto">
            @if(Session::has('exam_done'))
            <div class="alert alert-success">
                {{ Session::get('exam_done') }}
            </div>
            @endif

            @if(Session::has('error'))
            <div class="alert alert-danger">
                {{ Session::get('error') }}
            </div>
            @endif
            <div class="bg-white rounded">
                <div class="p-3 bg-light">
                    <nav>
                        <span class="text-black-50">
                            الإختبارات
                        </span> /
                        <span class="text-black-50">
                            {{ auth('students')->user()->level->name_ar }}
                        </span> /
                        <span>
                            الطالب {{ auth('students')->user()->name }}
                        </span>
                    </nav>
                </div>
                <div class="p-3">
                    <h6 class="fw-bold mb-3">
                        الإختبارات
                    </h6>
                    @forelse ($exams as $exam)
                    <a class="mb-3 bg-light p-3 rounded mb-2 exam-info-container d-block"
                    href="{{ route('students.exams.show', $exam->id) }}">
                        <div class="info">
                            إختبار ال{{ $exam->subject->name_ar }}
                            -
                            {{ $exam->level->name_ar }}
                            -
                            أ/ {{ $exam->teacher->profile->name }}
                        </div>
                        <small>
                            المدة: {{ $exam->duration }} دقيقة
                        </small>
                    </a>
                    @empty
                    <div class="alert alert-info mb-0">
                        لا يوجد إختبارات حتى الآن
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection