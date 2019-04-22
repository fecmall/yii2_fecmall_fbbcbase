<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\services\product;

use fecshop\models\mongodb\Product;
use fecshop\services\Service;
use Yii;

/**
 * Product ProductMysqldb Service
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class ProductMongodb extends \fecshop\services\product\ProductMongodb
{
    public $numPerPage = 20;
    
    protected $_productModelName = '\fbbcbase\models\mongodb\Product';

    /**
     * @param $one|array , 产品数据数组
     * @param $originUrlKey|string , 产品的原来的url key ，也就是在前端，分类的自定义url。
     * 保存产品（插入和更新），以及保存产品的自定义url
     * 如果提交的数据中定义了自定义url，则按照自定义url保存到urlkey中，如果没有自定义urlkey，则会使用name进行生成。
     */
    public function save($one, $originUrlKey = 'catalog/product/index')
    {
        if (!$this->initSave($one)) {
            return false;
        }
        $one['min_sales_qty'] = (int)$one['min_sales_qty'];
        $currentDateTime = \fec\helpers\CDate::getCurrentDateTime();
        $primaryVal = isset($one[$this->getPrimaryKey()]) ? $one[$this->getPrimaryKey()] : '';
       
        //验证sku 是否重复的where
        $whereOr = [];
        if ($one['warehouse']) {
            $whereOr[] =   [
                'sku' => $one['sku'],
                'warehouse' => $one['warehouse'],
            ];   
        } else {
            $whereOr[] =   [
                'sku' => $one['sku'],
            ];
        }
        $whereOr[] =   [
            'sku' => $one['sku'],
            ['<>', 'bdmin_user_id', $one['bdmin_user_id'],]
        ]; 
        $whereOr[] =   [
            'spu' => $one['spu'],
            ['<>', 'bdmin_user_id', $one['bdmin_user_id'],]
        ]; 
        $where = [
            '$or' => $whereOr,
        ];
        if ($primaryVal) {
            $model = $this->_productModel->findOne($primaryVal);
            if (!$model) {
                Yii::$service->helper->errors->add('Product {primaryKey} is not exist', ['primaryKey'=>$this->getPrimaryKey()]);

                return false;
            }
            $product_one = $this->_productModel->find()->asArray()->where([
                '<>', $this->getPrimaryKey(), (new \MongoDB\BSON\ObjectId($primaryVal)),
            ])->andWhere($where)->one();
            
            if ($product_one['sku']) {
                if ($one['warehouse'] && $product_one['sku'] == $one['sku'] && $product_one['warehouse'] == $one['warehouse']) {
                    Yii::$service->helper->errors->add('Product Sku:{sku} and warehouse:{warehouse} is exist, please use other sku and warehouse', [ 'sku' => $one['sku'], 'warehouse' => $one['warehouse'] ]);
                } else if (!$one['warehouse'] && $product_one['sku'] == $one['sku']) {
                    Yii::$service->helper->errors->add('Product Sku:{sku} is exist ，please use other sku', [ 'sku' => $one['sku'], 'warehouse' => $one['warehouse'] ]);
                }
                if ($product_one['bdmin_user_id'] != $one['bdmin_user_id'] && $product_one['spu'] == $one['spu']) {
                    Yii::$service->helper->errors->add('Product spu:{spu} is exist in other bdmin，please use other spu', [ 'spu' => $one['spu'], 'warehouse' => $one['warehouse'] ]);
                } 
                return false;
            }
        } else {
            $model = new $this->_productModelName();
            $model->created_at = time();
            $model->created_user_id = \fec\helpers\CUser::getCurrentUserId();
            $primaryVal = new \MongoDB\BSON\ObjectId();
            $model->{$this->getPrimaryKey()} = $primaryVal;
            //验证sku 是否重复
            $product_one = $this->_productModel->find()->asArray()->where([
                'sku' => $one['sku'],
            ])->andWhere($where)->one();
            if ($product_one['sku']) {
                if ($one['warehouse'] && $product_one['sku'] == $one['sku'] && $product_one['warehouse'] == $one['warehouse']) {
                    Yii::$service->helper->errors->add('Product Sku:{sku} and warehouse:{warehouse} is exist, please use other sku and warehouse', [ 'sku' => $one['sku'], 'warehouse' => $one['warehouse'] ]);
                } else if (!$one['warehouse'] && $product_one['sku'] == $one['sku']) {
                    Yii::$service->helper->errors->add('Product Sku:{sku} is exist ，please use other sku', [ 'sku' => $one['sku'], 'warehouse' => $one['warehouse'] ]);
                }
                if ($product_one['bdmin_user_id'] != $one['bdmin_user_id'] && $product_one['spu'] == $one['spu']) {
                    Yii::$service->helper->errors->add('Product spu:{spu} is exist in other bdmin，please use other spu', [ 'spu' => $one['spu'], 'warehouse' => $one['warehouse'] ]);
                } 
                return false;
            }
        }
        $model->updated_at = time();
        // 计算出来产品的最终价格。
        $one['final_price'] = Yii::$service->product->price->getFinalPrice($one['price'], $one['special_price'], $one['special_from'], $one['special_to']);
        $one['score'] = (int) $one['score'];
        unset($one['_id']);
        /**
         * 保存产品
         */
        $saveStatus = Yii::$service->helper->ar->save($model, $one);
        /**
         * 如果 $one['custom_option'] 不为空，则计算出来库存总数，填写到qty
         */
        if (is_array($one['custom_option']) && !empty($one['custom_option'])) {
            $custom_option_qty = 0;
            foreach ($one['custom_option'] as $co_one) {
                $custom_option_qty += $co_one['qty'];
            }
            $model->qty = $custom_option_qty;
        }
        $saveStatus = Yii::$service->helper->ar->save($model, $one);
        // 自定义url部分
        if ($originUrlKey) {
            $originUrl = $originUrlKey.'?'.$this->getPrimaryKey() .'='. $primaryVal;
            $originUrlKey = isset($one['url_key']) ? $one['url_key'] : '';
            $defaultLangTitle = Yii::$service->fecshoplang->getDefaultLangAttrVal($one['name'], 'name');
            $urlKey = Yii::$service->url->saveRewriteUrlKeyByStr($defaultLangTitle, $originUrl, $originUrlKey);
            $model->url_key = $urlKey;
            $model->save();
        }
        $product_id = $model->{$this->getPrimaryKey()};
        /**
         * 更新产品库存。
         */
        Yii::$service->product->stock->saveProductStock($product_id, $one);
        /**
         * 更新产品信息到搜索表。
         */
        Yii::$service->search->syncProductInfo([$product_id]);

        return $model;
    }
    
}
