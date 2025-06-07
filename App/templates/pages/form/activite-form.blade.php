{{-- Page détaillée d'une activite --}}
@extends('layouts.full')

@section('head')
<!-- Selectize.js -->
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css">

<!-- Quill Editor (WYSIWYG) -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@section('content')


<div class="navbar bg-base-200">
    <div class="navbar-start">
        <a href="activites.html" class="btn btn-ghost">
            <i class="fas fa-arrow-left"></i>
            Retour aux activites
        </a>
    </div>
    <div class="navbar-center">
        <h1 class="text-xl font-bold" id="page-title">{{ $title ?? 'Créer une nouvelle activite' }}</h1>
    </div>
    <div class="navbar-end">
        <button type="button" id="save-btn" class="btn btn-primary">
            <i class="fas fa-save"></i>
            Enregistrer
        </button>
    </div>
</div>

<!-- Main Content -->
<div class="container mx-auto p-4 max-w-4xl">
    <form id="activite-form" class="space-y-6" method="POST" enctype="multipart/form-data">

        @if(isset($activite))
        <input type="hidden" name="id" value="{{ $activite->getId() }}">
        @endif
        @include('components.activite.form-infos')
        @include('components.activite.form-joueurs')
        @include('components.activite.form-image')
        @include('components.activite.form-description')
        @include('components.activite.form-actions')
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script type="module" src="./assets/js/activite-form.js"></script>
@include('components.activite.scripts')
@endsection