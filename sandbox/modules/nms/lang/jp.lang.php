<?php
    /**
     * @file   modules/nms/lang/jp.lang.php
     * @author ラルゲデン (developer@nmsxe.com)
     * @brief  日本語言語パッケージ
     **/

    /**
     * @brief Snmp 収集(Cron) 種類を設定します。
     * @remarks 配列を追加したらnmsXE->基本設定->SNMP収集時間種類に反映されます。
     **/
    $lang->nms_schedule = array(
        "15" => "15秒",
        "30" => "30秒",
        "60" => "1分",
        "180" => "3分",
        "300" => "5分"
    );

    /**
     * @brief Severity正義によるイベント遂行の種類を設定します。
     * @remarks 配列を追加したらnms.act.phpの関数名（小文字）で自動呼び出しされます。
     **/
    $lang->nms_act = array(
        "mail" => "最初のメールアドレスを送る住所で使用します。例）山田:yamada@abc.com （ , でいくつか指定可能）",
        "smsXE" => "最初の携帯番号を送る番号で使用します。（ , でいくつか指定可能）",
        "XEblogAPI" => "XE blogAPI 住所を入力します。例）http://ID:PW@APIアドレス:モジュール名（ , でいくつか指定可能）",
        "Twitter" => "ツイッターで内容を入力します。例）oauth_token:oauth_token_secret（ , でいくつか指定可能）"
    );

    /**
     * @brief nms_actに設定されたACTにmember情報呼び出し用ポップアップアイコンを呼び出しします。
     * @remarks ACTが遂行されたとき登録されたmemberの拡張変数の値を呼びます。(=> '値')は会員情報の拡張変数名です。複数選択時(,)で区分してください。
     **/
    $lang->nms_act_member = array(
        "mail" => "email_address",
        "smsXE" => "phone",
        "Twitter" => "twitter_oauth_token,twitter_oauth_token_secret"
    );

    /* menu */
    $lang->nms_title = "nmsXE";
    $lang->nms_cmd_management = "管理";
    $lang->nms_about_nms = "システム、ネットワーク資源を分析してくれるモジュールです。\nsnmp、socket関数が使用可能にならなければならないし、使用するウェブアカウントがshellスクリップト実行、ファイル生成権限が必要とされます。";
    $lang->nms_dispNmsAdminIndex = "NMSリスト";
    $lang->nms_dispNmsAdminConfig = "基本設定";
    $lang->nms_dispNmsAdminGroupList = "グループ管理";
    $lang->nms_dispNmsAdminSyslogLog = "SYSLOGローグ";
    $lang->nms_dispNmsAdminSnmpTrapLog = "SNMP TRAPローグ";
    $lang->nms_dispNmsAdminSeverityLog = "Severityローグ";
    $lang->nms_dispNmsAdminPageIndex = "NMS管理者ページ";
    $lang->nms_dispNmsAdminMigrationIndex = "マイグレーション";
    $lang->nms_dispNmsAdminHostInfo = "HOST設定";
    $lang->nms_dispNmsAdminInsertHost = "HOST登録";
    $lang->nms_dispNmsAdminMibList = "MIBリスト";
    $lang->nms_dispNmsAdminSeverityList = "Severity設定";
    $lang->nms_dispNmsAdminInsertGroup = "グループ登録";
    $lang->nms_dispNmsAdminGroupInfo = "グループ設定";
    $lang->nms_dispNmsAdminInsertMib = "MIB登録";
    $lang->nms_dispNmsAdminMibInfo = "MIB設定";

    $lang->nms_caption = "リスト情報";

    $lang->nms_typeselect = array(
        "Y" => "Y",
        "N" => "N",
    );
    $lang->nms_notuse = "選択";

    $lang->nms_submit_summary = "情報をセーブします。";

    /* Host List */
    $lang->nms_triggerDispNmsHostList = "HOSTリスト";
    $lang->nms_triggerDispNmsHostList_summary = "モジュールで登録されたホスト情報リストです。";

    /* InsertHost & HostInfo */
    $lang->nms_dispNmsAdminInsertHost_summary = "ホスト登録画面です。";
    $lang->nms_dispNmsAdminHostInfo_summary = "ホスト設定画面です。";
    $lang->nms_mid_title = "モジュールタイトル";
    $lang->nms_about_mid_title = "モジュールタイトルを表す値です。";
    $lang->nms_group_select = "グループ選択";
    $lang->nms_about_group_select = "選択したグループの情報（cron時間等）通りにデータを収集します。グループ登録は’nmsXE > グループ管理’で使用することができます。";
    $lang->nms_ip_type_title = "IP体系";
    $lang->nms_about_ip_type_title = "IPバージョンを選んでください。バージョンによって下記のIP情報処理が代わります。（IPv6は今後支援）";
    $lang->nms_host = "HOSTアドレス";
    $lang->nms_about_host = "SNMP情報を収集する対象IP又はホストドメインを入力してください。（http:// 除外）";
    $lang->nms_community = "コミュニティー名";
    $lang->nms_about_community = "対象ホストで接近可能なコミュニティー名を入力してください。";
    $lang->nms_description = "HOST説明";
    $lang->nms_about_description = "設定したホストの説明を入力してください。（HTMLテグ使用可能）";

    /* DeleteHost */
    $lang->nms_dispNmsAdminDeleteHost_summary = "選んだモジュール情報を削除します。";
    $lang->nms_delete_host_log = "該当モジュールで収集されたローグを全部削除";
    $lang->nms_caption_dispNmsAdminDeleteHost = "件";

    /* Service List */
    $lang->nms_triggerDispNmsServiceList = "サービスリスト";
    $lang->nms_triggerDispNmsServiceList_summary = "モジュールで登録されたサービス情報リストです。";
    $lang->nms_dispNmsAdminServiceInfo = "サービス設定";
    $lang->nms_dispNmsAdminServiceInfo_summary = "サービズ設定画面です。";
    $lang->nms_service_description = "サービス説明";
    $lang->nms_about_service_description = "サービスの説明を入力してください。（HTMLテグ採用可能）";

    /* InsertService */
    $lang->nms_service_type = "サービス種類";
    $lang->nms_about_service_type = "生成するサービス種類を選んでくだざい。";

    /* GroupList */
    $lang->nms_dispNmsAdminGroupList_summary = "グループ情報リストです。";
    $lang->nms_group_list = "グループ名";
    $lang->nms_host_list = "HOSTアドレス";
    $lang->nms_schedule_list = "cron時間";
    $lang->nms_cronstate_list = "実行状態";

    /* InsertGroup */
    $lang->nms_dispNmsAdminInsertGroup_summary = "グループ登録画面です。";
    $lang->nms_dispNmsAdminGroupInfo_summary = "グループ設定画面です。";
    $lang->nms_group_title = "グループ名";
    $lang->nms_about_group_title = "グループ名はHOST情報を登録するとき使用します。（英文+[英文+数字+_]だけ可能。最大40文字）";
    $lang->nms_schedule_title = "cron時間";
    $lang->nms_about_schedule_title = "基本設定で使用されたcron実行時間を表します。設定された時間で収集可能を繰り返して実行します。";
    $lang->nms_cronstate_title = "crontab実行";
    $lang->nms_about_cronstate_title = "今実行するかしないか設定してください。’実行’で選択して登録、修正しすぐ適用されます。";
    $lang->nms_group_description_title = "グループ説明";
    $lang->nms_about_group_description_title = "設定したグループの説明を入力してください。（HTMLテグ使用可能）";

    /* Config */
    $lang->nms_dispNmsAdminConfig_summary = "基本設定画面です。";
    $lang->nms_cron_type = "cron収集";
    $lang->nms_about_cron_type = "特定時間に繰り返して処理をするかしないか選んでください。（crontab等で直接実行する場合は’N’を選択してください。）";
    $lang->nms_syslog_type = "syslog収集";
    $lang->nms_about_syslog_type = "syslogサーバを機動する場合選んでください。（既に機動していたものがあれば’N’を選択してください。）\n※ Unix種類の場合サービスポートが1024以前の場合一般アカウントではサービス実行ができないので下記の命令をコマンド上で入力して実行してください。\n\n（su ログイン後） php -q "._XE_PATH_."modules/nms/nms.socket.php syslog start > /dev/null &\n（su ログイン後） php -q "._XE_PATH_."modules/nms/nms.socket.php syslog stop > /dev/null &";
    $lang->nms_snmptrap_type = "snmp trap収集";
    $lang->nms_about_snmptrap_type = "snmptrapサーバを機動する場合選んでください。（既に機動していた者があれば’N’を選択してください。）\n※ Unix種類の場合サービスポートが1024以前の場合一般アカウントではサービス実行ができないので下記の命令をコマンド上で入力して実行してください。\n\n（su ログイン後） php -q "._XE_PATH_."modules/nms/nms.socket.php snmptrap start > /dev/null &\n（su ログイン後） php -q "._XE_PATH_."modules/nms/nms.socket.php snmptrap stop > /dev/null &";
    $lang->nms_schedule_type = "SNMP収集時間種類";
    $lang->nms_about_schedule_type = "収集形式を独自的に実行する場合収集時間種類を設定できます。\nグループ追加をするとき選択した収集時間だけ選ぶことができます。";
    $lang->nms_compress = "データ圧縮";
    $lang->nms_about_compress = "入力した期間以前のデータは以前になると時、日、週、月順に圧縮され記録されます。（単位：日）";
    $lang->nms_compress_type = "最終データ圧縮方式";
    $lang->nms_about_compress_type = "データ圧縮ができるときどこまで圧縮をするか選んでください。（基本：月）";
    $lang->nms_about_compress_type_mode = array(
        "3" => "時",
        "4" => "日",
        "5" => "週",
        "6" => "月"
    );

    /* InsertMib & MibInfo */
    $lang->nms_dispNmsAdminInsertMib_summary = "MIB登録画面です。";
    $lang->nms_dispNmsAdminMibInfo_summary = "MIB設定画面です。";
    $lang->nms_about_dispNmsAdminInsertMib = "収集するMIBアドレスを入力してください。";
    $lang->nms_mmid = "MIB名";
    $lang->nms_about_mmid = "MIB名は情報を出力するとき円滑に出力するため使用します。（英文+[英文+数字+_]だけ可能。最大40文字）";
    $lang->nms_mib_title = "MIBタイトル";
    $lang->nms_about_mib_title = "MIBタイトルを入力してください。";
    $lang->nms_mib = "MIBアドレス";
    $lang->nms_about_mib = "収集するMIBアドレスを入力してください。（ '.1.3.6 ...' のような形式で入力してください。）\nMBrowserボタンを押すとMBrowserを通じて入力することができます。";
    $lang->nms_max = "MAX数値";
    $lang->nms_about_max = "収集する内容の最大数値を入力してください。この数値は値を計算してseverity等級計算が可能です。";
    $lang->nms_group_name = "グループ名";
    $lang->nms_about_group_name = "情報を出力するとき同じく出力するグループ名を入力してください。";
    $lang->nms_mib_description = "MIB説明";
    $lang->nms_about_mib_description = "設定したMIBの説明を入力してください。（HTMLテグ使用可能）";
    $lang->nms_extra_dispNmsAdminInsertMib_summary = "Extra_Vars登録画面です。";
    $lang->nms_extra_dispNmsAdminMibInfo_summary = "Extra_Vars登録画面です。";
    $lang->nms_legend_name = "凡例名";
    $lang->nms_about_legend_name = "グラフに出力する凡例名を入力してください。（値がない場合MIB名が出力されます。）";
    $lang->nms_extra_collect_mode_title = "収集方法";
    $lang->nms_extra_collect_mode = array(
        "0" => "基本",
        "1" => "増加値",
        "2" => "増加値平均",
        "3" => "平均",
        "4" => "利用率（1）",
        "5" => "利用率（2）"
    );
    $lang->nms_extra_about_collect_mode = "<strong>増加値</strong>：現在トラフィック使用率の場合収集する対象を前後入力値と比較して増加値を求めて現在使用率を求めることができます。\nPC等で現在トラフィック使用率を探すことができない場合はifInOctets.(no)を利用して増加値求めるを設定してください。\n<strong>増加値平均</strong>：増加値求めるの結果をmaxと計算して平均値を求めます。\n<strong>平均</strong>：収集する対象とmaxの値を比較して平均使用率を求めることができます。\n<strong>利用率(1)</strong> : ((max - value) / max) * 100 公式で対象を求めます。\nmemTotalFree.0, memAvailReal.0 等の情報を利用する場合使用してください。\n<strong>利用率(2)</strong> : maxで収集する対象の値を減算します。(max - value) \nssCpuIdle.0 等の情報を利用する場合使用してください。\n\n詳しい説明は<a href=\"http://nms.xpressengine.net/18330001\" title=\"nmsXE MIB設定\" target=\"_blank\"><strong>nmsXE MIB設定</strong></a>を参考してください。";
    $lang->nms_extra_complete_act = "Severity 完了 act";
    $lang->nms_extra_about_complete_act = "設定されたSeverityのすべての条件が完了された場合イベントを送るかどうかを内容です。\n（一つでもチェックされていたらSeverity完了のとき該当のactを実行します。）";

    /* MibList */
    $lang->nms_dispNmsAdminMibList_summary = "グループ情報リストです。";

    /* DeleteMib */
    $lang->nms_dispNmsAdminDeleteMib_summary = "選択したMIB情報を削除します。";
    $lang->nms_delete_mib_log = "該当MIBで収集されたローグを全部削除";
    $lang->nms_caption_dispNmsAdminDeleteMib = "件";

    /* InsertSeverity */
    $lang->nms_dispNmsAdminSeverityInfo_summary = "Severity設定画面です。";
    $lang->nms_about_dispNmsAdminSeverityInfo = "Severity Level規則を適用します。各規則に当たるように適切な値を入れてイベントを発生させるのを目的とします。\n詳しい説明は<a href=\"http://nms.xpressengine.net/19265085\" title=\"nmsXEプロジェクトSeverity設定\" target=\"_blank\"><strong>nmsXEプロジェクトSeverity設定</strong></a>を参考してください。";
    $lang->nms_title_mib_list = "MIB対象選択";
    $lang->nms_title_value = $lang->nms_title_type = "条件";
    $lang->nms_about_type = "入力値とMAX値、収集された値を条件を通じて処理します。（文字型、数字型可能）\n（条件をいくつか登録可能 a,b,c 等で , で区分）";
    $lang->nms_title_event_type = "基準";
    $lang->nms_about_event_type = "上記の条件と一致した場合下記actをどんな規則で送るか設定します。";
    $lang->nms_title_event_sec = "決まった時間";
    $lang->nms_about_event_sec = "秒単位で入力してください。例）1秒 -> 60, 30分 -> 1800";
    $lang->nms_title_event_count = "発生回数";
    $lang->nms_about_event_count = "発生繰り返し回数を入力してください。（少なくない場合や0だった場合、毎回になります。）";
    $lang->nms_type = array(
        "0" => "数値値以上(more)",
        "1" => "数値値以下(less)",
        "2" => "%値以上(more)",
        "3" => "%値以下(less)",
        "4" => "一致(equal)",
        "5" => "不一致(notequal)",
        "6" => "値存在(notnull)",
        "7" => "値未存在(null)",
        "8" => "含み(like_prefix)",
        "9" => "含み(like_tail)",
        "10" => "含み(like)",
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
        "0" => "すべての使用者にブロードキャストされるパニック状況のlevelです。",
        "1" => "システム、データベース損傷等、早速修正しなければならない状況のlevelです。",
        "2" => "ハード装置エラーの中で重大なエラー状況のlevelです。",
        "3" => "ハード装置以外のエラー状況のlevelです。",
        "4" => "警告メッセージlevelです。",
        "5" => "特別な処理が必要とされる非エラーのlevelです。",
        "6" => "情報メッセージlevelです。",
        "7" => "プログラムデバッギング状況のlevelです。",
        "8" => "facilityから発生したメッセージを選択さらたファイルで送らない。除くとき使用(ex. *.degug;mail.none)",
    );

    $lang->nms_event_type = array(
        "0" => "条件が一致するとき発生後決まった時間に発生(Snmp)",
        "1" => "条件一致後決まった時間に発生(Snmp)",
        "2" => "該当条件のみ条件が一致するとき決まった時間に発生(Syslog, SnmpTrap)",
        "3" => "該当条件のみ条件一致後決まった時間に発生(Syslog, SnmpTrap)",
        "4" => "条件が一致したとき発生後該当件を完了処理(Syslog, SnmpTrap)",
    );

    $lang->nms_title_act = "動かせる対象";
    $lang->nms_member = "会員検索";

    /* DeleteSeverity */
    $lang->nms_dispNmsAdminDeleteSeverity_summary = "選択したSeverity情報を削除します。";
    $lang->nms_delete_severity_log= "該当MIBのSeverityで収集されたローグを全部削除";
    $lang->nms_caption_dispNmsAdminDeleteSeverity = "件";

    /* triggerConfigSeverity */
    $lang->nms_dispNmsAdminSeverityConfig_summary = "Severity基本設定";
    $lang->nms_about_dispNmsAdminSeverityConfig = "Severity Level設定のact情報の基本設定及び伝達されるメッセージ様式を作ることが出来ます。\n詳しい説明は<a href=\"http://nms.xpressengine.net/19267112\" title=\"nmsXEお知らせ機能\" target=\"_blank\"><strong>nmsXEお知らせ機能</strong></a>を参考してください。";
    $lang->nms_severity_value = "値";
    $lang->nms_severity_form = "メッセージ";
    $lang->nms_severity_path = "外部ファイル";
    $lang->nms_about_severity_form= "メッセージを送る様式を入力してください。（html可能）";
    $lang->nms_about_severity_path= "外部ファイルの位置を絶対パスを入力してください。\n絶対パス：";

    /* Syslog Log */
    $lang->nms_dispNmsAdminSyslogLog_summary = "SYSLOGを出力します。";
    $lang->nms_syslog_time = "日付";
    $lang->nms_syslog_ip_type = "IP体系";
    $lang->nms_syslog_ip_address = "IPアドレス";
    $lang->nms_syslog_ip_port = "IPポット";
    $lang->nms_syslog_facility = "facility";
    $lang->nms_syslog_severity = "severity";
    $lang->nms_syslog_value = "メッセージ";

    /* SnmpTrap Log */
    $lang->nms_dispNmsAdminSnmpTrapLog_summary = "SNMP TRAPを出力します。";
    $lang->nms_snmptrap_time = "日付";
    $lang->nms_snmptrap_ip_type = "IP体系";
    $lang->nms_snmptrap_ip_address = "IPアドレス";
    $lang->nms_snmptrap_ip_port = "IPポット";
    $lang->nms_snmptrap_trap = "trap level";
    $lang->nms_snmptrap_value = "メッセージ";

    $lang->nms_ip_type = array(
        "4" => "IPv4",
        "6" => "IPv6"
    );

    /* Severity Log */
    $lang->nms_dispNmsAdminSeverityLog_summary = "Severity情報を出力します。";
    $lang->nms_severity_time = "発生日付";
    $lang->nms_severity_module = "モジュール名";
    $lang->nms_severity_mib = "MIB名";
    $lang->nms_severity_level = "Severity Level";
    $lang->nms_severity_value = "値";
    $lang->nms_severity_aware = array(
        "aware" => "状態",
        "0" => "発生",
        "1" => "完了",
        "2" => "確認"
    );
    $lang->nms_severity_awaredate = "完了日付";
    $lang->nms_severity_checkdate = "イベント時間";
    $lang->nms_severity_count = "イベント回数";
    $lang->nms_severity_difftime = "発生間隔（秒）";

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
    $lang->nms_triggerDispNmsHostGraph_summary = "集計画面です。";
    $lang->nms_about_triggerDispNmsHostGraph = "MIBを通じて収集された内容を元に情報を持ってきて処理します。";

    /* dispNmsSeverityList */
    $lang->nms_dispNmsSeverityList_summary = "Severity Level リスト";
    $lang->nms_severity_type = "条件";
    $lang->nms_severity_typeValue = "条件値";
    $lang->nms_severity_value = "値";

    /* triggerDispNmsSeverityRestore */
    $lang->nms_triggerDispNmsSeverityRestore_summary = "Severity完了act設定";
    $lang->nms_about_triggerDispNmsSeverityRestore = "各MIB情報に’Severity完了act’項目を一括で登録します。";

    /* triggerDispNmsSettingWizard */
    $lang->nms_triggerDispNmsSettingWizard_summary = "設定ウィザード";
    $lang->nms_about_triggerDispNmsSettingWizard = "HOST情報からMIB、Severity設定まで設定ウィザードを通じて安く設定を登録することができます。";
    $lang->nms_cmd_wizard = "設定ウィザード";

    /* Wizard Step */
    $lang->nms_wizard_mib_list = "MIB情報";
    $lang->nms_wizard_mib_list_summary = "MIB登録対象情報";
    $lang->nms_wizard_xml_file = "XML設定登録";
    $lang->nms_wizard_xml_file_summary = "XML設定登録対象情報";
    $lang->nms_wizard_cmd_next = "次に";
    $lang->nms_wizard_cmd_prev = "以前に";
    $lang->nms_wizard_list = "対象";
    $lang->nms_wizard_description = "説明";
    $lang->nms_wizard_none = "選択無し";
    $lang->nms_wizard_file = "選択ファイル名";
    $lang->nms_wizard_title = "タイトル";
    $lang->nms_wizard_date = "製作日";
    $lang->nms_wizard_author = "制作者";
    $lang->nms_wizard_count = "登録対象数";
    $lang->nms_wizard_complete = "登録する";
    $lang->nms_mbrowser = "MBrowser情報";
    $lang->nms_mbrowser_summary = "MBrowser情報で対象を登録します。";
    $lang->nms_mb_info_summary = "選択された対象の情報を出力します。";
    $lang->nms_remake_cache_summary = "snmpwalkキャッシュを再生成します。";
    $lang->nms_about_remake_cache = "設定したHOSTのSNMP情報を又読んでキャッシュにセーブします。\n結果値が円滑に出てない場合はキャッシュ再生成をしてください。";
    $lang->nms_invalid_alpha_number = "xml形式が間違えました。";
    $lang->nms_wizard_step3 = "MIB登録";
    $lang->nms_wizard_step4 = "最終確認";

    /* Twitter OAuth Config */
    $lang->nms_twitter_config = "Twitter OAuth設定";
    $lang->nms_about_twitter_config = "Twitter ACTを使用する場合はTwitterでOAuth認証番号を発給しなければなりません。\n登録は<a href=\"http://dev.twitter.com/apps/new\" title=\"OAuth発給アドレス\" target=\"_blank\">http://dev.twitter.com/apps/new</a> ここから行います。\n 登録するときCallbackアドレスは".Context::get('request_uri')."?module=nms&act=getNmsTwitterOauth を入力してください。\n 登録後Consumer keyとConsumer secretを下記に入力してください。";
    $lang->nms_twitter_redirect = "ツイッターに移動中。。";
    $lang->nms_twiiter_extra_var_name = "出力させる拡張変数名";
    $lang->nms_about_twiiter_extra_var_name = "会員情報で使用するtwitter拡張変数名を入力してください。\n入力した名前の拡張変数があれば認証ボタンが出力されます。";

    /* bit.ly Config */
    $lang->nms_bitly_config = "bit.ly設定";
    $lang->nms_about_bitly_config = "bit.lyを設定すればTwitter ACTに内容を登録するとき発生されるURLアドレスを一緒に登録させます。\n bit.lyに加入をして<a href=\"http://bit.ly/a/your_api_key/\" title=\"bit.ly 登録する\" target=\"_blank\">http://bit.ly/a/your_api_key/</a>ここを見るとAPI keyがあります。\n下記にusernameとAPI keyを入力してください。";

    /* SMTP Config */
    $lang->nms_smtp_config = "SMTPサーバ設定";
    $lang->nms_smtp_config_summary = "SMTPサーバ設定画面です。";
    $lang->nms_about_smtp_config = "メールを送るとき使用するSMTPサーバを設定ください。\nサーバ値がない場合ローカルIPで設定されます。";
    $lang->nms_smtp_server = "サーバIP";
    $lang->nms_smtp_secure = "認証使用";
    $lang->nms_smtp_port = "サーバポット";
    $lang->nms_smtp_user = "アカウント名";
    $lang->nms_smtp_pass = "アカウントパスワード";
    $lang->msg_smtp_checks_error = "SMTPサービスが確認できません。";
    $lang->msg_smtp_checks_complete = "SMTPサービスを確認";

    /* error code */
    $lang->msg_group_name_exists = "既に存在するグループの名前です。他の名前を入力してください。";
    $lang->msg_mmid_name_exists = "既に存在するMIB名です。他の名前で入力してください。";
    $lang->msg_mib_name_exists = "既に存在するMIBアドレスです。他のアドレスを入力してください。";
    $lang->msg_severity_checks = "入力した値がありません。";
    $lang->msg_invalid_group_name = "間違ったグループ番号です。";
    $lang->msg_not_cache = "snmpwalkキャッシュを生成に失敗しました。\n（SNMP機能が問題ないか確認してください。）";
    $lang->msg_cache_complete = "snmpwalkキャッシュを再生成しました。";
    $lang->msg_twiiter_oauth_exists = "既にOAuth情報が存在します。";
    $lang->msg_service_checks = "選択された値がありません。";
    $lang->msg_fail_to_fileupload = "ファイル登録を失敗しました。";
    $lang->msg_fail_to_history = "History登録を失敗しました。";

    /* order target / search option */
    $lang->nms_host_order_target = array(
        "group_name" => "グループ名"
    );
    $lang->nms_host_search_option = array(
        "mib_title" => "MIBタイトル",
        "mmid" => "MIBタイトル",
        "mib" => "MIB(OID)",
        "group_name" => "グループタイトル"
    );

    $lang->nms_severity_order_target = array(
        "regdate" => "登録日付",
        "awaredate" => "完了日付",
        "module_srl" => "モジュール番号",
        "mib_srl" => "MIB番号",
        "severity" => "Severity Level",
        "aware" => "状態",
        "value" => "値"
    );
    $lang->nms_severity_search_option = array(
        "value" => "値",
        "mid" => "モジュール名",
        "mmid" => "MIB名",
        "severity" => "Severity Level",
        "aware" => "状態",
        "regdate" => "登録日付",
        "awaredate" => "完了日付"
    );

    $lang->nms_snmptrap_order_target = array(
        "regdate" => "登録日付",
        "snmptrap_srl" => "Snmptrap番号",
        "trap" => "Trap Level",
        "ip_address" => "IPアドレス",
        "ip_port" => "IPポット",
        "value" => "値"
    );
    $lang->nms_snmptrap_search_option = array(
        "trap" => "Trap Level",
        "ip_address" => "IPアドレス",
        "ip_port" => "IPポット",
        "regdate" => "登録日付"
    );

    $lang->nms_syslog_order_target = array(
        "regdate" => "登録日付",
        "syslog_srl" => "Syslogアドレス",
        "priority" => "Priority",
        "facility" => "Facility",
        "severity" => "Severity Level",
        "ip_address" => "IPアドレス",
        "ip_port" => "IPポット",
        "value" => "値"
    );
    $lang->nms_syslog_search_option = array(
        "value" => "値",
        "priority" => "priority",
        "facility" => "facility",
        "severity" => "Severity Level",
        "ip_address" => "IPアドレス",
        "ip_port" => "IPポット",
        "regdate" => "登録日付"
    );

    /* Skin */
    $lang->nms_view_table = "Snmp収集データを表で見せます。";
    $lang->nms_view_current = "現在値／全体";
    $lang->nms_view_legend = "凡例";
    $lang->nms_history_save = "Historyセーブ";

    /* new version */
    $lang->msg_nms_new_version = "現在バージョンは最新バージョンより旧バージョンです。\\n最新バージョンにアップグレードしてください。";
?>