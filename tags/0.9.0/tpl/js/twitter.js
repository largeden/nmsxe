var nms_twitter = {
    member_srl : false,
    extra_var_name : false,
    // 스크립트 로드
    nms_ready : function() {
        jQuery('html').ready(function(){
            if(!nms_twitter.extra_var_name) return;
            nms_twitter.member_srl = jQuery('input[name=member_srl]').val();
            jQuery('input[name='+nms_twitter.extra_var_name+']').after(' <a href="./?module=nms&act=getNmsTwitterOauth&mode=start&member_srl='+nms_twitter.member_srl+'" onclick="popopen(this.href);return false;" class="buttonSet buttonTwitter" title="Twitter Authentication"><span>Twitter Authentication</span></a>');
            if(jQuery(window).width() < 200) window.resizeBy(800, 400);
        });
    }
};

nms_twitter.nms_ready();