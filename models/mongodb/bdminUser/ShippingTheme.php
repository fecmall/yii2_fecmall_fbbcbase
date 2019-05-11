<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\models\mongodb\bdminUser;

use yii\mongodb\ActiveRecord;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class ShippingTheme extends ActiveRecord
{
    
    /**
     * mongodb collection 的名字，相当于mysql的table name
     */
    public static function collectionName()
    {
        return 'bdmin_shipping_theme';
    }

    
    public function attributes()
    {
        return [
            '_id',
            'bdmin_user_id',  // 经销商id
            'code',   // 模板编号
            'label',   // 模板名称
            'type',   //  类型，买家还是卖家承担运费， bdmin_cost  or customer_cost
            'first_weight', //  首重 [ 'weight' => 1, 'cost'  => 10]
            'first_cost',
            'next_weight', //  增重 [ 'weight' => 1, 'cost'  => 8]
            'next_cost',
            'created_at',
            'updated_at',
        ];
    }


    /**
     * 给model对应的表创建索引的方法
     * 在indexs数组中填写索引，如果有多个索引，可以填写多行
     * 在migrate的时候会运行创建索引，譬如：
     * @fecshop/migrations/mongodb/m170228_072455_fecshop_tables
     */
    public static function create_index()
    {
        $indexs = [
            ['type' => -1, 'key'  => 1, 
            'user_id'  => 1,],
        ];

        $options = ['background' => true];
        foreach ($indexs as $columns) {
            self::getCollection()->createIndex($columns, $options);
        }
    }
   
}
