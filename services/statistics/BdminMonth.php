<?php

/*
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\services\statistics;

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
class  BdminMonth extends Service
{
    
    public $numPerPage = 2;
    
    protected $_modelName = '\fbbcbase\models\mysqldb\statistics\BdminMonth';

    protected $_model;
    
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
        return 'id';
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
    
    
    
}
