<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appserver\modules\Customer\controllers;

use fecshop\app\appserver\modules\AppserverController;
use Yii;
use \Firebase\JWT\JWT;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class LoginController extends AppserverController
{
    public $enableCsrfValidation = false ;
    /**
     * 登录用户的部分
     */
    public function actionAccount(){
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $identity = Yii::$service->customer->loginByAccessToken(get_class($this));
        if($identity){
            // 用户已经登录
            $code = Yii::$service->helper->appserver->account_is_logined;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        $email       = Yii::$app->request->post('email');
        $password    = Yii::$app->request->post('password');
        $loginParam  = \Yii::$app->getModule('customer')->params['login'];
        $loginCaptchaActive = isset($loginParam['loginPageCaptcha']) ? $loginParam['loginPageCaptcha'] : false;
        if($loginCaptchaActive){
            $captcha    = Yii::$app->request->post('captcha');
            if(!Yii::$service->helper->captcha->validateCaptcha($captcha)){
                $code = Yii::$service->helper->appserver->status_invalid_captcha;
                $data = [];
                $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
                
                return $responseData;
            }
        }
        $accessToken = Yii::$service->customer->loginAndGetAccessToken($email,$password);
        if($accessToken){
            $cartQty = Yii::$service->cart->getCartItemQty();
            $code = Yii::$service->helper->appserver->status_success;
            $data = [
                'cart_qty' => $cartQty,
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }else{
            $code = Yii::$service->helper->appserver->account_login_invalid_email_or_password;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        
    }
    
    /**
     * 登录页面
     *
     */
    public function actionIndex(){
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $identity = Yii::$service->customer->loginByAccessToken(get_class($this));
        if($identity){
            // 用户已经登录
            $code = Yii::$service->helper->appserver->account_is_logined;
            $data = [ ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        $loginParam = \Yii::$app->getModule('customer')->params['login'];
        $loginCaptchaActive = isset($loginParam['loginPageCaptcha']) ? $loginParam['loginPageCaptcha'] : false;
        $googleRedirectUrl   = Yii::$app->request->get('googleRedirectUrl');
        $facebookRedirectUrl = Yii::$app->request->get('facebookRedirectUrl');
        
        $code = Yii::$service->helper->appserver->status_success;
        $data = [ 
            'loginCaptchaActive'=> $loginCaptchaActive,
            'googleLoginUrl'    => Yii::$service->customer->google->getLoginUrl($googleRedirectUrl,true),
            'facebookLoginUrl'  => Yii::$service->customer->facebook->getLoginUrl($facebookRedirectUrl,true),
        ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
        
        return $responseData;
        
    }
    
}