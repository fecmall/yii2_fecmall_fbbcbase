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
class Edit
{
    
    public function getLastData()
    {
        $identity = Yii::$app->user->identity;
        $customer_id = $identity['id'];
        $address_id = Yii::$app->request->post('address_id');
        if ($customer_id && $address_id) {
            $address = Yii::$service->customer->address->getAddressByIdAndCustomerId($address_id, $customer_id);
            $state_city = $address['state'] . ' ' . $address['city'] . ' ' . $address['area'];
            $is_default = Yii::$service->customer->address->is_default;
            $is_not_default = Yii::$service->customer->address->is_not_default;
            $code = Yii::$service->helper->appserver->status_success;
            $address = [
                'state_city' => $state_city,
                'address_id' => $address['address_id'],
                'first_name' => $address['first_name'],
                'telephone' => $address['telephone'],
                'street1' => $address['street1'],
                'zip' => $address['zip'],
                'is_default' => $address['is_default'] == $is_default ? true : false,
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, ['address' => $address]);
            
            return $responseData;
        }
    }

}
