<?php

/*
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\services\product;

//use fecshop\models\mongodb\Product;
//use fecshop\models\mysqldb\product\ProductFlatQty;
//use fecshop\models\mysqldb\product\ProductCustomOptionQty;
use fecshop\services\Service;
use Yii;

/**
 * Stock sub-service of product service.
 *
 * @method deduct($items = [])
 * @see \fecshop\services\product\Stock::actionDeduct()
 * @method productIsInStock($product, $qty, $custom_option_sku)
 * @see Stock::actionProductIsInStock()
 *
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Stock extends \fecshop\services\product\Stock
{
    
    /**
     * @var array $items
     * example:
     * [
     *		[
     *			'sku' => '22221',
     *			'qty' => 2,
     *			'custom_option_sku' => 'black-s',  # 存在该项，则应该到产品的
     *		],
     *		[
     *			'sku' => 'op0002',
     *			'qty' => -1,
     *		],
     *	]
     *  @return bool
     *  通过累加和累减的方式更新产品库存，通过累加和累减的方式如果扣除成功，则返回true，如果返回失败，则返回false
     *  如果库存为正数，则累加，如果库存为负数，则累减。
     *  一般是api的方式更新库存。  
     *
     *  **注意**：在调用该函数的时候必须使用事务，在返回false的时候要回滚。
     *  **注意**：在调用该函数的时候必须使用事务，在返回false的时候要回滚。
     *  **注意**：在调用该函数的时候必须使用事务，在返回false的时候要回滚。
     */
    protected function actionBdminupdatebybase($items, $bdmin_user_id)
    {
        if (empty($items) || !is_array($items)) {
            Yii::$service->helper->errors->add('param items is not array or is empty');
            
            return false;
        }
        if (empty($bdmin_user_id) ) {
            Yii::$service->helper->errors->add('bdmin_user_id is empty');
            
            return false;
        }
        // 查看产品的状态，上下架状态，以及产品库存检查是否够用。
        foreach ($items as $k=>$item) {
            $sku        = $item['sku'];
            $sale_qty           = (int)$item['qty'];
            $custom_option_sku  = $item['custom_option_sku'];
            $product = Yii::$service->product->getBySku($sku);
            // 购物车中的产品已经被删除，则会查询不到
            if ($bdmin_user_id != $product['bdmin_user_id']) {
                Yii::$service->helper->errors->add('product: [ {sku} ] , you do not have role to operate it', ['sku' => $sku]);
                
                return false;
            } 
            $status = $this->productIsInStock($product, $sale_qty, $custom_option_sku, false);
            if (!$status) {
                
                return false;
            }
            $primaryKey = Yii::$service->product->getPrimaryKey();
            $items[$k]['product_id'] = (string)$product[$primaryKey];
        } 
        
        /**
         * $this->checkItemsStock 函数检查产品是否都是上架状态
         * 如果满足上架状态 && 零库存为1，则直接返回。
         */
        if ($this->zeroInventory) {
            
            return true; // 零库存模式 不会更新产品库存。
        }
        
        // 开始扣除库存。
        foreach ($items as $k=>$item) {
            $sku         = $item['sku'];
            $product_id         = $item['product_id'];
            $sale_qty           = (int)$item['qty'];
            $custom_option_sku  = $item['custom_option_sku'];
            if ($product_id && $sale_qty) {
                // 应对高并发库存超卖的控制，扣除库存的时候，加上qty个数的查询，不满足查询条件则不扣除库存
                
                $updateColumns = $this->_flatQtyModel->updateAllCounters(
                    ['qty' => $sale_qty],
                    ['and', ['product_id' => $product_id], ['>=', 'qty', 0 - $sale_qty]]
                );
                
                if (empty($updateColumns)) {// 上面更新sql返回的更新行数如果为0，则说明更新失败，产品不存在，或者产品库存不够
                    Yii::$service->helper->errors->add('product: [ {sku} ] is stock out', ['sku' => $sku]);
                    
                    return false;
                }
                // 对于custom option（淘宝模式）的库存扣除
                if ($custom_option_sku) {
                    $updateColumns = $this->_COQtyModel->updateAllCounters(
                        ['qty' => $sale_qty],
                        [
                            'and',
                            [
                                'custom_option_sku' => $custom_option_sku,
                                'product_id'        => $product_id
                            ],
                            ['>=', 'qty', 0 - $sale_qty]
                        ]
                    );
                    if (empty($updateColumns)) {// 上面更新sql返回的更新行数如果为0，则说明更新失败，产品不存在，或者产品库存不够
                        Yii::$service->helper->errors->add('product: [ {sku} ] is stock out', ['sku' => $sku]);
                        
                        return false;
                    }
                }
            }
        }
        
        return true;
    }
}
