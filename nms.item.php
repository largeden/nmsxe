<?php
    /**
     * @class  nmsItem
     * @author largeden (developer@nmsxe.com)
     * @brief  nmsXE Item class
     **/

    // 향후 연동을 위해 선언함
    require_once(_XE_PATH_.'modules/document/document.item.php');

    class nmsItem extends documentItem {

        function setAttribute($attribute) {
            switch($attribute->type) {
                case 'module_srl': if(!$attribute->module_srl) { $this->module_srl = null; return; } $this->module_srl = $attribute->module_srl; break;
                case 'mib_srl': if(!$attribute->mib_srl) { $this->mib_srl = null; return; } $this->mib_srl = $attribute->mib_srl; break;
                case 'snmp_srl': if(!$attribute->snmp_srl) { $this->snmp_srl = null; return; } $this->snmp_srl = $attribute->snmp_srl; break;
                case 'snmptrap_srl': if(!$attribute->snmptrap_srl) { $this->snmptrap_srl = null; return; } $this->snmptrap_srl = $attribute->snmptrap_srl; break;
                case 'syslog_srl': if(!$attribute->syslog_srl) { $this->syslog_srl = null; return; } $this->syslog_srl = $attribute->syslog_srl; break;
                case 'severity_srl': if(!$attribute->severity_srl) { $this->severity_srl = null; return; } $this->severity_srl = $attribute->severity_srl; break;
                case 'group_name': if(!$attribute->group_name) { $this->group_name = null; return; } $this->group_name = $attribute->group_name; break;
                case 'regdate': if(!$attribute->regdate) { $this->regdate = null; return; } $this->regdate = $attribute->regdate; break;
                case 'key': if(!$attribute->key) { $this->key = null; return; } $this->key = $attribute->key; break;
                default: break;
            }

            unset($attribute->type);
            $this->adds($attribute);
        }

        /**
         * @brief Module Item
         **/
        function getGid() {
            $oNmsModel = &getModel('nms');
            $output = $oNmsModel->getNmsGroupInfo($this->get('group_srl'));
            return $output->gid;
        }

        function getModuleSrl() {
            return $this->get('module_srl');
        }

        function getMibSrl() {
            return $this->get('mib_srl');
        }

        /**
         * @brief Service Item
         **/
        function getAware() {
            $lang = &$GLOBALS['lang'];
            return $lang->nms_severity_aware[$this->get('aware')];
        }

        function getVarIdx() {
            $lang = &$GLOBALS['lang'];
            return $lang->nms_severity[$this->get('var_idx')];
        }

        function getAwaredate($date = false) {
            $date = ($date)?$date:"Y-m-d H:i:s";
            return zdate($this->get('awaredate'), $date);
        }

        function getSnmpTrapSrl() {
            return $this->get('snmptrap_srl');
        }

        function getIpType() {
            return "IPv".$this->get('ip_type');
        }

        function getIpAddr() {
            return $this->get('ip_address');
        }

        function getIpPort() {
            return $this->get('ip_port');
        }

        function getFacility() {
            $lang = &$GLOBALS['lang'];
            return $lang->nms_facility[$this->get('facility')];
        }

        function getSeverity() {
            $lang = &$GLOBALS['lang'];
            return $lang->nms_severity[$this->get('severity')];
        }

        function getTrap() {
            $lang = &$GLOBALS['lang'];
            return $lang->nms_severity[$this->get('trap')];
        }

        function getSnmpTrapValue() {
            $extra_vars = unserialize($this->get('extra_vars'));
            $trap_list = $this->getTrapList($extra_vars->var);

            return $trap_list;
        }

        function getTrapList($trap) {
            if(!$trap) return;

            $depth = 0;
            $type = array('circle','square');

            foreach($trap as $key => $val) {
                unset($val->tag);
                $val->tag->type = $type[$val->depth%2];

                if($depth > $val->depth) {
                    for($i=$val->depth; $i<$depth; $i++) {
                        $val->tag->endtag .= "</ul>\n</li>\n"; /* 죵료 변수 생성 */
                    }

                    $depth = $val->depth;
                }

                if($val->child_count) {
                    $depth++;
                    $val->tag->starttag = "\n<ul type=\"".$val->tag->type."\">\n"; /* 죵료 변수 생성 */
                    $val->tag->parent = true;
                }else{
                    $val->tag->stoptag = "</li>\n"; /* 죵료 변수 생성 */
                }

                $category[] = $val;
            }

            unset($val);
            $val->depth = 0;

            if($depth > $val->depth) {
                for($i=$val->depth; $i<$depth; $i++) {
                    $val->tag->endtag .= "</ul>\n</li>\n"; /* 죵료 변수 생성 */
                }
            }

            $val->tag->finalendtag = true;
            $category[] = $val;

            return $category;
        }

        /**
         * @brief Snmp/Skin Item
         **/
        function getMid() {
            if($this->get('mid')) return $this->get('mid');
        }

        function getGroupSrl() {
            return $this->get('group_srl');
        }

        function getHost() {
            return $this->get('host');
        }

        function getGroupName() {
            return $this->get('group_name');
        }

        function getMmid() {
            return ($this->get('mmid'))?$this->get('mmid'):"-";
        }

        function getBrowserTitle() {
            return $this->get('browser_title');
        }

        function getMibTitle() {
            return $this->get('mib_title');
        }

        function getValue($type = false) {
            if($type && preg_match("/O:8:\"stdClass\"/", $this->get('value'))) {
                $value = unserialize($this->get('value'));
                return $value->{$type};
            } else return $this->get('value');
        }

        function getRegdate($date = false) {
            $date = ($date)?$date:"Y-m-d H:i:s";
            return zdate($this->get('regdate'), $date, true);
        }

        function getMax() {
            return $this->get('max');
        }

        function getSnmpSrl() {
            return $this->get('snmp_srl');
        }

        function getDescription() {
            return $this->get('description');
        }

        function getSession() {
            $lang = &$GLOBALS['lang'];
            return $lang->nms_session[$this->get('session')];
        }

        function getNo() {
            return $this->get('no');
        }

        /**
         * @brief 통계 처리정보를 추출
         **/
        function getSnmpSummary($type='normal',$mib_srl = null,$regdate = null) {
            if($regdate==null) $regdate = date('YmdHis');
            if($mib_srl==null) $mib_srl = $this->get('mib_srl');
            $oNmsModel = &getModel('nms');

            return $oNmsModel->getNmsSnmpSummary($type,$mib_srl,$regdate);
        }

        /**
         * @brief Snmp Log에서 현재 값을 구함
         **/
        function getSnmpCurrent($mib_srl=null) {
            $oNmsModel = &getModel('nms');
            return $oNmsModel->getNmsSnmpCurrent($this->get('module_srl'), $this->group_name, $mib_srl);
        }

        /**
         * @brief 입력 값을 가지고 단위 계산
         **/
        function getSizeBytes($value, $round = 3, $byte = 1000) {
            // 요청값이 숫자가 아니면 그대로 리턴
            if(!preg_match("/^[-]?[0-9]+([\.][0-9]+)?$/", $value)) return $value;

            $Go = floor($value/($byte*$byte*$byte));
            $Mo = floor(($value - $Go*($byte*$byte*$byte))/($byte*$byte));
            $Ko = floor(($value - $Go*($byte*$byte*$byte) - $Mo*($byte*$byte))/$byte);
            $o  = floor($value - $Go*($byte*$byte*$byte) - $Mo*($byte*$byte) - $Ko*$byte);

            if($Go != 0) return round(($Go.".".$Mo),$round)."G";
            if($Mo != 0) return round(($Mo.".".$Ko),$round)."M";
            if($Ko != 0) return round(($Ko.".".$o),$round)."K";

            if($o == 0) return round($value,$round);
            else return $o;
        }

        function getExtraVars($value = null) {
            $extra_vars = unserialize($this->get('extra_vars'));
            if(!$value) return $extra_vars;
            else return $extra_vars->{$value}?$extra_vars->{$value}:'';
        }

        /**
         * @brief getExtraImages로 구한 값을 이미지 태그를 씌워서 리턴
         **/
        function printExtraImages($time_check = 43200) {

            // 아이콘 디렉토리 구함
            $path = sprintf('%s%s',getUrl(), 'modules/nms/tpl/images/');

            $buffs = $this->getExtraImages($time_check);
            if(!count($buffs)) return;

            $buff = null;
            foreach($buffs as $key => $val) {
                $buff .= sprintf('<img src="%s%s.gif" width="9" height="9" alt="%s" title="%s" />', $path, $val, $val, $val);
            }
            return $buff;
        }

        /**
         * @brief 새글, 최신 업데이트글 아이콘 출력용 함수
         **/
        function getExtraImages($time_interval = 43200) {
            // 아이콘 목록을 담을 변수 미리 설정
            $buffs = array();

            $check_files = false;

            // 최신 시간 설정
            $time_check = date('YmdHis', time()-$time_interval);

            // 새글 체크
            if($this->get('regdate')>$time_check) $buffs[] = 'new';
            else if($this->get('awaredate')>$time_check) $buffs[] = 'update';

            return $buffs;
        }

        /**
         * @brief group_name 값으로 캐쉬를 생성(0과 1로 결과출력)
         **/
        function nmsGroupCache($mKey = null) {
            if(!$mKey) return;
            if(!$this->get('module_srl') || $this->get('group_name')) return;

            $oNmsModel = &getModel('nms');
            $oNmsController = &getController('nms');

            $args->module_srl = $this->get('module_srl');
            $args->group_name = $this->get('group_name');
            $output = $oNmsModel->getNmsMib($args);

            foreach($output as $mib) {
                $mib->extra_vars = unserialize($mib->extra_vars);
                if($mib->extra_vars->graphCache->{md5($mKey)}) {
                    $cache = true;
                    continue;
                }

                $mib->mkey = md5($mKey);
                $mib->extra_vars = serialize($mib->extra_vars);
                $oNmsController->insertGraphCache($mib);
            }

            if($cache) return true;
            else return false;
        }

        /**
         * @brief mib_srl 값으로 캐쉬를 생성(0과 1로 결과출력)
         **/
        function nmsMibCache($mKey = null) {
            if(!$mKey) return;

            if(!$this->get('module_srl') || !$this->get('mib_srl')) return;

            $oNmsModel = &getModel('nms');
            $oNmsController = &getController('nms');

            $args->module_srl = $this->get('module_srl');
            $args->mib_srl = $this->get('mib_srl');
            $output = $oNmsModel->getNmsMib($args);

            foreach($output as $mib) {
                $mib->extra_vars = unserialize($mib->extra_vars);
                if($mib->extra_vars->graphCache->{md5($mKey)}) {
                    $cache = true;
                    continue;
                }

                $mib->mkey = md5($mKey);
                $mib->extra_vars = serialize($mib->extra_vars);
                $oNmsController->insertGraphCache($mib);
            }

            if($cache) return true;
            else return false;
        }
    }
?>