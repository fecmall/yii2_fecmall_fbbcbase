<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appserver\modules\Customer\controllers;

use fecshop\app\appserver\modules\AppserverController;
use Yii;
use \Firebase\JWT\JWT;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class RegisterController extends \fecshop\app\appserver\modules\Customer\controllers\RegisterController
{
    public $enableCsrfValidation = false ;
    public $_errors;
    
     public function register($param)
    {
        
        Yii::$service->customer->register($param);
        $errors = Yii::$service->helper->errors->get(',');
        if (!$errors) {
            // 发送注册邮件
            $this->sendRegisterEmail($param);

            return true;
        }else{
            $this->_errors[] = $errors;
            
            return false;
        }
    }

    /**
     * 发送登录邮件.
     */
    public function sendRegisterEmail($param)
    {
        if ($param) {
            //Email::sendRegisterEmail($param);
            Yii::$service->email->customer->sendRegisterEmail($param);
        }
    }
    
    public function validateParam2($email,$phone,$password,$firstname,$lastname,$captcha){
        $minNameLength = Yii::$service->customer->getRegisterNameMinLength();
        $maxNameLength = Yii::$service->customer->getRegisterNameMaxLength();
        $minPassLength = Yii::$service->customer->getRegisterPassMinLength();
        $maxPassLength = Yii::$service->customer->getRegisterPassMaxLength();
            
        $registerParam = \Yii::$app->getModule('customer')->params['register'];
        $registerPageCaptcha = isset($registerParam['registerPageCaptcha']) ? $registerParam['registerPageCaptcha'] : false;
        $errorArr = [];
        // 如果开启了验证码，但是验证码验证不正确就报错返回。
        if ($registerPageCaptcha && !$captcha) {
            $errorArr[] = 'Captcha can not empty';
        } elseif ($captcha && $registerPageCaptcha && !\Yii::$service->helper->captcha->validateCaptcha($captcha)) {
            $errorArr[] = 'Captcha is not right';
        } elseif (!$email) {
            $errorArr[] = 'email can not empty';
        } elseif (!$phone) {
            $errorArr[] = 'phone can not empty';
        } elseif (!$password) {
            $errorArr[] = 'password can not empty';
        } elseif (strlen($password) < $minPassLength || strlen($password) > $maxPassLength) {
            $errorArr[] = 'password must >= '.$minPassLength.' and <= '.$maxPassLength;
        } elseif (strlen($firstname) < $minNameLength || strlen($firstname) > $maxNameLength) {
            $errorArr[] = 'firstname must >= '.$minPassLength.' and <= '.$maxPassLength;
        //} elseif (strlen($lastname) < $minNameLength || strlen($lastname) > $maxNameLength) {
        //    $errorArr[] = 'lastname must >= '.$minPassLength.' and <= '.$maxPassLength;
        }
        if (!empty($errorArr)) {
            return implode(',',$errorArr);
        } else {
            return true;
        }
    }
    
    
    
    public function actionAccount(){
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $identity = Yii::$service->customer->loginByAccessToken(get_class($this));
        if($identity){
            $code = Yii::$service->helper->appserver->account_is_logined;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        $phone      = Yii::$app->request->post('phone');
        $email      = Yii::$app->request->post('email');
        $password   = Yii::$app->request->post('password');
        $firstname  = Yii::$app->request->post('firstname');
        $lastname   = Yii::$app->request->post('lastname');
        $captcha    = Yii::$app->request->post('captcha');
        $is_subscribed = Yii::$app->request->post('is_subscribed');
        $is_subscribed = $is_subscribed ? 1 : 2;
        $errorInfo = $this->validateParam2($email,$phone,$password,$firstname,$lastname,$captcha);
        if($errorInfo !== true){
            $code = Yii::$service->helper->appserver->account_register_invalid_data;
            $data = [
                'error' => $errorInfo,
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        
        $param['email']         = $email;
        $param['phone']         = $phone;
        $param['password']      = $password;
        $param['firstname']     = $firstname;
        $param['lastname']      = $lastname;
        $param['is_subscribed'] = $is_subscribed;
        if ($bdmin_user_id = Yii::$service->helper->getGuestUrlParamRelateBdminUserId()) {
            $param['bdmin_user_id'] = $bdmin_user_id;
        } 
        if (!empty($param) && is_array($param)) {
            $param = \Yii::$service->helper->htmlEncode($param);
            $registerStatus = $this->register($param);
            if ($registerStatus) {
                $params_register = Yii::$app->getModule('customer')->params['register'];
                $redirect = '/customer/account/login';
                // 注册成功后，是否自动登录
                if (isset($params_register['successAutoLogin']) && $params_register['successAutoLogin']) {
                    $accessToken = Yii::$service->customer->loginAndGetAccessToken($phone,$password);
                    if($accessToken){
                        $redirect = '/customer/account/index';
                    }
                    // 查看是否需要验证bdmin_user_id 是否为空
                    if (Yii::$service->helper->isCustomerLoginCheckBdminUserId()) {
                        $identity = Yii::$app->user->identity;
                        $bdmin_user_id = $identity['bdmin_user_id'];
                        if (empty($bdmin_user_id)) {
                            Yii::$service->customer->logoutByAccessToken();
                            $code = Yii::$service->helper->appserver->account_login_bdmin_user_id_is_empty;
                            $data = [
                                'content' => 'bdmin user id is empty',
                                //'redirect' => $redirect,
                            ];
                            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
                            
                            return $responseData;
                        }
                    }
                }
                
                $code = Yii::$service->helper->appserver->status_success;
                $data = [
                    'content' => 'register success',
                    //'redirect' => $redirect,
                ];
                $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
                
                return $responseData;
            
            }else{
                $code = Yii::$service->helper->appserver->account_register_fail;
                $data = [
                    'error' => implode(',',$this->_errors),
                ];
                $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
                
                return $responseData;
            }
        }
        
    }
    
    /**
     * register页面
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
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        $registerParam = \Yii::$app->getModule('customer')->params['register'];
        $registerPageCaptcha = isset($registerParam['registerPageCaptcha']) ? $registerParam['registerPageCaptcha'] : false;

        $code = Yii::$service->helper->appserver->status_success;
        $data = [
            'minNameLength' => Yii::$service->customer->getRegisterNameMinLength(),
            'maxNameLength' => Yii::$service->customer->getRegisterNameMaxLength(),
            'minPassLength' => Yii::$service->customer->getRegisterPassMinLength(),
            'maxPassLength' => Yii::$service->customer->getRegisterPassMaxLength(),
            'registerCaptchaActive' => $registerPageCaptcha,
        ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
        
        return $responseData;
    }
    
}