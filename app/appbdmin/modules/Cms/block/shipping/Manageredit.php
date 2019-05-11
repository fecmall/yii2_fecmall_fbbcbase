<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appbdmin\modules\cms\block\shipping;

use fec\helpers\CRequest;
use fec\helpers\CUrl;
use fbbcbase\app\appbdmin\interfaces\base\AppbdminbaseBlockEditInterface;
use fbbcbase\app\appbdmin\modules\AppbdminbaseBlockEdit;
use Yii;

/**
 * block cms\staticblock.
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Manageredit extends AppbdminbaseBlockEdit implements AppbdminbaseBlockEditInterface
{
    public $_saveUrl;

    public function init()
    {
        $this->_saveUrl = CUrl::getUrl('cms/shipping/managereditsave');
        parent::init();
    }

    // 传递给前端的数据 显示编辑form
    public function getLastData()
    {
        return [
            'editBar'      => $this->getEditBar(),
            'textareas'   => $this->_textareas,
            'lang_attr'   => $this->_lang_attr,
            'saveUrl'      => $this->_saveUrl,
        ];
    }

    public function setService()
    {
        $this->_service = Yii::$service->bdminUser->shipping;
    }

    public function getEditArr()
    {
        return [
            /*
            [
                'label'  => Yii::$service->page->translate->__('Shipping Code'),
                'name' => 'code',
                'display' => [
                    'type' => 'inputString',
                    'lang' => false,
                ],
                'require' => 1,
            ],
            */
            [
                'label'  => Yii::$service->page->translate->__('Shipping Label'),
                'name' => 'label',
                'display' => [
                    'type' => 'inputString',
                    'lang' => true,
                ],
                'require' => 1,
            ],
            
            
            [
                'label'  => Yii::$service->page->translate->__('Shipping Type'),
                'name' => 'type',
                'display' => [
                    'type' => 'select',
                    'data' => [
                        Yii::$service->bdminUser->shipping->type_cost_bdmin    => Yii::$service->page->translate->__(Yii::$service->bdminUser->shipping->type_cost_bdmin),
                        Yii::$service->bdminUser->shipping->type_cost_customer   => Yii::$service->page->translate->__(Yii::$service->bdminUser->shipping->type_cost_customer),
                    ],
                ],
                'require' => 1,
                'default' => 1,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Shipping First Weight'),
                'name' => 'first_weight',
                'display' => [
                    'type' => 'inputString',
                    'lang' => false,
                ],
                'require' => 0,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Shipping First Cost'),
                'name' => 'first_cost',
                'display' => [
                    'type' => 'inputString',
                    'lang' => false,
                ],
                'require' => 0,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Shipping Next Weight'),
                'name' => 'next_weight',
                'display' => [
                    'type' => 'inputString',
                    'lang' => false,
                ],
                'require' => 0,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Shipping Next Cost'),
                'name' => 'next_cost',
                'display' => [
                    'type' => 'inputString',
                    'lang' => false,
                ],
                'require' => 0,
            ],
            
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
        $this->_param['first_weight'] = (float)$this->_param['first_weight'];
        $this->_param['first_cost'] = (float)$this->_param['first_cost'];
        $this->_param['next_weight'] = (float)$this->_param['next_weight'];
        $this->_param['next_cost'] = (float)$this->_param['next_cost'];
        $identity = Yii::$app->user->identity;
        $this->_param['bdmin_user_id'] = $identity->id;
        $this->_service->save($this->_param, 'cms/article/index');
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
                'message'    => Yii::$service->page->translate->__('Remove Success') ,
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
