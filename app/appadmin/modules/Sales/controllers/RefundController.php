<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appadmin\modules\Sales\controllers;

use fecshop\app\appadmin\modules\Sales\SalesController;
use Yii;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class RefundController extends SalesController
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
        $primaryKey = Yii::$service->refund->getPrimaryKey();
        $refund_id = Yii::$app->request->get($primaryKey);
        //$this->orderHasRole($refund_id);
        
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id, $data);
    }
    
    public function actionManagereditsave()
    {
          
        $primaryKey = Yii::$service->refund->getPrimaryKey();
        $editFormData = Yii::$app->request->post('editFormData');
        $refund_id = $editFormData['id'];
        //$this->orderHasRole($refund_id);
        
        $data = $this->getBlock('manageredit')->save();
    }
    
    public function actionManageraccept(){
        $ids = Yii::$app->request->post('ids');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $refund_id ) {
            $innerTransaction = Yii::$app->db->beginTransaction();
            try { 
                if (!Yii::$service->refund->payReturnRefund($refund_id, 'admin')) {
                    throw new \Exception('pay refund fail');
                }
                $innerTransaction->commit();
            } catch (\Exception $e) {
                $innerTransaction->rollBack();
                
                echo  json_encode([
                    'statusCode' => '300',
                    'message' => Yii::$service->page->translate->__('pay refund fail'),
                ]);
                exit;
            }
        }
        echo  json_encode([
            'statusCode' => '200',
            'message' => Yii::$service->page->translate->__('Save Success'),
        ]);
        exit;
    }
}
