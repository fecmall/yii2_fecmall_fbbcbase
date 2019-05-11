<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appadmin\modules\Supplier\block\account;

use fec\helpers\CUrl;
use fecshop\app\appadmin\interfaces\base\AppadminbaseBlockInterface;
use fecshop\app\appadmin\modules\AppadminbaseBlock;
use Yii;

/**
 * block cms\article.
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Index extends AppadminbaseBlock implements AppadminbaseBlockInterface
{
    /**
     * init param function ,execute in construct.
     */
    public function init()
    {
        /*
         * edit data url
         */
        $this->_editUrl = CUrl::getUrl('supplier/account/manageredit');
        /*
         * delete data url
         */
        $this->_deleteUrl = CUrl::getUrl('supplier/account/managerdelete');
        /*
         * service component, data provider
         */
        $this->_service = Yii::$service->bdminUser->bdminUser;
        parent::init();
    }

    public function getLastData()
    {

        // hidden section ,that storage page info
        $pagerForm = $this->getPagerForm();
        // search section
        $searchBar = $this->getSearchBar();
        // edit button, delete button,
        $editBar = $this->getEditBar();
        // table head
        $thead = $this->getTableThead();
        // table body
        $tbody = $this->getTableTbody();
        // paging section
        $toolBar = $this->getToolBar($this->_param['numCount'], $this->_param['pageNum'], $this->_param['numPerPage']);

        return [
            'pagerForm'    => $pagerForm,
            'searchBar'     => $searchBar,
            'editBar'         => $editBar,
            'thead'           => $thead,
            'tbody'           => $tbody,
            'toolBar'         => $toolBar,
        ];
    }

    # 定义搜索部分字段格式
    public function getSearchArr(){

        $data = [

            [	# selecit的Int 类型
                'type'=>'select',
                'title'=> Yii::$service->page->translate->__('Status'),
                'name'=>'status',
                'columns_type' =>'int',  # int使用标准匹配， string使用模糊查询
                'value'=> [					# select 类型的值
                    Yii::$service->adminUser->adminUser->getActiveStatus() => Yii::$service->page->translate->__('Enable'),
                    Yii::$service->adminUser->adminUser->getDeleteStatus() => Yii::$service->page->translate->__('Disable'),
                ],
            ],
            [	# 字符串类型
                'type'=>'inputtext',
                'title'=> Yii::$service->page->translate->__('User Name'),
                'name'=>'username' ,
                'columns_type' =>'string'
            ],
            [	# 字符串类型
                'type'=>'inputtext',
                'title'=>Yii::$service->page->translate->__('Worker No'),
                'name'=>'code' ,
                'columns_type' =>'string'
            ],

            [	# 字符串类型
                'type'=>'inputtext',
                'title'=>Yii::$service->page->translate->__('Email'),
                'name'=>'email' ,
                'columns_type' =>'string'
            ],
            [	# 时间区间类型搜索
                'type'=>'inputdatefilter',
                'name'=> 'created_at_datetime',
                'columns_type' =>'datetime',
                'value'=>[
                    'gte'=> Yii::$service->page->translate->__('Created Begin'),
                    'lt' => Yii::$service->page->translate->__('Created End'),
                ]
            ],


        ];
        return $data;
    }







    # 定义表格显示部分的配置
    public function getTableFieldArr(){
        $table_th_bar = [
            [
                'orderField' 	=> 'id',
                'label'			=> 'ID',
                'width'			=> '110',
                'align' 		=> 'center',

            ],
            [
                'orderField'	=> 'username',
                'label'			=> Yii::$service->page->translate->__('User Name'),
                'width'			=> '110',
                'align' 		=> 'left',
            ],

            [
                'orderField'	=> 'person',
                'label'			=> Yii::$service->page->translate->__('Name'),
                'width'			=> '110',
                'align' 		=> 'left',
            ],
            
            [
                'orderField'	=> 'email',
                'label'			=> Yii::$service->page->translate->__('Email'),
                'width'			=> '110',
                'align' 		=> 'left',
            ],
            [
                'orderField'	=> 'created_at_datetime',
                'label'			=> Yii::$service->page->translate->__('Created At'),
                'width'			=> '190',
                'align' 		=> 'center',
                //'convert'		=> ['datetime' =>'date'],
            ],
            [
                'orderField'	=> 'updated_at_datetime',
                'label'			=> Yii::$service->page->translate->__('Updated At'),
                'width'			=> '190',
                'align' 		=> 'center',
                //'convert'		=> ['datetime' =>'date'],   # int  date datetime  显示的转换
            ],
            [
                'orderField'	=> 'status',
                'label'			=> Yii::$service->page->translate->__('Status'),
                'width'			=> '60',
                'align' 		=> 'center',
                'display'		=> [       # 显示转换  ，譬如 值为1显示为激活，值为10显示为关闭
                    '1'		=> Yii::$service->page->translate->__('Enable'),
                    '10'	=> Yii::$service->page->translate->__('Disable'),
                ],
            ],
        ];
        return $table_th_bar ;
    }
    
    /**
     * get edit html bar, it contains  add ,eidt ,delete  button.
     */
    //public function getEditBar()
    //{
    //    return '';
    //}
}
