<?php
    /**
     * @class  nms
     * @author largeden (developer@nmsxe.com)
     * @brief  nmsXE Class
     **/

    class nms extends ModuleObject {

        var $nms_version = '0.9.0';

        /**
         * @brief 설치시 추가 작업이 필요할시 구현
         **/
        function moduleInstall() {
            $oModuleController = &getController('module');

            $oModuleController->insertTrigger('nms.dispNmsAdminIndex', 'nms', 'view', 'triggerDispNmsHostList', 'before');
            $oModuleController->insertTrigger('nms.dispNmsAdminIndex', 'nms', 'view', 'triggerDispNmsServiceList', 'before');
            $oModuleController->insertTrigger('nms.dispNmsAdminIndex', 'nms', 'view', 'triggerDispNmsSettingWizard', 'after');
            $oModuleController->insertTrigger('nms.dispNmsConfig', 'nms', 'view', 'triggerDispNmsSmtpConfig', 'after');
            $oModuleController->insertTrigger('nms.dispNmsHostInfo', 'nms', 'view', 'triggerDispNmsSeverityConfig', 'after');
            $oModuleController->insertTrigger('nms.dispNmsSeverityList', 'nms', 'view', 'triggerDispNmsSeverityRestore', 'after');
            $oModuleController->insertTrigger('nms.deleteNmsHost', 'nms', 'controller', 'triggerDeleteNmsMib', 'after');
            $oModuleController->insertTrigger('nms.deleteNmsMib', 'nms', 'controller', 'triggerDeleteNmsSeverity', 'after');
            $oModuleController->insertTrigger('nms.dispNmsServiceInfo', 'nms', 'view', 'triggerDispNmsSeverityConfig', 'after');
            $oModuleController->insertTrigger('nms.dispNmsServiceInfo', 'nms', 'view', 'triggerDispNmsSeverityConfig', 'after');
            $oModuleController->insertTrigger('display', 'nms', 'view', 'triggerDispNmsMemberDisplay', 'before');
            $oModuleController->insertTrigger('member.updateMember', 'nms', 'controller', 'triggerUpdateMemberBefore', 'before');
            $oModuleController->insertTrigger('member.updateMember', 'nms', 'controller', 'triggerUpdateMemberAfter', 'after');
            $oModuleController->insertTrigger('nms.dispNmsConfig', 'nms', 'view', 'triggerDispNmsTwitterConfig', 'after');
            $oModuleController->insertTrigger('nms.logNmsAct', 'nms', 'controller', 'triggerLogNmsAct', 'check');
        }

        /**
         * @brief 설치가 이상이 없는지 체크하는 method
         **/
        function checkUpdate() {
            return false;
        }

        /**
         * @brief 업데이트 실행
         **/
        function moduleUpdate() {
            return new Object();
        }

        /**
         * @brief 모듈 제거
         **/
        function moduleUninstall() {
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');
            $oDB = &DB::getInstance();

            // Module Config Delete
            $args->module = 'nms';
            $output = executeQuery('module.deleteModuleConfig', $args);
            if(!$output->toBool()) {
                $oDB->rollback();
                return;
            }

            // Trigger Delete
            if($oModuleModel->getTrigger('nms.dispNmsAdminIndex', 'nms', 'view', 'triggerDispNmsHostList', 'before'))
                $oModuleController->deleteTrigger('nms.dispNmsAdminIndex', 'nms', 'view', 'triggerDispNmsHostList', 'before');
            if($oModuleModel->getTrigger('nms.dispNmsAdminIndex', 'nms', 'view', 'triggerDispNmsServiceList', 'before'))
                $oModuleController->deleteTrigger('nms.dispNmsAdminIndex', 'nms', 'view', 'triggerDispNmsServiceList', 'before');
            if($oModuleModel->getTrigger('nms.dispNmsAdminIndex', 'nms', 'view', 'triggerDispNmsSettingWizard', 'after'))
                $oModuleController->deleteTrigger('nms.dispNmsAdminIndex', 'nms', 'view', 'triggerDispNmsSettingWizard', 'after');
            if($oModuleModel->getTrigger('nms.dispNmsConfig', 'nms', 'view', 'triggerDispNmsSmtpConfig', 'after'))
                $oModuleController->deleteTrigger('nms.dispNmsConfig', 'nms', 'view', 'triggerDispNmsSmtpConfig', 'after');
            if($oModuleModel->getTrigger('nms.dispNmsHostInfo', 'nms', 'view', 'triggerDispNmsSeverityConfig', 'after'))
                $oModuleController->deleteTrigger('nms.dispNmsHostInfo', 'nms', 'view', 'triggerDispNmsSeverityConfig', 'after');
            if($oModuleModel->getTrigger('nms.dispNmsSeverityList', 'nms', 'view', 'triggerDispNmsSeverityRestore', 'after'))
                $oModuleController->deleteTrigger('nms.dispNmsSeverityList', 'nms', 'view', 'triggerDispNmsSeverityRestore', 'after');
            if($oModuleModel->getTrigger('nms.deleteNmsHost', 'nms', 'view', 'triggerDeleteNmsMib', 'after'))
                $oModuleController->deleteTrigger('nms.deleteNmsHost', 'nms', 'view', 'triggerDeleteNmsMib', 'after');
            if($oModuleModel->getTrigger('nms.deleteNmsMib', 'nms', 'view', 'triggerDeleteNmsSeverity', 'after'))
                $oModuleController->deleteTrigger('nms.deleteNmsMib', 'nms', 'view', 'triggerDeleteNmsSeverity', 'after');
            if($oModuleModel->getTrigger('nms.dispNmsServiceInfo', 'nms', 'view', 'triggerDispNmsSeverityConfig', 'after'))
                $oModuleController->deleteTrigger('nms.dispNmsServiceInfo', 'nms', 'view', 'triggerDispNmsSeverityConfig', 'after');
            if(!$oModuleModel->getTrigger('display', 'nms', 'view', 'triggerDispNmsMemberDisplay', 'before'))
                $oModuleController->deleteTrigger('display', 'nms', 'view', 'triggerDispNmsMemberDisplay', 'before');
            if(!$oModuleModel->getTrigger('display', 'nms', 'view', 'triggerDispNmsMemberDisplay', 'before'))
                $oModuleController->deleteTrigger('display', 'nms', 'view', 'triggerDispNmsMemberDisplay', 'before');
            if(!$oModuleModel->getTrigger('member.updateMember', 'nms', 'controller', 'triggerUpdateMemberBefore', 'before'))
                $oModuleController->deleteTrigger('member.updateMember', 'nms', 'controller', 'triggerUpdateMemberBefore', 'before');
            if(!$oModuleModel->getTrigger('member.updateMember', 'nms', 'controller', 'triggerUpdateMemberAfter', 'after'))
                $oModuleController->deleteTrigger('member.updateMember', 'nms', 'controller', 'triggerUpdateMemberAfter', 'after');
            if(!$oModuleModel->getTrigger('nms.dispNmsConfig', 'nms', 'view', 'triggerDispNmsTwitterConfig', 'after'))
                $oModuleController->deleteTrigger('nms.dispNmsConfig', 'nms', 'view', 'triggerDispNmsTwitterConfig', 'after');
            if(!$oModuleModel->getTrigger('nms.logNmsAct', 'nms', 'controller', 'triggerLogNmsAct', 'check'))
                $oModuleController->deleteTrigger('nms.logNmsAct', 'nms', 'controller', 'triggerLogNmsAct', 'check');

            // Modules Delete
            $args->list_count = 100000;
            $output = executeQueryArray('module.getModuleMidList', $args);
            if($output->toBool()) {
                foreach($output->data as $nms) $oModuleController->deleteModule($nms->module_srl);
            }

            // Table Delete
            $table_list = array(
                'nms_group',
                'nms_mib',
                'nms_severity_log',
                'nms_snmp_log',
                'nms_snmptrap_log',
                'nms_syslog_log',
                'nms'
            );

            foreach($table_list as $table_name) {
                if($oDB->isTableExists($table_name)) {
                    $oDB->begin();
                    $result = $oDB->_query(sprintf("drop table %s%s", $oDB->prefix, $table_name));
                    if($oDB->isError()) $oDB->rollback();
                }
            }

            // commit
            $oDB->commit();

            // Files, Cache Delete
            @FileHandler::removeDir(_XE_PATH_."files/attach/nms");
            @FileHandler::removeDir(_XE_PATH_."files/cache/nms");
            return new Object();
        }

        /**
         * @brief 캐시 파일 재생성
         **/
        function recompileCache() {
            // NMS Wizard, SMI, Snmpwalkoid 캐쉬 삭제
            @FileHandler::removeFilesInDir('./files/cache/nms');
        }

        /**
         * @brief version check
         **/
        function checkVersion() {
            $body = '<?xml version="1.0" encoding="utf-8" ?>
                <methodCall>
                <params>
                <module><![CDATA[resource]]></module>
                <act><![CDATA[getResourceItems]]></act>
                <module_srl><![CDATA[18322904]]></module_srl>
                <package_srl><![CDATA[18335043]]></package_srl>
                <list_count><![CDATA[1]]></list_count>
                </params>
                </methodCall>';
            $buff = @FileHandler::getRemoteResource('http://www.xpressengine.com', $body, 3, 'POST', 'application/xml');

            if($buff) {
                 $oXmlParser = new XmlParser();
                 $xml = $oXmlParser->parse($buff);

                 if($this->nms_version != $xml->response->items->item->version->body) return true;
            }
        }
    }

    require_once(_XE_PATH_.'modules/nms/nms.item.php');
?>