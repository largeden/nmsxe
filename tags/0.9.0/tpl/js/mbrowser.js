var $nms_mbrowser = {
    // 출력되는 OID의 갯수 저장 변수
    mbrowser_count : 0,
    // 요청하는 번호(요청이 끝나지 않은 상태에서 재요청의 중복을 막기 위함)
    proc_count : 0,
    // php sprintf() Convert
    sprintf : function(str) {
        var arg = arguments;
        for(var i=0; i<arg.length; i++) if(arg[i]==undefined) arg[i] = "";
        i = 1;
        return str.replace(/(([^\\]?)%(s|d))/g, function(s, a1, a2){ return a2 + arg[i++]; });
    },
    // snmpwalk 캐쉬 재생성
    doRemakeCache : function(val) {
        var params = new Array();
        if(!val) params['module_srl'] = val;
        exec_xml('nms','procNmsAdminRemakeCache', params, $nms_mbrowser.completeRemakeCache);
    },
    // snmpwalk 캐쉬 확인
    completeRemakeCache : function(ret_obj) {
        var message = ret_obj['message'];
        if(message) alert(message);
    },
    // 선택된 정보를 입력 폼으로 출력
    doMbrowserInsert : function(val) {
        if(!val) return;

        var fo_obj = val.split(',');
        var duplicate = false;
        var params = new Array();
        var is_mmid = "";

        // 선택된 정보를 등록 폼으로 출력(header)
        if(!$('#mb_insert_table').is('#mb_insert_table')) {
            $('#mb_insert').append('<h4 class="xeAdmin">MBrowser '+lang_cmd_submit+'</h4>');
            $('#mb_insert').append('<table id="mb_insert_table" cellspacing="0" class="mbTable"></table>');
            $('#mb_insert table').append('<thead><tr>'
                +'<th scope="col"><div>type</div></th>'
                +'<th scope="col"><div>'+lang_nms_mmid+'</div></th>'
                +'<th scope="col"><div>'+lang_nms_mib_title+'</div></th>'
                +'<th scope="col"><div>'+lang_nms_mib+'</div></th>'
                +'<th scope="col"><div>'+lang_nms_max+'</div></th>'
                +'<th scope="col"><div>'+lang_nms_extra_collect_mode_title+'</div></th>'
                +'<th scope="col"><div>'+lang_nms_group_name+'</div></th>'
                +'<th scope="col"><div>'+lang_nms_extra_lenged+'</div></th>'
                +'<th scope="col"><div>'+lang_cmd_delete+'</div></th>'
                +'</tr></thead>');
            $('#mb_insert table').append('<tbody></tbody>');
            $('#mb_insert table').append('<tfoot><tr><th colspan="9" class="button"><a href="#$nms_mbrowser.complete_mib_insert" class="button green strong" title="'+lang_cmd_submit+'"><span class="mib_insert">'+lang_cmd_submit+'</span></a></th></tr></tfoot>');

            // 입력된 값을 최종 선택 테이블로 추가하기 전에 입력된 정보를 필터
            $('span.mib_insert').click(function(event) {
                is_mmid = "";
                // 등록된 대상이 없으면 수행 중단
                if($('#mb_insert_table tbody tr').length == 0) return;
                $('#mb_insert_table tbody tr').each(function(i) {

                    params[i] = new Array();
                    params[i]['type'] = $(this).children().eq(0).text();
                    params[i]['mmid'] = $(this).children().eq(1).children().val();
                    params[i]['title'] = $(this).children().eq(2).children().val();
                    params[i]['mib'] = $(this).children().eq(3).text();
                    params[i]['max'] = $(this).children().eq(4).children().val();
                    params[i]['collect'] = $(this).children().eq(5).children().val();
                    params[i]['group_name'] = $(this).children().eq(6).children().val();
                    params[i]['legend'] = $(this).children().eq(7).children().val();

                    if(!params[i]['mmid']) { // MIB 이름 미선택
                        is_filter = $nms_mbrowser.sprintf(lang_filter_isnull, lang_nms_mmid); alert(is_filter);
                        $(this).children().eq(1).children().focus();
                        return false;
                    } else if(!/^[a-z][a-z0-9_]*$/i.test(params[i]['mmid'])) { // MIB 이름이 첫 글자가 문자이고 문자,숫자,특수문자(_)만 선택
                        is_filter = $nms_mbrowser.sprintf(lang_filter_invalid_alpha_number, lang_nms_mmid); alert(is_filter);
                        $(this).children().eq(1).children().focus();
                        return false;
                    } else if(!params[i]['title']) { // MIB 제목 미선택
                        is_filter = $nms_mbrowser.sprintf(lang_filter_isnull, lang_nms_mib_title); alert(is_filter);
                        $(this).children().eq(2).children().focus();
                        return false;
                    } else if(!params[i]['max']) { // MAX 값 미선택
                        is_filter = $nms_mbrowser.sprintf(lang_filter_isnull, lang_nms_max); alert(is_filter);
                        $(this).children().eq(4).children().focus();
                        return false;
                    } else if(!/^[0-9]*$/.test(params[i]['max'])) { // MAX 값이 숫자만 선택
                        is_filter = $nms_mbrowser.sprintf(lang_filter_invalid_number, lang_nms_max); alert(is_filter);
                        $(this).children().eq(4).children().focus();
                        return false;
                    } else if(!params[i]['group_name']) { // Group 이름 미선택
                        is_filter = $nms_mbrowser.sprintf(lang_filter_isnull, lang_nms_group); alert(is_filter);
                        $(this).children().eq(6).children().focus();
                        return false;
                    } else if(is_mmid.indexOf(params[i]['mmid']+',') != -1) { // 입력된 값들 중 MIB 이름이 중복되었는지 확인
                        is_filter = lang_msg_mmid_name_exists; alert(is_filter);
                        $(this).children().eq(1).children().focus();
                        return false;
                    } else is_filter = false;

                    is_mmid += params[i]['mmid']+',';

                    $('#mibTable tbody tr').each(function(e) { // 최종 선택 테이블에 있는 MIB 이름과 중복되는지 확인
                        if(params[i]['mmid'] == $(this).children().eq(1).text()) {
                            is_filter = lang_msg_mmid_name_exists; alert(is_filter);
                            return false;
                        } else is_filter - false;
                    });

                    if(is_filter) return false;
                });

                if(is_filter) return false;

                // 최종 선택 테이블로 입력된 값을 추가
                $nms_mbrowser.complete_mib_insert(params);
                $('#mb_insert_table').prev().remove();
                $('#mb_insert_table').remove();

                // onclick 기능 중복 수행 시 전에 만들어졌던 기능을 정지
                event.stopImmediatePropagation();
            });
        }

        // 선택된 정보를 등록 폼으로 출력(body)
        for( i=0; i<fo_obj.length-1; i++ ) {
           $('#mb_insert table tbody tr').each(function() {
               if($(this).children().eq(3).text() == fo_obj[i].replace(/^./g, '')) duplicate = true;
           });

           if(duplicate == true) {
               duplicate = false;
               continue;
           }

           // doSnmpGetTable로 만들어진 정보일 경우 MIB이름과 제목을 자동으로 추가
           var value = "";
           if($('#mib_table').is('#mib_table')) {
               value = $('#oid_'+fo_obj[i].replace(/\./g, '_')).attr('value');
           }

           $('#mb_insert table tbody').append('<tr class="tt">'
           +'<td>MBrowser</td>'
           +'<td><input type="text" value="'+value+'" alt="text" class="inputTypeText" /></td>'
           +'<td><input type="text" value="'+value+'" alt="text" class="inputTypeText" /></td>'
           +'<td>'+fo_obj[i].replace(/^./g, '')+'</td>'
           +'<td><input type="text" value="" alt="text" class="inputTypeText" /></td>'
           +'<td><select><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></td>'
           +'<td><input type="text" value="" alt="text" class="inputTypeText" /></td>'
           +'<td><input type="text" value="" alt="text" class="inputTypeText w100" /></td>'
           +'<td class="tCenter"><a href="#mb_delete" title="'+lang_cmd_delete+'" class="buttonSet buttonDelete mb_delete"><span>'+lang_cmd_delete+'</span></a></td>'
           +'</tr>');
        }

        // 선택된 입력 폼을 삭제
        $('a.mb_delete').click(function(event) {
            if(confirm(lang_confirm_delete)) $(this).parent().parent().remove();
            // onclick 기능 중복 수행 시 전에 만들어졌던 기능을 정지
            event.stopImmediatePropagation();
        });
    },
    // 입력 MIB 정보를 최종 선택 테이블에 추가
    complete_mib_insert : function(fo_obj) {
        $.each(fo_obj, function(key, val) {
            $('#mibTable tbody').append('<tr id="'+val.mmid+'">'
            +'<td>'+val.type+'</td>'
            +'<td>'+val.mmid+'</td>'
            +'<td>'+val.title+'</td>'
            +'<td>'+val.mib+'</td>'
            +'<td></td>'
            +'<td>'+val.max+'</td>'
            +'<td></td>'
            +'<td>'+val.collect+'</td>'
            +'<td>'+val.group_name+'</td>'
            +'<td>'+val.legend+'</td>'
            +'</tr>');

            $('#'+val.mmid+' td').addClass('animate_yellow');
            $('#'+val.mmid+' td').removeClass('animate_yellow', 3000);
        });

        $('#mibTable').trigger('update');
    },
    // MIB 정보 요청
    doSnmpGet : function(fo_obj) {
        var params = new Array();
        var response_tags = new Array('value','oid','start','end','total','no','module_srl','message');
        fo_obj = fo_obj.split('_');
        params['oid'] = fo_obj[1];
        params['start'] = 1;
        params['end'] = 1000;
        $nms_mbrowser.proc_count += 1;
        params['no'] = $nms_mbrowser.proc_count;
        if($('#module_srl').is('#module_srl')) params['module_srl'] = $('#module_srl').val();

        $('div.oid_count img').removeClass('hidden');

        show_waiting_message=false;
        exec_xml('nms','getNmsSnmpGet', params, $nms_mbrowser.completeSnmpGet, response_tags);
        show_waiting_message=true;
    },
    // doSnmpGet 결과를 출력(header)
    completeSnmpGet : function(ret_obj) {
        var message = ret_obj['message'];
        var value = ret_obj['value'];
        var oid = ret_obj['oid'];
        var start = ret_obj['start'];
        var end = ret_obj['end'];
        var total = ret_obj['total'];
        var no = ret_obj['no'];

        if(value) {
            $('#value_select_result').text("");
            $('div.mbrowser_value table').remove();

            if(value['item'].oid == 'NULL') {
                $('div.oid_count img').addClass('hidden');
                $('div.oid_count p').text(0); return;
            }

            $('div.mbrowser_value').append('<table cellspacing="0" class="mbTable"></table>');
            $('div.mbrowser_value table').append('<thead><tr><th scope="col"><div>OID(MIB)</div></th><th scope="col"><div>VALUE</div></th></tr></thead>');
            $('div.mbrowser_value table').append('<tbody id="mbTableTbody"></tbody>');

            $nms_mbrowser.snmpGet(ret_obj);
        }
    },
    // doSnmpGet 결과를 출력(body)
    snmpGet : function(ret_obj) {
        var message = ret_obj['message'];
        var value = ret_obj['value'];
        var oid = ret_obj['oid'];
        var start = ret_obj['start'];
        var end = ret_obj['end'];
        var total = ret_obj['total'];
        var no = ret_obj['no'];
        var module_srl = ret_obj['module_srl'];
        var params = new Array();
        var response_tags = new Array('value','oid','start','end','total','no','module_srl','message');
        params['oid'] = oid;
        params['start'] = Number(start)+Number(end);
        params['end'] = 200;
        params['no'] = no;
        if(module_srl) params['module_srl'] = module_srl;

        if(no != $nms_mbrowser.proc_count) {
            $nms_mbrowser.mbrowser_count = '-';
            return false;
        }

        if(value['item'].oid == 'NULL') {
            $('div.oid_count img').addClass('hidden');
            $('div.oid_count p').text($nms_mbrowser.mbrowser_count+" ("+$nms_mbrowser.mbrowser_count+")");
            $nms_mbrowser.mbrowser_count = 0;

            // 출력된 내용에 selectable 적용
            if($('#mbTableTbody').is('#mbTableTbody')) {
                $('#mbTableTbody').selectable({
                    filter: 'tr',
                    stop: function(){
                        var result = $('#value_select_result').empty();
                        $('tr.ui-selected', this).each(function(){
                            var index = $(this).attr('title');
                            if(index) result.append(index+',');
                        });
                    }
                });
            }

            // 출력된 내용에 OID를 클릭하면 MBrowser Tree에 클릭한 값에 해당하는 위치로 이동
            $('#mbTableTbody tr').mouseup( function() {
                var $this  = this;
                // 다중 클릭 중 해제에 대해서 바로 확인이 안되어 딜레이를 100 사용
                setTimeout(function(){
                    if($($this).hasClass('ui-selected')) {
                        $('div.mb_direct input.inputTypeText').val($($this).attr('title'));

                        var oid = $($this).children().eq(0).text();
                        var mib = '';
                        oid = oid.split('.');
                        $.each(oid, function(key, val) {
                            if(!/^[0-9]*$/.test(val)) mib += '_'+val;
                        });

                        $('div.mbrowser_tree li span').removeClass('clicked');

                        if(!$('#mib'+mib).hasClass('clicked')) {
                            $('#mib'+mib).addClass('clicked');
                            var $size = $('#mib'+mib).offset().top - $('div.mbrowser_tree').offset().top + $('div.mbrowser_tree').scrollTop() - 50;

                            $('div.mbrowser_tree').animate({scrollTop:$size}, 'slow');
                        }
                    }
                },100);
            });

            return;
        }

        // body 부분 출력
        if($.isArray(value['item'])) {
            $nms_mbrowser.mbrowser_count += value['item'].length;

            $.each(value['item'], function(key, val) {
                $('div.mbrowser_value table tbody').append('<tr title="'+val.numeric+'"><td><div>'+val.oid+'</div></td><td><div>'+val.val+'</div></td></tr>');
            });

            $('div.oid_count p').text($nms_mbrowser.mbrowser_count+" ("+total+")");
        } else {
            if($nms_mbrowser.mbrowser_count > 1) $nms_mbrowser.mbrowser_count += 1;
            else $nms_mbrowser.mbrowser_count = 1;

            $('div.mbrowser_value table tbody').append('<tr title="'+value['item'].numeric+'"><td><div>'+value['item'].oid+'</div></td><td><div>'+value['item'].val+'</div></td></tr>');
            $('div.oid_count p').text($nms_mbrowser.mbrowser_count+" ("+total+")");
        }

        // 출력 내용이 많을 경우 자바스크립트 정보를 브라우저에서 다 처리할 수 없기 때문에 분할해서 출력(결과가 없을때까지 0.5초 딜레이 간격으로 출력)
        setTimeout(function(){show_waiting_message=false;exec_xml('nms','getNmsSnmpGet', params, $nms_mbrowser.snmpGet, response_tags);show_waiting_message=true;},500);
    },
    // 요청 MIB값 노드의 타입이 table, row일 경우 테이블 형식으로 정보 요청
    doSnmpGetTable : function(fo_obj) {
        var params = new Array();
        var response_tags = new Array('table','oid','start','end','total','no','message');
        fo_obj = fo_obj.split('_');
        params['oid'] = fo_obj[1];
        params['start'] = 1;
        params['end'] = 1000;
        $nms_mbrowser.proc_count += 1;
        params['no'] = $nms_mbrowser.proc_count;
        if($('#module_srl').is('#module_srl')) params['module_srl'] = $('#module_srl').val();

        $('div.oid_count img').removeClass('hidden');

        show_waiting_message=false;
        exec_xml('nms','getNmsSnmpGetTable', params, $nms_mbrowser.completeSnmpGetTable, response_tags);
        show_waiting_message=true;
    },
    // doSnmpGetTable 결과를 테이블 형식으로 출력
    completeSnmpGetTable : function(ret_obj) {
        var message = ret_obj['message'];
        var table = ret_obj['table'];
        var oid = ret_obj['oid'];
        var start = ret_obj['start'];
        var end = ret_obj['end'];
        var total = ret_obj['total'];
        var no = ret_obj['no'];
        var module_srl = ret_obj['module_srl'];

        if(table != 'NULL') {
            // 테이블 형식으로 출력(header)
            $('#value_select_result').text("");
            $('div.mbrowser_value table').remove();

            $('div.mbrowser_value').append('<table id="mib_table" cellspacing="0" class="mbTable"></table>');
            $('div.mbrowser_value table').append('<thead><tr></tr></thead>');
            $('div.mbrowser_value table').append('<tbody id="mbTableTbody"></tbody>');

            $.each(table.table_header, function(key, val) {
                $('div.mbrowser_value table thead tr').append('<th scope="col"><div>'+val+'</div></th>');
            });

            var i = 0;
            // 테이블 형식으로 출력(body)
            $.each(table.table_no['item'], function(key, val) {
                $('div.mbrowser_value table tbody').append('<tr></tr>');
                $.each(val.mib, function(key, val) {
                    if(!val.val) val.val = "&nbsp;";
                    if(!val.numeric) val.numeric = "null";
                    $('div.mbrowser_value table tbody tr').eq(i).append('<td id="oid_'+val.numeric.replace(/\./g, '_')+'" title="'+val.numeric+'" oid="'+val.oid+'" value="'+key+'_'+(i+1)+'">'+val.val+'</td>');
                });

                i++;
            });

            // 출력된 테이블에 selectable 적용
            if($('#mbTableTbody').is('#mbTableTbody')) {
                $('#mbTableTbody').selectable({
                    filter: 'td',
                    stop: function(){
                        var result = $('#value_select_result').empty();
                        $('td.ui-selected', this).each(function(){
                            var index = $(this).attr('title');
                            if(index) result.append(index+',');
                        });
                    }
                });
            }

            // 출력된 테이블의 OID를 클릭하면 MBrowser Tree에 클릭한 값에 해당하는 위치로 이동
            $('#mbTableTbody tr td').mouseup( function() {
                var $this  = this;
                // 다중 클릭 중 해제에 대해서 바로 확인이 안되어 딜레이를 100 사용
                setTimeout(function(){
                    if($($this).hasClass('ui-selected')) {
                        $('div.mb_direct input.inputTypeText').val($($this).attr('title'));

                        var oid = $($this).attr('oid');
                        var mib = '';
                        oid = oid.split('.');
                        $.each(oid, function(key, val) {
                            if(!/^[0-9]*$/.test(val)) mib += '_'+val;
                        });

                        $('div.mbrowser_tree li span').removeClass('clicked');

                        if(!$('#mib'+mib).hasClass('clicked')) {
                            $('#mib'+mib).addClass('clicked');
                            var $size = $('#mib'+mib).offset().top - $('div.mbrowser_tree').offset().top + $('div.mbrowser_tree').scrollTop() - 50;

                            $('div.mbrowser_tree').animate({scrollTop:$size}, 'slow');
                        }
                    }
                },100);
            });

          $('div.oid_count p').text(total+" ("+total+")");
        } else $('div.oid_count p').text(0);

        $('div.oid_count img').addClass('hidden');
    },
    // OID 정보 요청
    doOidInfo : function(fo_obj) {
        var params = new Array();
        var response_tags = new Array('oid_info','message');
        fo_obj = fo_obj.split('_');
        params['oid'] = fo_obj[1];

        show_waiting_message=false;
        exec_xml('nms','getNmsOidInfo', params, $nms_mbrowser.completeOidInfo, response_tags);
        show_waiting_message=true;
    },
    // OID 정보 출력
    completeOidInfo : function(ret_obj) {
        var message = ret_obj['message'];
        var oid_info = ret_obj['oid_info'];

        if(oid_info.type == 'node') {
            $('#mb_stat td:eq(0)').html("");
            $('#mb_stat td select').attr('disabled', 'disabled');
            $('#mb_stat td select').addClass('none_select');
            $('#mb_stat td select option').remove();
            $('#mb_stat td:eq(2)').html("");
            $('#mb_stat td:eq(3)').html("");
            if(!oid_info.description) oid_info.description = "";
            $('#mb_stat td:eq(4)').html(oid_info.description);
        } else {
            if(oid_info.syntax) {
                $('#mb_stat td:eq(0)').html(oid_info.syntax.type.basetype);

                if(oid_info.syntax.type.namednumber) {
                    $.each(oid_info.syntax.type.namednumber['item'], function(key, val) {
                        $('#mb_stat td select').removeAttr('disabled');
                        $('#mb_stat td select').removeClass('none_select');
                        $('#mb_stat td select').append('<option value="'+val.number+'">'+val.name+' ('+val.number+')</option>');
                    });
                } else {
                    $('#mb_stat td select').attr('disabled', 'disabled');
                    $('#mb_stat td select').addClass('none_select');
                    $('#mb_stat td select option').remove();
                }
            } else {
                $('#mb_stat td select').attr('disabled', 'disabled');
                $('#mb_stat td select').addClass('none_select');
                $('#mb_stat td select option').remove();
            }

            if(!oid_info.access) oid_info.access = "";
            $('#mb_stat td:eq(2)').html(oid_info.access);

            if(!oid_info.status) oid_info.status = "";
            $('#mb_stat td:eq(3)').html(oid_info.status);

            if(!oid_info.description) oid_info.description = "";
            $('#mb_stat td:eq(4)').html(oid_info.description);
        }
    },
    // 스크립트 로드
    nms_ready : function() {
        $('html').ready(function(){
            // MBrowser Tree
            if($('div.mbrowser_tree').is('.mbrowser_tree')) {
                $('div.mbrowser_tree').tree({
                    callback : {
                        onchange : function (NODE) {
                            target = $(NODE).children('a:eq(0)').attr('target');
                            if(target == '_blank') window.open($(NODE).children('a:eq(0)').attr('href'), '_blank');
                            else document.location.href = $(NODE).children('a:eq(0)').attr('href');
                        }
                    }
                });

                $('div.mbrowser_tree li span').mouseover(function() {
                    $(this).addClass('over');
                });

                $('div.mbrowser_tree li span').mouseout(function() {
                    $(this).removeClass('over');
                });
                // MBrowser Tree 항목 선택 시 선택 MIB 정보와 MIB값을 요청
                $('div.mbrowser_tree li span').click(function() {
                    $('div.mbrowser_tree li span').removeClass('clicked');
                    $(this).addClass('clicked');
                    $nms_mbrowser.doOidInfo($(this).parent().attr('id'));
                    $nms_mbrowser.doSnmpGet($(this).parent().attr('id'));
                    $('div.mb_direct input.inputTypeText').val($(this).parent().attr('title'));
                });
                // MBrowser Tree 펼치기, 덮기
                $('input.branch').toggle(function() {
                    $.tree.focused().open_branch('div.mbrowser_tree ul');
                    $('input.branch').val(lang_cmd_close_all);
                }, function() {
                    $.tree.focused().close_branch('div.mbrowser_tree ul');
                    $('input.branch').val(lang_cmd_open_all);
                });
            }

            // MBrowser Tree height resize
            $('div.mbrowser_tree_resize').resizable({
                minHeight: 225,
                handles: 's',
                start: function(event, ui) { $('div.mbrowser_tree_resize div.mbrowser_tree').css('display','none'); },
                stop: function(event, ui) { $('div.mbrowser_tree_resize div.mbrowser_tree').css({display:'block',height:($('div.mbrowser_tree_resize').height()-30)+'px'}); }
            });

            // MBrowser Value height resize
            $('div.mbrowser_value_resize').resizable({
                minHeight: 225,
                handles: 's',
                start: function(event, ui) { $('div.mbrowser_value_resize div.mbrowser_value').css('display','none'); },
                stop: function(event, ui) { $('div.mbrowser_value_resize div.mbrowser_value').css({display:'block',height:($('div.mbrowser_value_resize').height()-20)+'px'}); }
            });

            // MBrowser로 선택된 MIB 정보를 최종 선택 테이블로 추가
            $('span.mb_insert').click(function() {
                if($('#mibTable').is('#mibTable')) {
                    $nms_mbrowser.doMbrowserInsert($('#value_select_result').text());
                } else {
                    if(!$('#value_select_result').text()) return;

                    $('input.nms_mib').val($('div.mb_direct input.inputTypeText').val());
                    $('#mbrowser').css({display:'none'});
                    $('input.nms_mib').addClass('animate_yellow');
                    $('input.nms_mib').removeClass('animate_yellow', 2000);
                    $('input.nms_mib').focus();
                }
            });

            // Wizard 최종 선택 테이블이 있을 경우 MBrowser를 표시
            if($('#mibTable').is('#mibTable')) $('#mbrowser').css({display:'block'});

            // MBrowser 주소창에 표시된 값을 실행(Enter키 선택 시 실행)
            $('div.mb_direct input').keypress(function(event) {
                if(event.keyCode == '13') $nms_mbrowser.doSnmpGet('oid_'+$(this).val());
            });
            // MBrowser 주소창에 표시된 값을 실행
            $('div.mb_direct span input').click(function() {
                $nms_mbrowser.doSnmpGet('oid_'+$(this).parent().prev().val());
            });
            // MBrowser 주소창에 표시된 값을 실행
            $('span.recache').click(function() {
                $nms_mbrowser.doRemakeCache($('form input[name=module_srl]').val());
            });

            // 선택된 노드가 table, row 일경우 테이블 형식으로 정보 요청
            $('a.buttonTable').click(function() {
                $nms_mbrowser.doOidInfo($(this).parent().attr('id'));
                $nms_mbrowser.doSnmpGetTable($(this).parent().attr('id'));
            });

        });
    }
};

//$.fn.extend(nms_mbrowser);
$nms_mbrowser.nms_ready();