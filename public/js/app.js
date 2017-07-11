
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
        var waypoint = new Waypoint({
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
                history.replaceState(null, null, document.location.pathname + '#' + id);
            }
        })
    });
    $("a[href^='#']").on('click', function() {
        var a = this; 
        console.log($(a).attr('href').replace('.', '\\.').replace(':', '\\:'));
        $('html, body').animate({
            scrollTop: $($(a).attr('href').replace(new RegExp(/\./, 'g'), '\\.').replace(new RegExp(/\:/, 'g'), '\\:')).offset().top 
        });
        return false;
    });
});

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

