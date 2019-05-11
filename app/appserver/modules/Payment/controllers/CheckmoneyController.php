<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appserver\modules\Payment\controllers;

use fbbcbase\app\appserver\modules\Payment\PaymentController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class CheckmoneyController extends PaymentController
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
        
        foreach ($this->_order_model_arr as $order_model) {
            $payment_method = isset($order_model['payment_method']) ? $order_model['payment_method'] : '';
            if ($payment_method) {
                $updateArr = [];
                $updateArr['updated_at'] = time();
                // 更改订单的状态、
                $orderstatus = Yii::$service->order->payment_no_need_status_confirmed;
                $updateArr['order_status'] = $orderstatus;
                $updateColumn = $order_model->updateAll(
                    $updateArr,
                    [
                        'and',
                        ['order_id' => $order_model['order_id']],
                        ['order_status' => Yii::$service->order->payment_status_pending],
                        ['order_operate_status' => Yii::$service->order->operate_status_normal],
                    ]
                );
                
            }
        }
        // 清空购物车
        Yii::$service->cart->clearCartProductAndCoupon();
        $code = Yii::$service->helper->appserver->status_success;
        $data = [];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
        
        return $responseData;
    }

    
}
