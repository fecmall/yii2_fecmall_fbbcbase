<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appserver\modules\Payment\controllers;

use fecshop\app\appserver\modules\Payment\PaymentController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class CheckmoneyController extends \fecshop\app\appserver\modules\Payment\controllers\CheckmoneyController
{
    public $enableCsrfValidation = false;
    /**
     * 支付开始页面.
     */
    public function actionStart()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $checkOrder = $this->checkOrder();
        if($checkOrder !== true){
            return $checkOrder;
        }
        
        $payment_method = isset($this->_order_model['payment_method']) ? $this->_order_model['payment_method'] : '';
        if ($payment_method) {
            $updateArr = [];
            $updateArr['updated_at'] = time();
            // 更改订单的状态、
            $orderstatus = Yii::$service->order->payment_no_need_status_confirmed;
            $updateArr['order_status'] = $orderstatus;
            $updateColumn = $this->_order_model->updateAll(
                $updateArr,
                [
                    'and',
                    ['order_id' => $this->_order_model['order_id']],
                    ['order_status' => Yii::$service->order->payment_status_pending],
                    ['order_operate_status' => Yii::$service->order->operate_status_normal],
                ]
            );
            
            // 清空购物车
            Yii::$service->cart->clearCartProductAndCoupon();
            $code = Yii::$service->helper->appserver->status_success;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
    }

    
}
