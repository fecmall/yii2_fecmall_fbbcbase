<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appserver\modules\Checkout\block\onepage;

use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Addresssave
{
    
    public function getLastData()
    {
        $identity = Yii::$app->user->identity;
        $customer_id = $identity['id'];
        if ($customer_id) {
            $address = Yii::$app->request->post('address');
            $address['customer_id'] = $customer_id;
            $state_city = $address['state_city'];
            $state_city_arr = explode(' ', $address['state_city']);
            if (count($state_city_arr) < 2) {
                $code = Yii::$service->helper->appserver->account_address_edit_param_invaild;
                $data = [
                    'errors' => 'state_city fomat is invalid',
                ];
                $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
                
                return $responseData;
            }
            unset($address['state_city']);
            if (count($state_city_arr) == 2) {
                $address['state'] = $state_city_arr[0];
                $address['city'] = $state_city_arr[0];
                $address['area'] = $state_city_arr[1];
            } else {
                $address['state'] = $state_city_arr[0];
                $address['city'] = $state_city_arr[1];
                $address['area'] = $state_city_arr[2];
            }
            
            $saveStatus = Yii::$service->customer->address->save($address);
            if (!$saveStatus) {
                $code = Yii::$service->helper->appserver->account_address_save_fail;
                $data = [
                    'errors' => Yii::$service->helper->errors->get(),
                ];
                $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
                
                return $responseData;
                
            }
            $code = Yii::$service->helper->appserver->status_success;
            $data = [
                'address_list' => $coll
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
    }

}
