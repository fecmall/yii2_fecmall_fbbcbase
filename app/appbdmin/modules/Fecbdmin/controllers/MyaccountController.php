<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
namespace fbbcbase\app\appbdmin\modules\Fecbdmin\controllers;
use Yii;
use fec\helpers\CRequest;
use fecadmin\FecadminbaseController;
use fbbcbase\app\appbdmin\modules\AppbdminController;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class MyaccountController extends AppbdminController
{
	public $enableCsrfValidation = true;
    public $blockNamespace = 'fbbcbase\\app\\appbdmin\\modules\\Fecbdmin\\block';
    
    # 我的账户
    public function actionIndex()
    {
		$data = $this->getBlock()->getLastData();
		return $this->render($this->action->id,$data);
	}
	
}








