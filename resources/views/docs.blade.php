@extends('apidocs::layout')

@section('main')

@include('apidocs::header')


<div id="background">
    <div class="background-actual"></div>
</div>


<div id="documenter_content" class="method-area-wrapper">

@foreach ($endpoints as $name => $endpoint)
    @include('apidocs::section', ['endpoints' => $endpoint])
@endforeach

</div>



@endsection
