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
class AfterSale extends \fecshop\services\Service
{
    
    /**
     *  订单售后状态
     */
    // 1.订单售后退货申请状态
    public $after_sale_status_return_request   = 'after_sale_waiting_return';
    // 2.订单售后退货申请通过状态
    public $after_sale_status_return_accept   = 'after_sale_return_accept';
    // 3.订单售后退货申请被拒绝状态
    public $after_sale_status_return_refuse   = 'after_sale_return_refuse';
    // 4.订单售后退货申请取消状态
    public $after_sale_status_return_cancel   = 'after_sale_return_cancel';
    // 5.订单售后退货已发货状态
    public $after_sale_status_return_dispatch   = 'after_sale_return_dispatch';
    // 6.订单售后退货已收货状态
    public $after_sale_status_return_received   = 'after_sale_return_received';
    // 7.订单售后退货已退款状态
    public $after_sale_status_return_refund   = 'after_sale_return_refund';
    
    
    
    // 订单售后换货申请状态
    //public $after_sale_status_waiting_exchange   = 'after_sale_waiting_exchange';
    // 订单售后已换货状态
    //public $after_sale_status_exchange   = 'after_sale_exchange';
    // 订单售后返修申请状态
    //public $after_sale_status_waiting_rework   = 'after_sale_waiting_rework';
    // 订单售后已返修状态
    //public $after_sale_status_rework   = 'after_sale_rework';
    // 订单已退款【已收款订单因为某些原因进行退款，譬如：仓库无货，用户收到货后发现破损退款等】
    //public $operate_status_refunded   = 'operate_refunded';
    protected $_modelName = '\fbbcbase\models\mysqldb\order\AfterSale';

    protected $_model;
    
    public function init()
    {
        parent::init();
        list($this->_modelName, $this->_model) = \Yii::mapGet($this->_modelName);
    }
    
    public function getAllReturnStatusArr(){
        return [
            $this->after_sale_status_return_request => Yii::$service->page->translate->__($this->after_sale_status_return_request),
            $this->after_sale_status_return_accept => Yii::$service->page->translate->__($this->after_sale_status_return_accept),
            $this->after_sale_status_return_refuse => Yii::$service->page->translate->__($this->after_sale_status_return_refuse),
            $this->after_sale_status_return_cancel => Yii::$service->page->translate->__($this->after_sale_status_return_cancel),
            $this->after_sale_status_return_dispatch => Yii::$service->page->translate->__($this->after_sale_status_return_dispatch),
            $this->after_sale_status_return_received => Yii::$service->page->translate->__($this->after_sale_status_return_received),
            $this->after_sale_status_return_refund => Yii::$service->page->translate->__($this->after_sale_status_return_refund),
        ];
        
    }
    
    /**
     * 得到order 表的id字段。
     */
    protected function actionGetPrimaryKey()
    {
        return 'id';
    }

    /**
     * @param $primaryKey | Int
     * @return Object($this->_orderModel)
     * 通过主键值，返回Order Model对象
     */
    protected function actionGetByPrimaryKey($primaryKey)
    {
        $one = $this->_model->findOne($primaryKey);
        $primaryKey = $this->getPrimaryKey();
        if ($one[$primaryKey]) {
            return $one;
        } else {
            return new $this->_modelName();
        }
    }
    
    protected function actionGetByItemId($item_id)
    {
        $one = $this->_model->findOne(['item_id' => $item_id]);
        $primaryKey = $this->getPrimaryKey();
        if ($one[$primaryKey]) {
            return $one;
        } else {
            return new $this->_modelName();
        }
    }
    
    public function getInfoByPrimaryKey($as_id)
    {
        $item = $this->_model->findOne($as_id);
        $product_one = Yii::$service->product->getByPrimaryKey($item['product_id']);
        $item_arr = [];
        if (!isset($item['id']) || !$item['id']) {
            Yii::$service->helper->errors->add('afterSale is empty or is not array');
            
            return null;
        }
        if (!isset($product_one['sku']) || !$product_one['sku']) {
            Yii::$service->helper->errors->add('product is empty');
            
            return null;
            
        }
        foreach ($item as $k=>$v) {
            $item_arr[$k] = $v;
        }
        $item_arr['custom_option'] =  $product_one['custom_option'];
        $item_arr['custom_option_info'] = $this->getProductOptions($item_arr);
        
        return $item_arr;
    }
    
    /**
     * @param $item_one | Array , order item
     * 通过$item_one 的$item_one['custom_option_sku']，$item_one['custom_option'] , $item_one['spu_options']
     * 将spu的选择属性和自定义属性custom_option 组合起来，返回一个统一的数组
     */
    public function getProductOptions($item_one)
    {
        $custom_option_sku = $item_one['custom_option_sku'];
        $custom_option_info_arr = [];
        $custom_option = isset($item_one['custom_option']) ? $item_one['custom_option'] : '';
        if (isset($custom_option[$custom_option_sku]) && !empty($custom_option[$custom_option_sku])) {
            $custom_option_info = $custom_option[$custom_option_sku];
            foreach ($custom_option_info as $attr=>$val) {
                if (!in_array($attr, ['qty', 'sku', 'price', 'image'])) {
                    $attr = str_replace('_', ' ', $attr);
                    $attr = ucfirst($attr);
                    $custom_option_info_arr[$attr] = $val;
                }
            }
        }
        $spu_options = isset($item_one['spu_options']) ? $item_one['spu_options'] : '';
        if (is_array($spu_options) && !empty($spu_options)) {
            foreach ($spu_options as $label => $val) {
                $custom_option_info_arr[$label] = $val;
            }
        }

        return $custom_option_info_arr;
    }
    
    /**
     * @param $filter|array
     * @return Array;
     *              通过过滤条件，得到coupon的集合。
     *              example filter:
     *              [
     *                  'numPerPage' 	=> 20,
     *                  'pageNum'		=> 1,
     *                  'orderBy'	    => ['_id' => SORT_DESC, 'sku' => SORT_ASC ],
     *                  'where'			=> [
     *                      ['>','price',1],
     *                      ['<=','price',10]
     * 			            ['sku' => 'uk10001'],
     * 		            ],
     * 	                'asArray' => true,
     *              ]
     * 根据$filter 搜索参数数组，返回满足条件的订单数据。
     */
    protected function actionColl($filter = '')
    {
        $query  = $this->_model->find();
        $query  = Yii::$service->helper->ar->getCollByFilter($query, $filter);
        $coll   = $query->all();
        
        return [
            'coll' => $coll,
            'count'=> $query->limit(null)->offset(null)->count(),
        ];
    }
    
    
    /**
     * @param $orderItemModel | order item model
     * 
     * 用户发起退货请求，创建退货
     */
    public function requestReturn($orderModel, $orderItemModel, $return_qty){
        if (!$this->isCanRequestReturn($orderItemModel)) {
            
            return false;
        }
        $order_id = $orderItemModel['order_id'];
        $customer_id = $orderItemModel['customer_id'];
        $bdmin_user_id = $orderItemModel['bdmin_user_id'];
        $item_id = $orderItemModel['item_id'];
        $sku = $orderItemModel['sku'];
        list($baseReturnCost, $returnCost) = $this->getReturnCost($orderModel, $orderItemModel, $return_qty) ;
        // 创建退款记录
        $orderAfterSale = new $this->_modelName();
        $orderAfterSale->order_id = $order_id;
        $orderAfterSale->increment_id = $orderModel['increment_id'];
        $orderAfterSale->payment_method = $orderModel['payment_method'];
        $orderAfterSale->bdmin_user_id = $bdmin_user_id;
        $orderAfterSale->customer_id = $customer_id;
        $orderAfterSale->item_id = $item_id;
        $orderAfterSale->status = $this->after_sale_status_return_request;
        $orderAfterSale->sku = $sku;
        $orderAfterSale->image = $orderItemModel['image'];
        $orderAfterSale->product_id = $orderItemModel['product_id'];
        $orderAfterSale->custom_option_sku = $orderItemModel['custom_option_sku'];
        $orderAfterSale->currency_code = $orderModel['order_currency_code'];
        $orderAfterSale->order_to_base_rate = $orderModel['order_to_base_rate'];
        
        $orderAfterSale->price = $returnCost;
        $orderAfterSale->base_price = $baseReturnCost;
        $orderAfterSale->qty = $return_qty;
        $orderAfterSale->created_at = time();
        $orderAfterSale->updated_at = time();
        $orderAfterSale->save();
        
        // 售后操作日志。
        $logType = Yii::$service->order->processLog->after_sale_return_request; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderAfterSale, $logType);
        
        return true;
    }
    
    /**
     * @param $orderItemModel
     * 是否可以发起退款请求，一个order items只能发起依次退款请求
     */
    protected function isCanRequestReturn($orderItemModel){
        $item_id = $orderItemModel['item_id'];
        $orderAfterSaleOne = $this->_model->findOne(['item_id' => $item_id]);
        if ($orderAfterSaleOne['item_id']) {
            Yii::$service->helper->errors->add('order item id: {item_id} is exist in order after sale', ['item_id' => $item_id]);
            return false;
        }
        
        return true;
    }
    /**
     *
     * 得到退款的金额
     */
    protected function getReturnCost($orderModel, $orderItemModel, $return_qty){
        $grand_total = $orderModel['grand_total'];
        $base_grand_total= $orderModel['base_grand_total'];
        $subtotal= $orderModel['subtotal'];
        $base_subtotal= $orderModel['base_subtotal'];
        $subtotal_with_discount= $orderModel['subtotal_with_discount'];
        $base_subtotal_with_discount= $orderModel['base_subtotal_with_discount'];
        $shipping_total = $orderModel['shipping_total'];
        $base_shipping_total = $orderModel['base_shipping_total'];
        
        $item_price = $orderItemModel['price'];
        $item_base_price = $orderItemModel['base_price'];
        // 退款产品的优惠金额 = (订单产品单价 * 个数/订单产品总金额) * 订单优惠
        $baseReturnDiscount = (($item_base_price * $return_qty) / $base_subtotal) * $base_subtotal_with_discount;
        // 退款的金额 = 订单产品单价 * 个数 - 退款产品的优惠金额
        $baseReturnCost = $item_base_price * $return_qty - $baseReturnDiscount;
        $baseReturnCost = Yii::$service->helper->format->number_format($baseReturnCost);
        
        // 退款产品的优惠金额 = (订单产品单价 * 个数/订单产品总金额) * 订单优惠
        $returnDiscount = (($item_price * $return_qty) / $subtotal) * $subtotal_with_discount;
        // 退款的金额 = 订单产品单价 * 个数 - 退款产品的优惠金额
        $returnCost = $item_price * $return_qty - $returnDiscount;
        $returnCost = Yii::$service->helper->format->number_format($returnCost);
        
        return [$baseReturnCost, $returnCost];
    }
    /**
     *
     * 退货：供应商进行审核通过操作
     */
    public function bdminAuditAcceptReturnByAsId($as_id){
        $orderAfterSaleModel = $this->_model->findOne($as_id);
        // 状态是否允许判断
        if (!Yii::$service->order->info->isBdminCanAcceptAfterSaleReturndOrder($orderAfterSaleModel)) {
            Yii::$service->helper->errors->add('order after sale return after_sale_id: {as_id}, status: {status} can not audit accept ', ['as_id'=>$as_id, 'status'=>$orderAfterSaleModel['status']]);
            
            return false;
        }
        // 审核通过
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['status'] = $this->after_sale_status_return_accept;
        $updateColumn = $orderAfterSaleModel->updateAll(
            $updateArr,
            [
                'and',
                ['id' => $as_id],
                ['in', 'status', Yii::$service->order->info->afterSaleReturnStatusCanAuditAcceptArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order after sale return after_sale_id: {as_id}, status: {status} audit accept fail', ['as_id'=>$as_id, 'status'=>$orderAfterSaleModel['status']]);
            
            return false;
        }
        // 售后操作日志。
        $logType = Yii::$service->order->processLog->after_sale_return_audit_accept; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderAfterSaleModel, $logType);
        
        return true;
    }
    
    /**
     *
     * 退货：供应商进行审核拒绝操作
     */
    public function bdminAuditRefuseReturnByAsId($as_id){
        $orderAfterSaleModel = $this->_model->findOne($as_id);
        // 状态是否允许判断
        if (!Yii::$service->order->info->isBdminCanRefuseAfterSaleReturndOrder($orderAfterSaleModel)) {
            Yii::$service->helper->errors->add('order after sale return after_sale_id: {as_id}, status: {status} can not audit refuse ', ['as_id'=>$as_id, 'status'=>$orderAfterSaleModel['status']]);
            
            return false;
        }
        // 审核通过
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['status'] = $this->after_sale_status_return_refuse;
        $updateColumn = $orderAfterSaleModel->updateAll(
            $updateArr,
            [
                'and',
                ['id' => $as_id],
                ['in', 'status', Yii::$service->order->info->afterSaleReturnStatusCanAuditRefuseArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order after sale return after_sale_id: {as_id}, status: {status} audit refuse fail', ['as_id'=>$as_id, 'status'=>$orderAfterSaleModel['status']]);
            
            return false;
        }
        // 售后操作日志。
        $logType = Yii::$service->order->processLog->after_sale_return_audit_refuse; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderAfterSaleModel, $logType);
        
        return true;
    }
    
   
    /**
     *
     * 退货：用户进行取消退货操作
     */
    public function customerCancelReturnByAsId($as_id, $customer_id=''){
        $orderAfterSaleModel = $this->_model->findOne($as_id);
        // 判断当前的customer_id是否等于订单中的customer_id
        if ($customer_id && $orderAfterSaleModel['customer_id'] != $customer_id) {
            Yii::$service->helper->errors->add('you donot have role operate this order after sale, current customer_id:{customer_id} is not equel to order after sale customer_id:{order_customer_id}', [
                'customer_id' => $customer_id, 
                'order_customer_id' => $orderAfterSaleModel['customer_id'] 
            ]);
            
            return false;
        }
        // 状态是否允许判断
        if (!Yii::$service->order->info->isCustomerCanCancelAfterSaleReturndOrder($orderAfterSaleModel)) {
            Yii::$service->helper->errors->add('order after sale return after_sale_id: {as_id}, status: {status} can not cancel ', ['as_id'=>$as_id, 'status'=>$orderAfterSaleModel['status']]);
            
            return false; 
        }
        // 审核通过
        $deleteColumn = $orderAfterSaleModel->deleteAll(
            [
                'and',
                ['id' => $as_id],
                ['in', 'status', Yii::$service->order->info->afterSaleReturnStatusCanCancelArr],
            ]
        );
        if (empty($deleteColumn)) {
            Yii::$service->helper->errors->add('order after sale return after_sale_id: {as_id}, status: {status} cancel fail', ['as_id'=>$as_id, 'status'=>$orderAfterSaleModel['status']]);
            
            return false;
        }
        // 售后操作日志。
        $logType = Yii::$service->order->processLog->after_sale_return_cancel_return; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderAfterSaleModel, $logType);
        
        return true;
        
    }
    /**
     *
     * 退货：用户进行发货操作
     */
    public function customerDispatchReturnByAsId($as_id, $tracking_number, $customer_id=''){
        $orderAfterSaleModel = $this->_model->findOne($as_id);
        if ($customer_id && $orderAfterSaleModel['customer_id'] != $customer_id) {
            Yii::$service->helper->errors->add('you donot have role operate this order after sale, current customer_id:{customer_id} is not equel to order after sale customer_id:{order_customer_id}', [
                'customer_id' => $customer_id, 
                'order_customer_id' => $orderAfterSaleModel['customer_id'] 
            ]);
            
            return false;
        }
        // 状态是否允许判断
        if (!Yii::$service->order->info->isCustomerCanDispatchAfterSaleReturndOrder($orderAfterSaleModel)) {
            Yii::$service->helper->errors->add('order after sale return after_sale_id: {as_id}, status: {status} can not dispatch ', ['as_id'=>$as_id, 'status'=>$orderAfterSaleModel['status']]);
            
            return false; 
        }
        // 审核通过
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['tracking_number'] = $tracking_number;
        $updateArr['status'] = $this->after_sale_status_return_dispatch;
        $updateColumn = $orderAfterSaleModel->updateAll(
            $updateArr,
            [
                'and',
                ['id' => $as_id],
                ['in', 'status', Yii::$service->order->info->afterSaleReturnStatusCanDispatchArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order after sale return after_sale_id: {as_id}, status: {status} dispatch fail', ['as_id'=>$as_id, 'status'=>$orderAfterSaleModel['status']]);
            
            return false;
        }
        // 售后操作日志。
        $logType = Yii::$service->order->processLog->after_sale_return_dispatch; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderAfterSaleModel, $logType);
        
        return true;
        
    }
    /**
     *
     * 退货：供应商进行收货操作
     */
    public function bdminReceiveReturnByAsId($as_id){
        $orderAfterSaleModel = $this->_model->findOne($as_id);
        // 状态是否允许判断
        if (!Yii::$service->order->info->isBdminCanReceiveAfterSaleReturndOrder($orderAfterSaleModel)) {
            Yii::$service->helper->errors->add('order after sale return after_sale_id: {as_id}, status: {status} can not receive ', ['as_id'=>$as_id, 'status'=>$orderAfterSaleModel['status']]);
            
            return false; 
        }
        // 审核通过
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['status'] = $this->after_sale_status_return_received;
        $updateColumn = $orderAfterSaleModel->updateAll(
            $updateArr,
            [
                'and',
                ['id' => $as_id],
                ['in', 'status', Yii::$service->order->info->afterSaleReturnStatusCanReceiveArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order after sale return after_sale_id: {as_id}, status: {status} receive fail', ['as_id'=>$as_id, 'status'=>$orderAfterSaleModel['status']]);
            
            return false;
        }
        // 生成退款记录
        if (!Yii::$service->refund->customerOrderReturnCreateRefund($orderAfterSaleModel)) {
            
            return false;
        }
        // 售后操作日志。
        $logType = Yii::$service->order->processLog->after_sale_return_received; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderAfterSaleModel, $logType);
        
        return true;
        
    }
    
    
    /**
     *
     * 退货：退货货物收到后，进行退款操作 refund
     */
    public function refundReturnByAsId($as_id){
        $orderAfterSaleModel = $this->_model->findOne($as_id);
        // 状态是否允许判断
        if (!Yii::$service->order->info->isBdminCanRefundAfterSaleReturndOrder($orderAfterSaleModel)) {
            Yii::$service->helper->errors->add('order after sale return after_sale_id: {as_id}, status: {status} can not refund ', ['as_id'=>$as_id, 'status'=>$orderAfterSaleModel['status']]);
            
            return false; 
        }
        // 审核通过
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['status'] = $this->after_sale_status_return_refund;
        $updateColumn = $orderAfterSaleModel->updateAll(
            $updateArr,
            [
                'and',
                ['id' => $as_id],
                ['in', 'status', Yii::$service->order->info->afterSaleReturnStatusCanRefundArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order after sale return after_sale_id: {as_id}, status: {status} refund fail', ['as_id'=>$as_id, 'status'=>$orderAfterSaleModel['status']]);
            
            return false;
        }
        // 售后操作日志。
        $logType = Yii::$service->order->processLog->after_sale_return_refund; // delayReceiveOrder;
        Yii::$service->order->processLog->consoleAdd($orderAfterSaleModel, $logType);
        
        return true;
        
    }
    
    
}
