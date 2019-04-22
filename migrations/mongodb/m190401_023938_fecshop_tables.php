<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
class m190401_023938_fecshop_tables extends \yii\mongodb\Migration
{
    public function up()
    {
        \fbbcbase\models\mongodb\SystemConfig::create_index();
        \fbbcbase\models\mongodb\order\ProcessLog::create_index();
    }

    public function down()
    {
        echo "m190401_023938_fecshop_tables cannot be reverted.\n";

        return false;
    }
}
