@extends('layouts.base')

@section('content')
<div class="container">
    <h2>{{ $page_title }}</h2>
    
    <div class="alert alert-success">
        <p>Merci {{ $name }} ! Votre message a été envoyé avec succès.</p>
        <p>Nous vous répondrons dans les plus brefs délais.</p>
    </div>
    
    <a href="/contact" class="btn btn-primary">Retour au formulaire</a>
    <a href="/" class="btn btn-secondary">Retour à l'accueil</a>
</div>
@endsection
