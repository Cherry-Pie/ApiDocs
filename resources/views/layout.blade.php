<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ApiDocs</title>
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700,800,900">
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600,700,800,900">
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="/vendor/yaro/apidocs/css/style.css" media="all">
        <link rel="stylesheet" type="text/css" href="/vendor/yaro/apidocs/css/responsive.css">
        <link rel="stylesheet" type="text/css" href="/vendor/yaro/apidocs/css/jquery.json-viewer.css">
        <link rel="stylesheet" type="text/css" href="/vendor/yaro/apidocs/css/easyautocomplete.css">
        
    </head>
    <body id="apidocs-api">
        <div id="page">
            <header class="header" id="header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-2 col-sm-2 logo-main">
                            <a class="header__block header__brand" href="javascript:void(0);"> <h1><img src="{{ config('yaro.apidocs.logo') }}"></h1> </a>
                        </div>
                        <div class="col-lg-10 col-sm-10 hidden-xs">
                            @include('apidocs::partials.menu_top')                            
                        </div>
                    </div>
                </div>
            </header>

            <div class="header-section-wrapper">
                <div class="header-section header-section-example">
                    <div id="language">
                        <ul class="language-toggle">
                            <li>
                                <input onchange="changeTab('response')" type="radio" class="language-toggle-source" name="language-toggle" id="toggle-lang-response" checked="checked">
                                <label for="toggle-lang-response" class="language-toggle-button language-toggle-button--response">response</label>
                            </li>
                            <li>
                                <input onchange="changeTab('request-headers')" type="radio" class="language-toggle-source" name="language-toggle" id="toggle-lang-request-headers">
                                <label for="toggle-lang-request-headers" class="language-toggle-button language-toggle-button--request-headers">request headers</label>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            @yield('main')
            
        </div>
        
        
        <script type="text/template" id="header-row-template">
            <div class="form-group" style="height: 30px;">
                <div class="col-sm-1">
                    <div class="checkbox" style="margin-top: -7px;">
                        <label style="font-size: 2em">
                            <input type="checkbox" value="1" checked class="req-header-active">
                            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <input type="text" class="form-control req-header"  placeholder="Header" value="">
                </div>
                <div class="col-sm-6">
                    <input type="text" class="form-control req-header-value"  placeholder="Value" value="">
                </div>
                
                <div class="col-sm-1">
                    <a class="btn btn-default" href="javascript:void(0);" role="button" onclick="$(this).closest('.form-group').remove()">
                            <span class="cr"><i class="cr-icon fa fa-times"></i></span></a>
                </div>
            </div>
        </script>
        
        <script type="text/template" id="preloader-template">
            <div class="preloader">
              <div class="status">
                 <div class="spinner">
                  <div class="rect1"></div>
                  <div class="rect2"></div>
                  <div class="rect3"></div>
                  <div class="rect4"></div>
                  <div class="rect5"></div>
                </div>
              </div>
            </div>
        </script>
        
        <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
        
        <script type="text/javascript" src="/vendor/yaro/apidocs/js/tendina.min.js"></script>
        <script type="text/javascript" src="/vendor/yaro/apidocs/js/jquery.waypoints.min.js"></script>

        <script type="text/javascript" src="/vendor/yaro/apidocs/js/jquery.json-viewer.js"></script>
        <script type="text/javascript" src="/vendor/yaro/apidocs/js/bootstrap-notify.min.js"></script> 
        
        <script type="text/javascript" src="/vendor/yaro/apidocs/js/easyautocomplete.min.js"></script>
        
        <script type="text/javascript" src="/vendor/yaro/apidocs/js/app.js"></script>

    </body>
</html>