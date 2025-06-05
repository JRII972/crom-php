<div class="breadcrumbs text-sm md:text-md lg:text-lg">
    <ul>
        @foreach($breadcrumbs as $index => $breadcrumb)
            <li class="{{ $loop->last ? 'active' : '' }}">
                @if(!$loop->last && !empty($breadcrumb['location']))
                    <a href="{{ $breadcrumb['location'] }}">
                        {{ $breadcrumb['titre'] }}
                    </a>
                @else
                    {{ $breadcrumb['titre'] }}
                @endif
            </li>
        @endforeach
    </ul>
</div>

