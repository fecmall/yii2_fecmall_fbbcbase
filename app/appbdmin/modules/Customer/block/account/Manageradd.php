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
class Manageradd extends AppbdminbaseBlockEdit implements AppbdminbaseBlockEditInterface
{
    public $_saveUrl;

    public function init()
    {
        $this->_saveUrl = CUrl::getUrl('customer/account/manageraddsave');
        parent::init();
    }

    // 传递给前端的数据 显示编辑form
    public function getLastData()
    {
        return [
            'editBar'      => $this->getEditBar(),
            'textareas'   => $this->_textareas,
            'lang_attr'   => $this->_lang_attr,
            'saveUrl'     => $this->_saveUrl,
        ];
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
            [
                'label'  => Yii::$service->page->translate->__('First Name'),
                'name' => 'firstname',
                'display' => [
                    'type' => 'inputString',

                ],
                'require' => 0,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Email'),
                'name' => 'email',
                'display' => [
                    'type' => 'inputString',
                ],
                'require' => 1,
            ],
            [
                'label'  => Yii::$service->page->translate->__('Phone'),
                'name' => 'phone',
                'display' => [
                    'type' => 'inputString',
                ],
                'require' => 1,
            ],
            [
                'label'  => Yii::$service->page->translate->__('Password'),
                'name' => 'password',
                'display' => [
                    'type' => 'inputPassword',
                ],
                'require' => 0,
            ],
            [
                'label'  => Yii::$service->page->translate->__('Status'),
                'name' => 'status',
                'display' => [
                    'type' => 'select',
                    'data' => [
                        $activeStatus    => Yii::$service->page->translate->__('Enable'),
                        $deleteStatus    => Yii::$service->page->translate->__('Disable'),
                    ],
                ],
                'require' => 1,
                'default' => 1,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Customer Bank'),
                'name' => 'customer_bank',
                'display' => [
                    'type' => 'inputString',
                ],
            ],
            [
                'label'  => Yii::$service->page->translate->__('Customer Bank Name'),
                'name' => 'customer_bank_name',
                'display' => [
                    'type' => 'inputString',
                ],
            ],
            [
                'label'  => Yii::$service->page->translate->__('Customer Bank Name'),
                'name' => 'customer_bank_name',
                'display' => [
                    'type' => 'inputString',
                ],
            ],
            
        ];
    }
    
    
    public function getActiveSupplierArr(){
        $arr = [];
        $supplierArr = Yii::$service->bdminUser->getAllActiveUser();
        if (is_array($supplierArr ) && !empty($supplierArr )) {
            foreach ($supplierArr  as $one) {
                $arr[$one['id']] = $one['username'];
            }
        }
        return $arr;
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
        $identity = Yii::$app->user->identity;
        $bdmin_user_id = $identity->id;
        $this->_param['bdmin_user_id'] = $bdmin_user_id;
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

    // 批量删除
    public function delete()
    {
        $ids = '';
        if ($id = CRequest::param($this->_primaryKey)) {
            $ids = $id;
        } elseif ($ids = CRequest::param($this->_primaryKey.'s')) {
            $ids = explode(',', $ids);
        }
        $this->_service->remove($ids);
        $errors = Yii::$service->helper->errors->get();
        if (!$errors) {
            echo  json_encode([
                'statusCode' => '200',
                'message'    => Yii::$service->page->translate->__('Remove Success'),
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
