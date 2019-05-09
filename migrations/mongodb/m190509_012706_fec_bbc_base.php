<?php

class m190509_012706_fec_bbc_base extends \yii\mongodb\Migration
{
    public function up()
    {
        // init system config
        $sysConfig = new \fbbcbase\models\mongodb\SystemConfig;
        $sysConfig->created_at = time();
        $sysConfig->updated_at = time();
        $sysConfig->type = 'admin';
        $sysConfig->key = 'home_page';
        $sysConfig->content = [
            'title' => 'FecMall 多商户商城',
            'skus' => '222212,22221'
        ];
        $sysConfig->save();
    }



    public function down()
    {
        echo "m190509_012706_fec_bbc_base cannot be reverted.\n";

        return false;
    }
}
