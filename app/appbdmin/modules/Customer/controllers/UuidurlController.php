<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appbdmin\modules\Customer\controllers;

use fbbcbase\app\appbdmin\modules\Customer\CustomerController;
use Yii;
use fec\helpers\CUrl;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class UuidurlController extends CustomerController
{
    public $enableCsrfValidation = true;
    public $store_uuid = 'store_uuid';
    
    public function actionGenerate()
    {
        $postParam = Yii::$app->request->post();
        
        $data = [
            'saveUrl' => CUrl::getUrl('customer/uuidurl/generate')
        ];
        if (!empty($postParam)) {
            $data['url'] = $postParam['url'];
            $uuid = Yii::$app->user->identity->uuid;
            if ($data['url']) {
                if (strstr($data['url'], '?')) {
                    $generateUrl = $data['url']. '&'.$this->store_uuid.'='.$uuid;
                } else {
                    $generateUrl = $data['url']. '?'.$this->store_uuid.'='.$uuid;
                }
            }
            $data['uuid_url'] = $generateUrl;
        }
        
        return $this->render($this->action->id, $data);
    }
    
}
