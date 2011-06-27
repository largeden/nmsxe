var $ = jQuery.noConflict();
var $nms_member = {
    // Member 최종 추가
    doMemberInsert : function(fo_obj) {
        var value = fo_obj.replace(/\_\_/g, ':');

        var target = $('#nms_member input[name=target]').val();
        $('input[name='+target+']', window.opener.document).val(value);
        window.close();
    },
    // Member를 임시 추가 리스트에 추가
    doMemberAdd : function(fo_obj) {
        fo_obj = fo_obj.split('_');
        var id = fo_obj[1];
        var member = '';

        $('#member_insert').find('.groups').remove();

        if(!$('#member_insert_table').attr('id')) {
            $('#member_insert').append('<table id="member_insert_table" cellspacing="0" class="rowTable"><tbody></tbody></table>');
            $('#member_insert table').append('<thead><tr><th>user_id</th><th>user_name</th><th>nick_name</th><th>delete</th></tr></thead>');
            $('#member_insert table').append('<tfoot><tr><td colspan="4" class="tCenter"></td></tr></tfoot>');
        }

        if(!$('#member_srl__'+id).attr('id')) {
            $('#member_insert table tbody').append('<tr id="member_srl__'+id+'" class="members"><td>'+$('#member_'+id+' td').eq(0).text()+'</td><td>'+$('#member_'+id+' td').eq(1).text()+'</td><td>'+$('#member_'+id+' td').eq(2).text()+'</td><td><a href="#member_delete" title="delete" class="buttonSet buttonDelete"><span>delete</span></a></td></tr>');
        }

        $('#member_insert tbody tr').each(function(i) {
            if($(this).attr('id')) {
            id = $(this).attr('id');
            member += id+',';
            }
        });

        $('#nms_member input[name=target_value]').val(member);
        member = '';

        $('#member_insert table .buttonDelete').bind('click', function(event) {
            $(this).parent().parent().remove();

            $('#member_insert tbody').each(function(i) {
                if($('tr', this).attr('id')) {
                    id = $('tr', this).attr('id');
                    member += id+',';
                }
            });

            $('#nms_member input[name=target_value]').val(member);
            if(!member) $('.insert_button div').hide();

            event.stopPropagation();
        });

        $('.insert_button div').show();
    },
    // Group을 임시 추가 리스트에 추가
    doGroupAdd : function(fo_obj) {
        var id = fo_obj;
        var group = '';
        var group_name = '';

        if(!id) return;
        if(id == 0) return;
        if($('#member_insert').find('.members:eq(0)').attr('id')) return;

        if(!$('#member_insert_table').attr('id')) {
            $('#member_insert').append('<table id="member_insert_table" cellspacing="0" class="rowTable"><tbody></tbody></table>');
            $('#member_insert table').append('<thead><tr><th>user_id</th><th>user_name</th><th>nick_name</th><th>delete</th></tr></thead>');
            $('#member_insert table').append('<tfoot><tr><td colspan="4" class="tCenter"></td></tr></tfoot>');
        }

        if(!$('#group_srl__'+id).attr('id')) {

            $('#nms_member select[name=selected_group_srl] option').each(function (i) {
                if(id == $(this).val()) group_name = $(this).text();
            });

            $('#member_insert table tbody').append('<tr id="group_srl__'+id+'" class="groups"><td>group</td><td>'+group_name+'</td><td></td><td><a href="#member_delete" title="delete" class="buttonSet buttonDelete"><span>delete</span></a></td></tr>');
        }

        $('#member_insert tbody tr').each(function(i) {
            if($(this).attr('id')) {
                id = $(this).attr('id');
                group += id+',';
            }
        });

        $('#nms_member input[name=target_value]').val(group);
        group = '';

        $('#member_insert table .buttonDelete').bind('click', function(event) {
            $(this).parent().parent().remove();

            $('#member_insert tbody').each(function(i) {
                if($('tr', this).attr('id')) {
                    id = $('tr', this).attr('id');
                    group += id+',';
                }
            });

            $('#nms_member input[name=target_value]').val(group);
            if(!group) $('.insert_button div').hide();

            event.stopPropagation();
        });

        $('div.insert_button div').show();
    },
    // Member 검색
    doMember : function(fo_obj) {
        var params = new Array();
        var response_tags = new Array('member_list','member_navigation','page_navigation','selected_group_srl','error');
        params['mode'] = $('#nms_member input[name=mode]').val();
        params['type'] = $('#nms_member input[name=type]').val();
        params['target'] = $('#nms_member input[name=target]').val();
        params['selected_group_srl'] = $('#nms_member select[name=selected_group_srl]').val();
        params['search_target'] = $('#nms_member select[name=search_target]').val();
        params['search_keyword'] = $('#nms_member input[name=search_keyword]').val();
        if(fo_obj) params['page'] = fo_obj;

        exec_xml('nms','dispNmsAdminMember', params, $nms_member.completeMember, response_tags);

    },
    // Member 검색 결과 출력
    completeMember : function(ret_obj) {
        var error = ret_obj['error'];
        var member_list = ret_obj['member_list'];
        var member_navigation = ret_obj['member_navigation'];
        var page_navigation = ret_obj['page_navigation'];
        var selected_group_srl = ret_obj['selected_group_srl'];
        var member = false;

        $('#member_list table').remove();

        if(member_list) {
            $('#member_list').append('<table id="member_table" cellspacing="0" class="rowTable"><tbody></tbody></table>');
            $('#member_list table').append('<thead><tr><th>user_id</th><th>user_name</th><th>nick_name</th><th>insert</th></tr></thead>');
            $('#member_list table').append('<tfoot><tr><td colspan="4" class="tCenter"></td></tr></tfoot>');

        if(member_navigation['item']) {
            var navi_page = '';
            $.each(member_navigation['item'], function(key, val) {
                if(page_navigation.cur_page == val) val = '<strong>'+val+'</strong>';
                else val = '<span class="hand" value="'+val+'">'+val+'</span>';
                navi_page += " "+val;
            });

            $('#member_list table tfoot tr td').append('<span class="hand" value="'+page_navigation.first_page+'">&lt;&lt;</span>'+' '+navi_page+' '+'<span class="hand" value="'+page_navigation.last_page+'">&gt;&gt;</span>');
        }

            if(member_list['item'].length) member = member_list['item'];
            else member = member_list;

            $.each(member, function(key, val) {
                $('#member_list table tbody').append('<tr id="member_'+val.member_srl+'"><td>'+val.user_id+'</td><td>'+val.user_name+'</td><td>'+val.nick_name+'</td><td><a href="#member_add" title="insert" class="buttonSet buttonAdd"><span>insert</span></a></td></tr>');
            });
        }

        $('#member_list table .hand').one('click', function(event) {
            $nms_member.doMember($(this).attr('value'));
        });

        $('#member_list table .buttonAdd').bind('click', function(event) {
            $nms_member.doMemberAdd($(this).parent().parent().attr('id'));
            event.stopPropagation();
        });

        if(selected_group_srl) $nms_member.doGroupAdd(selected_group_srl);

    },
    // 스크립트 로드
    nms_ready : function() {
        $('html').ready(function(){
            $('input.insert').one('click', function(event) {
                $nms_member.doMemberInsert($('#nms_member input[name=target_value]').val());
            });
        });
    }
};

//$.fn.extend(nms_mbrowser);
$nms_member.nms_ready();