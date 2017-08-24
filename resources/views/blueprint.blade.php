FORMAT: 1A
HOST: {{ $host }}

# {{ $title }}
{{ $introduction }}

@foreach ($endpoints as $group => $groupEndpoints)
## Group {{ $group }}

@foreach ($groupEndpoints as $endpoint)
### {{ $endpoint['docs']['title'] }} [{{ $endpoint['methods'][0] }} /{{ $endpoint['uri'] }}]
{{ $endpoint['docs']['description'] }}
@if ($endpoint['docs']['uri_params'])
+ Parameters
@foreach ($endpoint['docs']['uri_params'] as $param)
    + {{ $param }} {{ isset($endpoint['docs']['params'][$param]['description']) ? '- '. $endpoint['docs']['params'][$param]['description'] : '' }}
@endforeach
@endif

@if ($endpoint['docs']['params'])
+ Attributes
@foreach ($endpoint['docs']['params'] as $param)
@if (!in_array($param['name'], $endpoint['docs']['uri_params']))
    + {{ $param['name'] }} {{ $param['description'] ? '- '. $param['description'] : '' }}
@endif
@endforeach
@endif

@endforeach
@endforeach
