

@if (!isset($endpoints['hash']))
    @foreach ($endpoints as $endpoint)
        @include('apidocs::section', ['endpoints' => $endpoint])
    @endforeach
@else
<section id="{{ $endpoints['hash'] }}" class="method">
        <div class="method-area">
            <div class="method-copy">
                <pre class="pull-left language-none"><code class=" language-none">{{ $endpoints['uri'] }}</code></pre>
                <div class="method-copy-padding">
                    <div class="pull-right">
                        <h5> @foreach ($endpoints['methods'] as $method) <span class="label label-info">{{ $method }}</span> @endforeach </h5>
                    </div>
                    <h3>{{ $endpoints['docs']['title'] }} </h3>
                    <h5>{{ $endpoints['docs']['description'] }}</h5>
                    <hr>
                    @if ($endpoints['docs']['params'])
                    <p>
                        &nbsp
                    </p>
                    @endif
                    <form class="form-horizontal" enctype="multipart/form-data" autocomplete="off" action="{{ url($endpoints['uri']) }}" method="{{ $endpoints['methods'][0] }}" onsubmit="sendRequest(this);return false;">

                        @foreach ($endpoints['docs']['params'] as $param)
                        @if (!in_array($param['name'], $endpoints['docs']['uri_params']))
                            @include('apidocs::input.'. $param['template'])
                        @endif
                        @endforeach

                        <div class="form-group">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-default pull-right">
                                    Send
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <div class="method-example">
                <div class="method-tab response">
                    <form class="form-horizontal" autocomplete="off" onsubmit="return false;">

                        <div class="form-group" style="height: 30px;">
                            <div class="col-sm-12">
                                <input type="text" disabled class="form-control action-url" readonly data-original="{{ url($endpoints['uri']) }}" value="{{ url($endpoints['uri']) }}">
                            </div>
                        </div>
                        <hr style="width: 100%;">
                        @foreach ($endpoints['docs']['uri_params'] as $param)
                        <div class="form-group" style="height: 35px;">
                            <label class="col-sm-2 control-label"> <h4 style="margin-top: -5px;"><span class="label label-info">{{ '{'. $param .'}' }}</span></h4> </label>
                            <div class="col-sm-10">
                                <input type="text"
                                class="form-control"
                                placeholder="{{ isset($endpoints['docs']['params'][$param]) ? $endpoints['docs']['params'][$param]['description'] : $param }}"
                                name="{{ $param }}"
                                readonly
                                onkeyup="changeApiUrl(this)"
                                onfocus="if (this.hasAttribute('readonly')) { this.removeAttribute('readonly'); this.blur(); this.focus(); }">
                            </div>
                        </div>
                        @endforeach
                    </form>

                    <div class="method-example-part">
                        <div class="method-example-endpoint" style="position: relative;">
                            <a href="javascript:void(0)" class="btn btn-default btn-xs" style="position: absolute; right: 3px; top: 23px;" onclick="changeSourceView(this)">
                                <span class="cr"><i class="cr-icon fa fa-eye"></i></span>
                            </a>
                            <pre class="language-none"><code class="language-none response-content response-highlighted"></code></pre>
                            <pre class="language-none" style="display:none"><code class="language-none response-content response-raw"></code></pre>
                        </div>
                        <div class="method-example-endpoint">
                            <pre class="language-none"><code class="language-none response-headers"></code></pre>
                        </div>
                        
                        
                    </div>
                </div>
                
                <div class="method-tab request-headers" style="display:none">
                    
                        
                    <form class="form-horizontal headers-form" autocomplete="off" onsubmit="return false;">
                        
                        <div class="form-group except" style="height: 30px;">
                            <div class="col-sm-1 pull-right">
                                <a class="btn btn-default" href="javascript:void(0);" role="button" onclick="addNewHeaderInput(this)">
                                    <span class="cr"><i class="cr-icon fa fa-plus"></i></span>
                                </a>
                            </div>
                        </div>
                    </form>                                
                </div>
                    
                    
                    
                    
            </div>
        </div>
    </section>
@endif



