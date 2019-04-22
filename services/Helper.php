<?php

/*
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\services;

use Yii;
use Ramsey\Uuid\Uuid;

/**
 * Helper service.
 *
 * @property \fbbcbase\services\helper\Appapi $appapi
 * @property \fbbcbase\services\helper\Appserver $appserver appserver sub-service of helper service
 * @property \fbbcbase\services\helper\AR $ar
 * @property \fbbcbase\services\helper\Captcha $captcha
 * @property \fbbcbase\services\helper\Country $country
 * @property \fbbcbase\services\helper\Echart $echart
 * @property \fbbcbase\services\helper\ErrorHandler $errorHandler
 * @property \fbbcbase\services\helper\Errors $errors errors sub-service of helper service
 * @property \fbbcbase\services\helper\Format $format
 * @property \fbbcbase\services\helper\MobileDetect $mobileDetect
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Helper extends \fecshop\services\Helper
{
    protected $_app_name;

    protected $_param;
    /**
     * 用户在前端商城，是否只能看到绑定的供应商的产品
     * 其他的供应商的产品是否不能看到？
     */
    public $loginCustomerOnlySeeSupplierProduct = true;
    
    
    
    public $showCouponCode = false;
    // 登陆用户-是否只能查看绑定的供应商的商品
    public function isLoginCustomerOnlySeeSupplierProduct() {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        return $this->loginCustomerOnlySeeSupplierProduct;
    }
    // 登陆用户-是否只能查看设置的仓库的商品
    public $loginCustomerOnlySeeSelectedWarehouseProduct = true;
    public function isLoginCustomerOnlySeeSelectedWarehouseProduct(){
        if (Yii::$app->user->isGuest) {
            return false;
        }
        return $this->loginCustomerOnlySeeSelectedWarehouseProduct;
    }
    
    // 是否只有登陆用户才能查看产品价格
    public $loginShowProductPrice = true;
    public function canShowProductPrice(){
        if ($this->loginShowProductPrice && Yii::$app->user->isGuest) {
            
            return false;
        } 
        
        return true;
    }
    // 是否可以显示并使用优惠券模块
    public function canShowUseCouponCode(){
        
        return $this->showCouponCode;
    }
    
    /**
     * 这个是供应商分享链接的时候，带上一个供应商唯一码的尾巴，然后vue端就可以写入cookie
     * 然后vue的每次访问，就会在header中发送这个供应商唯一码
     * appserver端接收这个唯一码，然后查看产品，帐号注册等都绑定这个供应商
     * 
     * 上面的描述就是这个参数和函数的作用
     */
    public $guestRelateBdminUserIdByUrlParam = true;
    
    public function getGuestUrlParamRelateBdminUserId()
    {
        if (!$this->guestRelateBdminUserIdByUrlParam) {
            
            return '';
        }
        if (!Yii::$app->user->isGuest) {
            
            return '';
        }
           
        return $this->getUrlParamBdminUserId();
    }
    
    
    // 得到后台配置的warehouse
    public function getbdminWarehouseList() {
        $bdmin_user_id = $this->getProductBdminUserId();
        $config = Yii::$service->systemConfig->getBdminBaseConfig($bdmin_user_id);
        $warehouse = isset($config['warehouse']) ? $config['warehouse'] : '';
        $warehouseArr = explode('，', $warehouse);
        
        return $warehouseArr;
    }
    // 是否使用默认已有的仓库选项
    public function ifUseDefaultWarehouse() {
        
    }
    
    protected  $_product_bdmin_user_id;
    // 用来暂存产品对应的bdmin_user_id
    public function getProductBdminUserId() {
        return $this->_product_bdmin_user_id;
    }
    public function setProductBdminUserId($bdmin_user_id) {
        $this->_product_bdmin_user_id = $bdmin_user_id;
    }
    
    /**
     * 登陆账户是否检查bdmin_user_id
     * 如果true，那么如果bdmin_user_id 为空，则不能登陆
     * 如果false，那么如果bdmin_user_id 为空，可以登陆
     */
    protected $customerLoginCheckBdminUserId =true;
    
    public function isCustomerLoginCheckBdminUserId() 
    {
        return $this->customerLoginCheckBdminUserId;
    }
    // 得到uuid的字符串
    public function getUuidString()
    {
        $uuid1 = Uuid::uuid1();
        
        return $uuid1->toString();
    }
    
    
    public $fecshop_store_uuid = 'fecshop-store-uuid';
    protected $_headerFecshopStoreUuid = null;
    public function getHeaderFecshopStoreUuid()
    {
        if ($this->_headerFecshopStoreUuid === null) {
            $header = Yii::$app->request->getHeaders();
            $uuidName = $this->fecshop_store_uuid;
            // 1.从requestheader里面获取uuid，
            if (isset($header[$uuidName]) && !empty($header[$uuidName])) {
                $this->_headerFecshopStoreUuid = $header[$uuidName];
            } else {
                $this->_headerFecshopStoreUuid = '';
            }
        }
        return $this->_headerFecshopStoreUuid;
    }
    
    protected  $_url_param_bdmin_user_id = null;
    // 前端从header获取的bdmin对应的uuid code, 通过下面的方法获取对应的bdmin_user_id;
    public function getUrlParamBdminUserId() {
        if ($this->_url_param_bdmin_user_id === null) {
            $uuidStr = $this->getHeaderFecshopStoreUuid();
            if ($uuidStr) {
                $bdminUser = Yii::$service->bdminUser->bdminUser->getByUuid($uuidStr);
                $this->_url_param_bdmin_user_id = $bdminUser->id;
            } else {
                $this->_url_param_bdmin_user_id = '';
            }
        }
        
        return $this->_url_param_bdmin_user_id;
    }  
    
}
