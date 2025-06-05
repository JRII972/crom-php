{{-- Page de connexion modulaire --}}
@extends('layouts.auth')

@section('content')
<div class="flex flex-col lg:flex-row gap-8 w-min md:w-auto">
    <!-- Activite gauche - Image et texte d'introduction -->
    @include('components.auth.login-illustration')

    <!-- Activite droite - Formulaire de connexion -->
    @include('components.auth.login-form')
</div>
@endsection

@section('scripts')
@include('components.auth.login-scripts')
@endsection
