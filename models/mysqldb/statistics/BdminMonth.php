<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\models\mysqldb\statistics;

use yii\db\ActiveRecord;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class BdminMonth extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%statistical_bdmin_month}}';
    }
    
}
