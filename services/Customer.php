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

/**
 * BdminUser services. 用来给后台的用户提供数据。
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Customer extends \fecshop\services\Customer
{
    
    protected $_customerRegisterModelName = '\fbbcbase\models\mysqldb\customer\CustomerRegister';
    protected $_customerModelName = '\fbbcbase\models\mysqldb\Customer';
    protected $_customerLoginModelName = '\fecshop\models\mysqldb\customer\CustomerLogin';
    
    /**
     * Register customer account.
     * @param array $param
     * 数据格式如下：
     * ```php
     * [
     *      'email',
             'phone',
     *      'firstname',
     *      'lastname',
     *      'password'
     * ]
     * ```
     * @return bool whether the customer is registered ok
     */
    protected function actionRegister($param)
    {
        $model = $this->_customerRegisterModel;
        $model->attributes = $param;
        if ($model->validate()) {
            $model->created_at = time();
            $model->updated_at = time();
            
            $saveStatus = $model->save();
            if (!$saveStatus) {
                Yii::$service->helper->errors->add('identity is not right');
                return false;
            }
            
            
            // 发送注册信息到trace系统
            Yii::$service->page->trace->sendTraceRegisterInfoByApi($model->phone);
            return true;
        } else {
            $errors = $model->errors;
            Yii::$service->helper->errors->addByModelErrors($errors);

            return false;
        }
    }
    
    
    /**
     * Save the customer info.
     * @param array $param
     * 数据格式如下：
     * ['email' => 'xxx', 'password' => 'xxxx','firstname' => 'xxx','lastname' => 'xxx']
     * @return bool
     */
    protected function actionSave($param)
    {
        $primaryKey = $this->getPrimaryKey();
        $primaryVal = isset($param[$primaryKey]) ? $param[$primaryKey] : '';
        if ($primaryVal) {
            $model = $this->_customerRegisterModel;
            $model->attributes = $param;
            if (!$model->validate()) {
                $errors = $model->errors;
                Yii::$service->helper->errors->addByModelErrors($errors);
                
                return false;
            }
            $model = $this->getByPrimaryKey($primaryVal);
            if ($model[$primaryKey]) {
                unset($param[$primaryKey]);
                $param['updated_at'] = time();
                $password = isset($param['password']) ? $param['password'] : '';
                if ($password) {
                    $model->setPassword($password);
                    unset($param['password']);
                }
                
                $saveStatus = Yii::$service->helper->ar->save($model, $param);
                if ($saveStatus) {
                    
                    return true;
                } else {
                    $errors = $model->errors;
                    Yii::$service->helper->errors->addByModelErrors($errors);

                    return false;
                }
            }
        } else {
            if ($this->register($param)) {
                return true;
            }
        }
        return false;
    }
    
    
    /**
     * @param array $data
     *
     * example:
     *
     * ```php
     * $data = ['email' => 'user@example.com', 'password' => 'your password'];
     * $loginStatus = \Yii::$service->customer->login($data);
     * ```
     *
     * @return bool
     */
    protected function actionLogin($data)
    {
        $model = $this->_customerLoginModel;
        $model->password = $data['password'];
        $model->email = $data['email'];
        $loginStatus = $model->login();
        $errors = $model->errors;
        if (empty($errors)) {
            // 合并购物车数据
            Yii::$service->cart->mergeCartAfterUserLogin();
            // 发送登录信息到trace系统
            Yii::$service->page->trace->sendTraceLoginInfoByApi($data['email']);
        } else {
            Yii::$service->helper->errors->addByModelErrors($errors);
        }

        return $loginStatus;
    }
    
    /** AppServer 部分使用的函数
     * @param $email | String
     * @param $password | String
     * 无状态登录，通过email 和password进行登录
     * 登录成功后，合并购物车，返回accessToken
     * ** 该函数是未登录用户，通过参数进行登录需要执行的函数。
     */
    protected function actionLoginAndGetAccessToken($email, $password)
    {
        $header = Yii::$app->request->getHeaders();
        if (isset($header['access-token']) && $header['access-token']) {
            $accessToken = $header['access-token'];
        }
        // 如果request header中有access-token，则查看这个 access-token 是否有效
        if ($accessToken) {
            $identity = Yii::$app->user->loginByAccessToken($accessToken);
            if ($identity !== null) {
                $access_token_created_at = $identity->access_token_created_at;
                $timeout = Yii::$service->session->timeout;
                if ($access_token_created_at + $timeout > time()) {
                    return $accessToken;
                }
            }
        }
        // 如果上面access-token不存在
        $data = [
            'email'     => $email,
            'password'  => $password,
        ];
        
        if (Yii::$service->customer->login($data)) {
            $identity = Yii::$app->user->identity;
            $identity->generateAccessToken();
            $identity->access_token_created_at = time();
            $identity->save();
            // 执行购物车合并等操作。
            Yii::$service->cart->mergeCartAfterUserLogin();
            $this->setHeaderAccessToken($identity->access_token);
            return $identity->access_token;
        }
    }

    
}