@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $page_title }}</h2>
    
    <div class="contact-info">
        <p><strong>Email:</strong> {{ $contact_email }}</p>
        <p><strong>Téléphone:</strong> {{ $contact_phone }}</p>
    </div>
    
    <div class="contact-form">
        <h3>Envoyez-nous un message</h3>
        
        @if(isset($errors) && !empty($errors))
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors as $field => $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="/contact/submit" method="POST">
            <div class="form-group">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" value="{{ $old['name'] ?? '' }}" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ $old['email'] ?? '' }}" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="message">Message</label>
                <textarea id="message" name="message" class="form-control" rows="5">{{ $old['message'] ?? '' }}</textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
    </div>
</div>
@endsection
