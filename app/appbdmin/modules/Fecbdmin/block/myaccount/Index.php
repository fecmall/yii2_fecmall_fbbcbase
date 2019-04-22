<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
namespace fbbcbase\app\appbdmin\modules\Fecbdmin\block\myaccount;

use Yii;
use fec\helpers\CUrl;
use fec\helpers\CRequest;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Index{
	
	public function getLastData(){
		$data = CRequest::param("updatepass");
		if($data){
            // ajax update 
			$resetStatus = Yii::$service->bdminUser->bdminUser->resetCurrentPassword($data);
            if (!$resetStatus) {
                $errors = Yii::$service->helper->errors->get();
                echo  json_encode(["statusCode"=>"300",
					"message" => $errors,
				]);
            } else {
                echo  json_encode(["statusCode"=>"200",
					"message" => 'Update Password Success',
				]);
            }
            exit;
        }
        $bdminUser = \Yii::$app->user->identity;
		$current_account = $bdminUser->username;
		$editUrl = CUrl::getUrl("fecbdmin/myaccount/index");
        
		return [
			'current_account' => $current_account,
			'editUrl'			=> $editUrl,
		];
	}
	
}