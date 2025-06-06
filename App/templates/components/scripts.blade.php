
{{-- Inclusion des scripts définis dans le contrôleur --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

@if(isset($scripts) && is_array($scripts))
    @foreach($scripts as $script)
        <script src="/assets/js/{{ $script }}"></script>
    @endforeach
@endif

{{-- Scripts supplémentaires spécifiques à certaines pages --}}
@yield('scripts')