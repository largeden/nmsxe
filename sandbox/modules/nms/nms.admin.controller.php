<?php
    /**
     * @class  nmsAdminController
     * @author largeden (developer@nmsxe.com)
     * @brief  nmsXE Admin Controller class
     **/

    class nmsAdminController extends nms {

        var $FileHandler;

        /**
         * @brief 초기화
         **/
        function init() {
            if (substr(PHP_OS, 0, 3) == 'WIN') define('PEAR_OS', 'Windows');
            else define('PEAR_OS', 'Unix');

            // event 처리용으로 act 값 load
            $lang = &$GLOBALS['lang'];
            foreach($lang->nms_act as $act => $val)
                $this->acts[] = $act;

            $this->FileHandler = new FileHandler();
        }

        /**
         * @brief Shell 스크립트 처리 함수 (관리자페이지 기본설정에서 소켓을 실행할때 이 함수가 백그라운드 명령 수행을 함)
         **/
        function getShellScript($cmd) {
            if(!$cmd) return;
            $path = _XE_PATH_.'modules/nms/';
            if(PEAR_OS == 'Unix') shell_exec("php -q ".$path."nms.socket.php ".$cmd." > /dev/null &");
            else pclose(popen("start /B ". "php -q ".$path."nms.socket.php ".$cmd, "r"));
        }

        /**
         * @brief 기본설정에서 syslog, snmptrap 수행시 그룹에서 시간 설정 시 sockets 폴더로 해당 Url정보를 만들어 생성 함
         **/
        function procSocketInsert($folder = null, $val = null) {
            if(!$folder || !$val) return;
             $path = _XE_PATH_.'modules/nms/socket/crontab/'.$folder.'/';

            if($val == 'syslog') {
                $socket_url = array('uri'=>Context::getRequestUri(),
                                    'args'=>array(
                                            'module'=>'nms',
                                            'act'=>'procNmsSyslog'
                                        ));
            } elseif($val == 'snmptrap') {
                $socket_url = array('uri'=>Context::getRequestUri(),
                                    'args'=>array(
                                            'module'=>'nms',
                                            'act'=>'procNmsSnmpTrap'
                                        ));
            } elseif($val == 'compress') {
                $socket_url = array('uri'=>Context::getRequestUri(),
                                    'args'=>array(
                                            'module'=>'nms',
                                            'act'=>'procNmsCompress'
                                        ));
            } else {
                $socket_url = array('uri'=>Context::getRequestUri(),
                                    'args'=>array(
                                            'module'=>'nms',
                                            'act'=>'procNmsSnmp',
                                            'group_srl'=>$val
                                        ));
            }

            $this->FileHandler->writeFile($path.$val,serialize($socket_url),'w');
        }

        /**
         * @brief Cron 정보 삭제 함수
         **/
        function procCronDelete($folder = null, $val = null) {
            if(!$folder || !$val) return;
            $path = _XE_PATH_.'modules/nms/socket/crontab/'.$folder.'/';

            if(PEAR_OS == 'Unix') shell_exec("rm -rf ".$path.$val." > /dev/null &");
            else pclose(popen("del ".$path.$val, "r"));
        }

        /**
         * @brief 기본정보 설정
         **/
        function procNmsAdminInsertConfig($args = null) {
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');

            $args = Context::getRequestVars();

            $obj = $oModuleModel->getModuleConfig('nms');
            $path = _XE_PATH_.'modules/nms/socket/';

            /* cron 사용여부에 따른 shell 명령 처리 */
            if($args->crontype=='Y') {
                if($args->crontype == $obj->moduleConfig->crontype) {
                    $this->getShellScript("cron stop");
                    $this->getShellScript("compress stop");
                }

                sleep(1);

                if(preg_match("/\|@\|/",$args->schedule)) {
                    $cron_schedule = explode('|@|',$args->schedule);
                    foreach($cron_schedule as $key => $val) $this->getShellScript("cron ".$val);

                    if($args->compress > 0) {
                        $this->procSocketInsert('compress','compress');
                        $this->getShellScript("compress start");
                    }
                } else {
                    $this->getShellScript("cron ".$args->schedule);

                    if($args->compress > 0) {
                        $this->procSocketInsert('compress','compress');
                        $this->getShellScript("compress start");
                    }
                }
            } elseif ($args->crontype=='N') {
                $this->getShellScript("cron stop");
                $this->getShellScript("compress stop");
            }

            /* syslog 사용여부에 따른 shell 명령 처리 */
            if($args->syslogtype=='Y') {
                $this->procSocketInsert('syslog','syslog');
                if($obj->moduleConfig->syslogtype=='N') $this->getShellScript("syslog start");
                else {
                    $this->getShellScript("syslog stop");
                    sleep(1);
                    $this->getShellScript("syslog start");
                }
            } elseif($args->syslogtype=='N') $this->getShellScript("syslog stop");

            /* snmp trap 사용여부에 따른 shell 명령 처리 */
            if($args->snmptraptype=='Y') {
                $this->procSocketInsert('snmptrap','snmptrap');
                if($obj->moduleConfig->snmptraptype=='N') $this->getShellScript("snmptrap start");
                else {
                    $this->getShellScript("snmptrap stop");
                    sleep(1);
                    $this->getShellScript("snmptrap start");
                }
            } elseif($args->snmptraptype=='N') $this->getShellScript("snmptrap stop");

            $obj->moduleConfig->crontype = $args->crontype;
            $obj->moduleConfig->syslogtype = $args->syslogtype;
            $obj->moduleConfig->snmptraptype = $args->snmptraptype;
            $obj->moduleConfig->schedule = $args->schedule;
            $obj->moduleConfig->compress = $args->compress;
            $obj->moduleConfig->compress_type = $args->compress_type;

            $oModuleController->insertModuleConfig('nms', $obj);

            $this->setMessage("success_saved");
        }

        /**
         * @brief Twitter OAuth 설정
         **/
        function procNmsAdminInsertTwitterConfig($args = null) {
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');

            $args = Context::getRequestVars();

            $oNms = $oModuleModel->getModuleConfig('nms');

            $obj = $oNms;

            $obj->twitter_config->consumer_key = $args->consumer_key;
            $obj->twitter_config->consumer_secret = $args->consumer_secret;
            $obj->twitter_config->extra_var_name = $args->extra_var_name;
            $obj->bitly_config->username = $args->username;
            $obj->bitly_config->apikey = $args->apikey;

            $oModuleController->insertModuleConfig('nms', $obj);

            $this->setMessage("success_saved");
        }

        /**
         * @brief SMTP 서버 설정
         **/
        function procNmsAdminInsertSmtpConfig($args = null) {
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');

            $args = Context::getRequestVars();

            $oNms = $oModuleModel->getModuleConfig('nms');

            $obj = $oNms;

            if(!$args->smtp_server) $args->smtp_server = "127.0.0.1";
            if(!$args->smtp_port) $args->smtp_port = 25;
            $smtp->smtp_server = str_replace(" ","",$args->smtp_server);
            $smtp->smtp_secure = str_replace(" ","",$args->smtp_secure);
            $smtp->smtp_port = str_replace(" ","",$args->smtp_port);
            $smtp->smtp_user = str_replace(" ","",$args->smtp_user);
            $smtp->smtp_pass = ($args->smtp_pass)?str_replace(" ","",$args->smtp_pass):$obj->smtp_config->smtp_pass;
            $obj->smtp_config = $smtp;

            $oModuleController->insertModuleConfig('nms', $obj);

            $this->setMessage('success_saved');
        }

        /**
         * @brief Host 정보 추가
         **/
        function procNmsAdminInsertHost($args = null) {
            // module 모듈의 model/controller 객체 생성
            $oModuleController = &getController('module');
            $oModuleModel = &getModel('module');
            $oNmsController = &getController('nms');
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();
            $args->module = 'nms';
            $args->mid = $args->board_name;
            unset($args->board_name);

            // module_srl이 넘어오면 원 모듈이 있는지 확인
            if($args->module_srl) {
                $module_info = $oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
                if($module_info->module_srl != $args->module_srl) unset($args->module_srl);

                $host_info = $oNmsModel->getNmsHostInfo($args->module_srl);
            }

            // module_srl의 값에 따라 module insert/update
            if(!$args->module_srl) {
                $output = $oModuleController->insertModule($args);
                $msg_code = "success_registed";
            } else {
                $output = $oModuleController->updateModule($args);
                $msg_code = "success_updated";
            }

            if(!$output->toBool()) return $output;

            // module_srl의 값에 따라 host insert/update
            if(!$args->module_srl) {
                $args->module_srl = $output->get('module_srl');
                $output = $oNmsController->insertNmsHost($args);
            } else {
                $args->checkdate = $host_info->checkdate;
                $output = $oNmsController->updateNmsHost($args);
            }

            $this->add('page',Context::get('page'));
            $this->add('module_srl',$output->get('module_srl'));
            $this->add('act','dispNmsAdminHostInfo');
            $this->setMessage($msg_code);
        }

        /**
         * @brief Service 정보 추가
         **/
        function procNmsAdminInsertService($args = null) {
            // module 모듈의 model/controller 객체 생성
            $oModuleController = &getController('module');
            $oModuleModel = &getModel('module');
            $oNmsController = &getController('nms');
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();
            $args->module = 'nms';
            $args->mid = $args->board_name;
            unset($args->board_name);

            // module_srl이 넘어오면 원 모듈이 있는지 확인
            if($args->module_srl) {
                $module_info = $oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
                if($module_info->module_srl != $args->module_srl) unset($args->module_srl);
            }

            // module_srl의 값에 따라 module insert/update
            if(!$args->module_srl) {
                $output = $oModuleController->insertModule($args);
                $msg_code = "success_registed";
                $this->add('act','dispNmsAdminIndex');
            } else {
                $output = $oModuleController->updateModule($args);
                $msg_code = "success_updated";
                $this->add('act','dispNmsAdminServiceInfo');
            }

            if(!$output->toBool()) return $output;

            $obj = $oModuleModel->getModuleConfig('nms');

            switch($args->service_type) {
                case 'severity': $obj->severity_mid = $args->mid; break;
                case 'syslog': $obj->syslog_mid = $args->mid; break;
                case 'snmptrap': $obj->snmptrap_mid = $args->mid; break;
                default: break;
            }

            $oModuleController->insertModuleConfig('nms', $obj);

            $this->add('module_srl',$output->get('module_srl'));
            $this->setMessage($msg_code);
        }

        /**
         * @brief Group 정보 추가
         **/
        function procNmsAdminInsertGroup($args = null) {
            $oNmsController = &getController('nms');
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();

            if($args->group_srl) {
                $group_info = $oNmsModel->getNmsGroupInfo($args->group_srl);
                if($args->schedule != $group_info->schedule) $this->procCronDelete($group_info->schedule,$group_info->group_srl);
            }

            // group_srl의 값에 따라 insert/update
            if(!$args->group_srl) {
                $output = $oNmsController->insertNmsGroup($args);
                $msg_code = "success_registed";
                $this->add('act','dispNmsAdminGroupList');
            } else {
                $args->checkdate = $group_info->checkdate;
                $output = $oNmsController->updateNmsGroup($args);
                $msg_code = "success_updated";
                $this->add('act','dispNmsAdminGroupInfo');
            }

            if(!$output->toBool()) return $output;

            // Cron 정보를 저장하기 위한 처리
            if(!$args->group_srl) $args->group_srl = $output->get('group_srl');
            if($args->cronstate=='Y') $this->procSocketInsert($args->schedule,$args->group_srl);
            elseif($args->cronstate=='N') $this->procCronDelete($args->schedule,$args->group_srl);

            $this->add('page',Context::get('page'));
            $this->add('group_srl',$output->get('group_srl'));
            $this->setMessage($msg_code);
        }

        /**
         * @brief Serverity 정보 추가
         **/
        function procNmsAdminInsertSeverity($args = null) {
            $oNmsController = &getController('nms');
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();

            $obj->var_idx = $args->severity;

            $values->value = $args->value;
            $values->type = $args->type;

            $values->event->type = $args->event_type;
            $values->event->sec = $args->event_sec;
            $values->event->count = $args->event_count;

            foreach($this->acts as $act) {
                if($args->{'act_'.strtolower($act)})
                    $values->act->{$act} = $args->{'act_'.strtolower($act)};

                foreach(array('value', 'form', 'path') as $type) {
                    if($args->{'act_'.strtolower($act).'_'.$type})
                        $values->{'act_'.$type}->{$act} = $args->{'act_'.strtolower($act).'_'.$type};
                }

            }

            $obj->value = serialize($values);

            if(preg_match("/,/", $args->mib_srl)) $args->mib_srl = explode(',', $args->mib_srl);

            if(!is_array($args->mib_srl)) $args->mib_srl = array($args->mib_srl);

            foreach($args->mib_srl as $key => $val) {
                $obj->module_srl = null;
                $obj->mib_srl = $obj->document_srl = $val;

                $oMib = $oNmsModel->getNmsMibInfo($obj);

                $obj->module_srl = ($oMib->data)?$oMib->data->module_srl:$obj->mib_srl;

                $output = $oNmsModel->getNmsExtraVars($obj);

                if(!$output->data) {
                    $output = $oNmsController->insertNmsExtraVars($obj);
                    $msg_code = "success_registed";
                } else {
                    $oNmsController->deleteNmsExtraVars($obj);
                    $output = $oNmsController->insertNmsExtraVars($obj);
                    $msg_code = "success_updated";
                }
            }

            $this->add('module_srl',$output->get('module_srl'));

            if(count($args->mib_srl) == 1 && $msg_code == "success_updated") {
                $this->add('act','dispNmsAdminSeverityInfo');
                $this->add('mib_srl',$obj->mib_srl);
                $this->add('severity',$obj->var_idx);
            } else {
                $this->add('act','dispNmsAdminSeverityList');
            }

            if(!$msg_code) $msg_code = "msg_severity_checks";

            $this->setMessage($msg_code);
        }

        /**
         * @brief Serverity 정보 추가
         **/
        function procNmsAdminInsertRestore($args = null) {
            $oNmsController = &getController('nms');
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();

            foreach($this->acts as $act) {
                if($args->{'act_'.strtolower($act)})
                    $values->act->{$act} = $args->{'act_'.strtolower($act)};

                foreach(array('value', 'form', 'path') as $type) {
                    if($args->{'act_'.strtolower($act).'_'.$type})
                        $values->{'act_'.$type}->{$act} = $args->{'act_'.strtolower($act).'_'.$type};
                }
            }

            if(preg_match("/,/", $args->mib_srl)) $args->mib_srl = explode(',', $args->mib_srl);

            if(!is_array($args->mib_srl)) $args->mib_srl = array($args->mib_srl);

            foreach($args->mib_srl as $key => $val) {
                $obj->mib_srl = $obj->document_srl = $val;
                $oMib = $oNmsModel->getNmsMibInfo($obj);
                $obj->module_srl = ($oMib->data)?$oMib->data->module_srl:$obj->mib_srl;
                $obj->var_idx = -4;

                $output = $oNmsModel->getNmsExtraVars($obj);

                if($values) $obj->value = $values;
                $obj->value = ($obj->value)?serialize($obj->value):null;

                if(!$output->data) {
                    if($obj->value) $output = $oNmsController->insertNmsExtraVars($obj);
                } else {
                    $oNmsController->deleteNmsExtraVars($obj);
                    if($obj->value) $output = $oNmsController->insertNmsExtraVars($obj);
                }

                if(!$output->toBool()) return $output;
            }

            $msg_code = "success_updated";
            $this->add('act','dispNmsAdminSeverityList');

            $this->setMessage($msg_code);
        }

        /**
         * @brief Wizard 정보 입력
         **/
        function procNmsAdminSettingWizard($args = null) {
            $oNmsAdminModel = &getAdminModel('nms');

            $args = Context::getRequestVars();

            // 설정 도중까지의 캐쉬가 남아있다면 그 다음 Step 메뉴 실행
            if(!$args->step) {
                for($i=3; $i>0; $i--) {
                    $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', $i);
                    if(file_exists($cache_file)) {
                        $step = $i+1;
                        break;
                    }
                }

                $this->add('step',$step);
                $this->add('act','dispNmsAdminSettingWizard');

                return;
            }

            // step 필터링
            switch($args->step) {
                case 1:
                    // 이미 존재하는 모듈 이름인지 체크
                    if(!$args->site_srl) $args->site_srl = 0;
                    $oModuleModel = &getModel('module');
                    if($oModuleModel->isIDExists($args->board_name, $args->site_srl)) return new Object(-1, "msg_module_name_exists");
                break;
                case 2:
                    $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', 2);
                    if(file_exists($cache_file)) @include($cache_file);

                    if($args->wizard_file != $wizard_info[2]->wizard_file) {
                        $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', 3);
                        if(file_exists($cache_file)) FileHandler::removeFile($cache_file);
                    }
                break;
                case 3:
                    $mmids = explode(',', $args->nms_wizard_mib_list);
                    foreach($mmids as $mmid) {
                        if($chk[$mmid]) return new Object(-1, "msg_mmid_name_exists");
                        $chk[$mmid] = $mmid;
                    }
                break;
                default: break;
            }

            $step = $oNmsAdminModel->procWizardCache($args);

            if($args->mibs) $this->setMessage($step);

            $this->add('step', $step);
        }

        /**
         * @brief Wizard 정보 등록
         **/
        function procNmsAdminInsertWizard($args = null) {
            // module 모듈의 controller 객체 생성
            $oModuleController = &getController('module');
            $oNmsController = &getController('nms');

            // 설정 캐쉬를 모두 가져옴
            for($i=3; $i>0; $i--) {
                $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', $i);
                if(file_exists($cache_file)) @include($cache_file);
            }

            $module_info = $wizard_info[1];
            $module_info->module = 'nms';

            // module insert
            $output = $oModuleController->insertModule($module_info);
            if(!$output->toBool()) return $output;

            // host insert
            $module_info->module_srl = $output->get('module_srl');
            $output = $oNmsController->insertNmsHost($module_info);

            $mib_info = $wizard_info[3]->mibs;
            if(!$mib_info) $mib_info = array();

            foreach($mib_info as $mibs) {
                $obj = $mibs;
                $obj->module_srl = $module_info->module_srl;
                $obj->mib = preg_match("/^[0-9]\./", $obj->mib)?".".$obj->mib:$obj->mib;
                $obj->max = $mibs->max_value;
                if($mibs->collect) $obj->extra_vars->collect_mode = $obj->collect;
                if($mibs->legend) $obj->extra_vars->legend_name = $obj->legend;
                $obj->extra_vars = ($obj->extra_vars)?serialize($obj->extra_vars):null;

                // mib insert/update
                $output = $oNmsController->insertNmsMib($obj);
                if(!$output->toBool()) return $output;

                unset($obj);
            }

            // 설정에 사용된 캐쉬를 모두 지움
            for($i=3; $i>0; $i--) {
                $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', $i);
                if(file_exists($cache_file)) FileHandler::removeFile($cache_file);
            }

            $this->add('act', 'dispNmsAdminIndex');
            $this->add('step', 5);
            $this->setMessage("success_registed");
        }

        /**
         * @brief Snmpwalk 정보를 재생성
         **/
        function procNmsAdminRemakeCache() {
            $oNmsAdminModel = &getAdminModel('nms');
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();

            if($args->module_srl) {
                $module_info = $oNmsModel->getNmsHostInfo($args->module_srl);
                $host = $module_info->host;
                $community = $module_info->community;
            } else {
                $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', 1);
                if(file_exists($cache_file)) @include($cache_file);

                $host = $wizard_info[1]->host;
                $community = $wizard_info[1]->community;
            }

            $cache_file = sprintf('./files/cache/nms/snmpwalk/%s_%s.cache.php', $host, 'snmpwalkoid');
            if(file_exists($cache_file)) FileHandler::removeFile($cache_file);

            $object_id[] = "";
            $object_id[] = ".1.3.6.1.4";
            $cache = $oNmsAdminModel->_snmpwalkCache($host, $community, $object_id, 100000000000000);
            if(!$cache) $this->setMessage("msg_not_cache");
            else $this->setMessage("msg_cache_complete");
        }

        /**
         * @brief Serverity 기본 정보 추가
         **/
        function procNmsAdminInsertSeverityConfig($args = null) {
            $oModuleModel = &getModel('module');
            $oNmsModel = &getModel('nms');
            $oNmsController = &getController('nms');

            $oNmsConfig = $oModuleModel->getModuleConfig('nms');
            $args = Context::getRequestVars();

            $obj->var_idx = -3;
            $obj->module_srl = $obj->document_srl = $args->module_srl;

            foreach($this->acts as $act) {
                foreach(array('value', 'form', 'path') as $type) {
                    if($args->{'act_'.strtolower($act).'_'.$type})
                        $obj->severity->{'act_'.$type}->{$act} = $args->{'act_'.strtolower($act).'_'.$type};
                }
            }

            if($obj->severity) $obj->value = serialize($obj->severity);

            $output = $oNmsModel->getNmsExtraVars($obj);

            if(!$output->data) {
                if($obj->value) $output = $oNmsController->insertNmsExtraVars($obj);
                $msg_code = "success_registed";
            } else {
                $oNmsController->deleteNmsExtraVars($obj);
                if($obj->value) $output = $oNmsController->insertNmsExtraVars($obj);
                $msg_code = "success_updated";
            }

            $this->setMessage($msg_code);
        }

        /**
         * @brief Host 정보 삭제
         **/
        function procNmsAdminDeleteHost($args = null) {
            $oModuleController = &getController('module');
            $oNmsController = &getController('nms');

            $args = Context::getRequestVars();

            // 모듈 삭제
            $output = $oModuleController->deleteModule($args->module_srl);

            // 호스트 삭제
            $output = $oNmsController->deleteNmsHost($args);

            // 삭제 전 Host로 설정된 모든 SNMP 로그를 삭제 함
            if($args->log_delete=='Y')
                $snmp_log_output = $oNmsController->deleteNmsSnmpLog($args);

            $this->add('page_mib',Context::get('page_mib'));
            $this->add('act','dispNmsAdminIndex');
            $this->setMessage("success_deleted");
        }

        /**
         * @brief Group 정보 삭제
         **/
        function procNmsAdminDeleteGroup($args = null) {
            $oNmsController = &getController('nms');
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();

            // 생성한 Cron 파일 삭제
            if($args->cronstate=='Y') {
                $group_info = $oNmsModel->getNmsGroupInfo($args->group_srl);
                $this->procCronDelete($group_info->schedule,$group_info->group_srl);
            }

            $output = $oNmsController->deleteNmsGroup($args);

            $this->add('page',Context::get('page'));
            $this->add('act','dispNmsAdminGroupList');
            $this->setMessage("success_deleted");
        }

        /**
         * @brief MIB 정보 추가
         **/
        function procNmsAdminInsertMib($args = null) {
            $oNmsController = &getController('nms');
            $oNmsModel = &getModel('nms');

            $args = Context::getRequestVars();

            if($args->collect_mode) $args->extra_vars->collect_mode = $args->collect_mode;
            if($args->legend_name) $args->extra_vars->legend_name = $args->legend_name;
            $args->extra_vars = ($args->extra_vars)?serialize($args->extra_vars):null;

            // mib_srl의 값에 따라 insert/update
            if(!$args->mib_srl) {
                $output = $oNmsController->insertNmsMib($args);
                $msg_code = "success_registed";
                $this->add('act','dispNmsAdminMibList');
            } else {
                $output = $oNmsController->updateNmsMib($args);
                $msg_code = "success_updated";
                $this->add('act','dispNmsAdminMibInfo');
            }

            if(!$output->toBool()) return $output;

            // Severity로 설정된 정보가 완료 시 사용될 이벤트를 설정
            foreach($this->acts as $act) {
                if($args->{'act_'.strtolower($act)})
                    $values->act->{$act} = $args->{'act_'.strtolower($act)};

                foreach(array('value', 'form', 'path') as $type) {
                    if($args->{'act_'.strtolower($act).'_'.$type})
                        $values->{'act_'.$type}->{$act} = $args->{'act_'.strtolower($act).'_'.$type};
                }
            }

            $args->document_srl = $args->mib_srl;
            $args->var_idx = -4;
            $output = $oNmsModel->getNmsExtraVars($args);

            if($values) $args->value = $values;
            $args->value = ($args->value)?serialize($args->value):null;

            if(!$output->data) {
                if($args->value) $output = $oNmsController->insertNmsExtraVars($args);
            } else {
                $oNmsController->deleteNmsExtraVars($args);
                if($args->value) $output = $oNmsController->insertNmsExtraVars($args);
            }

            if(!$output->toBool()) return $output;

            $this->add('page',Context::get('page'));
            $this->add('module_srl',$output->get('module_srl'));
            $this->add('mib_srl',$output->get('mib_srl'));
            $this->setMessage($msg_code);
        }

        /**
         * @brief MIB 정보 삭제
         **/
        function procNmsAdminDeleteMib($args = null) {
            $oNmsController = &getController('nms');

            $args = Context::getRequestVars();

            $output = $oNmsController->deleteNmsMib($args);

            // MIB 삭제 전 MIB의 SNMP 로그를 삭제
            if($args->log_delete=='Y')
                $snmp_log_output = $oNmsController->deleteNmsSnmpLog($args);

            $this->add('page',Context::get('page'));
            $this->add('act','dispNmsAdminMibList');
            $this->setMessage("success_deleted");
        }

        /**
         * @brief Severity 정보 삭제
         **/
        function procNmsAdminDeleteSeverity($args = null) {
            $oModuleModel = &getModel('module');
            $oNmsController = &getController('nms');

            $oNms = $oModuleModel->getModuleConfig('nms');

            $args = Context::getRequestVars();

            $args->document_srl = $args->mib_srl;
            $args->var_idx = $args->severity;

            $output = $oNmsController->deleteNmsExtraVars($args);

            // Severity 정보를 삭제 전에 Severity log에 기록된 정보를 삭제
            if($args->log_delete=='Y') {
                $severity_log_output = $oNmsController->deleteNmsSeverityLog($args);
            }

            $this->add('page_sl',Context::get('page_sl'));
            $this->add('module_srl',$output->get('module_srl'));
            $this->add('act','dispNmsAdminSeverityList');

            $this->setMessage("success_deleted");
        }

        /**
         * @brief Syslog Log 삭제
         **/
        function procNmsAdminDeleteSyslogLog($args = null) {
            $oNmsController = &getController('nms');
            $args = Context::getRequestVars();

            if(!$args->cart) return new Object(-1, "msg_service_checks");

            $args->cart = explode('|@|',$args->cart);
            $obj->syslog_srl = implode(',',$args->cart);

            $output = $oNmsController->deleteNmsSyslogLog($obj);
            if(!$output->toBool()) return new Object(-1, "msg_error_occured");

            $msg_code = "success_deleted";
            $this->setMessage($msg_code);
        }

        /**
         * @brief Snmp Trap Log 삭제
         **/
        function procNmsAdminDeleteSnmpTrapLog($args = null) {
            $oNmsController = &getController('nms');
            $args = Context::getRequestVars();

            if(!$args->cart) return new Object(-1, "msg_service_checks");

            $args->cart = explode('|@|',$args->cart);
            $obj->snmptrap_srl = implode(',',$args->cart);

            $output = $oNmsController->deleteNmsSnmpTrapLog($obj);
            if(!$output->toBool()) return new Object(-1, "msg_error_occured");

            $msg_code = "success_deleted";
            $this->setMessage($msg_code);
        }

        /**
         * @brief Severity Log 삭제
         **/
        function procNmsAdminDeleteSeverityLog($args = null) {
            $oNmsController = &getController('nms');
            $args = Context::getRequestVars();

            if(!$args->cart) return new Object(-1, "msg_service_checks");

            $args->cart = explode('|@|',$args->cart);
            $obj->severity_srl = implode(',',$args->cart);

            $output = $oNmsController->deleteNmsSeverityLog($obj);
            if(!$output->toBool()) return new Object(-1, "msg_error_occured");

            $msg_code = "success_deleted";
            $this->setMessage($msg_code);
        }
    }
?>