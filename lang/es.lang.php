<?php
    /**
     * @file   modules/nms/lang/es.lang.php
     * @author largeden (developer@nmsxe.com)
     * @brief  Español language pack
     **/

    /**
     * @brief Snmp 수집(Cron) 종류를 설정합니다.
     * @remarks 배열을 추가하면 nmsXE->기본설정->SNMP 수집 시간 종류에 반영됩니다.
     **/
    $lang->nms_schedule = array(
        "15" => "15초",
        "30" => "30초",
        "60" => "1분",
        "180" => "3분",
        "300" => "5분"
    );

    /**
     * @brief Severity 정의에 따른 이벤트 수행의 종류를 설정합니다.
     * @remarks 배열을 추가하면 nms.act.php의 함수명(소문자)으로 자동 호출 됩니다.
     **/
    $lang->nms_act = array(
        "mail" => "첫번째 메일주소를 보내는 주소로 사용합니다. 예) 홍길동:hong@abc.com ( , 로 여러개 지정가능)",
        "smsXE" => "첫번째 전화번호를 보내는 번호로 사용합니다.( , 로 여러개 지정가능)",
        "XEblogAPI" => "XE blogAPI 주소를 입력합니다. 예) http://아이디:비밀번호@API주소:모듈이름 ( , 로 여러개 지정가능)",
        "Twitter" => "트위터로 글을 등록합니다. 예) oauth_token:oauth_token_secret ( , 로 여러개 지정가능)"
    );

    /**
     * @brief nms_act에 설정된 ACT에 member 정보호출용 팝업 아이콘을 호출합니다.
     * @remarks ACT가 수행될때 등록된 member의 확장변수의 값을 불러옵니다. (=> '값')은 회원정보의 확장변수명입니다. 복수선택시 (,)로 구분하세요.
     **/
    $lang->nms_act_member = array(
        "mail" => "email_address",
        "smsXE" => "phone",
        "Twitter" => "twitter_oauth_token,twitter_oauth_token_secret"
    );

    /* menu */
    $lang->nms_title = "nmsXE";
    $lang->nms_cmd_management = "관리";
    $lang->nms_about_nms = "시스템, 네트워크 자원을 분석해주는 모듈 입니다.\nsnmp, socket 함수가 사용 가능으로 되어있어야 하며, 사용하는 웹 계정이 shell스크립트 실행, 파일 생성 권한이 있으셔야 합니다.";
    $lang->nms_dispNmsAdminIndex = "NMS 목록";
    $lang->nms_dispNmsAdminConfig = "기본 설정";
    $lang->nms_dispNmsAdminGroupList = "그룹 관리";
    $lang->nms_dispNmsAdminSyslogLog = "SYSLOG 로그";
    $lang->nms_dispNmsAdminSnmpTrapLog = "SNMP TRAP 로그";
    $lang->nms_dispNmsAdminSeverityLog = "Severity 로그";
    $lang->nms_dispNmsAdminPageIndex = "NMS 관리자페이지";
    $lang->nms_dispNmsAdminMigrationIndex = "마이그레이션";
    $lang->nms_dispNmsAdminHostInfo = "HOST 설정";
    $lang->nms_dispNmsAdminInsertHost = "HOST 등록";
    $lang->nms_dispNmsAdminMibList = "MIB 목록";
    $lang->nms_dispNmsAdminSeverityList = "Severity 설정";
    $lang->nms_dispNmsAdminInsertGroup = "그룹 등록";
    $lang->nms_dispNmsAdminGroupInfo = "그룹 설정";
    $lang->nms_dispNmsAdminInsertMib = "MIB 등록";
    $lang->nms_dispNmsAdminMibInfo = "MIB 설정";

    $lang->nms_caption = "목록 정보";

    $lang->nms_typeselect = array(
        "Y" => "실행",
        "N" => "안함",
    );
    $lang->nms_notuse = "- 선택 -";

    $lang->nms_submit_summary = "정보를 저장 합니다.";

    /* Host List */
    $lang->nms_triggerDispNmsHostList = "HOST 목록";
    $lang->nms_triggerDispNmsHostList_summary = "모듈로 등록된 호스트 정보 리스트 입니다.";

    /* InsertHost & HostInfo */
    $lang->nms_dispNmsAdminInsertHost_summary = "호스트 등록 화면 입니다.";
    $lang->nms_dispNmsAdminHostInfo_summary = "호스트 설정 화면 입니다.";
    $lang->nms_mid_title = "모듈 제목";
    $lang->nms_about_mid_title = "모듈 제목을 나타내는 값입니다.";
    $lang->nms_group_select = "그룹 선택";
    $lang->nms_about_group_select = "선택한 그룹의 정보(cron 시간 등)대로 데이터를 수집 합니다. 그룹 등록은 'nmsXE > 그룹관리'에서 하실 수 있습니다.";
    $lang->nms_ip_type_title = "IP 체계";
    $lang->nms_about_ip_type_title = "IP 버전을 선택해주세요. 버전에 따라서 아래 IP 정보 처리가 달라집니다. (IPv6는 향후 지원)";
    $lang->nms_host = "HOST 주소";
    $lang->nms_about_host = "SNMP 정보를 수집할 대상 IP 또는 호스트 도메인을 적어주세요.(http:// 제외)";
    $lang->nms_community = "커뮤니티 이름";
    $lang->nms_about_community = "대상 호스트로 접근 가능한 커뮤니티 이름을 적어주세요.";
    $lang->nms_description = "HOST 설명";
    $lang->nms_about_description = "설정한 호스트의 설명을 적어주세요. (HTML 태그 사용 가능)";

    /* DeleteHost */
    $lang->nms_dispNmsAdminDeleteHost_summary = "선택한 모듈 정보를 삭제 합니다.";
    $lang->nms_delete_host_log = "해당 모듈로 수집된 로그를 전부 삭제";
    $lang->nms_caption_dispNmsAdminDeleteHost = "건";

    /* Service List */
    $lang->nms_triggerDispNmsServiceList = "서비스 목록";
    $lang->nms_triggerDispNmsServiceList_summary = "모듈로 등록된 서비스 정보 리스트 입니다.";
    $lang->nms_dispNmsAdminServiceInfo = "서비스 설정";
    $lang->nms_dispNmsAdminServiceInfo_summary = "서비스 설정 화면 입니다.";
    $lang->nms_service_description = "서비스 설명";
    $lang->nms_about_service_description = "서비스의 설명을 적어주세요. (HTML 태그 사용 가능)";

    /* InsertService */
    $lang->nms_service_type = "서비스 종류";
    $lang->nms_about_service_type = "생성하려는 서비스 종류를 선택해주세요.";

    /* GroupList */
    $lang->nms_dispNmsAdminGroupList_summary = "그룹 정보 리스트 입니다.";
    $lang->nms_group_list = "그룹 이름";
    $lang->nms_host_list = "HOST 주소";
    $lang->nms_schedule_list = "cron 시간";
    $lang->nms_cronstate_list = "실행상태";

    /* InsertGroup */
    $lang->nms_dispNmsAdminInsertGroup_summary = "그룹 등록 화면 입니다.";
    $lang->nms_dispNmsAdminGroupInfo_summary = "그룹 설정 화면 입니다.";
    $lang->nms_group_title = "그룹 이름";
    $lang->nms_about_group_title = "그룹 이름은 HOST 정보를 등록할 때 사용됩니다. (영문+[영문+숫자+_] 만 가능. 최대 40 글자)";
    $lang->nms_schedule_title = "cron 시간";
    $lang->nms_about_schedule_title = "기본설정에서 사용된 cron 실행 시간을 나타냅니다. 설정된 시간으로 수집 기능을 반복 실행 됩니다.";
    $lang->nms_cronstate_title = "crontab 실행";
    $lang->nms_about_cronstate_title = "지금 실행할지 안할지 설정하세요. '실행'으로 선택하여 등록, 수정 시 바로 적용 됩니다.";
    $lang->nms_group_description_title = "그룹 설명";
    $lang->nms_about_group_description_title = "설정한 그룹의 설명을 적어주세요. (HTML 태그 사용 가능)";

    /* Config */
    $lang->nms_dispNmsAdminConfig_summary = "기본 설정 화면 입니다.";
    $lang->nms_cron_type = "cron 수집";
    $lang->nms_about_cron_type = "특정 시간에 반복 처리를 할지 선택 하세요.(crontab 등으로 직접 하실 경우 '안함'으로 하세요.)";
    $lang->nms_syslog_type = "syslog 수집";
    $lang->nms_about_syslog_type = "syslog 서버를 기동할 경우 선택 하세요.(자체적으로 기동중인게 있다면 '안함'으로 하세요.)\n※ Unix 계열의 경우 서비스 포트가 1024 이전일 경우 일반 계정에서는 서비스 동작을 올릴 수 없으니 아래의 명령을 커멘드상에서 실행해주세요.\n\n(su 로그인 후) php -q "._XE_PATH_."modules/nms/nms.socket.php syslog start > /dev/null &\n(su 로그인 후) php -q "._XE_PATH_."modules/nms/nms.socket.php syslog stop > /dev/null &";
    $lang->nms_snmptrap_type = "snmp trap 수집";
    $lang->nms_about_snmptrap_type = "snmptrap 서버를 기동할 경우 선택 하세요.(자체적으로 기동중인게 있다면 '안함'으로 하세요.)\n※ Unix 계열의 경우 서비스 포트가 1024 이전일 경우 일반 계정에서는 서비스 동작을 올릴 수 없으니 아래의 명령을 커멘드상에서 실행해주세요.\n\n(su 로그인 후) php -q "._XE_PATH_."modules/nms/nms.socket.php snmptrap start > /dev/null &\n(su 로그인 후) php -q "._XE_PATH_."modules/nms/nms.socket.php snmptrap stop > /dev/null &";
    $lang->nms_schedule_type = "SNMP 수집 시간 종류";
    $lang->nms_about_schedule_type = "수집 형식을 자체실행으로 할 경우 수집 시간 종류를 설정할 수 있습니다.\n그룹 추가 시 선택한 수집 시간만 고르실 수 있습니다.";
    $lang->nms_compress = "데이터 압축";
    $lang->nms_about_compress = "입력한 기간 이전의 데이터는 이전으로 갈수록 시,일,주,월 순으로 압축되어 기록됩니다. (단위 일)";
    $lang->nms_compress_type = "최종 데이터 압축 방식";
    $lang->nms_about_compress_type = "데이터 압축이 이루어질때 어디까지 압축을 할지 정해주세요. (기본 월)";
    $lang->nms_about_compress_type_mode = array(
        "3" => "시",
        "4" => "일",
        "5" => "주",
        "6" => "월"
    );

    /* InsertMib & MibInfo */
    $lang->nms_dispNmsAdminInsertMib_summary = "MIB 등록 화면 입니다.";
    $lang->nms_dispNmsAdminMibInfo_summary = "MIB 설정 화면 입니다.";
    $lang->nms_about_dispNmsAdminInsertMib = "수집하려는 MIB 주소를 적어주세요.";
    $lang->nms_mmid = "MIB 이름";
    $lang->nms_about_mmid = "MIB 이름은 정보 출력 시 원할하게 출력하게 위해 사용합니다. (영문+[영문+숫자+_] 만 가능. 최대 40 글자)";
    $lang->nms_mib_title = "MIB 제목";
    $lang->nms_about_mib_title = "MIB 제목을 적어주세요.";
    $lang->nms_mib = "MIB 주소";
    $lang->nms_about_mib = "수집하려는 MIB 주소를 적어주세요. ( '.1.3.6 ...' 과 같은 형식으로 적어주세요.)\nMBrowser 버튼을 누르면 MBrowser를 통해서 입력하실 수 있습니다.";
    $lang->nms_max = "MAX 수치";
    $lang->nms_about_max = "수집할 내용의 최대 수치를 적어주세요. 이 수치와 값을 계산하여 severity 등급 계산이 가능합니다.";
    $lang->nms_group_name = "그룹 이름";
    $lang->nms_about_group_name = "정보 출력 시 같이 출력할 그룹 이름를 입력해주세요";
    $lang->nms_mib_description = "MIB 설명";
    $lang->nms_about_mib_description = "설정한 MIB의 설명을 적어주세요. (HTML 태그 사용 가능)";
    $lang->nms_extra_dispNmsAdminInsertMib_summary = "Extra_Vars 등록 화면 입니다.";
    $lang->nms_extra_dispNmsAdminMibInfo_summary = "Extra_Vars 설정 화면 입니다.";
    $lang->nms_legend_name = "범례 이름";
    $lang->nms_about_legend_name = "그래프에 출력할 범례 이름 적어주세요. (값이 없을 경우 MIB 이름을 출력 됩니다.)";
    $lang->nms_extra_collect_mode_title = "수집 방법";
    $lang->nms_extra_collect_mode = array(
        "0" => "기본",
        "1" => "증가치",
        "2" => "증가치 평균",
        "3" => "평균",
        "4" => "이용률(1)",
        "5" => "이용률(2)"
    );
    $lang->nms_extra_about_collect_mode = "<strong>증가치</strong> : 현재 트래픽 사용률의 경우 수집하려는 대상을 전후 입력값을 비교해서 증가치 구해 현재 사용률을 구할 수 있습니다.\nPC 등에서 현재 트래픽 사용률을 찾을 수 없는 경우는 ifInOctets.(no)를 이용하여 증가치 구하기로 설정하세요.\n<strong>증가치 평균</strong> : 증가치 구하기의 결과를 max와 계산하여 평균값을 구합니다.\n<strong>평균</strong> : 수집하려는 대상과 max의 값을 비교하여 평균 사용률을 구할 수 있습니다.\n<strong>이용률(1)</strong> : ((max - value) / max) * 100 공식으로 대상을 구합니다.\nmemTotalFree.0, memAvailReal.0 등의 정보를 이용할 경우 사용하세요.\n<strong>이용률(2)</strong> : max에서 수집하려는 대상의 값을 뺍니다. (max - value) \nssCpuIdle.0 등의 정보를 이용할 경우 사용하세요.\n\n자세한 설명은 <a href=\"http://nms.xpressengine.net/18330001\" title=\"nmsXE MIB 설정\" target=\"_blank\"><strong>nmsXE MIB 설정</strong></a>을 참조하세요.";
    $lang->nms_extra_complete_act = "Severity 완료 act";
    $lang->nms_extra_about_complete_act = "설정된 Severity의 모든 조건이 완료 되었을 경우 이벤트를 보낼지 여부 입니다.\n(하나라도 체크되어있으면 Severity 완료 시 해당 act를 동작시킵니다.)";

    /* MibList */
    $lang->nms_dispNmsAdminMibList_summary = "그룹 정보 리스트 입니다.";

    /* DeleteMib */
    $lang->nms_dispNmsAdminDeleteMib_summary = "선택한 MIB 정보를 삭제 합니다.";
    $lang->nms_delete_mib_log = "해당 MIB로 수집된 로그를 전부 삭제";
    $lang->nms_caption_dispNmsAdminDeleteMib = "건";

    /* InsertSeverity */
    $lang->nms_dispNmsAdminSeverityInfo_summary = "Severity 설정 화면 입니다.";
    $lang->nms_about_dispNmsAdminSeverityInfo = "Severity Level 규칙을 적용합니다. 각 규칙에 해당되도록 적당한 값을 넣어서 이벤트를 발생시킴을 목적으로 합니다.\n자세한 설명은 <a href=\"http://nms.xpressengine.net/19265085\" title=\"nmsXE 프로젝트 Severity 설정\" target=\"_blank\"><strong>nmsXE 프로젝트 Severity 설정</strong></a>을 참조하세요.";
    $lang->nms_title_mib_list = "MIB 대상 선택";
    $lang->nms_title_value = $lang->nms_title_type = "조건";
    $lang->nms_about_type = "입력값과 MAX값, 수집된 값을 조건을 통하여 처리합니다.(문자형,숫자형 가능)\n(조건을 여러개 등록가능 a,b,c 등으로 , 로 구분)";
    $lang->nms_title_event_type = "기준";
    $lang->nms_about_event_type = "위의 조건과 일치할 경우 아래 act를 어떤 규칙으로 보낼지 설정합니다.";
    $lang->nms_title_event_sec = "정해진 시간";
    $lang->nms_about_event_sec = "초단위로 적습니다. 예) 1분 -> 60, 30분 -> 1800";
    $lang->nms_title_event_count = "발생횟수";
    $lang->nms_about_event_count = "발생 반복횟수를 적어주세요. (적지 않거나 0일 경우 매번이 됩니다.)";
    $lang->nms_type = array(
        "0" => "수치값 이상(more)",
        "1" => "수치값 이하(less)",
        "2" => "%값 이상(more)",
        "3" => "%값 이하(less)",
        "4" => "일치(equal)",
        "5" => "불일치(notequal)",
        "6" => "값 존재(notnull)",
        "7" => "값 미존재(null)",
        "8" => "포함(like_prefix)",
        "9" => "포함(like_tail)",
        "10" => "포함(like)",
    );

    $lang->nms_facility = array(
        "0" => "kernel msg<br />(0)",
        "1" => "user-level msg<br />(1)",
        "2" => "mail system<br />(2)",
        "3" => "system daemons<br />(3)",
        "4" => "security/authorization msg<br />(4)",
        "5" => "msg generated internally by syslogd<br />(5)",
        "6" => "line printer subsystem<br />(6)",
        "7" => "network news subsystem<br />(7)",
        "8" => "UUCP subsystem<br />(8)",
        "9" => "clock daemon<br />(9)",
        "10" => "security/authorization messages<br />(10)",
        "11" => "FTP daemon<br />(11)",
        "12" => "NTP subsystem<br />(12)",
        "13" => "log audit<br />(13)",
        "14" => "log alert<br />(14)",
        "15" => "clock daemon<br />(15)",
        "16" => "local0<br />(16)",
        "17" => "local1<br />(17)",
        "18" => "local2<br />(18)",
        "19" => "local3<br />(19)",
        "20" => "local4<br />(20)",
        "21" => "local5<br />(21)",
        "22" => "local6<br />(22)",
        "23" => "local7<br />(23)"
    );

    $lang->nms_title_severity_level = "Severity Level";
    $lang->nms_severity = array(
        "0" => "emergencies(0)",
        "1" => "alerts(1)",
        "2" => "critical(2)",
        "3" => "errors(3)",
        "4" => "warnings(4)",
        "5" => "notifications(5)",
        "6" => "informational(6)",
        "7" => "debugging(7)",
        "8" => "none(8)",
    );

    $lang->nms_about_severity = array(
        "0" => "모든 사용자에게 브로드캐스트되는 패닉 상황의 level 입니다.",
        "1" => "시스템, 데이터베이스 손상 등 즉각 수정해야 하는 상황의 level 입니다.",
        "2" => "하드장치 오류중 중대한 에러 상황의 level 입니다.",
        "3" => "하드장치 이외의 오류 상황의 level 입니다.",
        "4" => "경고메시지 level 입니다.",
        "5" => "특별한 처리가 필요할 수 있는 비 오류 상황의 level 입니다.",
        "6" => "정보메시지 level 입니다.",
        "7" => "프로그램 디버깅 상황의 level 입니다.",
        "8" => "facility로부터 발생한 메시지를 선택된 파일로 보내지 않는다. 제외할 때 사용(ex. *.degug;mail.none)",
    );

    $lang->nms_event_type = array(
        "0" => "조건 일치 시 발생 후 정해진 시간에 발생(Snmp)",
        "1" => "조건 일치 후 정해진 시간에 발생(Snmp)",
        "2" => "해당 조건에 대해서만 조건 일치 시 발생 후 정해진 시간에 발생(Syslog, SnmpTrap)",
        "3" => "해당 조건에 대해서만 조건 일치 후 정해진 시간에 발생(Syslog, SnmpTrap)",
        "4" => "조건 일치 시 발생 후 해당 건 완료 처리(Syslog, SnmpTrap)",
    );

    $lang->nms_title_act = "동작시킬 대상";
    $lang->nms_member = "회원 검색";

    /* DeleteSeverity */
    $lang->nms_dispNmsAdminDeleteSeverity_summary = "선택한 Severity 정보를 삭제 합니다.";
    $lang->nms_delete_severity_log= "해당 MIB의 Severity로 수집된 로그를 전부 삭제";
    $lang->nms_caption_dispNmsAdminDeleteSeverity = "건";

    /* triggerConfigSeverity */
    $lang->nms_dispNmsAdminSeverityConfig_summary = "Severity 기본 설정";
    $lang->nms_about_dispNmsAdminSeverityConfig = "Severity Level 설정의 act 정보의 기본 설정 및 전달될 메시지 양식을 만드실 수 있습니다.\n자세한 설명은 <a href=\"http://nms.xpressengine.net/19267112\" title=\"메시지 규칙\" target=\"_blank\"><strong>nmsXE 알림 기능</strong></a>을 참조하세요.";
    $lang->nms_severity_value = "값";
    $lang->nms_severity_form = "메시지";
    $lang->nms_severity_path = "외부파일";
    $lang->nms_about_severity_form= "메시지를 전달할 양식을 입력해주세요.(html 가능)";
    $lang->nms_about_severity_path= "외부파일의 위치를 절대경로로 입력해주세요.\n절대경로 :";

    /* Syslog Log */
    $lang->nms_dispNmsAdminSyslogLog_summary = "SYSLOG를 출력 합니다.";
    $lang->nms_syslog_time = "일시";
    $lang->nms_syslog_ip_type = "IP 체계";
    $lang->nms_syslog_ip_address = "아이피";
    $lang->nms_syslog_ip_port = "이용 포트";
    $lang->nms_syslog_facility = "facility";
    $lang->nms_syslog_severity = "severity";
    $lang->nms_syslog_value = "메시지";

    /* SnmpTrap Log */
    $lang->nms_dispNmsAdminSnmpTrapLog_summary = "SNMP TRAP을 출력 합니다.";
    $lang->nms_snmptrap_time = "일시";
    $lang->nms_snmptrap_ip_type = "IP 체계";
    $lang->nms_snmptrap_ip_address = "아이피";
    $lang->nms_snmptrap_ip_port = "이용 포트";
    $lang->nms_snmptrap_trap = "trap level";
    $lang->nms_snmptrap_value = "메시지";

    $lang->nms_ip_type = array(
        "4" => "IPv4",
        "6" => "IPv6"
    );

    /* Severity Log */
    $lang->nms_dispNmsAdminSeverityLog_summary = "Severity 정보를 출력 합니다.";
    $lang->nms_severity_time = "발생일시";
    $lang->nms_severity_module = "모듈이름";
    $lang->nms_severity_mib = "MIB이름";
    $lang->nms_severity_level = "Severity Level";
    $lang->nms_severity_value = "값";
    $lang->nms_severity_aware = array(
        "aware" => "상태",
        "0" => "발생",
        "1" => "완료",
        "2" => "인지"
    );
    $lang->nms_severity_awaredate = "완료일시";
    $lang->nms_severity_checkdate = "이벤트시간";
    $lang->nms_severity_count = "이벤트횟수";
    $lang->nms_severity_difftime = "발생간격(초)";

    /* Snmp Log */
    $lang->nms_session = array(
        "1" => "sec",
        "2" => "min",
        "3" => "hour",
        "4" => "day",
        "5" => "week",
        "6" => "mon",
        "7" => "year"
    );

    /* triggerDispNmsHostGraph */
    $lang->nms_triggerDispNmsHostGraph_summary = "집계 화면 입니다.";
    $lang->nms_about_triggerDispNmsHostGraph = "MIB를 통해 수집된 내용을 바탕으로 정보를 뽑아 처리 합니다.";

    /* dispNmsSeverityList */
    $lang->nms_dispNmsSeverityList_summary = "Severity Level 리스트";
    $lang->nms_severity_type = "조건";
    $lang->nms_severity_typeValue = "조건 값";
    $lang->nms_severity_value = "값";

    /* triggerDispNmsSeverityRestore */
    $lang->nms_triggerDispNmsSeverityRestore_summary = "Severity 완료 act 설정";
    $lang->nms_about_triggerDispNmsSeverityRestore = "각 MIB 정보에 'Severity 완료 act' 항목을 일괄 등록합니다.";

    /* triggerDispNmsSettingWizard */
    $lang->nms_triggerDispNmsSettingWizard_summary = "설정 마법사";
    $lang->nms_about_triggerDispNmsSettingWizard = "HOST 정보부터 MIB, Severity 설정까지 설치 마법사를 통하여 쉽게 설정 등록할 수 있습니다.";
    $lang->nms_cmd_wizard = "설정하기";

    /* Wizard Step */
    $lang->nms_wizard_mib_list = "MIB 정보";
    $lang->nms_wizard_mib_list_summary = "MIB 등록 대상 정보";
    $lang->nms_wizard_xml_file = "XML 설정 등록";
    $lang->nms_wizard_xml_file_summary = "XML 설정 등록 대상 정보";
    $lang->nms_wizard_cmd_next = "다음";
    $lang->nms_wizard_cmd_prev = "이전";
    $lang->nms_wizard_list = "대상";
    $lang->nms_wizard_description = "설명";
    $lang->nms_wizard_none = "선택 안됨";
    $lang->nms_wizard_file = "선택 파일명";
    $lang->nms_wizard_title = "이름";
    $lang->nms_wizard_date = "제작일";
    $lang->nms_wizard_author = "제작자";
    $lang->nms_wizard_count = "등록대상 수";
    $lang->nms_wizard_complete = "등록하기";
    $lang->nms_mbrowser = "MBrowser 정보";
    $lang->nms_mbrowser_summary = "MBrowser 정보로 대상을 등록합니다.";
    $lang->nms_mb_info_summary = "선택된 대상의 정보를 출력합니다.";
    $lang->nms_remake_cache_summary = "snmpwalk 캐시를 재생성 합니다.";
    $lang->nms_about_remake_cache = "설정한 HOST의 SNMP정보를 다시 읽어서 캐시에 저장합니다.\n결과값이 원활하게 나오지 않을 경우 캐시재생성을 하세요.";
    $lang->nms_invalid_alpha_number = "xml 형식이 잘못되었습니다.";
    $lang->nms_wizard_step3 = "MIB 등록";
    $lang->nms_wizard_step4 = "최종 확인";

    /* Twitter OAuth Config */
    $lang->nms_twitter_config = "Twitter OAuth 설정";
    $lang->nms_about_twitter_config = "Twitter ACT를 사용하실려면 Twitter에서 OAuth 인증번호를 발급받으셔야 합니다.\n등록은 <a href=\"http://dev.twitter.com/apps/new\" title=\"OAuth 발급 주소\" target=\"_blank\">http://dev.twitter.com/apps/new</a> 이곳에서 하시면 됩니다.\n 등록시 Callback 주소는 ".Context::get('request_uri')."?module=nms&act=getNmsTwitterOauth 를 적어주세요.\n 등록 후 Consumer key와 Consumer secret를 아래에 입력하세요.";
    $lang->nms_twitter_redirect = "트위터로 이동 중...";
    $lang->nms_twiiter_extra_var_name = "출력시킬 확장 변수명";
    $lang->nms_about_twiiter_extra_var_name = "회원 정보에서 사용하는 twitter 확장변수명을 적어주세요.\n적어놓으신 이름의 확장변수가 있으면 인증 버튼이 출력됩니다.";

    /* bit.ly Config */
    $lang->nms_bitly_config = "bit.ly 설정";
    $lang->nms_about_bitly_config = "bit.ly를 설정하면 Twitter ACT로 글을 등록할때 발생되는 URL주소를 같이 등록해줍니다.\n bit.ly에 가입을 하시고 <a href=\"http://bit.ly/a/your_api_key/\" title=\"bit.ly 등록하기\" target=\"_blank\">http://bit.ly/a/your_api_key/</a>여기를 보시면 API key가 있습니다.\n아래에 username과 API key를 입력하세요.";

    /* SMTP Config */
    $lang->nms_smtp_config = "SMTP 서버 설정";
    $lang->nms_smtp_config_summary = "SMTP 서버 설정 화면 입니다.";
    $lang->nms_about_smtp_config = "메일을 보낼 때 사용할 SMTP 서버를 설정하십시오.\n서버 값이 없을 경우 로컬 IP로 설정 됩니다.";
    $lang->nms_smtp_server = "서버 IP";
    $lang->nms_smtp_secure = "인증 사용";
    $lang->nms_smtp_port = "서버 Port";
    $lang->nms_smtp_user = "계정 아이디";
    $lang->nms_smtp_pass = "계정 패스워드";
    $lang->msg_smtp_checks_error = "SMTP 서비스를 확인 할 수 없습니다.";
    $lang->msg_smtp_checks_complete = "SMTP 서비스를 확인";

    /* error code */
    $lang->msg_group_name_exists = "이미 존재하는 그룹 이름입니다. 다른 이름을 입력해주세요.";
    $lang->msg_mmid_name_exists = "이미 존재하는 MIB 이름입니다. 다른 이름을 입력해주세요.";
    $lang->msg_mib_name_exists = "이미 존재하는 MIB 주소입니다. 다른 주소을 입력해주세요.";
    $lang->msg_severity_checks = "입력 값이 없습니다.";
    $lang->msg_invalid_group_name = '잘못된 그룹번호입니다.';
    $lang->msg_not_cache = "snmpwalk 캐시를 생성하지 못했습니다.\n(SNMP가 동작중인지 체크필요)";
    $lang->msg_cache_complete = "snmpwalk 캐시를 재생성 하였습니다.";
    $lang->msg_twiiter_oauth_exists = "이미 OAuth 정보가 존재합니다.";
    $lang->msg_service_checks = "선택된 값이 없습니다.";
    $lang->msg_fail_to_fileupload = "파일 등록을 실패하였습니다.";
    $lang->msg_fail_to_history = "History 등록을 실패하였습니다.";

    /* order target / search option */
    $lang->nms_host_order_target = array(
        "group_name" => "그룹이름"
    );
    $lang->nms_host_search_option = array(
        "mib_title" => "MIB 제목",
        "mmid" => "MIB 이름",
        "mib" => "MIB(OID)",
        "group_name" => "그룹이름"
    );

    $lang->nms_severity_order_target = array(
        "regdate" => "등록일시",
        "awaredate" => "완료일시",
        "module_srl" => "모듈 번호",
        "mib_srl" => "MIB 번호",
        "severity" => "Severity Level",
        "aware" => "상태",
        "value" => "값"
    );
    $lang->nms_severity_search_option = array(
        "value" => "값",
        "mid" => "모듈 이름",
        "mmid" => "MIB 이름",
        "severity" => "Severity Level",
        "aware" => "상태",
        "regdate" => "등록일시",
        "awaredate" => "완료일시"
    );

    $lang->nms_snmptrap_order_target = array(
        "regdate" => "등록일시",
        "snmptrap_srl" => "Snmptrap 번호",
        "trap" => "Trap Level",
        "ip_address" => "IP 주소",
        "ip_port" => "IP 포트",
        "value" => "값"
    );
    $lang->nms_snmptrap_search_option = array(
        "trap" => "Trap Level",
        "ip_address" => "IP 주소",
        "ip_port" => "IP 포트",
        "regdate" => "등록일시"
    );

    $lang->nms_syslog_order_target = array(
        "regdate" => "등록일시",
        "syslog_srl" => "Syslog 주소",
        "priority" => "Priority",
        "facility" => "Facility",
        "severity" => "Severity Level",
        "ip_address" => "IP 주소",
        "ip_port" => "IP 포트",
        "value" => "값"
    );
    $lang->nms_syslog_search_option = array(
        "value" => "값",
        "priority" => "priority",
        "facility" => "facility",
        "severity" => "Severity Level",
        "ip_address" => "IP 주소",
        "ip_port" => "IP 포트",
        "regdate" => "등록일시"
    );

    /* Skin */
    $lang->nms_view_table = "Snmp 수집 데이터를 표로 보여줍니다.";
    $lang->nms_view_current = "현재 값/전체";
    $lang->nms_view_legend = "범례";
    $lang->nms_history_save = "History 저장";

    /* new version */
    $lang->msg_nms_new_version = "현재 버전은 최신 버전보다 구버전 입니다.\\n최신버전으로 업그래이드 해주세요.";
?>