<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appserver\modules\Checkout\block\onepage;

use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Addresslist
{
    
    public function getLastData()
    {
        $identity = Yii::$app->user->identity;
        $customer_id = $identity['id'];
        if ($customer_id) {
            $filter = [
                'numPerPage'    => 30,
                'pageNum'        => 1,
                'orderBy'        => ['updated_at' => SORT_DESC],
                'where'            => [
                    ['customer_id' => $customer_id],
                ],
                'asArray' => true,
            ];
            $data = Yii::$service->customer->address->coll($filter);
            $coll = $data['coll'];
            foreach ($coll as $k=>$one) {
                $coll[$k]['is_default'] = $one['is_default'] == 1 ? true : false;
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
