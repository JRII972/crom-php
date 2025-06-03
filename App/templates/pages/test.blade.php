{{-- /var/www/html/App/templates/pages/test.blade.php --}}
@extends('layouts.base')

@section('content')
<div class="card bg-base-100 shadow-xl">
  <div class="card-body">
    <h2 class="card-title">Test du template modulaire</h2>
    <p>Cette page utilise le template base.blade.php qui a été découpé en composants réutilisables.</p>
    <div class="alert alert-success">
      <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
      <span>Si vous voyez cette page, le template modulaire fonctionne correctement !</span>
    </div>
  </div>
</div>
@endsection
