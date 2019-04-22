<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appserver\modules\Customer\block\address;

use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Remove
{
    
    public function getLastData()
    {
        $identity = Yii::$app->user->identity;
        $customer_id = $identity['id'];
        if ($customer_id) {
            $address_id = Yii::$app->request->post('address_id');
            $removeStatus = Yii::$service->customer->address->remove($address_id, $customer_id);
            if (!$removeStatus) {
                $code = Yii::$service->helper->appserver->customer_remove_address_fail;
                $data = [
                    'errors' => Yii::$service->helper->errors->get(),
                ];
                $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
                
                return $responseData;
                
            }
            $code = Yii::$service->helper->appserver->status_success;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
    }

}
