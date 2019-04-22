<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appserver\modules\Checkout\controllers;

use fecshop\app\appserver\modules\AppserverController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class OnepageController extends \fecshop\app\appserver\modules\Checkout\controllers\OnepageController
{
    public $enableCsrfValidation = false;
    public $blockNamespace = 'fbbcbase\\app\\appserver\\modules\\Checkout\\block';
    

    public function actionIndex()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $guestOrder = Yii::$app->controller->module->params['guestOrder'];
        if(!$guestOrder && Yii::$app->user->isGuest){
            $code = Yii::$service->helper->appserver->account_no_login_or_login_token_timeout;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        } 

        return $this->getBlock()->getLastData();
    }
    
    /*
    public function actionAddresslist()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $guestOrder = Yii::$app->controller->module->params['guestOrder'];
        if(!$guestOrder && Yii::$app->user->isGuest){
            $code = Yii::$service->helper->appserver->account_no_login_or_login_token_timeout;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        } 

        return $this->getBlock()->getLastData();
    }
    
    public function actionChangedefaultaddress()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $guestOrder = Yii::$app->controller->module->params['guestOrder'];
        if(!$guestOrder && Yii::$app->user->isGuest){
            $code = Yii::$service->helper->appserver->account_no_login_or_login_token_timeout;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        } 

        return $this->getBlock()->getLastData();
    }
    
    public function actionAddressedit(){
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $guestOrder = Yii::$app->controller->module->params['guestOrder'];
        if(!$guestOrder && Yii::$app->user->isGuest){
            $code = Yii::$service->helper->appserver->account_no_login_or_login_token_timeout;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        } 

        return $this->getBlock()->getLastData();
        
    }
    
    public function actionAddresssave(){
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $guestOrder = Yii::$app->controller->module->params['guestOrder'];
        if(!$guestOrder && Yii::$app->user->isGuest){
            $code = Yii::$service->helper->appserver->account_no_login_or_login_token_timeout;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        } 

        return $this->getBlock()->getLastData();
        
    }
    */
    public function actionSubmitorder(){
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $guestOrder = Yii::$app->controller->module->params['guestOrder'];
        if(!$guestOrder && Yii::$app->user->isGuest){
            $code = Yii::$service->helper->appserver->account_no_login_or_login_token_timeout;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        } 
        
        return $this->getBlock('placeorder')->getLastData();
        
        
        
    }

    public function actionChangecountry()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        return $this->getBlock('index')->ajaxChangecountry();
    }

    public function actionGetshippingandcartinfo()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        return $this->getBlock('index')->ajaxUpdateOrderAndShipping();
    }
}
