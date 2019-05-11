<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
use yii\db\Migration;

/**
 * Class m190308_010213_fec_bbc_base
 */
class m190308_010213_fec_bbc_base extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $arr = [
            "
            CREATE TABLE IF NOT EXISTS `bdmin_user` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `username` varchar(50) DEFAULT NULL COMMENT '用户名',
              `password_hash` varchar(80) DEFAULT NULL COMMENT '密码',
              `password_reset_token` varchar(60) DEFAULT NULL COMMENT '密码token',
              `email` varchar(60) DEFAULT NULL COMMENT '邮箱',
              `person` varchar(100) DEFAULT NULL COMMENT '用户姓名',
              `code` varchar(100) DEFAULT NULL,
              `auth_key` varchar(60) DEFAULT NULL,
              `status` int(5) DEFAULT NULL COMMENT '状态',
              `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
              `updated_at` int(11) DEFAULT NULL COMMENT '更新时间',
              `password` varchar(50) DEFAULT NULL COMMENT '密码',
              `access_token` varchar(60) DEFAULT NULL,
              `access_token_created_at` int(11) DEFAULT NULL COMMENT 'access token 的创建时间，格式为Int类型的时间戳',
              `allowance` int(11) DEFAULT NULL,
              `allowance_updated_at` int(11) DEFAULT NULL,
              `created_at_datetime` datetime DEFAULT NULL,
              `updated_at_datetime` datetime DEFAULT NULL,
              `birth_date` datetime DEFAULT NULL COMMENT '出生日期',
              PRIMARY KEY (`id`),
              UNIQUE KEY `username` (`username`),
              UNIQUE KEY `access_token` (`access_token`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
            "
            ,
            
            "
            ALTER TABLE  `customer` ADD  `bdmin_user_id` INT( 11 ) NOT NULL COMMENT  '该用户所属的供应商的id', ADD INDEX (  `bdmin_user_id` )
            "
            ,
            "
            ALTER TABLE  `sales_flat_order` ADD  `bdmin_user_id` INT( 11 ) NOT NULL COMMENT  '供应商的id' AFTER  `customer_id` , ADD INDEX (  `bdmin_user_id` )
            "
            ,
            "
            ALTER TABLE  `sales_flat_order_item` ADD  `bdmin_user_id` INT( 11 ) NOT NULL COMMENT  '供应商的id' AFTER  `customer_id` ,ADD INDEX (  `bdmin_user_id` )
            "
            ,
            "
            ALTER TABLE  `sales_flat_order` CHANGE  `order_status`  `order_status` VARCHAR( 80 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT  '订单流程状态'
            "
            ,
            
            "
            ALTER TABLE  `sales_flat_order` ADD  `order_operate_status` VARCHAR( 80 ) NULL COMMENT  '订单操作状态' AFTER  `order_status`
            ",
            
            "
            ALTER TABLE  `sales_flat_order_item` ADD  `item_status` VARCHAR( 80 ) NULL COMMENT  '订单商品状态，退款，换货等状态'
            ",
            "
            CREATE TABLE IF NOT EXISTS `sales_flat_order_after_sale` (
                `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `order_id` INT( 11 ) NOT NULL ,
                `item_id` INT( 11 ) NOT NULL ,
                `sku` VARCHAR( 100 ) NULL ,
                `price` DECIMAL( 12, 2 ) NULL ,
                `qty` INT( 11 ) NULL ,
                `tracking_number` VARCHAR( 100 ) NULL ,
                `created_at` INT( 11 ) NULL ,
                `updated_at` INT( 11 ) NULL
                ) ENGINE = INNODB; 
            ",
            
            "
            ALTER TABLE  `sales_flat_order_after_sale` ADD  `status` VARCHAR( 50 ) NOT NULL COMMENT  '退款状态' AFTER  `item_id` , ADD INDEX (  `status` )
            ",
            
            "
            ALTER TABLE  `sales_flat_order_after_sale` ADD  `bdmin_user_id` INT( 11 ) NULL COMMENT  '供应商id' AFTER  `order_id` , ADD INDEX (  `bdmin_user_id` )
            ",
            
            "
            ALTER TABLE  `sales_flat_order_after_sale` ADD  `customer_id` INT( 11 ) NULL AFTER  `bdmin_user_id` , ADD INDEX (  `customer_id` )
            ",
            
            "
           ALTER TABLE  `sales_flat_order_after_sale` ADD  `base_price` DECIMAL( 12, 2 ) NULL AFTER  `price` 
            ",
            "
            ALTER TABLE  `sales_flat_order_after_sale` ADD  `image` VARCHAR( 255 ) NULL AFTER  `sku` 
            ",
            "
            ALTER TABLE  `sales_flat_order_after_sale` ADD  `product_id` VARCHAR( 100 ) NULL AFTER  `sku`
            ",
            
            "
            ALTER TABLE  `sales_flat_order_after_sale` ADD  `custom_option_sku` VARCHAR( 100 ) NULL AFTER  `sku`
            ",
            
            "
            ALTER TABLE  `sales_flat_order_after_sale` ADD  `currency_code` VARCHAR( 20 ) NULL AFTER  `product_id` 
            ",
            
            "
            ALTER TABLE  `sales_flat_order_after_sale` ADD  `order_to_base_rate` DECIMAL( 12, 4 ) NULL COMMENT  '汇率' AFTER  `currency_code` 
            ",
            
            "
            ALTER TABLE  `sales_flat_order_after_sale` ADD  `increment_id` VARCHAR( 50 ) NULL AFTER  `order_id` 
            ",
            "
            ALTER TABLE  `sales_flat_order_after_sale` ADD  `payment_method` VARCHAR( 50 ) NULL AFTER  `increment_id`
            ",
            
            "
            CREATE TABLE IF NOT EXISTS `refund_bdmin` (
                `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `bdmin_user_id` INT( 11 ) NOT NULL COMMENT  '供应商id',
                `as_id` INT( 11 ) NULL COMMENT  'after sale id',
                `increment_id` VARCHAR( 50 ) NULL ,
                `price` DECIMAL( 12, 2 ) NULL ,
                `base_price` DECIMAL( 12, 2 ) NULL ,
                `currency_code` VARCHAR( 20 ) NULL ,
                `order_to_base_rate` DECIMAL( 12, 2 ) NULL ,
                `customer_id` INT( 11 ) NULL ,
                `customer_bank_name` VARCHAR( 50 ) NULL ,
                `customer_bank_account` VARCHAR( 100 ) NULL ,
                `created_at` INT( 11 ) NULL ,
                `updated_at` INT( 11 ) NULL ,
                `type` VARCHAR( 60 ) NULL COMMENT  '退款的类型',
                INDEX (  `bdmin_user_id` )
                ) ENGINE = INNODB; 
            ",
            
            "
            CREATE TABLE IF NOT EXISTS `refund_admin` (
                `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `bdmin_user_id` INT( 11 ) NOT NULL COMMENT  '供应商id',
                `as_id` INT( 11 ) NULL COMMENT  'after sale id',
                `increment_id` VARCHAR( 50 ) NULL ,
                `price` DECIMAL( 12, 2 ) NULL ,
                `base_price` DECIMAL( 12, 2 ) NULL ,
                `currency_code` VARCHAR( 20 ) NULL ,
                `order_to_base_rate` DECIMAL( 12, 2 ) NULL ,
                `customer_id` INT( 11 ) NULL ,
                `customer_bank_name` VARCHAR( 50 ) NULL ,
                `customer_bank_account` VARCHAR( 100 ) NULL ,
                `created_at` INT( 11 ) NULL ,
                `updated_at` INT( 11 ) NULL ,
                `type` VARCHAR( 60 ) NULL COMMENT  '退款的类型',
                INDEX (  `bdmin_user_id` )
                ) ENGINE = INNODB; 
            ",
            
            "
            ALTER TABLE  `refund_admin` ADD  `customer_bank` VARCHAR( 100 ) NULL AFTER  `customer_bank_name` 
            ",
            
            "
            ALTER TABLE  `refund_bdmin` ADD  `customer_bank` VARCHAR( 100 ) NULL AFTER  `customer_bank_name` 
            ",
            
            "
            ALTER TABLE  `customer` ADD  `customer_bank` VARCHAR( 100 ) NULL COMMENT  '银行名称（银行，支付宝，微信）',
            ADD  `customer_bank_name` VARCHAR( 100 ) NULL COMMENT  '银行账户对应的姓名',
            ADD  `customer_bank_account` VARCHAR( 100 ) NULL COMMENT  '银行账户'
            ",
            
                
            "
            ALTER TABLE  `bdmin_user` ADD  `bdmin_bank` VARCHAR( 100 ) NULL COMMENT  '银行名称（银行，支付宝，微信）',
            ADD  `bdmin_bank_name` VARCHAR( 100 ) NULL COMMENT  '银行账户对应的姓名',
            ADD  `bdmin_bank_account` VARCHAR( 100 ) NULL COMMENT  '银行账户'
           ",
            
            "
            ALTER TABLE  `refund_admin` ADD  `status` VARCHAR( 50 ) NULL 
            ",
            
            "
            ALTER TABLE  `refund_bdmin` ADD  `status` VARCHAR( 50 ) NULL 
            ",
            
            "
            ALTER TABLE  `refund_admin` ADD  `customer_email` VARCHAR( 100 ) NULL AFTER  `customer_id` 
            ",
            
            "
            ALTER TABLE  `refund_bdmin` ADD  `customer_email` VARCHAR( 100 ) NULL AFTER  `customer_id` 
            ",
            "
            ALTER TABLE  `refund_admin` ADD  `refunded_at` INT( 11 ) NULL COMMENT  '退款时间' AFTER  `updated_at`
            ",
            "
            ALTER TABLE  `refund_bdmin` ADD  `refunded_at` INT( 11 ) NULL COMMENT  '退款时间' AFTER  `updated_at`
            ",
            
            "
            CREATE TABLE IF NOT EXISTS `statistical_bdmin_month` (
                `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `bdmin_user_id` INT( 11 ) NOT NULL COMMENT  '供应商ID',
                `complete_order_total` DECIMAL( 12, 2 ) NULL COMMENT  '完成的订单金额',
                `refund_return_total` DECIMAL( 12, 2 ) NULL COMMENT  '退货-退款总额',
                `month` INT( 5 ) NULL COMMENT  '月份',
                `updated_at` INT( 11 ) NULL COMMENT  '更新时间',
                `created_at` INT( 11 ) NULL COMMENT  '创建时间'
                ) ENGINE = INNODB;
            ",
            "
            ALTER TABLE  `sales_flat_order` ADD  `received_at` INT( 11 ) NULL COMMENT  '用户订单收货时间' AFTER  `updated_at`
            ",
            
            "
            ALTER TABLE  `statistical_bdmin_month` ADD  `year` INT( 5 ) NULL AFTER  `month`
            ",
            
            "
            ALTER TABLE  `statistical_bdmin_month` ADD  `month_total` DECIMAL( 12, 2 ) NULL COMMENT  '月结算金额 = 订单总额 - 退款总额' AFTER  `refund_return_total`
            ",
            
            "
            ALTER TABLE  `statistical_bdmin_month` CHANGE  `refund_return_total`  `admin_refund_return_total` DECIMAL( 12, 2 ) NULL DEFAULT NULL COMMENT  '平台退货-退款总额'
            " ,
            "
            ALTER TABLE  `statistical_bdmin_month` ADD  `bdmin_refund_return_total` DECIMAL( 12, 2 ) NULL COMMENT  '供应商退款(货到付款类型的订单退款)-退款总额' AFTER  `admin_refund_return_total`
            ",
            
            "
            ALTER TABLE  `statistical_bdmin_month` CHANGE  `month_total`  `month_total` DECIMAL( 12, 2 ) NULL DEFAULT NULL COMMENT  '月结算金额 = 完成订单总额 - 平台退货-退款总额'
            ",
            
            "
            ALTER TABLE  `customer_address` ADD  `area` VARCHAR( 50 ) NULL COMMENT  '城市对应的区' AFTER  `city`
            ",
            
            "
            ALTER TABLE  `sales_flat_order` ADD  `customer_address_area` VARCHAR( 50 ) NULL COMMENT  '订单地址城市对应的区' AFTER  `customer_address_city`
            ",
            
            
            "
            ALTER TABLE  `sales_flat_order` ADD  `dispatched_at` INT( 11 ) NULL COMMENT  '订单的发货时间' AFTER  `updated_at`
            ",
            
            "
            ALTER TABLE  `sales_flat_order` ADD  `recevie_delay_days` INT( 11 ) NULL COMMENT  '收货时间延迟的天数，用户可以发起延迟收货天数的操作' AFTER  `dispatched_at`
            ",
            
            "
            ALTER TABLE  `sales_flat_order` ADD  `bdmin_audit_acceptd_at` INT( 11 ) NULL COMMENT  '供应商审核通过的时间' AFTER  `updated_at`
            ",
            
            "
            ALTER TABLE  `customer` CHANGE  `bdmin_user_id`  `bdmin_user_id` INT( 11 ) NULL COMMENT  '该用户所属的供应商的id'
            ",
            "
            ALTER TABLE  `customer` ADD  `phone` VARCHAR( 20 ) NULL COMMENT  '手机号' AFTER  `email`
            ",
            
            "
            ALTER TABLE  `bdmin_user` ADD  `uuid` VARCHAR( 100 ) NULL COMMENT  '供应商的唯一编码', ADD UNIQUE (`uuid`)
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
        echo "m190308_010213_fec_bbc_base cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190308_010213_fec_bbc_base cannot be reverted.\n";

        return false;
    }
    */
}
