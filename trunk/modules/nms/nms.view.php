<?php
    /**
     * @class  nmsView
     * @author largeden (developer@nmsxe.com)
     * @brief  nmsXE View class
     **/

    class nmsView extends nms {

        /**
         * @brief 초기화
         **/
        function init() {
            /**
             * 스킨 경로를 미리 template_path 라는 변수로 설정함
             * 스킨이 존재하지 않는다면 xe_board로 변경
             **/
            $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            if(!is_dir($template_path)||!$this->module_info->skin) {
                $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            }
            $this->setTemplatePath($template_path);
        }

        /**
         * @brief MIB,Syslog,Snmp Trap 등록 화면에 표시될 Severity 트리거 함수
         **/
        function triggerDispNmsSeverityConfig(&$obj) {
            $oNmsModel = &getModel('nms');
            $oModuleModel = &getModel('module');

            $oNmsConfig = $oModuleModel->getModuleConfig('nms');
            $args = Context::getRequestVars();

            $nms_info = $oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
            if($nms_info->mid == $oNmsConfig->severity_mid) return;

            $args->var_idx = -3;

            $args->order_type = 'asc';

            $output = $oNmsModel->getNmsExtraVars($args);

            foreach($output->data as $key => $vals)
                $severity_config = unserialize($vals->value);

            foreach(array('value', 'form', 'path') as $type) {
                if($severity_config->{"act_$type"}) {
                    foreach($severity_config->{"act_$type"} as $acts => $val)
                        $severity->{"act_$type"}[$acts] = $val;
                }
            }

            $severity_config = $severity;

            // 템플릿에 쓰기 위해서 context::set
            Context::set('severity_config', $severity_config);

            // 템플릿 파일 지정
            $oTemplate = &TemplateHandler::getInstance();
            $tpl = $oTemplate->compile($this->module_path.'tpl', 'trigger_severity_config');
            $obj .= $tpl;

            return new Object();
        }

        /**
         * @brief 기본설정 메뉴에서 Twitter OAuth 설정 트리거 함수
         **/
        function triggerDispNmsTwitterConfig(&$obj) {
            $oNmsModel = &getModel('nms');
            $oModuleModel = &getModel('module');

            $args = Context::getRequestVars();

            $config = $oModuleModel->getModuleConfig('nms');

            // 템플릿에 쓰기 위해서 context::set
            Context::set('twiiter_info', $config->twitter_config);
            Context::set('bitly_info', $config->bitly_config);

            // 템플릿 파일 지정
            $oTemplate = &TemplateHandler::getInstance();
            $tpl = $oTemplate->compile($this->module_path.'tpl', 'trigger_twitter_config');
            $obj .= $tpl;

            return new Object();
        }

        /**
         * @brief 기본설정 메뉴에서 SMTP 설정 트리거 함수
         **/
        function triggerDispNmsSmtpConfig(&$obj) {
            $oNmsModel = &getModel('nms');
            $oModuleModel = &getModel('module');

            $args = Context::getRequestVars();

            $config = $oModuleModel->getModuleConfig('nms');

            // 템플릿에 쓰기 위해서 context::set
            Context::set('smtp_info', $config->smtp_config);

            // 템플릿 파일 지정
            $oTemplate = &TemplateHandler::getInstance();
            $tpl = $oTemplate->compile($this->module_path.'tpl', 'trigger_smtp_config');
            $obj .= $tpl;

            return new Object();
        }

        /**
         * @brief Host 목록 트리거 함수
         **/
        function triggerDispNmsHostList(&$obj) {
            $oNmsModel = &getModel('nms');

            // 등록된 nms 모듈을 불러와 세팅
            $args->sort_index = 'module_srl';
            $args->page = Context::get('page');
            $oHost = $oNmsModel->getNmsHostList($args);

            foreach($oHost->data as $key => $attribute) {
                $module_srl = $attribute->module_srl;
                $attribute->type = 'module_srl';
                if(!$GLOBALS['XE_NMS_LIST'][$key]) {
                    $oNms = null;
                    $oNms = new nmsItem();
                    $oNms->setAttribute($attribute);

                    $oHost->data[$key] = $GLOBALS['XE_NMS_LIST'][$key] = $oNms;
                }
            }

            // 템플릿에 쓰기 위해서 context::set
            Context::set('total_count', $oHost->total_count);
            Context::set('total_page', $oHost->total_page);
            Context::set('host_list', $oHost->data);
            Context::set('page', $oHost->page);
            Context::set('page_navigation', $oHost->page_navigation);

            // 템플릿 파일 지정
            $oTemplate = &TemplateHandler::getInstance();
            $tpl = $oTemplate->compile($this->module_path.'tpl', 'trigger_host_list');
            $obj .= $tpl;

            return new Object();
        }

        /**
         * @brief Service 목록 트리거 함수
         **/
        function triggerDispNmsServiceList(&$obj) {
            $oModuleModel = &getModel('module');

            $nms_config = $oModuleModel->getModuleConfig('nms');

            $mid = array($nms_config->severity_mid, $nms_config->syslog_mid, $nms_config->snmptrap_mid);

            $module_srl = $oModuleModel->getModuleSrlByMid($mid);

            if($module_srl) {
                $args->module = 'nms';
                $args->module_srls = implode(',', $module_srl);

                $oService = $oModuleModel->getMidList($args);
                if(!$oService) $oService = array();

                // 등록된 nms 모듈을 불러와 세팅
                foreach($oService as $key => $attribute) {
                    $module_srl = $attribute->module_srl;
                    $attribute->type = 'module_srl';
                    if(!$GLOBALS['XE_NMS_LIST'][$key]) {
                        $oNms = null;
                        $oNms = new nmsItem();
                        $oNms->setAttribute($attribute);

                        $oService[$key] = $GLOBALS['XE_NMS_LIST'][$key] = $oNms;
                    }
                }
            }

            // 템플릿에 쓰기 위해서 context::set
            Context::set('service_list', $oService);

            // 템플릿 파일 지정
            $oTemplate = &TemplateHandler::getInstance();
            $tpl = $oTemplate->compile($this->module_path.'tpl', 'trigger_service_list');
            $obj .= $tpl;

            return new Object();
        }

        /**
         * @brief Severity 오류가 복구 시 수행시킬 정보를 기록하기 위한 트리거 함수
         **/
        function triggerDispNmsSeverityRestore(&$obj) {
            // 템플릿 파일 지정
            $oTemplate = &TemplateHandler::getInstance();
            $tpl = $oTemplate->compile($this->module_path.'tpl', 'trigger_severity_restore');
            $obj .= $tpl;

            return new Object();
        }

        /**
         * @brief Severity 오류가 복구 시 수행시킬 정보를 기록하기 위한 트리거 함수
         **/
        function triggerDispNmsSettingWizard(&$obj) {
            // 템플릿 파일 지정
            $oTemplate = &TemplateHandler::getInstance();
            $tpl = $oTemplate->compile($this->module_path.'tpl', 'trigger_setting_wizard');
            $obj .= $tpl;

            return new Object();
        }

        /**
         * @brief 모듈 출력
         **/
        function dispNmsContent() {
            // 접근 권한 체크
            if(!$this->grant->access) return $this->dispNmsMessage("msg_not_permitted");

            $args = Context::getRequestVars();

            $oModuleModel = &getModel('module');
            $obj = $oModuleModel->getModuleConfig('nms');
            switch($this->mid) {
                case $obj->severity_mid:
                    $this->dispNmsContentSeverity();
                break;
                case $obj->syslog_mid:
                    $this->dispNmsContentSyslog();
                break;
                case $obj->snmptrap_mid:
                    $this->dispNmsContentSnmptrap();
                break;
                default:
                    $this->dispNmsContentHost();
                break;
            }
        }

        /**
         * @brief 모듈 출력 (Host)
         **/
        function dispNmsContentHost() {
            $oNmsModel = &getModel('nms');
            $oModuleModel = &getModel('module');
            $lang = &$GLOBALS['lang'];

            $args = Context::getRequestVars();

            if($args->list_style) $list_style = $args->list_style;
            else $list_style = $this->module_info->default_style;
            Context::set('list_style', $list_style);

            if($args->view_style) $view_style = $args->view_style;
            else $view_style = $this->module_info->view_style;
            Context::set('view_style', $view_style);

            $obj->module_srl = $this->module_srl;
            $obj->page = $args->page;
            $obj->list_count = $this->module_info->list_count;
            $obj->page_count = $this->module_info->page_count;

            // 템플릿에서 사용할 검색옵션 세팅 (검색옵션 key값은 미리 선언되어 있는데 이에 대한 언어별 변경을 함)
            Context::set('search_option', $lang->nms_host_search_option);

            // 검색과 정렬을 위한 변수 설정
            $obj->search_target = $args->search_target;
            $obj->search_keyword = $args->search_keyword;

            // 만약 카테고리가 있거나 검색어가 있으면list_count를 search_list_count 로 이용
            if($args->search_keyword) $obj->list_count = $this->module_info->search_list_count;

            // 지정된 정렬값이 없다면 스킨에서 설정한 정렬 값을 이용함
            $obj->sort_index = ($this->module_info->order_target)?$this->module_info->order_target:'group_name';
            $obj->order_type = $this->module_info->order_type;

            if(!$this->module_info->width_count) $this->module_info->width_count = 5;

            $module_config = $oModuleModel->getModuleConfig('nms');
            $module_info = $this->module_info;
            $group_info = $oNmsModel->getNmsGroupInfo($module_info->group_srl);
            $nms_group_info = $oNmsModel->getNmsMibGroup($obj);

            if($args->group_name) {
                // 접근 권한 체크
                if(!$this->grant->view) return $this->dispNmsMessage("msg_not_permitted");

                $obj->group_name = $args->group_name;
                $mib_group_info = $oNmsModel->getNmsMibGroupInfo($obj);
                Context::set('group_name',$mib_group_info->group_name);
                Context::set('mib_group_info',$mib_group_info);
                $this->dispNmsContentGroup($args->group_name);
            }

            Context::set('module_config',$module_config->moduleConfig);
            Context::set('module_info',$module_info);
            Context::set('group_info',$group_info);
            Context::set('nms_group_info',$nms_group_info->data);
            Context::set('total_count', $nms_group_info->total_count);
            Context::set('total_page', $nms_group_info->total_page);
            Context::set('page', $nms_group_info->page);
            Context::set('page_navigation', $nms_group_info->page_navigation);

            // History 출력
            if(Context::get('document_srl')) $this->dispNmsHistory();
            $this->dispNmsHistoryList();

            $this->setTemplateFile('list');
        }

        /**
         * @brief HOST 모듈에서 Group 선택시 출력
         **/
        function dispNmsContentGroup($group_name = null) {
            $oNmsModel = &getModel('nms');
            $obj->module_srl = $this->module_srl;
            $obj->group_name = $group_name;

            if(Context::get('view_style')=='table') {
                $group_info = $oNmsModel->getNmsMib($obj);

                // 출력 수를 정함(기본 20)
                $args->list_count = (Context::get('list_count'))?Context::get('list_count'):20;
                $args->page = Context::get('table_page');
                $args->page_count = 10;
                if(Context::get('regdate')) {
                    $args->regdate = Context::get('regdate');
                    if(strlen(Context::get('regdate'))>=10) $args->regdate.=59;
                }

                foreach($group_info as $mib) {
                    $args->mib_srl = $mib->mib_srl;
                    $mib->extra_vars = unserialize($mib->extra_vars);
                    $output = $oNmsModel->getNmsSnmpLog($args);
                    if(!$output->toBool()) continue;
                    if(!$i) $table_output = $output;

                    $i=1;
                    // Snmp log 정보를 구함
                    foreach($output->data as $no => $attribute) {
                        $key = $i++;
                        $attribute->key = $key;
                        $attribute->type = 'key';
                        if(!$GLOBALS['XE_NMS_KEY_LIST'][$key]) {
                            $oNms = null;
                            $oNms = new nmsItem();
                            $oNms->setAttribute($attribute);
                            $oNms->variables['session'] = $attribute->session;
                            $oNms->variables['no'] = $no;

                            $oSnmp_info[$key] = $GLOBALS['XE_NMS_KEY_LIST'][$key] = $oNms;
                            unset($oSnmp_info[$key]->variables['value']);
                        }

                        // group_name에 포함되는 mib의 수만큼 배열화
                        $oSnmp_info[$key]->variables['value'][$mib->mib_srl] = $attribute->value;
                    }
                }

                Context::set('snmp_info',$oSnmp_info);
                Context::set('table_total_count', $table_output->total_count);
                Context::set('table_total_page', $table_output->total_page);
                Context::set('table_page', $table_output->page);
                Context::set('table_page_navigation', $table_output->page_navigation);
            }

            $output = $oNmsModel->getNmsMib($obj);

            foreach($output as $key => $attribute) {
                $mib_srl = $attribute->mib_srl;
                $attribute->type = 'mib_srl';
                if(!$GLOBALS['XE_NMS_MIB_LIST'][$key]) {
                    $oNms = null;
                    $oNms = new nmsItem();
                    $oNms->setAttribute($attribute);

                    $output[$key] = $GLOBALS['XE_NMS_MIB_LIST'][$key] = $oNms;
                }
            }

            // mib_srl 순으로 정렬
            uksort($output, 'strnatcasecmp');

            Context::set('mib_info',$output);
        }

        /**
         * @brief History List 출력
         **/
        function dispNmsHistoryList() {
            $oDocumentModel = &getModel('document');

            $args = Context::gets('group_name','h_page','act');

            $obj->module_srl = $this->module_info->module_srl;
            $obj->search_target = 'title';
            $obj->search_keyword = "History (group name : {$args->group_name})";
            $obj->list_count = 10;
            $obj->page = $args->h_page;
            $output = $oDocumentModel->getDocumentList($obj);

            Context::set('oHistory', $output);

            // 단독 호출시에는 템플릿 파일을 호출하도록 함
            if($args->act == 'dispNmsHistoryList') $this->setTemplateFile('history.list');
        }

        /**
         * @brief History View 출력
         **/
        function dispNmsHistory() {
            $oDocumentModel = &getModel('document');
            $oNmsModel = &getModel('nms');

            $args = Context::gets('module_srl','group_name','document_srl','act');

            $output = $oDocumentModel->getDocument($args->document_srl);

            Context::set('history', $output);

            // 단독 호출시에는 템플릿 파일을 호출하도록 함
            if($args->act == 'dispNmsHistory') {
                $args->module_srl = $this->module_srl;
                $mib_group_info = $oNmsModel->getNmsMibGroupInfo($args);
                Context::set('mib_group_info',$mib_group_info);

                $this->setTemplateFile('history.view');
            }
        }

        /**
         * @brief 모듈 출력 (Severity)
         **/
        function dispNmsContentSeverity() {
            $oNmsModel = &getModel('nms');
            $lang = &$GLOBALS['lang'];
            $args = Context::getRequestVars();

            $args->page = Context::get('page');
            $args->list_count = $this->module_info->list_count;
            $args->page_count = $this->module_info->page_count;
            if($args->search_keyword) $args->list_count = $this->module_info->search_list_count;
            $args->sort_index = (Context::get('sort_index'))?Context::get('sort_index'):$this->module_info->order_target;
            $args->order_type = (Context::get('order_type'))?Context::get('order_type'):$this->module_info->order_type;

            // 템플릿에서 사용할 검색옵션 세팅 (검색옵션 key값은 미리 선언되어 있는데 이에 대한 언어별 변경을 함)
            Context::set('search_option', $lang->nms_severity_search_option);

            $output = $oNmsModel->getNmsSeverityList($args);

            foreach($output->data as $key => $attribute) {
                $severity_srl = $attribute->severity_srl;
                $attribute->type = 'severity_srl';

                if(!$GLOBALS['XE_SEVERITY_LIST'][$key]) {
                    $oSeverity = null;
                    $oSeverity = new nmsItem();
                    $oSeverity->setAttribute($attribute);

                    $seveirty_info[$key] = $GLOBALS['XE_SEVERITY_LIST'][$key] = $oSeverity;
                }
            }

            // 템플릿에 쓰기 위해서 context::set
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('severity_info', $seveirty_info);
            Context::set('page_navigation', $output->page_navigation);

            $this->setTemplateFile('list');
        }

        /**
         * @brief 모듈 출력 (Syslog)
         **/
        function dispNmsContentSyslog() {
            $oNmsModel = &getModel('nms');
            $lang = &$GLOBALS['lang'];
            $args = Context::getRequestVars();

            $args->page = Context::get('page');
            $args->list_count = $this->module_info->list_count;
            $args->page_count = $this->module_info->page_count;
            if($args->search_keyword) $args->list_count = $this->module_info->search_list_count;
            $args->sort_index = (Context::get('sort_index'))?Context::get('sort_index'):$this->module_info->order_target;
            $args->order_type = (Context::get('order_type'))?Context::get('order_type'):$this->module_info->order_type;

            // 템플릿에서 사용할 검색옵션 세팅 (검색옵션 key값은 미리 선언되어 있는데 이에 대한 언어별 변경을 함)
            Context::set('search_option', $lang->nms_syslog_search_option);

            $output = $oNmsModel->getNmsSyslogList($args);

            foreach($output->data as $key => $attribute) {
                $syslog_srl = $attribute->syslog_srl;
                $attribute->type = 'syslog_srl';

                if(!$GLOBALS['XE_SYSLOG_LIST'][$key]) {
                    $oSyslog = null;
                    $oSyslog = new nmsItem();
                    $oSyslog->setAttribute($attribute);

                    $syslog_info[$key] = $GLOBALS['XE_SYSLOG_LIST'][$key] = $oSyslog;
                }
            }

            // 템플릿에 쓰기 위해서 context::set
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('syslog_info', $syslog_info);
            Context::set('page_navigation', $output->page_navigation);

            $this->setTemplateFile('list');
        }

        /**
         * @brief 모듈 출력 (Snmptrap)
         **/
        function dispNmsContentSnmptrap() {
            $oNmsModel = &getModel('nms');
            $lang = &$GLOBALS['lang'];
            $args = Context::getRequestVars();

            $args->page = Context::get('page');
            $args->list_count = $this->module_info->list_count;
            $args->page_count = $this->module_info->page_count;
            if($args->search_keyword) $args->list_count = $this->module_info->search_list_count;
            $args->sort_index = (Context::get('sort_index'))?Context::get('sort_index'):$this->module_info->order_target;
            $args->order_type = (Context::get('order_type'))?Context::get('order_type'):$this->module_info->order_type;

            // 템플릿에서 사용할 검색옵션 세팅 (검색옵션 key값은 미리 선언되어 있는데 이에 대한 언어별 변경을 함)
            Context::set('search_option', $lang->nms_snmptrap_search_option);

            $output = $oNmsModel->getNmsSnmpTrapList($args);

            foreach($output->data as $key => $attribute) {
                $snmptrap_srl = $attribute->snmptrap_srl;
                $attribute->type = 'snmptrap_srl';

                if(!$GLOBALS['XE_SNMPTRAP_LIST'][$key]) {
                    $oSnmpTrap = null;
                    $oSnmpTrap = new nmsItem();
                    $oSnmpTrap->setAttribute($attribute);

                    $snmptrap_info[$key] = $GLOBALS['XE_SNMPTRAP_LIST'][$key] = $oSnmpTrap;
                }
            }

            // 템플릿에 쓰기 위해서 context::set
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('snmptrap_info', $snmptrap_info);
            Context::set('page_navigation', $output->page_navigation);

            $this->setTemplateFile('list');
        }

        /**
         * @brief pChart 그래프 출력
         **/
        function dispNmsGraph($args=null) {
            // 접근 권한 체크
            if(!$this->grant->graph && ($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) && !$args) {
                header('Content-type: image/png');
                @readfile(_XE_PATH_.'modules/nms/libs/pChart/Images/access.gif');
                exit();
            }

            if(!$args) $args = Context::getRequestVars();
            else $GLOBALS['nms_act'] = true;

            $oModuleModel = &getModel('module');

            if(!$args->mid || !$args->graph || !$args->mode || !$args->skin) return;
            if(!$args->mmid && !$args->group_name) return;
            if(!$this->module_srl) {
                $module_info = $oModuleModel->getModuleInfoByMid($args->mid);
                if(!$module_info) return;
                $this->module_srl = $module_info->module_srl;
            }

            switch($args->graph) {
                case 'line': return $this->dispNmsGraphLine($args); break;
                default: break;
            }
        }

        /**
         * @brief pChart 그래프 타입이 line일 경우 pChart 그래프 출력
         **/
        function dispNmsGraphLine($args=null) {
            Context::setRequestMethod('XMLRPC');
            $oNmsModel = &getModel('nms');
            $oNmsController = &getController('nms');

            require_once(_XE_PATH_.'modules/nms/libs/pChart/pData.class');
            require_once(_XE_PATH_.'modules/nms/libs/pChart/pChart.class');
            require_once(_XE_PATH_.'modules/nms/libs/pChart/pChartItem.class');
            require_once(_XE_PATH_.'modules/nms/libs/pChart/pCache.class');
            require_once(_XE_PATH_.'modules/nms/libs/pChart/pCacheItem.class');

            $mKey = $args->mid.$args->graph.$args->mode.$args->group_name.$args->mmid.$args->skin;
            if($args->date) $mKey .= 'customer';

            if(!$args->unit) $args->unit = 'Bps';

            if($args->date) $graph_path = sprintf("%sfiles/attach/nms/%s/customer/%s/", _XE_PATH_, $this->module_srl,md5($mKey));
            elseif($args->group_name) $graph_path = sprintf("%sfiles/attach/nms/%s/group/%s/%s/", _XE_PATH_, $this->module_srl, $args->group_name,md5($mKey));
            elseif($args->mmid) {
                $obj->module_srl = $this->module_srl;
                $obj->mmid = $args->mmid;
                $mib_info = $oNmsModel->getNmsMibInfo($obj);
                if(!$mib_info->data) return;
                $graph_path = sprintf("%sfiles/attach/nms/%s/%s/%s/", _XE_PATH_, $this->module_srl, getNumberingPath($mib_info->data->mib_srl, 3),md5($mKey));
            }

            $font_path = sprintf("%smodules/nms/libs/pChart/Fonts/", _XE_PATH_);

            if(!$args->date) {
                // Graph Cache Get
                if($oNmsModel->getGraphCache(md5($mKey))) {
                    if($GLOBALS['nms_act']) return $graph_path;
                    $graph_png = @FileHandler::readDir($graph_path);
                    foreach($graph_png as $file) {
                       header('Content-type: image/png');
                       @readfile($graph_path.$file);
                       exit();
                    }
                }
            }

            $obj->module_srl = $this->module_srl;

            if($args->group_name) $obj->group_name = $args->group_name;
            elseif($args->mmid) $obj->mmid = $args->mmid;

            $output = $oNmsModel->getNmsMib($obj);
            if(!$output) return;

            foreach($output as $key => $attribute) {
                $mib_srl = $attribute->mib_srl;
                $attribute->type = 'mib_srl';
                if(!$GLOBALS['XE_NMS_MIB_LIST'][$key]) {
                    $oNms = null;
                    $oNms = new nmsItem();
                    $oNms->setAttribute($attribute);

                    $output[$key] = $GLOBALS['XE_NMS_MIB_LIST'][$key] = $oNms;
                }
            }
            unset($GLOBALS['XE_NMS_MIB_LIST']);

            foreach($output as $key => $val) {
                $oSnmp = $val->getSnmpSummary($args->mode,null,$args->date);

                if($oSnmp->error) $error = $oSnmp->error;
                if(count($oSnmp->data) <= 0) continue;

                unset($regdate);

                foreach($oSnmp->data as $no => $snmp) {
                    $Serie[$key][] = $snmp->value;
                    if($snmp->value != null) $Serie_count[$key]++;
                    $regdate[] = $snmp->regdate;
                    if($snmp->delay) $delay[$no] = $snmp->delay;
                }

                $extra_vars = unserialize($val->get('extra_vars'));
                $legend_name[] = $extra_vars->legend_name;
                $stats[] = $oSnmp;
            }

            // Graph Cache Insert
            $mib_group_info = $oNmsModel->getNmsMib($obj);
            foreach($mib_group_info as $key => $val) {
                $val->mkey = md5($mKey);
                $oNmsController->insertGraphCache($val);
            }

            $DataSet = new pData;

            foreach($Serie as $key => $val) {
                $DataSet->AddPoint($val,'Serie'.$key);
                $DataSet->AddSerie('Serie'.$key);
            }

            $DataSet->AddPoint($regdate,'Abscise'.$i);
            $DataSet->SetAbsciseLabelSerie('Abscise'.$i);

            if(in_array(strtolower($args->unit),array('bps','cps','byte'))) $Yformat = 'metric';
            elseif(in_array(strtolower($args->unit),array('kbyte'))) $Yformat = 'kbyte';
            elseif(in_array(strtolower($args->unit),array('mbyte'))) $Yformat = 'mbyte';
            elseif(in_array(strtolower($args->unit),array('gbyte'))) $Yformat = 'gbyte';
            elseif(in_array(strtolower($args->unit),array('%'))) $Yformat = 'number';
            else $Yformat = 'number';

            $DataSet->SetYAxisFormat($Yformat);
            $DataSet->SetXAxisFormat('date');

            krsort($DataSet->Data);
            krsort($DataSet->DataDescription);

            $Cache = new pCacheItem($graph_path);

            $Cache->GetFromCache('pChart',$DataSet->GetData());

            include(_XE_PATH_.'modules/nms/libs/pChart/Skins/'.$args->skin.'.class');

            $Cache->ClearCache();
            $Cache->WriteToCache('pChart',$DataSet->GetData(),$oNms);

            return $graph_path;
        }

        /**
         * @brief 메세지 출력
         **/
        function dispNmsMessage($msg_code) {
            $msg = Context::getLang($msg_code);
            if(!$msg) $msg = $msg_code;
            Context::set('message', $msg);
            $this->setTemplateFile('message');
        }

        /**
         * @brief Twitter OAuth 인증
         **/
        function triggerDispNmsMemberDisplay(&$obj) {
            $oModuleModel = &getModel('module');
            $module_config = $oModuleModel->getModuleConfig('nms');
            if(!$module_config->twitter_config->consumer_key||!$module_config->twitter_config->consumer_secret) return;

            if(!in_array(Context::get('act'), array('dispMemberAdminInsert','dispMemberModifyInfo','getNmsTwitterOauth'))) return;
            if(Context::get('act')=='dispMemberAdminInsert' && !Context::get('member_srl')) return;

            Context::addCssFile($this->module_path.'tpl/css/twitter.css', false);
            Context::addJsFile($this->module_path.'tpl/js/twitter.js', false);

            $scriptCode = '
            <script type="text/javascript">//<![CDATA[
            nms_twitter.extra_var_name = \''.$module_config->twitter_config->extra_var_name.'\';
            //]]></script>';
            Context::addHTMLFooter($scriptCode);
        }
    }
?>