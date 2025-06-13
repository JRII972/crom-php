<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page_title ?? 'Mon Application' }}</title>
    @if(isset($styles) && is_array($styles))
    @foreach($styles as $style)        
    <link rel="stylesheet" href="{{ $baseURL }}/assets/css/{{ $style }}">
    @endforeach
    @endif
@endif
</head>
<body>
    <header>
        <h1>Mon Application</h1>
    </header>
    
    <main>
        @yield('content')
    </main>
    
    <footer>
        <p>&copy; {{ date('Y') }} Mon Application</p>
    </footer>
</body>
</html>
