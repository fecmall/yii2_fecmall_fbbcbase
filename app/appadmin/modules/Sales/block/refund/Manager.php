<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appadmin\modules\Sales\block\refund;

use fec\helpers\CUrl;
use fec\helpers\CRequest;
use fecshop\app\appadmin\interfaces\base\AppadminbaseBlockInterface;
use fecshop\app\appadmin\modules\AppadminbaseBlock;
use Yii;

/**
 * block cms\article.
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Manager extends AppadminbaseBlock implements AppadminbaseBlockInterface
{
    //protected $_exportExcelUrl;
    protected $_auditAcceptUrl;
    protected $_auditRefuseUrl;
    /**
     * init param function ,execute in construct.
     */
    public function init()
    { 
        /*
         * edit data url
         */
        $this->_editUrl = CUrl::getUrl('sales/refund/manageredit');
        /*
         * delete data url
         */
        $this->_auditAcceptUrl = CUrl::getUrl('sales/refund/manageraccept');
        
        /*
         * service component, data provider
         */
        $this->_service = Yii::$service->refund;
        $this->_service->initModel('admin');
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
            'pagerForm'   => $pagerForm,
            'searchBar'    => $searchBar,
            'editBar'        => $editBar,
            'thead'          => $thead,
            'tbody'          => $tbody,
            'toolBar'        => $toolBar,
        ];
    }

    /**
     * get search bar Arr config.
     */
    public function getSearchArr()
    { 
        $returnStatus = [
            Yii::$service->refund->status_payment_pending => Yii::$service->page->translate->__(Yii::$service->refund->status_payment_pending),
            Yii::$service->refund->status_payment_confirmed => Yii::$service->page->translate->__(Yii::$service->refund->status_payment_confirmed),
        ];
        $typeArr = [
            Yii::$service->refund->type_system_admin_refund_order_return => Yii::$service->page->translate->__(Yii::$service->refund->type_system_admin_refund_order_return),
        ];
        $data = [
            
            [    // selecit的Int 类型
                'type' => 'select',
                'title'  => Yii::$service->page->translate->__('Type')
                ,
                'name' => 'type',
                'columns_type' => 'string',  // int使用标准匹配， string使用模糊查询
                'value' => $typeArr,
            ],
            
            [    // 字符串类型
                'type' => 'select',
                'title'  => Yii::$service->page->translate->__('Status'),
                'name' => 'status',
                'columns_type' => 'string',
                'value' => $returnStatus,
            ],
            
            [    // 字符串类型
                'type' => 'inputtext',
                'title'  => Yii::$service->page->translate->__('AS Id'),
                'name' => 'as_id',
                'columns_type' => 'string',
            ],
            
            [    // 时间区间类型搜索
                'type' => 'inputdatefilter',
                'title'  => Yii::$service->page->translate->__('Created At'),
                'name' => 'created_at',
                'columns_type' => 'int',
                'value' => [
                    'gte' => Yii::$service->page->translate->__('Created Begin'),
                    'lt'    => Yii::$service->page->translate->__('Created End'),
                ],
            ],
        ];

        return $data;
    }

    /**
     * config function ,return table columns config.
     */
    public function getTableFieldArr()
    {
        $table_th_bar = [
            [
                'orderField'   => $this->_primaryKey,
                'label'          => Yii::$service->page->translate->__('Id'),
                'width'         => '50',
                'align'          => 'center',
            ],
            
            [
                'orderField'   => 'as_id',
                'label'          => Yii::$service->page->translate->__('AS Id'),
                'width'         => '50',
                'align'          => 'center',
                //'lang'		  => true,
            ],
            
            [
                'orderField'   => 'customer_email',
                'label'          => Yii::$service->page->translate->__('Customer Email'),
                'width'         => '50',
                'align'          => 'center',
                //'lang'		  => true,
            ],
            
            [
                'orderField'   => 'type',
                'label'          => Yii::$service->page->translate->__('Refund Type'),
                'width'         => '50',
                'align'          => 'left',
                'display'        => Yii::$service->refund->getAllRefundTypeArr(),
                //'lang'        => true,
            ],
            
            [
                'orderField'   => 'status',
                'label'          => Yii::$service->page->translate->__('Return Status'),
                'width'         => '50',
                'align'          => 'left',
                'display'        => Yii::$service->refund->getAllRefundStatusArr(),
                //'lang'        => true,
            ],
            
            
            [
                'orderField'   => 'currency_code',
                'label'          => Yii::$service->page->translate->__('Currency Code'),
                'width'         => '50',
                'align'          => 'center',
                //'lang'		  => true,
            ],
            [
                'orderField'   => 'price',
                'label'          => Yii::$service->page->translate->__('Price'),
                'width'         => '50',
                'align'          => 'left',
                //'lang'		  => true,
            ],
            
            
            [
                'orderField'   => 'customer_bank',
                'label'          => Yii::$service->page->translate->__('Customer Bank'),
                'width'         => '50',
                'align'          => 'center',
                //'lang'		  => true,
            ],
            
            [
                'orderField'   => 'customer_bank_name',
                'label'          => Yii::$service->page->translate->__('Customer Bank Name'),
                'width'         => '50',
                'align'          => 'center',
                //'lang'		  => true,
            ],
            
            [
                'orderField'   => 'customer_bank_account',
                'label'          => Yii::$service->page->translate->__('Customer Bank Account'),
                'width'         => '50',
                'align'          => 'center',
                //'lang'		  => true,
            ],
            
             [
                'orderField'   => 'updated_at',
                'label'          => Yii::$service->page->translate->__('Updated At'),
                'width'         => '50',
                'align'          => 'left',
                'convert'      => ['int' => 'date'],
                //'lang'        => true,
            ],
            
            [
                'orderField'   => 'created_at',
                'label'          => Yii::$service->page->translate->__('Created At'),
                'width'         => '50',
                'align'          => 'left',
                'convert'      => ['int' => 'date'],
                //'lang'        => true,
            ],
            
            
        ];

        return $table_th_bar;
    }

    
    
    
    /**
     * get edit html bar, it contains  add ,eidt ,delete  button.
     */
    public function getEditBar()
    {
        /*
        if(!strstr($this->_currentParamUrl,"?")){
            $csvUrl = $this->_currentParamUrl."?type=export";
        }else{
            $csvUrl = $this->_currentParamUrl."&type=export";
        }
        target="dwzExport" targetType="navTab"  rel="'.$this->_primaryKey.'s"
        <li class="line">line</li>
        <li><a class="icon csvdownload"   href="'.$csvUrl.'" target="dwzExport" targetType="navTab" title="实要导出这些记录吗?"><span>导出EXCEL</span></a></li>
        */
        return '<ul class="toolBar">
					<li><a csrfName="' .CRequest::getCsrfName(). '" csrfVal="' .CRequest::getCsrfValue(). '" title="' . Yii::$service->page->translate->__('Are you sure refund in bulk') . '?" target="selectedTodo" rel="'.$this->_primaryKey.'s" postType="string" href="'.$this->_auditAcceptUrl.'" class="edit"><span>' . Yii::$service->page->translate->__('Bulk Refund') . '</span></a></li>
                </ul>';
    }
    
    /**
     * list table body.
     */
    public function getTableTbody()
    {
        $searchArr = $this->getSearchArr();
        if (is_array($searchArr) && !empty($searchArr)) {
            $where = $this->initDataWhere($searchArr);
        }
        $identity = Yii::$app->user->identity;
        
        $where[] = ['in', 'status', [
            Yii::$service->refund->status_payment_pending,
            Yii::$service->refund->status_payment_confirmed,
        ]];
        
        $filter = [
            'numPerPage'    => $this->_param['numPerPage'],
            'pageNum'        => $this->_param['pageNum'],
            'orderBy'          => [$this->_param['orderField'] => (($this->_param['orderDirection'] == 'asc') ? SORT_ASC : SORT_DESC)],
            'where'            => $where,
            'asArray'          => $this->_asArray,
        ];
        $coll = $this->_service->coll($filter);
        $data = $coll['coll'];
        $this->_param['numCount'] = $coll['count'];

        return $this->getTableTbodyHtml($data);
    }
    
    public function getTableTheadHtml($table_th_bar)
    {
        $table_th_bar = $this->getTableTheadArrInit($table_th_bar);
        $this->_param['orderField'] = $this->_param['orderField'] ? $this->_param['orderField'] : $this->_primaryKey;
        $this->_param['orderDirection'] = $this->_param['orderDirection'];
        foreach ($table_th_bar as $k => $field) {
            if ($field['orderField'] == $this->_param['orderField']) {
                $table_th_bar[$k]['class'] = $this->_param['orderDirection'];
            }
        }
        $str = '<thead><tr>';
        $str .= '<th width="22"><input type="checkbox" group="'.$this->_primaryKey.'s" class="checkboxCtrl"></th>';
        foreach ($table_th_bar as $b) {
            $width = $b['width'];
            $label = $b['label'];
            $orderField = $b['orderField'];
            $class = isset($b['class']) ? $b['class'] : '';
            $align = isset($b['align']) ? 'align="'.$b['align'].'"' : '';
            $str .= '<th width="'.$width.'" '.$align.' orderField="'.$orderField.'" class="'.$class.'">'.$label.'</th>';
        }
        $str .= '<th width="80" >' . Yii::$service->page->translate->__('Edit') . '</th>';
        $str .= '</tr></thead>';

        return $str;
    }
    
    /**
     * rewrite parent getTableTbodyHtml($data).
     */
    public function getTableTbodyHtml($data)
    {
        $fileds = $this->getTableFieldArr();
        $str = '';
        $csrfString = CRequest::getCsrfString();
        $user_ids = [];
        foreach ($data as $one) {
            $user_ids[] = $one['created_person'];
        }
        $users = Yii::$service->adminUser->getIdAndNameArrByIds($user_ids);
        foreach ($data as $one) {
            $str .= '<tr target="sid_user" rel="'.$one[$this->_primaryKey].'">';
            $str .= '<td><input name="'.$this->_primaryKey.'s" value="'.$one[$this->_primaryKey].'" type="checkbox"></td>';
            foreach ($fileds as $field) {
                $orderField = $field['orderField'];
                $display = $field['display'];
                $val = $one[$orderField];
                if ($orderField == 'created_person') {
                    $val = isset($users[$val]) ? $users[$val] : $val;
                    $str .= '<td>'.$val.'</td>';
                    continue;
                }
                
                if ($orderField == 'image') {
                    $imgUrl = Yii::$service->product->image->getUrl($val);
                    $str .= '<td><span style="display:block;padding:10px 5px;" title="'.$imgUrl.'"><img style="margin:auto;display:block;max-width:100px;max-height:100px;" src="'.$imgUrl.'" /></span></td>';
                    continue;
                }
                
                if ($val) {
                    if (isset($field['display']) && !empty($field['display'])) {
                        $display = $field['display'];
                        $val = $display[$val] ? $display[$val] : $val;
                    }
                    if (isset($field['convert']) && !empty($field['convert'])) {
                        $convert = $field['convert'];
                        foreach ($convert as $origin =>$to) {
                            if (strstr($origin, 'mongodate')) {
                                if (isset($val->sec)) {
                                    $timestramp = $val->sec;
                                    if ($to == 'date') {
                                        $val = date('Y-m-d', $timestramp);
                                    } elseif ($to == 'datetime') {
                                        $val = date('Y-m-d H:i:s', $timestramp);
                                    } elseif ($to == 'int') {
                                        $val = $timestramp;
                                    }
                                }
                            } elseif (strstr($origin, 'date')) {
                                if ($to == 'date') {
                                    $val = date('Y-m-d', strtotime($val));
                                } elseif ($to == 'datetime') {
                                    $val = date('Y-m-d H:i:s', strtotime($val));
                                } elseif ($to == 'int') {
                                    $val = strtotime($val);
                                }
                            } elseif ($origin == 'int') {
                                if ($to == 'date') {
                                    $val = date('Y-m-d', $val);
                                } elseif ($to == 'datetime') {
                                    $val = date('Y-m-d H:i:s', $val);
                                } elseif ($to == 'int') {
                                    $val = $val;
                                }
                            } elseif ($origin == 'string') {
                                if ($to == 'img') {
                                    $t_width = isset($field['img_width']) ? $field['img_width'] : '100';
                                    $t_height = isset($field['img_height']) ? $field['img_height'] : '100';
                                    $val = '<img style="width:'.$t_width.'px;height:'.$t_height.'px" src="'.$val.'" />';
                                }
                            }
                        }
                    }

                    if (isset($field['lang']) && !empty($field['lang'])) {
                        //var_dump($val);
                        //var_dump($orderField);
                        $val = Yii::$service->fecshoplang->getDefaultLangAttrVal($val, $orderField);
                    }
                }
                $str .= '<td>'.$val.'</td>';
            }
            $str .= '<td> 
						<a title="' . Yii::$service->page->translate->__('Edit') . '" target="dialog" class="btnEdit" mask="true" drawable="true" width="1200" height="680" href="'.$this->_editUrl.'?id='.$one['id'].'" ><i class="fa fa-edit"></i></a>
						 
					</td>';
            $str .= '</tr>';
        }

        return $str;
    }
}
