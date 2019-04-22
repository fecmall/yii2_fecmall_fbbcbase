<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appadmin\modules\Sales\controllers;

use fecshop\app\appadmin\modules\Sales\SalesController;
use Yii;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class OrderinfoController extends \fecshop\app\appadmin\modules\Sales\controllers\OrderinfoController
{
    public $enableCsrfValidation = true;
    public $blockNamespace = 'fbbcbase\\app\\appadmin\\modules\\Sales\\block';
    
    public function actionManager()
    {
        $data = $this->getBlock()->getLastData();

        return $this->render($this->action->id, $data);
    }
    
    public function actionManageredit()
    {
        $data = $this->getBlock()->getLastData();

        return $this->render($this->action->id, $data);
    }
    
    public function actionGetstate()
    {
        $customer_address_country = Yii::$app->request->get('country');
        $customer_address_state   = Yii::$app->request->get('state');
        $stateOption = Yii::$service->helper->country->getStateOptionsByContryCode($customer_address_country,$customer_address_state);
        echo json_encode([
            'status' => 'success',
            'content'=> $stateOption,
        ]);
        exit;
    }

    
    public function actionManagereditsave()
    {
        $data = $this->getBlock("manageredit")->save();
    }
    /*
    public function actionManagerdelete()
    {
        $this->getBlock("manageredit")->delete();
    }
    */
    
    public function actionManagerexport()
    {
        $data = $this->getBlock("manageredit")->exportExcel();
        
    }
}
