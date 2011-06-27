<?php
    /**
     * @class  nmsAPI
     * @author largeden (developer@nmsxe.com)
     * @brief  nmsXE API class
     **/

    class nmsAPI extends nms {

        /**
         * @brief History List API
         **/
        function dispNmsHistoryList(&$oModule) {
            $oHistory = Context::get('oHistory');
            $oModule->add('oHistory',$this->arrangeHistoryList($oHistory->data));
            $oModule->add('page',$oHistory->page);
            $oModule->add('total_page',$oHistory->total_page);
        }

        /**
         * @brief History View API
         **/
        function dispNmsHistory(&$oModule) {
            $oModule->add('history',$this->arrangeHistory(Context::get('history')));
        }

        /**
         * @brief History List arrange API
         **/
        function arrangeHistoryList($content_list) {
            $output = array();
            if(count($content_list)) {
                foreach($content_list as $key => $val) $output[] = $this->arrangeHistory($val);
            }

            return $output;
        }

        /**
         * @brief History arrange API
         **/
        function arrangeHistory($content) {
            $output = null;
            if($content) {
                $output = $content->gets('document_srl','title','content','regdate','last_update');
                $output->thumbnail = $content->getThumbnail(150, 150, 'crop');
            }

            return $output;
        }
    }
?>