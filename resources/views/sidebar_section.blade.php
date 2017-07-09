


@if (!isset($endpoints['hash']))

    <li>
        <a href="javascript:void(0);" title="{{ $name }}">{{ $name }}</a>
        <ol>
            @foreach ($endpoints as $name => $endpoint)
                @include('apidocs::sidebar_section', ['endpoints' => $endpoint, 'name' => $name])
            @endforeach
        </ol>
    </li>

@else

<li>
    <a href="#{{ $endpoints['hash'] }}" title="{{ $endpoints['name'] }}">{{ $endpoints['name'] }}</a>
</li>

@endif


