<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\models\mysqldb\refund;

use yii\db\ActiveRecord;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class BdminRefund extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%refund_bdmin}}';
    }
    
}
