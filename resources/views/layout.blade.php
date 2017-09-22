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
        @include('apidocs::assets.css.style')
        @include('apidocs::assets.css.responsive')
        @include('apidocs::assets.css.jquery-json-viewer')
        @include('apidocs::assets.css.easyautocomplete')
    </head>
    <body id="apidocs-api">
        <div id="page">
            <header class="header" id="header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-2 col-sm-2 logo-main">
                            <a class="header__block header__brand" href="javascript:void(0);"> <h1><img src="{{ config('yaro.apidocs.logo') ?: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFAAAAAsCAIAAAB9k6hAAAAACXBIWXMAAAsSAAALEgHS3X78AAAMYUlEQVRogeVZe0xUVxo/59w7dx4wgLCCVV6CvHxAq4xWoGotPiCmoZhgmzRxpS1ETEEUN13XZWlIXEw3djV1ldYWUm2ttQ+a1FSkKlollQoIyMwII8yAMIC8hhkG5s695+wfJ4w4sjysYHb3+2Ny597vfOf7nfM9z4GEEPD/RGiW56Pra7VaeZ53/J1NmlXAGGMIYXd3986dOwsKCkRRhBDONmYyK4QxFkWREGI0GlNSUgAArq6uhw8f5nmefp0dNQghMw64vb3N2NVBn41G42uvvQYAUCqVEomE47hDhw4JgkC/dnV3tt03zLQ+7Iyaj6GtpUFdzzKsNJqzDdszdmV8//33Li4uIyMjCCFCSF5eHgBgT3Z2v6m/prZKEHlREAICggAhAMKZUGlGXIgQAiFsbdXf0dSKoiiVSDBGhSdOlpSUKBQKu91OGShmjPGhQwWrY1c+6HnAMBBCtCRiWYB/EOV56rpNCfB0FwVCqG9t1mobBEFgWJZgcPSfH5WWXnSgpTwAAISQKIpyhfzdd3dtStg4ODgAIGQQWro4ys834AnmnZRnSiY93ZVu0d/TNqoxFjgZJ9jxPz44fOVyuVwut9vtTtIwxizLWoes//roBELMxk2vmC2DGOMGTT0EwNc3YFrzToUm2WFqV52d7dZh66SwMSEyqcwyZNbr7/F2O8uyI1bbkSPHrpZflUqlNAmNOxAhZLPZPD09U1P/uClhA2+3YYw5VhK4cJHSVWkdHkJwkvRJCHFxUfp4z5vUESbZYTrY1dVNKpNBgACYYHUgJlgi4czmQQFjBiEACCeV9vX1T2ofGGO5XN7X1yfhOEIwIQBCKGARAaBUukml0skBA8BJODAFY3z6QUsUxUadprnlHgCYZbmBvoG/H/ygtrZWKpVijMdXAkKe5/fu3b1x8wZBtGOMEUShoRFBgYsQesql0VMWRwhhGCYsJCJo4SIAkN1um+vzhz/9ee8LLyy32Wzjag8htNuFrOzMTYkbMREIIQihkJDw4IUhNIxPceYpajhTlR0hpLFJrWtuxJjI5XJTn+nDDz+6ceOG0z7TvT3w17+8/Moam21EEESEUPDC4LDQJTORk8DM1dIQwtCQJcFBYQhBwS6sWfvyqVOn1q9f77TPPM/n5eXl/S0vdFE4AAAhEBQYHBa6dIbQAjDztfTdRk2zXkefDQZDfHw8AEAqlUokEghhfn6+o7TU65u1d+/MtD6z1DwQQmjzcOfOnZUrVwIAGIbZs2eP1Wol/2PNw1iimDUaTVxcXFZW1sjIiKOLmjWa7XaUEAIh7OvrgxB6eHgQQp564pmYZr3/HsU8y5M66BkABqPdyDOB/WwAP0OaqJZ+ivswwbLO8j7/xx3+nZ5GCBEEASHEMMwEAmkSZhhm1kLXRCZtMpk4jpPL5b8T/MjIyMDAgJubGyHEZrPRs0uEEMdxLi4ulEcURcfSzCiNY9IYY4SQ0WjMysrasWNHQkLCtABTZrPZnJ+fHx0dnZKSwvN8aWnpl19+CSGMi4tTKpWCIJjN5rt373Z1dcXGxmZkZDz33HOzFL0fT8306PSzzz4DAOTk5NhsNjKdYohy6nQ6FxeXbdu2EUJEUWxtbVWpVH5+fmq1uqOjo62tTa/XNzQ0nD59euXKlcHBwWfOnHmSMmL65AxYFEWMcX9///bt2wEAwcHBWq2WjBZJUyee5ysrK/V6Pf1rs9m2bNmydOnSkZERJ862traoqCiFQnH58uUnRTENcg4V1MHOnTsnk8n2799/7969srIyAMB0g4pEIlGpVAEBAYQQAADP84IgYIwpYMfyiaLo6+t78OBBAMC+ffusVuvj1jepeU7lpYNYJ1aWZQVB+Pnnn6Ojo9PS0s6cOfP555+/8cYbXl5eZNTHWlpabt++rdVqN2zYMHfu3KNHj1ZVVc2bNy8jI2PNmjUAAEEQWlparl+//uDBg5ycHAihwznhGAIA0BZ/7dq1YWFhNTU1Op0uMjISANDd3f3JJ5/odDqO40RRfPXVV7ds2UIXnaqh1+tLSkra29ubm5tlMtmOHTvi4+OHh4d/+umniooKk8k0ODiYlJSUnJwslUrJ2Ogwdrtpkjh//vzGjRtv3bpFCElLSwMAlJaWklHnFATh448/jouLc3d3T09PT01NPXTo0M6dOz09PSGEX331Fcb42rVrqampLMvGxsbSURaLZfPmzYsXLzaZTORRB6EHt4mJiQCAsrIyQohWqw0PD9+0aZPRaCSEXLhwwdfXNzMzUxAEOrCkpCQ+Pp5q2NPTs3379gMHDgiC8N577+Xl5VGx3333XWpqalNTk9N0DwFjjDHGgiDs3r07IyODvqyoqPDy8nr99depKTpCV1FREQAgPT3d4ZNff/21XC5fvny5wWCg67Jo0aKYmJgpAqZXMJcvXxZFcdu2bV5eXmq1moxG0CNHjgAAvvnmG0JIU1PTggULzp4965BQVVX17bffajSa0NDQiooKx/vKysr79++TRyMuGmvPEMLa2try8vKgoCCNRlNXV6dUKv39/c+ePavX68dyDg0NcRyXlJTEcZzNZhNFMTk5OSUlpbq6ur29nRBis9mmlWOGh4cZhvHx8ent7S0tLY2Ojo6IiLDb7fR68aWXXvLy8iotLQUAnDp1ShTFqKgoh69GRUUlJydzHGexWDIzM0+fPl1dXd3Z2alSqRYsWAAeLeYeAoYQYowvXrzY3d3d3t5+/PjxwsLCkydPent7syx77tw5hyc4nFAQBAghwzCiKAIAAgMDWZYlo9coU8FJNbZYLB0dHQEBAYGBgRaLZWBgICIiwiEHQujt7e3v708XvbGx0Wq1ymSyhxgQwhj7+fnl5uaazea0tLRVq1bFx8cfPXrUYrGARwtbduzEOp3uwoUL+fn5qampFouFZVmEUGdnZ2Ji4qeffvrOO+94e3s73RvQEzkKu6+vLyQkxN/ff1KEjgee56VSaVlZWV1d3cGDBxUKBYRQIpFQOxzLKQiCA+Tg4GBvb6+/vz/NKXRpJBJJenp6UlJSVVXVzZs3r1y5kpOT4+vrm5ycPDZoIYdQCCHNhFu3brXb7QqFQiaTIYT8/f03b96s1+t/+eUXp/trd3d3hBDP8xKJxGQyVVRUvPnmm/Pnz6eXDAghxzkrGS2YqQQy2pZIpdLm5uZ9+/bFxMRkZGQAADw9PcPCwtRqNTUcnuchhL29vUaj8cUXXwQAREZGIoQKCwvpKtNyHSHU29t769YtHx+fxMTE999//9q1a0uWLNFoNMCpP3H4dE9Pz+rVq7Ozs2kgobtHo0tNTY2Hh8eKFSuGhoaouoWFhQzD7N27t6urixDS2dm5a9eut956q7Ozk0oTRXHhwoWrVq2i/DzPr1u3Ljw83GKxOOJHe3t7UVHRmjVrUlJSdDqd431xcfH8+fPLy8vpX1EUMzMz161bp9frRVHU6/WxsbEAgNzc3Kampv7+/urq6jt37jQ0NLz99ttjhW/durWkpMQpRjJ5eXkQwtbW1oKCgsrKSolEolAo/Pz86AEywzBVVVU//vhjc3Nzd3d3f39/VFSUq6trTU3NDz/80NHR8euvv9bW1lZWVs6dOzc7O3vevHkAAK1WW1xcXFtbS/c5IiKiuLj4+vXrAICenh61Wn3z5s3y8vLffvvNYDCsX78+NzfXx8fHYXjLli1zc3MrKSkxGo1qtfr8+fPDw8P79+8PCwvDGM+ZM+f55583mUxffPHF1atXL126VF9fv2LFCldX1xMnTmg0Gp1Od/fu3Rs3bsTGxiYkJLAs+0ghQK3rwYMHt2/fdnNzM5vN7u7ukZGRFDBCyGAwNDY2KhQKarcqlcrDw+P48eNZWVkFBQWBgYGDg4OhoaExMTFgtOkxGo1arZbjOCpcpVLRnIkQslgs9IBWEIQ5c+ZEREQoFAowXrdUX1+v1+shhBzHRUdHe3p6Un3o78DAQF1dncFgQAiFhoaqVCqbzVZTU2MwGGQymVwu9/HxWbZs2TixkzwRHTt2TCaTXbp0aezLJzt/pCXn2DfUmx5nG/d5YnpczsMo7YiKTkll7CcwWgxSMpvNtGygZuMY9bg0OrHTWlNLe7wNpubndCMzlo1hmLECHVPT7OhUxjoJZ8eV6DS90yeHFJlMJpFI6MnGxEOe4EBj4iHjfp3SEcIUbcNBPM9nZ2cHBgYCAMLDw4uKishoefhfQU9yatnV1SWKIkJIEASlUunu7j5dCc+Q/g2Xv/q7yI8jgQAAAABJRU5ErkJggg==' }}"></h1> </a>
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
                    <a class="btn btn-default" href="javascript:void(0);" role="button" onclick="removeNewHeaderInput(this)">
                            <span class="cr"><i class="cr-icon fa fa-times"></i></span></a>
                </div>
            </div>
        </script>
        
        <script type="text/template" id="header-global-row-template">
            <div class="form-group" style="height: 30px;">
                <div class="col-sm-1">
                    <div class="checkbox" style="margin-top: -7px;">
                        <label style="font-size: 2em">
                            <input type="checkbox" value="1" checked class="req-header-active">
                            <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                        </label>
                    </div>
                </div>
                <div class="col-sm-1">
                    <a class="btn btn-default" href="javascript:void(0);" role="button" onclick="saveGlobalHeader(this)">
                        <span class="cr"><i class="cr-icon fa fa-save"></i></span>
                    </a>
                </div>
                <div class="col-sm-4">
                    <input type="text" class="form-control req-header"  placeholder="Header" value="">
                </div>
                <div class="col-sm-5">
                    <input type="text" class="form-control req-header-value"  placeholder="Value" value="">
                </div>
                
                <div class="col-sm-1">
                    <a class="btn btn-default" href="javascript:void(0);" role="button" onclick="removeGlobalHeaderInput(this)">
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
        
        @include('apidocs::assets.js.jquery-2-2-4')
        @include('apidocs::assets.js.tendina')
        @include('apidocs::assets.js.jquery-waypoints')
        @include('apidocs::assets.js.jquery-json-viewer')
        @include('apidocs::assets.js.bootstrap-notify')
        @include('apidocs::assets.js.easyautocomplete')
        @include('apidocs::assets.js.app')

    </body>
</html>
