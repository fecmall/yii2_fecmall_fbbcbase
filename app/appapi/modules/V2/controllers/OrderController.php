<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
namespace fbbcbase\app\appapi\modules\V2\controllers;

use fbbcbase\app\appapi\modules\AppapiTokenController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class OrderController extends AppapiTokenController
{
    public $numPerPage = 5;

    /**
     * begin_date 2019-04-08 00:00:00
     * end_date   2019-04-09 00:00:00
     * Get Lsit Api：得到Category 列表的api
     */
    public function actionWaitingdispatchlist(){

        $page = Yii::$app->request->get('page');
        $begin_date = Yii::$app->request->get('begin_date');
        $end_date = Yii::$app->request->get('end_date');
        $nowDate = date('Y-m-d', time());
        $tomorrowDate = date('Y-m-d', strtotime(' +1 days ')) ;
        $begin_date = $begin_date ? $begin_date : $nowDate . ' 00:00:00';
        $end_date = $end_date ? $end_date : $tomorrowDate . ' 00:00:00';
        $page = $page ? $page : 1;
        
        $begin_date_time = strtotime($begin_date);
        $end_date_time = strtotime($end_date);
        $identity = Yii::$app->user->identity;
        $filter = [
            'numPerPage'    => $this->numPerPage,
            'pageNum'       => $page,
            'asArray'       => true,
            'where'         => [
                ['bdmin_user_id' => $identity->id],
                ['>=', 'bdmin_audit_acceptd_at', $begin_date_time],
                ['<', 'bdmin_audit_acceptd_at', $end_date_time],
                ['in', 'order_status', Yii::$service->order->info->orderStatusCanDispatchArr],
                ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusCanDispatchArr],
            ],
        ];
        
        $data  = Yii::$service->order->getorderinfocoll($filter);
        $coll  = $data['coll'];
        if (is_array($coll) && !empty($coll)) {
            foreach ($coll as $k => $one) {
                // 处理mongodb类型
                if (isset($one['_id'])) {
                    $coll[$k]['id'] = (string)$one['_id'];
                    unset($coll[$k]['_id']);
                }
            }
        }
        $count = $data['count'];
        $pageCount = ceil($count / $this->numPerPage);
        $serializer = new \yii\rest\Serializer();
        Yii::$app->response->getHeaders()
            ->set($serializer->totalCountHeader, $count)
            ->set($serializer->pageCountHeader, $pageCount)
            ->set($serializer->currentPageHeader, $page)
            ->set($serializer->perPageHeader, $this->numPerPage);
        if ($page <= $pageCount  || $pageCount == 0) {
            return [
                'code'    => 200,
                'message' => 'fetch order success',
                'data'    => $coll,
            ];
        } else {
            return [
                'code'    => 400,
                'message' => 'fetch order fail , exceeded the maximum number of pages',
                'data'    => [],
            ];
        }
    }
    /**
     * Get One Api：根据url_key 和 id 得到Category 列表的api
     */
    public function actionDispatch(){
        $increment_id  = Yii::$app->request->post('increment_id');
        $tracking_number  = Yii::$app->request->post('tracking_number');
        
        $bdmin_user_id = Yii::$app->user->identity->id;
        
        try {    
            if (!Yii::$service->order->process->bdminDispatchOrderByIncrementId($increment_id, $tracking_number, $bdmin_user_id)) {
                throw new \Exception('dispatch order by increment id fail');
            }
            $innerTransaction->commit();
        } catch (\Exception $e) {
            $innerTransaction->rollBack();
            
            return [
                'code'    => 400,
                'message' => 'dispatch order by increment id fail',
                'data'    => [
                    'errors' => Yii::$service->helper->errors->get(),
                ],
            ];
        }
        
        return [
            'code'    => 200,
            'message' => 'dispatch order success',
            'data'    => [],
        ];
    }

}
