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
class ReturnlistController extends SalesController
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
    
    public function actionManageraccept(){
        $ids = Yii::$app->request->post('ids');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $as_id ) {
            $innerTransaction = Yii::$app->db->beginTransaction();
            try { 
                if (!Yii::$service->order->afterSale->bdminReceiveReturnByAsId($as_id)) {
                    throw new \Exception('receive return accept fail');
                }
                $innerTransaction->commit();
            } catch (\Exception $e) {
                $innerTransaction->rollBack();
                
                echo  json_encode([
                    'statusCode' => '300',
                    'message' => Yii::$service->page->translate->__('receive return accept fail'),
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
    
    public function actionManagerrefuse(){
        $ids = Yii::$app->request->post('ids');
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $as_id ) {
            $innerTransaction = Yii::$app->db->beginTransaction();
            try { 
                if (!Yii::$service->order->afterSale->bdminAuditRefuseReturnByAsId($as_id)) {
                    throw new \Exception('audit return refuse fail');
                }
                $innerTransaction->commit();
            } catch (\Exception $e) {
                $innerTransaction->rollBack();
                
                echo  json_encode([
                    'statusCode' => '300',
                    'message' => Yii::$service->page->translate->__('audit return refuse fail'),
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
