<?php

/*
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\services\order;

//use fecshop\models\mysqldb\order\Item as MyOrderItem;
use fecshop\services\Service;
use yii\db\Expression;
use Yii;

/**
 * Cart items services.
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Process extends \fecshop\services\Service
{
    // 延迟订单收货时间: 最大天数
    public $delayReceiveOrderMaxDays = 45;
    // 延迟订单收货时间: 单次操作的天数，也就是用户在前端点击延迟收货后, 一次性延迟的天数
    public $delayReceiveOrderDaysPerTime = 5;
    // 延迟订单收货时间: 订单收货到期日期 - 当前日期  <= x 天，  可以触发延迟收货操作，譬如
    // 譬如这里设置为3，当日期为3天后就要被系统自动化自动设置为收货的时候（3天后就要被强制收货），
    // 这个时候用户可以在前端账户中心订单列表，进行延迟收货操作。
    public $delayReceiveOrderTriggerDays = 3;
    // 订单收货最大间隔默认时间：订单发货后，超过x天，而且用户没有进行延迟订单收货操作，系统将自动将订单设置为订单已收货。
    public $orderDefaultMaxRecevieDay = 10;
    
    public function consoleScriptCancelPaymentPendingOrder($increment_id) {
        $orderModel = Yii::$service->order->getByIncrementId($increment_id);
        if (Yii::$service->order->info->isConsoleCanCancelPaymentPendingOrder($orderModel)) {
            if ($this->cancelPaymentPendingOrder($orderModel)) {
                
                return true;
            }
        } 
    }
    
    /**
     * @param $orderModel | Object,  order model
     * @return bool
     * 系统脚本，自动取消，未支付的订单
     */
    public function cancelPaymentPendingOrder($orderModel)
    {
        if (!isset($orderModel['increment_id']) || !$orderModel['increment_id']) {
            Yii::$service->helper->errors->add('order model is empty');
            
            return false;
        }
        $minute     = Yii::$service->order->minuteBeforeThatReturnPendingStock;
        $begin_time = strtotime(date('Y-m-d H:i:s'). ' -'.$minute.' minutes ');
        
        // 更改订单状态--> 已取消
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['order_operate_status'] = Yii::$service->order->operate_status_canceled;
        $updateColumn = $orderModel->updateAll(
            $updateArr,
            [
                'and',
                ['order_id' => $orderModel['order_id']],
                ['<', 'updated_at', $begin_time],
                ['in', 'order_status', Yii::$service->order->info->orderStatusRedirectCancelArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusRedirectCancelArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order increment id: {increment_id} cancel fail', ['increment_id' => $orderModel['increment_id']]);
            
            return false;
        }
        
        // 返还库存
        $order_primary_key   = Yii::$service->order->getPrimaryKey();
        $product_items          = Yii::$service->order->item->getByOrderId($orderModel[$order_primary_key], true);
        
        $returnQtyStatus = Yii::$service->product->stock->returnQty($product_items);
        
        // 添加订单操作日志。
        if ($returnQtyStatus) {
            $logType = Yii::$service->order->processLog->order_payment_pending_console_auto_cancel;
            Yii::$service->order->processLog->consoleAdd($orderModel, $logType);
            
            return true;
        }   
        
        return false;
    }
    
    /**
     * @param $increment_id | string, 订单编号
     * @return boolean, 
     * 前端用户是否可以发起订单取消请求，如果可以，那么用户在账户中心可以对该订单发起 订单取消申请请求
     */
    public function customerRequestCancelByIncrementId($increment_id, $customer_id='') {
        $orderModel = Yii::$service->order->getByIncrementId($increment_id);
        // 判断当前的customer_id是否等于订单中的customer_id
        if ($customer_id && $orderModel['customer_id'] != $customer_id) {
            Yii::$service->helper->errors->add('you donot have role operate this order, current customer_id:{customer_id} is not equel to order customer_id:{order_customer_id}', [
                'customer_id' => $customer_id, 
                'order_customer_id' => $orderModel['customer_id'] 
            ]);
            
            return false;
        }
        // 订单是否可以直接被取消。
        if (Yii::$service->order->info->isCustomerCanRedirectCancel($orderModel)) {
            if ($this->redirectCancel($orderModel)) {
                
                return true;
            }
        } 
        // 查看订单是否可以通过审核的方式被取消。
        if (Yii::$service->order->info->isCustomerCanRequestCancel($orderModel)) {
            if ($this->requestCancel($orderModel)) {
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * @param $increment_id | string, 订单编号
     * @return boolean, 
     * 前端用户发起订单取消请求，然后，进行 撤销 订单取消请求
     */
    public function customerCancelBackByIncrementId($increment_id, $customer_id='') {
        $orderModel = Yii::$service->order->getByIncrementId($increment_id);
        // 判断当前的customer_id是否等于订单中的customer_id
        if ($customer_id && $orderModel['customer_id'] != $customer_id) {
            Yii::$service->helper->errors->add('you donot have role operate this order, current customer_id:{customer_id} is not equel to order customer_id:{order_customer_id}', [
                'customer_id' => $customer_id, 
                'order_customer_id' => $orderModel['customer_id'] 
            ]);
            
            return false;
        }
        if (!Yii::$service->order->info->isCustomerCanCancelBack($orderModel)) {
            return false;
        } 
        
        if (!$this->cancelBack($orderModel)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * @param $increment_id | string, 订单编号
     * @return boolean, 
     * 后台bdmin，将用户提交的订单取消
     */
    public function bdminAuditCancelByIncrementId($increment_id) {
        $orderModel = Yii::$service->order->getByIncrementId($increment_id);
        // 订单是否可以直接被取消。
        if (Yii::$service->order->info->isBdminCanAuditCancel($orderModel)) {
            if ($this->auditCancel($orderModel)) {
                
                return true;
            }
        } 
        
        return false;
    }
    
    /**
     * @param $order_id | int, 订单id
     * @return boolean, 
     * 后台bdmin，取消订单审核通过
     */
    public function bdminAuditCancelAcceptById($order_id) {
        $orderModel = Yii::$service->order->getByPrimaryKey($order_id);
        // 订单是否可以直接被取消。
        if (Yii::$service->order->info->isBdminCanAuditCancel($orderModel)) {
            if ($this->auditCancelAccept($orderModel)) {
                
                return true;
            }
        } 
        
        return false;
    }
    
    /**
     * @param $order_id | int, 订单id
     * @return boolean, 
     * 后台bdmin，将用户提交的订单取消，拒绝
     */
    public function bdminAuditCancelRefuseById($order_id) {
        $orderModel = Yii::$service->order->getByPrimaryKey($order_id);
        // 订单是否可以直接被取消。
        if (Yii::$service->order->info->isBdminCanAuditCancel($orderModel)) {
            if ($this->auditCancelRefuse($orderModel)) {
                
                return true;
            }
        } 
        
        return false;
    }
    
    /**
     * @param $order_id | int, 订单id
     * @return boolean, 
     * 后台bdmin，审核订单通过
     */
    public function bdminAuditOrderAcceptById($order_id) {
        $orderModel = Yii::$service->order->getByPrimaryKey($order_id);
        // 订单是否可以直接被取消。
        if (Yii::$service->order->info->isBdminCanAuditOrderAccept($orderModel)) {
            if ($this->auditOrderAccept($orderModel)) {
                
                return true;
            }
        } 
        
        return false;
    }
    
    /**
     * @param $order_id | int, 订单id
     * @return boolean, 
     * 后台bdmin，审核订单拒绝
     */
    public function bdminAuditOrderRefuseById($order_id) {
        $orderModel = Yii::$service->order->getByPrimaryKey($order_id);
        // 订单是否可以直接被取消。
        if (Yii::$service->order->info->isBdminCanAuditOrderRefuse($orderModel)) {
            if ($this->auditOrderRefuse($orderModel)) {
                
                return true;
            }
        } 
        
        return false;
    }
    
    /**
     * @param $order_id | int, 订单id
     * @return boolean, 
     * 后台bdmin，订单发货
     */
    public function bdminDispatchOrderById($order_id, $tracking_number) {
        $orderModel = Yii::$service->order->getByPrimaryKey($order_id);
        if (!$tracking_number) {
            Yii::$service->helper->errors->add('tracking number is empty');
            
            return false;
        }
        // 订单是否可以直接被取消。
        if (Yii::$service->order->info->isBdminCanDispatchOrder($orderModel)) {
            if ($this->dispatchOrder($orderModel, $tracking_number)) {
                
                return true;
            }
        } 
        
        return false;
    }
    
    /**
     * @param $order_id | int, 订单id
     * @return boolean, 
     * 后台bdmin，订单发货
     */
    public function bdminDispatchOrderByIncrementId($increment_id, $tracking_number, $bdmin_user_id) {
        $orderModel = Yii::$service->order->getByIncrementId($increment_id);
        if (!$bdmin_user_id || $bdmin_user_id != $orderModel['bdmin_user_id']) {
            Yii::$service->helper->errors->add('you do not have role to operate this order');
        }
        // 订单是否可以直接被取消。
        if (Yii::$service->order->info->isBdminCanDispatchOrder($orderModel)) {
            if ($this->dispatchOrder($orderModel, $tracking_number)) {
                
                return true;
            }
        } 
        
        return false;
    }
    
    /**
     * @param $order_id | int, 订单id
     * @return boolean, 
     * 后台bdmin，延迟订单收货时间
     */
    public function customerDelayReceiveOrderById($order_id, $customer_id='')
    {
        $orderModel = Yii::$service->order->getByPrimaryKey($order_id);
        // 判断当前的customer_id是否等于订单中的customer_id
        if ($customer_id && $orderModel['customer_id'] != $customer_id) {
            Yii::$service->helper->errors->add('you donot have role operate this order, current customer_id:{customer_id} is not equel to order customer_id:{order_customer_id}', [
                'customer_id' => $customer_id, 
                'order_customer_id' => $orderModel['customer_id'] 
            ]);
            
            return false;
        }
        // 订单是否进行延迟收货操作。
        if (Yii::$service->order->info->isCustomerCanDelayReceiveOrder($orderModel)) {
            if ($this->delayReceiveOrder($orderModel)) {
                
                return true;
            }
        } 
        
        return false;
    }
    /**
     * @param $order_id | int, 订单id
     * @return boolean, 
     * console脚本端，自动将订单进行售后操作。
     */
    public function autoReceiveOrderById($order_id)
    {
        $orderModel = Yii::$service->order->getByPrimaryKey($order_id);
        // 订单是否进行延迟收货操作。
        if (Yii::$service->order->info->isConsoleCanAutoReceiveOrder($orderModel)) {
            if ($this->receiveOrder($orderModel)) {
                
                return true;
            }
        } 
        
        return false;
        
    }
    /**
     * @param $orderModel | order model
     * @return boolean
     *  进行延迟订单收货时间操作，因为有一些其他的条件判断，因此执行该函数前需要进行判断：Yii::$service->order->info->isCustomerCanDelayReceiveOrder($orderModel)
     */
    public function delayReceiveOrder($orderModel)
    {
        if (!isset($orderModel['increment_id']) || !$orderModel['increment_id']) {
            Yii::$service->helper->errors->add('order model is empty');
            
            return false;
        }
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['recevie_delay_days'] = new Expression('recevie_delay_days +' . $this->delayReceiveOrderDaysPerTime);
        
        $updateColumn = $orderModel->updateAll(
            $updateArr,
            [
                'and',
                ['order_id' => $orderModel['order_id']],
                ['in', 'order_status', Yii::$service->order->info->orderStatusCanDelayReceiveArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusCanDelayReceiveArr],
                
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order increment id: {increment_id} dispatch fail', ['increment_id' => $orderModel['increment_id']]);
            
            return false;
        }
        
        $logType = Yii::$service->order->processLog->order_receive_date_delay; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderModel, $logType);
            
        return true;
    }
    
    /**
     * @param $orderModel | object or Array, 订单model
     * @param $tracking_number | string, 货运追踪号
     * @return boolean, 
     * 后台bdmin，订单发货
     */
    public function dispatchOrder($orderModel, $tracking_number) 
    {
        if (!isset($orderModel['increment_id']) || !$orderModel['increment_id']) {
            Yii::$service->helper->errors->add('order model is empty');
            
            return false;
        }
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['dispatched_at'] = time();
        $updateArr['recevie_delay_days'] = 0;   // 延迟天数
        $updateArr['order_status'] = Yii::$service->order->status_dispatched;
        $updateArr['tracking_number'] = $tracking_number;
        $updateColumn = $orderModel->updateAll(
            $updateArr,
            [
                'and',
                ['order_id' => $orderModel['order_id']],
                ['in', 'order_status', Yii::$service->order->info->orderStatusCanDispatchArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusCanDispatchArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order increment id: {increment_id} dispatch fail', ['increment_id' => $orderModel['increment_id']]);
            
            return false;
        }
        // 订单操作日志。
        $logType = Yii::$service->order->processLog->order_dispatch; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderModel, $logType);
        
        return true;
    }
    
    
    /**
     * @param $orderModel | Object,  order model
     * @return bool
     * 对于已经付款的订单，用户通过发起请求的方式来取消订单（发起后，需要后台管理员审核，是否允许取消）
     *  注意：此方法不是取消订单的方法，而是发起取消订单请求的方法
     */
    public function requestCancel($orderModel)
    {
        if (!isset($orderModel['increment_id']) || !$orderModel['increment_id']) {
            Yii::$service->helper->errors->add('order model is empty');
            
            return false;
        }
        
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['order_operate_status'] = Yii::$service->order->operate_status_waiting_canceled;
        $updateColumn = $orderModel->updateAll(
            $updateArr,
            [
                'and',
                ['order_id' => $orderModel['order_id']],
                ['in', 'order_status', Yii::$service->order->info->orderStatusRequestCancelArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusRequestCancelArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order increment id: {increment_id} request cancel fail', ['increment_id' => $orderModel['increment_id']]);
            
            return false;
        }
        // 订单操作日志。
        $logType = Yii::$service->order->processLog->order_request_cancel; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderModel, $logType);
        
        return true;
    }
    
    /**
     * @param $orderModel | Object,  order model
     * @return bool
     * 用户发起的取消订单，需要后台审核的类型，发起后，等待供应商审核
     * 再这个过程中，用户可以通过该方法撤销订单取消请求。
     */
    public function cancelBack($orderModel)
    {
        if (!isset($orderModel['increment_id']) || !$orderModel['increment_id']) {
            Yii::$service->helper->errors->add('order model is empty');
            
            return false;
        }
        
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['order_operate_status'] = Yii::$service->order->operate_status_normal;
        $updateColumn = $orderModel->updateAll(
            $updateArr,
            [
                'and',
                ['order_id' => $orderModel['order_id']],
                ['in', 'order_status', Yii::$service->order->info->orderStatusCanCancelBackArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusCanCancelBackArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order increment id: {increment_id} cancel back fail', ['increment_id' => $orderModel['increment_id']]);
            
            return false;
        }
        // 订单操作日志。
        $logType = Yii::$service->order->processLog->order_cancel_back; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderModel, $logType);
        
        return true;
    }
    /**
     * @param $orderModel | Object,  order model
     * @return bool
     * 前端用户直接进行的订单取消操作，如果订单满足条件，可以不需要后台进行审核，直接取消订单。
     * 取消订单，更新订单的状态为cancel,并且释放库存给产品
     */
    public function redirectCancel($orderModel)
    {
        if (!isset($orderModel['increment_id']) || !$orderModel['increment_id']) {
            Yii::$service->helper->errors->add('order model is empty');
            
            return false;
        }
        // 更改订单状态--> 已取消
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['order_operate_status'] = Yii::$service->order->operate_status_canceled;
        $updateColumn = $orderModel->updateAll(
            $updateArr,
            [
                'and',
                ['order_id' => $orderModel['order_id']],
                ['in', 'order_status', Yii::$service->order->info->orderStatusRedirectCancelArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusRedirectCancelArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order increment id: {increment_id} cancel fail', ['increment_id' => $orderModel['increment_id']]);
            
            return false;
        }
        // 取消订单进行的退款
        if (!Yii::$service->refund->customerOrderCancelCreateRefund($orderModel)) {
            
            return false;
        }
        
        // 返还库存
        $order_primary_key   = Yii::$service->order->getPrimaryKey();
        $product_items          = Yii::$service->order->item->getByOrderId($orderModel[$order_primary_key], true);
        Yii::$service->product->stock->returnQty($product_items);
        // 订单操作日志。
        $logType = Yii::$service->order->processLog->order_redirect_cancel; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderModel, $logType);
        
        return true;
    }
    
    /**
     * @param $orderModel | Object,  order model
     * @return bool
     * 取消订单，更新订单的状态为cancel。
     * 并且释放库存给产品
     * 此处用于审核用户发起的订单取消请求，发起后，进行订单取消操作
     * 只有审核通过的订单，用户才会发起的取消订单，才会需要审核
     */
    public function auditCancelAccept($orderModel)
    {
        if (!isset($orderModel['increment_id']) || !$orderModel['increment_id']) {
            Yii::$service->helper->errors->add('order model is empty');
            
            return false;
        }
        
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['order_operate_status'] = Yii::$service->order->operate_status_canceled;
        $updateColumn = $orderModel->updateAll(
            $updateArr,
            [
                'and',
                ['order_id' => $orderModel['order_id']],
                ['in', 'order_status', Yii::$service->order->info->orderStatusAuditCancelArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusAuditCancelArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order increment id: {increment_id} cancel fail', ['increment_id' => $orderModel['increment_id']]);
            
            return false;
        }
        // 返还库存
        $order_primary_key   = Yii::$service->order->getPrimaryKey();
        $product_items          = Yii::$service->order->item->getByOrderId($orderModel[$order_primary_key], true);
        Yii::$service->product->stock->returnQty($product_items);
        
        // 进行退款操作。
        if (!Yii::$service->refund->customerOrderCancelCreateRefund($orderModel)) {
            
            return false;
        }
        // 订单操作日志。
        $logType = Yii::$service->order->processLog->order_cancel_audit_accept; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderModel, $logType);
        
        return true;
    }
    
    /**
     * @param $orderModel | Object,  order model
     * @return bool
     * 取消订单，拒绝用户取消订单的申请
     */
    public function auditCancelRefuse($orderModel)
    {
        if (!isset($orderModel['increment_id']) || !$orderModel['increment_id']) {
            Yii::$service->helper->errors->add('order model is empty');
            
            return false;
        }
        
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['order_operate_status'] = Yii::$service->order->operate_status_normal;
        $updateColumn = $orderModel->updateAll(
            $updateArr,
            [
                'and',
                ['order_id' => $orderModel['order_id']],
                ['in', 'order_status', Yii::$service->order->info->orderStatusAuditCancelArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusAuditCancelArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order increment id: {increment_id} cancel fail', ['increment_id' => $orderModel['increment_id']]);
            
            return false;
        }
        // 订单操作日志。
        $logType = Yii::$service->order->processLog->order_cancel_audit_refuse; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderModel, $logType);
        
        return true;
    }
    
    
    /**
     * @param $orderModel | Object,  order model
     * @return bool
     * 订单审核通过（bdmin后台审核订单） 
     */
    public function auditOrderAccept($orderModel)
    {
        if (!isset($orderModel['increment_id']) || !$orderModel['increment_id']) {
            Yii::$service->helper->errors->add('order model is empty');
            
            return false;
        }
        
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['bdmin_audit_acceptd_at'] = time();
        $updateArr['order_status'] = Yii::$service->order->status_processing;
        $updateColumn = $orderModel->updateAll(
            $updateArr,
            [
                'and',
                ['order_id' => $orderModel['order_id']],
                ['in', 'order_status', Yii::$service->order->info->orderStatusAuditOrderAcceptArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusAuditOrderAcceptArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order increment id: {increment_id} audit order info fail', ['increment_id' => $orderModel['increment_id']]);
            
            return false;
        }
        // 订单操作日志。
        $logType = Yii::$service->order->processLog->order_audit_accept; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderModel, $logType);
        
        return true;
    }
    
    
    /**
     * @param $orderModel | Object,  order model
     * @return bool
     * 订单审核通过（bdmin后台审核订单） 
     */
    public function auditOrderRefuse($orderModel)
    {
        if (!isset($orderModel['increment_id']) || !$orderModel['increment_id']) {
            Yii::$service->helper->errors->add('order model is empty');
            
            return false;
        }
        
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['order_status'] = Yii::$service->order->status_audit_fail;
        $updateColumn = $orderModel->updateAll(
            $updateArr,
            [
                'and',
                ['order_id' => $orderModel['order_id']],
                ['in', 'order_status', Yii::$service->order->info->orderStatusAuditOrderRefuseArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusAuditOrderRefuseArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order increment id: {increment_id} audit order info fail', ['increment_id' => $orderModel['increment_id']]);
            
            return false;
        }
        // 订单操作日志。
        $logType = Yii::$service->order->processLog->order_audit_refuse; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderModel, $logType);
        
        return true;
    }
    /**
     * @param $orderModel | Object,  order model
     * @return bool
     * 订单收货操作（订单发货后，customer收货后，进行订单收货操作） 
     */
    public function receiveOrder($orderModel)
    {
        if (!isset($orderModel['increment_id']) || !$orderModel['increment_id']) {
            Yii::$service->helper->errors->add('order model is empty');
            
            return false;
        }
        
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['received_at'] = time();
        $updateArr['order_status'] = Yii::$service->order->status_received;
        $updateColumn = $orderModel->updateAll(
            $updateArr,
            [
                'and',
                ['order_id' => $orderModel['order_id']],
                ['in', 'order_status', Yii::$service->order->info->orderStatusCanReceivedArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusCanReceivedArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order increment id: {increment_id} receive order info fail', ['increment_id' => $orderModel['increment_id']]);
            
            return false;
        }
        // 订单操作日志。
        $logType = Yii::$service->order->processLog->order_receive; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderModel, $logType);
        
        return true;
    }
    
    
    /**
     * @param $order | object or Array, order data
     * @return boolean, 
     * 前端用户在账户中心可以对该订单发起 订单收货操作。
     */
    public function customerReceiveOrderByIncrementId($increment_id, $customer_id='') {
        $orderModel = Yii::$service->order->getByIncrementId($increment_id);
        // 判断当前的customer_id是否等于订单中的customer_id
        if ($customer_id && $orderModel['customer_id'] != $customer_id) {
            Yii::$service->helper->errors->add('you donot have role operate this order, current customer_id:{customer_id} is not equel to order customer_id:{order_customer_id}', [
                'customer_id' => $customer_id, 
                'order_customer_id' => $orderModel['customer_id'] 
            ]);
            
            return false;
        }
        // 订单是否可以收货操作。
        if (Yii::$service->order->info->isCustomerCanReceive($orderModel)) {
            if ($this->receiveOrder($orderModel)) {
                
                return true;
            }
        } 
        
        return false;
    }
    
    
    
    // 记录订单操作记录
    public function logHistory($orderModel, $operate){
        
        
    }
}
