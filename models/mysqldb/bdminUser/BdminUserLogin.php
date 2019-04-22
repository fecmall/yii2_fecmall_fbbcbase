<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
namespace fbbcbase\models\mysqldb\bdminUser;
use fbbcbase\models\mysqldb\BdminUser;
use yii\base\Model;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class BdminUserLogin extends Model{
	
	public $username;
	public $password;
	public $captcha;
	private $_bdmin_user;
	public function rules()
    {
        return [
            [['username', 'password'], 'required'],
			['password', 'validatePassword'],
         //   ['captcha', 'captcha','captchaAction'=>'/fecadmin/captcha/index'],
		//	 ['captcha', 'required'],
        ];
    }
	
	public function validatePassword($attribute,$params){
		
		if (!$this->hasErrors()) {
            $bdminUser = $this->getBdminUser();
            if (!$bdminUser) {
                $this->addError('用户名', '用户名不存在');
            }else if(!$bdminUser->validatePassword($this->password)){
				$this->addError('用户名或密码','不正确');
			}
        }
	}
	
	
	public function getBdminUser(){
		if($this->_bdmin_user === null){
			$this->_bdmin_user = BdminUser::findByUsername($this->username);
		}
		return $this->_bdmin_user;
		
	}
	
	public function login()
    {
        if ($this->validate()) {
            //return \Yii::$app->user->login($this->getBdminUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
			return \Yii::$app->user->login($this->getBdminUser(), 3600 * 24);
        } else {
            return false;
        }
    }
}




