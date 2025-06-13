
{{-- Inclusion des scripts définis dans le contrôleur --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.9.0/axios.min.js" integrity="sha512-FPlUpimug7gt7Hn7swE8N2pHw/+oQMq/+R/hH/2hZ43VOQ+Kjh25rQzuLyPz7aUWKlRpI7wXbY6+U3oFPGjPOA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@if(isset($scripts) && is_array($scripts))
    @foreach($scripts as $script)
        <script src="/assets/js/{{ $script }}"></script>
    @endforeach
@endif

@if(isset($modules) && is_array($modules))
    @foreach($modules as $module)
        <script type="module" src="/assets/js/{{ $module }}"></script>
    @endforeach
@endif


{{-- Scripts supplémentaires spécifiques à certaines pages --}}
@yield('scripts')