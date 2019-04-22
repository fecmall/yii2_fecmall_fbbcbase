<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\models\mysqldb\order;

use yii\db\ActiveRecord;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class BdminRefund extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%sales_flat_order_bdmin_refund}}';
    }
    
}
