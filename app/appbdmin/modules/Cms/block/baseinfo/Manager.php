<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appbdmin\modules\Cms\block\baseinfo;

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
class Manager extends AppbdminbaseBlockEdit implements AppbdminbaseBlockEditInterface
{
    public $_saveUrl;
    // 需要配置
    public $_key;
    public $_type;
    protected $_attrArr = [
        'phone', 
        'warehouse',
        'default_warehouse'
    ];
    
    public function init()
    {
        $this->_type = Yii::$service->systemConfig->bdminType;
        $this->_key = Yii::$service->systemConfig->baseInfoKey;
        if (!($this instanceof AppbdminbaseBlockEditInterface)) {
            echo  json_encode([
                    'statusCode'=>'300',
                    'message'=>Yii::$service->page->translate->__('Manager edit must implements {file_patch}', ['file_patch' => 'fbbcbase\app\appbdmin\interfaces\base\AppbdminbaseBlockEditInterface']),
            ]);
            exit;
        }
         // 需要配置
        $this->_saveUrl = CUrl::getUrl('cms/baseinfo/managereditsave');
        $this->_editFormData = 'editFormData';
        $this->setService();
        $this->_param = CRequest::param();
        $this->_primaryKey = $this->_service->getPrimaryKey();
        $identity = Yii::$app->user->identity;
        $this->_one = $this->_service->getByKeyAndTypeAndUserId([
            'key' => $this->_key,
            'user_id' => $identity->id,
            'type' => $this->_type,
        ]);
        
    }
    
    
    

    // 传递给前端的数据 显示编辑form
    public function getLastData()
    {
        $_id = ''; 
        if (isset($this->_one['_id'])) {
           $_id = $this->_one['_id'];
        } 
        return [
            '_id'            =>   $_id, 
            'editBar'      => $this->getEditBar(),
            'textareas'   => $this->_textareas,
            'lang_attr'   => $this->_lang_attr,
            'saveUrl'     => $this->_saveUrl,
        ];
    }

    public function setService()
    {
        $this->_service = Yii::$service->systemConfig;
    }

    public function getEditArr()
    {
        $deleteStatus = Yii::$service->customer->getStatusDeleted();
        $activeStatus = Yii::$service->customer->getStatusActive();

        return [
             // 需要配置
            
            [
                'label'  => Yii::$service->page->translate->__('phone'),
                'name' => 'phone',
                'display' => [
                    'type' => 'inputString',
                ],
                'require' => 0,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('warehouse config'),
                'name' => 'warehouse',
                'display' => [
                    'type' => 'inputString',
                ],
                'require' => 0,
            ],
            [
                'label'  => Yii::$service->page->translate->__('default warehouse config'),
                'name' => 'default_warehouse',
                'display' => [
                    'type' => 'inputString',
                ],
                'require' => 0,
            ],
            
            
            
        ];
    }
    
    public function getArrParam(){
        $request_param = CRequest::param();
        $this->_param = $request_param[$this->_editFormData];
        $param = [];
        $attrVals = [];
        foreach($this->_param as $attr => $val) {
            if (in_array($attr, $this->_attrArr)) {
                $attrVals[$attr] = $val;
            } else {
                $param[$attr] = $val;
            }
        }
        $param['content'] = $attrVals;
        $identity = Yii::$app->user->identity;
        $param['user_id'] = $identity['id'];
        $param['key'] = $this->_key;
        $param['type'] = $this->_type;
        
        return $param;
    }
    
    /**
     * save article data,  get rewrite url and save to article url key.
     */
    public function save()
    {
        /*
         * if attribute is date or date time , db storage format is int ,by frontend pass param is int ,
         * you must convert string datetime to time , use strtotime function.
         */
        // 设置 bdmin_user_id 为 当前的user_id
        $this->_service->saveConfig($this->getArrParam());
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
    
    
    
    public function getVal($name, $column){
        if (is_object($this->_one) && property_exists($this->_one, $name) && $this->_one[$name]) {
            
            return $this->_one[$name];
        }
        $content = $this->_one['content'];
        if (is_array($content) && !empty($content) && isset($content[$name])) {
            
            return $content[$name];
        }
        
        return '';
    }

}
