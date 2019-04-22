<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appserver\modules\Customer\block\address;

use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Changedefaultaddress
{
    
    public function getLastData()
    {
        $address_id = Yii::$app->request->post('address_id');
        $identity = Yii::$app->user->identity;
        $customer_id = $identity->id;
        $setStatus = Yii::$service->customer->address->setDefaultAddress($customer_id, $address_id);
        if (!$setStatus) {
            $code = Yii::$service->helper->appserver->customer_set_default_address_fail;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        $code = Yii::$service->helper->appserver->status_success;
        $data = [];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
        
        return $responseData;
    }

}
