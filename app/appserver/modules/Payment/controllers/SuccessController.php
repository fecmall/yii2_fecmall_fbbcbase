<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appserver\modules\Payment\controllers;

use fecshop\app\appserver\modules\AppserverController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class SuccessController extends AppserverController
{
    public function actionIndex()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        
        $trade_no = Yii::$service->order->getSessionTradeNo();
        if (!$trade_no) {
            $code = Yii::$service->helper->appserver->order_not_find_increment_id_from_dbsession;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        $increment_id_arr = [];
        $order_model_arr = [];
        // 判断存储的订单交易号，是订单编号，还是支付号（多个订单一起支付而设立的支付号）
        if (Yii::$service->order->paymentCodeIsPaymentNo($trade_no)) {
            $order_model_arr = Yii::$service->order->getByPayNo($trade_no, false);
            foreach ($order_model_arr as $order_model) {
                $increment_id_arr[] = $order_model['increment_id'];
            }
        } else {
            $order_model = Yii::$service->order->getByIncrementId($trade_no);
            if ($order_model['increment_id']) {
                $order_model_arr[] = $order_model;
                $increment_id_arr[] = $order_model['increment_id'];
            }
        }
        
        
        
        // 清空购物车。这里针对的是未登录用户进行购物车清空。
        Yii::$service->cart->clearCartProductAndCoupon();
        // 清空session中存储的当前订单编号。
        Yii::$service->order->removeSessionTradeNo();
        $cartQty = Yii::$service->cart->getCartItemQty();
        $code = Yii::$service->helper->appserver->status_success;
        $data = [ 
            'increment_ids'  => implode(',', $increment_id_arr),
            'orders'         => $order_model_arr,
            'cart_qty' => $cartQty,
        ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
        
        return $responseData;
    }
}
