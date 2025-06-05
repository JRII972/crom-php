
{{-- Inclusion des scripts définis dans le contrôleur --}}
@if(isset($scripts) && is_array($scripts))
    @foreach($scripts as $script)
        <script src="/assets/js/{{ $script }}"></script>
    @endforeach
@endif

{{-- Scripts supplémentaires spécifiques à certaines pages --}}
@yield('scripts')