var $ = jQuery.noConflict();
var $nms = {
    // act 정보 선택 시 처리
    act_var : function(element) {
        if($('#'+element).attr('checked') == true) $('tr[id^='+element+'_]').show();
        else $('tr[id^='+element+'_]').hide();
    },
    // Severity 설정 대상 추가
    doInsertItem : function() {
        var target_obj = xGetElementById('targetItem');
        var display_obj = xGetElementById('displayItem');
        var idx_check = false;

        if(!target_obj || !display_obj) return;

        for( s=0; s<target_obj.length; s++ ) {

            if(target_obj.options[s].selected == false) continue;

            var text = target_obj.options[s].text;
            var value = target_obj.options[s].value;

            for(var i=0;i<display_obj.options.length;i++) {
                if(display_obj.options[i].value == value) {
                    idx_check = true;
                    break;
                } else idx_check = false;
            }

            if(idx_check == true) continue;

            var obj = new Option(text, value, true, true);
            display_obj.options[display_obj.options.length] = obj;
        }

        var list = new Array();
        for(var i=0;i<display_obj.options.length;i++) list[list.length] = display_obj.options[i].value;
        $('#mib_srl').val(list);
    },
    // Severity 설정 대상 삭제
    doDeleteItem : function() {
        var sel_obj = xGetElementById('displayItem');
        var sel_count = sel_obj.length;

        for( s=sel_count-1; s>=0; s-- ) {
            if(sel_obj.options[s].selected == false) continue;
            sel_obj.remove(s);
        }

        var list = new Array();
        for(var i=0;i<sel_obj.options.length;i++) list[list.length] = sel_obj.options[i].value;
        $('#mib_srl').val(list);
    },
    // SMTP 서버 동작 확인
    doCheckSMTPInfo : function() {
        var fo_obj = jQuery('#smtp_form').get(0);
        var params = new Array();
        params['smtp_server'] = fo_obj['smtp_server'].value;
        params['smtp_secure'] = fo_obj['smtp_secure'].value;
        params['smtp_port'] = fo_obj['smtp_port'].value;
        params['smtp_user'] = fo_obj['smtp_user'].value;
        params['smtp_pass'] = fo_obj['smtp_pass'].value;

        exec_xml('nms','procNmsCheckSmtp', params, completeCheckSMTPInfo);
    },
    // Severity 상태 변경
    doSeverityAware : function(aware) {
        var fo_obj = aware.split('/');
        var params = new Array();
        params['severity_srl'] = fo_obj['0'];
        params['aware'] = fo_obj['1'];

        exec_xml('nms','procNmsChangeAware', params, complete);
    },
    // 설치마법사 설정 파일 정보 출력
    doWizardInsertMib : function(ret_obj) {
        var step = ret_obj['step'];
        var fo_obj = $('#select_result').text();
        var params = new Array();

        params['mibs'] = true;

        params['step'] = $('form input:eq(1)').val();
        fo_obj = fo_obj.split(',');

        for( i=0; i<fo_obj.length-1; i++ ) {
            params[fo_obj[i]] = '';
            $('#'+fo_obj[i]+' td').each(function (e) {
                params[fo_obj[i]] += $(this).html()+'|@|';
            });

            params[fo_obj[i]] += $('#'+fo_obj[i]).attr('title');
        }

        exec_xml('nms','procNmsAdminSettingWizard', params, completeWizard);
    },
    // 설치마법사 설정 파일 정보 출력
    doWizardDesc : function(fo_obj) {
        var params = new Array();
        params['wizard_file'] = fo_obj;
        var response_tags = new Array('wizard','message');
        show_waiting_message=false;
        exec_xml('nms','getNmsWizardDesc', params, completeWizardDesc, response_tags);
        show_waiting_message=true;
    },
    // 최신 버전 상태 알림
    new_version : function() {
        alert(msg_nms_new_version);

        var url = "http://www.xpressengine.com/?mid=download&package_srl=18335043";
        window.open(url,'_blank');
    },
    // 스크립트 로드
    nms_ready : function() {
        $('html').ready(function(){
            // 최신 버전 상태 알림
            if(typeof(msg_nms_new_version) != 'undefined') $nms.new_version();

            // Snmptrap Tree
            if($('div.snmptraps').attr('class')) {
                $('div.snmptraps').tree({
                    callback : {
                        onchange : function (NODE) {
                            target = $(NODE).children('a:eq(0)').attr('target');
                            if(target == '_blank') window.open($(NODE).children('a:eq(0)').attr('href'), '_blank');
                            else document.location.href = $(NODE).children('a:eq(0)').attr('href');
                        }
                    }
                });
                // Snmptrap Tree 펼치기, 덮기
                $('input.branch').toggle(function() {
                  $.tree.focused().open_branch('div.snmptraps ul');
                  $('input.branch').val(lang_cmd_close_all);
                }, function() {
                  $.tree.focused().close_branch('div.snmptraps ul');
                  $('input.branch').val(lang_cmd_open_all);
                });
            }

            // Wizard Table 정렬
            if($('#mibTable').is('#mibTable')) $('#mibTable').tablesorter();
            // Wizard Table 선택
            if($('#mibTableTbody').is('#mibTableTbody')) {
                $('#mibTableTbody').selectable({
                    filter: 'tr',
                    stop: function(){
                        var result = $('#select_result').empty();
                        $('tr.ui-selected', this).each(function(){
                            var index = $(this).attr('id');
                            if(index) {
                                result.append(index+',');
                                $('#nms_wizard_mib_list').val(result.html());
                            }
                        });
                    }
                });
            }

            // act 최초 로딩시 닫기
            $('tr[id^=act_]').hide();

            // act 보기
            $('.act input.checkbox').each(function (i) {
                if(this.checked) $nms.act_var($(this).attr('id'));
            });
            // act 펼치기 덮기
            $('.act input.checkbox').click(function() {
                $nms.act_var($(this).attr('id'));
            });

            // Severity 상태 변경
            $('select.severity_aware').change(function() {
                $nms.doSeverityAware($(this).val());
            });

            // Wizard Step2 페이지 로딩시 현재 선택된 Wizard File 정보 출력
            if($('div.wizardItem select').val()) $nms.doWizardDesc($('.wizardItem select').val());
            // 선택된 Wizard File 정보 출력
            $('div.wizardItem select').click(function() {
                $nms.doWizardDesc($(this).val());
            });

            // MBrowser 펼치기(HOST->MIB 추가메뉴에서 설치 마법사에서 불러올 경우)
            $('input.nms_mib').next().click(function() {
                $('#mbrowser').css({display:'block'});
            });
        });
    }
};

//$.fn.extend(nms);
$nms.nms_ready();