@extends('layouts.admin')

@section('title', 'Edit Blog')

@section('content')
    <h1>Edit Blog</h1>
    <p class="page-subtitle">Update an existing post.</p>

    <div class="admin-panel">
        <form method="POST" action="{{ route('admin.blogs.update', $blog->id) }}" enctype="multipart/form-data" id="blogForm">
            @csrf
            @method('PUT')
            @include('admin.blogs._form', ['blog' => $blog])

            <div class="form-actions">
                <button type="submit" class="jy-btn"><i class="fa-solid fa-save"></i> Save Changes</button>
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
