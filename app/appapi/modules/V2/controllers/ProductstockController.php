<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
namespace fbbcbase\app\appapi\modules\V2\controllers;

use fbbcbase\app\appapi\modules\AppapiTokenController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class ProductstockController extends AppapiTokenController
{
    
    /**
     * api: /v2/productstock/updatebybase
     * 更新产品库存。
     * @postParam 参数例子如下：
     * [
     *      {
     *          "sku": "22221",    // 必填 ，字符串,产品id
     *          "qty": 2,                           // 必填 ，int类型，库存更新的个数，正数为累加，负数为累减少
     *          "custom_option_sku": "black-s"  // 选填 ，字符串，如果是custom option对应的库存减少，那么需要填写该项
     *      },
     *      {
     *          "sku": "p10001-kahaki-xxl",    // 必填 ，字符串
     *          "qty": 3,                           // 必填 ，int类型，库存更新的个数，正数为累加，负数为累减少
     *       },
     *      {
     *          "sku": "op0002",    // 必填 ，字符串
     *          "qty": -1,                           // 必填 ，int类型，库存更新的个数，正数为累加，负数为累减少
     *      }
     *  ]
     *  注意，此处是递增的库存的个数，而不是覆盖的值，如果库存减少，值为负数
     */
    public function actionUpdatebybase(){
        $items = Yii::$app->request->post('items');
        
        if (empty($items) || !is_array($items)) {
            
            return [
                'code'    => 400,
                'message' => 'post param : items is empty or is not array',
                'data'    => [],
            ];
        }
        
        foreach ($items as $one) {
            if (empty($one) || !is_array($one)) {
                
                return [
                    'code'    => 400,
                    'message' => 'post param : items one is empty or is not array',
                    'data'    => [],
                ];
            }
        }
        
        $innerTransaction = Yii::$app->db->beginTransaction();
        $errors = '';
        $identity = Yii::$app->user->identity;
        $bdmin_user_id = $identity->id;
        
        try {    
            if (!Yii::$service->product->stock->bdminupdatebybase($items, $bdmin_user_id)) {
                throw new \Exception('product stock update by base fail');
            }
            $innerTransaction->commit();
        } catch (\Exception $e) {
            $innerTransaction->rollBack();
            
            return [
                'code'    => 400,
                'message' => 'product stock update by base fail',
                'data'    => [
                    'errors' => Yii::$service->helper->errors->get(),
                ],
            ];
        }
        
        return [
            'code'    => 200,
            'message' => 'update product stock success',
            'data'    => []
        ];
    }
    
    
}
