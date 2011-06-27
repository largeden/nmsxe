<?php
    /**
     * @class  nmsModel
     * @author largeden (developer@nmsxe.com)
     * @brief  nmsXE Model class
     **/

    class nmsModel extends nms {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 모듈과 등록되는 Host Ip에 대한 정보를 리스트 출력
         **/
        function getNmsHostList($args = null) {
            $output = executeQueryArray('nms.getNmsHostList', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data = array();

            return $output;
        }

        /**
         * @brief 모듈번호로 Host 정보를 출력
         **/
        function getNmsHostInfo($module_srl = null) {
            $args->module_srl = $module_srl;
            $output = executeQuery('nms.getNmsHostInfo', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data = array();

            return $output->data;
        }

        /**
         * @brief 모듈에 해당하는 MIB 정보를 출력
         **/
        function getNmsHost($args = null) {
            $output = executeQueryArray('nms.getNmsHost', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data = array();

            return $output->data;
        }

        /**
         * @brief 그룹 번호로 그룹 정보를 리스트 출력
         **/
        function getNmsGroupList($args = null) {
            $output = executeQueryArray('nms.getNmsGroupList', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data = array();

            return $output;
        }

        /**
         * @brief 그룹 번호로 그룹 정보를 출력
         **/
        function getNmsGroupInfo($group_srl = null) {
            $args->group_srl = $group_srl;
            $output = executeQuery('nms.getNmsGroupInfo', $args);
            if(!$output->toBool()) return $output;

            return $output->data;
        }

        /**
         * @brief 그룹 정보의 gid를 HOST 등록 과정에서 출력
         **/
        function getNmsGroup($args = null) {
            $output = executeQuery('nms.getNmsGroupInfo', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) return array();

            return $output->data;
        }

        /**
         * @brief MIB 정보를 출력
         **/
        function getNmsMibList($args = null) {
            $output = executeQueryArray('nms.getNmsMibList', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data = array();

            return $output;
        }

        /**
         * @brief 모듈에 해당하는 MIB 정보를 출력
         **/
        function getNmsMib($args = null) {
            $output = executeQueryArray('nms.getNmsMib', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data = array();

            return $output->data;
        }

        /**
         * @brief MIB번호, 모듈번호로 MIB 정보를 출력
         **/
        function getNmsMibInfo($args = null) {
            $output = executeQuery('nms.getNmsMibInfo', $args);
            if(!$output->toBool()) return $output;

            return $output;
        }

        /**
         * @brief MIB 번호로 모든 NMS 정보를 출력
         **/
        function getNmsTotalInfo($args = null) {
            $oModuleModel = &getModel('module');
            if(!in_array($args->order_type, array('asc','desc'))) $args->order_type = 'asc';
            $output = executeQuery('nms.getNmsTotalInfo', $args);
            if(!$output->toBool()) return $output;
            $module_info = $oModuleModel->addModuleExtraVars($output->data);
            return $module_info;
        }

        /**
         * @brief Severity 설정 정보(ExtraVars)를 출력
         **/
        function getNmsExtraVars($args = null) {
            if(!in_array($args->order_type, array('asc','desc'))) $args->order_type = 'asc';
            $output = executeQueryArray('nms.getNmsExtraVars', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data = array();

            return $output;
        }

        /**
         * @brief Severity 정보를 출력
         **/
        function getNmsSeverityLog($args = null) {
            if(!in_array($args->order_type, array('asc','desc'))) $args->order_type = 'asc';
            $output = executeQueryArray('nms.getNmsSeverityLog', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data = array();

            return $output;
        }

        /**
         * @brief Syslog List 출력
         **/
        function getNmsSyslogList($args = null) {
            $args->order_type = (in_array($args->order_type, array('asc','desc')))?$args->order_type:'desc';

            switch($args->search_target) {
                case 'priority': $args->s_priority = $args->search_keyword; break;
                case 'facility': $args->s_facility = $args->search_keyword; break;
                case 'severity': $args->s_severity = $args->search_keyword; break;
                case 'ip_address': $args->s_ip_address = $args->search_keyword; break;
                case 'ip_port': $args->s_ip_port = $args->search_keyword; break;
                case 'value': $args->s_value = $args->search_keyword; break;
                case 'regdate': $args->s_regdate = $args->search_keyword; break;
                default: break;
            }

            $output = executeQueryArray('nms.getNmsSyslogList', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data = array();

            return $output;
        }

        /**
         * @brief Snmp Trap List 출력
         **/
        function getNmsSnmpTrapList($args = null) {
            $args->order_type = (in_array($args->order_type, array('asc','desc')))?$args->order_type:'desc';

            switch($args->search_target) {
                case 'trap': $args->s_trap = $args->search_keyword; break;
                case 'ip_address': $args->s_ip_address = $args->search_keyword; break;
                case 'ip_port': $args->s_ip_port = $args->search_keyword; break;
                case 'regdate': $args->s_regdate = $args->search_keyword; break;
                default: break;
            }

            $output = executeQueryArray('nms.getNmsSnmpTrapList', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data = array();

            return $output;
        }

        /**
         * @brief Severity List 출력
         **/
        function getNmsSeverityList($args = null) {
            $args->order_type = (in_array($args->order_type, array('asc','desc')))?$args->order_type:'desc';

            switch($args->search_target) {
                case 'mid': $args->s_mid = $args->search_keyword; break;
                case 'mmid': $args->s_mmid = $args->search_keyword; break;
                case 'severity': $args->s_severity = $args->search_keyword; break;
                case 'value': $args->s_value = $args->search_keyword; break;
                case 'aware': $args->s_aware = $args->search_keyword; break;
                case 'regdate': $args->s_regdate = $args->search_keyword; break;
                case 'awaredate': $args->s_awaredate = $args->search_keyword; break;
                default: break;
            }

            switch($args->sort_index) {
                case 'module_srl': $args->sort_index = 'modules.'.$args->sort_index; break;
                case 'mib_srl': $args->sort_index = 'nms_mib.'.$args->sort_index; break;
                case 'severity': $args->sort_index = 'nms_severity_log.'.$args->sort_index; break;
                case 'value': $args->sort_index = 'nms_severity_log.'.$args->sort_index; break;
                case 'aware': $args->sort_index = 'nms_severity_log.'.$args->sort_index; break;
                case 'awaredate': $args->sort_index = 'nms_severity_log.'.$args->sort_index; break;
                case 'regdate': $args->sort_index = 'nms_severity_log.'.$args->sort_index; break;
                default: break;
            }

            $output = executeQueryArray('nms.getNmsSeverityList', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data = array();

            return $output;
        }

        /**
         * @brief 선택된 MIB Group 정보를 출력
         **/
        function getNmsMibGroupInfo($args = null) {
            $output = executeQueryArray('nms.getNmsMibInfo', $args);
            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data = array();

            foreach($output->data as $key => $attribute) {
                $group_name = $attribute->group_name;
                $attribute->type = 'group_name';

                if(!$GLOBALS['XE_NMS_MIB_GROUP_NAME'][$group_name]) {
                    $oNmsGroup = null;
                    $oNmsGroup = new nmsItem();
                    $oNmsGroup->setAttribute($attribute);

                    $oNmsGroup->variables['mib_title'] = $attribute->mib_title;
                    $oNmsGroup->variables['max'] = $attribute->max;
                    $oNmsGroup->variables['description'] = $attribute->description;

                    $oMibGroup[$key] = $GLOBALS['XE_NMS_MIB_GROUP_NAME'][$group_name] = $oNmsGroup;
                }
            }

            return $oMibGroup[0];
        }

        /**
         * @brief MIB Group 정보를 출력
         **/
        function getNmsMibGroup($args = null) {
            if(!$args->order_type) $args->order_type = 'asc';

            switch($args->search_target) {
                case 'mmid': $args->s_mmid = $args->search_keyword; break;
                case 'mib_title': $args->s_mib_title = $args->search_keyword; break;
                case 'mib': $args->s_mib = $args->search_keyword; break;
                case 'group_name': $args->s_group_name = $args->search_keyword; break;
                default: break;
            }

            $output = executeQueryArray('nms.getNmsMibGroup', $args);
            if(!$output->toBool()) return new Object(-1, "msg_invalid_group_name");
            if(!$output->data) $output->data = array();

            $obj->module_srl = $args->module_srl;

            foreach($output->data as $key => $attribute) {
                $group_name = $attribute->group_name;
                $attribute->type = 'group_name';
                if(!$GLOBALS['XE_NMS_MIB_GROUP_LIST'][$key]) {
                    $oNmsGroup = null;
                    $oNmsGroup = new nmsItem();
                    $oNmsGroup->setAttribute($attribute);
                    $obj->group_name = $attribute->group_name;
                    $oMib = $this->getNmsMib($obj);
                    foreach($oMib as $val) $mib = $val;

                    $oNmsGroup->variables['module_srl'] = $obj->module_srl;
                    $oNmsGroup->variables['mib_title'] = $mib->mib_title;
                    $oNmsGroup->variables['max'] = $mib->max;
                    $oNmsGroup->variables['description'] = $mib->description;

                    $oMibGroup[$key] = $GLOBALS['XE_NMS_MIB_GROUP_LIST'][$key] = $oNmsGroup;
                }
            }

            $mibgroup_info = $output;
            $mibgroup_info->data = $oMibGroup;

            return $mibgroup_info;
        }

        /**
         * @brief SNMP로 수집된 내용을 출력
         **/
        function getNmsSnmpLog($args = null) {
            $output = executeQuery('nms.getNmsSnmpLog', $args);
            if(!$output->toBool()) return $output;
            if(!is_array($output->data)) $output->data = array($output->data);

            return $output;
        }

        /**
         * @brief SNMP로 수집된 내용을 출력
         **/
        function getNmsLastSnmpLog($args = null) {
            $args->order_type = 'desc';
            $args->list_count = 1;

            $output = executeQuery('nms.getNmsLastSnmpLog', $args);
            if(!$output->toBool()) return $output;

            return $output->data;
        }

        /**
         * @brief MIB의 ID인 mmid 이름으로 SNMP의 정보를 출력
         **/
        function getNmsMmidSnmp($mmid = null, $list_count = null) {
            $args->mmid = $mmid;
            $oMib = $this->getNmsMibInfo($args);
            $args->mib_srl = $oMib->data->mib_srl;

            $args->sort_index = 'snmp_srl';
            $args->page = Context::get('page');
            $args->list_count = $list_count;
            $args->page_count = 10;

            $output = $this->getNmsSnmpLog($args);

            return $output->data;
        }

        /**
         * @brief Graph Cache가 있는지 확인
         **/
        function getGraphCache($mKey) {
            if(Context::get('group_name') || Context::get('mmid')) {
                $oModuleModel = &getModel('module');

                $module_info = $oModuleModel->getModuleInfoByMid(Context::get('mid'));
                $obj->module_srl = $module_info->module_srl;
                if(Context::get('group_name')) $obj->group_name = Context::get('group_name');
                elseif(Context::get('mmid')) $obj->mmid = Context::get('mmid');

                $output = $this->getNmsMib($obj);

                foreach($output as $mib) {
                    $mib->extra_vars = unserialize($mib->extra_vars);
                    if($mib->extra_vars->graphCache->{$mKey}) return true;
                }
            }

            return false;
        }

        /**
         * @brief 수집된 SNMP LOG를 분석하여 통계로 만듬
         **/
        function getNmsSnmpSummary($type = null, $mib_srl = null, $regdate = null) {
            if(!$type || !$mib_srl || !$regdate) return;

            switch($type) {
                case 'normal':
                    $query = 'getNmsSnmpStatsNormal';
                    $delay = 300;
                    $total = 396;
                break;
                case 'mini':
                    $query = 'getNmsSnmpStatsMini';
                    $delay = 300;
                    $total = 66;
                break;
                case '1hour':
                    $query = 'getNmsSnmpStats1hour';
                    $delay = 15;
                    $total = 240;
                break;
                case '3hour':
                    $query = 'getNmsSnmpStats3hour';
                    $delay = 30;
                    $total = 360;
                break;
                case '6hour':
                    $query = 'getNmsSnmpStats6hour';
                    $delay = 60;
                    $total = 360;
                break;
                case '12hour':
                    $query = 'getNmsSnmpStats12hour';
                    $delay = 180;
                    $total = 240;
                break;
                case 'day':
                    $query = 'getNmsSnmpStatsDay';
                    $delay = 300;
                    $total = 288;
                break;
                case 'week':
                    $query = 'getNmsSnmpStatsWeek';
                    $delay = 1800;
                    $total = 336;
                break;
                case 'month':
                    $query = 'getNmsSnmpStatsMonth';
                    $delay = 7200;
                    $total = 360;
                break;
                case 'year':
                    $query = 'getNmsSnmpStatsYear';
                    $delay = 86400;
                    $total = 365;
                break;
            }

            if(!$query) return;

            $args->mib_srl = $mib_srl;

            $mib_info = $this->getNmsMibInfo($args);
            $module_info = $this->getNmsHostInfo($mib_info->data->module_srl);
            $group_info = $this->getNmsGroupInfo($module_info->group_srl);

            if($regdate) $args->regdate = $regdate;

            $output = executeQueryArray("nms.".$query, $args);

            if(!$output->toBool()) return $output;
            if(!$output->data) $output->data[$total-1]->value = 0;

            $snmp = $output->data;

            foreach($snmp as $key=>$val) {
                if($snmp[$key+1]) {
                    if(floor(strtotime($snmp[$key]->regdate)-strtotime($snmp[$key+1]->regdate)) > $delay) {
                        $time = floor(strtotime($snmp[$key]->regdate)-strtotime($snmp[$key+1]->regdate)) - $delay;

                        if($group_info->schedule > $delay) { $error = 'interval'; continue; }

                        if(abs($time) > 14) {
                            if(floor($time / 86400) > 0) $snmp[$key]->delay = floor($time / 86400)." Day";
                            elseif(floor($time % 86400 / 3600) > 0) $snmp[$key]->delay = floor($time % 86400 / 3600)." Hour";
                            elseif(floor($time % 86400 % 3600 / 60) > 0) $snmp[$key]->delay = floor($time % 86400 % 3600 / 60)." Min";
                            elseif(($time % 86400 % 3600 % 60) > 0) $snmp[$key]->delay = ($time % 86400 % 3600 % 60)." Sec";
                        }
                    }
                }

                if(!preg_match("/^[-]?[0-9]+([\.][0-9]+)?$/", $val->value)) continue;
                if(!$first) $first = $val->value;
                $max = ($val->value > $max)?$val->value:$max;
                if(!$min) {
                    if($snmp[$key]->value < $snmp[$key+1]->value) $min = $snmp[$key]->value;
                    else $min = $snmp[$key+1]->value;
                } else $min = ($val->value < $min)?$val->value:$min;
                $last = $val->value;
                $sum += $val->value;
            }

            $oSnmp->max = $max;
            $oSnmp->min = (!$min)?0:$min;
            $oSnmp->first = $first;
            $oSnmp->last = $last;
            $oSnmp->avg = $sum/count($snmp);
            $oSnmp->error = $error;

            for($i=1;$i<=$total-count($output->data);$i++) {
                $none->module_srl = $module_info->module_srl;
                $none->mib_srl = $mib_srl;
                $none->value = null;
                $none->regdate = null;

                $snmp[count($output->data)+$i-1] = $none;
            }

            $oSnmp->data = $snmp;

            return $oSnmp;
        }

        /**
         * @brief SNMP LOG에서 mib_srl의 현재 값을 구함
         **/
        function getNmsSnmpCurrent($module_srl = null, $group_name = null, $mib_srl = null) {
            if(!$module_srl || !$group_name) return;
            $args->module_srl = $module_srl;
            $args->group_name = $group_name;
            $args->mib_srl = $mib_srl;

            $group_info = $this->getNmsMib($args);

            foreach($group_info as $mib) {
                $args->mib_srl = $mib->mib_srl;
                $mib->extra_vars = unserialize($mib->extra_vars);
                $output = executeQueryArray('nms.getNmsSnmpCurrent', $args);
                if(!$output->toBool()) continue;
                if(!$output->data) $output->data = array();

                foreach($output->data as $val) {
                    $val->mib_srl = $mib->mib_srl;
                    $val->legend_name = $mib->extra_vars->legend_name;
                    $val->collect_mode = $mib->extra_vars->collect_mode;
                    $val->max = $mib->max;
                    if($val->value == "") $val->value = 0;
                    $stats[] = $val;
                }
            }

            return $stats;
        }

        /**
         * @brief OID numeric을 MIB 이름으로 변환
         **/
        function getNmsOidByMib($oid = null, $smi_oid = null) {
            if(!$oid || !$smi_oid) return;

            $oid_each = explode('.', $oid);

            $mib_value = "";
            $numeric_fullname = "";
            foreach($oid_each as $numeric) {
                if($numeric == null) continue;
                $numeric_fullname .= '.'.$numeric;

                if($smi_oid[$numeric_fullname]) $mib_value .= '.'.$smi_oid[$numeric_fullname];
                else $mib_value .= '.'.$numeric;
            }

            return $mib_value;
        }

        /**
         * @brief MIB 이름을 OID numeric 번호로 변환
         **/
        function getNmsMibByOid($mib = null, $smi_oid = null) {
            if(!$mib || !$smi_oid) return;

            $mib_each = explode('.', $mib);

            $oid_value = "";
            foreach($mib_each as $name) {

                if($name == null) continue;

                if(preg_match("/^[-]?[0-9]+([\.][0-9]+)?$/", $name)) $oid_value .= '.'.$name;

                $i++;
                foreach($smi_oid as $key => $val) {

                    $oid_numeric = explode('.', $key);
                    if(count($oid_numeric)-1 != $i) continue;

                    if($val == $name) {
                        $oid_value .= '.'.$oid_numeric[$i];
                        break;
                    }
                }
            }

            return $oid_value;
        }

        /**
         * @brief 설정 마법사의 대상 xml 설명을 출력
         **/
        function getNmsWizardDesc() {
            $oNmsAdminModel = &getAdminModel('nms');

            $args = Context::getRequestVars();

            if(!$args->wizard_file) return new Object(-1);

            $wizard_info = $oNmsAdminModel->getWizardInfo($args->wizard_file.".xml");

            $lang = &$GLOBALS['lang'];

            $wizard->file = sprintf($lang->nms_wizard_file.' : %s', $wizard_info->wizard.".xml");
            $wizard->title = sprintf($lang->nms_wizard_title.' : %s', $wizard_info->title);
            $wizard->date = sprintf($lang->nms_wizard_date.' : %s', zdate($wizard_info->date, "Y-m-d"));

            $oAuthor = "";
            foreach($wizard_info->author as $author) {
                $oAuthor .= sprintf('%s (%s, %s)<br />', $author->name, $author->email_address, $author->homepage);
            }

            $wizard->author = sprintf($lang->nms_wizard_author.' : %s', $oAuthor);

            $wizard->count = sprintf($lang->nms_wizard_count.' : %s', $wizard_info->mib_var_count+$wizard_info->mib_group_var_count);

            $wizard->description = sprintf($lang->nms_wizard_description.' : %s', nl2br($wizard_info->description));

            $this->setMessage();
            $this->add('wizard', $wizard);
        }

        /**
         * @brief MBrowser 선택 대상의 정보를 포멧 가져옴
         **/
        function getNmsOidInfo() {
            $oNmsAdminModel = &getAdminModel('nms');

            $args = Context::getRequestVars();

            if($args->oid == null) return new Object(-1);

            $path = _XE_PATH_.'modules/nms/';
            $smis_path = $path.'tpl/smis/';
            $output = @FileHandler::readDir($smis_path);

            foreach($output as $key => $val) {
                $oNmsAdminModel->getSmiInfo($val);
                $cache_file = sprintf('./files/cache/nms/smi/%s.cache.php', preg_replace("/\.xml$/", "", $val));
                @include($cache_file);
            }

            uksort($smi_info->oid, 'strnatcasecmp');

            $oid_numeric = $this->getNmsMibByOid($args->oid, $smi_info->oid);

            $info = $smi_info->smi[$oid_numeric];
            $info->description = nl2br($info->description);

            $this->setMessage();
            $this->add('oid_info', $info);
        }

        /**
         * @brief MBrowser 선택 대상의 값(OID)을 가져옴
         **/
        function getNmsSnmpGet() {
            $oNmsAdminModel = &getAdminModel('nms');

            $args = Context::getRequestVars();

            if($args->oid == null) return new Object(-1, "msg_severity_checks");

            if(!preg_match("/^\./", $args->oid)) $args->oid = '.'.$args->oid;

            if($args->module_srl) $GLOBALS['__module_srl__'] = $args->module_srl;

            $info = $this->getNmsSnmpValue($args->oid, $args->start, $args->end, true, false, false);

            $this->setMessage();
            $this->add('value', $info->value);
            $this->add('oid', $args->oid);
            $this->add('start', $args->start);
            $this->add('end', $args->end);
            $this->add('total', $info->total);
            $this->add('no', $args->no);
            $this->add('module_srl', $args->module_srl);
        }

        /**
         * @brief MBrowser 선택 대상의 값(OID)을 Table 형식으로 처리
         **/
        function getNmsSnmpGetTable() {
            $oNmsAdminModel = &getAdminModel('nms');

            $args = Context::getRequestVars();

            if($args->oid == null) return new Object(-1, "msg_severity_checks");

            if(!preg_match("/^\./", $args->oid)) $args->oid = '.'.$args->oid;

            if($args->module_srl) $GLOBALS['__module_srl__'] = $args->module_srl;

            $info = $this->getNmsSnmpValue($args->oid, $args->start, $args->end);

            foreach($info->value as $val) {
                if($val->oid == 'NULL') { $info = 'NULL'; break; }

                $mib_each = explode('.', $val->oid);

                foreach($mib_each as $name) {
                    if($name == null) continue;
                    if(preg_match("/^[-]?[0-9]+([\.][0-9]+)?$/", $name)) {
                        $numeric = $name;
                        continue;
                    }

                    $mib = $name;
                }

                $info->table_no[$numeric]->mib[$mib]->oid = $val->oid;
                $info->table_no[$numeric]->mib[$mib]->numeric = $val->numeric;
                $info->table_no[$numeric]->mib[$mib]->val = $val->val;
                $info->table_header[$mib] = $mib;
            }

            $this->setMessage();
            $this->add('table', $info);
            $this->add('oid', $args->oid);
            $this->add('start', $args->start);
            $this->add('end', $args->end);
            $this->add('total', $info->total);
            $this->add('no', $args->no);
            $this->add('module_srl', $args->module_srl);
        }

        /**
         * @brief OID의 값을 가져옴
         **/
        function getNmsSnmpValue($oid, $start = 0, $end = false, $numeric = true, $quick = true, $enum = true) {
            $oNmsAdminModel = &getAdminModel('nms');

            if($GLOBALS['__module_srl__']) {
                $obj->module_srl = $GLOBALS['__module_srl__'];
                $output = $this->getNmsMibInfo($obj);
                $host_info = $this->getNmsHostInfo($obj->module_srl);
                $host = $host_info->host;
                $community = $host_info->community;
            } else {
                $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', 1);
                if(file_exists($cache_file)) {
                    @include($cache_file);
                    $host = $wizard_info[1]->host;
                    $community = $wizard_info[1]->community;
                }
            }

            if($host && $community) {
                $output = $oNmsAdminModel->_snmpwalkCache($host, $community, $oid, 100000000000000, $numeric, $quick, $enum, $start, $end);
                if(!$output->data) $output->data['NULL']->value = 'NULL';

                $data->total = $output->total;

                foreach($output->data as $key => $val) {
                    $snmpwalk->oid = $key;
                    $snmpwalk->numeric = $val->numeric;
                    $snmpwalk->val = $val->value;
                    $data->value[] = $snmpwalk;
                    unset($snmpwalk);
                }
            }

            return $data;
        }

        /**
         * @brief SNMP 값을 수집
         **/
        function _snmpget($host = null, $community = null, $mib = null, $delay = 500000, $numeric = true, $quick = true, $enum = true) {
            if($host == null || $community == null | $mib == null) return 'NULL';

            // OID표시를 numeric 선택, 순수 정보값 선택
            if(function_exists(snmp_set_oid_numeric_print)) snmp_set_oid_numeric_print($numeric);
            if(function_exists(snmp_set_quick_print)) snmp_set_quick_print($quick);
            if(function_exists(snmp_set_enum_print)) snmp_set_enum_print($enum);

            $snmp = @snmpget($host, $community, $mib, $delay);
            if($snmp == null) return 'NULL';

            return $snmp;
        }

        /**
         * @brief SNMP 주소에 해당되는 값들을 수집
         **/
        function _snmpwalkoid($host = null, $community = null, $mib = null, $delay = 500000, $numeric = true, $quick = true, $enum = true) {
            if($host == null || $community == null) return array('NULL' => 'NULL');

            // OID표시를 numeric 선택, 순수 정보값 선택
            if(function_exists(snmp_set_oid_numeric_print)) snmp_set_oid_numeric_print($numeric);
            if(function_exists(snmp_set_quick_print)) snmp_set_quick_print($quick);
            if(function_exists(snmp_set_enum_print)) snmp_set_enum_print($enum);

            /* snmpwalkoid,snmprealwalk 함수를 사용하면 제대로 출력하지 못해 snmp2_real_walk을 이용합니다. */
            if(function_exists(snmp2_real_walk)) $snmp = @snmp2_real_walk($host, $community, $mib, $delay);
            else $snmp = @snmpwalkoid($host, $community, $mib, $delay);
            if($snmp == null) return array('NULL' => 'NULL');

            foreach($snmp as $oid => $val) {
                if(function_exists(snmp_set_oid_numeric_print)) $snmpwalk[$oid] = $val;
                else $snmpwalk[preg_replace("/iso/",1,$oid)] = $val; // windows에서 snmp_set_oid_numeric_print 함수 이용불능으로 이를 조치
            }

            return $snmpwalk;
        }

        /**
         * @brief nms.act.php 에서 호출할 멤버 정보 구하기
         **/
        function getNmsActMember($args, $act) {
            if(!$act) return;
            $oMemberModel = &getModel('member');
            $lang = &$GLOBALS['lang'];
            if(preg_match('/,/',$lang->nms_act_member[$args->act_name])) {
                $search_var = explode(',',$lang->nms_act_member[$args->act_name]);
            } else $search_var = $lang->nms_act_member[$args->act_name];

            $member_info = $oMemberModel->getMemberInfoByMemberSrl($act);
            $member_info->extra_vars = unserialize($member_info->extra_vars);

            if(is_array($search_var)) {
                foreach($search_var as $key => $var) {
                    if(!isset($member_info->{$var})) {
                        if(!isset($member_info->extra_vars->{$var})) return;
                        else $target->var[$key] = (is_array($member_info->extra_vars->{$var}))?implode('',$member_info->extra_vars->{$var}):$member_info->extra_vars->{$var};
                    } else $target->var[$key] = (is_array($member_info->{$var}))?implode('',$member_info->{$var}):$member_info->{$var};
                }
            } else {
                if(!isset($member_info->{$search_var})) {
                    if(!isset($member_info->extra_vars->{$search_var})) return;
                    else $target->var = (is_array($member_info->extra_vars->{$search_var}))?implode('',$member_info->extra_vars->{$search_var}):$member_info->extra_vars->{$search_var};
                } else $target->var = (is_array($member_info->{$search_var}))?implode('',$member_info->{$search_var}):$member_info->{$search_var};
            }

            $target->name = $member_info->user_name;

            return $target;
        }

        /**
         * @brief nms.act.php 에서 호출할 그룹 정보 구하기
         **/
        function getNmsActGroup($args, $act = null, $count = 1) {
            if(!$act) return;
            $oMemberModel = &getModel('member');
            $lang = &$GLOBALS['lang'];
            if(preg_match('/\,/',$lang->nms_act_member[$args->act_name])) {
                $search_var = explode(',',$lang->nms_act_member[$args->act_name]);
            } else $search_var = $lang->nms_act_member[$args->act_name];

            $obj->selected_group_srl = $act;
            $obj->sort_index = 'member.member_srl';
            $obj->sort_order = 'asc';
            $obj->page = 1;
            $obj->list_count = $count;
            $obj->page_count = 10;
            $group_member = executeQuery('member.getMemberListWithinGroup', $obj);
            if(!$group_member->data) $group_member->data = array();

            foreach($group_member->data as $keys => $g_member) {
                $g_member->extra_vars = unserialize($g_member->extra_vars);
                if(is_array($search_var)) {
                    foreach($search_var as $key => $var) {
                        if(!isset($g_member->{$var})) {
                            if(!isset($g_member->extra_vars->{$var})) continue;
                            else $target[$keys]->var[$key] = (is_array($g_member->extra_vars->{$var}))?implode('',$g_member->extra_vars->{$var}):$g_member->extra_vars->{$var};
                        } else $target[$keys]->var[$key] = (is_array($g_member->{$var}))?implode('',$g_member->{$var}):$g_member->{$var};
                    }
                } else {
                    if(!isset($g_member->{$search_var})) {
                        if(!isset($g_member->extra_vars->{$search_var})) continue;
                        else $target[$keys]->var = (is_array($g_member->extra_vars->{$search_var}))?implode('',$g_member->extra_vars->{$search_var}):$g_member->extra_vars->{$search_var};
                    } else $target[$keys]->var = (is_array($g_member->{$search_var}))?implode('',$g_member->{$search_var}):$g_member->{$search_var};
                }

                $target[$keys]->name = $g_member->user_name;
            }

            return ($target)?$target:array();
        }

        /**
         * @brief 문자를 byte로 자릅니다.(휴대폰 80byte 전용)
         **/
        function cut_str($str,$size) {
            $len = strlen($str);
            if($size >= $len) return $str;

            $flag = 0;
            $start = 0;
            $end = $size-1;
            for($i=0;$i<$len;$i++) {
                if(ord($str[$i]) > 127) $flag++;
                if($end == $i) {
                    if($flag%2 == 1) {
                        return substr($str,$start,$size-1);
                        $start += $size - 1;
                    } else {
                        return substr($str,$start,$size);
                        $start += $size;
                    }

                    $end = $start + $size - 1;
                }
            }

            if($len>=$start) return substr($str,$start,$len-$start);

            return $str;
        }

        /**
         * @brief Hex 코드를 찾아서 문자열로 변환합니다.
         **/
        function hexStr2Ascii($str) {
            if(!preg_match("/Hex/",$str)) $hex_str = "Hex:".$str;
            preg_match_all("!Hex([^\>]*):([^\>]*)!is", $hex_str, $hex);
            if(preg_match("/[\-\(\)\_\\\,\/\=\:]/",$hex[2][0])) return $str;
            preg_match_all("![0-9a-zA-Z]!is", $hex[2][0], $hexStr);

            $code = null;
            foreach($hexStr[0] as $val) $code .= $val;

            // Hex Code가 아니면 요청값 리턴
            if(!dechex($code)) return $str;

            $asciiOut = null;
            for($i=0;$i<strlen($code);$i+=2) $asciiOut .= chr(hexdec(substr($code,$i,2)));

            if($this->is_utf8($asciiOut)) return $asciiOut;
            else return iconv('EUC-KR','UTF-8',substr($asciiOut,0,strlen($asciiOut)-1));
        }

        /**
         * @brief string charset 포멧이 utf-8인지 검사합니다.
         **/
        function is_utf8($string) {
          // From http://w3.org/International/questions/qa-forms-utf-8.html
          return preg_match('%^(?:
                [\x09\x0A\x0D\x20-\x7E]           # ASCII
              | [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
              |  \xE0[\xA0-\xBF][\x80-\xBF]       # excluding overlongs
              | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
              |  \xED[\x80-\x9F][\x80-\xBF]       # excluding surrogates
              |  \xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
              | [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
              |  \xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
          )*$%xs', $string);
        }
    }
?>