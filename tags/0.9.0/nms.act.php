<?php
    /**
     * @class  nmsAct
     * @author largeden (developer@nmsxe.com)
     * @brief  nmsXE Act Class
     **/

    class nmsAct extends nms {
        /**
         * @brief mail act 처리 함수
         **/
        function mail(&$args) {
            if(!$args) return;
            $oNmsController = &getController('nms');
            $oNmsView = &getView('nms');
            $oNmsModel = &getModel('nms');
            $oTemplateHandler = new TemplateHandler();

            $args->act_value = explode(',', $args->act_value);
            $senders = explode(':', $args->act_value[0]);

            // 보내는사람 정보 설정
            if($senders[0] == 'member_srl') {
                $sender = $oNmsModel->getNmsActMember($args, $senders[1]);
                $mail->sender->email = $sender->var;
                $mail->sender->name = $sender->name;
            } elseif($senders[0] == 'group_srl') {
                $sender = $oNmsModel->getNmsActGroup($args, $senders[1]);
                foreach($sender as $val) {
                    $mail->sender->email = $val->var;
                    $mail->sender->name = $val->name;
                }
            } else {
                $mail->sender->email = $senders[1];
                $mail->sender->name = $senders[0];
            }

            // 받는사람 정보 설정
            foreach($args->act_value as $email) {
                if(!$email) continue;

                $receives = explode(':',$email);

                if($receives[0] == 'member_srl') {
                    $receive = $oNmsModel->getNmsActMember($args, $receives[1]);
                    $receiptor_email->email = $receive->var;
                    $receiptor_email->name = $receive->name;
                    $receiptor[] = $receiptor_email;
                } elseif($receives[0] == 'group_srl') {
                    $receive = $oNmsModel->getNmsActGroup($args, $receives[1],1000);
                    foreach($receive as $val) {
                        $receiptor_email->email = $val->var;
                        $receiptor_email->name = $val->name;
                        $receiptor[] = $receiptor_email;
                        unset($receiptor_email);
                    }
                } else {
                    $receiptor_email->email = $receives[1];
                    $receiptor_email->name = $receives[0];
                    $receiptor[] = $receiptor_email;
                }

                unset($receiptor_email);
            }

            // 템플릿에서 사용할 변수 설정
            Context::set('args', $args);

            // 템플릿 해석 처리
            if($args->act_path && file_exists($args->act_path)) {
                $filepath = explode('/', $args->act_path);
                $path = preg_replace('/'.$filepath[count($filepath)-1].'/', '', $args->act_path);
                $file = $filepath[count($filepath)-1];
                $output = $oTemplateHandler->compile($path, $file);
            } elseif($args->act_form) {
                FileHandler::writeFile(_XE_PATH_.'/modules/nms/socket/cache/form.html', $args->act_form);
                $output = $oTemplateHandler->compile(_XE_PATH_.'/modules/nms/socket/cache/', 'form.html');
                FileHandler::removeFile(_XE_PATH_.'/modules/nms/socket/cache/form.html');
            } else return;

            // 해석된 템플릿에서 첨부파일용 이미지 추출
            preg_match_all('!<img([^\>]*)src=([^\>]*?)\>!is', $output, $matches);
            foreach($matches[0] as $key => $val) {
                preg_match_all('/([^=^"^ ]*)src=([^ ^>]*)/i', $val, $src_match);
                $src[] = preg_replace('/\"/','',$src_match[2][0]);
            }

            // graph 호출용 url일경우 첨부파일용 이미지로 처리
            foreach($src as $key => $val) {
                if(!preg_match('/act\=dispNmsGraph/',$val)) continue;

                $val_explode = explode('&amp;',$val);
                foreach($val_explode as $name) {
                    $name = explode('=',$name);
                    if(preg_match('/\?act/',$name[0])) continue;
                    $graph->{$name[0]} = $name[1];
                }

                // graph를 최신내용으로 갱신 후 첨부파일 생성
                $graph_url = $oNmsView->dispNmsGraph($graph);
                $dir = FileHandler::readDir($graph_url);
                foreach($dir as $file) $graph_url .= $file;
                $graph_name = "{$graph->mid}_{$args->mmid}_{$graph->mode}.png";
                $mail->attach[$key]->fileurl = $graph_url;
                $mail->attach[$key]->filename = $graph_name;
                // img 태그에서 첨부파일용으로 쓰인 호출 url을 메일 본문에서 사용할 첨부파일명으로 변경
                $output = str_replace($val,"./".$graph_name,$output);

                unset($graph);
                unset($graph_name);
                unset($graph_url);
            }

            // 메일 제목, 본문 저장 (h2태그가 있으면 제목으로 처리 없으면 전체 값에서 길이 조정)
            preg_match_all('!<h3>([^\>]*)</h3>!is', $output, $title);
            if($title[1]) $mail->title = $title[1][0];
            else $mail->title = cut_str(strip_tags($output), 50, '...');
            $mail->content = $output;

            // ACT 발생 로그 시작
            $args->act_log->value = $blogapi->address;
            $args->act_log->type = 'start';
            $output = ModuleHandler::triggerCall("nms.logNmsAct", "check", $args);

            foreach($receiptor as $key => $receiptors) {
                $mail->receiptor[0] = $receiptors;

                // ACT 발생 로그 시작
                $args->act_log->value = $receiptors->name;
                $args->act_log->type = 'start';
                ModuleHandler::triggerCall("nms.logNmsAct", "check", $args);

                // 메일 보내기
                $oNmsController->sendMail($mail);

                // ACT 발생 로그 완료
                $args->act_log->result = 'complete';
                $args->act_log->type = 'end';
                ModuleHandler::triggerCall("nms.logNmsAct", "check", $args);
            }

            return;
        }

        /**
         * @brief twitter act 처리 함수
         **/
        function twitter(&$args) {
            if(!$args) return;
            $oModuleModel = &getModel('module');
            $oNmsModel = &getModel('nms');
            $oTemplateHandler = new TemplateHandler();
            $oNms = $oModuleModel->getModuleConfig('nms');

            require_once(_XE_PATH_.'/modules/nms/libs/twitteroauth/twitteroauth.php');

            if(!$oNms->twitter_config->consumer_key || !$oNms->twitter_config->consumer_secret) return;

            define('CONSUMER_KEY', $oNms->twitter_config->consumer_key);
            define('CONSUMER_SECRET', $oNms->twitter_config->consumer_secret);
            define('OAUTH_CALLBACK', Context::get('request_uri')."?module=nms&act=getNmsTwitterOauth");

            $args->act_value = explode(',', $args->act_value);

            // Twitter OAuth 정보 설정
            foreach($args->act_value as $oauth) {
                if(!$oauth) continue;

                $twitter_oauth = null;
                $oauths = explode(':',$oauth);
                if($oauths[0] == 'member_srl') {
                    $twitter_info = $oNmsModel->getNmsActMember($args, $oauths[1]);
                    if(!$twitter_info->var[0] && !$twitter_info->var[1]) continue;
                    $twitter_oauth->oauth_token = $twitter_info->var[0];
                    $twitter_oauth->oauth_token_secret = $twitter_info->var[1];
                    $twitter_oauth->user_name = $twitter_info->name;
                    $twitter[] = $twitter_oauth;
                } elseif($oauths[0] == 'group_srl') {
                    $twitter_info = $oNmsModel->getNmsActGroup($args, $oauths[1],1000);
                    foreach($twitter_info as $val) {
                        if(!$val->var[0] && !$val->var[1]) continue;
                        $twitter_oauth->oauth_token = $val->var[0];
                        $twitter_oauth->oauth_token_secret = $val->var[1];
                        $twitter_oauth->user_name = $val->name;
                        $twitter[] = $twitter_oauth;
                        unset($twitter_oauth);
                    }
                } else {
                    $twitter_oauth->oauth_token = $oauths[0];
                    $twitter_oauth->oauth_token_secret = $oauths[1];
                    $twitter_oauth->user_name = $i++;
                    $twitter[] = $twitter_oauth;
                }

                unset($twitter_oauth);
            }

            // 템플릿에서 사용할 변수 설정
            Context::set('args', $args);

            // 템플릿 해석 처리
            if($args->act_path && file_exists($args->act_path)) {
                $filepath = explode('/', $args->act_path);
                $path = preg_replace('/'.$filepath[count($filepath)-1].'/', '', $args->act_path);
                $file = $filepath[count($filepath)-1];
                $output = $oTemplateHandler->compile($path, $file);
            } elseif($args->act_form) {
                FileHandler::writeFile(_XE_PATH_.'/modules/nms/socket/cache/form.html', $args->act_form);
                $output = $oTemplateHandler->compile(_XE_PATH_.'/modules/nms/socket/cache/', 'form.html');
                FileHandler::removeFile(_XE_PATH_.'/modules/nms/socket/cache/form.html');
            } else return;

            // bit.ly 주소 설정
            if($oNms->bitly_config->username || $oNms->bitly_config->apikey) {
                $url = urlencode(Context::get('request_uri')."?mid={$args->mid}&group_name={$args->group_name}");
                $bitly = file_get_contents("http://api.bit.ly/v3/shorten?login={$oNms->bitly_config->username}&apiKey={$oNms->bitly_config->apikey}&format=json&longUrl={$url}");
                $bitly = json_decode($bitly, true);

                if($bitly['status_code'] != 200) $url = "";
                else $url = $bitly['data']['url'];
            }

            // Twiiter 의 140글 규칙에 맞춰 글을 줄임 (bit.ly가 있으면 100글이 될때까지 줄임)
            if($url) $twitter_str = 100;
            else $twitter_str = 140;
            $lenth = mb_strlen($output, 'utf8');
            if($lenth > $twitter_str) {
                $lenth = strlen($output);
                while(mb_strlen(@cut_str($output,$lenth,''), 'utf8') > $twitter_str) $lenth-=10;

                $output = @cut_str($output,$lenth,'...');
            }

            // bit.ly 주소 입력
            if($url) $output .= " {$url}";

            // Twitter로 글 등록
            foreach($twitter as $oauth) {
                // ACT 발생 로그 시작
                $args->act_log->value = $oauth->user_name;
                $args->act_log->type = 'start';
                ModuleHandler::triggerCall('nms.logNmsAct', 'check', $args);

                $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth->oauth_token, $oauth->oauth_token_secret);
                // 글조회
                //$content = $connection->get('account/verify_credentials');
                // 글등록
                $content = $connection->post('statuses/update', array('status' => $output));

                // ACT 발생 로그 완료
                if($content->error) $args->act_log->result = 'fail';
                else $args->act_log->result = 'complete';
                $args->act_log->type = 'end';
                ModuleHandler::triggerCall('nms.logNmsAct', 'check', $args);
            }

            return;
        }

        /**
         * @brief smsXE act 처리 함수
         **/
        function smsxe(&$args) {
            if(!$args) return;
            $oSmsModel = &getModel('sms');
            if(!$oSmsModel) return;
            $oNmsModel = &getModel('nms');
            $oTemplateHandler = new TemplateHandler();

            $args->act_value = explode(',', $args->act_value);
            $senders = explode(':', $args->act_value[0]);

            // 보내는사람 정보 설정
            if($senders[0] == 'member_srl') {
                $sender = $oNmsModel->getNmsActMember($args, $senders[1]);
                $sender_sms->rec_pcs = $sender->var;
            } elseif($senders[0] == 'group_srl') {
                $sender = $oNmsModel->getNmsActGroup($args, $senders[1]);
                foreach($sender as $val) $sender_sms->rec_pcs = $sender->var;
            } else {
                $sender_sms->rec_pcs = $senders[0];
            }

            // 받는사람 정보 설정
            foreach($args->act_value as $sms) {
                if(!$sms) continue;

                $receives = explode(':',$sms);

                if($receives[0] == 'member_srl') {
                    $receive = $oNmsModel->getNmsActMember($args, $receives[1]);
                    $receiptor->send_pcs = $receive->var;
                    $receiptor->name = $receive->name;
                    $receiptor_sms[] = $receiptor;
                } elseif($receives[0] == 'group_srl') {
                    $receive = $oNmsModel->getNmsActGroup($args, $receives[1],1000);
                    foreach($receive as $val) {
                        $receiptor->send_pcs = $val->var;
                        $receiptor->name = $val->name;
                        $receiptor_sms[] = $receiptor;
                        unset($receiptor);
                    }
                } else {
                    $receiptor->send_pcs = $receives[1];
                    $receiptor->name = $receives[0];
                    $receiptor_sms[] = $receiptor;
                }

                unset($receiptor);
            }

            // 템플릿에서 사용할 변수 설정
            Context::set('args', $args);

            // 템플릿 해석 처리
            if($args->act_path && file_exists($args->act_path)) {
                $filepath = explode('/', $args->act_path);
                $path = preg_replace('/'.$filepath[count($filepath)-1].'/', '', $args->act_path);
                $file = $filepath[count($filepath)-1];
                $output = $oTemplateHandler->compile($path, $file);
            } elseif($args->act_form) {
                FileHandler::writeFile(_XE_PATH_.'/modules/nms/socket/cache/form.html', $args->act_form);
                $output = $oTemplateHandler->compile(_XE_PATH_.'/modules/nms/socket/cache/', 'form.html');
                FileHandler::removeFile(_XE_PATH_.'/modules/nms/socket/cache/form.html');
            } else return;

            $output = preg_replace(array('/\&nbsp;/','/\&amp;/i','/\&/i'),array(' ','',''),$oNmsModel->cut_str(strip_tags($output), 80));
            if(is_array($sender_sms->rec_pcs)) $sender_sms->rec_pcs = implode('',$sender_sms->rec_pcs);

            $target = 'mode='.$oSmsModel->sendEncode('user_check');
            $target .= '&send_method='.$oSmsModel->sendEncode('direct');
            $target .= '&appoint=';
            $target .= '&content='.$output;
            $target .= '&bhead=';
            $target .= '&rec_pcs='.$oSmsModel->sendEncode(preg_replace('/[a-z\'\" \-\.\,\/\(\)]/i','',$sender_sms->rec_pcs));
            $target .= '&site='.$oSmsModel->sendEncode($oSmsModel->domain);
            $target .= '&site_id='.$oSmsModel->sendEncode($oSmsModel->auth_id);
            $target .= '&authkey='.$oSmsModel->sendEncode($oSmsModel->auth_key);

            foreach($receiptor_sms as $pcs) {
                if(is_array($pcs->send_pcs)) $pcs->send_pcs = implode('',$pcs->send_pcs);

                // ACT 발생 로그 시작
                $args->act_log->value = $pcs->name;
                $args->act_log->type = 'start';
                ModuleHandler::triggerCall('nms.logNmsAct', 'check', $args);

                $send .= $target.'&send_pcs[]='.preg_replace('/[a-z\'\" \-\.\,\/\(\)]/i','',$pcs->send_pcs);
                $result = $oSmsModel->sendPerbiz($send);

                // ACT 발생 로그 완료
                $result = unserialize(trim($result));
                if($result['code'] != '17') $args->act_log->result = 'fail';
                else $args->act_log->result = 'complete';
                $args->act_log->type = 'end';
                ModuleHandler::triggerCall('nms.logNmsAct', 'check', $args);
            }

            return;
        }

        /**
         * @brief blogAPI act 처리 함수
         **/
        function blogapi(&$args) {
            $oNmsView = &getView('nms');
            $oTemplateHandler = new TemplateHandler();

            $args->act_value = explode(',', $args->act_value);

            foreach($args->act_value as $key => $blogapi) {
                $str->address = explode('@',$blogapi);
                if(count($str->address)<2) continue;
                $str->address[0] = preg_replace('/^http:\/\//','',$str->address[0]);
                $str->passwd = explode(':',$str->address[0]);
                if(count($str->passwd)<2) continue;
                $str->user_id = $str->passwd[0];
                $str->passwd = $str->passwd[1];
                $str->module = explode(':',$str->address[1]);
                if(count($str->module)<2) continue;
                $str->address = preg_replace('/\/$/','',$str->module[0]);
                $str->module = $str->module[1];

                $api[] = $str;
                unset($str);
            }

            // 템플릿에서 사용할 변수 설정
            Context::set('args', $args);

            // 템플릿 해석 처리
            if($args->act_path && file_exists($args->act_path)) {
                $filepath = explode('/', $args->act_path);
                $path = preg_replace('/'.$filepath[count($filepath)-1].'/', '', $args->act_path);
                $file = $filepath[count($filepath)-1];
                $output = $oTemplateHandler->compile($path, $file);
            } elseif($args->act_form) {
                FileHandler::writeFile(_XE_PATH_.'/modules/nms/socket/cache/form.html', $args->act_form);
                $output = $oTemplateHandler->compile(_XE_PATH_.'/modules/nms/socket/cache/', 'form.html');
                FileHandler::removeFile(_XE_PATH_.'/modules/nms/socket/cache/form.html');
            } else return;

            // 해석된 템플릿에서 첨부파일용 이미지 추출
            preg_match_all('!<img([^\>]*)src=([^\>]*?)\>!is', $output, $matches);
            foreach($matches[0] as $key => $val) {
                preg_match_all('/([^=^"^ ]*)src=([^ ^>]*)/i', $val, $src_match);
                $src[] = preg_replace('/\"/','',$src_match[2][0]);
            }
            // graph 호출용 url일경우 첨부파일용 이미지로 처리
            foreach($src as $key => $val) {
                if(!preg_match('/act\=dispNmsGraph/',$val)) continue;

                $val_explode = explode('&amp;',$val);
                foreach($val_explode as $name) {
                    $name = explode('=',$name);
                    if(preg_match('/\?act/',$name[0])) continue;
                    $graph->{$name[0]} = $name[1];
                }

                // graph를 최신내용으로 갱신 후 첨부파일 생성
                $graph_url = $oNmsView->dispNmsGraph($graph);
                $dir = FileHandler::readDir($graph_url);
                foreach($dir as $file) $graph_url .= $file;
                $graph_name = "{$graph->mid}_{$args->mmid}_{$graph->mode}.png";
                $attach[$key]->fileurl = $graph_url;
                $attach[$key]->filename = $graph_name;
                // img 태그에서 첨부파일용으로 쓰인 호출 url의 이미지를 제거
                $val = preg_replace(array('/\//','/\?/'),array('\\/','\\?'),$val);
                $output = preg_replace("/\<img([^\>]*?)src=\"".$val."\"([^\>]*?)\>/","",$output);
                unset($graph);
                unset($graph_name);
                unset($graph_url);
            }

            // 메일 제목, 본문 저장 (h2태그가 있으면 제목으로 처리 없으면 전체 값에서 길이 조정)
            preg_match_all('!<h3>([^\>]*)</h3>!is', $output, $title);
            if($title[1]) $title = $title[1][0];
            else $title = cut_str(strip_tags($output), 50, '...');

            foreach($attach as $val) {
                $files .= "<member><name><![CDATA[name]]></name><value><string><![CDATA[{$val->filename}]]></string></value></member>
                          <member><name><![CDATA[type]]></name><value><string><![CDATA[image/png]]></string></value></member>
                          <member><name><![CDATA[bits]]></name><value><base64><![CDATA[".base64_encode(FileHandler::readFile($val->fileurl))."]]></base64></value></member>";
            }

            foreach($api as $blogapi) {
                $uri = "http://{$blogapi->address}";

                // ACT 발생 로그 시작
                $args->act_log->value = $blogapi->address;
                $args->act_log->type = 'start';
                ModuleHandler::triggerCall('nms.logNmsAct', 'check', $args);

                // 첨부파일 등록
                foreach($attach as $val) {
                    $body = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
                            <methodCall>
                            <methodName>metaWeblog.newMediaObject</methodName>
                            <params>
                            <param><value><string><![CDATA[{$blogapi->module}]]></string></value></param>
                            <param><value><string><![CDATA[{$blogapi->user_id}]]></string></value></param>
                            <param><value><string><![CDATA[{$blogapi->passwd}]]></string></value></param>
                            <param><value><struct>
                            <member><name><![CDATA[name]]></name><value><string><![CDATA[{$val->filename}]]></string></value></member>
                            <member><name><![CDATA[type]]></name><value><string><![CDATA[image/png]]></string></value></member>
                            <member><name><![CDATA[bits]]></name><value><base64><![CDATA[".base64_encode(FileHandler::readFile($val->fileurl))."]]></base64></value></member>
                            </struct></value></param>
                            </params>
                            </methodCall>";

                    $buff = @FileHandler::getRemoteResource($uri, $body, 3, 'POST', 'application/octet-stream');
                }

                // 글 등록
                $body = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
                        <methodCall>
                        <methodName>metaWeblog.newPost</methodName>
                        <params>
                        <param><value><string><![CDATA[{$blogapi->module}]]></string></value></param>
                        <param><value><string><![CDATA[{$blogapi->user_id}]]></string></value></param>
                        <param><value><string><![CDATA[{$blogapi->passwd}]]></string></value></param>
                        <param><value><struct>
                        <member><name><![CDATA[title]]></name><value><string><![CDATA[{$title}]]></string></value></member>
                        <member><name><![CDATA[description]]></name><value><string><![CDATA[{$output}]]></string></value></member>
                        </struct></value></param>
                        <param>
                        <value>
                        <boolean>1</boolean>
                        </value>
                        </param>
                        </params>
                        </methodCall>";

                // blogApi 보내기
                $buff = @FileHandler::getRemoteResource($uri, $body, 3, 'POST', 'application/octet-stream');

                // ACT 발생 로그 완료
                $oXmlParser = new XmlParser();
                $obj = $oXmlParser->parse($buff);
                if(!$obj->methodresponse->params->param->value->string->body) $args->act_log->result = 'fail';
                else $args->act_log->result = 'complete';
                $args->act_log->type = 'end';
                ModuleHandler::triggerCall('nms.logNmsAct', 'check', $args);
            }

            return;
        }
    }
?>