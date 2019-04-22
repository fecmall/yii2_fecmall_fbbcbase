<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\models\mysqldb;

use fec\helpers\CUrl;
use fec\helpers\CRequest;
use fbbcbase\app\appbdmin\interfaces\base\AppbdminbaseBlockInterface;
use Yii;
use yii\base\BaseObject;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class BdminUser extends \fecshop\models\mysqldb\AdminUser
{
    
    /**
     * @inheritdoc
     */
    # 设置table
    public static function tableName()
    {
        return '{{%bdmin_user}}';
    }

    
}    