<?php

/*
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\services\statistics;

use Yii;
use fecshop\services\Service;

/**
 * Order services.
 *
 * @property \fecshop\services\order\Item $item
 *
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class  Order extends Service
{
    
    public $numPerPage = 2;
    
    protected $_bdminMonthModelName = '\fbbcbase\models\mysqldb\statistics\BdminMonth';

    protected $_bdminMonthModel;
    
    public function init()
    {
        parent::init();
        list($this->_bdminMonthModelName, $this->_bdminMonthModel) = \Yii::mapGet($this->_bdminMonthModelName);
    }
    /**
     * @param $year | string, 年，  譬如  2019
     * @param $month | string, 譬如  2
     * @return string
     * 得到开始和结束时间的时间戳
     */
    public function getMonthBeginAndEndTime($year, $month)
    {
        
        $beginTime = mktime(0,0,0,$month,1,$year);
        $endTime = strtotime("+1 month", $beginTime);
        return [$beginTime, $endTime];
    }
    
    /** 
     * @param $beginDateTime | string, Y:m:d H:i:s 格式，开始时间
     * @param $endDateTime | string, Y:m:d H:i:s 格式，结束时间
     */
    public function getMonthBdminCompleteOrder($bdmin_user_id, $year, $month, $pageNum=1)
    {   
        list($beginDateTime, $endDateTime) = $this->getMonthBeginAndEndTime($year, $month);
        //var_dump($beginDateTime, $endDateTime);
        // 得到订单
        $beginTime = $beginDateTime;
        $endTime = $endDateTime;
        //echo date('Y-m-d H:i:s', $beginTime);
        //echo date('Y-m-d H:i:s', $endTime);
        $filter = [
          'numPerPage' 	=> $this->numPerPage,
          'pageNum'		=> $pageNum,
          'select'            => ['base_grand_total'],
          'where'			=> [
                ['>=', 'received_at', $beginTime],
                ['<', 'received_at', $endTime],
                ['bdmin_user_id' => $bdmin_user_id],
                ['in', 'order_status', Yii::$service->order->info->orderStatusCanMonthStatisticsArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusCanMonthStatisticsArr],
                ['not in', 'payment_method', Yii::$service->payment->getCashOnDeliveryMethods()],
                
            ],
            'asArray' => true,
        ];
        
        return Yii::$service->order->coll($filter);
    }
    
    public function getMonthBdminCompleteOrderPageCount($bdmin_user_id, $year, $month) {
        
        $data = $this->getMonthBdminCompleteOrder($bdmin_user_id, $year, $month);
        
        return ceil($data['count'] / $this->numPerPage);
    }
    
    public function getMonthBdminCompleteOrderColl($bdmin_user_id, $year, $month, $pageNum) {
        $data = $this->getMonthBdminCompleteOrder($bdmin_user_id, $year, $month, $pageNum);
        
        return $data['coll'];
    }
    
    /**
     * @param $bdmin_user_id | int, 用户id。 
     * @param $year | string, 年，  譬如  2019
     * @param $month | string, 譬如  2
     * @param $pageNum | string , 页数。
     * @return boolean
     * 计算bdmin_user_id 在月份里面的金额
     */
    public function statisticsMonthBdminCompleteOrderTotal($bdmin_user_id, $year, $month, $pageNum)
    {
        $coll = $this->getMonthBdminCompleteOrderColl($bdmin_user_id, $year, $month, $pageNum);
        $orderTotal = 0;
        if (is_array($coll) && !empty( $coll)) {
            foreach ($coll as $one) {
                $orderTotal += $one['base_grand_total'];
            }
        }
        $statisticsModel = $this->_bdminMonthModel->findOne([
            'year' => $year,
            'month' => $month,
            'bdmin_user_id' => $bdmin_user_id,
        ]);
        if (!isset($statisticsModel['id']) ||  !$statisticsModel['id']) {
           Yii::$service->helper->errors->add('you must init (func statisticsMonthBdminInit)');
            
            return false;
        } 
        $statisticsModel->complete_order_total += $orderTotal;
        $statisticsModel->updated_at = time();
        if (!$statisticsModel->save()) {
            Yii::$service->helper->errors->add('statistics bdmin month model save fail');
            
            return false;
        }
        
        return true;
    }
    /**
     * @param $bdmin_user_id | int, 用户id。 
     * @param $year | string, 年，  譬如  2019
     * @param $month | string, 譬如  2
     * @return boolean
     * 统计脚本进行之前，先初始化数据。
     */
    public function statisticsMonthBdminInit($bdmin_user_id, $year, $month) 
    {
        
        $statisticsModel = $this->_bdminMonthModel->findOne([
            'year' => $year,
            'month' => $month,
            'bdmin_user_id' => $bdmin_user_id,
        ]);
        if (!isset($statisticsModel['id']) ||  !$statisticsModel['id']) {
            $statisticsModel = new $this->_bdminMonthModelName();
            $statisticsModel->year = $year;
            $statisticsModel->month = $month;
            $statisticsModel->bdmin_user_id = $bdmin_user_id;
            $statisticsModel->created_at = time();
            
        } 
        $statisticsModel->complete_order_total = 0;
        $statisticsModel->admin_refund_return_total = 0;
        $statisticsModel->bdmin_refund_return_total = 0;
        $statisticsModel->month_total = 0;
        $statisticsModel->updated_at = time();
        if (!$statisticsModel->save()) {
            Yii::$service->helper->errors->add('statistics bdmin month model init fail');
            
            return false;
        }
        
        return true;
    }
    
    
    public function getMonthBdminRefundPageCount($bdmin_user_id, $year, $month)
    {
        $data = $this->getMonthBdminRefund($bdmin_user_id, $year, $month);
        
        return ceil($data['count'] / $this->numPerPage);
    }
    
    public function getMonthBdminRefundColl($bdmin_user_id, $year, $month, $pageNum) {
        $data = $this->getMonthBdminRefund($bdmin_user_id, $year, $month, $pageNum);
        
        return $data['coll'];
    }
    
    public function statisticsMonthBdminRefundTotal($bdmin_user_id, $year, $month, $pageNum)
    {
        $coll = $this->getMonthBdminRefundColl($bdmin_user_id, $year, $month, $pageNum);
        $refundTotal = 0;
        if (is_array($coll) && !empty( $coll)) {
            foreach ($coll as $one) {
                $refundTotal += $one['base_price'];
            }
        }
        $statisticsModel = $this->_bdminMonthModel->findOne([
            'year' => $year,
            'month' => $month,
            'bdmin_user_id' => $bdmin_user_id,
        ]);
        if (!isset($statisticsModel['id']) ||  !$statisticsModel['id']) {
           Yii::$service->helper->errors->add('you must init (func statisticsMonthBdminInit)');
            
            return false;
        } 
        $statisticsModel->admin_refund_return_total  += $refundTotal;
        $statisticsModel->updated_at = time();
        if (!$statisticsModel->save()) {
            Yii::$service->helper->errors->add('statistics bdmin month model save fail');
            
            return false;
        }
        
        return true;
        
    }
    
    
    
    /** 
     * @param $beginDateTime | string, Y:m:d H:i:s 格式，开始时间
     * @param $endDateTime | string, Y:m:d H:i:s 格式，结束时间
     */
    public function getMonthBdminRefund($bdmin_user_id, $year, $month, $pageNum=1)
    {   
        list($beginDateTime, $endDateTime) = $this->getMonthBeginAndEndTime($year, $month);
        //var_dump($beginDateTime, $endDateTime);
        // 得到订单
        $beginTime = $beginDateTime;
        $endTime = $endDateTime;
        //echo date('Y-m-d H:i:s', $beginTime);
        //echo date('Y-m-d H:i:s', $endTime);
        $filter = [
          'numPerPage' 	=> $this->numPerPage,
          'pageNum'		=> $pageNum,
          'select'            => ['base_price'],
          'where'			=> [
                ['>=', 'refunded_at', $beginTime],
                ['<', 'refunded_at', $endTime],
                ['bdmin_user_id' => $bdmin_user_id],
                ['in', 'status', Yii::$service->refund->refundPaymentConfirmedArr],
            ],
            'asArray' => true,
        ];
        // 初始化，平台退款
        Yii::$service->refund->initModelForce('admin');
        
        return Yii::$service->refund->coll($filter);
    }
    
    public function statisticsBdminMonthTotal($bdmin_user_id, $year, $month)
    {
        $statisticsModel = $this->_bdminMonthModel->findOne([
            'year' => $year,
            'month' => $month,
            'bdmin_user_id' => $bdmin_user_id,
        ]);
        if (!isset($statisticsModel['id']) ||  !$statisticsModel['id']) {
           Yii::$service->helper->errors->add('you must init (func statisticsMonthBdminInit)');
            
            return false;
        } 
        $statisticsModel->month_total   = $statisticsModel->complete_order_total  - $statisticsModel->admin_refund_return_total;
        $statisticsModel->updated_at = time();
        if (!$statisticsModel->save()) {
            Yii::$service->helper->errors->add('statistics bdmin month model save fail');
            
            return false;
        }
        
        return true;
    }
    
    
    public function getBdMonthBdminRefundPageCount($bdmin_user_id, $year, $month)
    {
        $data = $this->getBdMonthBdminRefund($bdmin_user_id, $year, $month);
        
        return ceil($data['count'] / $this->numPerPage);
        
    }
    
    public function bdStatisticsMonthBdminRefundTotal($bdmin_user_id, $year, $month, $pageNum)
    {
        $coll = $this->getBdMonthBdminRefundColl($bdmin_user_id, $year, $month, $pageNum);
        $refundTotal = 0;
        if (is_array($coll) && !empty( $coll)) {
            foreach ($coll as $one) {
                $refundTotal += $one['base_price'];
            }
        }
        $statisticsModel = $this->_bdminMonthModel->findOne([
            'year' => $year,
            'month' => $month,
            'bdmin_user_id' => $bdmin_user_id,
        ]);
        if (!isset($statisticsModel['id']) ||  !$statisticsModel['id']) {
           Yii::$service->helper->errors->add('you must init (func statisticsMonthBdminInit)');
            
            return false;
        } 
        $statisticsModel->bdmin_refund_return_total  += $refundTotal;
        $statisticsModel->updated_at = time();
        if (!$statisticsModel->save()) {
            Yii::$service->helper->errors->add('statistics bdmin month model save fail');
            
            return false;
        }
        
        return true;
        
    }
    
    
    public function getBdMonthBdminRefundColl($bdmin_user_id, $year, $month, $pageNum) {
        $data = $this->getBdMonthBdminRefund($bdmin_user_id, $year, $month, $pageNum);
        
        return $data['coll'];
    }
    
    /** 
     * @param $beginDateTime | string, Y:m:d H:i:s 格式，开始时间
     * @param $endDateTime | string, Y:m:d H:i:s 格式，结束时间
     */
    public function getBdMonthBdminRefund($bdmin_user_id, $year, $month, $pageNum=1)
    {   
        list($beginDateTime, $endDateTime) = $this->getMonthBeginAndEndTime($year, $month);
        //var_dump($beginDateTime, $endDateTime);
        // 得到订单
        $beginTime = $beginDateTime;
        $endTime = $endDateTime;
        //echo date('Y-m-d H:i:s', $beginTime);
        //echo date('Y-m-d H:i:s', $endTime);
        $filter = [
          'numPerPage' 	=> $this->numPerPage,
          'pageNum'		=> $pageNum,
          'select'            => ['base_price'],
          'where'			=> [
                ['>=', 'refunded_at', $beginTime],
                ['<', 'refunded_at', $endTime],
                ['bdmin_user_id' => $bdmin_user_id],
                ['in', 'status', Yii::$service->refund->refundPaymentConfirmedArr],
            ],
            'asArray' => true,
        ];
        // 初始化，供应商退款
        Yii::$service->refund->initModelForce('bdmin');
        
        return Yii::$service->refund->coll($filter);
    }
    
}
