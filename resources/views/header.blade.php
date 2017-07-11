<div id="documenter_sidebar">
    <div id="scrollholder" class="scrollholder">
        <div id="scroll" class="scroll">
            <ul id="documenter_nav">
                @foreach ($endpoints as $name => $endpoint)
                    @include('apidocs::sidebar_section', ['endpoints' => $endpoint, 'name' => $name])
                @endforeach
            </ul>
        </div>
    </div>
</div>