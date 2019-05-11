<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
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
    
    // 是否只有登陆用户才能查看产品价格
    public $loginShowProductPrice = false;
    // 是否可以显示并使用优惠券模块
    public $showCouponCode = false;
    
    
    
    // 是否只有登陆用户才能查看产品价格
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
    
}
