<?php
    /**
     * @class  nmsSocket
     * @author largeden (developer@nmsxe.com)
     * @brief  nmsXE Socket class
     **/

    class nmsSocket {

        var $socketClass;
        var $socketType;
        var $socketTime;
        var $FileHandler;

        /**
         * @brief 유일한 nmsSocket 객체를 반환 (Singleton)
         * nmsSocket는 어디서든 객체 선언없이 사용하기 위해서 static 하게 사용
         **/
        function &getInstance() {
            static $theInstance;
            if(!isset($theInstance)) $theInstance = new nmsSocket();
            return $theInstance;
        }

        /**
         * @brief 초기화
         **/
        function init() {
            // linux, mac osx 등에서 백그라운드 실행 시 간혹 timezone 오류가 나타나기 때문에 이를 직접 선언
            date_default_timezone_set(@date_default_timezone_get());

            // _XE_PATH_ 생성
            define('_XE_PATH_', str_replace('modules/nms/nms.socket.php', "", str_replace("\\", "/", __FILE__)));

            // FileHandler(PEAR,Socket) 등을 사용하기 위해서 XE 정보를 선언
            define('__ZBXE__', true);

            // OS 정보를 선언
            if (substr(PHP_OS, 0, 3) == 'WIN') define('PEAR_OS', 'Windows');
            else define('PEAR_OS', 'Unix');

            require_once(_XE_PATH_.'config/config.inc.php');
        }

        /**
         * @brief 소켓 실행 명령어 함수
         **/
        function procSocket() {
            $this->init();
            $this->socketClass = $_SERVER['argv'][1];
            $this->socketType = $_SERVER['argv'][2];
            $this->socketTime = date('YmdHis');
            $this->FileHandler = new FileHandler();

            switch($this->socketClass) {
                case 'cron':
                    if($this->socketType=='stop') $this->procSocketStop();
                    else $this->procCron();
                    break;
                case 'syslog':
                    if($this->socketType=='start') $this->procSyslog();
                    elseif($this->socketType=='stop') $this->procSocketStop();
                    break;
                case 'snmptrap':
                    if($this->socketType=='start') $this->procSnmpTrap();
                    elseif($this->socketType=='stop') $this->procSocketStop();
                    break;
                case 'compress':
                    if($this->socketType=='start') $this->procCompress();
                    elseif($this->socketType=='stop') $this->procSocketStop();
                    break;
                case 'call':
                    $this->procCronCall($this->socketType);
                default:
                    break;
            }

        }

        /**
         * @brief Cron 시작
         **/
        function procCronStart(){
            $this->FileHandler->writeFile(_XE_PATH_.'modules/nms/socket/cron',$this->socketTime.":1\n",'a');
        }

        /**
         * @brief 소켓을 실행할 때 실행용 파일을 생성
         **/
        function procSocketStart(){
            $this->FileHandler->writeFile(_XE_PATH_.'modules/nms/socket/'.$this->socketClass,true,'w');
        }

        /**
         * @brief 특정주기로 지정된 폴더안에 있는 파일을 검색하여 API Call 처리 시도
         **/
        function procCronSearch($crontype) {
            if(!$crontype) return;
            $path = _XE_PATH_.'modules/nms/';
            $cron_path = $path.'socket/crontab/'.$crontype.'/';

            $output = $this->FileHandler->readDir($cron_path);
            if(!$output) return;

            foreach($output as $file) {
                $site = array();
                $site = unserialize($this->FileHandler->readFile($cron_path.$file));

                if(PEAR_OS == 'Unix') shell_exec("php -q ".$path."nms.socket.php call ".$cron_path.$file." > /dev/null &");
                else pclose(popen("start /B ". "php -q ".$path."nms.socket.php call ".$cron_path.$file, 'r'));
            }
        }

        /**
         * @brief 요청들어온 API Call을 처리 시도
         **/
        function procCronCall($path) {
            if(!$path) return;
            $site = array();
            $site = unserialize($this->FileHandler->readFile($path));

            $uri = $site['uri'];
            foreach($site['args'] as $key => $val) {
                $site['body'] .= sprintf("<%s><![CDATA[%s]]></%s>",$key,$val,$key);
            }
            $body = sprintf("<?xml version=\"1.0\" encoding=\"utf-8\" ?><methodCall><params>%s</params></methodCall>", $site['body']);

            $buff = @$this->FileHandler->getRemoteResource($uri, $body, 3, 'POST', 'application/xml');
        }

        /**
         * @brief UDP로 Syslog, Snmp Trap 요청이 들어오면 해당 경로의 파일안의 내용주소로 API Call
         **/
        function procUdpCall($type, $msg) {
            if(!$msg) return;
            $msg = urlencode($msg);
            $path = _XE_PATH_.'modules/nms/socket/crontab/'.$type.'/'.$type;

            $site = array();
            $site = unserialize($this->FileHandler->readFile($path));

            $uri = $site['uri'];
            $site['args']['msg'] = $msg;
            foreach($site['args'] as $key => $val) {
                $site['body'] .= sprintf("<%s><![CDATA[%s]]></%s>",$key,$val,$key);
            }
            $body = sprintf("<?xml version=\"1.0\" encoding=\"utf-8\" ?><methodCall><params>%s</params></methodCall>", $site['body']);

            $buff = @$this->FileHandler->getRemoteResource($uri, $body, 3, 'POST', 'application/xml');
        }

        /**
         * @brief 데이터 압축 API Call
         **/
        function procCompressCall($type) {
            if(!$type) return;
            $path = _XE_PATH_.'modules/nms/socket/crontab/'.$type.'/'.$type;

            $site = array();
            $site = unserialize($this->FileHandler->readFile($path));

            $uri = $site['uri'];
            foreach($site['args'] as $key => $val) {
                $site['body'] .= sprintf("<%s><![CDATA[%s]]></%s>",$key,$val,$key);
            }
            $body = sprintf("<?xml version=\"1.0\" encoding=\"utf-8\" ?><methodCall><params>%s</params></methodCall>", $site['body']);

            $buff = @$this->FileHandler->getRemoteResource($uri, $body, 3, 'POST', 'application/xml');

            $oXmlParser = new XmlParser();
            $output = $oXmlParser->parse($buff);
            if(!$output->response->group_info->item) return;

            foreach($output->response->group_info->item as $group_info) {
               unset($site['body']);

               $site['args']['group_srl'] = $group_info->group_srl->body;
                foreach($site['args'] as $key => $val) {
                    $site['body'] .= sprintf("<%s><![CDATA[%s]]></%s>",$key,$val,$key);
                }
                $body = sprintf("<?xml version=\"1.0\" encoding=\"utf-8\" ?><methodCall><params>%s</params></methodCall>", $site['body']);

                $buff = @$this->FileHandler->getRemoteResource($uri, $body, 3, 'POST', 'application/xml');
            }
        }

        /**
         * @brief Cron을 초 단위로 반복할때마다 해당 파일내용 값을 검색
         **/
        function getCronStatus(){
            $fp = @fopen(_XE_PATH_.'modules/nms/socket/cron', 'r');
            while (!feof($fp)) {
              $crontime = explode(':',fgets($fp));
              $status[$crontime[0]] = $crontime[1];
            }
            @fclose($fp);

            return $status[$this->socketTime];
        }

        /**
         * @brief 생성된 소켓파일을 소켓수행 후 한번씩 체크
         **/
        function getSocketStatus(){
            return $this->FileHandler->readFile(_XE_PATH_.'modules/nms/socket/'.$this->socketClass);
        }

        /**
         * @brief cron 실행 함수 (초 단위의 명령대로 반복 실행)
         **/
        function procCron() {
            if(!$this->socketType) return;
            $this->procCronStart();
            set_time_limit(0);

            do
            {
                if(!$this->getCronStatus()) return;

                $this->proc_msg(date('s')."seconds is recorded ".$this->socketType);

                $this->procCronSearch($this->socketType);

                sleep($this->socketType);
            }
            while($this->getCronStatus());

            return false;
        }

        /**
         * @brief Compress 실행 (1시간(3600초) 단위의 명령대로 반복 실행)
         **/
        function procCompress() {
            if(!$this->socketType) return;
            $this->procCronStart();
            set_time_limit(0);

            do
            {
                if(!$this->getCronStatus()) return;

                // 최초 수행시에는 실행하지 않음(1시간 뒤부터 실행 시작)
                if($Compress) {
                    $this->proc_msg(date('s')."seconds is compress ".$this->socketType);

                    $this->procCompressCall('compress');
                }
                $Compress = true;
                sleep(3600);
            }
            while($this->getCronStatus());

            return false;
        }

        /**
         * @brief Syslog Listen
         **/
        function procSyslog() {
            if(!$this->socketType) return;
            $this->procSocketStart();
            set_time_limit(0);

            $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            socket_bind($socket, '0.0.0.0', 514) or die ($this->procUdpCall('syslog', "error UDP port(514)"));
            $this->proc_msg("start syslog");
            $this->procUdpCall('syslog', "[127.0.0.1:$port]<191>start syslog");

            do
            {
                $from = "";
                $port = 0;
                $buf = "";

                socket_recvfrom($socket, $buf, 65535, 0, $from, $port);
                $this->proc_msg("syslog : "."$from $buf");
                $this->procUdpCall("syslog", "[$from:$port]$buf");
            }
            while($this->getSocketStatus());

            socket_close($socket);

            return false;
        }

        /**
         * @brief Snmp Trap Listen
         **/
        function procSnmpTrap() {
            if(!$this->socketType) return;
            $this->procSocketStart();
            set_time_limit(0);

            $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            socket_bind($socket, '0.0.0.0', 162) or die ($this->procUdpCall('snmptrap', "error UDP port(162)"));
            $this->proc_msg("start snmptrap");
            $this->procUdpCall('snmptrap', "[127.0.0.1:$port]start snmptrap");

            do
            {
                $from = "";
                $port = 0;
                $buf = "";

                socket_recvfrom($socket, $buf, 65535, 0, $from, $port);
                if(!preg_match("/(snmptrap)|(stop)/", $buf)) $buf = base64_encode($buf);
                $this->proc_msg("snmptrap : "."$from $buf");
                $this->procUdpCall("snmptrap", "[$from:$port]$buf");
            }
            while($this->getSocketStatus());

            socket_close($socket);

            return false;
        }

        /**
         * @brief 실행된 소켓으로 Stop 메시지를 보내 While문을 종료 시킴
         **/
        function procSocketStop(){
            $this->FileHandler->writeFile(_XE_PATH_.'modules/nms/socket/'.$this->socketClass,false,'w');

            if($this->socketClass=='syslog') {
                $socket=socket_create(AF_INET,SOCK_DGRAM,SOL_UDP);
                $ssock=socket_connect($socket,'127.0.0.1',514);
                socket_send($socket, "<191>stop syslog", strlen("<191>stop syslog"), 0);
                socket_close($socket);
            }elseif($this->socketClass=='snmptrap') {
                $socket=socket_create(AF_INET,SOCK_DGRAM,SOL_UDP);
                $ssock=socket_connect($socket,'127.0.0.1',162);
                socket_send($socket, "stop snmptrap", strlen("stop snmptrap"), 0);
                socket_close($socket);
            }

        }

        /**
         * @brief 모든 결과치를 Text 파일로 덤프를 실행함 (필요할 경우 주석제거)
         **/
        function proc_msg($msg){
            //$this->FileHandler->writeFile(_XE_PATH_.'modules/nms/socket/temp.txt',$msg."\n",'a');
        }
    }

    $nmsSocket = &nmsSocket::getInstance();
    $nmsSocket->procSocket();
?>