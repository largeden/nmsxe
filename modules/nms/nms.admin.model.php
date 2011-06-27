<?php
    /**
     * @class  nmsAdminModel
     * @author largeden (developer@nmsxe.com)
     * @brief  nmsXE Admin Model class
     **/

    class nmsAdminModel extends nms {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 모듈의 tpl/wizard/file.xml 을 읽어서 정보를 구함
         * 캐싱을 통해서 xml parsing 시간 단축
         **/
        function getWizardInfo($wizard_file = null) {
            // 현재 선택된 정보 xml 파일을 읽음
            $path = _XE_PATH_.'modules/nms/';
            $wizard_path = $path.'tpl/wizard/';
            if(!file_exists($wizard_path.$wizard_file)) return;

            // cache 파일을 비교하여 문제 없으면 include하고 $wizard_info 변수를 return
            $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', preg_replace("/\.xml$/", "", $wizard_file), Context::getLangType());

            if(file_exists($cache_file)&&filemtime($cache_file)>filemtime($wizard_path.$wizard_file)) {
                @include($cache_file);
                return $wizard_info;
            }

            // cache 파일이 없으면 xml parsing하고 변수화 한 후에 캐시 파일에 쓰고 변수 바로 return
            $oXmlParser = new XmlParser();
            $tmp_xml_obj = $oXmlParser->loadXmlFile($wizard_path.$wizard_file);
            $xml_obj = $tmp_xml_obj->wizard;
            if(!$xml_obj) return;

            $buff = "";

            if($xml_obj->attrs->version == '0.2') {
                // Wizard 제목, 버전
                $buff .= sprintf('$wizard_info->wizard = "%s";', preg_replace("/\.xml$/", "", $wizard_file));
                $buff .= sprintf('$wizard_info->path = "%s";', $wizard_path);
                $buff .= sprintf('$wizard_info->title = "%s";', $xml_obj->title->body);
                $buff .= sprintf('$wizard_info->description = "%s";', preg_replace("/\"/","'",$xml_obj->description->body));
                sscanf($xml_obj->date->body, '%d-%d-%d', $date_obj->y, $date_obj->m, $date_obj->d);
                $date = sprintf('%04d%02d%02d', $date_obj->y, $date_obj->m, $date_obj->d);
                $buff .= sprintf('$wizard_info->date = "%s";', $date);

                // 작성자 정보
                if(!is_array($xml_obj->author)) $author_list[] = $xml_obj->author;
                else $author_list = $xml_obj->author;

                $i=0;
                foreach($author_list as $author) {
                    $buff .= sprintf('$wizard_info->author[%s]->name = "%s";', $i, $author->name->body);
                    $buff .= sprintf('$wizard_info->author[%s]->email_address = "%s";', $i, $author->attrs->email_address);
                    $buff .= sprintf('$wizard_info->author[%s]->homepage = "%s";', $i, $author->attrs->link);
                    $i++;
                }
            }

            // 사용자 정의 모드일 경우
            if($xml_obj->mode->body) $buff .= sprintf('$wizard_info->mode = "%s";', $xml_obj->mode->body);

            // Mib 변수
            if($xml_obj->mibs->body) {
                $mibs = $xml_obj->mibs;
                if(!is_array($mibs)) $mibs = array($mibs);

                foreach($mibs as $mib) {
                    $mib_vars = $mib->var;
                    if(!is_array($mib_vars)) $mib_vars = array($mib_vars);
                    $mib_var_count = count($mib_vars);

                    $buff .= sprintf('$wizard_info->mib_var_count = "%s";', $mib_var_count);

                    $i=0;
                    foreach($mib_vars as $var) {
                        $id = $var->attrs->id;
                        $mmid = $var->mmid->body;
                        $mib_title = $var->mib_title->body;
                        $mib = $var->mib->body;
                        $max = $var->max->body;
                        $group_name = $var->group_name->body;
                        $description = preg_replace("/\"/","'",$var->description->body);
                        $legend = $var->legend->body;
                        $collect = $var->collect->body;

                        iF($filter_id[$id] == $id) {
                            $filter->title = $xml_obj->title->body." (id error)";
                        }

                        if(!preg_match("/^[a-z][a-z0-9_]/", $mmid)) {
                            $filter->title = $xml_obj->title->body." (mmid error)";
                        }

                        if($filter) return $filter;

                        $buff .= sprintf('$wizard_info->mibs->%s->mmid = "%s";', $id, $mmid);
                        $buff .= sprintf('$wizard_info->mibs->%s->mib_title = "%s";', $id, $mib_title);
                        $buff .= sprintf('$wizard_info->mibs->%s->mib = "%s";', $id, $mib);
                        $buff .= sprintf('$wizard_info->mibs->%s->max = "%s";', $id, $max);
                        $buff .= sprintf('$wizard_info->mibs->%s->group_name = "%s";', $id, $group_name);
                        $buff .= sprintf('$wizard_info->mibs->%s->description = "%s";', $id, $description);
                        $buff .= sprintf('$wizard_info->mibs->%s->legend = "%s";', $id, $legend);
                        $buff .= sprintf('$wizard_info->mibs->%s->collect = "%s";', $id, $collect);

                        $i++;
                        $filter_id[$id] = $id;
                        unset($var);
                    }
                }
            }

            // MIB Group 변수
            if($xml_obj->mib_groups->body) {
                $mib_groups = $xml_obj->mib_groups;
                if(!is_array($mib_groups)) $mib_groups = array($mib_groups);

                foreach($mib_groups as $group) {
                    $mib_group_vars = $group->var;
                    if(!is_array($mib_group_vars)) $mib_group_vars = array($mib_group_vars);
                    $mib_group_var_count = count($mib_group_vars);

                    $buff .= sprintf('$wizard_info->mib_group_var_count = "%s";', $mib_group_var_count);

                    $i=0;
                    foreach($mib_group_vars as $var) {
                        $id = $var->attrs->id;
                        $mmid = $var->mmid->body;
                        $mib_title = $var->mib_title->body;
                        $mib = $var->mib->body;
                        $max = $var->max->body;
                        $group_name = $var->group_name->body;
                        $description = preg_replace("/\"/","'",$var->description->body);
                        $legend = $var->legend->body;
                        $collect = $var->collect->body;

                        iF($filter_id[$id] == $id) {
                            $filter->title = $xml_obj->title->body." (id error)";
                        }

                        if(!preg_match("/^[a-z][a-z0-9_]/", $mmid)) {
                            $filter->title = $xml_obj->title->body." (mmid error)";
                        }

                        if($filter) return $filter;

                        $buff .= sprintf('$wizard_info->mib_groups->%s->mmid = "%s";', $id, $mmid);
                        $buff .= sprintf('$wizard_info->mib_groups->%s->mib_title = "%s";', $id, $mib_title);
                        $buff .= sprintf('$wizard_info->mib_groups->%s->mib = "%s";', $id, $mib);
                        $buff .= sprintf('$wizard_info->mib_groups->%s->max = "%s";', $id, $max);
                        $buff .= sprintf('$wizard_info->mib_groups->%s->group_name = "%s";', $id, $group_name);
                        $buff .= sprintf('$wizard_info->mib_groups->%s->description = "%s";', $id, $description);
                        $buff .= sprintf('$wizard_info->mib_groups->%s->legend = "%s";', $id, $legend);
                        $buff .= sprintf('$wizard_info->mib_groups->%s->collect = "%s";', $id, $collect);

                        $i++;
                        unset($var);
                    }
                }
            }

            $buff = '<?php if(!defined("__ZBXE__")) exit(); '.$buff.' ?>';
            FileHandler::writeFile($cache_file, $buff);

            if(file_exists($cache_file)) @include($cache_file);
            return $wizard_info;
        }

        /**
         * @brief Wizard의 Step 들의 정보를 cache로 저장
         **/
        function procWizardCache($args = null) {
            if(!$args->step) return;

            $buff = "";

            switch($args->step) {
                case 1:
                    $buff .= sprintf('$wizard_info[%s]->module = "%s";', $args->step, "nms");
                    $buff .= sprintf('$wizard_info[%s]->mid = "%s";', $args->step, $args->board_name);
                    $buff .= sprintf('$wizard_info[%s]->module_category_srl = "%s";', $args->step, $args->module_category_srl);
                    $buff .= sprintf('$wizard_info[%s]->browser_title = "%s";', $args->step, $args->browser_title);
                    $buff .= sprintf('$wizard_info[%s]->layout_srl = "%s";', $args->step, $args->layout_srl);
                    $buff .= sprintf('$wizard_info[%s]->skin = "%s";', $args->step, $args->skin);
                    $buff .= sprintf('$wizard_info[%s]->ip_type = "%s";', $args->step, $args->ip_type);
                    $buff .= sprintf('$wizard_info[%s]->host = "%s";', $args->step, $args->host);
                    $buff .= sprintf('$wizard_info[%s]->order_target = "%s";', $args->step, $args->order_target);
                    $buff .= sprintf('$wizard_info[%s]->order_type = "%s";', $args->step, $args->order_type);
                    $buff .= sprintf('$wizard_info[%s]->list_count = "%s";', $args->step, $args->list_count);
                    $buff .= sprintf('$wizard_info[%s]->search_list_count = "%s";', $args->step, $args->search_list_count);
                    $buff .= sprintf('$wizard_info[%s]->page_count = "%s";', $args->step, $args->page_count);
                    $buff .= sprintf('$wizard_info[%s]->community = "%s";', $args->step, $args->community);
                    $buff .= sprintf('$wizard_info[%s]->description = "%s";', $args->step, $args->description);
                break;
                case 2:
                    $buff .= sprintf('$wizard_info[%s]->wizard_file = "%s";', $args->step, $args->wizard_file);
                break;
                case 3:
                    if(!$args->mibs) break;

                    $i=0;
                    foreach($args as $key => $val) {
                        if(in_array($key, array("body","mibs","step","module","act"))) continue;

                        $mibs = explode("|@|", $val);

                        $buff .= sprintf('$wizard_info[%s]->mibs->%s->type = "%s";', $args->step, $key, $mibs[0]);
                        $buff .= sprintf('$wizard_info[%s]->mibs->%s->mmid = "%s";', $args->step, $key, $mibs[1]);
                        $buff .= sprintf('$wizard_info[%s]->mibs->%s->mib_title = "%s";', $args->step, $key, $mibs[2]);
                        $buff .= sprintf('$wizard_info[%s]->mibs->%s->mib = "%s";', $args->step, $key, $mibs[3]);
                        $buff .= sprintf('$wizard_info[%s]->mibs->%s->mib_value = "%s";', $args->step, $key, $mibs[4]);
                        $buff .= sprintf('$wizard_info[%s]->mibs->%s->max = "%s";', $args->step, $key, $mibs[5]);
                        $buff .= sprintf('$wizard_info[%s]->mibs->%s->max_value = "%s";', $args->step, $key, $mibs[6]);
                        $buff .= sprintf('$wizard_info[%s]->mibs->%s->collect = "%s";', $args->step, $key, $mibs[7]);
                        $buff .= sprintf('$wizard_info[%s]->mibs->%s->group_name = "%s";', $args->step, $key, $mibs[8]);
                        $buff .= sprintf('$wizard_info[%s]->mibs->%s->legend = "%s";', $args->step, $key, $mibs[9]);
                        $buff .= sprintf('$wizard_info[%s]->mibs->%s->description = "%s";', $args->step, $key, $mibs[10]);

                        $i++;
                    }

                    $buff .= sprintf('$wizard_info[%s]->mibs_count = "%s";', $args->step, $i);
                break;
                default: break;
            }

            // wizard cache 파일을 생성
            $cache_file = sprintf('./files/cache/nms/wizard/%s.%s.cache.php', 'wizard', $args->step);
            $buff = '<?php if(!defined("__ZBXE__")) exit(); '.$buff.' ?>';
            FileHandler::writeFile($cache_file, $buff);

            return $args->step+1;
        }

        /**
         * @brief MIB Browser를 만들어 출력
         **/
        function getMibBrowserInfo() {
            $oNmsModel = &getModel('nms');
            $oNmsController = &getController('nms');

            $path = _XE_PATH_.'modules/nms/';
            $smis_path = $path.'tpl/smis/';
            $output = @FileHandler::readDir($smis_path);

            if($output) {
                foreach($output as $key => $val) {
                    $info = $this->getSmiInfo($val);
                    $cache_file = sprintf('./files/cache/nms/smi/%s.cache.php', preg_replace("/\.xml$/", "", $val));
                    @include($cache_file);
                }

                uksort($smi_info->oid, 'strnatcasecmp');
                uksort($smi_info->smi, 'strnatcasecmp');
            }

            if($smi_info->smi) {
                foreach($smi_info->smi as $key => $val) {
                    $depth = explode('.', $key);
                    $parent_oid = $key;
                    $child_complete = false;
                    while(!$child_complete && $parent_oid) {
                        array_pop($depth);
                        $parent_oid = implode('.', $depth);

                        if($smi_info->smi[$parent_oid]) {
                            $smi_info->smi[$parent_oid]->child = true;
                            $child_complete = true;
                        }
                    }

                    $smi_info->smi[$key]->mib = $oNmsModel->getNmsOidByMib($key, $smi_info->oid);
                }
            }

            $depth = 0;
            $type = array('circle','square');

            foreach($smi_info->smi as $key => $val) {

                unset($val->tag);

                $val->tag->type = $type[$val->depth%2];

                if($depth > $val->depth) {
                    for($i=$val->depth; $i<$depth; $i++) {
                        $val->tag->endtag .= "</ul>\n</li>\n"; /* 죵료 변수 생성 */
                    }

                    $depth = $val->depth;
                }

                if($val->child) {
                    $depth++;
                    if($depth == $val->depth) $depth++; // 증가한 $depth의 수와 현재 depth와 같으면 한번더 증가

                    $val->tag->starttag = "\n<ul type=\"".$val->tag->type."\">\n"; /* 죵료 변수 생성 */
                    $val->tag->parent = true;
                }else{
                    $val->tag->stoptag = "</li>\n"; /* 죵료 변수 생성 */
                }

                $smi[$key] = $val;
            }

            unset($val);
            $val->depth = 0;

            if($depth > $val->depth) {
                for($i=$val->depth; $i<$depth; $i++) {
                    $val->tag->endtag .= "</ul>\n</li>\n"; /* 죵료 변수 생성 */
                }
            }

            $val->tag->finalendtag = true;
            $smi['end'] = $val;

            $mbrowser->oid = $smi_info->oid;
            $mbrowser->smi = $smi;

            return $mbrowser;
        }

        /**
         * @brief SMI 정보를 담은 xml을 분석하여 출력
         **/
        function getSmiInfo($smi_file = null) {

            // 현재 선택된 정보 xml 파일을 읽음
            $path = _XE_PATH_.'modules/nms/';
            $smis_path = $path.'tpl/smis/';
            if(!file_exists($smis_path.$smi_file)) return;

            // cache 파일을 비교하여 문제 없으면 include하고 $wizard_info 변수를 return
            $cache_file = sprintf('./files/cache/nms/smi/%s.cache.php', preg_replace("/\.xml$/", "", $smi_file));

            if(file_exists($cache_file)&&filemtime($cache_file)>filemtime($smis_path.$smi_file)) return;

            // cache 파일이 없으면 xml parsing하고 변수화 한 후에 캐시 파일에 쓰고 변수 바로 return
            $oXmlParser = new XmlParser();
            $tmp_xml_obj = $oXmlParser->loadXmlFile($smis_path.$smi_file);
            $xml_obj = $tmp_xml_obj->smi;
            if(!$xml_obj) return;

            $buff = "";

            $buff .= sprintf('$smi_info->module["%s"]->name = "%s";', preg_replace("/\.xml$/", "", $smi_file), $xml_obj->module->attrs->name);
            $buff .= sprintf('$smi_info->module["%s"]->language = "%s";', preg_replace("/\.xml$/", "", $smi_file), $xml_obj->module->attrs->language);

            if($xml_obj->nodes->node) {
                if(!is_array($xml_obj->nodes->node)) $xml_obj->nodes->node = array($xml_obj->nodes->node);

                foreach($xml_obj->nodes->node as $node) {
                    $buff .= sprintf('$smi_info->oid[".%s"] = "%s";', $node->attrs->oid, $node->attrs->name);
                    $buff .= sprintf('$smi_info->smi[".%s"]->type = "%s";', $node->attrs->oid, "node");
                    $buff .= sprintf('$smi_info->smi[".%s"]->name = "%s";', $node->attrs->oid, $node->attrs->name);
                    $depth = explode('.', $node->attrs->oid);
                    $buff .= sprintf('$smi_info->smi[".%s"]->depth = "%s";', $node->attrs->oid, count($depth)-1);

                    if($node->description->body) $buff .= sprintf('$smi_info->smi[".%s"]->description = "%s";', $node->attrs->oid, preg_replace("/\"/","'",$node->description->body));
                }
            }

            if($xml_obj->notifications->notification) {
                if(!is_array($xml_obj->notifications->notification)) $xml_obj->notifications->notification = array($xml_obj->notifications->notification);

                foreach($xml_obj->notifications->notification as $notification) {
                    $buff .= sprintf('$smi_info->oid[".%s"] = "%s";', $notification->attrs->oid, $notification->attrs->name);
                    $buff .= sprintf('$smi_info->smi[".%s"]->type = "%s";', $notification->attrs->oid, "notification");
                    $buff .= sprintf('$smi_info->smi[".%s"]->name = "%s";', $notification->attrs->oid, $notification->attrs->name);
                    $buff .= sprintf('$smi_info->smi[".%s"]->status = "%s";', $notification->attrs->oid, $notification->attrs->status);
                    $depth = explode('.', $notification->attrs->oid);
                    $buff .= sprintf('$smi_info->smi[".%s"]->depth = "%s";', $notification->attrs->oid, count($depth)-1);

                    if($notification->objects) {
                        if(!is_array($notification->objects->object)) $notification->objects->object = array($notification->objects->object);

                        foreach($notification->objects->object as $object) {
                            $buff .= sprintf('$smi_info->smi[".%s"]->object->module = "%s";', $notification->attrs->oid, $object->attrs->module);
                            $buff .= sprintf('$smi_info->smi[".%s"]->object->name = "%s";', $notification->attrs->oid, $object->attrs->name);
                        }
                    }

                    if($notification->description->body) $buff .= sprintf('$smi_info->smi[".%s"]->description = "%s";', $notification->attrs->oid, preg_replace("/\"/","'",$notification->description->body));
                }
            }

            if($xml_obj->groups->group) {
                if(!is_array($xml_obj->groups->group)) $xml_obj->groups->group = array($xml_obj->groups->group);

                foreach($xml_obj->groups->group as $group) {
                    $buff .= sprintf('$smi_info->oid[".%s"] = "%s";', $group->attrs->oid, $group->attrs->name);
                    $buff .= sprintf('$smi_info->smi[".%s"]->type = "%s";', $group->attrs->oid, "group");
                    $buff .= sprintf('$smi_info->smi[".%s"]->name = "%s";', $group->attrs->oid, $group->attrs->name);
                    $buff .= sprintf('$smi_info->smi[".%s"]->status = "%s";', $group->attrs->oid, $group->attrs->status);
                    $depth = explode('.', $group->attrs->oid);
                    $buff .= sprintf('$smi_info->smi[".%s"]->depth = "%s";', $group->attrs->oid, count($depth)-1);

                    if($group->members) {
                        if(!is_array($group->members->member)) $group->members->member = array($group->members->member);

                        foreach($group->members->member as $member) {
                            $buff .= sprintf('$smi_info->smi[".%s"]->object->module = "%s";', $group->attrs->oid, $member->attrs->module);
                            $buff .= sprintf('$smi_info->smi[".%s"]->object->name = "%s";', $group->attrs->oid, $member->attrs->name);
                        }
                    }

                    if($group->description->body) $buff .= sprintf('$smi_info->smi[".%s"]->description = "%s";', $group->attrs->oid, preg_replace("/\"/","'",$group->description->body));
                }
            }

            if($xml_obj->compliances->compliance) {
                if(!is_array($xml_obj->compliances->compliance)) $xml_obj->compliances->compliance = array($xml_obj->compliances->compliance);

                foreach($xml_obj->compliances->compliance as $compliance) {
                    $buff .= sprintf('$smi_info->oid[".%s"] = "%s";', $compliance->attrs->oid, $compliance->attrs->name);
                    $buff .= sprintf('$smi_info->smi[".%s"]->type = "%s";', $compliance->attrs->oid, "group");
                    $buff .= sprintf('$smi_info->smi[".%s"]->name = "%s";', $compliance->attrs->oid, $compliance->attrs->name);
                    $buff .= sprintf('$smi_info->smi[".%s"]->status = "%s";', $compliance->attrs->oid, $compliance->attrs->status);
                    $depth = explode('.', $compliance->attrs->oid);
                    $buff .= sprintf('$smi_info->smi[".%s"]->depth = "%s";', $compliance->attrs->oid, count($depth)-1);

                    if($compliance->requires) {
                        if(!is_array($compliance->requires->mandatory)) $compliance->requires->mandatory = array($compliance->requires->mandatory);

                        foreach($compliance->requires->mandatory as $mandatory) {
                            $buff .= sprintf('$smi_info->smi[".%s"]->object->module = "%s";', $compliance->attrs->oid, $mandatory->attrs->module);
                            $buff .= sprintf('$smi_info->smi[".%s"]->object->name = "%s";', $compliance->attrs->oid, $mandatory->attrs->name);
                        }
                    }

                    if($compliance->description->body) $buff .= sprintf('$smi_info->smi[".%s"]->description = "%s";', $compliance->attrs->oid, preg_replace("/\"/","'",$compliance->description->body));
                }
            }

            if($xml_obj->nodes->scalar) {
                if(!is_array($xml_obj->nodes->scalar)) $xml_obj->nodes->scalar = array($xml_obj->nodes->scalar);

                foreach($xml_obj->nodes->scalar as $scalar) {
                    $buff .= sprintf('$smi_info->oid[".%s"] = "%s";', $scalar->attrs->oid, $scalar->attrs->name);
                    $buff .= sprintf('$smi_info->smi[".%s"]->type = "%s";', $scalar->attrs->oid, "scalar");
                    $buff .= sprintf('$smi_info->smi[".%s"]->name = "%s";', $scalar->attrs->oid, $scalar->attrs->name);
                    $depth = explode('.', $scalar->attrs->oid);
                    $buff .= sprintf('$smi_info->smi[".%s"]->depth = "%s";', $scalar->attrs->oid, count($depth)-1);

                    if($scalar->attrs->name) $buff .= sprintf('$smi_info->smi[".%s"]->status = "%s";', $scalar->attrs->oid, $scalar->attrs->status);
                    if($scalar->access->body) $buff .= sprintf('$smi_info->smi[".%s"]->access = "%s";', $scalar->attrs->oid, $scalar->access->body);
                    if($scalar->description->body) $buff .= sprintf('$smi_info->smi[".%s"]->description = "%s";', $scalar->attrs->oid, preg_replace("/\"/","'",$scalar->description->body));

                    if($scalar->syntax->typedef) {
                        $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->basetype = "%s";', $scalar->attrs->oid, $scalar->syntax->typedef->attrs->basetype);
                        if($scalar->syntax->typedef->range->attrs->min) $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->range->min = "%s";', $scalar->attrs->oid, $scalar->syntax->typedef->range->attrs->min);
                        if($scalar->syntax->typedef->range->attrs->max) $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->range->max = "%s";', $scalar->attrs->oid, $scalar->syntax->typedef->range->attrs->max);

                        if($scalar->syntax->typedef->namednumber) {
                             if(!is_array($scalar->syntax->typedef->namednumber)) $scalar->syntax->typedef->namednumber = array($scalar->syntax->typedef->namednumber);
                            foreach($scalar->syntax->typedef->namednumber as $namednumber) {
                                $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->namednumber["%s"]->name = "%s";', $scalar->attrs->oid, $namednumber->attrs->number, $namednumber->attrs->name);
                                $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->namednumber["%s"]->number = "%s";', $scalar->attrs->oid, $namednumber->attrs->number, $namednumber->attrs->number);
                            }
                        }
                    }

                    if($scalar->syntax->type) {
                        $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->basetype = "%s";', $scalar->attrs->oid, $scalar->syntax->type->attrs->name);
                        if($scalar->syntax->type->attrs->module) $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->module = "%s";', $scalar->attrs->oid, $scalar->syntax->type->attrs->module);
                    }
                }
            }

            if($xml_obj->nodes->table) {
                if(!is_array($xml_obj->nodes->table)) $xml_obj->nodes->table = array($xml_obj->nodes->table);

                foreach($xml_obj->nodes->table as $table) {
                    $buff .= sprintf('$smi_info->oid[".%s"] = "%s";', $table->attrs->oid, $table->attrs->name);
                    $buff .= sprintf('$smi_info->smi[".%s"]->type = "%s";', $table->attrs->oid, "table");
                    $buff .= sprintf('$smi_info->smi[".%s"]->name = "%s";', $table->attrs->oid, $table->attrs->name);
                    $depth = explode('.', $table->attrs->oid);
                    $buff .= sprintf('$smi_info->smi[".%s"]->depth = "%s";', $table->attrs->oid, count($depth)-1);

                    if($table->attrs->status) $buff .= sprintf('$smi_info->smi[".%s"]->status = "%s";', $table->attrs->oid, $table->attrs->status);
                    if($table->access->body) $buff .= sprintf('$smi_info->smi[".%s"]->access = "%s";', $table->attrs->oid, $table->access->body);
                    if($table->description->body) $buff .= sprintf('$smi_info->smi[".%s"]->description = "%s";', $table->attrs->oid, preg_replace("/\"/","'",$table->description->body));

                    if($table->row) {
                        $buff .= sprintf('$smi_info->oid[".%s"] = "%s";', $table->row->attrs->oid, $table->row->attrs->name);
                        $buff .= sprintf('$smi_info->smi[".%s"]->type = "%s";', $table->row->attrs->oid, "row");
                        $buff .= sprintf('$smi_info->smi[".%s"]->name = "%s";', $table->row->attrs->oid, $table->row->attrs->name);
                        $depth = explode('.', $table->row->attrs->oid);
                        $buff .= sprintf('$smi_info->smi[".%s"]->depth = "%s";', $table->row->attrs->oid, count($depth)-1);

                        if($table->row->attrs->create) $buff .= sprintf('$smi_info->smi[".%s"]->create = "%s";', $table->row->attrs->oid, $table->row->attrs->create);
                        if($table->row->attrs->status) $buff .= sprintf('$smi_info->smi[".%s"]->status = "%s";', $table->row->attrs->oid, $table->row->attrs->status);
                        if($table->row->linkage->attrs->implied) $buff .= sprintf('$smi_info->smi[".%s"]->linkage->implied = "%s";', $table->row->attrs->oid, $table->row->linkage->attrs->implied);

                        if($table->row->linkage->index) {
                            if(!is_array($table->row->linkage->index)) $table->row->linkage->index = array($table->row->linkage->index);

                            foreach($table->row->linkage->index as $no => $index) {
                                if($index->attrs->module) $buff .= sprintf('$smi_info->smi[".%s"]->linkage->index["%s"]->module = "%s";', $table->row->attrs->oid, $no, $index->attrs->module);
                                if($index->attrs->name) $buff .= sprintf('$smi_info->smi[".%s"]->linkage->index["%s"]->name = "%s";', $table->row->attrs->oid, $no, $index->attrs->name);
                            }
                        }

                        if($table->description->body) $buff .= sprintf('$smi_info->smi[".%s"]->description = "%s";', $table->row->attrs->oid, preg_replace("/\"/","'",$table->description->body));

                        if($table->row->column) {
                            if(!is_array($table->row->column)) $table->row->column = array($table->row->column);

                            foreach($table->row->column as $column) {
                                $buff .= sprintf('$smi_info->oid[".%s"] = "%s";', $column->attrs->oid, $column->attrs->name);
                                $buff .= sprintf('$smi_info->smi[".%s"]->type = "%s";', $column->attrs->oid, "column");
                                $buff .= sprintf('$smi_info->smi[".%s"]->name = "%s";', $column->attrs->oid, $column->attrs->name);
                                $depth = explode('.', $column->attrs->oid);
                                $buff .= sprintf('$smi_info->smi[".%s"]->depth = "%s";', $column->attrs->oid, count($depth)-1);

                                if($column->attrs->status) $buff .= sprintf('$smi_info->smi[".%s"]->status = "%s";', $column->attrs->oid, $column->attrs->status);
                                if($column->access->body) $buff .= sprintf('$smi_info->smi[".%s"]->access = "%s";', $column->attrs->oid, $column->access->body);
                                if($column->description->body) $buff .= sprintf('$smi_info->smi[".%s"]->description = "%s";', $column->attrs->oid, preg_replace("/\"/","'",$column->description->body));

                                if($column->syntax->typedef) {
                                    $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->basetype = "%s";', $column->attrs->oid, $column->syntax->typedef->attrs->basetype);
                                    if($column->syntax->typedef->range->attrs->min) $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->range->min = "%s";', $column->attrs->oid, $column->syntax->typedef->range->attrs->min);
                                    if($column->syntax->typedef->range->attrs->max) $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->range->max = "%s";', $column->attrs->oid, $column->syntax->typedef->range->attrs->max);

                                    if($column->syntax->typedef->namednumber) {
                                        if(!is_array($column->syntax->typedef->namednumber)) $column->syntax->typedef->namednumber = array($column->syntax->typedef->namednumber);

                                        foreach($column->syntax->typedef->namednumber as $namednumber) {
                                            $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->namednumber["%s"]->name = "%s";', $column->attrs->oid, $namednumber->attrs->number, $namednumber->attrs->name);
                                            $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->namednumber["%s"]->number = "%s";', $column->attrs->oid, $namednumber->attrs->number, $namednumber->attrs->number);
                                        }
                                    }
                                }

                                if($sysDescr->syntax->type) {
                                    $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->basetype = "%s";', $column->attrs->oid, $column->syntax->type->attrs->name);
                                    if($column->syntax->type->attrs->module) $buff .= sprintf('$smi_info->smi[".%s"]->syntax->type->module = "%s";', $column->attrs->oid, $column->syntax->type->attrs->module);
                                }
                            }
                        }
                    }
                }
            }

            $buff = '<?php if(!defined("__ZBXE__")) exit(); '.$buff.' ?>';
            FileHandler::writeFile($cache_file, $buff);

            return;
        }

        /**
         * @brief SNMP 주소에 해당되는 값들을 캐쉬로 만들어서 출력함 (속도, 부하 절감)
         **/
        function _snmpwalkCache($host = null, $community = null, $mib = null, $delay = 500000, $numeric = true, $quick = true, $enum = true, $start = 1, $end = false) {
            if($host == null || $community == null) {
                $oids->data['NULL']->value = 'NULL';
                return $oids;
            }

            $oNmsModel = &getModel('nms');

            // OID표시를 numeric 선택, 순수 정보값 선택
            if(function_exists(snmp_set_oid_numeric_print)) snmp_set_oid_numeric_print($numeric);
            if(function_exists(snmp_set_quick_print)) snmp_set_quick_print($quick);
            if(function_exists(snmp_set_enum_print)) snmp_set_enum_print($enum);

            // sysDescr 정보를 확인해서 값이 안나오면 snmp 동작을 하지 않는걸로 판단함
            $output = $oNmsModel->_snmpget($host, $community, ".1.3.6.1.2.1.1.1.0");
            if($output == 'NULL') return;

            $cache_name = ($numeric)?'snmpwalkoid':'snmpwalkmib';
            $cache_file = sprintf('./files/cache/nms/snmpwalk/%s_%s.cache.php', $host, $cache_name);

            if(file_exists($cache_file)) {
                @include($cache_file);

                if(is_array($mib)) $mib = $mib[0];

                $path = _XE_PATH_.'modules/nms/';
                $smis_path = $path.'tpl/smis/';
                $output = @FileHandler::readDir($smis_path);

                if($output) {
                    foreach($output as $key => $val) {
                        $smi_file = sprintf('./files/cache/nms/smi/%s.cache.php', preg_replace("/\.xml$/", "", $val));
                        @include($smi_file);
                    }
                }

                // 요청된 OID 이름을 OID numeric으로 변환
                $mib = $oNmsModel->getNmsMibByOid($mib, $smi_info->oid);

                $i=0;
                foreach($snmpwalk as $oid => $val) {
                    if($oid == "") continue;
                    if(!preg_match("/^\\".$mib."\./", $oid)) continue;

                    /* 요청된 결과가 많을 경우 횟수를 나눠서 처리시도(mbrowser.js의 요청정보와 연동)  */
                    $i++;
                    if($i < $start) continue;
                    if($end && ($i >= ($start+$end))) continue;

                    $snmp = $oNmsModel->_snmpget($host, $community, $oid, $delay, $numeric, $quick, $enum);
                    // 결과에 나오는 OID numeric을 MIB로 변경
                    $snmp_mib = $oNmsModel->getNmsOidByMib($oid, $smi_info->oid);
                    if($snmp == 'NULL') $oids->data[$snmp_mib]->value = "";
                    // 결과 값에 OID형식이 있을 경우 MIB명으로 변경
                    elseif(preg_match("/^OID\: /", $snmp)) $oids->data[$snmp_mib]->value = "OID: ".$oNmsModel->getNmsOidByMib(preg_replace(array("/^OID\: /","/iso/"), array("",1), $snmp), $smi_info->oid);
                    elseif(preg_match("/(Desc|netInstance)/", $snmp_mib)) $oids->data[$snmp_mib]->value = $oNmsModel->hexStr2Ascii($snmp);
                    else $oids->data[$snmp_mib]->value = $snmp;
                    // MBrowser tree의 결과 title과 타켓처리를 위해 별도로 OID 정보를 저장
                    if($snmp != 'NULL')  $oids->data[$snmp_mib]->numeric = ($numeric)?$oid:$oNmsModel->getNmsOidByMib($oid, $smi_info->oid);
                }

                if(!$oids->data && $start == 1) {
                    $i++;
                    $snmp = $oNmsModel->_snmpget($host, $community, $mib, $delay, $numeric, $quick, $enum);
                    // 결과에 나오는 OID numeric을 MIB로 변경
                    $snmp_mib = $oNmsModel->getNmsOidByMib($mib, $smi_info->oid);
                    if($snmp == 'NULL') $oids->data['NULL']->value = 'NULL';
                    // 결과 값에 OID형식이 있을 경우 MIB명으로 변경
                    elseif(preg_match("/^OID\: /", $snmp)) $oids->data[$snmp_mib]->value = "OID: ".$oNmsModel->getNmsOidByMib(preg_replace(array("/^OID\: /","/iso/"), array("",1), $snmp), $smi_info->oid);
                    elseif(preg_match("/(Desc|netInstance)/", $snmp_mib)) $oids->data[$snmp_mib]->value = $oNmsModel->hexStr2Ascii($snmp);
                    else $oids->data[$snmp_mib]->value = $snmp;
                    // MBrowser tree의 결과 title과 타켓처리를 위해 별도로 OID 정보를 저장
                    if($snmp != 'NULL') $oids->data[$snmp_mib]->numeric = ($numeric)?$mib:$oNmsModel->getNmsOidByMib($mib, $smi_info->oid);
                }

                $oids->total = $i;

                return $oids;
            } else {
                /* 캐쉬 파일이 없을 경우는 요청받은 mib값들의 전체주소를 캐쉬파일로 저장 */
                if(!is_array($mib)) $mib = array($mib);

                $buff = "";
                foreach($mib as $key => $mib_numeric) {
                    $snmp = $oNmsModel->_snmpwalkoid($host, $community, $mib_numeric, $delay, $numeric, $quick, $enum);
                    if($snmp == null) $oids->data[0]->value = 'NULL';
                    else {
                        // 요청에 사용했던 주소를 캐쉬로 저장
                        $buff .= sprintf('$snmpwalk["%s"] = %s;', $mib_numeric, true);
                        $i = 0;
                        foreach($snmp as $oid => $val) {
                            $i++;
                            $buff .= sprintf('$snmpwalk["%s"] = %s;', $oid, true);
                            // 결과에 나오는 OID numeric을 MIB로 변경
                            $snmp_mib = $oNmsModel->getNmsOidByMib($oid, $smi_info->oid);
                            // 결과 값에 OID형식이 있을 경우 MIB명으로 변경
                            if(preg_match("/^OID\: /", $val)) $oids->data[$snmp_mib]->value = "OID: ".$oNmsModel->getNmsOidByMib(preg_replace(array("/^OID\: /","/iso/"), array("",1), $val), $smi_info->oid);
                            else $oids->data[$snmp_mib]->value = $val;
                            // MBrowser tree의 결과 title과 타켓처리를 위해 별도로 OID 정보를 저장
                            $oids->data[$snmp_mib]->numeric = ($numeric)?$oid:$oNmsModel->getNmsOidByMib($oid, $smi_info->oid);
                        }
                    }
                }

                $oids->total = $i;

                $buff = '<?php if(!defined("__ZBXE__")) exit(); '.$buff.' ?>';
                FileHandler::writeFile($cache_file, $buff);

                return $oids;
            }
        }
    }
?>