<?php

use yii\db\Migration;

/**
 * Class m190509_043609_fec_bbc_base
 */
class m190509_043609_fec_bbc_base extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $arr = [
            
            '
            INSERT INTO `bdmin_user` (`id`, `username`, `password_hash`, `password_reset_token`, `email`, `person`, `code`, `auth_key`, `status`, `created_at`, `updated_at`, `password`, `access_token`, `access_token_created_at`, `allowance`, `allowance_updated_at`, `created_at_datetime`, `updated_at_datetime`, `birth_date`, `bdmin_bank`, `bdmin_bank_name`, `bdmin_bank_account`, `uuid`) VALUES
(3, \'fecshop\', \'$2y$13$sR7jfcfHULF9sSx9VY70auBZob9kGI1skGLBB1CDX5SiXrswBqFzO\', NULL, \'2358269014@qq.com\', \'terry\', NULL, \'EzZd2MFyeS3nkyZ1QX4FvsQnlHelfbzM\', 1, NULL, NULL, \'\', \'Kfyho3dAwWSRxUopzZ_OPzaQSsl-25Jg\', NULL, NULL, NULL, \'2019-05-09 12:33:31\', \'2019-05-09 12:33:31\', NULL, NULL, NULL, NULL, \'9928e826-7213-11e9-8dd4-00163e021360\');
            '
            ,
            
            "
            ALTER TABLE  `sales_flat_order` CHANGE  `bdmin_user_id`  `bdmin_user_id` INT( 11 ) NULL COMMENT  '供应商的id'
            ",
            
            "
            ALTER TABLE  `sales_flat_order_item` CHANGE  `bdmin_user_id`  `bdmin_user_id` INT( 11 ) NULL COMMENT  '供应商的id'
            ",
            
            "
            ALTER TABLE  `sales_flat_order` ADD  `payment_no` VARCHAR( 50 ) NULL COMMENT  '订单交易编码' AFTER  `order_id` , ADD INDEX (  `payment_no` )
            ",
            
            "
            ALTER TABLE  `sales_flat_order` CHANGE  `payment_no`  `payment_no` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  '订单交易编码'
            ",
            "
            ALTER TABLE  `sales_flat_order` CHANGE  `payment_method`  `payment_method` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  '支付方式'
            ",
            "
            ALTER TABLE  `sales_flat_order` CHANGE  `shipping_method`  `shipping_method` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  '货运方式'
            ",
            ];
    
        foreach ($arr as $sql) {
            $this->execute($sql);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190509_043609_fec_bbc_base cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190509_043609_fec_bbc_base cannot be reverted.\n";

        return false;
    }
    */
}
