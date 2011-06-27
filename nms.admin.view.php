<?php
    /**
     * @class  nmsAdminView
     * @author largeden (developer@nmsxe.com)
     * @brief  nmsXE Admin View class
     **/

    class nmsAdminView extends nms {

        /**
         * @brief 초기화
         **/
        function init() {
            // module model 객체 생성
            $oModuleModel = &getModel('module');
            $oNmsModel = &getModel('nms');

            $module_srl = Context::get('module_srl');
            $mib_srl = Context::get('mib_srl');

            if(!$module_srl && $this->module_srl) {
                $module_srl = $this->module_srl;
                Context::set('module_srl', $module_srl);
            }

            if(!$mib_srl && $this->mib_srl) {
                $mib_srl = $this->mib_srl;
                Context::set('mib_srl', $mib_srl);
            }

            // module_srl이 넘어오면 해당 모듈의 정보를 미리 구해 놓음
            if($module_srl) {
                $nms_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
                if(!$nms_info) Context::set('module_srl','');
                else {
                    $host_info = $oNmsModel->getNmsHostInfo($module_srl);
                    foreach($host_info as $key => $val) $nms_info->{$key} = $val;

                    Context::set('nms_info',$nms_info);
                }
            }

            if($nms_info && $nms_info->module != 'nms') return $this->stop("msg_invalid_request");

            $group_srl = Context::get('group_srl');
            if(!$group_srl && $this->group_srl) {
                $group_srl = $this->group_srl;
                Context::set('group_srl', $group_srl);
            }

            if($group_srl) {
                $group_info = $oNmsModel->getNmsGroupInfo($group_srl);
                if(!$group_info) Context::set('group_srl','');
                else Context::set('group_info',$group_info);
            }

            $oNms = $oModuleModel->getModuleConfig('nms');
            $oNms->moduleConfig->schedule = explode("|@|",$oNms->moduleConfig->schedule);
            foreach($oNms->moduleConfig->schedule as $key => $val) {
                $oNms->moduleConfig->scheduleclass[$val] = $val;
            }
            unset($oNms->moduleConfig->schedule);
            Context::set('schedule',$oNms->moduleConfig->scheduleclass);
            Context::set('module_config',$oNms);

            $template_path = sprintf("%stpl/",$this->module_path);
            $this->setTemplatePath($template_path);
        }

        /**
         * @brief Host 목록 출력
         **/
        function dispNmsAdminIndex() {
            // 추가 설정을 위한 트리거 호출
            $output = ModuleHandler::triggerCall('nms.dispNmsAdminIndex', 'before', $befor_content);
            $output = ModuleHandler::triggerCall('nms.dispNmsAdminIndex', 'after', $after_content);

            Context::set('befor_content', $befor_content);
            Context::set('after_content', $after_content);

            /* 버전 업데이트가 되면 해당일을 알림 */
            $config = Context::get('module_config');
            $time = date('Ymd', strtotime(sprintf('-%d days', 7)));
            if($config->check_date <= $time) {
                if($this->checkVersion()) Context::set('new_version',true);
                $oModuleController = &getController('module');
                $config->check_date = date('Ymd');
                $oModuleController->insertModuleConfig('nms', $config);
            }

            // 템플릿 파일 지정
            $this->setTemplateFile('list');
        }

        /**
         * @brief 기본 설정 출력
         **/
        function dispNmsAdminConfig() {
            $oModuleModel = &getModel('module');
            $oNms = $oModuleModel->getModuleConfig('nms');

            Context::set('nms_info', $oNms->moduleConfig);

            $output = ModuleHandler::triggerCall('nms.dispNmsConfig', 'after', $after_content);
            Context::set('after_content', $after_content);

            $this->setTemplateFile('config');
        }

        /**
         * @brief Host 정보 출력
         **/
        function dispNmsAdminHostInfo() {
            $this->dispNmsAdminInsertHost();
        }

        /**
         * @brief Host 정보 등록 화면
         **/
        function dispNmsAdminInsertHost() {
            if(!in_array($this->module_info->module, array('admin','nms'))) return $this->alertMessage("msg_invalid_request");

            $oModuleModel = &getModel('module');
            $oLayoutMode = &getModel('layout');
            $oNmsModel = &getModel('nms');
            $lang = &$GLOBALS['lang'];

            // 모듈 카테고리 목록을 구함
            $module_category = $oModuleModel->getModuleCategories();
            Context::set('module_category', $module_category);

            /* 스킨 목록 구하기  */
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list',$skin_list);

            // 레이아웃 목록을 구해옴
            $layout_list = $oLayoutMode->getLayoutList();
            Context::set('layout_list', $layout_list);

            if(!$this->group_srl) {
                $args->list_count = 1000;
                $group_info = $oNmsModel->getNmsGroupList($args);
                Context::set('group_info',$group_info->data);
            }

            // 정렬 옵션을 세팅
            Context::set('order_target', $lang->nms_host_order_target);

            // 추가 설정을 위한 트리거 호출
            if(Context::get('module_srl')) {
                $output = ModuleHandler::triggerCall('nms.dispNmsHostInfo', 'before', $befor_content);
                $output = ModuleHandler::triggerCall('nms.dispNmsHostInfo', 'after', $after_content);
            }

            Context::set('befor_content', $befor_content);
            Context::set('after_content', $after_content);

            $this->setTemplateFile('host_insert');
        }

        /**
         * @brief Group 목록 출력
         **/
        function dispNmsAdminGroupList(){
            $oNmsModel = &getModel('nms');

            // 등록된 nms 모듈을 불러와 세팅
            $args->page = Context::get('page');
            $output = $oNmsModel->getNmsGroupList($args);

            // 템플릿에 쓰기 위해서 context::set
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('group_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

            // 템플릿 파일 지정
            $this->setTemplateFile('group_list');

        }

        /**
         * @brief Group 정보 출력
         **/
        function dispNmsAdminGroupInfo() {
            $this->dispNmsAdminInsertGroup();
        }

        /**
         * @brief Group 등록 정보 출력
         **/
        function dispNmsAdminInsertGroup() {
            if(!in_array($this->module_info->module, array('admin','nms'))) {
                return $this->alertMessage("msg_invalid_request");
            }

            $this->setTemplateFile('group_insert');
        }

        /**
         * @brief Host 삭제 정보 출력
         **/
        function dispNmsAdminDeleteHost() {
            $this->setTemplateFile('host_delete');
        }

        /**
         * @brief Group 삭제 정보 출력
         **/
        function dispNmsAdminDeleteGroup() {
            $this->setTemplateFile('group_delete');
        }

        /**
         * @brief MIB 목록 출력
         **/
        function dispNmsAdminMibList() {
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();

            // 등록된 nms 모듈을 불러와 세팅
            $args->sort_index = (Context::get('sort_index'))?Context::get('sort_index'):'mib_srl';
            $args->order_type = (Context::get('order_type'))?Context::get('order_type'):'desc';
            $args->page = Context::get('page_mib');

            $output = $oNmsModel->getNmsMibList($args);

            // 템플릿에 쓰기 위해서 context::set
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page_mib', $output->page);
            Context::set('mib_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

            $this->setTemplateFile('mib_list');
        }

        /**
         * @brief MIB 정보 출력
         **/
        function dispNmsAdminMibInfo() {
            $this->dispNmsAdminInsertMib();
        }

        /**
         * @brief MIB 등록 화면
         **/
        function dispNmsAdminInsertMib() {
            $oNmsAdminModel = &getAdminModel('nms');
            $oNmsModel = &getModel('nms');
            $mib_srl = Context::get('mib_srl');

            $args = Context::getRequestVars();
            if($args->mib_srl) {
                $output = $oNmsModel->getNmsMibInfo($args);
                if(!$output->toBool()) return $this->stop("msg_invalid_request");
            }

            $output->data->extra_vars = unserialize($output->data->extra_vars);

            // complete_act 정보는 document_extra_vars 에서 가져옴
            $args->var_idx = -4;

            $oSeverity = $oNmsModel->getNmsExtraVars($args);

            foreach($oSeverity->data as $key => $val)
                $values->data->value = unserialize($val->value);

            if($values->data->value->act) {
                foreach($values->data->value->act as $acts => $val)
                    $severity_info->act[$acts] = $val;
            }

            foreach(array('value', 'form', 'path') as $type) {
                if($values->data->value->{"act_$type"}) {
                    foreach($values->data->value->{"act_$type"} as $acts => $val)
                        $severity_info->{"act_$type"}[$acts] = $val;
                }
            }

            /* MBrowser 출력 */
            $host_info = $oNmsModel->getNmsHostInfo($output->data->module_srl);
            $host = $host_info->host;
            $community = $host_info->community;

            $wizard_info[$args->step]->mbrowser = $oNmsAdminModel->getMibBrowserInfo();

            $cache_file = sprintf('./files/cache/nms/snmpwalk/%s_%s.cache.php', $host, 'snmpwalkoid');

            // 캐시 수집 대상을 일반(전체)과 enterprises 정보만으로 함
            $object_id[] = "";
            $object_id[] = ".1.3.6.1.4";
            if(!file_exists($cache_file)) $oNmsAdminModel->_snmpwalkCache($host, $community, $object_id, 100000000000000);

            $wizard_info = $wizard_info[$args->step];

            // 템플릿에 쓰기 위해서 context::set
            Context::set('wizard_info', $wizard_info);
            Context::set('mib_info', $output->data);
            Context::set('severity_info', $severity_info);

            // 추가 설정을 위한 트리거 호출
            if(Context::get('module_srl')) {
                $output = ModuleHandler::triggerCall('nms.dispNmsInsertMib', 'before', $befor_content);
                $output = ModuleHandler::triggerCall('nms.dispNmsInsertMib', 'after', $after_content);
            }

            Context::set('befor_content', $befor_content);
            Context::set('after_content', $after_content);

            // 템플릿 파일 지정
            $this->setTemplateFile('mib_insert');
        }

        /**
         * @brief MIB 삭제 정보 출력
         **/
        function dispNmsAdminDeleteMib() {
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();

            $output = $oNmsModel->getNmsMibInfo($args);
            if(!$output->toBool()) return $this->stop("msg_invalid_request");

            // 템플릿에 쓰기 위해서 context::set
            Context::set('mib_info', $output->data);

            $this->setTemplateFile('mib_delete');
        }

        /**
         * @brief Service 정보 출력
         **/
        function dispNmsAdminServiceInfo() {
            $this->dispNmsAdminInsertService();
        }

        /**
         * @brief Service 정보 등록
         **/
        function dispNmsAdminInsertService() {
            if(!in_array($this->module_info->module, array('admin','nms')))
                return $this->alertMessage("msg_invalid_request");

            $oModuleModel = &getModel('module');
            $oLayoutMode = &getModel('layout');
            $oNmsModel = &getModel('nms');
            $lang = &$GLOBALS['lang'];

            $nms_info = Context::get('nms_info');
            $module_srl = Context::get('module_srl');

            $obj = $oModuleModel->getModuleConfig('nms');

            if($module_srl) {
                if($obj->severity_mid == $nms_info->mid) { $nms_info->service_type = 'severity'; $service_order_target = $lang->nms_severity_order_target; }
                elseif($obj->syslog_mid == $nms_info->mid) { $nms_info->service_type = 'syslog'; $service_order_target = $lang->nms_syslog_order_target; }
                elseif($obj->snmptrap_mid == $nms_info->mid) { $nms_info->service_type = 'snmptrap'; $service_order_target = $lang->nms_snmptrap_order_target; }
            } else {
                if($obj->severity_mid) $nms_info->is_severity = true;
                if($obj->syslog_mid) $nms_info->is_syslog = true;
                if($obj->snmptrap_mid) $nms_info->is_snmptrap = true;
            }
            Context::set('nms_info',$nms_info);

            // 모듈 카테고리 목록을 구함
            $module_category = $oModuleModel->getModuleCategories();
            Context::set('module_category', $module_category);

            /* 스킨 목록 구하기  */
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list',$skin_list);

            // 레이아웃 목록을 구해옴
            $layout_list = $oLayoutMode->getLayoutList();
            Context::set('layout_list', $layout_list);

            // 정렬 옵션을 세팅
            Context::set('order_target', $service_order_target);

            // 추가 설정을 위한 트리거 호출
            if(Context::get('module_srl')) {
                $output = ModuleHandler::triggerCall('nms.dispNmsServiceInfo', 'before', $befor_content);
                $output = ModuleHandler::triggerCall('nms.dispNmsServiceInfo', 'after', $after_content);
            }

            Context::set('befor_content', $befor_content);
            Context::set('after_content', $after_content);

            $this->setTemplateFile('service_insert');
        }

        /**
         * @brief Syslog 목록 출력
         **/
        function dispNmsAdminSyslogLog() {
            $oNmsModel = &getModel('nms');

            $module_info = Context::get('nms_info');

            $args = Context::getRequestVars();

            $args->page = Context::get('page');
            $args->list_count = $module_info->list_count;
            $args->page_count = $module_info->page_count;
            if($args->search_keyword) $args->list_count = $module_info->search_list_count;
            $args->sort_index = (Context::get('sort_index'))?Context::get('sort_index'):$module_info->order_target;
            $args->order_type = (Context::get('order_type'))?Context::get('order_type'):$module_info->order_type;

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

            // 추가 설정을 위한 트리거 호출
            $output = ModuleHandler::triggerCall('nms.dispNmsSyslogLog', 'before', $befor_content);
            $output = ModuleHandler::triggerCall('nms.dispNmsSyslogLog', 'after', $after_content);

            Context::set('befor_content', $befor_content);
            Context::set('after_content', $after_content);

            $this->setTemplateFile('syslog_log');
        }

        /**
         * @brief Snmp Trap 목록 출력
         **/
        function dispNmsAdminSnmpTrapLog() {
            $oNmsModel = &getModel('nms');
            $lang = &$GLOBALS['lang'];

            $module_info = Context::get('nms_info');

            $args = Context::getRequestVars();

            $args->page = Context::get('page');
            $args->list_count = $module_info->list_count;
            $args->page_count = $module_info->page_count;
            if($args->search_keyword) $args->list_count = $module_info->search_list_count;
            $args->sort_index = (Context::get('sort_index'))?Context::get('sort_index'):$module_info->order_target;
            $args->order_type = (Context::get('order_type'))?Context::get('order_type'):$module_info->order_type;

            $output = $oNmsModel->getNmsSnmpTrapList($args);

            foreach($output->data as $key => $attribute) {
                $snmptrap_srl = $attribute->snmptrap_srl;
                $attribute->type = 'snmptrap_srl';

                if(!$GLOBALS['XE_SNMPTRAP_LIST'][$key]) {
                    $oSnmptrap = null;
                    $oSnmptrap = new nmsItem();
                    $oSnmptrap->setAttribute($attribute);

                    $snmptrap_info[$key] = $GLOBALS['XE_SNMPTRAP_LIST'][$key] = $oSnmptrap;
                }
            }

            // 템플릿에 쓰기 위해서 context::set
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('snmptrap_info', $snmptrap_info);
            Context::set('page_navigation', $output->page_navigation);

            // 추가 설정을 위한 트리거 호출
            $output = ModuleHandler::triggerCall('nms.dispNmsSnmpTrapLog', 'before', $befor_content);
            $output = ModuleHandler::triggerCall('nms.dispNmsSnmpTrapLog', 'after', $after_content);

            Context::set('befor_content', $befor_content);
            Context::set('after_content', $after_content);

            $this->setTemplateFile('snmptrap_log');
        }

        /**
         * @brief Severity Level 출력
         **/
        function dispNmsAdminSeverityLog() {
            $oModuleModel = &getModel('module');
            $oNmsModel = &getModel('nms');
            $lang = &$GLOBALS['lang'];
            $oNms = $oModuleModel->getModuleConfig('nms');

            $module_info = Context::get('nms_info');

            $args = Context::getRequestVars();

            $args->page = Context::get('page');
            $args->list_count = $module_info->list_count;
            $args->page_count = $module_info->page_count;
            if($args->search_keyword) $args->list_count = $module_info->search_list_count;
            $args->sort_index = (Context::get('sort_index'))?Context::get('sort_index'):$module_info->order_target;
            $args->order_type = (Context::get('order_type'))?Context::get('order_type'):$module_info->order_type;

            $output = $oNmsModel->getNmsSeverityList($args);

            foreach($output->data as $key => $attribute) {
                $severity_srl = $attribute->severity_srl;
                $attribute->type = 'severity_srl';

                if(!$GLOBALS['XE_SEVERITY_LIST'][$key]) {
                    $oSeverity = null;
                    $oSeverity = new nmsItem();
                    $oSeverity->setAttribute($attribute);

                    $severity_info[$key] = $GLOBALS['XE_SEVERITY_LIST'][$key] = $oSeverity;
                }
            }

            // 템플릿에 쓰기 위해서 context::set
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('severity_info', $severity_info);
            Context::set('page_navigation', $output->page_navigation);
            // 템플릿에서 사용할 검색옵션 세팅 (검색옵션 key값은 미리 선언되어 있는데 이에 대한 언어별 변경을 함)
            Context::set('search_option', $lang->nms_severity_search_option);

            // 추가 설정을 위한 트리거 호출
            $output = ModuleHandler::triggerCall('nms.dispNmsSeverityLog', 'before', $befor_content);
            $output = ModuleHandler::triggerCall('nms.dispNmsSeverityLog', 'after', $after_content);

            Context::set('befor_content', $befor_content);
            Context::set('after_content', $after_content);

            $this->setTemplateFile('severity_log');
        }

        /**
         * @brief Severity 설정 화면 출력
         **/
        function dispNmsAdminSeverityList() {
            $oModuleModel = &getModel('module');
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();

            // 등록된 nms 모듈을 불러와 세팅
            $args->sort_index = "document_extra_vars.module_srl, document_extra_vars.document_srl, document_extra_vars.var_idx";
            $args->not_var_idx = "-3,-4,-5";

            $args->page = Context::get('page_sl');
            $args->list_count = 10;
            $args->page_count = 10;

            $output = $oNmsModel->getNmsExtraVars($args);

            foreach($output->data as $key => $attribute) {
                $attribute->mib_srl = $attribute->document_srl;
                $oMib = $oNmsModel->getNmsMibInfo($attribute);
                if($oMib->data->mmid) $attribute->mmid = $oMib->data->mmid;
                else {
                    $nms_info = $oModuleModel->getModuleInfoByModuleSrl($attribute->module_srl);
                    $attribute->mmid = $nms_info->mid;
                }
                $module_srl = $attribute->module_srl;
                $attribute->type = 'module_srl';

                if(!$GLOBALS['XE_NMS_LIST'][$key]) {
                    $oNms = null;
                    $oNms = new nmsItem();
                    $oNms->setAttribute($attribute);

                    $severity_info[$key] = $GLOBALS['XE_NMS_LIST'][$key] = $oNms;
                }
            }

            // 템플릿에 쓰기 위해서 context::set
            Context::set('total_count_sl', $output->total_count);
            Context::set('total_page_sl', $output->total_page);
            Context::set('page_sl', $output->page);
            Context::set('severity_info', $severity_info);
            Context::set('page_navigation_sl', $output->page_navigation);

            // 추가 설정을 위한 트리거 호출
            $output = ModuleHandler::triggerCall('nms.dispNmsSeverityList', 'before', $befor_content);
            $output = ModuleHandler::triggerCall('nms.dispNmsSeverityList', 'after', $after_content);

            Context::set('befor_content', $befor_content);
            Context::set('after_content', $after_content);

            $this->setTemplateFile('severity_list');
        }

        /**
         * @brief Severity 정보 출력
         **/
        function dispNmsAdminInsertRestore() {
            $oModuleModel = &getModel('module');
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();

            if(!$args->mib_srl) {
                $args->sort_index = "module_srl,mib_srl";
                $output = $oNmsModel->getNmsMib($args);

                foreach($output as $key => $attribute) {
                    $oMib = $oNmsModel->getNmsMibInfo($attribute);
                    $attribute->module_srl = $oMib->data->module_srl;
                    $nms_info = $oModuleModel->getModuleInfoByModuleSrl($attribute->module_srl);
                    $attribute->mid = $nms_info->mid;
                    $module_srl = $attribute->module_srl;
                    $attribute->type = 'module_srl';

                    if(!$GLOBALS['XE_NMS_LIST'][$key]) {
                        $oNms = null;
                        $oNms = new nmsItem();
                        $oNms->setAttribute($attribute);

                        $mib_info[$key] = $GLOBALS['XE_NMS_LIST'][$key] = $oNms;
                    }
                }

                Context::set('mib_info', $mib_info);
            }

            $this->setTemplateFile('trigger_severity_restore');
        }

        /**
         * @brief Severity 정보 출력
         **/
        function dispNmsAdminSeverityInfo() {
            $this->dispNmsAdminInsertSeverity();
        }

        /**
         * @brief Severity Syslog 정보 출력
         **/
        function dispNmsAdminSeverityInfoSyslog() {
            $this->dispNmsAdminInsertSeveritySyslog();
        }

        /**
         * @brief Severity SnmpTrap 정보 출력
         **/
        function dispNmsAdminSeverityInfoSnmpTrap() {
            $this->dispNmsAdminInsertSeveritySnmpTrap();
        }

        /**
         * @brief Severity 등록 페이지 출력
         **/
        function dispNmsAdminInsertSeverity() {
            $oModuleModel = &getModel('module');
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();

            $args->document_srl = $args->mib_srl;
            $args->var_idx = $args->severity;

            if($args->mib_srl) {
                $output = $oNmsModel->getNmsExtraVars($args);
                foreach($output->data as $key => $vals) {
                    $values = unserialize($vals->value);

                    $severity_info->module_srl = $vals->module_srl;
                    $severity_info->mib_srl = $vals->document_srl;
                    $severity_info->severity = $vals->var_idx;
                    $severity_info->type = $values->type;
                    $severity_info->value = $values->value;
                    $severity_info->event = $values->event;

                    if($values->act) {
                        foreach($values->act as $acts => $val)
                            $severity_info->act[$acts] = $val;
                    }

                    foreach(array('value', 'form', 'path') as $type) {
                        if($values->{"act_$type"}) {
                            foreach($values->{"act_$type"} as $acts => $val)
                                $severity_info->{"act_$type"}[$acts] = $val;
                        }
                    }
                }
            } else {
                $args->sort_index = "module_srl,mib_srl";
                $output = $oNmsModel->getNmsMib($args);

                $module_config = $oModuleModel->getModuleConfig('nms');
                $nms_info = $oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
                if(in_array($nms_info->mid, array($module_config->syslog_mid,$module_config->snmptrap->mid))) $output[$args->module_srl]->module_srl = $args->module_srl;

                foreach($output as $key => $attribute) {
                    $nms_info = $oModuleModel->getModuleInfoByModuleSrl($attribute->module_srl);
                    $module_srl = $attribute->module_srl;
                    $attribute->mid = $nms_info->mid;
                    if(!$attribute->mmid) $attribute->mmid = $nms_info->mid;
                    $attribute->type = 'module_srl';
                    if(!$GLOBALS['XE_NMS_LIST'][$key]) {
                        $oNms = null;
                        $oNms = new nmsItem();
                        $oNms->setAttribute($attribute);

                        $mib_info[$key] = $GLOBALS['XE_NMS_LIST'][$key] = $oNms;
                    }
                }

                Context::set('mib_info', $mib_info);
            }

            Context::set('severity_info', $severity_info);

            $this->setTemplateFile('severity_insert');
        }

        /**
         * @brief Severity 삭제 페이지 출력
         **/
        function dispNmsAdminDeleteSeverity() {
            $oModuleModel = &getModel('module');
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();

            $args->document_srl = $args->mib_srl;
            $args->var_idx = $args->severity;

            $output = $oNmsModel->getNmsExtraVars($args);

            foreach($output->data as $key => $attribute) {
                $attribute->mib_srl = $args->document_srl;
                $nms_info = $oModuleModel->getModuleInfoByModuleSrl($attribute->module_srl);
                $oMib = $oNmsModel->getNmsMibInfo($attribute);
                if($oMib) $attribute->mmid = $oMib->data->mmid;
                else $attribute->mmid = $nms_info->mid;
                $attribute->mid = $nms_info->mid;
                $module_srl = $attribute->module_srl;
                $attribute->type = 'module_srl';
                if(!$GLOBALS['XE_NMS_LIST'][$key]) {
                    $oNms = null;
                    $oNms = new nmsItem();
                    $oNms->setAttribute($attribute);

                    $output->data = $GLOBALS['XE_NMS_LIST'][$key] = $oNms;
                }
            }

            Context::set('severity_info', $output->data);

            $this->setTemplateFile('severity_delete');
        }

        /**
         * @brief 권한 목록 출력
         **/
        function dispNmsAdminGrantInfo() {
            $args = Context::getRequestVars();

            // 공통 모듈 권한 설정 페이지 호출
            $oModuleAdminModel = &getAdminModel('module');

            $grant_content = $oModuleAdminModel->getModuleGrantHTML($args->module_srl, $this->xml_info->grant);
            Context::set('grant_content', $grant_content);

            $this->setTemplateFile('grant_list');
        }

        /**
         * @brief 스킨 목록 출력
         **/
        function dispNmsAdminSkinInfo() {

            $args = Context::getRequestVars();

            // 공통 모듈 스킨 설정 페이지 호출
            $oModuleAdminModel = &getAdminModel('module');
            $skin_content = $oModuleAdminModel->getModuleSkinHTML($args->module_srl);
            Context::set('skin_content', $skin_content);

            $this->setTemplateFile('skin_info');
        }

        /**
         * @brief member 조회 목록 출력
         **/
        function dispNmsAdminMember() {
            $oMemberModel = &getModel('member');

            // retrieve group list
            $group_list = $oMemberModel->getGroups();
            Context::set('group_list', $group_list);

            $oMemberAdminModel = &getAdminModel('member');
            $oMemberModel = &getModel('member');
            $output = $oMemberAdminModel->getMemberList();
            if(!$output->data) $output->data = array();

            // retrieve list of groups for each member
            foreach($output->data as $key => $member) {
                $output->data[$key]->group_list = $oMemberModel->getMemberGroups($member->member_srl,0);
            }

            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('member_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

            if(Context::get('mode') == 'search') {
                if($output->data) $this->add('selected_group_srl', Context::get('selected_group_srl'));
                $this->add('total_count', $output->total_count);
                $this->add('total_page', $output->total_page);
                $this->add('page', $output->page);
                $this->add('member_list', $output->data);
                $this->add('page_navigation', $output->page_navigation);

                while($page_no = $output->page_navigation->getNextPage()) $member_navigation[] = $page_no;

                $this->add('member_navigation', $member_navigation);

                return;
            }

            // 레이아웃을 팝업으로 지정
            $this->setLayoutFile('popup_layout');

            // 템플릿 파일 지정
            $this->setTemplateFile('member');
        }

        /**
         * @brief 설정 마법사 페이지 출력
         **/
        function dispNmsAdminSettingWizard() {
            $oNmsAdminModel = &getAdminModel('nms');
            $oModuleModel = &getModel('module');
            $oLayoutMode = &getModel('layout');
            $oNmsModel = &getModel('nms');
            $lang = &$GLOBALS['lang'];

            $args = Context::getRequestVars();

            if(!$args->step) $args->step = 1;

            switch($args->step) {
                case 1:
                    // 모듈 카테고리 목록을 구함
                    $module_category = $oModuleModel->getModuleCategories();
                    Context::set('module_category', $module_category);

                    /* 스킨 목록 구하기  */
                    $skin_list = $oModuleModel->getSkins($this->module_path);
                    Context::set('skin_list',$skin_list);

                    // 레이아웃 목록을 구해옴
                    $layout_list = $oLayoutMode->getLayoutList();
                    Context::set('layout_list', $layout_list);

                    if(!$this->group_srl) {
                        $group_info = $oNmsModel->getNmsGroup();
                        if(!$group_info) Context::set('group_srl','');
                        else Context::set('group_info',$group_info);
                    }

                    // 정렬 옵션을 세팅
                    Context::set('order_target', $lang->nms_host_order_target);

                    $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', $args->step);

                    if(file_exists($cache_file)) {
                        @include($cache_file);
                        Context::set('nms_info', $wizard_info[$args->step]);
                    }
                break;
                case 2:
                    $path = _XE_PATH_.'modules/nms/';
                    $wizard_path = $path.'tpl/wizard/';
                    $output = @FileHandler::readDir($wizard_path);
                    foreach($output as $key => $val) $wizard_info[] = $oNmsAdminModel->getWizardInfo($val);

                    $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', $args->step);

                    if(file_exists($cache_file)) {
                        @include($cache_file);
                        Context::set('nms_info', $wizard_info[$args->step]);
                    }
                break;
                case 3:
                    $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', 1);

                    if(file_exists($cache_file)) @include($cache_file);

                    $host = $wizard_info[1]->host;
                    $community = $wizard_info[1]->community;

                    $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', 2);

                    if(file_exists($cache_file)) @include($cache_file);

                    $mib_info = $oNmsAdminModel->getWizardInfo($wizard_info[$args->step-1]->wizard_file.".xml");

                    /* MBrowser 출력 */
                    if($mib_info->mode) {
                        $wizard_info[$args->step]->mode = $mib_info->mode;
                        $wizard_info[$args->step]->mbrowser = $oNmsAdminModel->getMibBrowserInfo();
                        $cache_file = sprintf('./files/cache/nms/snmpwalk/%s.cache.php', 'snmpwalkoid');

                        $object_id[] = "";
                        $object_id[] = ".1.3.6.1.4";
                        if(!file_exists($cache_file)) $oNmsAdminModel->_snmpwalkCache($host, $community, $object_id);
                    }

                    $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', 3);

                    $z=0;
                    if(file_exists($cache_file)) {
                        @include($cache_file);

                        if(!$wizard_info[$args->step]->mibs) $wizard_info[$args->step]->mibs = array();
                        foreach($wizard_info[$args->step]->mibs as $key => $val) {
                            if($val->type == 'MBrowser') $wizard_mib_cache->{$key} = $val;
                            $z++;
                        }

                        unset($wizard_info[$args->step]->mibs);
                        $wizard_info[$args->step]->mibs = $wizard_mib_cache;
                    }

                    // sysDescr 정보를 확인해서 값이 안나오면 snmp 동작을 하지 않는걸로 판단함
                    $snmp = $oNmsModel->_snmpget($host, $community, ".1.3.6.1.2.1.1.1.0");

                    // 설치 마법사 xml 파일에 mibs 그룹 정보를 불러와 출력
                    if(!$mib_info->mibs) $mib_info->mibs = array();
                    foreach($mib_info->mibs as $mibs) {
                        $wizard_mib = $mibs;
                        $wizard_mib->type = 'mibs';
                        if(preg_match("/^\.?[0-9]+([\.0-9]+)?[0-9]$/", $wizard_mib->mib_title))
                            $wizard_mib->mib_title = (!$snmp||$snmp=='NULL')?'NULL':preg_replace("/\"/", "", $oNmsModel->hexStr2Ascii($oNmsModel->_snmpget($host, $community, $wizard_mib->mib_title)));

                        $wizard_mib->mib_value = (!$snmp||$snmp=='NULL')?'NULL':preg_replace("/\"/", "", $oNmsModel->_snmpget($host, $community, $wizard_mib->mib));

                        if(preg_match("/^[-]?[0-9]+([\.][0-9]+)?$/", $wizard_mib->max)) $wizard_mib->max_value = $wizard_mib->max;
                        else $wizard_mib->max_value = (!$snmp||$snmp=='NULL')?'NULL':preg_replace("/\"/", "", $oNmsModel->_snmpget($host, $community, $wizard_mib->max));

                        $wizard_mib->mib = preg_replace("/^\./", "", $wizard_mib->mib);
                        $wizard_mib->max = (!$snmp||$snmp=='NULL')?0:preg_replace("/^\./", "", $wizard_mib->max);

                        $wizard_info[$args->step]->mibs->{$wizard_mib->mmid} = $wizard_mib;
                        unset($wizard_mib);
                        $z++;
                    }

                    // 설치 마법사 xml 파일에 mib_groups 그룹 정보를 불러와 출력
                    if(!$mib_info->mib_groups) $mib_info->mib_groups = array();
                    foreach($mib_info->mib_groups as $mib_groups) {
                        $wizard_mib = $mib_groups;

                        $groups = (!$snmp||$snmp=='NULL')?array('NULL' => 'NULL'):$oNmsModel->_snmpwalkoid($host, $community, $wizard_mib->mib);

                        $i=1;
                        foreach($groups as $oid => $val) {
                            $wizard_mib_groups->type = 'mib_groups';
                            $wizard_mib_groups->mmid = $wizard_mib->mmid.$i;
                            $wizard_mib_groups->mib_title = $wizard_mib->mib_title.$i;
                            $wizard_mib_groups->mib = preg_replace("/^\./", "", $oid);
                            $wizard_mib_groups->mib_value = preg_replace("/\"/", "", $val);
                            $wizard_mib_groups->description = $wizard_mib->description;
                            $wizard_mib_groups->legend = $wizard_mib->legend;
                            $wizard_mib_groups->collect = $wizard_mib->collect;
                            $wizard_mib_groups->group_name = $i;
                            $wizard_mib_groups->max = $wizard_mib->max;
                            $wizard_mib_groups->max_value = $wizard_mib->max;

                            $wizard_info[$args->step]->mib_groups->{$wizard_mib->mmid.$i} = $wizard_mib_groups;

                            $i++;
                            unset($wizard_mib_groups);
                            $z++;
                        }

                        if(preg_match("/^\.?[0-9]+([\.0-9]+)?[0-9]$/", $wizard_mib->mib_title)) {
                            $groups_mib_title = (!$snmp||$snmp=='NULL')?array('NULL' => 'NULL'):$oNmsModel->_snmpwalkoid($host, $community, $wizard_mib->mib_title);

                            $i=1;
                            foreach($groups_mib_title as $oid => $val) {
                                $wizard_info[$args->step]->mib_groups->{$wizard_mib->mmid.$i}->mib_title = preg_replace("/\"/", "", $oNmsModel->hexStr2Ascii($val));

                                $i++;
                            }
                        }

                        if(!preg_match("/^[-]?[0-9]+([\.][0-9]+)?$/", $wizard_mib->max)) {
                            $groups_max = (!$snmp||$snmp=='NULL')?array('NULL' => 'NULL'):$oNmsModel->_snmpwalkoid($host, $community, $wizard_mib->max);

                            $i=1;
                            foreach($groups_max as $oid => $val) {
                                $wizard_info[$args->step]->mib_groups->{$wizard_mib->mmid.$i}->max = (!$snmp||$snmp=='NULL')?0:preg_replace("/^\./", "", $oid);
                                $wizard_info[$args->step]->mib_groups->{$wizard_mib->mmid.$i}->max_value = preg_replace("/\"/", "", $val);

                                $i++;
                            }
                        }

                        unset($wizard_mib);
                    }

                    $wizard_info[$args->step]->total_count = $z;
                    $wizard_info = $wizard_info[$args->step];
                break;
                case 4:
                     for($i=3; $i>0; $i--) {
                        $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', $i);
                        if(file_exists($cache_file)) @include($cache_file);
                    }

                    // 모듈 카테고리 목록을 구함
                    $module_category = $oModuleModel->getModuleCategories();
                    Context::set('module_category', $module_category);

                    /* 스킨 목록 구하기  */
                    $skin_list = $oModuleModel->getSkins($this->module_path);
                    Context::set('skin_list',$skin_list);

                    // 레이아웃 목록을 구해옴
                    $layout_list = $oLayoutMode->getLayoutList();
                    Context::set('layout_list', $layout_list);

                    if(!$this->group_srl) {
                        $group_info = $oNmsModel->getNmsGroup();
                        if(!$group_info) Context::set('group_srl','');
                        else Context::set('group_info',$group_info);
                    }

                    $total_info->host = $wizard_info[1];
                    $total_info->wizard_file = $wizard_info[2]->wizard_file;
                    $total_info->mibs = $wizard_info[3]->mibs;
                    $total_info->mibs_count = $wizard_info[3]->mibs_count;

                    $wizard_info = $total_info;
                break;
                default: break;
            }

            Context::set('wizard_info', $wizard_info);
            $this->setTemplateFile('wizard');
        }
    }
?>