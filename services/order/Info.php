<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\services\order;

//use fecshop\models\mysqldb\order\Item as MyOrderItem;
use fecshop\services\Service;
use Yii;

/**
 * Cart items services.
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Info extends \fecshop\services\Service
{
    
    // 订单流程状态：等待支付状态
    public $orderStatusPaymentPendingArr;
    // 订单操作状态：等待支付状态
    public $orderOperateStatusPaymentPendingArr;
    
    // 订单流程状态：等待支付的订单状态
    public $orderStatusWaintingPaymentOrderArr;
    // 订单操作状态：等待支付的订单状态
    public $orderOperateStatusWaintingPaymentOrderArr;
    
    
    // 订单流程状态：可以进行【订单取消操作】的状态
    public $orderStatusCanCancelArr;
    // 订单操作状态：可以进行【订单取消操作】的状态
    public $orderOperateStatusCanCancelArr;
    
    // 订单流程状态：可以进行【撤销订单取消操作】的状态
    public $orderStatusCanCancelBackArr;
    // 订单操作状态：可以进行【撤销订单取消操作】的状态
    public $orderOperateStatusCanCancelBackArr;
    
    // 订单流程状态：状态满足哪些条件？用户可以通过审核的方式进行提交【订单取消请求】
    public $orderStatusRequestCancelArr;
    // 订单操作状态：状态满足哪些条件？用户可以通过审核的方式进行提交【订单取消请求】
    public $orderOperateStatusRequestCancelArr;
    
    // 订单流程状态：bdmin查看用户提交的取消订单请求，满足订单状态条件的才能进行【订单取消操作】
    public $orderStatusAuditCancelArr;
    // 订单操作状态：bdmin查看用户提交的取消订单请求，满足订单状态条件的才能进行【订单取消操作】
    public $orderOperateStatusAuditCancelArr;
    
    // 订单流程状态：允许用户直接进行【订单取消操作】的状态数组
    public $orderStatusRedirectCancelArr;
    // 订单操作状态：允许用户直接进行【订单取消操作】的状态数组
    public $orderOperateStatusRedirectCancelArr;
    
    // 订单流程状态：允许用户直接进行取消操作，需要进行【退款】的状态数组
    // 此处仅仅是订单状态的数组，在订单判断逻辑中，还会加入支付状态的判断（非货到付款的支付方式）
    public $orderStatusRedirectCancelNeedRefundArr;
    // 订单操作状态：允许用户直接进行取消操作，需要进行【退款】的状态数组
    // 此处仅仅是订单状态的数组，在订单判断逻辑中，还会加入支付状态的判断（非货到付款的支付方式）
    public $orderOperateStatusRedirectCancelNeedRefundArr;
    
    
    // 订单流程状态：允许用户进行【订单支付】的状态数组
    public $orderStatusCanPaymentArr;
    // 订单操作状态：允许用户进行【订单支付】的状态数组
    public $orderOperateStatusCanPaymentArr;
    
    // 订单流程状态：允许bdmin进行【订单审核通过】的状态数组
    public $orderStatusAuditOrderAcceptArr;
    // 订单操作状态：允许bdmin进行【订单审核通过】的状态数组
    public $orderOperateStatusAuditOrderAcceptArr;
    
    // 订单流程状态：允许bdmin进行订单【审核不通过】（拒绝）的状态数组
    public $orderStatusAuditOrderRefuseArr;
    // 订单操作状态：允许bdmin进行订单【审核不通过】（拒绝）的状态数组
    public $orderOperateStatusAuditOrderRefuseArr;
    
    // 订单流程状态：【订单审核通过后】的状态数组
    public $orderStatusProcessingOrderAcceptArr;
    // 订单操作状态：【订单审核通过后】的状态数组
    public $orderOperateStatusProcessingOrderAcceptArr;
    
    // 订单流程状态：允许【订单发货】的状态数组
    public $orderStatusCanDispatchArr;
    // 订单操作状态：允许【订单发货】的状态数组
    public $orderOperateStatusCanDispatchArr;
    
    // 订单流程状态：允许【延迟订单收货时间】的状态数组
    public $orderStatusCanDelayReceiveArr;
    // 订单操作状态：允许【延迟订单收货时间】的状态数组
    public $orderOperateStatusCanDelayReceiveArr;
    
    // 订单流程状态：【确认收货订单后】的状态数组
    public $orderStatusReceivedOrderArr;
    // 订单操作状态：【确认收货订单后】的状态数组
    public $orderOperateStatusReceivedOrderArr;
    
    // 订单流程状态：【允许订单收货】的状态数组
    public $orderStatusCanReceivedArr;   
     // 订单操作状态：【允许订单收货】的状态数组
    public $orderOperateStatusCanReceivedArr;
    
    // 订单流程状态：【允许订单售后】的状态数组
    public $orderStatusCanAfterSaleArr;
    // 订单操作状态：【允许订单售后】的状态数组
    public $orderOperateStatusCanAfterSaleArr;
    
    // 订单流程状态：【允许进行月结】的订单状态数组
    public $orderStatusCanMonthStatisticsArr;
    // 订单操作状态：【允许进行月结】的订单状态数组
    public $orderOperateStatusCanMonthStatisticsArr;
    
    
    // 订单售后退货状态：用户发起退货申请后，允许供应商进行【供应商审核通过操作】的状态
    public $afterSaleReturnStatusCanAuditAcceptArr;
    // 订单售后退货状态：用户发起退货申请后，允许供应商进行【供应商审核拒绝操作】的状态
    public $afterSaleReturnStatusCanAuditRefuseArr;
    // 订单售后退货状态：用户发起退货申请后，，进行【取消退货操作】的状态
    public $afterSaleReturnStatusCanCancelArr;
    // 订单售后退货状态：用户发起退货申请后，，进行【发货操作】的状态
    public $afterSaleReturnStatusCanDispatchArr;
    // 订单售后退货状态：允许供应商将退货的商品，进行【收货操作】的状态
    public $afterSaleReturnStatusCanReceiveArr;
    // 订单售后退货状态：允许供应商将退货的商品，进行【退款操作】的状态
    public $afterSaleReturnStatusCanRefundArr;
    
   
    
    public function init()
    {
        parent::init();
        
        $this->orderStatusPaymentPendingArr = [
            Yii::$service->order->payment_status_pending,
        ];
        $this->orderOperateStatusPaymentPendingArr = [
            Yii::$service->order->operate_status_normal,
        ];
    
        $this->orderStatusCanCancelArr = [
            Yii::$service->order->payment_status_pending,
            Yii::$service->order->payment_status_processing,
            Yii::$service->order->payment_status_canceled,
            Yii::$service->order->payment_no_need_status_confirmed,
            Yii::$service->order->payment_status_confirmed,
            Yii::$service->order->status_processing,
            Yii::$service->order->status_audit_fail,
        ];
        
        $this->orderStatusWaintingPaymentOrderArr = [
            Yii::$service->order->payment_status_pending,
            Yii::$service->order->payment_status_processing,
        ];
        
        $this->orderOperateStatusWaintingPaymentOrderArr = [
             Yii::$service->order->operate_status_normal,
        ];
        
        $this->orderOperateStatusCanCancelArr = [
            Yii::$service->order->operate_status_normal,
        ];
        
        $this->orderStatusCanCancelBackArr = [
             Yii::$service->order->status_processing,
        ];
        
        $this->orderOperateStatusCanCancelBackArr = [
            Yii::$service->order->operate_status_waiting_canceled,
        ];
        
        $this->orderStatusRequestCancelArr = [
            // Yii::$service->order->payment_status_confirmed,
            // Yii::$service->order->status_audit_fail,
            Yii::$service->order->status_processing,
            
        ];
        $this->orderOperateStatusRequestCancelArr = [
            Yii::$service->order->operate_status_normal,
        ];
        
        
        $this->orderStatusAuditCancelArr = [
            Yii::$service->order->payment_status_confirmed,
            Yii::$service->order->status_audit_fail,
            Yii::$service->order->status_processing,
        ];
        $this->orderOperateStatusAuditCancelArr = [
            Yii::$service->order->operate_status_waiting_canceled,
        ];
        
        $this->orderStatusRedirectCancelArr = [
            Yii::$service->order->payment_status_pending,
            Yii::$service->order->payment_status_processing,
            Yii::$service->order->payment_status_canceled,
            Yii::$service->order->payment_no_need_status_confirmed,
            Yii::$service->order->payment_status_confirmed,
            Yii::$service->order->status_audit_fail,
        ];
        $this->orderOperateStatusRedirectCancelArr = [
            Yii::$service->order->operate_status_normal,
        ];
        
        $this->orderStatusRedirectCancelNeedRefundArr = [
            Yii::$service->order->payment_status_confirmed,
            Yii::$service->order->status_audit_fail,
            Yii::$service->order->status_processing,
        ];
        $this->orderOperateStatusRedirectCancelNeedRefundArr = [
            Yii::$service->order->operate_status_normal,
            Yii::$service->order->operate_status_waiting_canceled,
        ];
        
        
        
        $this->orderStatusCanPaymentArr = [
            Yii::$service->order->payment_status_pending
        ];
        $this->orderOperateStatusCanPaymentArr = [
            Yii::$service->order->operate_status_normal,
        ];
        
        $this->orderStatusAuditOrderAcceptArr = [
            Yii::$service->order->payment_status_confirmed,
            Yii::$service->order->payment_no_need_status_confirmed,
            Yii::$service->order->status_audit_fail,
        ];
        $this->orderOperateStatusAuditOrderAcceptArr = [
            Yii::$service->order->operate_status_normal,
        ];
        
        $this->orderStatusAuditOrderRefuseArr = [
            Yii::$service->order->payment_status_confirmed,
            Yii::$service->order->payment_no_need_status_confirmed,
        ];
        $this->orderOperateStatusAuditOrderRefuseArr = [
            Yii::$service->order->operate_status_normal,
        ];
        
        $this->orderStatusProcessingOrderAcceptArr = [
            Yii::$service->order->status_processing,
        ];
        
        
        $this->orderOperateStatusProcessingOrderAcceptArr = [
            Yii::$service->order->operate_status_normal,
        ];
        
        $this->orderStatusCanDispatchArr = [
            Yii::$service->order->status_processing,
        ];
        $this->orderOperateStatusCanDispatchArr = [
            Yii::$service->order->operate_status_normal,
        ];
        
        $this->orderStatusCanDelayReceiveArr = [
            Yii::$service->order->status_dispatched,
        ];
        $this->orderOperateStatusCanDelayReceiveArr = [
            Yii::$service->order->operate_status_normal,
        ];
        

        $this->orderStatusReceivedOrderArr = [
            Yii::$service->order->status_received,
        ];
        $this->orderOperateStatusReceivedOrderArr = [
            Yii::$service->order->operate_status_normal,
        ];
        
        $this->orderStatusCanReceivedArr = [
            Yii::$service->order->status_dispatched,
        ];
        $this->orderOperateStatusCanReceivedArr = [
            Yii::$service->order->operate_status_normal,
        ];
        
        $this->orderStatusCanAfterSaleArr = [
            Yii::$service->order->status_received,
        ];
        $this->orderOperateStatusCanAfterSaleArr = [
            Yii::$service->order->operate_status_normal,
            //Yii::$service->order->operate_status_return,
            //Yii::$service->order->operate_status_waiting_exchange,
        ];
        
        $this->afterSaleReturnStatusCanAuditAcceptArr = [
            Yii::$service->order->afterSale->after_sale_status_return_request,
            Yii::$service->order->afterSale->after_sale_status_return_refuse,
        ];
        $this->afterSaleReturnStatusCanAuditRefuseArr = [
            Yii::$service->order->afterSale->after_sale_status_return_request,
        ];
        
        $this->afterSaleReturnStatusCanCancelArr = [
            Yii::$service->order->afterSale->after_sale_status_return_request,
            Yii::$service->order->afterSale->after_sale_status_return_refuse,
            Yii::$service->order->afterSale->after_sale_status_return_accept,
        ];
        
        
        $this->afterSaleReturnStatusCanDispatchArr = [
            Yii::$service->order->afterSale->after_sale_status_return_accept,
        ];
        
        $this->afterSaleReturnStatusCanReceiveArr = [
            Yii::$service->order->afterSale->after_sale_status_return_dispatch,
        ];
        
        $this->afterSaleReturnStatusCanRefundArr = [
            Yii::$service->order->afterSale->after_sale_status_return_received,
        ];
        
        
        $this->orderStatusCanMonthStatisticsArr = [
            Yii::$service->order->status_received,
        ];
        $this->orderOperateStatusCanMonthStatisticsArr = [
            Yii::$service->order->operate_status_normal,
        ];
        
        
    }
    
    
    
    /**
     * @param $order | Object,  order model
     * @return string， 返回订单的展示状态
     */
    public function getLabelStatus($order)
    {
        $order_status = $order['order_status'];
        $order_operate_status = $order['order_operate_status'];
        if ($order_operate_status == Yii::$service->order->operate_status_normal) {
            
            return $order_status;
        } else {
            
            return $order_operate_status;
        }
    }
    
    /**
     * @param $afterSale | Object,  order after sale model
     * @return boolean
     * 订单售后退货申请，是否可以进行允许操作
     */
    public function isBdminCanRefundAfterSaleReturndOrder($afterSale)
    {
        $afterSaleStatus = $afterSale['status'];
        if (!in_array($afterSaleStatus,  $this->afterSaleReturnStatusCanRefundArr)) {
            
            return false;
        } 
        
        return true;
    }
    
    
    public function isConsoleCanCancelPaymentPendingOrder($orderModel)
    {
        if (!in_array($orderModel['order_status'],  $this->orderStatusWaintingPaymentOrderArr)) {
            
            return false;
        } 
        if (!in_array($orderModel['order_operate_status'],  $this->orderOperateStatusWaintingPaymentOrderArr)) {
            
            return false;
        } 
        // 超出时间的订单，才可以被脚本自动取消。
        $m = Yii::$service->order->minuteBeforeThatReturnPendingStock * 60;
        $order_created_at = $orderModel->created_at;
        if ($order_created_at + $m > time()) {
            
            return false;
        }
        
        return true;
        
    }
    
    /**
     * @param $afterSale | Object,  order after sale model
     * @return boolean
     * 订单售后退货，是否可以进行允许收货操作
     */
    public function isBdminCanReceiveAfterSaleReturndOrder($afterSale)
    {
        $afterSaleStatus = $afterSale['status'];
        if (!in_array($afterSaleStatus,  $this->afterSaleReturnStatusCanReceiveArr)) {
            
            return false;
        } 
        
        return true;
    }
    
    /**
     * @param $afterSale | Object,  order after sale model
     * @return boolean
     * 订单售后退货，是否可以进行允许发货操作
     */
    public function isCustomerCanDispatchAfterSaleReturndOrder($afterSale)
    {
        $afterSaleStatus = $afterSale['status'];
        if (!in_array($afterSaleStatus,  $this->afterSaleReturnStatusCanDispatchArr)) {
            
            return false;
        } 
        
        return true;
    }
    
    /**
     * @param $afterSale | Object,  order after sale model
     * @return boolean
     * 订单售后退货，是否可以进行允许取消退货操作
     */
    public function isCustomerCanCancelAfterSaleReturndOrder($afterSale)
    {
        $afterSaleStatus = $afterSale['status'];
        if (!in_array($afterSaleStatus,  $this->afterSaleReturnStatusCanCancelArr)) {
            
            return false;
        } 
        
        return true;
    }
    
    /**
     * @param $afterSale | Object,  order after sale model
     * @return boolean
     * 订单售后退货，是否可以进行允许审核通过操作
     */
    public function isBdminCanAcceptAfterSaleReturndOrder($afterSale)
    {
        $afterSaleStatus = $afterSale['status'];
        if (!in_array($afterSaleStatus,  $this->afterSaleReturnStatusCanAuditAcceptArr)) {
            
            return false;
        } 
        
        return true;
    }
    /**
     * @param $afterSale | Object,  order after sale model
     * @return boolean
     * 订单售后退货，是否可以进行拒绝审核通过操作
     */
    public function isBdminCanRefuseAfterSaleReturndOrder($afterSale)
    {
        $afterSaleStatus = $afterSale['status'];
        if (!in_array($afterSaleStatus,  $this->afterSaleReturnStatusCanAuditRefuseArr)) {
            
            return false;
        } 
        
        return true;
    }
    
    
    
    /**
     * @param $order | Object, Order Model
     * @return boolean
     * 供应商是否可以进行订单发货操作
     */
    public function isBdminCanDispatchOrder($order)
    {
        if (!in_array($order['order_status'],  $this->orderStatusCanDispatchArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusCanDispatchArr)) {
            
            return false;
        } 
        
        return true;
    }
    
    /**
     * @param $order | Object, Order Model
     * @return boolean
     * 供应商是否可以进行延迟订单收货时间操作
     */
    public function isCustomerCanDelayReceiveOrder($order)
    {
        if (!in_array($order['order_status'],  $this->orderStatusCanDelayReceiveArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusCanDelayReceiveArr)) {
            
            return false;
        } 
        $dispatched_at = $order['dispatched_at'];
        $recevie_delay_days = $order['recevie_delay_days'];
        $delayReceiveOrderDaysPerTime = Yii::$service->order->process->delayReceiveOrderDaysPerTime;
        $delayReceiveOrderMaxDays = Yii::$service->order->process->delayReceiveOrderMaxDays;
        $orderDefaultMaxRecevieDay = Yii::$service->order->process->orderDefaultMaxRecevieDay;
         $delayReceiveOrderTriggerDays = Yii::$service->order->process->delayReceiveOrderTriggerDays;
        // 最大天数是否超限，超过最大延迟收货时间，则不能继续操作延迟收货时间
        if ($recevie_delay_days + $delayReceiveOrderDaysPerTime > $delayReceiveOrderMaxDays) {
            return false;
        }
        // 订单发货后，日期时间可以进行该操作
        $limitTime = $dispatched_at + 86400 * ($orderDefaultMaxRecevieDay + $recevie_delay_days) - time();
        // 如果过期，或者没有到期限，也不能执行该操作
        if (($limitTime < 0) || ($limitTime  > 86400 * $delayReceiveOrderTriggerDays)) {
            return false;
        }
        
        return true;
    }
    /**
     * @param $orderModel | Object, Order Model
     * @return boolean
     * 后台脚本是否可以？自动将订单进行收货操作
     */
    public function isConsoleCanAutoReceiveOrder($orderModel)
    {
        if (!in_array($order['order_status'],  $this->orderStatusCanReceivedArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusCanReceivedArr)) {
            
            return false;
        } 
        $dispatched_at = $order['dispatched_at'];
        $recevie_delay_days = $order['recevie_delay_days'];
        $orderDefaultMaxRecevieDay = Yii::$service->order->process->orderDefaultMaxRecevieDay;
        
        $limitReceiveTime = $dispatched_at + ($recevie_delay_days + $orderDefaultMaxRecevieDay) * 86400 ;
        if ($limitReceiveTime > time()) {
            
            return false;
        }
        
        return true;
    }
    
    
    /**
     * @param $order | Object, Order Model
     * @return boolean
     * 是否是已确认收货订单
     */
    public function isReceivedOrder($order)
    {
        if (!in_array($order['order_status'],  $this->orderStatusReceivedOrderArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusReceivedOrderArr)) {
            
            return false;
        } 
        
        return true;
    }
    /**
     * @param $order | Object, Order Model
     * @return boolean
     * 是否是等待支付订单
     */
    public function isPaymentPending($order)
    {
        if (!in_array($order['order_status'],  $this->orderStatusPaymentPendingArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusPaymentPendingArr)) {
            
            return false;
        } 
        
        return true;
    }
    
    
    /**
     * @param $order | object or Array, order data
     * @return boolean
     * 【支付部分：订单是否可以进入售后流程】，用于在用户账户中心，显示售后
     */
    public function isCustomerCanAfterSale($order) 
    {
        if (!in_array($order['order_status'],  $this->orderStatusCanAfterSaleArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusCanAfterSaleArr)) {
            
            return false;
        } 
        
        return true;
    }
    
    
    /**
     * @param $order | object or Array, order data
     * @return boolean, 返回订单是否可以前端用户被支付
     * 【支付部分：订单是否可以被支付】，用于在用户账户中心，未支付的订单显示订单支付按键
     */
    public function isCustomerCanReceive($order) 
    {
        if (!in_array($order['order_status'],  $this->orderStatusCanReceivedArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusCanReceivedArr)) {
            
            return false;
        } 
        
        return true;
    }
    
    /**
     * @param $order | object or Array, order data
     * @return boolean, 返回订单是否可以前端用户被支付
     * 【支付部分：订单是否可以被支付】，用于在用户账户中心，未支付的订单显示订单支付按键
     */
    public function isCustomerCanPayment($order) 
    {
        if (!in_array($order['order_status'],  $this->orderStatusCanPaymentArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusCanPaymentArr)) {
            
            return false;
        } 
        
        return true;
    }
    /** 
     * @param $order | object or Array, order data
     * @return boolean, 
     * 状态满足哪些条件？用户可以发起订单取消请求，用于在用户账户中心显示订单取消按键
     */
    public function isCustomerCanCancel($order) 
    {
        
        if (!in_array($order['order_status'],  $this->orderStatusCanCancelArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusCanCancelArr)) {
            
            return false;
        } 
        
        return true;
    }
    /** 
     * @param $order | object or Array, order data
     * @return boolean, 
     * 状态满足哪些条件？用户可以发起 【撤销订单取消请求】，用于在用户账户中心显示【撤销订单取消请求】按键
     */
    public function isCustomerCanCancelBack($order) 
    {
        
        if (!in_array($order['order_status'],  $this->orderStatusCanCancelBackArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusCanCancelBackArr)) {
            
            return false;
        } 
        
        return true;
    }
    
    /** 
     * @param $order | object or Array, order data
     * @return boolean, 
     * 状态满足哪些条件？用户可以通过审核的方式进行提交订单取消请求
     * 【订单取消请求：发起条件判断】
     */
    public function isCustomerCanRequestCancel($order) 
    {
        
        if (!in_array($order['order_status'],  $this->orderStatusRequestCancelArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusRequestCancelArr)) {
            
            return false;
        } 
        
        return true;
    }
    /**
     * @return boolean
     * 订单状态：前端用户发起取消订单请求后，订单可以直接被取消，不需要后台审核的订单状态
     * 也就是订单状态是否满足，直接被取消，而不需要后台审核？
     * 如果已经付款，那么需要进行退款处理
     */
    public function isCustomerCanRedirectCancel($order)
    {
        if (!in_array($order['order_status'],  $this->orderStatusRedirectCancelArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusRedirectCancelArr)) {
            
            return false;
        } 
        
        return true;
    }

    /**
     * 用户直接取消订单，是否需要进行退款处理
     *
     */
    public function isCustomerRedirectCancelNeedRefund($order){
        
        if (!in_array($order['order_status'],  $this->orderStatusRedirectCancelNeedRefundArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusRedirectCancelNeedRefundArr)) {
            
            return false;
        } 
        
        return true;
    }
    
    /**
     * @return boolean
     * 订单状态：前端用户发起取消订单请求后，订单可以直接被取消，不需要后台审核的订单状态
     * 也就是订单状态是否满足，直接被取消，而不需要后台审核？
     */
    public function isBdminCanAuditCancel($order)
    {
        if (!in_array($order['order_status'],  $this->orderStatusAuditCancelArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusAuditCancelArr)) {
            
            return false;
        } 
        return true;
    }
    /**
     * @return boolean
     * 
     */
    public function isBdminCanAuditOrderAccept($order)
    {
        if (!in_array($order['order_status'],  $this->orderStatusAuditOrderAcceptArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusAuditOrderAcceptArr)) {
            
            return false;
        } 
        
        return true;
    }
    /**
     * @return boolean
     */
    public function isBdminCanAuditOrderRefuse($order)
    {
        if (!in_array($order['order_status'],  $this->orderStatusAuditOrderRefuseArr)) {
            
            return false;
        } 
        if (!in_array($order['order_operate_status'],  $this->orderOperateStatusAuditOrderRefuseArr)) {
            
            return false;
        } 
        
        return true;
    }
      
}
