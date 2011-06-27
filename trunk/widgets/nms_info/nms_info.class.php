<?php
    /**
     * @class nms_info
     * @author largeden (webmaster@animeclub.net)
     * @brief nmsXE 모듈로 수집된 데이터를 출력하는 위젯
     * @version 0.2
     **/

    require_once(_XE_PATH_.'widgets/nms_info/pChart/pChart/pData.class');   
    require_once(_XE_PATH_.'widgets/nms_info/pChart/pChart/pChart.class');   

    class nms_info extends WidgetHandler {

        /**
         * @brief 위젯의 실행 부분
         *
         * ./widgets/위젯/conf/info.xml 에 선언한 extra_vars를 args로 받는다
         * 결과를 만든후 print가 아니라 return 해주어야 한다
         **/
        function proc($args) {

            if(!$args->id) return;
            $oNmsModel = &getModel("nms");

            // 정렬 순서
            if(!in_array($args->order_type, array('asc','desc'))) $args->order_type = 'asc';

            if(in_array($args->data_type, array('syslog','snmptrap'))) {

                if($args->data_type == "syslog") $query = "getNmsSyslogList";
                else $query = "getNmsSnmpTrapList";

                // 정렬 대상
                $order_target = $args->order_target;
                if(!$order_target) $order_target = "regdate";

                // 출력된 목록 수
                $list_count = (int)$args->list_count;
                if(!$list_count) $list_count = 5;

                // 페이지 구분자
                $page_id = 'page_'.$args->id;

                // 페이지 목록 수
                $page_count = $args->page_count;
                if(!$page_count) $page_count = 0;

                $obj->data_type = $args->data_type;
                $obj->sort_index = $order_target;
                $obj->order_type = $order_type=="desc"?"asc":"desc";
                $obj->list_count = $list_count;
                $obj->page_count = $page_count;

                // 등록된 nms 모듈을 불러와 세팅
                $obj->page = Context::get('page_'.$args->id);

                $output = executeQueryArray('widgets.nms_info.'.$query, $obj);

                // 템플릿에 쓰기 위해서 context::set
                Context::set('total_count', $output->total_count);
                Context::set('total_page', $output->total_page);
                Context::set('widget_info', $output->data);
                Context::set('page_navigation', $output->page_navigation);
                $pages[$args->id] = $output->page; Context::set('pages', $pages);

            }elseif($args->data_type == "snmp" || !$args->data_type) {

                if(!$args->module_srls) return;
                
                $module_srl = explode(',',$args->module_srls);

                // 하나의 모듈만 가능
                $args->module_srl = $module_srl[0];

                if(!$args->statistics) $args->statistics = "5minutely";
                if(!$args->width) $args->width = 600;
                if(!$args->height) $args->height = 300;
                if(!$args->datepattern) $args->datepattern = 1;


                $widget_info = $oNmsModel->getNmsModuleInfo($args);
                if(!$widget_info->oMib) return;

                foreach($widget_info->oMib as $oMib) {
                    $mmid = $oMib->get('mmid');
                    $output = $oMib->getNmsSnmpSummary($args->statistics,$mmid,$args->date);

                    foreach($output as $key => $val) {
                        if($val->value > $oSize->value_max) $oSize->value_max = $val->value;
                    }
                    if($oSize->value_max > $oSize->total_max) $oSize->total_max = $oSize->value_max;
                }
                $widget_info->size = $oMib->getSizeFormat($oSize->total_max);            
                if($oMib->get('max')<101) $widget_info->size = "%";

                $color = explode(",",$args->color); 
                $i = 1; 
                $z = 0;
                foreach($widget_info->oMib as $oMib) {
                    $mmid = $oMib->get('mmid');
                    /*
                    해당 모듈에 등록된 MIB 정보를 선언 
                    (하루 기준)
                    5minutely : 5분 평균 값
                    hourly : 1시간 평균 값
                    (한달 기준)
                    daily : 1일 평균 값
                    weekly : 1주 평균 값
                    (년 기준)
                    monthly : 월 평균 값
                    yearly : 년 평균 값(특정 년도를 선언하면 그 해 부터 현재 년도까지를 집계)
                    */
                    $output = $oMib->getNmsSnmpSummary($args->statistics,$mmid,$args->date);

                    if($i>1) $chart->num[] = "_".$i;
                    $chart->color[] = $color[$i-1];
                    $chart->title[] = $oMib->get('mib_title');
                    $ptraffic = "";
                    $chart->count = count($output);
                    foreach($output as $key => $val) {
                        $traffic[$i] .= $oMib->getSizeBytes($val->value,$widget_info->size).",";
                        $ptraffic[] = $oMib->getSizeBytes($val->value,$widget_info->size);
                        if($i==1) {
                            if(!($z%$args->datepattern)) {
                                $snmpDate .= zdate($val->regdate,$args->datetype).",";
                                $psnmpDate[] = zdate($val->regdate,$args->datetype);
                            }else{
                                if(($chart->count-1) == $z) {
                                    $snmpDate .= zdate($val->regdate,$args->datetype).",";
                                    $psnmpDate[] = zdate($val->regdate,$args->datetype);
                                } else {
                                    $snmpDate .= ",";
                                    $psnmpDate[] = "";
                                }
                            }

                        }
                        if($oMib->getSizeBytes($val->value,$widget_info->size) > $value_max) {
                         $value_max = $oMib->getSizeBytes($val->value,$widget_info->size);
                        }
                        $z++;
                    }
                    if($i==1) {
                        $chart->snmpdate = trim($snmpDate,',');
                        $chart->psnmpdate = $psnmpDate;
                    }
                    $chart->traffic[] = trim($traffic[$i],',');
                    $chart->ptraffic[] = $ptraffic;
                    $i++;
                }
                $chart->value_max = $value_max;

                Context::set('chart',$chart);
                Context::set('widget_info',$widget_info);
            }


            Context::set('args',$args);

            // 템플릿의 스킨 경로를 지정 (skin, colorset에 따른 값을 설정)
            $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
            Context::set('colorset', $args->colorset);

            // 템플릿 파일을 지정
            $tpl_file = 'list';

            // 템플릿 컴파일
            $oTemplate = &TemplateHandler::getInstance();
            $output = $oTemplate->compile($tpl_path, $tpl_file);
            return $output;
        }

    }

?>
