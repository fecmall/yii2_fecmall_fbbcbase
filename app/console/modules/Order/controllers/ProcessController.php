<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\console\modules\Order\controllers;

use Yii;
use yii\console\Controller;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class ProcessController extends Controller
{
    public $numPerPage = 20;
    
    public function actionAutoreceiveorderpagecount()
    {
        $data = $this->getOrderData();
        $count = $data['count'];
        
        echo ceil($count / $this->numPerPage);
    }
    
    
    public function actionAutoreceiveorder($pageNum)
    {
        $data = $this->getOrderData($pageNum);
        $coll = $data['coll'];
        if (is_array($coll ) && !empty($coll )) {
            foreach ($coll  as $one) {
                $autoReceiveStatus = Yii::$service->order->process->autoReceiveOrderById($one['order_id']);
            }
        }
    }
    
    
    protected function getOrderData($pageNum = 1){
        $filter = [
            'asArray' => true,
            'select'  => ['order_id'],
            'pageNum'		=> $pageNum,
            'where' => [
                ['in', 'order_status', Yii::$service->order->info->orderStatusCanReceivedArr ],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusCanReceivedArr ],
            ]
        ];
        $data = Yii::$service->order->coll($filter);
        
        return $data;
    }
}
