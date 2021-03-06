<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appserver\modules\Customer\controllers;

use fecshop\app\appserver\modules\AppserverTokenController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class AddressController extends AppserverTokenController
{
    public $enableCsrfValidation = false;
    public $blockNamespace = 'fbbcbase\\app\\appserver\\modules\\Customer\\block';
    

    public function actionLists()
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
    
    public function actionEdit(){
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
    
    public function actionSave(){
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
    
    
    public function actionRemove(){
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
    
}
