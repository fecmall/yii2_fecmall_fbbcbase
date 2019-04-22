<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appadmin\modules\Cms\block\baseinfo;

use fec\helpers\CRequest;
use fec\helpers\CUrl;
use fecshop\app\appadmin\interfaces\base\AppadminbaseBlockEditInterface;
use fecshop\app\appadmin\modules\AppadminbaseBlockEdit;
use Yii;

/**
 * block cms\article.
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Manager extends AppadminbaseBlockEdit implements AppadminbaseBlockEditInterface
{
    public $_saveUrl;
    // 需要配置
    public $_key;
    public $_type;
    protected $_attrArr = [
        'phone', 
    ];
    
    public function init()
    {
        $this->_type = Yii::$service->systemConfig->adminType;
        $this->_key = Yii::$service->systemConfig->baseInfoKey;
        if (!($this instanceof AppadminbaseBlockEditInterface)) {
            echo  json_encode([
                    'statusCode'=>'300',
                    'message'=>'Manager edit must implements fbbcbase\app\appbdmin\interfaces\base\AppbdminbaseBlockEditInterface',
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
