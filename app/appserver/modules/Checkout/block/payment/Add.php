<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appserver\modules\Checkout\block\payment;

use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Add 
{
    protected $_payment_method;
    
    public function getLastData()
    {
        $payment_method = Yii::$app->request->post('payment_method');
        $order_increment_id = Yii::$app->request->post('order_increment_id');
        if (!Yii::$service->payment->ifIsCorrectStandard($payment_method)) {
            $code = Yii::$service->helper->appserver->status_invalid_param;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        // update order payment method
        if (!Yii::$service->order->updatePaymentMethod($order_increment_id, $payment_method, true)) {
            $code = Yii::$service->helper->appserver->order_payment_method_update_fail;
            $data = [
                'errors' => Yii::$service->helper->errors->get(),
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        $startUrl = Yii::$service->payment->getStandardStartUrl($payment_method, 'appserver');
        $data = [
            'redirectUrl' => $startUrl,
        ];
        $code = Yii::$service->helper->appserver->status_success;
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
        
        return $responseData;
    }
   
    

}
