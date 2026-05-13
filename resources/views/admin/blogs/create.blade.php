@extends('layouts.admin')

@section('title', 'New Blog')

@section('content')
    <h1>New Blog</h1>
    <p class="page-subtitle">Compose a new blog post.</p>

    <div class="admin-panel">
        <form method="POST" action="{{ route('admin.blogs.store') }}" enctype="multipart/form-data" id="blogForm">
            @csrf
            @include('admin.blogs._form', ['blog' => null])

            <div class="form-actions">
                <button type="submit" class="jy-btn"><i class="fa-solid fa-save"></i> Publish Blog</button>
                <a href="{{ route('admin.blogs.index') }}" class="jy-btn secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('head')
    @include('admin.blogs._ckeditor')
@endpush

@push('scripts')
    @include('admin.blogs._form_scripts')
@endpush
