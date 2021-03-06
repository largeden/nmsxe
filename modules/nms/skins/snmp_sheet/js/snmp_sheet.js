var $ = jQuery.noConflict();
var snmp_sheet = {
    interval : false,
    preview : false,
    colorset : 'white',
    group_name : new Array(),
    threshold : 0,
    timestamp : false,
    z_index : 1,
    unit : 'Bps',
    element : new Array(),
    // 스킨 설정 정보를 설정
    start : function() {
        if(typeof(snmp_sheet_interval) != 'undefined') snmp_sheet.interval = snmp_sheet_interval;
        if(typeof(snmp_sheet_preview) != 'undefined') snmp_sheet.preview = snmp_sheet_preview;
        if(typeof(snmp_sheet_colorset) != 'undefined') snmp_sheet.colorset = snmp_sheet_colorset;
        if(typeof(snmp_sheet_unit) != 'undefined') snmp_sheet.unit = snmp_sheet_unit;
    },
    // normal 그래프 미리보기
    open : function(element, group_name) {
        if(snmp_sheet.group_name[group_name]) return;
        snmp_sheet.group_name[group_name] = group_name;
        snmp_sheet.timestamp = new Date().getTime();

        $('img', element).animate({opacity: 0.2}, 'fast');
        $('div.loading', element).fadeIn('Fast');

        var url = request_uri.setQuery('act','dispNmsGraph').
        setQuery('mid',current_mid).
        setQuery('graph','line').
        setQuery('group_name',group_name).
        setQuery('mode','normal').
        setQuery('skin',snmp_sheet.colorset).
        setQuery('unit',snmp_sheet.unit).
        setQuery('timestamp',snmp_sheet.timestamp);

        $('div.snmp_sheet').append('<div class="graph graph_open" value="0">'+
                         '<div class="graph_option"><span class="close" title="'+group_name+'">&nbsp;</span></div>'+
                         '<div class="loading"></div>'+
                         '<img src='+url+' alt="graph" />'+
                         '</div>');

        $('div.graph_open img').load(function() {
            if($(this).parent().attr('value')==1) return;
            else $(this).parent().attr('value',1);

            var graph_open = $(this).parent();
            $(graph_open).css({top:$(element).offset().top,left:$(element).offset().left,width:0});
            $(graph_open).draggable({
                start : function (event,ui) {
                    $(this).css('z-index',snmp_sheet.z_index);
                    snmp_sheet.z_index = snmp_sheet.z_index+1;
                }
            });

            $(graph_open).mouseenter(function() {
                var element_option = this;
                if(!element_option.N) {
                    element_option.N = true;
                    $('div.graph_option', this).show('fast', function() { element_option.N = false; });
                }
            }).mouseleave(function() {
                $('div.graph_option', this).hide('fast');
            });

            $('div.loading', element).fadeOut('fast');
            $('img', element).animate({opacity: 1}, 'slow');

            var graph_close = $('div.graph_option',graph_open);
            $('span.close',graph_close).click(function (event) {
                var group_name = $(this).attr('title');
                var element = $(this).parent().parent();
                $(element).hide('slow', function() { $(element).remove(); });

                snmp_sheet.group_name[group_name] = false;
                event.stopPropagation();
            });

            $(this).css('display','block');
            $(this).parent().css('zIndex',snmp_sheet.z_index);
            snmp_sheet.z_index = snmp_sheet.z_index+1;

            if(($(element).offset().top-200) < $(window).scrollTop()) var top = $(window).scrollTop()+10;
            else var top = $(element).offset().top-200;

            $(this).parent().animate({
                top:top+'px',
                left:10+'px',
                width:700,
                opacity:1
            }, 500);
        });
    },
    // customer 그래프 출력
    customer : function() {
        var mode = false;
        var regdate = false;
        var reghour = false;
        var group_name = false;
        var colorset = false;
        var data = $('#fo_nms_customer').serializeArray();
        $.each(data, function(i, field){
            switch(field.name) {
                case 'mode': mode = field.value; break;
                case 'group_name': group_name = field.value; break;
                case 'colorset': colorset = field.value; break;
                case 'regdate': regdate = field.value; break;
                case 'reghour': reghour = field.value; break;
                default: break;
            }
        });
        if(regdate&&reghour) {
            regdate = regdate+reghour;
            regdate = regdate.replace(/-/g, '')+59;
        }

        // %가 넘어오면 문제가 생김
        if(snmp_sheet.unit == '%') snmp_sheet.unit = '%25';
        var url = request_uri.setQuery('act','dispNmsGraph').
        setQuery('mid',current_mid).
        setQuery('graph','line').
        setQuery('group_name',group_name).
        setQuery('mode',mode).
        setQuery('skin',colorset).
        setQuery('unit',snmp_sheet.unit).
        setQuery('date',regdate);

        $('div.customer div.graph').remove();
        $('div.customer').append('<div class="graph normal"><em>'+url+'</em></div>');
        $('div.graph').each(function() {
            if(snmp_sheet.abovethetop(this) ||  snmp_sheet.leftofbegin(this)) {
            } else if(!snmp_sheet.belowthefold(this) && !snmp_sheet.rightoffold(this)) snmp_sheet.graph(this);
        });
    },
    // table view search
    search : function() {
        var list_count = false;
        var regdate = false;
        var reghour = false;
        var data = $('#fo_nms_search').serializeArray();
        $.each(data, function(i, field){
            switch(field.name) {
                case 'list_count': list_count = field.value; break;
                case 'regdate': regdate = field.value; break;
                case 'reghour': reghour = field.value; break;
                default: break;
            }
        });

        if(regdate&&reghour) {
            regdate = regdate+reghour;
            regdate = regdate.replace(/-/g, '');
        }

        var url = current_url;
        if(list_count) url = url.setQuery('list_count',list_count);
        if(regdate) url = url.setQuery('regdate',regdate);

        location.href=url;
    },
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
            if(snmp_sheet.colorset == 'white') $(this).css('background','#eaeaea');
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
    // 화면에 노출될때 이미지를 불러오도록 함
    graph : function(element) {
        if(element.loaded) return;
        if($('img', element).is('img')) snmp_sheet.reGraph(element);
        else {
            var src = $('em', element).text();
            $('em', element).remove();
            $(element).append('<div class="loading"></div>');
            $('div.loading', element).fadeIn('Fast');
            if($('a', element).is('a')) $('a', element).append('<img src='+src+' alt="graph" />');
            else $(element).append('<img src='+src+' alt="graph" />');

            $('img', element).load(function() {
                $('div.loading', element).fadeOut('fast');
                $(this).fadeIn('slow');
            });
        }
        element.loaded = true;
    },
    // 자동 새로고침이 진행 되었을때 다시 화면에 노출되는 이미지를 불러옴
    reGraph : function(element) {
        if(element.loaded) return;
        snmp_sheet.timestamp = new Date().getTime();
        var src = $('img', element).attr('src');
        src = src.replace(/\&timestamp\=[0-9]+/g, '');
        $('img', element).animate({opacity: 0.2}, 'fast');
        $('div.loading', element).fadeIn('Fast');
        $('img', element).attr('src', src+"&timestamp="+snmp_sheet.timestamp);

        $('img', element).load(function(event) {
            $('div.loading', element).fadeOut('fast');
            $('img', element).animate({opacity: 1}, 'slow');

            event.stopPropagation();
        });

        element.loaded = true;
    },
    // 자동 새로고침시 현재 화면에 노출되어있는 대상 이미지 갱신
    insertvalGraph : function() {
        $('div.graph').each(function(index) {
            this.loaded = false;
            if(snmp_sheet.abovethetop(this) || snmp_sheet.leftofbegin(this)) {
            } else if(!snmp_sheet.belowthefold(this) && !snmp_sheet.rightoffold(this)) snmp_sheet.reGraph(this);
        });
    },
    // 자동 새로고침 중지
    clearGraph : function() {
        clearInterval(graphTime); //vTimer를 중지시킴
    },
    /* 전방 스크롤과 위치를 계산함 */
    belowthefold : function(element) {
        var fold = $(window).height() + $(window).scrollTop();
        return fold <= $(element).offset().top - snmp_sheet.threshold;
    },
    rightoffold : function(element) {
        var fold = $(window).width() + $(window).scrollLeft();
        return fold <= $(element).offset().left - snmp_sheet.threshold;
    },
    abovethetop : function(element) {
        var fold = $(window).scrollTop();
        return fold >= $(element).offset().top + snmp_sheet.threshold + $(element).height();
    },
    leftofbegin : function(element) {
        var fold = $(window).scrollLeft();
        return fold >= $(element).offset().left + snmp_sheet.threshold + $(element).width();
    },
    // History 페이지 이동
    history_page : function(page) {
        var src = current_url.replace(/\&timestamp\=[a-zA-Z0-9]+/g, '');
        $('ul.history_item').toggle('fast', function() { 
            var group_name =  current_url.match(/group_name\=[^>]/ig);
            var group_name = group_name[0].split('=');
            show_waiting_message = false;
            $.exec_json("nms.dispNmsHistoryList",{mid:current_mid,group_name:group_name[1],h_page:page}, snmp_sheet.history_list);
            show_waiting_message = true;
        });
    },
    // History List 출력
    history_list : function(msg) {
        $('div.history_list ul.history_item').remove();

        var history_item = '';
        $.each(msg.oHistory, function(key, history) {
            history_item += '<li><a href="'+current_url.setQuery('document_srl',history.document_srl)+'" title="'+history.title+'">'
                          + '<img src="'+history.thumbnail+'" alt="history image" class="thumb" /></a>'
                          + '<p>'+history.regdate.substr(0,4)+'-'+history.regdate.substr(4,2)+'-'+history.regdate.substr(6,2)+' '+history.regdate.substr(8,2)+':'+history.regdate.substr(10,2)+':'+history.regdate.substr(12,2)+'</p></li>';
        });
        var h_navi = '<li class="history_navigation">';
        if(msg.page > 1) h_navi += '<div class="arrow_left"><img src="./modules/nms/skins/snmp_sheet/images/'+snmp_sheet.colorset+'/arrow_left.gif" alt="prev" /></div>';
        if(msg.page < msg.total_page) h_navi += '<div class="arrow_right"><img src="./modules/nms/skins/snmp_sheet/images/'+snmp_sheet.colorset+'/arrow_right.gif" alt="prev" /></div>';
        h_navi += '</li>';

        if(!$('div.history_list ul').is('.history_item')) $('div.history_list').append('<ul class="history_item">'+h_navi+history_item+'</ul>');

        $('ul.history_item').toggle('fast');

        if($('div.history_list div').is('.arrow_left')) {
            $('div.history_list div.arrow_left').click(function () {
                snmp_sheet.history_page(Number(msg.page)-1);
            });
        }

        if($('div.history_list div').is('.arrow_right')) {
            $('div.history_list div.arrow_right').click(function () {
                snmp_sheet.history_page(Number(msg.page)+1);
            });
        }
    },
    // History 실행
    history : function() {
        if(!$('div.history_list ul').is('.history_item')) {
            var group_name =  current_url.match(/group_name\=[^>]/ig);
            var group_name = group_name[0].split('=');
            show_waiting_message = false;
            $.exec_json("nms.dispNmsHistoryList",{mid:current_mid,group_name:group_name[1]}, snmp_sheet.history_list);
            show_waiting_message = true;
        } else $('ul.history_item').toggle('fast');
    },
    // 스크립트 로드
    ready : function() {
        $('body').ready(function() {
            // 스킨 설정 정보를 설정
            snmp_sheet.start();

            // 화면에 노출될때 이미지를 불러오도록 함
            $('div.graph').each(function() {
                if(snmp_sheet.abovethetop(this) ||  snmp_sheet.leftofbegin(this)) {
                } else if(!snmp_sheet.belowthefold(this) && !snmp_sheet.rightoffold(this)) snmp_sheet.graph(this);
            });

            $(window).bind('scroll', function(event) {
                $('div.graph').each(function() {
                    if($(this).attr('value')!=1) {
                        if(snmp_sheet.abovethetop(this) || snmp_sheet.leftofbegin(this)) {
                        } else if(!snmp_sheet.belowthefold(this) && !snmp_sheet.rightoffold(this)) snmp_sheet.graph(this);
                    }
                });
            });

            // normal 그래프를 미리보기 설정했을 시 수행
            if(snmp_sheet.preview == 'Y') {
                $('div.graph').mouseenter(function() {
                    var element_option = this;
                    if(!element_option.N) {
                        element_option.N = true;
                        $('div.graph_option', this).show('fast', function() { element_option.N = false; });
                    }
                }).mouseleave(function() {
                    $('div.graph_option', this).hide('fast');
                });

                $('div.graph div.graph_option span.open').click(function () {
                    var element = $(this).parent().parent();
                    element.loaded = true;
                    snmp_sheet.open(element, $(this).attr('title'));
                });
            }

            // 주기적으로 그래프를 갱신
            if(snmp_sheet.interval) graphTime = setInterval(snmp_sheet.insertvalGraph, snmp_sheet.interval*1000);

            // 날자 형식의 입력폼일 경우 캘린더 출력
            if($('input.inputDate').is('.inputDate')) $('input.inputDate').datepicker();

            // select 형식의 표현을 바꿈
            if(!$.browser.msie) if($('select.select').is('.select')) snmp_sheet.select();

            // History 출력
            if($('#history_list').is('#history_list')) {
                $('#history_list').click(function () {
                    snmp_sheet.history();
                });
            }
        });
    }
};

// History 저장 완료시
function complete(ret_obj) {
    var message = ret_obj['message'];
    alert(message);
    location.reload();
}

//$.fn.extend(nms);
snmp_sheet.ready();