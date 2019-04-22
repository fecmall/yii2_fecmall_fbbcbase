<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\services;

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
class Refund extends Service
{
    // 类型：用户在线支付订单，取消订单进而导致的退款，由平台支付退款
    public $type_system_admin_refund_order_cancel = 'system_order_cancel_admin_cancel';
    
    // 类型：用户在线支付订单，订单退货进而导致的退款，由平台支付退款
    public $type_system_admin_refund_order_return = 'system_order_return_admin_refund';
    
    // 类型：用户订单货到付款，收货支付后，售后环节进行的退款，由供应商支付退款
    public $type_system_bdmin_refund_order_return = 'system_order_return_bdmin_refund';
    
    // 状态：退款未支付
    public $status_payment_pending = 'payment_pending';
    // 状态：退款已支付
    public $status_payment_confirmed = 'payment_confirmed';
    
    public $refundCanPaymentConfirmedArr;
    
    public $refundPaymentConfirmedArr;
    
    protected $_adminModelName = '\fbbcbase\models\mysqldb\refund\AdminRefund';

    protected $_adminModel;
    
    protected $_bdminModelName = '\fbbcbase\models\mysqldb\refund\BdminRefund';

    protected $_bdminModel;
    
    protected $_model;
    
    protected $_modelName;
    
    protected $_currentType;
    
    protected $_isInited;
    
    public function init()
    {
        parent::init();
        $this->refundCanPaymentConfirmedArr = [
            $this->status_payment_pending,
        ];
        
        $this->refundPaymentConfirmedArr = [
            $this->status_payment_confirmed,
        ];
        
    }
    
    public function getAllRefundTypeArr(){
        return [
            $this->type_system_admin_refund_order_cancel  => Yii::$service->page->translate->__($this->type_system_admin_refund_order_cancel),
            $this->type_system_admin_refund_order_return  => Yii::$service->page->translate->__($this->type_system_admin_refund_order_return),
            $this->type_system_bdmin_refund_order_return  => Yii::$service->page->translate->__($this->type_system_bdmin_refund_order_return),
        ];
    }
    public function getAllRefundStatusArr(){
        return [
            $this->status_payment_pending  => Yii::$service->page->translate->__($this->status_payment_pending),
            $this->status_payment_confirmed  => Yii::$service->page->translate->__($this->status_payment_confirmed),
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
    protected function actionGetByPrimaryKey($primaryKey, $type = "admin")
    {
        $this->initModel($type); 
        $one = $this->_model->findOne($primaryKey);
        $primaryKey = $this->getPrimaryKey();
        if ($one[$primaryKey]) {
            return $one;
        } else {
            return new $this->_modelName();
        }
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
    protected function actionColl($filter = '', $type = "admin")
    {
        $this->initModel($type); 
        $query  = $this->_model->find();
        $query  = Yii::$service->helper->ar->getCollByFilter($query, $filter);
        $coll   = $query->all();
        
        return [
            'coll' => $coll,
            'count'=> $query->limit(null)->offset(null)->count(),
        ];
    }
    
    public function initModelForce($type="admin"){
        if ($type == 'bdmin') {
            list($this->_modelName, $this->_model) = \Yii::mapGet($this->_bdminModelName);
            $this->_currentType = $this->type_system_bdmin_refund_order_return;
        } else if ($type == 'admin'){
            list($this->_modelName, $this->_model) = \Yii::mapGet($this->_adminModelName);
            $this->_currentType = $this->type_system_admin_refund_order_return;
        }
        $this->_isInited = true;
    }
    
    public function initModel($type="admin"){
        if (!$this->_isInited) {
            if ($type == 'bdmin') {
                list($this->_modelName, $this->_model) = \Yii::mapGet($this->_bdminModelName);
                $this->_currentType = $this->type_system_bdmin_refund_order_return;
            } else if ($type == 'admin'){
                list($this->_modelName, $this->_model) = \Yii::mapGet($this->_adminModelName);
                $this->_currentType = $this->type_system_admin_refund_order_return;
            }
            $this->_isInited = true;
        }
    }
    
    
    protected function initModelAndTypeByPayMethod($payment_method){
        if (!$this->_isInited) {
            if (Yii::$service->payment->isCashOnDeliveryMethod($payment_method)) {
                list($this->_modelName, $this->_model) = \Yii::mapGet($this->_bdminModelName);
                $this->_currentType = $this->type_system_bdmin_refund_order_return;
            } else {
                list($this->_modelName, $this->_model) = \Yii::mapGet($this->_adminModelName);
                $this->_currentType = $this->type_system_admin_refund_order_return;
            }
            $this->_isInited = true;
        }
    }
    
    protected function initOrderCancelModelAndType(){
        list($this->_modelName, $this->_model) = \Yii::mapGet($this->_adminModelName);
        $this->_currentType = $this->type_system_admin_refund_order_cancel;
        $this->_isInited = true;
    }
    
    
    // 用户订单取消，进而产生的退款。
    public function customerOrderCancelRefund(){
        
        
    }
    
    
    /**
     * @param $afterSaleModel | after sale model
     * @param $customer_bank | string, 用户的银行名称
     * @param $customer_bank_name | string, 用户银行账户对应的用户姓名
     * @param $customer_bank_account | string, 用户用户银行账户。
     * @return boolean
     * 用户订单退货，进而产生的退款，通过该方法生成退款。
     */
    public function customerOrderReturnCreateRefund($afterSaleModel, $customer_bank='', $customer_bank_name='', $customer_bank_account=''){
        $as_id = $afterSaleModel['id'];
        $order_id = $afterSaleModel['order_id'];
        $increment_id = $afterSaleModel['increment_id'];
        $payment_method = $afterSaleModel['payment_method'];
        $bdmin_user_id = $afterSaleModel['bdmin_user_id'];
        $customer_id = $afterSaleModel['customer_id'];
        $status = $afterSaleModel['status'];
        $sku = $afterSaleModel['sku'];
        $custom_option_sku = $afterSaleModel['custom_option_sku'];
        $product_id = $afterSaleModel['product_id'];
        $currency_code = $afterSaleModel['currency_code'];
        $order_to_base_rate = $afterSaleModel['order_to_base_rate'];
        $image = $afterSaleModel['image'];
        $price = $afterSaleModel['price'];
        $base_price = $afterSaleModel['base_price'];
        $qty = $afterSaleModel['qty'];
        $item_id = $afterSaleModel['item_id'];
        $customer = Yii::$service->customer->getByPrimaryKey($customer_id);
        if ($customer['id']) {
            $customer_bank = $customer_bank ? $customer_bank : $customer['customer_bank'];
            $customer_bank_name = $customer_bank_name ? $customer_bank_name : $customer['customer_bank_name'];
            $customer_bank_account = $customer_bank_account ? $customer_bank_account : $customer['customer_bank_account'];
        }
        $this->initModelAndTypeByPayMethod($payment_method);
        //$this->_currentType 
        //$this->status_payment_pending
        $one = $this->_model->findOne(['as_id' => $as_id]);
        if ($one['as_id']) {
            Yii::$service->helper->errors->add('refund return: as_id is exist in refund table');
            
            return false;
        }
        
        $model = new $this->_modelName();
        $model->bdmin_user_id = $bdmin_user_id;
        $model->as_id = $as_id;
        $model->increment_id = $increment_id;
        $model->price = $price;
        $model->base_price = $base_price;
        $model->currency_code = $currency_code;
        $model->order_to_base_rate = $order_to_base_rate;
        $model->customer_id = $customer_id;
        $model->customer_email = $customer['email'];
        $model->customer_bank_name = $customer_bank_name;
        $model->customer_bank = $customer_bank;
        $model->customer_bank_account = $customer_bank_account;
        $model->created_at = time();
        $model->updated_at = time();
        $model->type = $this->_currentType;
        $model->status = $this->status_payment_pending;
        if (!$model->save()) {
            Yii::$service->helper->errors->add('refund model save fail');
            
            return false;
        }
        
        return true;
    }
    
    /**
     * @param $refund_id | int
     * @param $type | string, 类型，['admin', 'bdmin']
     * @return boolean
     * 进行退款操作, 更改退款状态, 将
     */
    public function payReturnRefund($refund_id, $type='admin'){
        $refundModel = $this->getByPrimaryKey($refund_id, $type);
        if (!isset($refundModel['id']) || !$refundModel['id']) {
            Yii::$service->helper->errors->add('refund model is empty');
            
            return false;
        }
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['refunded_at'] = time();
        $updateArr['status'] = $this->status_payment_confirmed;
        $updateColumn = $refundModel->updateAll(
            $updateArr,
            [
                'and',
                ['id' => $refund_id],
                ['in', 'status', $this->refundCanPaymentConfirmedArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('refund id: {refund_id} pay fail', ['refund_id' => $refund_id]);
            
            return false;
        }
        
        $as_id = $refundModel['as_id'];
        // 退款类型
        if ($as_id && !Yii::$service->order->afterSale->refundReturnByAsId($as_id)) {
            
            return false;
        } else { // 订单取消退款
            
        }
        
        return true;
    }
    
    
    
    /**
     * @param $afterSaleModel | after sale model
     * @param $customer_bank | string, 用户的银行名称
     * @param $customer_bank_name | string, 用户银行账户对应的用户姓名
     * @param $customer_bank_account | string, 用户用户银行账户。
     * @return boolean
     * 用户订单取消，进而产生的退款，通过该方法生成退款条目。
     */
    public function customerOrderCancelCreateRefund($orderModel, $customer_bank='', $customer_bank_name='', $customer_bank_account=''){
        // 判断是否需要退款
        $order_payment_method = $orderModel['payment_method'];
        $order_status = $orderModel['order_status'];
        $order_operate_status = $orderModel['order_operate_status'];
        // 货到付款类型的订单，不需要退款
        if (Yii::$service->payment->isCashOnDeliveryMethod($order_payment_method)) {
            
            return true;
        }
        // 订单状态判断，是否需要退款，如果不需要，直接返回true
        if (!Yii::$service->order->info->isCustomerRedirectCancelNeedRefund($orderModel)) {
            
            return true;
        }
        // 进行退款处理，创建退款
        $as_id = '';
        $order_id = $orderModel['order_id'];
        $increment_id = $orderModel['increment_id'];
        $payment_method = $orderModel['payment_method'];
        $bdmin_user_id = $orderModel['bdmin_user_id'];
        $customer_id = $orderModel['customer_id'];
        $order_currency_code = $orderModel['order_currency_code'];
        $order_to_base_rate = $orderModel['order_to_base_rate'];
        $price = $orderModel['grand_total'];
        $base_price = $afterSaleModel['base_grand_total'];
        $customer = Yii::$service->customer->getByPrimaryKey($customer_id);
        if ($customer['id']) {
            $customer_bank = $customer_bank ? $customer_bank : $customer['customer_bank'];
            $customer_bank_name = $customer_bank_name ? $customer_bank_name : $customer['customer_bank_name'];
            $customer_bank_account = $customer_bank_account ? $customer_bank_account : $customer['customer_bank_account'];
        }
        $this->initOrderCancelModelAndType();
        //$this->_currentType 
        //$this->status_payment_pending
        $one = $this->_model->findOne([
            'increment_id' => $increment_id,
            'type' => $this->_currentType,
        ]);
        if ($one['increment_id']) {
            Yii::$service->helper->errors->add('order cancel: order increment id is exist in refund table');
            
            return false;
        }
        $model = new $this->_modelName();
        $model->bdmin_user_id = $bdmin_user_id;
        $model->as_id = $as_id;
        $model->increment_id = $increment_id;
        $model->price = $price;
        $model->base_price = $base_price;
        $model->currency_code = $currency_code;
        $model->order_to_base_rate = $order_to_base_rate;
        $model->customer_id = $customer_id;
        $model->customer_email = $customer['email'];
        $model->customer_bank_name = $customer_bank_name;
        $model->customer_bank = $customer_bank;
        $model->customer_bank_account = $customer_bank_account;
        $model->created_at = time();
        $model->updated_at = time();
        $model->type = $this->_currentType;
        $model->status = $this->status_payment_pending;
        if (!$model->save()) {
            Yii::$service->helper->errors->add('order cancel: refund model save fail');
            
            return false;
        }
        
        return true;
    }
    
    
    
}