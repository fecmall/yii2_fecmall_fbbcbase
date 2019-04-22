<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
namespace fbbcbase\models\mysqldb\bdminUser;
use Yii;
use fbbcbase\models\mysqldb\BdminUser;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class BdminUserResetPassword extends BdminUser{
	
	public $username;
	public $old_password;
	public $new_password;
	public $password_repeat;
	private $_bdmin_user;
	
	public function rules()
    {
        return [
            [['old_password', 'new_password','password_repeat'], 'required'],
		//	['username', 'validateLogin'],
			['new_password', 'validateNewPassword'],
            ['old_password', 'validateOldPassword'],
        ];
    }
	
	public function getBdminUser(){
		if($this->_bdmin_user === null){
			$this->_bdmin_user = Yii::$app->user->identity;
		}
		return $this->_bdmin_user;
	}
	
	
	public function updatePassword(){
		$bdminUser = $this->getBdminUser();
		$bdminUser->setPassword($this->new_password);
		$bdminUser->save();
        
        return true;
	}
	
	
	public function validateNewPassword($attribute,$params){
		
		if (!$this->hasErrors()) {
			
			if($this->new_password != $this->password_repeat){
				$this->addError($attribute, 'Password and PasswordRepeat is Inconsistent!');
				return;
			}
			
        }
	}
	
	public function validateOldPassword($attribute,$params){
		
		if (!$this->hasErrors()) {
			$username = $this->getBdminUser()->username;
			$bdminUser = BdminUser::findByUsername($username);
			if($bdminUser->validatePassword($this->old_password)){
				
			}else{
				$this->addError($attribute, 'old password is not right!');
			}
        }
	}
	


}
