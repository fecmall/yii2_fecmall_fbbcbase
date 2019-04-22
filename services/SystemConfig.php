<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\services;

use Yii;
use fecshop\services\Service;

/**
 * Order services.
 *
 * @property \fecshop\services\order\Item $item
 *
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class SystemConfig extends Service
{
    
    protected $_modelName = '\fbbcbase\models\mongodb\SystemConfig';

    protected $_model;
    
    public $adminType = 'admin';
    public $bdminType = 'bdmin';
    
    public $homePageKey = 'home_page';
    public $baseInfoKey = 'base_info';
    
    public function init()
    {
        parent::init();
        list($this->_modelName, $this->_model) = \Yii::mapGet($this->_modelName);
    }
    
    /**
     * 得到order 表的id字段。
     */
    protected function actionGetPrimaryKey()
    {
        return '_id';
    }
    
    /**
     * @param $primaryKey | Int
     * @return Object($this->_orderModel)
     * 通过主键值，返回Order Model对象
     */
    protected function actionGetByPrimaryKey($primaryKey)
    {
        $one = $this->_model->findOne($primaryKey);
        $primaryKey = $this->getPrimaryKey();
        if ($one[$primaryKey]) {
            return $one;
        } else {
            return new $this->_modelName();
        }
    }
    /**
     * @param $whereArr | Array , like: 
     *     [
     *         'key' => $this->_key,
     *         'user_id' => $identity->id,
     *         'type' => $this->_type,
     *     ]
     * @return Object($this->_orderModel)
     * 通过主键值，返回Model对象
     */
    public function getByKeyAndTypeAndUserId($whereArr){
        $one = $this->_model->findOne($whereArr);
        $primaryKey = $this->getPrimaryKey();
        if ($one[$primaryKey]) {
            
            return $one;
        }
        
        return null;
    }
    
    /**
     * @param $filter|array
     * @return Array;
     *              通过过滤条件，得到coupon的集合。
     *              example filter:
     *              [
     *                  'numPerPage' 	=> 20,
     *                  'pageNum'		=> 1,
     *                  'orderBy'	    => ['_id' => SORT_DESC, 'sku' => SORT_ASC ],
     *                  'where'			=> [
     *                      ['>','price',1],
     *                      ['<=','price',10]
     * 			            ['sku' => 'uk10001'],
     * 		            ],
     * 	                'asArray' => true,
     *              ]
     * 根据$filter 搜索参数数组，返回满足条件的订单数据。
     */
    protected function actionColl($filter = '')
    {
        $query  = $this->_model->find();
        $query  = Yii::$service->helper->ar->getCollByFilter($query, $filter);
        $coll   = $query->all();
        
        return [
            'coll' => $coll,
            'count'=> $query->limit(null)->offset(null)->count(),
        ];
    }
    
    /**
     * Save the customer info.
     * @param array $param
     * 数据格式如下：
     * ['email' => 'xxx', 'password' => 'xxxx','firstname' => 'xxx','lastname' => 'xxx']
     * @param type | string,  `admin` or `bdmin`
     * @return bool
     * mongodb save system config
     */
    protected function actionSave($param, $type)
    {
        $primaryKey = $this->getPrimaryKey();
        $primaryVal = isset($param[$primaryKey]) ? $param[$primaryKey] : '';
        $model = $this->_model;
        $model->attributes = $param;
        // 验证数据。
        if (!$model->validate()) {
            $errors = $model->errors;
            Yii::$service->helper->errors->addByModelErrors($errors);
            
            return false;
        }
            
        if ($primaryVal) {
            $model = $this->getByPrimaryKey($primaryVal);
            if (!$model[$primaryKey]) {
                Yii::$service->helper->errors->add('Static block {primaryKey} is not exist', ['primaryKey' => $this->getPrimaryKey()]);

                return false;
            } 
        } else {
            $model = new $this->_modelName();
            $model->created_at = time();
            $identity = Yii::$app->user->identity;
            $primaryVal = new \MongoDB\BSON\ObjectId();
            $model->{$this->getPrimaryKey()} = $primaryVal;
        }
        
        $model->updated_at = time();
        unset($param['_id']);
        $param['type'] = $type;
        $saveStatus = Yii::$service->helper->ar->save($model, $param);
        if ($saveStatus) {
            
            return true;
        } else {
            $errors = $model->errors;
            Yii::$service->helper->errors->addByModelErrors($errors);

            return false;
        }
    }
    
    
    /**
     * Save the customer info.
     * @param array $param
     * 数据格式如下：
     * ['email' => 'xxx', 'password' => 'xxxx','firstname' => 'xxx','lastname' => 'xxx']
     * @param type | string,  `admin` or `bdmin`
     * @return bool
     * mongodb save system config
     */
    protected function actionSaveConfig($param)
    {
        $primaryKey = $this->getPrimaryKey();
        $primaryVal = isset($param[$primaryKey]) ? $param[$primaryKey] : '';
        $model = $this->_model;
        $model->attributes = $param;
        // 验证数据。
        if (!$model->validate()) {
            $errors = $model->errors;
            Yii::$service->helper->errors->addByModelErrors($errors);
            
            return false;
        }
        $whereArr  = [];
        if ($param['type'] == $this->bdminType){
            if (!$param['key'] || !$param['user_id'] || !$param['type']) {
                Yii::$service->helper->errors->add('param[key, user_id, type] can not empty');
                
                return false;
            }
            $whereArr = [
                'key' => $param['key'],
                'user_id' => $param['user_id'],
                'type' => $param['type'],
            ];
        } else if ($param['type'] == $this->adminType){
            if (!$param['key'] || !$param['type']) {
                Yii::$service->helper->errors->add('param[key, type] can not empty');
                
                return false;
            }
            $whereArr = [
                'key' => $param['key'],
                'type' => $param['type'],
            ];
        }
        $model = $this->getByKeyAndTypeAndUserId($whereArr) ;  
        if (!$model[$primaryKey]) {
            $model = new $this->_modelName();
            $model->created_at = time();
            $identity = Yii::$app->user->identity;
            $primaryVal = new \MongoDB\BSON\ObjectId();
            $model->{$this->getPrimaryKey()} = $primaryVal;
        }
        
        $model->updated_at = time();
        unset($param['_id']);
        $saveStatus = Yii::$service->helper->ar->save($model, $param);
        if ($saveStatus) {
            
            return true;
        } else {
            $errors = $model->errors;
            Yii::$service->helper->errors->addByModelErrors($errors);

            return false;
        }
    }
    
    public function getCustomerFrontHomePageConfig()
    {
        if (!Yii::$app->user->isGuest) {
            $identity = Yii::$app->user->identity;
            $bdmin_user_id = $identity['bdmin_user_id'];
            $whereArr = [
                'key' => $this->homePageKey,
                'user_id' => $bdmin_user_id,
                'type' => $this->bdminType,
            ];
        } else {
            $whereArr = [
                'key' => $this->homePageKey,
                'type' => $this->adminType,
            ];
        }
        
        $model = $this->getByKeyAndTypeAndUserId($whereArr) ; 
        if (!$model) {
            Yii::$service->helper->errors->add('front home page config is empty');
            
            return false;
        }
        $content = $model['content'];
        $content['skus'] = explode(',', $content['skus']);
        return $content;
    }
    
    protected $baseFrontConfigContent;
    
    public function getCustomerFrontServiceBaseConfig()
    {
        if (!$this->baseFrontConfigContent) {
            if (!Yii::$app->user->isGuest) {
                $identity = Yii::$app->user->identity;
                $bdmin_user_id = $identity['bdmin_user_id'];
                $whereArr = [
                    'key' => $this->baseInfoKey,
                    'user_id' => (int)$bdmin_user_id,
                    'type' => $this->bdminType,
                ];
            } else {
                $whereArr = [
                    'key' => $this->baseInfoKey,
                    'type' => $this->adminType,
                ];
            }
            
            $model = $this->getByKeyAndTypeAndUserId($whereArr) ; 
            if (!$model) {
                Yii::$service->helper->errors->add('front home page config is empty');
                
                return false;
            }
            $this->baseFrontConfigContent = $model['content'];
        }
        
        return $this->baseFrontConfigContent;
    }
    
    protected $baseConfigContent;
    
    public function getBdminBaseConfig($bdmin_user_id)
    {
        if (!$this->baseConfigContent) {
            if (!$bdmin_user_id) {
                $this->baseConfigContent = [];
            } else {
                $whereArr = [
                    'key' => $this->baseInfoKey,
                    'user_id' => (int)$bdmin_user_id,
                    'type' => $this->bdminType,
                ];
                $model = $this->getByKeyAndTypeAndUserId($whereArr) ; 
                if (!$model) {
                    Yii::$service->helper->errors->add('front home page config is empty');
                    
                    $this->baseConfigContent = [];
                } else {
                    $this->baseConfigContent = $model['content'];
                }
                
            }
        }
        
        return $this->baseConfigContent;
    }
    
    public function getBdminDefaultWarehouseArr($bdmin_user_id) {
        $bdminBaseConfig = $this->getBdminBaseConfig($bdmin_user_id);
        $default_warehouse = $bdminBaseConfig['default_warehouse'];
        if (!$default_warehouse) {
            return [];
        }
        $default_warehouse_arr = explode('，',$default_warehouse);
        return $default_warehouse_arr;
    }
    
}