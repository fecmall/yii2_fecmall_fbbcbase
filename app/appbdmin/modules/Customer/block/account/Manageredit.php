<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appbdmin\modules\Customer\block\account;

use fec\helpers\CRequest;
use fec\helpers\CUrl;
use fbbcbase\app\appbdmin\interfaces\base\AppbdminbaseBlockEditInterface;
use fbbcbase\app\appbdmin\modules\AppbdminbaseBlockEdit;
use Yii;

/**
 * block cms\article.
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Manageredit extends AppbdminbaseBlockEdit implements AppbdminbaseBlockEditInterface
{
    public $_saveUrl;

    public function init()
    {
        $this->_saveUrl = CUrl::getUrl('customer/account/managereditsave');
        parent::init();
    }

    // 传递给前端的数据 显示编辑form
    public function getLastData()
    {
        $bdmin_user_id = Yii::$app->user->identity->id; 
        Yii::$service->helper->setProductBdminUserId($bdmin_user_id); 
        return [
            'editBar'      => $this->getEditBar(),
            'textareas'   => $this->_textareas,
            'lang_attr'   => $this->_lang_attr,
            'saveUrl'     => $this->_saveUrl,
            'warehouses' => $this->getWarehouseArr(),
            'selectWarehouses' => $this->getSelectWarehouses(),
        ];
    }
    
    public function getSelectWarehouses()
    {
        $warehouses = $this->_one->warehouses;
        $warehouseArr = explode(',', $warehouses);
        
        return $warehouseArr;
    }
    
    public function getWarehouseArr(){
        $arr = Yii::$service->helper->getbdminWarehouseList();
        
        return $arr ? $arr : [];
    }

    public function setService()
    {
        $this->_service = Yii::$service->customer;
    }

    public function getEditArr()
    {
        $deleteStatus = Yii::$service->customer->getStatusDeleted();
        $activeStatus = Yii::$service->customer->getStatusActive();

        return [
            
        ];
    }
    
    /**
     * save article data,  get rewrite url and save to article url key.
     */
    public function save()
    {
        $request_param = CRequest::param();
        $this->_param = $request_param[$this->_editFormData];
        /*
         * if attribute is date or date time , db storage format is int ,by frontend pass param is int ,
         * you must convert string datetime to time , use strtotime function.
         */
        // 设置 bdmin_user_id 为 当前的user_id
        if ($this->_param['warehouses']) {
            $this->_param['warehouses'] = implode(',', $this->_param['warehouses']);
        }
        $identity = Yii::$app->user->identity;
        $this->_param['bdmin_user_id'] = $identity['id'];
        $this->_service->save($this->_param);
        $errors = Yii::$service->helper->errors->get();
        if (!$errors) {
            echo  json_encode([
                'statusCode' => '200',
                'message'    => Yii::$service->page->translate->__('Save Success'),
            ]);
            exit;
        } else {
            echo  json_encode([
                'statusCode' => '300',
                'message'    => $errors,
            ]);
            exit;
        }
    }

}
