<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\console\modules\Statistics\controllers;

use Yii;
use yii\console\Controller;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class BdminmonthController extends Controller
{
    
    public $numPerPage = 1;
    
    public function actionOrdertotal($year)
    {
        echo $year;
    }
    
    public function actionInit(){
        
        Yii::$service->statistics->order->statisticsMonthBdminInit($bdmin_user_id, $year, $month);
    }
    
    public function actionGetyear(){
        $dateStr = date('Y-m-d',strtotime("-1 month"));
        
        echo  substr($dateStr, 0, 4);
    }
    
    public function actionGetmonth(){
        $dateStr = date('Y-m-d',strtotime("-1 month"));
        
        $str = substr($dateStr, 5, 2);
        if (substr($str, 0, 1) == '0') {
            $str = substr($str, 1, 1);
        }
        
        echo $str;
    }
    
    
    public function actionGetbdminuseridcount(){
        $filter = [
            'where' => [
               ['status' => Yii::$service->bdminUser->bdminUser->getActiveStatus()],
            ],
        ];
        $data = Yii::$service->bdminUser->bdminUser->coll($filter);
        
        echo $data['count'];
    }
    
    public function actionGetbdminuserid($pageNum) {
        $filter = [
            'where' => [
               ['status' => Yii::$service->bdminUser->bdminUser->getActiveStatus()],
            ],
            'numPerPage' 	=> 1,
     		'pageNum'		=> $pageNum,
        ];
        $data = Yii::$service->bdminUser->bdminUser->coll($filter);
        
        $coll = $data['coll'];
        
        echo $coll[0]['id'];
    }
    
    
    public function actionGetorderpagecount($bdmin_user_id, $year, $month)
    {
        echo Yii::$service->statistics->order->getMonthBdminCompleteOrderPageCount($bdmin_user_id, $year, $month);
        
    }
    
    public function actionInitstatisticsmonthbdmin($bdmin_user_id, $year, $month){
        Yii::$service->statistics->order->statisticsMonthBdminInit($bdmin_user_id, $year, $month);
    }
    
    public function actionStatisticsmonthbdmincompleteordertotal($bdmin_user_id, $year, $month, $pageNum){
        Yii::$service->statistics->order->statisticsMonthBdminCompleteOrderTotal($bdmin_user_id, $year, $month, $pageNum);
    }
    
    
    
    public function actionGetrefundpagecount($bdmin_user_id, $year, $month)
    {
        echo Yii::$service->statistics->order->getMonthBdminRefundPageCount($bdmin_user_id, $year, $month);
        
    }
    public function actionStatisticsmonthbdminrefundtotal($bdmin_user_id, $year, $month, $pageNum){
        Yii::$service->statistics->order->statisticsMonthBdminRefundTotal($bdmin_user_id, $year, $month, $pageNum);
    }
    
    public function actionMonthtotal($bdmin_user_id, $year, $month)
    {
        Yii::$service->statistics->order->statisticsBdminMonthTotal($bdmin_user_id, $year, $month);
        
    }
    
    
    public function actionGetbdrefundpagecount($bdmin_user_id, $year, $month)
    {
        echo Yii::$service->statistics->order->getBdMonthBdminRefundPageCount($bdmin_user_id, $year, $month);
        
    }
    public function actionBdstatisticsmonthbdminrefundtotal($bdmin_user_id, $year, $month, $pageNum){
        Yii::$service->statistics->order->bdStatisticsMonthBdminRefundTotal($bdmin_user_id, $year, $month, $pageNum);
    }
    
    
    
}
