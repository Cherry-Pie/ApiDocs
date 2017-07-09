
$(document).ready(function(){
    var hash = window.location.hash;
    var a = $('a[href="' + hash + '"]', '#scroll');
    if (a.length) {
        $('a[href="'+ hash +'"]', '#scroll')[0].click();
    }
    $('section.method').each(function() {
        var waypoint = new Waypoint({
            element : this,
            handler : function() {
                var id = this.element.attributes['id'].nodeValue;
                //
                $('a', '#scroll').removeClass('current');
                $('ol', '#scroll').removeClass('open');
                var a = $('a[href="#' + id + '"]', '#scroll');
                a.addClass('current');
    
                var ol = a.closest('ol');
                if (ol.length) {
                    openOl(ol);
                }
                if (a.next().is('ol')) {
                    a.next().addClass('open');
                }
                window.location.hash = id;
            }
        })
    });
});

function openOl(ol)
{
    ol.addClass('open');
    var ol = ol.parent().closest('ol');
    if (ol.length) {
        openOl(ol);
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
    
    $.ajax({
        url : $section.find('.action-url').val(),
        headers: headers,
        type : $form.attr('method'),
        data : $form.serializeArray(),
        success : function(response, status, xhr) {
            $btn.text('Send').attr('disabled', false);
            $section.find('.method-example-endpoint code.response-content').jsonViewer(response); 
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
            $section.find('.method-example-endpoint code.response-content').html($frame);
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

