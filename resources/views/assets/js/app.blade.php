<script>
$(window).on('resize', function() {
    $('.global-headers-section').width($('.method-copy').width() - 30);
});

$(document).ready(function(){
    $('#documenter_nav').tendina({activeMenu: '.current'});
    
    var hash = window.location.hash;
    var a = $('a[href="' + hash + '"]', '#scroll');
    if (a.length) {
        $('a[href="'+ hash +'"]', '#scroll')[0].click();
    } else {
        window.location.hash = $($("a[href^='#']")[0]).attr('href');
    }
    $('section.method').each(function() {
        /** global: Waypoint */
        new Waypoint({
            element : this,
            handler : function() {
                var id = this.element.attributes['id'].nodeValue;
                //
                $('a', '#scroll').removeClass('current');
                $('ul', '#scroll').removeClass('open');
                var a = $('a[href="#' + id + '"]', '#scroll');
                a.addClass('current');
    
                var ul = a.closest('ul');
                if (ul.length) {
                    openUl(ul);
                }
                if (a.next().is('ul')) {
                    a.next().addClass('open');
                }
                window.history.replaceState(null, null, document.location.pathname + document.location.search + '#' + id);
            }
        })
    });
    $("a[href^='#']").on('click', function() {
        var a = this; 
        $('html, body').animate({
            scrollTop: $($(a).attr('href').replace(new RegExp(/\./, 'g'), '\\.').replace(new RegExp(/\:/, 'g'), '\\:')).offset().top 
        });
        return false;
    });
    
    autocompleteHeaders();

    setHeadersCheckboxEvents();
}); 

function setHeadersCheckboxEvents()
{
    $('input[type=checkbox]', '.global-headers-form').change(function() {
        recalculateGlobalHeaders();
    });
}

function autocompleteHeaders()
{
    $('.req-header').easyAutocomplete({
        data: [
            'Accept',
            'Accept-Charset',
            'Accept-Encoding',
            'Accept-Language',
            'Accept-Datetime',
            'Access-Control-Request-Method',
            'Access-Control-Request-Headers',
            'Authorization',
            'Cache-Control',
            'Connection',
            'Cookie',
            'Content-Length',
            'Content-MD5',
            'Content-Type',
            'Content-Disposition',
            'Date',
            'Expect',
            'Forwarded',
            'From',
            'Host',
            'If-Match',
            'If-Modified-Since',
            'If-None-Match',
            'If-Range',
            'If-Unmodified-Since',
            'Max-Forwards',
            'Origin',
            'Pragma',
            'Proxy-Authorization',
            'Range',
            'Referer',
            'TE',
            'User-Agent',
            'Upgrade',
            'Via',
            'Warning',
            'X-Requested-With',
            'DNT',
            'X-Forwarded-For',
            'X-Forwarded-Host',
            'X-Forwarded-Proto',
            'Front-End-Https',
            'X-Http-Method-Override',
            'X-ATT-DeviceId',
            'X-Wap-Profile',
            'Proxy-Connection',
            'X-UIDH',
            'X-Csrf-Token',
            'X-Request-ID',
            'X-Correlation-ID',
        ],
        list: {
            match: {
                enabled: true
            }
        },
    });
}

function openUl(ul)
{
    ul.addClass('open');
    var ul = ul.parent().closest('ul');
    if (ul.length) {
        openUl(ul);
    }
}

function sendRequest(form) 
{
    $('#toggle-lang-response').trigger('click');
    
    var $form = $(form);
    var $section = $form.closest('section');
    
    var $btn = $form.find('button[type="submit"]');
    $btn.html($('#preloader-template').html()).attr('disabled', true);
    
    var headers = {};
    $section.find('.headers-form .form-group').not('.except').each(function(key, element) {
        var $el = $(element);
        if ($el.find('.req-header-active').is(':checked')) {
            var header = $el.find('.req-header').val();
            headers[header] = $el.find('.req-header-value').val();
        }
    });
    $('.global-headers-form .form-group').not('.except').each(function(key, element) {
        var $el = $(element);
        if ($el.find('.req-header-active').is(':checked')) {
            var header = $el.find('.req-header').val();
            headers[header] = $el.find('.req-header-value').val();
        }
    });
    
    $.ajax({
        url : $section.find('.action-url').val(),
        headers: headers,
        type : $form.attr('method'),
        data : $form.attr('method') == 'GET' ? $form.serializeArray() : new FormData(form),
        cache: false,
        processData: false,
        contentType: false, 
        success : function(response, status, xhr) {
            $btn.text('Send').attr('disabled', false);
            $section.find('.method-example-endpoint code.response-content.response-highlighted').jsonViewer(response); 
            $section.find('.method-example-endpoint code.response-content.response-raw').text(typeof response == 'object' ? JSON.stringify(response): String(response));  
            $section.find('.method-example-endpoint code.response-headers').text(xhr.getAllResponseHeaders());
        },
        error : function(xhr) {
            $btn.text('Send').attr('disabled', false);
            var content = xhr.responseText;
            if (xhr.statusText && !content) {
                $.notify({
                    message: xhr.statusText
                },{
                    type: 'danger'
                });
            }
            if (IsJsonString(content)) {
                $section.find('.method-example-endpoint code.response-content').jsonViewer(content); 
                return;
            }
            var $frame = $('<iframe class="supa" style="width:100%; height:350px;">');
            $section.find('.method-example-endpoint code.response-content.response-highlighted').html($frame);
            $section.find('.method-example-endpoint code.response-content.response-raw').text(typeof content == 'object' ? JSON.stringify(content): String(content)); 
            setTimeout(function() {
                var doc = $frame[0].contentWindow.document;
                var $body = $('body', doc);
                $body.html(content);
            }, 1);
            
            $section.find('.method-example-endpoint code.response-headers').text(xhr.getAllResponseHeaders());
        }
    });
    
    return false;
}

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function changeApiUrl(input)
{
    var $input = $(input);
    var $form = $input.closest('form');
    var $urlInput = $form.find('.action-url');
    var original = $urlInput.data('original');
    
    $form.find('input').not('.action-url').each(function(key, input) {
        if (!input.value) {
            return;
        }

        var regexp = new RegExp('{'+ input.name +'}', "g");
        original = original.replace(regexp, input.value);
    });
    
    $urlInput.val(original);
}

function changeTab(ident)
{
    $('.method-tab').hide();
    $('.method-tab.'+ ident).show();
}

function changeSourceView(ctx)
{
    var $a = $(ctx);
    var $parent = $a.parent();
    $parent.find('pre.language-none').hide();
    if ($a.hasClass('show-source-block')) {
        $a.removeClass('show-source-block').addClass('show-highlighted-block');
        $parent.find('.response-highlighted').parent().show();
        $a.find('.fa-eye-slash').removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
        $a.removeClass('show-highlighted-block').addClass('show-source-block');
        $parent.find('.response-raw').parent().show();
        $a.find('.fa-eye').removeClass('fa-eye').addClass('fa-eye-slash');
    }
}

function addNewHeaderInput(ctx)
{
    $(ctx).closest('.form-group').before($('#header-row-template').html());
    setHeadersCheckboxEvents();
    autocompleteHeaders();
    recalculateGlobalHeaders();
}

function removeNewHeaderInput(ctx)
{
    $(ctx).closest('.form-group').remove();
    recalculateGlobalHeaders();
}

function recalculateGlobalHeaders()
{
    var count = 0;
    $('.global-headers-form .form-group').not('.except').each(function(key, element) {
        var $el = $(element);
        if ($el.find('.req-header-active').is(':checked')) {
            count++;
        }
    });
    
    $('.global-headers-count').text(count);
}

function showGlobalHeaders(ctx)
{
    var $li = $(ctx).parent();
    if ($li.hasClass('dx-nav-active')) {
        $li.removeClass('dx-nav-active');
        $('.global-headers-section').slideUp();
    } else {
        $li.addClass('dx-nav-active');
        $(window).trigger('resize')
        $('.global-headers-section').slideDown();
    }
}
</script>
