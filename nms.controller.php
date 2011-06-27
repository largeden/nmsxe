<?php
    /**
     * @class  nmsController
     * @author largeden (developer@nmsxe.com)
     * @brief  nmsXE Controller class
     **/

    class nmsController extends nms {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief Snmp 수집을 api로 처리
         **/
        function procNmsSnmp() {
             $this->_procNmsSnmp();
        }

        /**
         * @brief Syslog 수집을 api로 처리
         **/
        function procNmsSyslog() {
             $this->_procNmsSyslog();
        }

        /**
         * @brief SnmpTrap 수집을 api로 처리
         **/
        function procNmsSnmpTrap() {
             $this->_procNmsSnmpTrap();
        }

        /**
         * @brief SnmpTrap 수집을 api로 처리
         **/
        function procNmsCompress() {
            $oNmsModel = &getModel('nms');

            if(Context::get('group_srl')) $this->_procNmsCompress(Context::get('group_srl'));
            else {
                $output = $oNmsModel->getNmsGroup();
                if($output) {
                    foreach($output as $group) {
                        if($group->cronstate=='Y') $group_info[] = $group;
                    }

                    $this->add('group_info',$group_info);
                }
            }
        }

        /**
         * @brief Host 정보 등록
         **/
        function insertNmsHost($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            $output = executeQuery('nms.insertNmsHost', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // commit
            $oDB->commit();

            $output->add('module_srl',$args->module_srl);

            return $output;
        }

        /**
         * @brief Host 정보 수정
         **/
        function updateNmsHost($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            $output = executeQuery('nms.updateNmsHost', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // commit
            $oDB->commit();

            return $output;

        }

        /**
         * @brief Host 정보 삭제
         **/
        function deleteNmsHost($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // trigger 호출 (before) : 모듈이 삭제되면 자식격인 MIB도 삭제되도록 함
            $output = ModuleHandler::triggerCall('nms.deleteNmsHost', 'before', $args);
            if(!$output->toBool()) return $output;

            $output = executeQuery('nms.deleteNmsHost', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // trigger 호출 (after) : 차후 연동을 위해 준비함
            if($output->toBool()) {
                $trigger_output = ModuleHandler::triggerCall('nms.deleteNmsHost', 'after', $args);
                if(!$trigger_output->toBool()) {
                    $oDB->rollback();
                    return $trigger_output;
                }
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief Group 정보 등록
         **/
        function insertNmsGroup($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // 이미 존재하는 그룹 이름인지 체크
            $output = executeQuery('nms.isExistsNmsGroup', $args);
            if(!$output->toBool() || $output->data->count) {
                return new Object(-1, "msg_group_name_exists");
            }

             // group_srl 생성
            if(!$args->group_srl) $args->group_srl = getNextSequence();

            // 그룹 등록
            $output = executeQuery('nms.insertNmsGroup', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // commit
            $oDB->commit();

            $output->add('group_srl',$args->group_srl);

            return $output;
        }

        /**
         * @brief Group 정보 수정
         **/
        function updateNmsGroup($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            $output = executeQuery('nms.updateNmsGroup', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief Group 정보 삭제
         **/
        function deleteNmsGroup($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // trigger 호출 (before) : 그룹이 삭제될때 연동될 내용을 위해 선언해 둠
            $output = ModuleHandler::triggerCall('nms.deleteNmsGroup', 'before', $args);
            if(!$output->toBool()) return $output;

            $output = executeQuery('nms.deleteNmsGroup', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // trigger 호출 (after) : 그룹이 삭제될때 연동될 내용을 위해 선언해 둠
            if($output->toBool()) {
                $trigger_output = ModuleHandler::triggerCall('nms.deleteNmsGroup', 'after', $args);
                if(!$trigger_output->toBool()) {
                    $oDB->rollback();
                    return $trigger_output;
                }
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief MIB 정보 등록
         **/
        function insertNmsMib($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // 이미 존재하는 MIB 이름인지 체크
            $output = executeQuery('nms.isExistsNmsMmid', $args);
            if(!$output->toBool() || $output->data->count) {
                return new Object(-1, "msg_mmid_name_exists");
            }

            // mib_srl 번호 생성 (생성은 XE의 번호로 부여한다)
            if(!$args->mib_srl) $args->mib_srl = getNextSequence();

            // MIB 등록
            $output = executeQuery('nms.insertNmsMib', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // commit
            $oDB->commit();

            $output->add('module_srl',$args->module_srl);
            $output->add('mib_srl',$args->mib_srl);

            return $output;
        }

        /**
         * @brief MIB 정보 수정
         **/
        function updateNmsMib($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // 이미 존재하는 MIB 이름인지 체크
            $output = executeQuery('nms.isExistsNmsMmid', $args);
            if(!$output->toBool() || $output->data->count) {
                return new Object(-1, "msg_mmid_name_exists");
            }

            $output = executeQuery('nms.updateNmsMib', $args);
            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief MIB 정보 삭제
         **/
        function deleteNmsMib($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // trigger 호출 (before) : MIB가 삭제될때 연동될 내용을 위해 선언해 둠
            $output = ModuleHandler::triggerCall('nms.deleteNmsMib', 'before', $args);
            if(!$output->toBool()) return $output;

            $output = executeQuery('nms.deleteNmsMib', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // trigger 호출 (after) : MIB가 삭제될때 연동될 내용을 위해 선언해 둠
            if($output->toBool()) {
                $trigger_output = ModuleHandler::triggerCall('nms.deleteNmsMib', 'after', $args);
                if(!$trigger_output->toBool()) {
                    $oDB->rollback();
                    return $trigger_output;
                }
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief Severity 설정 정보(ExtraVars) 등록
         **/
        function insertNmsExtraVars($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // Severity 등록
            $output = executeQuery('document.insertDocumentExtraVar', $args);
            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief Severity 설정 정보(ExtraVars) 삭제
         **/
        function deleteNmsExtraVars($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            $output = executeQuery('document.deleteDocumentExtraVars', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief Severity 정보 추가
         **/
        function insertNmsSeverityLog($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            $output = executeQuery('nms.insertNmsSeverityLog', $args);
            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief Severity 정보 수정
         **/
        function updateNmsSeverityLog($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // trigger 호출 (before) : SeverityLog가 수정될때 연동될 내용을 위해 선언해 둠
            $output = ModuleHandler::triggerCall('nms.updateNmsSeverityLog', 'before', $args);
            if(!$output->toBool()) return $output;

            $output = executeQuery('nms.updateNmsSeverityLog', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // trigger 호출 (after) : Syslog가 삭제될때 연동될 내용을 위해 선언해 둠
            if($output->toBool()) {
                $trigger_output = ModuleHandler::triggerCall('nms.updateNmsSeverityLog', 'after', $args);
                if(!$trigger_output->toBool()) {
                    $oDB->rollback();
                    return $trigger_output;
                }
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief 수집된 SNMP를 저장
         **/
        function insertNmsSnmpLog($args = false) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // trigger 호출 (before) : SNMP가 수집될떄 연동될 내용을 위해 선언해 둠
            $output = ModuleHandler::triggerCall('nms.insertNmsSnmpLog', 'before', $args);

            if(!$output->toBool()) return $output;

            $output = executeQuery('nms.insertNmsSnmpLog', $args);
            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // trigger 호출 (after) : SNMP가 수집될떄 연동될 내용을 위해 선언해 둠
            if($output->toBool()) {
                $trigger_output = ModuleHandler::triggerCall('nms.insertNmsSnmpLog', 'after', $args);
                if(!$trigger_output->toBool()) {
                    $oDB->rollback();
                    return $trigger_output;
                }
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief History 등록
         **/
        function procNmsInsertHistory() {
            // 권한 체크
            if(!$this->grant->manager||!$this->grant->is_admin) return new Object(-1, "msg_not_permitted");
            $args = Context::getRequestVars();
            if(!$args->mid || !$args->group_name) return;
            // 모듈 선언
            $oModuleModel = &getModel('module');
            $oMemberModel = &getModel('member');
            $oDocumentModel = &getModel('document');
            $oDocumentController = &getController('document');
            $oNmsModel = &getModel('nms');
            $oNmsView = &getView('nms');
            $oTemplateHandler = new TemplateHandler();

            // 모듈 정보를 구함
            $module_info = $oModuleModel->getModuleInfoByMid($args->mid);
            if(!$module_info) return;

            // 사용자 정보를 구함
            $member_srl = $oMemberModel->getLoggedMemberSrl();

            // 첨부파일을 등록하기 위해 먼저 글을 등록
            $obj->module_srl = $module_info->module_srl;
            $obj->title = "History (group name : {$args->group_name})";
            $obj->content = "<h3>{$obj->title}</h3>";
            $obj->extra_vars = $args->group_name;
            $output = $oDocumentController->insertDocument($obj);
            if(!$output->toBool()) return new Object(-1, "msg_fail_to_history");

            // History 첨부파일, 본문 내용 수정을 위해 document_srl을 저장
            $document_srl = $output->get('document_srl');

            /* 첨부파일 등록 진행 */
            // 스킨 설정을 가져와 pChart 그래프 파일 호출 변수를 만듬
            $skin_info = $oModuleModel->getModuleSkinVars($module_info->module_srl);
            $graph_info->act = 'dispNmsGraph';
            $graph_info->mid = $module_info->mid;
            $graph_info->graph = 'line';
            $graph_info->group_name = $args->group_name;
            $graph_info->skin = $skin_info['colorset']->value;
            $graph_info->unit = $skin_info['unit']->value;
            $skin_info['view_graph']->value = unserialize($skin_info['view_graph']->value);
            if(!$skin_info['view_graph']->value)
                $skin_info['view_graph']->value = array('normal','1hour','3hour','6hour','12hour','day','week','month','year');

            // 현재 출력되고 있는 그래프의 종류를 순차적으로 처리
            foreach($skin_info['view_graph']->value as $val) {
                // customer mode는 등록 제외
                if($val == 'customer') continue;

                // 그래프를 새로 갱신하면서 그래프 파일 경로를 받아옴
                $graph_info->mode = $val;
                $graph_dir = $oNmsView->dispNmsGraph($graph_info);
                $dir = FileHandler::readDir($graph_dir);
                foreach($dir as $file) $graph_path = $file;

                // 그래프 파일을 History 첨부파일로 복사
                $path = sprintf("./files/attach/images/%s/%s", $module_info->module_srl,getNumberingPath($document_srl,3));
                $filename = "{$graph_info->mid}_{$graph_info->group_name}_{$graph_info->mode}_{$graph_info->skin}.png";
                if(FileHandler::makeDir($path)) @copy($graph_dir.$graph_path, $path.$filename);

                // History 첨부파일 등록
                $obj->file_srl = getNextSequence();
                $obj->upload_target_srl = $document_srl;
                $obj->direct_download = 'Y';
                $obj->source_filename = $filename;
                $obj->uploaded_filename = $path.$filename;
                $obj->download_count = 0;
                $obj->file_size = @filesize($path.$filename);
                $obj->comment = NULL;
                $obj->member_srl = $member_srl;
                $obj->sid = md5(rand(rand(1111111,4444444),rand(4444445,9999999)));
                $obj->isvalid = 'Y';

                $output = executeQuery('file.insertFile', $obj);
                if(!$output->toBool()) return new Object(-1, "msg_fail_to_fileupload");

                $uploaded_filename[$val] = $path.$filename;
            }

            Context::set('document_srl', $document_srl);
            Context::set('module_info', $module_info);
            Context::set('args', $args);
            Context::set('uploaded_filename', $uploaded_filename);

            // mib, group_name, legend의 정보를 불러옴
            $mib_obj->module_srl = $module_info->module_srl;
            $mib_obj->group_name = $args->group_name;
            $mib_group_info = $oNmsModel->getNmsMibGroupInfo($mib_obj);
            Context::set('mib_group_info',$mib_group_info);

            $output = $oNmsModel->getNmsMib($mib_obj);

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

            // nms 스킨의 history_insert의 템플릿을 해석해서 본문을 바꿈
            $buff = $oTemplateHandler->compile("modules/nms/skins/{$module_info->skin}", "history.insert.html");
            $obj->content = $buff;

            // History 본문을 템플릿에서 처리된 내용으로 수정
            $oDocument = $oDocumentModel->getDocument($document_srl, $this->grant->manager);
            $obj->document_srl = $document_srl;
            $output = $oDocumentController->updateDocument($oDocument, $obj);
            if(!$output->toBool()) return new Object(-1, "msg_fail_to_history");

            $this->add('document_srl',$document_srl);
            $this->setMessage("success_saved");
        }

        /**
         * @brief SNMP 수집 처리
         **/
        function _procNmsSnmp() {
            set_time_limit(0);
            Context::setResponseMethod('XMLRPC');
            $oNmsModel = &getModel('nms');
            $args = Context::getRequestVars();
            if($args->act != 'procNmsSnmp' || !$args->group_srl) return;

            $oNmsGroupInfo = $oNmsModel->getNmsGroupInfo($args->group_srl);

            // crontab 실행을 하지 않을 경우 수행안함
            if($oNmsGroupInfo->cronstate == 'N') return;

            $oNmsGroupInfo->extra_vars = unserialize($oNmsGroupInfo->extra_vars);
            // 설정된 schedule 시간보다 짧은 시간의 요청이 들어올 경우 수행안함(중복 요청 방지)
            $group_checkdate = strtotime(date('YmdHis')) - strtotime($oNmsGroupInfo->checkdate) + 1 + $oNmsGroupInfo->extra_vars->delay;
            if($oNmsGroupInfo->schedule > $group_checkdate) return;
            $oNmsGroupInfo->extra_vars->delay = null;

            // 요청되어 들어온 group_srl 번호에 해당하는 모든 HOST MIB 정보를 구해서 기록
            $host_info = $oNmsModel->getNmsHost($args);
            $group_start_time = date('YmdHis');
            $group_delay_time = 0;
            foreach($host_info as $oHost) {
                $oHost->extra_vars = unserialize($oHost->extra_vars);
                // 설정된 schedule 시간보다 짧은 시간의 요청이 들어올 경우 수행안함(중복 요청 방지)
                $host_checkdate = strtotime(date('YmdHis')) - strtotime($oHost->checkdate) + 1 + $oHost->extra_vars->delay;
                //if($oNmsGroupInfo->schedule > $host_checkdate) continue;
                $oHost->extra_vars->delay = null;

                $mib_info = $oNmsModel->getNmsMib($oHost);
                $host_start_time = date('YmdHis');
                $host_delay_time = 0;
                foreach($mib_info as $oMib) {
                    // SNMP 값을 호출
                    $nowSnmp = $oNmsModel->_snmpget($oHost->host, $oHost->community, $oMib->mib);
                    $nowSnmp = preg_replace("/\"/","",$nowSnmp);
                    $obj->module_srl = $oHost->module_srl;
                    $obj->mib_srl = $oMib->mib_srl;

                    // 현재 정보를 입력하기 전에 바로 전 정보를 구함
                    $oLastSnmpInfo = $oNmsModel->getNmsLastSnmpLog($obj);

                    // 바로 전 정보의 시간과 현재 시간을 비교하여 기간(초)을 구함
                    if($oLastSnmpInfo) {
                        $oLastSnmpInfo->extra_vars = unserialize($oLastSnmpInfo->extra_vars);
                        $obj->extra_vars->sec = strtotime(date('YmdHis')) - strtotime($oLastSnmpInfo->regdate);

                           // 전 정보의 값이 없으면 0(초)로 생성
                    } else $obj->extra_vars->sec = 0;

                    // 수집된 자료의 압축기간 여부 1:sec, 2:min, 3:hour, 4:day, 5:week, 6:mon, 7:year
                    if($obj->extra_vars->sec < 60) $obj->session = 1;
                    elseif($obj->extra_vars->sec < 60*60) $obj->session = 2;
                    elseif($obj->extra_vars->sec < 60*60*24) $obj->session = 3;
                    elseif($obj->extra_vars->sec < 60*60*24*7) $obj->session = 4;
                    elseif(floor($obj->extra_vars->sec/(60*60*24)) < 30) $obj->session = 5;
                    elseif(floor($obj->extra_vars->sec/(60*60*24)) < 365) $obj->session = 6;
                    else $obj->session = 7;

                    $oMib->extra_vars = unserialize($oMib->extra_vars);
                    if(preg_match("/^[-]?[0-9]+([\.][0-9]+)?$/", $nowSnmp)) {
                        switch($oMib->extra_vars->collect_mode) {
                            case 1: // 현재 사용률의 MIB 정보가 없는 경우 전후 정보를 이용하여 현재 사용률을 구함
                                $lastSnmp = (!$oLastSnmpInfo)?0:$oLastSnmpInfo->extra_vars->realValue;
                                if(!$lastSnmp || $lastSnmp == 'NULL') $obj->value = 0;
                                else $obj->value = round(($nowSnmp - $lastSnmp) / $obj->extra_vars->sec,3);
                                // 구한 값이 0보다 작을 경우 0
                                if($obj->value < 0) $obj->value = 0;
                            break;
                            case 2: // 현재 사용률의 MIB 정보가 없는 경우 전후 정보를 이용하여 현재 사용률의 평균을 구함
                                $lastSnmp = (!$oLastSnmpInfo)?0:$oLastSnmpInfo->extra_vars->realValue;
                                if(!$lastSnmp || $lastSnmp == 'NULL') $obj->value = 0;
                                else $obj->value = round((round(($nowSnmp - $lastSnmp) / $obj->extra_vars->sec,0) / $oMib->max) * 100, 3);
                                // 구한 값이 0보다 작을 경우 0
                                if($obj->value < 0) $obj->value = 0;
                            break;
                            case 3: // max 값과 현재 값을 비교하여 평균 값을 구함
                                $obj->value = round(($nowSnmp / $oMib->max) * 100, 3);
                            break;
                            case 4: // max 값과 현재 값을 가지고 현재 사용중인 값의 평균 사용률을 구함
                                $obj->value = round((($oMib->max - $nowSnmp) / $oMib->max) * 100, 3);
                            break;
                            case 5: // max 값에서 현재 값을 뺌
                                $obj->value = $oMib->max - $nowSnmp;
                            break;
                            default: $obj->value = $nowSnmp; break;
                        }
                    } else $obj->value = $nowSnmp;

                    // 실제 수집된 값을 저장
                    $obj->extra_vars->realValue = $nowSnmp;
                    $obj->extra_vars = serialize($obj->extra_vars);

                    // Snmp 저장
                    $this->insertNmsSnmpLog($obj);

                    // Severity 수행
                    $this->procNmsSeverity($obj, 'snmp');

                    // 데이터 압축
                    //$this->procNmsCompressSnmp($obj);

                    // graphCache를 클리어 한다.
                    if($oMib->extra_vars->graphCache) unset($oMib->extra_vars->graphCache);
                    $oMib->extra_vars = serialize($oMib->extra_vars);
                    $this->updateNmsMib($oMib);

                    unset($obj);
                }

                $delay_time = strtotime(date('YmdHis')) - strtotime($host_start_time);
                // 수행시간이 늦어질 경우 딜레이된 시간을 계산
                $host_delay_time += $delay_time;
                // 딜레이된 시간만큼 다음 실행시 반영
                if($host_delay_time > 0) $oHost->extra_vars->delay = $host_delay_time;
                else $oHost->extra_vars->delay = null;
                $oHost->extra_vars = serialize($oHost->extra_vars);
                $oHost->checkdate = null;

                // 해당 HOST의 처리가 완료 될 경우 HOST 정보의 checkdate를 업데이트
                $this->updateNmsHost($oHost);
                unset($host_delay_time);
            }

            $delay_time = strtotime(date('YmdHis')) - strtotime($group_start_time);
            // 수행시간이 늦어질 경우 딜레이된 시간을 계산
            $group_delay_time += $delay_time;
            // 딜레이된 시간만큼 다음 실행시 반영
            if($group_delay_time > 0) $oNmsGroupInfo->extra_vars->delay = $group_delay_time;
            else $oNmsGroupInfo->extra_vars->delay = null;

            $oNmsGroupInfo->extra_vars = serialize($oNmsGroupInfo->extra_vars);
            $oNmsGroupInfo->checkdate = null;

            // 모든 처리가 완료 updateNmsGroup될 경우 Group 정보의 checkdate를 업데이트
            $this->updateNmsGroup($oNmsGroupInfo);

            unset($group_delay_time);
        }

        /**
         * @brief 데이터를 압축할 대상을 선정하여 압축 수행
         **/
        function _procNmsCompress($group_srl = null) {
            if(!$group_srl) return;
            $oNmsModel = &getModel('nms');

            $args->group_srl = $group_srl;
            $host_info = $oNmsModel->getNmsHost($args);

            foreach($host_info as $oHost) {
                $mib_info = $oNmsModel->getNmsMib($oHost);

                foreach($mib_info as $oMib) $this->procNmsCompressSnmp($oMib);
            }
        }

        /**
         * @brief 데이터 압축
         **/
        function procNmsCompressSnmp($args = null) {
            if($args == null) return;
            $oModuleModel = &getModel('module');
            $oNmsModel = &getModel('nms');
            $oNms = $oModuleModel->getModuleConfig('nms');
            // 기간이 없거나 1보다 작을 경우 수행중단
            if(!$oNms->moduleConfig->compress || $oNms->moduleConfig->compress <= 0) return;
            $compress = $oNms->moduleConfig->compress;
            $compress_type = $oNms->moduleConfig->compress_type;

            $snmp_args = $args;

            $snmp_args->order_type = 'asc';

            // 입력기간 전의 데이터를 시간별로 압축
            $args->regdate = date('YmdH', strtotime(date('YmdHis').' -'.$compress.' day'));
            $args->session = 3;

            $output = executeQueryArray('nms.getNmsCompress', $args);
            if(!count($output->data)) return;

            $output = executeQueryArray('nms.getNmsCompressHour', $args);
            if(!$output->toBool()) return;

            if($output->data) {
                foreach($output->data as $key=>$val) {
                    $obj = $val;
                    $obj->session = 3;
                    $obj->extra_vars->sec = 3600;
                    $obj->extra_vars = serialize($obj->extra_vars);
                    $snmp_srl[] = $val->snmp_srl;
                    $output = executeQuery('nms.updateNmsCompressSnmpLog', $obj);
                }
                unset($obj);
                // 압축 후 기간 session이 3미만은 모두 삭제
                $obj->module_srl = $args->module_srl;
                $obj->mib_srl = $args->mib_srl;
                $obj->session = 3;
                $obj->regdate = $args->regdate;
                $obj->snmp_srl = implode(',',$snmp_srl);
                $output = executeQuery('nms.deleteNmsCompressSnmpLog', $obj);
                unset($snmp_srl);
            } else return;

            // 최종 압축이 시간별이라면 여기서 수행 중단
            if($compress_type == 3) return;

            $compress += 1;
            // 입력기간 전의 데이터를 일일별로 압축
            $args->regdate = date('YmdH', strtotime(date('YmdHis').' -'.$compress.' day'));
            $args->session = 4;

            $output = executeQueryArray('nms.getNmsCompress', $args);
            if(!count($output->data)) return;

            $output = executeQueryArray('nms.getNmsCompressDay', $args);
            if(!$output->toBool()) return;

            if($output->data) {
                foreach($output->data as $key=>$val) {
                    $obj = $val;
                    $obj->session = 4;
                    $obj->extra_vars->sec = 86400;
                    $obj->extra_vars = serialize($obj->extra_vars);
                    $snmp_srl[] = $val->snmp_srl;
                    executeQuery('nms.updateNmsCompressSnmpLog', $obj);
                }
                unset($obj);
                // 압축 후 기간 session이 4미만은 모두 삭제
                $obj->module_srl = $args->module_srl;
                $obj->mib_srl = $args->mib_srl;
                $obj->session = 4;
                $obj->regdate = $args->regdate;
                $obj->snmp_srl = implode(',',$snmp_srl);
                executeQuery('nms.deleteNmsCompressSnmpLog', $obj);
                unset($snmp_srl);
            } else return;

            // 최종 압축이 일별이라면 여기서 수행 중단
            if($compress_type == 4) return;

           /**
            * 입력기간 전의 일주일 이전부터 주별로 압축 Query조건이 date_format(regdate, '%U')로 되어있음
            * 타 데이터베이스간 호환 문제의 영향이 있음
            **/
            $compress += 7;
            $args->regdate = date('YmdH', strtotime(date('YmdHis').' -'.$compress.' day'));
            $args->session = 5;

            $output = executeQueryArray('nms.getNmsCompress', $args);
            if(!count($output->data)) return;

            $output = executeQueryArray('nms.getNmsCompressWeek', $args);
            if(!$output->toBool()) return;

            if($output->data) {
                foreach($output->data as $key=>$val) {
                    $obj = $val;
                    $obj->session = 5;
                    $obj->extra_vars->sec = 604800;
                    $obj->extra_vars = serialize($obj->extra_vars);
                    $snmp_srl[] = $val->snmp_srl;
                    $output = executeQuery('nms.updateNmsCompressSnmpLog', $obj);
                }
                unset($obj);
                // 압축 후 기간 session이 5미만은 모두 삭제
                $obj->module_srl = $args->module_srl;
                $obj->mib_srl = $args->mib_srl;
                $obj->session = 5;
                $obj->regdate = $args->regdate;
                $obj->snmp_srl = implode(',',$snmp_srl);
                $output = executeQuery('nms.deleteNmsCompressSnmpLog', $obj);
                unset($snmp_srl);
            } else return;

            // 최종 압축이 주별이라면 여기서 수행 중단
            if($compress_type == 5) return;

            // 입력기간 전의 한달 이전부터 월별로 압축
            $compress += 30;
            $args->regdate = date('YmdH', strtotime(date('YmdHis').' -'.$compress.' day'));
            $args->session = 6;

            $output = executeQueryArray('nms.getNmsCompress', $args);
            if(!count($output->data)) return;

            $output = executeQueryArray('nms.getNmsCompressMonth', $args);
            if(!$output->toBool()) return;

            if($output->data) {
                foreach($output->data as $key=>$val) {
                    $obj = $val;
                    $obj->session = 6;
                    $obj->extra_vars->sec = 2592000;
                    $obj->extra_vars = serialize($obj->extra_vars);
                    $obj->snmp_srl = implode(',',$snmp_srl);
                    $snmp_srl[] = $val->snmp_srl;
                    executeQuery('nms.updateNmsCompressSnmpLog', $obj);
                }
                unset($obj);
                // 압축 후 기간 session이 6미만은 모두 삭제
                $obj->module_srl = $args->module_srl;
                $obj->mib_srl = $args->mib_srl;
                $obj->session = 6;
                $obj->regdate = $args->regdate;
                $obj->snmp_srl = implode(',',$snmp_srl);
                executeQuery('nms.deleteNmsCompressSnmpLog', $obj);
                unset($snmp_srl);
            } else return;

            // 최종 압축이 월별이라면 여기서 수행 중단
            if($compress_type == 6) return;

            /* 년 이상부터는 향후 상황을 봐서 진행 */
        }

        /**
         * @brief Syslog 등록
         **/
        function insertNmsSyslogLog($args = false) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // trigger 호출 (before)
            $output = ModuleHandler::triggerCall('nms.insertNmsSyslogLog', 'before', $args);
            if(!$output->toBool()) return $output;

            $output = executeQuery('nms.insertNmsSyslogLog', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // trigger 호출 (after)
            if($output->toBool()) {
                $trigger_output = ModuleHandler::triggerCall('nms.insertNmsSyslogLog', 'after', $args);
                if(!$trigger_output->toBool()) {
                    $oDB->rollback();
                    return $trigger_output;
                }
            }

            // commit
            $oDB->commit();

            return $output;

        }

        /**
         * @brief Syslog 처리
         **/
        function _procNmsSyslog() {
            Context::setResponseMethod('XMLRPC');
            $args = Context::getRequestVars();
            if($args->act != 'procNmsSyslog') return;

            $args->msg = urldecode($args->msg);

            /* 메시지 포멧을 nms.socket.php에서 "[192.168.0.1:2323]<191>메시지" 로 형식하여 보낸걸 파싱 함 */
            // [192.168.0.1:2323] 안의 정보를 구해서 ip와 port로 나눔
            preg_match_all("!\[([^\>]*)\]!is", $args->msg, $ip);
            $ip = $ip[1][0];
            $ip = explode(':',$ip);

            // <191> 안의 정보를 구함
            preg_match_all("!<([^\>]*)\>!is", $args->msg, $priority);
            $priority = $priority[1][0];

            // []과 <>의 내용을 제거
            $value = preg_replace_callback("!\[([^\>]*)\]!i",array($this, '_code'), $args->msg);
            $obj->value = preg_replace_callback("!\<([^\>]*)\>!i",array($this, '_code'), $value);

            // RFC 규약의 priority 값을 facility과 severity으로 계산 처리
            $obj->priority = $priority;
            $obj->facility = floor($priority/8);
            $obj->severity = $priority%8;
            $obj->ip_address = $ip[0];
            $obj->ip_port = $ip[1];
            // ip 버전을 구함 IPv4,IPv6
            if($obj->ip_address) $obj->ip_type = (count(explode('.', $obj->ip_address))==4)?4:6;

            // syslog 입력
            $this->insertNmsSyslogLog($obj);

            // 메시지를 보낸 ip를 추가
            $obj->value = "[".$obj->ip_address."]".$obj->value;

            // severity 값의 필터 처리를 위한 호출
            $this->procNmsSeverity($obj, 'syslog');
        }

        // []과 <> 내용 제거
        function _code($matches) {
            return $matches[2];
        }

        /**
         * @brief Snmp Trap 등록
         **/
        function insertNmsSnmpTrapLog($args = false) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // trigger 호출 (before)
            $output = ModuleHandler::triggerCall('nms.insertNmsSnmpTrapLog', 'before', $args);
            if(!$output->toBool()) return $output;

            $output = executeQuery('nms.insertNmsSnmpTrapLog', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // trigger 호출 (after)
            if($output->toBool()) {
                $trigger_output = ModuleHandler::triggerCall('nms.insertNmsSnmpTrapLog', 'after', $args);
                if(!$trigger_output->toBool()) {
                    $oDB->rollback();
                    return $trigger_output;
                }
            }

            // commit
            $oDB->commit();

            return $output;

        }

        /**
         * @brief Snmp Trap 처리
         **/
        function _procNmsSnmpTrap() {
            Context::setResponseMethod('XMLRPC');
            $args = Context::getRequestVars();
            if($args->act != 'procNmsSnmpTrap') return;

            $args->msg = urldecode($args->msg);

            /* 메시지 포멧을 nms.socket.php에서 "[192.168.0.1:2323]<191>메시지" 로 형식하여 보낸걸 파싱 함 */
            // [192.168.0.1:2323] 안의 정보를 구해서 ip와 port로 나눔
            preg_match_all("!\[([^\>]*)\]!is", $args->msg, $ip);
            $ip = $ip[1][0];
            $ip = explode(':',$ip);

            // []과 <>의 내용을 제거
            $obj->value = preg_replace_callback("!\[([^\>]*)\]!i",array($this, '_code'), $args->msg);

            // RFC 규약의 priority 값을 facility과 severity으로 계산 처리
            $obj->trap = 7;
            $obj->ip_address = $ip[0];
            $obj->ip_port = $ip[1];
            // ip 버전을 구함 IPv4,IPv6
            if($obj->ip_address) $obj->ip_type = (count(explode('.', $obj->ip_address))==4)?4:6;

            if(!preg_match("/(snmptrap)|(start)|(stop)/", $obj->value)) {
                // ASN.1 언어 처리를 위한 함수 호출
                require_once(_XE_PATH_.'modules/nms/libs/asn1/asn1.php');
                require_once(_XE_PATH_.'modules/nms/libs/asn1/stream.php');

                // ASN.1 언어를 해석해서 배열로 저장
                $asn1 = new asn1();
                $value->var = $asn1->_serialize($asn1->procAsn1($obj->value));
                $value->base64 = $obj->value;
                $obj->extra_vars = serialize($value);
                $obj->value = 'NULL';
            }

            // snmptrap 입력
            $this->insertNmsSnmpTrapLog($obj);

            // 메시지를 보낸 ip를 추가
            if($GLOBALS['asn1_octet_value']) $obj->value = "[".$obj->ip_address."]".$GLOBALS['asn1_octet_value'];

            // severity 값의 필터 처리를 위한 호출
            $this->procNmsSeverity($obj, 'snmptrap');
        }

        /**
         * @brief severity 값의 필터 처리를 위한 트리거 함수
         **/
        function procNmsSeverity($args = false, $type = false) {
            if(!$args || !$type) return;

            $oModuleModel = &getModel('module');
            $oNmsModel = &getModel('nms');

            // trigger 호출 (before)
            $output = ModuleHandler::triggerCall('nms.procNmsSeverity', 'before', $args);

            // Severity 정보가 설정된 값을 불러온다.
            $module_config = $oModuleModel->getModuleConfig('nms');
            if($type == 'syslog') $obj->mib_srl = $oModuleModel->getModuleSrlByMid($module_config->syslog_mid);
            elseif($type == 'snmptrap') $obj->mib_srl = $oModuleModel->getModuleSrlByMid($module_config->snmptrap_mid);
            else $obj->mib_srl = $args->mib_srl;

            $obj->not_var_idx = '-3,-4,-5';
            $obj->order_type = 'asc';

            $oMibInfo = $oNmsModel->getNmsMibInfo($obj);
            $oSeverityInfo = $oNmsModel->getNmsExtraVars($obj);

            // 설정된 값이 없으면 수행 종료
            if(!$oSeverityInfo->data) return;

            // 수집된 값과 설정된 조건이 일치하는지를 검사한다.
            foreach($oSeverityInfo->data as $key => $vals) {
                if($type == 'syslog') if($args->severity != $vals->var_idx) continue;
                else if($type == 'snmptrap') if($args->trap != $vals->var_idx) continue;

                $severity = null;
                $severity = unserialize($vals->value);
                $severity->severity = $vals->var_idx;
                $severity->mib_srl = $vals->document_srl;
                $severity->module_srl = $vals->module_srl;

                if(preg_match('/,/', $severity->value)) $severity->value = explode(',', $severity->value);
                if(!is_array($severity->value)) $severity->value = array($severity->value);

                switch($severity->type) {
                    case 0: // 수치값 이상(more)
                        foreach($severity->value as $value)
                            if($args->value >= $value && preg_match("/^[-]?[0-9]+([\.][0-9]+)?$/", $args->value)) $status = true; break;
                    break;
                    case 1: // 수치값 이하(less)
                        foreach($severity->value as $value)
                            if($args->value <= $value && preg_match("/^[-]?[0-9]+([\.][0-9]+)?$/", $args->value)) $status = true; break;
                    break;
                    case 2: // %값 이상(more)
                        foreach($severity->value as $value)
                            if(round(($args->value/$oMibInfo->data->max)*100,0) >= $value && preg_match("/^[-]?[0-9]+([\.][0-9]+)?$/", $args->value)) $status = true; break;
                    break;
                    case 3: // %값 이하(less)
                        foreach($severity->value as $value)
                            if(round(($args->value/$oMibInfo->data->max)*100,0) <= $value && preg_match("/^[-]?[0-9]+([\.][0-9]+)?$/", $args->value)) $status = true; break;
                    break;
                    case 4: // 일치(equal)
                        foreach($severity->value as $value)
                            if($args->value == $value) $status = true; break;
                    break;
                    case 5: // 불일치(not equal)
                        foreach($severity->value as $value)
                            if($args->value != $value) $status = true; break;
                    break;
                    case 6: // 값 존재(notnull)
                            if(isset($args->value)) $status = true; break;
                    break;
                    case 7: // 값 미존재(null)
                            if($args->value == 'NULL' && $args->value != 0) $status = true; break;
                    break;
                    case 8: // 포함(like_prefix)
                        foreach($severity->value as $value)
                            if(preg_match("/".$value."$/", $args->value)) $status = true; break;
                    break;
                    case 9: // 포함(like_tail)
                        foreach($severity->value as $value)
                            if(preg_match("/^".$value."/", $args->value)) $status = true; break;
                    break;
                    case 10: // 포함(like)
                        foreach($severity->value as $value)
                            if(preg_match("/".$value."/", $args->value)) $status = true; break;
                    break;
                    default: break;
                }

                if($status) break;
                if($args->value == 'NULL' && $args->value != 0) continue;

                // 조건과 일치했었던 문제점이 완료 되었을 시에 해당 severity log 를 완료 처리 시킴
                $severity->aware = "'0','2'"; //0 : 초기값, 2 : 인지시킨값
                $severity->order_type = 'asc';
                $oSeverityLog = $oNmsModel->getNmsSeverityLog($severity);

                if($oSeverityLog->data) {
                    foreach($oSeverityLog->data as $key => $val) {
                        $severityStd = unserialize($val->extra_vars);
                        $val->aware = 1;
                        if(!$val->awaredate) $val->awaredate = date('YmdHis');
                        $severityStd->checkdate = date('YmdHis');
                        $val->extra_vars = serialize($severityStd);
                        $this->updateNmsSeverityLog($val);
                    }

                    // 완료 프로세스를 진행한 후 해당 MIB에 대해 최종 완료 했는지를 검사
                    $c_obj->module_srl = $oMibInfo->data->module_srl;
                    $c_obj->mib_srl = $oMibInfo->data->mib_srl;
                    $c_obj->aware = "'0','2'"; //0 : 초기값, 2 : 인지시킨값

                    $oComplete = $oNmsModel->getNmsSeverityLog($c_obj);

                    // 최종 완료인 경우(severity 발생, 인지가 없을 경우) mib 설정에 따른 act 프로세스 진행
                    if(!$oComplete->data) {
                        // document_extra_vars 정보에 act 값이 설정되어있는지 확인
                        $r_obj->mib_srl = $oMibInfo->data->mib_srl;
                        $r_obj->var_idx = -4;
                        $output = $oNmsModel->getNmsExtraVars($r_obj);

                        if(!$output->data) continue; // 없으면 다음 수행으로 넘김

                        foreach($output->data as $key => $val)
                            $complete_act = unserialize($val->value);

                        $severity->act = $complete_act->act;
                        $severity->act_value = $complete_act->act_value;
                        $severity->act_path = $complete_act->act_path;

                        // 선택된 act이 있을 경우 act 함수로 모든 수집가능한 자료를 담아 보냅니다.
                        $acts = $oNmsModel->getNmsTotalInfo($oMibInfo->data);
                        $acts->oArgsInfo = $args;
                        $acts->oSeverityInfo = $severity;
                        $acts->oSeverityLog->mib_srl = $args->mib_srl;
                        $acts->oSeverityLog->severity = $severity->severity;
                        $acts->oSeverityLog->aware = 1;
                        $acts->oSeverityLog->value = $args->value;
                        $acts->oSeverityLog->regdate = date('YmdHis');
                        $acts->oSeverityLog->extra_vars = $severityStd;

                        $this->procNmsAct($acts);
                    }
                }
            }

            // 일치하는 조건이 있다면 act 프로세스를 수행
            if($status) {

                // 조건과 일치했다면 Severity log에 같은 건으로 등록된(완료 처리가 안된) 값이 있는지 체크
                $severity->aware = "'0','2'"; //0 : 초기값, 2 : 인지시킨값
                $severity->order_type = 'asc';
                $oSeverityLog = $oNmsModel->getNmsSeverityLog($severity);

                // Severity log에 같은 건으로 등록된 사항이 있다면 act 수행 준비 조건과 비교
                $event = $severity->event;
                if(!$event->sec) $event->sec = false;
                if(!$event->count) $event->count = false;

                if($oSeverityLog->data) {
                    // act 수행 조건 기준을 만족할 경우만 수행
                    if($event->type != null && $event->sec != null) {

                        foreach($oSeverityLog->data as $key => $val) {
                            $severityStd = unserialize($val->extra_vars);
                            $severityStd->DiffTime = strtotime(date('YmdHis')) - strtotime($severityStd->checkdate);

                            if($event->sec <= $severityStd->DiffTime) {
                                if(($event->count > $severityStd->count) && ($val->aware == 0) || !$event->count) {
                                    /* 횟수가 증가함에 따라 최종적인 값을 사용하려면 아래의 주석을 해제 */
                                    //$val->value = $args->value;
                                    $severityStd->checkdate = date('YmdHis');
                                    $severityStd->count++;

                                    // act 함수로 보내기 위한 모든 수집가능한 자료를 담습니다.
                                    $acts = $oNmsModel->getNmsTotalInfo($oMibInfo->data);
                                    $acts->oArgsInfo = $args;
                                    $acts->oSeverityInfo = $severity;
                                    $acts->oSeverityLog = $val;
                                    $acts->oSeverityLog->extra_vars = $severityStd;

                                    $val->extra_vars = serialize($severityStd);
                                    // act 횟수와 수집 데이터, 수집 시간 등을 Severity Log에 업데이트 합니다.
                                    $this->updateNmsSeverityLog($val);
                                    // act 함수 자료를 담아 보냅니다.
                                    $this->procNmsAct($acts);
                               }
                            }
                        }
                    }
                } else {
                    // Severity log에 같은 건으로 등록된 사항이 없다면 신규 등록

                    if($event->type == 4) {
                        $severity->aware = 1;
                        $severity->awaredate = date('YmdHis');
                    } else $severity->aware = 0;
                    $severity->extra_vars->checkdate = date('YmdHis');
                    $severity->extra_vars->count = 0;

                    // 조건과 일치 시 바로 act 수행일 경우 act진행과 함께 진행횟수를 1증가
                    if(in_array($event->type, array(0, 2)) && $event->sec != null) {
                        $severity->extra_vars->count++;

                        // act 함수로 보내기 위한 모든 수집가능한 자료를 담습니다.
                        $acts = $oNmsModel->getNmsTotalInfo($oMibInfo->data);
                        $acts->oArgsInfo = $args;
                        $acts->oSeverityInfo = $severity;
                        $acts->oSeverityLog->mib_srl = $args->mib_srl;
                        $acts->oSeverityLog->severity = $severity->severity;
                        $acts->oSeverityLog->aware = $severity->aware;
                        $acts->oSeverityLog->value = $args->value;
                        $acts->oSeverityLog->regdate = date('YmdHis');
                        $acts->oSeverityLog->extra_vars = $severity->extra_vars;
                    }

                    $severity->extra_vars = serialize($severity->extra_vars);

                    $val = $severity;
                    $val->value = $args->value;
                    // act 횟수와 수집 데이터, 수집 시간 등을 Severity Log에 업데이트 합니다.
                    $this->insertNmsSeverityLog($val);
                    // act 함수 자료를 담아 보냅니다.
                    $this->procNmsAct($acts);
                }

            // 일치하는 조건이 없다면(Severity에 설정했던 문제가 모두 해결 되면) act를 보낼지 여부
            } else {

            }

            // trigger 호출 (after)
            $output = ModuleHandler::triggerCall('nms.procNmsSeverity', 'after', $args);

            return false;
        }

        /**
         * @brief Severity Level에 설정된 act 동작을 수행
         **/
        function procNmsAct($args = false) {
            if(!$args) return;
            $oModuleModel = &getModel('module');
            $oNmsModel = &getModel('nms');
            require_once(_XE_PATH_.'modules/nms/nms.act.php');
            $oNmsAct = new nmsAct();

            /* 넘어온 관련 값을 정리 함 */
            // 각종 serialize의 정보를 초기화 unserialize 시킴
            $args->extra_vars = unserialize($args->extra_vars);
            $args->nms_extra_vars = unserialize($args->nms_extra_vars);
            $args->nms_group_extra_vars = unserialize($args->nms_group_extra_vars);
            $args->nms_mib_extra_vars = unserialize($args->nms_mib_extra_vars);
            $args->oArgsInfo->extra_vars = unserialize($args->oArgsInfo->extra_vars);
            // Severity 비교 조건 값이 배열일 경우 값을 합함
            if(is_array($args->oSeverityInfo->value))
                $args->oSeverityInfo->value = implode(',', $args->oSeverityInfo->value);

            // trigger 호출 (before)
            $output = ModuleHandler::triggerCall('nms.procNmsAct', 'before', $args);

            foreach($args->oSeverityInfo->act as $act => $val) {
                $args->act_value = $args->oSeverityInfo->act_value->{$act};
                $args->act_form = $args->oSeverityInfo->act_form->{$act};
                $args->act_path = $args->oSeverityInfo->act_path->{$act};

                $obj->module_srl = $obj->mib_srl = $args->module_srl;
                $obj->var_idx = -3;
                $act_form = $oNmsModel->getNmsExtraVars($obj);
                foreach($act_form->data as $key => $val)
                    $act_form->value = unserialize($val->value);

                if(!$args->act_value) $args->act_value = $act_form->value->{$act}->value;
                if(!$args->act_value) continue;

                if(!$args->act_form && !$args->act_path) {
                    $args->act_form = $act_form->value->act_form->{$act};
                    $args->act_path = $act_form->value->act_path->{$act};
                }

                // nms.act.php에 $act에 해당하는 함수가 있는지 체크 후 해당 함수 호출
                $args->act_name = $act;
                if(method_exists($oNmsAct, $act)) $oNmsAct->{$act}($args);
            }

            // trigger 호출 (after)
            $output = ModuleHandler::triggerCall('nms.procNmsAct', 'after', $args);
        }

        /**
         * @brief 메일 보내기 전 정보 초기화
         **/
        function sendMail($args = false) {
            $oModuleModel = &getModel('module');
            $oNms = $oModuleModel->getModuleConfig('nms');

            $mail = new Mail;
            $mail->smtp_secure = ($oNms->smtp_config->smtp_secure)?$oNms->smtp_config->smtp_secure:'tcp';
            $mail->smtp_server = $oNms->smtp_config->smtp_server;
            $mail->smtp_port = $oNms->smtp_config->smtp_port;
            $mail->user = $oNms->smtp_config->smtp_user;
            $mail->pass = $oNms->smtp_config->smtp_pass;

            $mail->setSender($args->sender->name,$args->sender->email);
            $mail->setTitle($args->title);
            $mail->setContent($args->content);
            $mail->setContentType('html');

            // 첨부파일 정리
            if(is_array($args->attach)) {
                foreach($args->attach as $key => $attach) {
                    $mail->attach[] = $attach;
                }
            }

            // 메일 보냄
            foreach($args->receiptor as $key => $receiptors) {
                $mail->setReceiptor($receiptors->name, $receiptors->email);
                $this->_sendMail($mail);
            }
        }

        /**
         * @brief 메일 보내기
         **/
        function _sendMail($mail) {
            // 영문이외의 문자 이름이 출력되도록 함
            $sender_email = sprintf("%s <%s>", '=?utf-8?b?'.base64_encode($mail->sender_name).'?= ', $mail->sender_email);
            $receiptor_email = sprintf("%s <%s>", '=?utf-8?b?'.base64_encode($mail->receiptor_name).'?= ', $mail->receiptor_email);

            $boundary = "----==".uniqid(rand(),true); // 바운드를 초기화한다
            $eol = $GLOBALS['_qmail_compatibility'] == 'Y' ? "\n" : "\r\n";

            $headers = sprintf(
                "MIME-Version: 1.0".$eol.
                "Content-Type: Multipart/mixed;".$eol."\tboundary=\"%s\"".$eol.
                "Subject: %s".$eol.
                "From: %s".$eol.
                "To: %s".$eol.$eol,
                $boundary,
                $mail->getTitle(),
                $sender_email,
                $receiptor_email
            );

            $body = sprintf(
                "--%s".$eol.
                "Content-Type: text/html; charset=utf-8".$eol.
                "Content-Transfer-Encoding: base64".$eol.$eol.
                "%s".$eol.$eol,
                $boundary,
                $mail->getHTMLContent()
            );

            // 첨부파일
            if(is_array($mail->attach)) {
                foreach($mail->attach as $key => $path) {
                   $name = basename($path->filename);
                   $file = FileHandler::readFile($path->fileurl);

                   $fileBody = sprintf(
                        "--%s".$eol.
                        "Content-Type: application/octet-stream".$eol.
                        "Content-Transfer-Encoding: base64".$eol.
                        "Content-Disposition: attachment; filename=\"%s\"".$eol.$eol.
                        "%s",
                        $boundary,
                        $name,
                        chunk_split(base64_encode($file))
                    );

                    $body .= $fileBody;
                }
            }

            // 인증방식으로 메일을 보냄
            if($smtp_socket = @fsockopen($mail->smtp_secure."://".$mail->smtp_server, $mail->smtp_port, $errno, $errstr, 5)) {
                $this->_getMail($smtp_socket);
                @fputs($smtp_socket, 'HELO '.$mail->smtp_secure."://".$mail->smtp_server.$eol);
                $this->_getMail($smtp_socket);
                @fputs($smtp_socket, 'AUTH LOGIN'.$eol);
                $this->_getMail($smtp_socket);
                @fputs($smtp_socket, base64_encode($mail->user).$eol);
                $this->_getMail($smtp_socket);
                @fputs($smtp_socket, base64_encode($mail->pass).$eol);
                $this->_getMail($smtp_socket);
                @fputs($smtp_socket, 'MAIL From: <'.$mail->sender_email.'>'.$eol);
                $this->_getMail($smtp_socket);
                @fputs($smtp_socket, 'RCPT To: <'.$mail->receiptor_email.'>'.$eol);
                $this->_getMail($smtp_socket);
                @fputs($smtp_socket, 'DATA'.$eol);
                $this->_getMail($smtp_socket);
                $content = sprintf(
                    "%s".$eol.
                    "%s".$eol.
                    ".".$eol,
                    $headers,
                    $body
                );
                @fputs($smtp_socket, $content);
                $this->_getMail($smtp_socket);
                @fputs($smtp_socket, 'QUIT'.$eol);
                @fclose($smtp_socket);
            }
        }

        /**
         * @brief 상대방의 응답을 기다립니다.(gmail등 인증 방식 사용시)
         **/
        function _getMail($socket = null) {
            if(!$socket) return;
            $i = 0;
            $response = '-';
            while($response == '-' && $i<10) {
                $response = @fgets($socket, 256);
                if($response) $response = substr($response,3,1);
                else return;

                $i++;
            }
        }

        /**
         * @brief aware의 상태 정보를 변경
         **/
        function procNmsChangeAware() {
            $args = Context::getRequestVars();

            // 완료 처리시 완료시간을 등록
            if($args->aware == 1) $args->awaredate = date('YmdHis');

            $this->updateNmsSeverityLog($args);

            $this->setMessage("success_updated");
        }

        /**
         * @brief SMTP 서버가 동작하는지 체크
         **/
        function procNmsCheckSmtp() {
            $args = Context::getRequestVars();

            if($smtp_socket = @fsockopen($args->smtp_secure."://".$args->smtp_server, $args->smtp_port, $errno, $errstr, 5)) {
                $message = @fgets($smtp_socket, 512);
                @fclose($smtp_socket);
            }

            if(!preg_match("/SMTP/", $message)) return new Object(-1,"msg_smtp_checks_error");

            $this->setMessage("msg_smtp_checks_complete");
        }

        /**
         * @brief Syslog 정보 삭제
         **/
        function deleteNmsSyslogLog($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // trigger 호출 (before) : Syslog Log가 삭제될때 연동될 내용을 위해 선언해 둠
            $output = ModuleHandler::triggerCall('nms.deleteNmsSyslogLog', 'before', $args);
            if(!$output->toBool()) return $output;

            $output = executeQuery('nms.deleteNmsSyslogLog', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // trigger 호출 (after) : Syslog Log가 삭제될때 연동될 내용을 위해 선언해 둠
            if($output->toBool()) {
                $trigger_output = ModuleHandler::triggerCall('nms.deleteNmsSyslogLog', 'after', $args);
                if(!$trigger_output->toBool()) {
                    $oDB->rollback();
                    return $trigger_output;
                }
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief Snmp Trap 정보 삭제
         **/
        function deleteNmsSnmpTrapLog($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // trigger 호출 (before) : Snmp Trap Log가 삭제될때 연동될 내용을 위해 선언해 둠
            $output = ModuleHandler::triggerCall('nms.deleteNmsSnmpTrapLog', 'before', $args);
            if(!$output->toBool()) return $output;

            $output = executeQuery('nms.deleteNmsSnmpTrapLog', $args);

            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // trigger 호출 (after) : Snmp Trap Log가 삭제될때 연동될 내용을 위해 선언해 둠
            if($output->toBool()) {
                $trigger_output = ModuleHandler::triggerCall('nms.deleteNmsSnmpTrapLog', 'after', $args);
                if(!$trigger_output->toBool()) {
                    $oDB->rollback();
                    return $trigger_output;
                }
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief Host가 삭제 될때 해당하는 MIB도 삭제를 하기 위한 트리거 함수
         **/
        function triggerDeleteNmsMib(&$args) {
            $this->deleteNmsMib($args);
        }

        /**
         * @brief MIB가 삭제될때 해당 Severity도 삭제를 하기 위한 트리거 함수
         **/
        function triggerDeleteNmsSeverity(&$args) {
            if($args->mib_srl) $args->document_srl = $args->mib_srl;
            $this->deleteNmsExtraVars($args);

            if($args->log_delete=='Y') {
                if($args->mib_srl) $args->mib_srl = (is_array($args->mib_srl))?implode(',', $args->mib_srl):$args->mib_srl;
                $this->deleteNmsSeverityLog($args);
            }
        }

        /**
         * @brief Severity Log 정보 삭제
         **/
        function deleteNmsSeverityLog($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // trigger 호출 (before) : Severity Log가 삭제될때 연동될 내용을 위해 선언해 둠
            $output = ModuleHandler::triggerCall('nms.deleteNmsSeverityLog', 'before', $args);
            if(!$output->toBool()) return $output;

            $output = executeQuery('nms.deleteNmsSeverityLog', $args);
            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // trigger 호출 (after) : Severity Log가 삭제될때 연동될 내용을 위해 선언해 둠
            if($output->toBool()) {
                $trigger_output = ModuleHandler::triggerCall('nms.deleteNmsSeverityLog', 'after', $args);
                if(!$trigger_output->toBool()) {
                    $oDB->rollback();
                    return $trigger_output;
                }
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief Snmp log 삭제
         **/
        function deleteNmsSnmpLog($args) {
            // begin transaction
            $oDB = &DB::getInstance();
            $oDB->begin();

            // trigger 호출 (before) : Snmp Log가 삭제될때 연동될 내용을 위해 선언해 둠
            $output = ModuleHandler::triggerCall('nms.deleteNmsSnmpLog', 'before', $args);
            if(!$output->toBool()) return $output;

            $output = executeQuery('nms.deleteNmsSnmpLog', $args);
            if(!$output->toBool()) {
                $oDB->rollback();
                return $output;
            }

            // trigger 호출 (after) : Snmp Log가 삭제될때 연동될 내용을 위해 선언해 둠
            if($output->toBool()) {
                $trigger_output = ModuleHandler::triggerCall('nms.deleteNmsSnmpLog', 'after', $args);
                if(!$trigger_output->toBool()) {
                    $oDB->rollback();
                    return $trigger_output;
                }
            }

            // commit
            $oDB->commit();

            return $output;
        }

        /**
         * @brief Graph Cache 등록
         **/
        function insertGraphCache($args) {
            $args->extra_vars = unserialize($args->extra_vars);
            $args->extra_vars->graphCache->{$args->mkey} = true;
            $args->extra_vars = serialize($args->extra_vars);
            $this->updateNmsMib($args);
        }

        /**
         * @brief OID numeric을 MIB 이름으로 변환합니다.
         **/
        function setOidByMib($args = null, $mode = null) {
            if(!$args) return;

            $path = _XE_PATH_.'modules/nms/';
            $smis_path = $path.'tpl/smis/';
            $output = @FileHandler::readDir($smis_path);

            if($output) {
                foreach($output as $key => $val) {
                    $cache_file = sprintf('./files/cache/nms/smi/%s.cache.php', preg_replace("/\.xml$/", "", $val));
                    @include($cache_file);
                }

                uksort($smi_info->oid, 'strnatcasecmp');
            }

            $cache_file = sprintf('./files/cache/nms/numeric/%s.cache.php', 'mib');

            $buff = "";

            if($mode == 'insert') {
                if(file_exists($cache_file)) {
                    @include($cache_file);
                    foreach($mib as $key => $val) $buff .= sprintf('$mib[\'%s\'] = \'%s\';', $key, $val);
                }
            } else {
                if(file_exists($cache_file)) return;
            }

            foreach($args as $key => $val) {
                if($mib[$key]) continue;

                $oid_each = explode('.', $key);

                $mib_value = "";
                $numeric_fullname = "";
                foreach($oid_each as $numeric) {
                    if($numeric == null) continue;
                    $numeric_fullname .= ".".$numeric;

                    if($smi_info->oid[$numeric_fullname]) $mib_value .= ".".$smi_info->oid[$numeric_fullname];
                    else $mib_value .= ".".$numeric;
                }

                $buff .= sprintf('$mib[\'%s\'] = \'%s\';', $key, $mib_value);
            }

            $buff = '<?php if(!defined("__ZBXE__")) exit(); '.$buff.' ?>';
            FileHandler::writeFile($cache_file, $buff);

            return;
        }

        /**
         * @brief MIB 이름을 OID numeric 번호로 변환합니다.
         **/
        function setMibByOid($args = null, $mode = null) {
            if(!$args) return;

            $path = _XE_PATH_.'modules/nms/';
            $smis_path = $path.'tpl/smis/';
            $output = @FileHandler::readDir($smis_path);

            if($output) {
                foreach($output as $key => $val) {
                    $cache_file = sprintf('./files/cache/nms/smi/%s.cache.php', preg_replace("/\.xml$/", "", $val));
                    @include($cache_file);
                }

                uksort($smi_info->oid, 'strnatcasecmp');
            }

            $cache_file = sprintf('./files/cache/nms/numeric/%s.cache.php', 'mib');

            if(file_exists($cache_file)) @include($cache_file);

            $oid_file = sprintf('./files/cache/nms/numeric/%s.cache.php', 'oid');

            $buff = "";

            if($mode == 'insert') {
                if(file_exists($oid_file)) {
                    @include($oid_file);
                    foreach($oid as $key => $val) $buff .= sprintf('$oid[\'%s\'] = \'%s\';', $key, $val);
                }
            } else {
                if(file_exists($oid_file)) return;
            }

            foreach($mib as $keys => $vals) {
                if($oid[$vals]) continue;

                $mib_each = explode('.', $vals);

                $oid_value = "";
                foreach($mib_each as $name) {

                    if($name == null) continue;

                    if(preg_match("/^[-]?[0-9]+([\.][0-9]+)?$/", $name)) {
                        $oid_value .= ".".$name;
                    }

                    $i++;
                    foreach($smi_info->oid as $key => $val) {

                        $oid_numeric = explode('.', $key);

                        if(count($oid_numeric)-1 != $i) continue;

                        if($val == $name) {
                            $oid_value .= ".".$oid_numeric[$i];
                            break;
                        }
                    }
                }

                $buff .= sprintf('$oid[\'%s\'] = \'%s\';', $vals, $oid_value);
                $i=0;
            }

            $buff = '<?php if(!defined("__ZBXE__")) exit(); '.$buff.' ?>';
            $cache_file = sprintf('./files/cache/nms/numeric/%s.cache.php', 'oid');
            FileHandler::writeFile($cache_file, $buff);

            return;
        }

        /**
         * @brief Twitter로 OAuth 인증하기
         **/
        function getNmsTwitterOauth() {
            session_start();
            $oModuleModel = &getModel('module');
            $oMemberModel = &getModel('member');
            $oMemberController = &getController('member');
            $oNms = $oModuleModel->getModuleConfig('nms');

            $template_path = sprintf('%stpl/',$this->module_path);
            $this->setTemplatePath($template_path);
            $this->setLayoutFile('popup_layout');
            $this->setTemplateFile('twitter');

            require_once(_XE_PATH_.'modules/nms/libs/twitteroauth/twitteroauth.php');

            if(!$oNms->twitter_config->consumer_key || !$oNms->twitter_config->consumer_secret) {
                Context::set('fail',true);
                return;
            }

            define('CONSUMER_KEY', $oNms->twitter_config->consumer_key);
            define('CONSUMER_SECRET', $oNms->twitter_config->consumer_secret);
            define('OAUTH_CALLBACK', Context::get('request_uri')."?module=nms&act=getNmsTwitterOauth");

            if(Context::get('member_srl')) {
                $_SESSION['twitter_member_srl'] = Context::get('member_srl');
            }

            $obj->member_srl = $_SESSION['twitter_member_srl'];
            $output = executeQuery('member.getMemberInfoByMemberSrl', $obj);
            if(!$output->data) {
                Context::set('fail',true);
                return;
            }

            // 이미 값이 존재할 경우 실행 중단
            $member_info = $output->data;
            $member_info->extra_vars = unserialize($member_info->extra_vars);
            if($member_info->extra_vars->twitter_oauth_token && $member_info->extra_vars->twitter_oauth_token_secret) {
                //Context::set('duplicate',true);
                //return;
            }

            if(Context::get('oauth_token') && Context::get('oauth_verifier')) {
                /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
                $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, Context::get('oauth_token'), Context::get('oauth_verifier'));
                /* Request access tokens from twitter */
                $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

                $member_info->extra_vars->twitter_oauth_token = $access_token['oauth_token'];
                $member_info->extra_vars->twitter_oauth_token_secret = $access_token['oauth_token_secret'];
                $member_info->extra_vars = serialize($member_info->extra_vars);
                unset($member_info->password);
                $oMemberController->updateMember($member_info);
                Context::set('oauth',true);
                unset($_SESSION['twitter_member_srl']);
                unset($_SESSION['oauth_token']);
                unset($_SESSION['oauth_token_secret']);
                return;
            }

            /* Build TwitterOAuth object with client credentials. */
            $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

            /* Get temporary credentials. */
            $request_token = $connection->getRequestToken(OAUTH_CALLBACK);

            $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
            $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

            switch ($connection->http_code) {
              case 200:
                $url = $connection->getAuthorizeURL($token);
                Context::set('url',$url);
                break;
              default: Context::set('error',true); break;
            }
        }

        /**
         * @brief 회원 정보가 수정 될때 Twitter OAuth 정보 보관
         **/
        function triggerUpdateMemberBefore(&$args) {
            $oMemberModel = &getModel('member');
            $oMemberController = &getController('member');
            $member_info = $oMemberModel->getMemberInfoByMemberSrl($args->member_srl);
            if($member_info->twitter_oauth_token) $GLOBALS['twitter_oauth_token'] = $member_info->twitter_oauth_token;
            if($member_info->twitter_oauth_token_secret) $GLOBALS['twitter_oauth_token_secret'] = $member_info->twitter_oauth_token_secret;
        }

        /**
         * @brief 회원 정보가 수정 될때 Twitter OAuth 정보 저장
         **/
        function triggerUpdateMemberAfter(&$args) {
            $oMemberModel = &getModel('member');
            $oMemberController = &getController('member');

            $args->extra_vars = unserialize($args->extra_vars);
            if($GLOBALS['twitter_oauth_token']) $args->extra_vars->twitter_oauth_token = $GLOBALS['twitter_oauth_token'];
            if($GLOBALS['twitter_oauth_token_secret']) $args->extra_vars->twitter_oauth_token_secret = $GLOBALS['twitter_oauth_token_secret'];
            if(!$args->extra_vars->twitter_oauth_token && !$args->extra_vars->twitter_oauth_token_secret) return;
            $args->extra_vars = serialize($args->extra_vars);

            $output = executeQuery('member.updateMember', $args);

            unset($GLOBALS['twitter_oauth_token']);
            unset($GLOBALS['twitter_oauth_token_secret']);
        }

        /**
         * @brief ACT 수행을 LOG로 기록합니다. (예정)
         **/
        function triggerLogNmsAct(&$args) {
            return;
        }
    }
?>