<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appserver\modules\Payment;

use fecshop\app\appserver\modules\AppserverController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class PaymentController extends AppserverController
{
    protected $_order_model_arr;

    public function checkOrder()
    {
        //$homeUrl = Yii::$service->url->homeUrl();
        $tradeNo = Yii::$service->order->getSessionTradeNo();
        if (!$tradeNo) {
            $code = Yii::$service->helper->appserver->order_not_find_increment_id_from_dbsession;
            $data = [
                'error' => 'can not find order from db session',
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        // 得到订单
        $this->_order_model_arr = Yii::$service->order->getOrderModelsByTradeNo($tradeNo);
        
        if (empty($this->_order_model_arr) || !is_array($this->_order_model_arr)) {
            $code = Yii::$service->helper->appserver->order_not_exist;
            $data = [
                'error' => 'order is not exist',
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        
        return true;
    }
}
