<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\models\mongodb\order;

use yii\mongodb\ActiveRecord;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class ProcessLog extends ActiveRecord
{
    
    /**
     * mongodb collection 的名字，相当于mysql的table name
     */
    public static function collectionName()
    {
        return 'sales_order_process_log';
    }

    
    public function attributes()
    {
        return [
            '_id',
            'order_id',
            'increment_id',
            'customer_id',
            'bdmin_user_id',
            'admin_user_id',
            'type',
            'remark',
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
            ['increment_id'  => 1,],
            ['customer_id'  => 1,],
            ['bdmin_user_id'  => 1,],
        ];

        $options = ['background' => true];
        foreach ($indexs as $columns) {
            self::getCollection()->createIndex($columns, $options);
        }
        
    }
   
}
