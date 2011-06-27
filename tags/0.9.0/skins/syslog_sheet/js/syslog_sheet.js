var $ = jQuery.noConflict();
var syslog_sheet = {
    colorset : 'white',
    // select 형식의 표현을 바꿈
    select : function() {
        var display = 0;
        var element = false;
        var value = false;
        $('select.select').after('<div class="to_select"><span class="to_select_value">select</span><ul></ul></div>');
        $('select.select').each(function(index) {
            value = $('option:selected',this).text();
            element = $(this).next();
            $('span.to_select_value',element).text(value);

            $('option',this).each(function(index) {
                $('ul', element).append('<li title="'+$(this).val()+'">'+$(this).text()+'</li>');
            });
        });

        $('div.to_select span').click(function() {
            element = $(this).parent();
            if(($(this).offset().top+$('ul',element).height()+20) > ($(window).height()+$(window).scrollTop())) $('ul',element).css('top','-'+($('ul',element).height()-14)+'px');
            else $('ul',element).css('top','0px');
            $('ul',element).toggle('fast');
        });

        $('body').click(function(event) {
            if(event.target.className != 'to_select_value') $('div.to_select ul').hide('fast');
        });

        $('div.to_select ul li').mouseenter(function() {
            if(syslog_sheet.colorset == 'white') $(this).css('background','#eaeaea');
            else $(this).css('background','#151515');
        }).mouseleave(function() {
            $(this).css('background','transparent');
        });

        $('div.to_select ul li').click(function(){
            element = $(this).parent().parent();
            $('span',element).text($(this).text());
            element = $(element).prev();
            $('option[value="'+$(this).attr('title')+'"]',element).attr('selected','selected');
        });
    },
    // 스크립트 로드
    ready : function() {
        $('body').ready(function() {
            // select 형식의 표현을 바꿈
            if(!$.browser.msie) if($('select.select').is('.select')) syslog_sheet.select();

            // 표의 홀수 짝수 스타일 변경
            $('table.nmsTable tbody tr').filter(':nth-child(even)').addClass('bg');
        });
    }
};

// 삭제 완료시
function complete(ret_obj) {
    var message = ret_obj['message'];
    alert(message);
    location.reload();
}

//$.fn.extend(nms);
syslog_sheet.ready();