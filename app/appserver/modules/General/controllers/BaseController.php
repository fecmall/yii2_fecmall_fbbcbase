<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appserver\modules\General\controllers;

//use fecshop\app\appserver\modules\AppserverController;
use Yii;
 
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0 
 */
class BaseController extends \fecshop\app\appserver\modules\General\controllers\BaseController
{
    public function actionMenu()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $arr = [];
        $displayHome = Yii::$service->page->menu->displayHome;
        $firstDisplay = 0;
        /*
        if($displayHome['enable']){
            $home = $displayHome['display'] ? $displayHome['display'] : 'Home';
            $home = Yii::$service->page->translate->__($home);
            $arr['home'] = [
                '_id'   => 'home',
                'current' => 'current',
                'level' => 1,
                'name'  => $home,
                'url'   => '/'
            ];
            $firstDisplay = 1;
        }
        */
        $currentLangCode = Yii::$service->store->currentLangCode;
        $treeArr = Yii::$service->category->getTreeArr('',$currentLangCode,true);
        if (is_array($treeArr)) {
            foreach ($treeArr as $k=>$v) {
                if (!$firstDisplay) {
                    $v['current'] = 'current';
                    $firstDisplay = 1;
                } else {
                    $v['current'] = '';
                }
                if (is_array($v['child'])) {
                    foreach ($v['child'] as $k1=>$v1) {
                        $v['child'][$k1]['current'] = 'current';
                        break;
                    }
                }
                
                $arr[$k] = $v ;
            }
        }
        
        $code = Yii::$service->helper->appserver->status_success;
        $data = [
            'menu_list'     => $arr,
        ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
    }
    
}