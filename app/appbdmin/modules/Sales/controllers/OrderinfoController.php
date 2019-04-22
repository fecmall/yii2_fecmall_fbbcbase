<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appbdmin\modules\Sales\controllers;

use fbbcbase\app\appbdmin\modules\Sales\SalesController;
use Yii;
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class OrderinfoController extends SalesController
{
    public $enableCsrfValidation = true;
    
    public function actionManager()
    {
        $data = $this->getBlock()->getLastData();

        return $this->render($this->action->id, $data);
    }
    
    public function actionManageredit()
    {
        $primaryKey = Yii::$service->order->getPrimaryKey();
        $order_id = Yii::$app->request->get($primaryKey);
        $this->orderHasRole($order_id);
        
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id, $data);
    }
    
    /**
     * 当前用户是否有操作该产品的权限
     */
    public function orderHasRole($order_id){
        $order = Yii::$service->order->getByPrimaryKey($order_id);
        $order_bdmin_user_id = isset($order['bdmin_user_id']) ? $order['bdmin_user_id'] : '';
        $identity = Yii::$app->user->identity;
        $currentUserId = $identity->id;
        if (!$order_bdmin_user_id || $currentUserId != $order_bdmin_user_id) {
            echo  json_encode([
                'statusCode' => '300',
                'message' => Yii::$service->page->translate->__('You do not have role to edit this order'),
            ]);
            exit;
        }
        unset($order);
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
        $primaryKey = Yii::$service->order->getPrimaryKey();
        $editForm = Yii::$app->request->post('editForm');
        $order_id = isset($editForm[$primaryKey]) ? $editForm[$primaryKey] : '';
        
        $this->orderHasRole($order_id);
        
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
        
        $order_ids = Yii::$app->request->post('order_ids');
        $order_arr = explode(',', $order_ids);
        if (!empty($order_arr) && is_array($order_arr)) {
            foreach ($order_arr as $order_id) {
                $this->orderHasRole($order_id);
            }
        }
        $data = $this->getBlock("manageredit")->exportExcel();
        
    }
}
