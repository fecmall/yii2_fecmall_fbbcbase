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
use Ramsey\Uuid\Uuid;

/**
 * Order services.
 *
 * @property \fecshop\services\order\Item $item
 *
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Order extends \fecshop\services\Order
{
    public $requiredAddressAttr;

    /**
     *  订单流程状态
     *
     */
    // 必填的订单字段。
    // 下面是订单支付状态
    // 等待付款状态
    public $payment_status_pending          = 'payment_pending';

    // 付款处理中，(支付处理中，因为信用卡有预售，因此需要等IPN消息来确认是否支付成功)
    public $payment_status_processing       = 'payment_processing';

    // 收款成功（支付状态已确认，代表已经收到钱了）
    public $payment_status_confirmed        = 'payment_confirmed';
    
    // 货到付款类型订单，创建成功后，用户订单创建完成后的状态（在流程方面，相当于上面的$payment_status_confirmed）
    public $payment_no_need_status_confirmed        = 'payment_no_need_confirmed';

    // 欺诈【当paypal的返回金额和网站金额不一致【以及货币类型】的情况，就会判定该状态】
    public $payment_status_suspected_fraud  = 'payment_suspected_fraud';

    // 订单支付已取消【用户进入paypal点击取消订单返回网站，或者payment_pending订单超过xx时间未支付被脚本取消，或者客服后台取消】
    public $payment_status_canceled         = 'payment_canceled';
    
    // 供应商后台审核不通过状态
    public $status_audit_fail                   = 'order_audit_fail';
    
    // 订单备货处理中，从成功收款进入erp并客服审核成功后，进入备货流程 到 发货前的状态
    public $status_processing                   = 'order_processing'; 

    // 订单已发货【订单包裹被物流公司收取后】
    public $status_dispatched                   = 'order_dispatched';
    
    // 订单已经收货 
    public $status_received                 = 'order_received';
    
    // 订单已完成，
    public $status_completed                 = 'order_completed';
    
    
    /**
     *  订单操作状态
     */
    // 订单正常操作状态 
    public $operate_status_normal   = 'operate_normal';
    
    // 订单取消待确认状态，【用户付款后，因为纠纷进行取消订单后的状态】
    public $operate_status_waiting_canceled   = 'operate_waiting_canceled';
    
    // 订单已取消，【用户付款后，因为纠纷进行取消订单后的状态】
    public $operate_status_canceled   = 'operate_canceled';
    
    // 订单审核中（订单收款成功后，进入erp，需要客服审核，才能开始发货流程，或者可能存在某些问题，被客服暂时挂起）
    public $operate_status_holded                   = 'operate_holded';
    
    
    
    // 订单号格式。
    public $increment_id = 1000000000;

    // 计算销量的订单时间范围（将最近几个月内的订单中的产品销售个数累加，作为产品的销量值,譬如3代表计算最近3个月的订单产品）
    // 0：代表计算订单表中所有的订单。
    // 这个值用于console入口（脚本端），通过shell脚本执行，计算产品的销量，将订单中产品个数累加作为产品的销量，然后将这个值更新到产品表字段中，用于产品按照销量排序或者过滤
    public $orderProductSaleInMonths = 3;

    // 将xx分钟内未支付的pending订单取消掉，并释放产品库存的设置
    public $minuteBeforeThatReturnPendingStock  = 180;

    // 每次处理未支付的pending订单的个数限制。
    public $orderCountThatReturnPendingStock    = 30;

    // 订单备注字符的最大数
    public $orderRemarkStrMaxLen = 1500;
    
    // 支付类型，目前只有standard 和 express 两种，express 指的是在购物车点击支付按钮的方式，譬如paypal的express
    // standard类型指的是填写完货运地址后生成订单跳转到第三方支付平台的支付类型。
    protected $checkout_type;

    // 当前的订单信息保存到这个变量中，订单信息是从数据库中取出来订单和产品信息，然后进行了一定的数据处理后，再保存到该变量的。
    protected $_currentOrderInfo;

    // 支付类型常量
    const CHECKOUT_TYPE_STANDARD    = 'standard';

    const CHECKOUT_TYPE_EXPRESS     = 'express';

    const CHECKOUT_TYPE_ADMIN_CREATE= 'admin_create';

    // 作为保存incrementId到session的key，把当前的order incrementId保存到session的时候，对应的key就是该常量。
    const CURRENT_ORDER_INCREAMENT_ID = 'current_order_increament_id';
    const CURRENT_ORDER_TRADE_NO = 'current_order_trade_no';
    
    protected $_orderModelName = '\fbbcbase\models\mysqldb\Order';

    protected $_orderModel;
    
    public function init()
    {
        parent::init();
        list($this->_orderModelName, $this->_orderModel) = \Yii::mapGet($this->_orderModelName);
    }
    
    /**
     * @return array
     * 将订单所有的支付类型，组合成一个数组，进行返回。
     */
    protected function actionGetCheckoutTypeArr()
    {
        return [
            self::CHECKOUT_TYPE_ADMIN_CREATE => self::CHECKOUT_TYPE_ADMIN_CREATE,
            self::CHECKOUT_TYPE_STANDARD     => self::CHECKOUT_TYPE_STANDARD,
            self::CHECKOUT_TYPE_EXPRESS      => self::CHECKOUT_TYPE_EXPRESS,
        ];
    }

    /**
     * 付款成功，而且订单付款状态正常的订单状态
     *
     */
    public function getOrderPaymentedStatusArr()
    {
        return [
            $this->payment_status_confirmed,
            $this->status_holded,
            $this->status_processing,
            $this->status_completed,
        ];
    }

    /**
     * @return array
     * 将订单所有的状态，组合成一个数组，进行返回。
     */
    protected function actionGetStatusArr()
    {
        return [
            $this->payment_status_pending           => $this->payment_status_pending,
            $this->payment_status_processing        => $this->payment_status_processing,
            $this->payment_status_confirmed         => $this->payment_status_confirmed,
            $this->payment_no_need_status_confirmed          => $this->payment_no_need_status_confirmed,
            $this->payment_status_suspected_fraud   => $this->payment_status_suspected_fraud,
            $this->payment_status_canceled                    => $this->payment_status_canceled,
            
            $this->status_audit_fail                  => $this->status_audit_fail,
            $this->status_processing                => $this->status_processing,
            $this->status_dispatched                => $this->status_dispatched,
            $this->status_received                => $this->status_received,
            
            $this->status_completed                 => $this->status_completed,
        ];
    }
    
    /**
     * @return array
     * 将订单所有的状态，组合成一个数组，进行返回。
     */
    protected function actionGetOperateStatusArr()
    {
        return [
            $this->operate_status_normal           => $this->operate_status_normal,
            $this->operate_status_waiting_canceled        => $this->operate_status_waiting_canceled,
            $this->operate_status_canceled         => $this->operate_status_canceled,
            $this->operate_status_holded            => $this->operate_status_holded,
            //$this->operate_status_return             => $this->operate_status_return,
            //$this->operate_status_waiting_exchange       => $this->operate_status_waiting_exchange,
        ];
    }
    
    public function getAllStatusArr()
    {
        $arr = [];
        $orderStatus = Yii::$service->order->getStatusArr();
        foreach ($orderStatus as $k=>$v) {
            $arr[$k] = Yii::$service->page->translate->__($v);
        }
        $orderOperateStatus = Yii::$service->order->getOperateStatusArr();
        foreach ($orderOperateStatus as $k=>$v) {
            // 去掉这个字段。
            if ($k == $this->operate_status_normal){
                continue;
            }
            $arr[$k] = Yii::$service->page->translate->__($v);
        }
        
        return $arr;
    }
    
    public function getAllCancelStatusArr()
    {
        $arr = [
            $this->operate_status_waiting_canceled        => $this->operate_status_waiting_canceled,
            $this->operate_status_canceled         => $this->operate_status_canceled,
        ];
        foreach ($arr as $k=>$v) {
            $arr[$k] = Yii::$service->page->translate->__($v);
        }
        
        return $arr;
    }
    
    /**
     * @return array
     * 将订单所有的状态，组合成一个数组，进行返回。
     */
    protected function actionGetSelectAllStatusArr()
    {
        return [
            $this->payment_status_pending           => '等待支付('.$this->payment_status_pending.')',
            $this->payment_status_processing        => '支付处理中('.$this->payment_status_processing.')',
            $this->payment_status_confirmed         => '支付成功('.$this->payment_status_confirmed.')',
            $this->payment_no_need_status_confirmed          => '订单创建（货到付款）('.$this->payment_no_need_status_confirmed.')',
            $this->payment_status_suspected_fraud   => '欺诈订单('.$this->payment_status_suspected_fraud.')',
            $this->payment_status_canceled                    => '支付取消('.$this->payment_status_canceled.')',
            $this->status_audit_fail                 => '审核不通过订单('.$this->status_audit_fail.')',
            $this->status_processing                => '备货中订单('.$this->status_processing.')',
            $this->status_dispatched                => '已发货订单('.$this->status_dispatched.')',
            $this->status_completed                 => '已完成订单('.$this->status_completed.')',
            $this->status_received  => $this->status_received,
            $this->operate_status_waiting_canceled        => '取消待确认订单('.$this->operate_status_waiting_canceled.')',
            $this->operate_status_canceled         => '已取消订单('.$this->operate_status_canceled.')',
            $this->operate_status_holded            => '挂起订单('.$this->operate_status_holded.')',
            //$this->operate_status_return             => '退货订单('.$this->operate_status_return.')',
            //$this->operate_status_waiting_exchange       => '换货('.$this->operate_status_waiting_exchange.')',
        
        ];
    }
    
    
    
    
    /**
     * @return array
     * 将订单所有的状态，组合成一个数组，进行返回。
     */
    protected function actionGetSelectOperateStatusArr()
    {
        return [];
        
    }
    
    
    /**
     * @param $checkout_type | String  ，支付类型
     * 设置支付类型，其他计算以此设置作为基础，进而获取其他的配置。
     */
    protected function actionSetCheckoutType($checkout_type)
    {
        $arr = [self::CHECKOUT_TYPE_STANDARD, self::CHECKOUT_TYPE_EXPRESS];
        if (in_array($checkout_type, $arr)) {
            $this->checkout_type = $checkout_type;

            return true;
        }

        return false;
    }

    /**
     * 得到支付类型
     */
    protected function actionGetCheckoutType()
    {
        return $this->checkout_type;
    }

    /**
     * @param $billing | Array
     * @return bool
     *              通过$this->requiredAddressAttr，检查地址的必填。
     */
    protected function actionCheckRequiredAddressAttr($billing)
    {
        //$this->requiredAddressAttr;
        if (is_array($this->requiredAddressAttr) && !empty($this->requiredAddressAttr)) {
            foreach ($this->requiredAddressAttr as $attr) {
                if (!isset($billing[$attr]) || empty($billing[$attr])) {
                    Yii::$service->helper->errors->add('{attr} can not empty', ['attr' => $attr]);

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 得到order 表的id字段。
     */
    protected function actionGetPrimaryKey()
    {
        return 'order_id';
    }

    /**
     * @param $primaryKey | Int
     * @return Object($this->_orderModel)
     * 通过主键值，返回Order Model对象
     */
    protected function actionGetByPrimaryKey($primaryKey)
    {
        $one = $this->_orderModel->findOne($primaryKey);
        $primaryKey = $this->getPrimaryKey();
        if ($one[$primaryKey]) {
            return $one;
        } else {
            return new $this->_orderModelName();
        }
    }
    
    /**
     * @param $increment_id | String , 订单号
     * @return object （$this->_orderModel），返回 $this->_orderModel model
     * 通过订单号incrementId，得到订单Model对象。
     */
    protected function actionGetByIncrementId($increment_id)
    {
        $one = $this->_orderModel->findOne(['increment_id' => $increment_id]);
        $primaryKey = $this->getPrimaryKey();
        if ($one[$primaryKey]) {
            return $one;
        } else {
            return false;
        }
    }

    /**
     * @param $reflush | boolean 是否从数据库中重新获取，如果是，则不会使用类变量中计算的值
     * 获取当前的订单信息，原理为：
     * 通过从session中取出来订单的increment_id,
     * 在通过increment_id(订单编号)取出来订单信息。
     */
    protected function actionGetCurrentOrderInfo($reflush = false)
    {
        if (!$this->_currentOrderInfo || $reflush) {
            $increment_id = Yii::$service->order->getSessionIncrementId();
            $this->_currentOrderInfo = Yii::$service->order->getOrderInfoByIncrementId($increment_id);
        }

        return $this->_currentOrderInfo;
    }
    
    protected $_currentOrderInfos;
    //  从session中取出来交易号，然后得到订单数组
    public function getCurrentOrderInfos($reflush = false)
    {
        if (!$this->_currentOrderInfos || $reflush) {
            $payNo = $this->getSessionTradeNo();
            if ($this->paymentCodeIsPaymentNo($payNo)) {
                $this->_currentOrderInfos = $this->getOrdersInfoByPayNo($payNo);
            } else {
                $this->_currentOrderInfos[] = $this->getOrderInfoByIncrementId($payNo);
            }
        }

        return $this->_currentOrderInfos;
    }
    
    // 通过传递给第三方支付传递的订单交易号，得到订单数组
    public function getOrderModelsByTradeNo($tradeNo)
    {
        $orders  = [];
        if ($this->paymentCodeIsPaymentNo($tradeNo)) {
            $orders = $this->getByPayNo($tradeNo, false);
        } else {
            $order_model = $this->getByIncrementId($tradeNo);
            if ($order_model['increment_id']) {
               $orders[] = $order_model;
           }
        }
        
        return  $orders;
    }
    
    public function getOrderGrandTotalByTradeNo($tradeNo)
    {
        $orders = $this->getOrdersByTradeNo($tradeNo);
        $grandTotal = 0;
        if (is_array($orders) || !empty($orders)) {
            foreach ($orders as $order) {
                $grandTotal += $order['grand_total'];
            }
        }
        return $grandTotal;
    }

    /**
     * @param $increment_id | String 订单编号
     * @return array
     *               通过increment_id 从数据库中取出来订单数据，
     *               然后进行一系列的处理，返回订单数组数据。
     */
    protected function actionGetOrderInfoByIncrementId($increment_id)
    {
        $one = $this->getByIncrementId($increment_id);
        if (!$one) {
            return;
        }

        $primaryKey = $this->getPrimaryKey();
        if (!isset($one[$primaryKey]) || empty($one[$primaryKey])) {
            return;
        }
        $order_info = [];
        foreach ($one as $k=>$v) {
            $order_info[$k] = $v;
        }
        $order_info['customer_address_state_name']      = Yii::$service->helper->country->getStateByContryCode($order_info['customer_address_country'], $order_info['customer_address_state']);
        $order_info['customer_address_country_name']    = Yii::$service->helper->country->getCountryNameByKey($order_info['customer_address_country']);
        $order_info['currency_symbol']                  = Yii::$service->page->currency->getSymbol($order_info['order_currency_code']);
        $order_info['products']                         = Yii::$service->order->item->getByOrderId($one[$primaryKey]);

        return $order_info;
    }
    
    /**
     * @param $increment_id | String 订单编号
     * @return array
     *               通过increment_id 从数据库中取出来订单数据，
     *               然后进行一系列的处理，返回订单数组数据。
     */
    protected function actionGetOrdersInfoByPayNo($payNo)
    {
        $orders = $this->getByPayNo($payNo);
        if (!is_array($orders) || empty($orders)) {
            return null;
        }
        $orderArr = [];
        foreach ($orders as $one) {
            $primaryKey = $this->getPrimaryKey();
            if (!isset($one[$primaryKey]) || empty($one[$primaryKey])) {
                return;
            }
            $order_info = [];
            foreach ($one as $k=>$v) {
                $order_info[$k] = $v;
            }
            $order_info['customer_address_state_name']      = Yii::$service->helper->country->getStateByContryCode($order_info['customer_address_country'], $order_info['customer_address_state']);
            $order_info['customer_address_country_name']    = Yii::$service->helper->country->getCountryNameByKey($order_info['customer_address_country']);
            $order_info['currency_symbol']                  = Yii::$service->page->currency->getSymbol($order_info['order_currency_code']);
            $order_info['products']                         = Yii::$service->order->item->getByOrderId($one[$primaryKey]);
            $orderArr[] = $order_info;
        }
        
        return $orderArr;
    }
    
    public function getByPayNo($payNo, $isArray=true)
    {
        $filter = [
            'where' => [
                ['payment_no' => $payNo],
            ],
            'fetchAll' => true,
            'asArray' => $isArray,
        ];
        $data = $this->coll($filter);
        
        return $data['coll'];
    }
    
    
    protected function actionGetorderinfocoll($filter = '')
    {
        $primaryKey = $this->getPrimaryKey();
        $query  = $this->_orderModel->find();
        $query  = Yii::$service->helper->ar->getCollByFilter($query, $filter);
        $coll   = $query->all();
        foreach ($coll as $k => $order_info) {
            $coll[$k]['customer_address_state_name']      = Yii::$service->helper->country->getStateByContryCode($order_info['customer_address_country'], $order_info['customer_address_state']);
            $coll[$k]['customer_address_country_name']    = Yii::$service->helper->country->getCountryNameByKey($order_info['customer_address_country']);
            $coll[$k]['currency_symbol']                  = Yii::$service->page->currency->getSymbol($order_info['order_currency_code']);
            $coll[$k]['products']                         = Yii::$service->order->item->getByOrderId($order_info[$primaryKey]);
        }
        return [
            'coll' => $coll,
            'count'=> $query->limit(null)->offset(null)->count(),
        ];
    }
    
    /**
     * @param $order_id | Int
     * @return array
     *               通过order_id 从数据库中取出来订单数据，
     *               然后进行一系列的处理，返回订单数组数据。
     */
    protected function actionGetOrderInfoById($order_id)
    {
        if (!$order_id) {
            return;
        }
        $one = $this->_orderModel->findOne($order_id);
        $primaryKey = $this->getPrimaryKey();
        if (!isset($one[$primaryKey]) || empty($one[$primaryKey])) {
            return;
        }
        $order_info = [];
        foreach ($one as $k=>$v) {
            $order_info[$k] = $v;
        }
        $order_info['customer_address_state_name']  = Yii::$service->helper->country->getStateByContryCode($order_info['customer_address_country'], $order_info['customer_address_state']);
        $order_info['customer_address_country_name']= Yii::$service->helper->country->getCountryNameByKey($order_info['customer_address_country']);
        $order_info['currency_symbol']              = Yii::$service->page->currency->getSymbol($order_info['order_currency_code']);
        $order_info['products']                     = Yii::$service->order->item->getByOrderId($order_id);

        return $order_info;
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
        $query  = $this->_orderModel->find();
        $query  = Yii::$service->helper->ar->getCollByFilter($query, $filter);
        $coll   = $query->all();
        
        return [
            'coll' => $coll,
            'count'=> $query->limit(null)->offset(null)->count(),
        ];
    }

    /**
     * @param $one|array , save one data .
     * @return int 保存order成功后，返回保存的id。
     */
    protected function actionSave($one)
    {
        $time = time();
        $primaryKey = $this->getPrimaryKey();
        $primaryVal = isset($one[$primaryKey]) ? $one[$primaryKey] : '';
        if ($primaryVal) {
            $model = $this->_orderModel->findOne($primaryVal);
            if (!$model) {
                Yii::$service->helper->errors->add('order {primaryKey} is not exist', ['primaryKey' => $this->getPrimaryKey()]);

                return;
            }
        } else {
            $model = new $this->_orderModelName();
            $model->created_at = time();
        }
        $model->updated_at = time();
        $model = Yii::$service->helper->ar->save($model, $one);
        $primaryVal = $model[$this->getPrimaryKey()];
        
        return $primaryVal;
    }

    /**
     * @param $ids | Int or Array
     * @return bool
     *              如果传入的是id数组，则删除多个
     *              如果传入的是Int，则删除一个
     */
    protected function actionRemove($ids)
    {
        if (!$ids) {
            Yii::$service->helper->errors->add('remove id is empty');

            return false;
        }
        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $id) {
                $model = $this->_orderModel->findOne($id);
                if (isset($model[$this->getPrimaryKey()]) && !empty($model[$this->getPrimaryKey()])) {
                    $model->delete();
                } else {
                    Yii::$service->helper->errors->add('Order Remove Errors:ID {id} is not exist', ['id' => $id]);

                    return false;
                }
            }
        } else {
            $id = $ids;
            $model = $this->_orderModel->findOne($id);
            if (isset($model[$this->getPrimaryKey()]) && !empty($model[$this->getPrimaryKey()])) {
                $model->delete();
            } else {
                Yii::$service->helper->errors->add('Coupon Remove Errors:ID:{id} is not exist.', ['id' => $id]);

                return false;
            }
        }

        return true;
    }

    /**
     * @param $increment_id | String , 订单号
     * @return object （$this->_orderModel），返回 $this->_orderModel model
     *                通过订单号，得到订单以及订单产品信息。
     */
    protected function actionGetInfoByIncrementId($increment_id)
    {
        $order      = $this->getByIncrementId($increment_id);
        $orderInfo  = [];
        if ($order) {
            $primaryKey = $this->getPrimaryKey();
            $order_id   = $order[$primaryKey];
            $items      = Yii::$service->order->item->getByOrderId($order_id);
            foreach ($order as $k=>$v) {
                $orderInfo[$k] = $v;
            }
            $orderInfo['items'] = $items;

            return $orderInfo;
        } else {
            return;
        }
    }

    /**
     * @param $token | String  , paypal 支付获取的token，订单生成后只有三个字段
     *       order_id, increment_id , payment_token ，目的就是将token对应到一个increment_id
     *       在paypal 点击continue的时候，可以通过token找到对应的订单。
     */
    protected function actionGeneratePPExpressOrder($token)
    {
        $myOrder = new $this->_orderModelName();
        $myOrder->setGenerateOrderByPaypalToken(true);
        $myOrder->payment_token = $token;
        $myOrder->save();
        $order_id = $myOrder['order_id'];
        if ($order_id) {
            $increment_id = $this->generateIncrementIdByOrderId($order_id);
            $myOrder['increment_id'] = $increment_id;
            $myOrder->save();
            $this->setSessionIncrementId($increment_id);
            // add log
            $logType = Yii::$service->order->processLog->order_create;
            Yii::$service->order->processLog->consoleAdd($myOrder, $logType);
            
            return true;
        } else {
            Yii::$service->helper->errors->add('generate order fail');
            
            return false;
        }
    }

    /**
     * @param $token | String  , paypal 支付获取的token，
     *   通过token 得到订单 Object
     */
    protected function actionGetByPaymentToken($token)
    {
        $one = $this->_orderModel->find()->where(['payment_token' => $token])
            ->one();
        if (isset($one['order_id']) && $one['order_id']) {
            return $one;
        } else {
            return '';
        }
    }
    /**
     * @param $increment_id | string 订单编号
     * @param $payment_method | string 支付方式
     * @param $paramVerifyed | boolean 支付方式的合法性是否验证，如果验证了，则此处不需要做合法性验证
     * @return boolean 更新订单成功后，返回true
     * 作用：在用户下单流程中，用户提交订单，跳转到订单支付页面，
     * 选择支付方式，进行支付的操作，服务端进行更新订单的支付方式的操作。
     */
    protected function actionUpdatePaymentMethod($increment_id, $payment_method, $paramIsVerifyed = false)
    {
        
        // 验证payment method的合法性
        if (!$paramIsVerifyed && !Yii::$service->payment->ifIsCorrectStandard($payment_method)) {
            
            Yii::$service->helper->errors->add('order payment method is invalid');
            
            return false;
        }
        
        
        $orderArr = [];
        // 得到订单编号
        if ($increment_id) {
            // 如果存在传递过来的订单编号，那么进行session service保存，方便后面进行支付。
            //$this->setSessionIncrementId($increment_id);
            $this->setSessionTradeNo($increment_id);
            // 得到订单
            $order = $this->getByIncrementId($increment_id);
            if (!isset($order['increment_id']) || !$order['increment_id']) {
                Yii::$service->helper->errors->add('order increment id: {increment_id} is not exist', ['increment_id' => $order['increment_id']]);
                
                return false;
            }
            $orderArr[] = $order;
        } else {
            $payment_no = $this->getSessionTradeNo();
            $orderArr = $this->getByPayNo($payment_no, false);
        }
        
        if (!is_array($orderArr) || empty($orderArr)) {
            Yii::$service->helper->errors->add('current payment order is empty');
                
            return false;
        }
        
        // 批量更改订单的支付方式
        foreach ($orderArr as $order) {
            // 判断订单是否属于当前的用户
            if (!Yii::$app->user->isGuest) {
                $identity = Yii::$app->user->identity;
                if ($identity['id'] != $order['customer_id']) {
                    Yii::$service->helper->errors->add('you do not have role to update this order');
                
                    return false;
                }
            }
            // 查看订单状态 , 是否满足支付条件？   
            if ($order->order_status != $this->payment_status_pending ||  $order->order_operate_status != $this->operate_status_normal ) {
                Yii::$service->helper->errors->add('order can not update payment method');
                
                return false;
            }
            
            // 更新订单状态
            $order->payment_method = $payment_method;
            $order->save();
            $saveOrderStatus = $order->save();
            if (!$saveOrderStatus) {
                Yii::$service->helper->errors->add('update order payment(model save) fail');
                
                return false;
            }
        }
        
        
        return true;
    }
    
    /**
     * @param $reflush | boolean 是否从数据库中重新获取，如果是，则不会使用类变量中计算的值
     * 通过从session中取出来订单的increment_id
     * 在通过increment_id(订单编号)取出来订单信息。
     */
    protected function actionGetInfoByPaymentToken($token)
    {
        $orderModel = $this->getByPaymentToken($token);
        $increment_id = isset($orderModel['increment_id']) ? $orderModel['increment_id'] : '';
        return Yii::$service->order->getOrderInfoByIncrementId($increment_id);
    }
    
    public function getOrdersByByPaymentToken($token)
    {
        $coll = $this->_orderModel->find()->where(['payment_token' => $token])
            ->all();
        if (!is_array($coll) || empty($coll)) {
            return null;
        }
        
        return $coll ;
    }
    
    public function getInfosByPaymentToken($token)
    {
        $coll = $this->getOrdersByByPaymentToken($token);
        $orderInfoArr = [];
        foreach ($coll as $orderModel) {
            $increment_id = isset($orderModel['increment_id']) ? $orderModel['increment_id'] : '';
            $orderInfoArr[] = Yii::$service->order->getOrderInfoByIncrementId($increment_id);
        }
        
        return $orderInfoArr;
    }

    /**
     * @param $address | Array 货运地址
     * @param $shipping_method | String 货运快递方式
     * @param $payment_method | Array 支付方式、['bdmin_user_id'  => 'shipping_methods']
     * @param $clearCartAndDeductStock | boolean 是否清空购物车，并扣除库存，这种情况是先 生成订单，在支付的情况下失败的处理方式。
     * @param $token | string 代表 通过payment_token得到order，然后更新order信息的方式生成order，这个是paypal购物车express支付对应的功能
     * @param $order_remark | string , 订单备注
     * @return bool 通过购物车的数据生成订单是否成功
     *              通过购物车中的产品信息，以及传递的货运地址，货运快递方式，支付方式生成订单。
     */
    protected function actionGenerateOrderByCart($address, $shipping_methods, $payment_methods, $clearCart = true, $token = '', $order_remark = '')
    {
        
        $cart = Yii::$service->cart->quote->getCurrentCart();
        if (!$cart) {
            Yii::$service->helper->errors->add('current cart is empty');
        }
        $currency_info  = Yii::$service->page->currency->getCurrencyInfo();
        $currency_code  = $currency_info['code'];
        $currency_rate  = $currency_info['rate'];
        $country        = $address['country'];
        $state          = $address['state'];
        $cartInfo       = Yii::$service->cart->getCartOrderInfo($shipping_methods);
        // 检查cartInfo中是否存在产品
        if (!is_array($cartInfo['cart_info']) && empty($cartInfo['cart_info'])) {
            Yii::$service->helper->errors->add('current cart product is empty');

            return false;
        }
        
        // 扣除库存。（订单生成后，库存产品库存。）
        // 备注）需要另起一个脚本，用来处理半个小时后，还没有支付的订单，将订单取消，然后将订单里面的产品库存返还。
        // 如果是无限库存（没有库存就去采购的方式），那么不需要跑这个脚本，将库存设置的非常大即可。
        foreach($cartInfo['cart_info'] as $bdmin_user_id => $bdmin_cart_info) {
            //var_dump($bdmin_cart_info);exit;
            $deductStatus = Yii::$service->product->stock->deduct($bdmin_cart_info['products']);
            if (!$deductStatus) {
                // 库存不足则返回
                return false;
            }
        }
        
        /*
        $beforeEventName = 'event_generate_order_before';
        $afterEventName  = 'event_generate_order_after';
        Yii::$service->event->trigger($beforeEventName, $cartInfo);
        
        if ($token) {
            // 有token 代表前面已经生成了order，直接通过token查询出来即可。
            $myOrder = $this->getByPaymentToken($token);
            if (!$myOrder) {
                Yii::$service->helper->errors->add('order increment id is not exist.');
                return false;
            } else {
                $increment_id = $myOrder['increment_id'];
            }
        } else {
            $myOrder = new $this->_orderModelName();
        }
        */
        
        $payment_no = $this->generatePaymentNo();
        
        foreach($cartInfo['cart_info'] as $bdmin_user_id => $bdmin_cart_info) {
            
            $shipping_method = isset($shipping_methods[$bdmin_user_id]) ? $shipping_methods[$bdmin_user_id] : '';
            $myOrder = new $this->_orderModelName();
            $myOrder['payment_no'] = $payment_no;
            $myOrder['bdmin_user_id'] = $bdmin_user_id;
            $myOrder['order_status'] = $this->payment_status_pending;
            $myOrder['order_operate_status'] = $this->operate_status_normal;
            $currentStore = Yii::$service->store->currentStore;
            $currentStore || $currentStore       = $bdmin_cart_info['store'];
            $myOrder['store']                          = $currentStore;
            $myOrder['created_at']                  = time();
            $myOrder['updated_at']                 = time();
            $myOrder['items_count']                = $bdmin_cart_info['product_qty_total'];
            $myOrder['total_weight']                = $bdmin_cart_info['product_weight'];
            $myOrder['order_currency_code']    = $currency_code;
            $myOrder['order_to_base_rate']      = $currency_rate;
            $myOrder['grand_total']                  = $bdmin_cart_info['grand_total'];
            $myOrder['base_grand_total']         = $bdmin_cart_info['base_grand_total'];
            $myOrder['subtotal']                      = $bdmin_cart_info['product_total'];
            $myOrder['base_subtotal']              = $bdmin_cart_info['base_product_total'];
            $myOrder['subtotal_with_discount'] = $bdmin_cart_info['coupon_cost'];
            $myOrder['base_subtotal_with_discount'] = $bdmin_cart_info['base_coupon_cost'];
            $myOrder['shipping_total']             = $bdmin_cart_info['shipping_cost'];
            $myOrder['base_shipping_total']     = $bdmin_cart_info['base_shipping_cost'];
            $myOrder['checkout_method']        = $this->getCheckoutType();
            /*
            !$order_remark || $myOrder['order_remark'] = \yii\helpers\Html::encode($order_remark);
            if ($address['customer_id']) {
                $is_guest = 2;
            } else {
                $is_guest = 1;
            }
            */
            if (!Yii::$app->user->isGuest) {
                $identity = Yii::$app->user->identity;
                $customer_id = $identity->id;
            } else {
                $customer_id = '';
            }
            
            $myOrder['customer_id']             = $customer_id;
            $myOrder['customer_email']          = $address['email'];
            $myOrder['customer_firstname']      = $address['first_name'];
            $myOrder['customer_lastname']       = $address['last_name'];
            $myOrder['customer_is_guest']       = 1;
            $myOrder['customer_telephone']      = $address['telephone'];
            $myOrder['customer_address_country']= $address['country'];
            $myOrder['customer_address_state']  = $address['state'];
            $myOrder['customer_address_city']   = $address['city'];
            $myOrder['customer_address_zip']    = $address['zip'];
            $myOrder['customer_address_street1']= $address['street1'];
            $myOrder['customer_address_street2']= $address['street2'];
            $myOrder['coupon_code']             = $bdmin_cart_info['coupon_code'];
            $myOrder['payment_method']          = $payment_method;
            $myOrder['shipping_method']         = $shipping_method;
            // 进行model验证。
            
            if (!$myOrder->validate()) {
                
                $errors = $myOrder->errors;
                Yii::$service->helper->errors->addByModelErrors($errors);
                
                return false;
            }
            // 保存订单
            $saveOrderStatus = $myOrder->save();
            
            if (!$saveOrderStatus) {
                return false;
            }
            
            $order_id = $myOrder['order_id'];
            $increment_id = $this->generateIncrementIdByOrderId($order_id);
            $myOrder['increment_id'] = $increment_id;
            // 保存订单
            $saveOrderStatus = $myOrder->save();
            if (!$saveOrderStatus) {
                return false;
            }
            
            Yii::$service->event->trigger($afterEventName, $myOrder);
            if (!$myOrder[$this->getPrimaryKey()]) {
                Yii::$service->helper->errors->add('generate order fail');

                return false;
            }
            
            // 保存订单产品
            $saveItemStatus = Yii::$service->order->item->saveOrderItems2($bdmin_cart_info['products'], $order_id, $bdmin_cart_info['store'], $bdmin_user_id);
            
            if (!$saveItemStatus) {
                return false;
            }
            // 订单生成成功，通过api传递数据给trace系统
            $this->sendTracePaymentPendingOrder($myOrder, $bdmin_cart_info['products']);
            
            
            // 执行成功，则在session中设置increment_id
            //$this->setSessionIncrementId($increment_id);
            // add log
            
            $logType = Yii::$service->order->processLog->order_create;
            Yii::$service->order->processLog->consoleAdd($myOrder, $logType);
        }
        
        $this->setSessionTradeNo($payment_no);
        // 如果是登录用户，那么，在生成订单后，需要清空购物车中的产品和coupon。
        if (!Yii::$app->user->isGuest && $clearCart) {
            //Yii::$service->cart->clearCartProductAndCoupon();
        }
        return true;
    }
    // 生成支付编号
    function generatePaymentNo()
    {
        $uuid1 = Uuid::uuid1();
        $uuidStr = $uuid1->toString();
        return 'pay_no_' . $uuid1;
    }
    // 判断session存储的是order increment id 还是 order payment no
    function paymentCodeIsPaymentNo($pay_no)
    {
        if (substr($pay_no, 0, 7) == 'pay_no_' ) {
            return true;
        }
        return false;
    }
    
    public function updatePendingOrderTotal($orderModel, $product_total, $base_product_total){
        $shipping_total = $orderModel['shipping_total'];
        $base_shipping_total = $orderModel['base_shipping_total'];
        $subtotal_with_discount = $orderModel['subtotal_with_discount'];
        $base_subtotal_with_discount = $orderModel['base_subtotal_with_discount'];
        $coupon_code = $orderModel['coupon_code'];
        
        list($currDiscountCost, $baseDiscountCost) = Yii::$service->cart->coupon->getComputeCouponPrice($coupon_code, $base_product_total);
        
        $curr_grand_total = $product_total + $shipping_total - $currDiscountCost;
        $base_grand_total = $base_product_total + $base_shipping_total - $baseDiscountCost;
        // update Order Total
        $updateArr = [];
        $updateArr['updated_at'] = time();
        $updateArr['subtotal'] = $product_total;
        $updateArr['base_subtotal'] = $base_product_total;
        $updateArr['subtotal_with_discount'] = $currDiscountCost;
        $updateArr['base_subtotal_with_discount'] = $baseDiscountCost;
        $updateArr['grand_total'] = $curr_grand_total;
        $updateArr['base_grand_total'] = $base_grand_total;
        
        $updateColumn = $orderModel->updateAll(
            $updateArr,
            [
                'and',
                ['order_id' => $orderModel['order_id']],
                ['in', 'order_status', Yii::$service->order->info->orderStatusPaymentPendingArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusPaymentPendingArr],
            ]
        );
        if (empty($updateColumn)) {
            Yii::$service->helper->errors->add('order increment id: {increment_id} , update pending payment order fail ', ['increment_id' => $orderModel['increment_id']]);
            
            return false;
        }
        
        return true;
    }

    /**
     * @param $order_increment_id | string，订单编号 increment_id
     * 订单支付成功后，执行的代码，该代码只会在接收到支付成功信息后，才会执行。
     * 在调用该函数前，会对IPN支付成功消息做验证，一次，无论发送多少次ipn消息，该函数只会执行一次。
     * 您可以把订单支付成功需要做的事情都在这个函数里面完成。
     **/
    public function orderPaymentCompleteEvent($order_increment_id)
    {
        if (!$order_increment_id) {
            Yii::$service->helper->errors->add('order increment id is empty');
            return false;
        }
        $orderInfo = Yii::$service->order->getOrderInfoByIncrementId($order_increment_id);
        if (!$orderInfo['increment_id']) {
            Yii::$service->helper->errors->add('get order by increment_id: {increment_id} fail, order is not exist ', ['increment_id' => $order_increment_id]);
            return false;
        }
        // 追踪信息
        Yii::$service->order->sendTracePaymentSuccessOrder($orderInfo);
        // 发送订单支付成功邮件
        Yii::$service->email->order->sendCreateEmail($orderInfo);
    }
    
    /**
     * @param $orderInfo | Object, 订单对象
     * @param $cartInfo | Object，购物车对象
     * 根据传递的参数，得出trace系统的要求的order参数格式数组
     * 执行page trace services，将支付完成订单的数据传递给trace系统
     */
    protected function sendTracePaymentSuccessOrder($orderInfo)
    {
        \Yii::info('sendTracePaymentSuccessOrder', 'fecshop_debug');
        if (Yii::$service->page->trace->traceJsEnable) {
            $arr = [];
            $arr['invoice']             = (string)$orderInfo['increment_id'];
            $arr['order_type']          = $orderInfo['checkout_method'];
            $arr['payment_status']      = $orderInfo['order_status'];
            $arr['order_operate_status']      = $orderInfo['order_operate_status'];
            $arr['payment_type']        = $orderInfo['payment_method'];
            $arr['amount']              = (float)$orderInfo['base_grand_total'];
            $arr['shipping']            = (float)$orderInfo['base_shipping_total'];
            $arr['discount_amount']     = (float)$orderInfo['base_subtotal_with_discount'];
            $arr['coupon']              = $orderInfo['coupon_code'];
            $arr['city']                = $orderInfo['customer_address_city'];
            $arr['email']               = $orderInfo['customer_email'];
            $arr['first_name']          = $orderInfo['customer_firstname'];
            $arr['last_name']           = $orderInfo['customer_lastname'];
            $arr['zip']                 = $orderInfo['customer_address_zip'];
            $arr['address1']            = $orderInfo['customer_address_street1'];
            $arr['address2']            = $orderInfo['customer_address_street2'];
            $arr['created_at']          = $orderInfo['created_at'];
            $arr['country_code']        = $orderInfo['customer_address_country'];
            $arr['state_code']          = $orderInfo['customer_address_state'];
            $arr['state_name']   = Yii::$service->helper->country->getStateByContryCode($orderInfo['customer_address_country'], $orderInfo['customer_address_state']);
            $arr['country_name'] = Yii::$service->helper->country->getCountryNameByKey($orderInfo['customer_address_country']);
            
            $product_arr = [];
            $products = $orderInfo['products'];
            if (is_array($products)) {
                foreach ($products as $product) {
                    $product_arr[] = [
                        'sku'   => $product['sku'],
                        'name'  => $product['name'],
                        'qty'   => (int)$product['qty'],
                        'price' => (float)$product['base_product_price'],
                    ];
                }
            }
            $arr['products'] =  $product_arr;
            \Yii::info('sendTracePaymentSuccessOrderByApi', 'fecshop_debug');
            Yii::$service->page->trace->sendTracePaymentSuccessOrderByApi($arr);
            
            return true;
        }
        return false;
    }

    /**
     * @param $myOrder | Object, 订单对象
     * @param $products | Array，购物车产品数组
     * 根据传递的参数，得出trace系统的要求的order参数格式数组，
     * 执行page trace services，将等待支付订单（刚刚生成的订单）的数据传递给trace系统
     */
    protected function sendTracePaymentPendingOrder($myOrder, $products)
    {
        if (Yii::$service->page->trace->traceJsEnable) {
            $arr = [];
            $arr['invoice']             = (string)$myOrder['increment_id'];
            $arr['order_type']          = $myOrder['checkout_method'];
            $arr['payment_status']      = $myOrder['order_status'];
            $arr['order_operate_status']      = $myOrder['order_operate_status'];
            $arr['payment_type']        = $myOrder['payment_method'];
            $arr['amount']              = (float)$myOrder['base_grand_total'];
            $arr['shipping']            = (float)$myOrder['base_shipping_total'];
            $arr['discount_amount']     = (float)$myOrder['base_subtotal_with_discount'];
            $arr['coupon']              = $myOrder['coupon_code'];
            $arr['city']                = $myOrder['customer_address_city'];
            $arr['created_at']          = $myOrder['created_at'];
            $arr['email']               = $myOrder['customer_email'];
            $arr['first_name']          = $myOrder['customer_firstname'];
            $arr['last_name']           = $myOrder['customer_lastname'];
            $arr['zip']                 = $myOrder['customer_address_zip'];
            
            $arr['address1']            = $myOrder['customer_address_street1'];
            $arr['address2']            = $myOrder['customer_address_street2'];
            
            $arr['country_code']        = $myOrder['customer_address_country'];
            $arr['state_code']          = $myOrder['customer_address_state'];
            $arr['state_name']   = Yii::$service->helper->country->getStateByContryCode($myOrder['customer_address_country'], $myOrder['customer_address_state']);
            $arr['country_name'] = Yii::$service->helper->country->getCountryNameByKey($myOrder['customer_address_country']);
            
            $product_arr = [];
            // $products = $cartInfo['products'];
            if (is_array($products)) {
                foreach ($products as $product) {
                    $product_arr[] = [
                        'sku'   => $product['sku'],
                        'name'  => $product['name'],
                        'qty'   => (int)$product['qty'],
                        'price' => (float)$product['base_product_price'],
                    ];
                }
            }
            $arr['products'] =  $product_arr;
            Yii::$service->page->trace->sendTracePaymentPendingOrderByApi($arr);
            
            return true;
        }
        return false;
    }
    
    /**
     * @param $increment_id | String 每执行一次，version都会+1 （version默认为0）
     * 执行完，查看version是否为1，如果不为1，则说明已经执行过了，返回false
     */
    public function checkOrderVersion($increment_id)
    {
        // 更新订单版本号，防止被多次执行。
        $sql    = 'update '.$this->_orderModel->tableName().' set version = version + 1  where increment_id = :increment_id';
        $data   = [
            'increment_id'  => $increment_id,
        ];
        $result     = $this->_orderModel->getDb()->createCommand($sql, $data)->execute();
        $myOrder    = $this->_orderModel->find()->where([
            'increment_id'  => $increment_id,
        ])->one();
        // 如果版本号不等于1，则回滚
        if ($myOrder['version'] > 1) {
            Yii::$service->helper->errors->add('Your order has been paid');
            return false;
        } elseif ($myOrder['version'] < 1) {
            Yii::$service->helper->errors->add('Your order is error');
            return false;
        } else {
            return true;
        }
    }

    /** 废弃
     * @param $increment_id | String ,order订单号
     * 将生成的订单号写入session
     */
    /*
    protected function actionSetSessionIncrementId($increment_id)
    {
        Yii::$service->session->set(self::CURRENT_ORDER_INCREAMENT_ID, $increment_id);
    }
    */

    /** 废弃
     * 从session中取出来订单号.
     */
    /*
    protected function actionGetSessionIncrementId()
    {
        return Yii::$service->session->get(self::CURRENT_ORDER_INCREAMENT_ID);
    }
    */
    
    /**
     * 从session中销毁订单号.
     */
    /*
    protected function actionRemoveSessionIncrementId()
    {
        return Yii::$service->session->remove(self::CURRENT_ORDER_INCREAMENT_ID);
    }
    */
    
    /**
     * @param $increment_id | String ,order订单号
     *  订单交易号，里面可以是订单号，也可能是支付号
     */
    protected function actionSetSessionTradeNo($trade_no)
    {
        Yii::$service->session->set(self::CURRENT_ORDER_TRADE_NO, $trade_no);
    }

    /**
     * 从session中取出来订单交易号.
     */
    protected function actionGetSessionTradeNo()
    {
        return Yii::$service->session->get(self::CURRENT_ORDER_TRADE_NO);
    }
    
     /**
     * 从session中销毁订单号.
     */
    protected function actionRemoveSessionTradeNo()
    {
        return Yii::$service->session->remove(self::CURRENT_ORDER_TRADE_NO);
    }

    /**
     * @param $increment_id | String 订单号
     * @param $token | String ，通过api支付的token
     * 通过订单号，更新订单的支付token
     */
    protected function actionUpdateTokenByIncrementId($increment_id, $token)
    {
        $myOrder = Yii::$service->order->getByIncrementId($increment_id);
        if ($myOrder) {
            $myOrder->payment_token = $token;
            $myOrder->save();
        }
    }
    
    // 通过trade_no ，更新订单 token
    public function updateTokenByTradeNo($trade_no,$token)
    {
        $orderModels = $this->getOrderModelsByTradeNo($trade_no);
        if (!is_array($orderModels) || empty($orderModels)) {
            return false;
        }
        foreach ($orderModels as $orderModel) {
            $orderModel->payment_token = $token;
            $orderModel->save();
        }
        
        return true;
    }
    

    

    /**
     * @param int $order_id the order id
     * @return int $increment_id
     * 通过 order_id 生成订单号。
     */
    protected function generateIncrementIdByOrderId($order_id)
    {
        $increment_id = (int) $this->increment_id + (int) $order_id;

        return $increment_id;
    }

    /**
     * get order list by customer account id.
     * @param int $customer_id
     * @deprecated
     */
    protected function actionGetCustomerOrderList($customer_id)
    {
    }

    /**
     * @param int $order_id the order id
     * 订单支付成功后，更改订单的状态为支付成功状态。
     * @deprecated
     */
    protected function actionOrderPaySuccess($order_id)
    {
    }

    

    /** 
     * 将xx时间内未支付的订单取消掉，并释放产品库存。
     * 这个是后台脚本执行的函数。
     */
    protected function actionReturnPendingStock()
    {
        $logMessage = [];
        $minute     = $this->minuteBeforeThatReturnPendingStock;
        $begin_time = strtotime(date('Y-m-d H:i:s'). ' -'.$minute.' minutes ');

        $where = [
            ['<', 'updated_at', $begin_time],
            ['in', 'order_status', Yii::$service->order->info->orderStatusWaintingPaymentOrderArr],
            ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusWaintingPaymentOrderArr],
        ];
        $logMessage[] = 'order_updated_at: '.$begin_time;
        
        $filter = [
            'select' => ['increment_id', 'order_id'],
            'where'         => $where,
            'numPerPage'    => $this->orderCountThatReturnPendingStock,
            'pageNum'       => 1,
            'asArray'       => true,
        ];

        $data   = $this->coll($filter);
        $coll   = $data['coll'];
        $count  = $data['count'];
        $logMessage[] = 'order count: '.$count;
        if ($count > 0) {
            foreach ($coll as $one) {
                /**
                 * service严格上是不允许使用事务的，该方法特殊，是命令行执行的操作。
                 * 每一个循环是一个事务。
                 */
                $innerTransaction = Yii::$app->db->beginTransaction();
                try {
                    $logMessage[] = 'cancel order[begin] increment_id: '.$one['increment_id'];
                    if (!Yii::$service->order->process->consoleScriptCancelPaymentPendingOrder($one['increment_id'])) {
                        
                        throw new \Exception('cancel payment pending order fail');
                    }
                    $innerTransaction->commit();
                    $logMessage[] = 'cancel order[end] increment_id: '.$one['increment_id'];
                    
                } catch (\Exception $e) {
                    $innerTransaction->rollBack();
                    $logMessage[] = 'cancel order[fail] increment_id: '.$one['increment_id'];
                    $logMessage[] = Yii::$service->helper->errors->get();
                }
            }
        }
        return $logMessage;
    }

    /**
     * @param $days | Int 天数
     * 得到最近1个月的订单数据，包括：日期，订单支付状态，订单金额
     * 下面的数据是为了后台的订单统计
     */
    public function getPreMonthOrder($days)
    {
        // 得到一个月前的时间戳
        $preMonthTime = strtotime("-$days days");
        $filter = [
            'select' => ['created_at', 'increment_id', 'order_status', 'base_grand_total' ],
            'numPerPage' 	=> 10000000,
            'pageNum'		=> 1,
            'where'			=> [
                ['>=', 'created_at', $preMonthTime]
            ],
            'asArray' => true,
        ];
        $orderPaymentStatusArr = $this->getOrderPaymentedStatusArr();
        $data = $this->coll($filter);
        $coll = $data['coll'];
        $dateArr = Yii::$service->helper->format->getPreDayDateArr($days);
        $orderAmountArr = $dateArr;
        $paymentOrderAmountArr = $dateArr;
        $orderCountArr = $dateArr;
        $paymentOrderCountArr = $dateArr;
        
        if (is_array($coll) && !empty($coll)) {
            foreach ($coll as $order) {
                $created_at = $order['created_at'];
                $created_at_str = date("Y-m-d", $created_at);
                $order_status = $order['order_status'];
                $base_grand_total = $order['base_grand_total'];
                if (isset($orderAmountArr[$created_at_str])) {
                    $orderAmountArr[$created_at_str] += $base_grand_total;
                    $orderCountArr[$created_at_str] += 1;
                    if (in_array($order_status, $orderPaymentStatusArr)) {
                        $paymentOrderAmountArr[$created_at_str] += $base_grand_total;
                        $paymentOrderCountArr[$created_at_str] += 1;
                    }
                }
            }
        }
        
        return [
            [
                Yii::$service->page->translate->__('Order Total') => $orderAmountArr,
                Yii::$service->page->translate->__('Payment Order Total') => $paymentOrderAmountArr,
            ],
            [
                Yii::$service->page->translate->__('Order Count') => $orderCountArr,
                Yii::$service->page->translate->__('Payment Order Count') => $paymentOrderCountArr,
            ],
        ];
    }
}
