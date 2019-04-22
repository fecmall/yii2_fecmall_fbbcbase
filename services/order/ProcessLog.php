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
class ProcessLog extends \fecshop\services\Service
{
    // 订单-创建
    public $order_create = 'log_order_create';
    // 订单-脚本端自动取消未支付订单
    public $order_payment_pending_console_auto_cancel = 'log_order_payment_pending_console_auto_cancel';
    // 订单-支付成功
    public $order_payment_confirm = 'log_order_payment_confirm';
    // 订单-用户取消订单（直接取消，不经过供应商审核）
    public  $order_redirect_cancel = 'log_order_redirect_cancel';
    // 订单-用户发起取消订单请求
    public  $order_request_cancel = 'log_order_request_cancel';
    // 订单-用户发起取消订单请求, 然后将这个请求撤销。
    public  $order_cancel_back = 'log_order_cancel_back';
    // 订单-用户取消订单，供应商审核通过
    public  $order_cancel_audit_accept = 'log_order_cancel_audit_accept';
    // 订单-用户取消订单，供应商审核拒绝
    public  $order_cancel_audit_refuse = 'log_order_cancel_audit_refuse';
    // 订单-用户订单，供应商审核通过
    public  $order_audit_accept = 'log_order_audit_accept';
    // 订单-用户订单，供应商审核拒绝
    public  $order_audit_refuse = 'log_order_audit_refuse';
    // 订单-用户订单，供应商发货
    public  $order_dispatch = 'log_order_dispatch';
    // 订单-用户订单，用户收货
    public  $order_receive = 'log_order_receive';
    // 订单-用户订单，用户延长收货时间
    public  $order_receive_date_delay = 'log_order_receive_date_delay';
    
    // 售后-用户退货，发起退货请求
    public  $after_sale_return_request = 'log_after_sale_return_request';
    // 售后-用户退货，退货请求 - 供应商审核通过
    public  $after_sale_return_audit_accept = 'log_after_sale_return_audit_accept';
    // 售后-用户退货，退货请求 - 供应商审核拒绝
    public  $after_sale_return_audit_refuse = 'log_after_sale_return_audit_refuse';
    // 售后-用户退货，退货请求 - 用户撤销退货请求
    public  $after_sale_return_cancel_return = 'log_after_sale_return_cancel_return';
    // 售后-用户退货，退货商品，用户发货
    public  $after_sale_return_dispatch = 'log_after_sale_return_dispatch';
    // 售后-用户退货，退货商品，供应商收货
    public  $after_sale_return_received = 'log_after_sale_return_received';
    // 售后-用户退货，退货商品，退款
    public  $after_sale_return_refund = 'log_after_sale_return_refund';
    
    public $enable = true;
    
    
    protected $_modelName = '\fbbcbase\models\mongodb\order\ProcessLog';

    protected $_model;
    protected $_allTypeArr;
    
    
    public function init()
    {
        parent::init();
        list($this->_modelName, $this->_model) = \Yii::mapGet($this->_modelName);
    }
    // 是否开启log
    public function isEnable() {
        
        return $this->enable;
    }
    // 得到所有的type
    public function getAllTypeArr()
    {
        if (!$this->_allTypeArr) {
            $arr = [
                $this->order_create => $this->order_create,
                $this->order_payment_pending_console_auto_cancel => $this->order_payment_pending_console_auto_cancel   ,
                $this->order_payment_confirm =>  $this->order_payment_confirm ,
                $this->order_redirect_cancel =>  $this->order_redirect_cancel ,
                $this->order_request_cancel => $this->order_request_cancel  ,
                $this->order_cancel_back => $this->order_cancel_back  ,
                $this->order_cancel_audit_accept => $this->order_cancel_audit_accept  ,
                $this->order_cancel_audit_refuse => $this->order_cancel_audit_refuse  ,
                $this->order_audit_accept =>  $this->order_audit_accept ,
                $this->order_audit_refuse => $this->order_audit_refuse  ,
                $this->order_dispatch =>  $this->order_dispatch ,
                $this->order_receive =>  $this->order_receive ,
                $this->order_receive_date_delay => $this->order_receive_date_delay  ,
                $this->after_sale_return_request => $this->after_sale_return_request  ,
                $this->after_sale_return_audit_accept =>  $this->after_sale_return_audit_accept ,
                $this->after_sale_return_audit_refuse => $this->after_sale_return_audit_refuse  ,
                $this->after_sale_return_cancel_return =>  $this->after_sale_return_cancel_return ,
                $this->after_sale_return_dispatch => $this->after_sale_return_dispatch  ,
                $this->after_sale_return_received => $this->after_sale_return_received  ,
                $this->after_sale_return_refund =>  $this->after_sale_return_refund ,
            ];
            foreach ($arr as $k=>$v) {
                $this->_allTypeArr[$k] = Yii::$service->page->translate->__($v);
            }
            
        }
        return $this->_allTypeArr;
    }
    /**
     * 得到order 表的id字段。
     */
    protected function actionGetPrimaryKey()
    {
        return '_id';
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
     *
     * customer 添加order log
     */
    public function customerAdd($order, $type, $remark='') {
        if (!$this->isEnable()) {
            
            return false;
        }
        if (!Yii::$app->user->isGuest) {
            $param = $this->getAddParam($order, $type, $remark);
            $identity = Yii::$app->user->identity;
            $customer_id = $identity->id;
            $param['customer_id'] = $customer_id;
            
            return $this->add($param, $type);
        }
        
        return false;
    }
    /**
     *
     * bdmin 添加order log
     */
    public function bdminAdd($order, $type, $remark='') {
        if (!$this->isEnable()) {
            
            return false;
        }
        $param = [];
        if (!Yii::$app->user->isGuest) {
            $param = $this->getAddParam($order, $type, $remark);
            $identity = Yii::$app->user->identity;
            $bdmin_user_id = $identity->id;
            $param['bdmin_user_id'] = $bdmin_user_id;
            
            return $this->add($param, $type);
        }
        
        return false;
    }
    /**
     *
     * admin 添加order log
     */
    public function adminAdd($order, $type, $remark='') {
        if (!$this->isEnable()) {
            
            return false;
        }
        if (!Yii::$app->user->isGuest) {
            $param = $this->getAddParam($order, $type, $remark);
            $identity = Yii::$app->user->identity;
            $admin_user_id = $identity->id;
            $param['admin_user_id'] = $admin_user_id;
            
            return $this->add($param, $type);
        }
        
        return false;
    }
    
    /**
     *
     * console 添加order log
     */
    public function consoleAdd($order, $type, $remark='') {
        if (!$this->isEnable()) {
            
            return false;
        }
        $param = $this->getAddParam($order, $type, $remark);
        
        return $this->add($param, $type);
    }
    
    public function getAddParam($order, $type, $remark='') {
        $param = [];
        $param['remark'] = $remark;
        $param['order_id'] = $order['order_id'];
        $param['increment_id'] = $order['increment_id'];
        $param['customer_id'] = $order['customer_id'];
        $param['bdmin_user_id'] = $order['bdmin_user_id'];
        return $param;
    }
    
    public function add($param, $type)
    {
        if (!$this->isEnable()) {
            
            return false;
        }
        $model = new $this->_modelName();
        $model->created_at = time();
        $model->updated_at = time();
        $primaryVal = new \MongoDB\BSON\ObjectId();
        $model->{$this->getPrimaryKey()} = $primaryVal;
        $model->order_id = $param['order_id'];
        $model->increment_id = $param['increment_id'];
        !$param['customer_id'] || $model->customer_id = $param['customer_id'];
        !$param['bdmin_user_id'] || $model->bdmin_user_id = $param['bdmin_user_id'];
        !$param['admin_user_id'] || $model->admin_user_id = $param['admin_user_id'];
        !$param['remark'] || $model->remark = $param['remark'];
        $model->type = $type;
        
        return $model->save();
    }
    
    /**
     * Save the customer info.
     * @param array $param
     * 数据格式如下：
     * ['email' => 'xxx', 'password' => 'xxxx','firstname' => 'xxx','lastname' => 'xxx']
     * @param type | string,  `admin` or `bdmin`
     * @return bool
     * mongodb save system config
     */
    protected function actionSave($param, $type)
    {
        
    }
      
}
