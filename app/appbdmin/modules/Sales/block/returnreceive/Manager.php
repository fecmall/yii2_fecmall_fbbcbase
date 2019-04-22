<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appbdmin\modules\Sales\block\returnreceive;

use fec\helpers\CUrl;
use fec\helpers\CRequest;
use fbbcbase\app\appbdmin\interfaces\base\AppbdminbaseBlockInterface;
use fbbcbase\app\appbdmin\modules\AppbdminbaseBlock;
use Yii;

/**
 * block cms\article.
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Manager extends AppbdminbaseBlock implements AppbdminbaseBlockInterface
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
        $this->_editUrl = CUrl::getUrl('sales/orderinfo/manageredit');
        /*
         * delete data url
         */
        $this->_auditAcceptUrl = CUrl::getUrl('sales/returnreceive/manageraccept');
        
        /*
         * service component, data provider
         */
        $this->_service = Yii::$service->order->afterSale;
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
            Yii::$service->order->afterSale->after_sale_status_return_dispatch => Yii::$service->page->translate->__(Yii::$service->order->afterSale->after_sale_status_return_dispatch),
            Yii::$service->order->afterSale->after_sale_status_return_received => Yii::$service->page->translate->__(Yii::$service->order->afterSale->after_sale_status_return_received),
        ];
        $data = [
            
            [    // selecit的Int 类型
                'type' => 'select',
                'title'  => Yii::$service->page->translate->__('Return Status')
                ,
                'name' => 'status',
                'columns_type' => 'string',  // int使用标准匹配， string使用模糊查询
                'value' => $returnStatus,
            ],
            
            [    // 字符串类型
                'type' => 'inputtext',
                'title'  => Yii::$service->page->translate->__('Tracking Number'),
                'name' => 'tracking_number',
                'columns_type' => 'string',
            ],
            
            [    // 字符串类型
                'type' => 'inputtext',
                'title'  => Yii::$service->page->translate->__('Sku'),
                'name' => 'sku',
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
                'orderField'   => 'image',
                'label'          => Yii::$service->page->translate->__('Image'),
                'width'         => '50',
                'align'          => 'center',
                //'lang'		  => true,
            ],
            [
                'orderField'   => 'increment_id',
                'label'          => Yii::$service->page->translate->__('Increment Id'),
                'width'         => '50',
                'align'          => 'center',
                //'lang'		  => true,
            ],
            [
                'orderField'   => 'sku',
                'label'          => Yii::$service->page->translate->__('Sku'),
                'width'         => '50',
                'align'          => 'left',
                //'lang'		  => true,
            ],
            
            [
                'orderField'   => 'status',
                'label'          => Yii::$service->page->translate->__('Return Status'),
                'width'         => '50',
                'align'          => 'left',
                'display'        => Yii::$service->order->afterSale->getAllReturnStatusArr(),
                //'lang'        => true,
            ],
            

            
            
            [
                'orderField'   => 'currency_code',
                'label'          => Yii::$service->page->translate->__('Currency Code'),
                'width'         => '50',
                'align'          => 'left',
                //'lang'        => true,
            ],
            
            [
                'orderField'   => 'price',
                'label'          => Yii::$service->page->translate->__('Return Price'),
                'width'         => '50',
                'align'          => 'left',
                //'lang'        => true,
            ],
            [
                'orderField'   => 'qty',
                'label'          => Yii::$service->page->translate->__('Qty') ,
                'width'         => '50',
                'align'          => 'left',
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
            
            [
                'orderField'   => 'tracking_number',
                'label'          => Yii::$service->page->translate->__('Tracking Number'),
                'width'         => '50',
                'align'          => 'left',
                //'lang'        => true,
            ],
        ];

        return $table_th_bar;
    }

    
    
    
    /**
     * @param $searchArr|Array.
     * generate where Array by  $this->_param and $searchArr.
     * foreach $searchArr , check each one if it is exist in this->_param.
     */
    public function initDataWhere($searchArr)
    {
        $where = [];
        foreach ($searchArr as $field) {
            $type = $field['type'];
            $name = $field['name'];
            $lang = $field['lang'];
            

            $columns_type = isset($field['columns_type']) ? $field['columns_type'] : '';
            if ($this->_param[$name] || $this->_param[$name.'_gte'] || $this->_param[$name.'_lt']) {
                if ($type == 'inputtext' || $type == 'select' || $type == 'chosen_select') {
                    if ($columns_type == 'string') {
                        if ($lang) {
                            $langname = $name.'.'.\Yii::$service->fecshoplang->getDefaultLangAttrName($name);
                            $where[] = ['like', $langname, $this->_param[$name]];
                        } else {
                            $val = $this->_param[$name];
                            if($name == '_id'){
                                $val = new \MongoDB\BSON\ObjectId($val);
                                $where[] = [$name => $val];
                            } else {
                                $where[] = ['like', $name, $val];
                            }
                        }
                    } elseif ($columns_type == 'int') {
                        $where[] = [$name => (int) $this->_param[$name]];
                    } elseif ($columns_type == 'float') {
                        $where[] = [$name => (float) $this->_param[$name]];
                    } elseif ($columns_type == 'date') {
                        $where[] = [$name => $this->_param[$name]];
                    } else {
                        $where[] = [$name => $this->_param[$name]];
                    }
                } elseif ($type == 'inputdatefilter') {
                    $_gte = $this->_param[$name.'_gte'];
                    $_lt = $this->_param[$name.'_lt'];
                    if ($columns_type == 'int') {
                        $_gte = strtotime($_gte);
                        $_lt = strtotime($_lt);
                    }
                    if ($_gte) {
                        $where[] = ['>=', $name, $_gte];
                    }
                    if ($_lt) {
                        $where[] = ['<', $name, $_lt];
                    }
                } elseif ($type == 'inputfilter') {
                    $_gte = $this->_param[$name.'_gte'];
                    $_lt = $this->_param[$name.'_lt'];
                    if ($columns_type == 'int') {
                        $_gte = (int) $_gte;
                        $_lt = (int) $_lt;
                    } elseif ($columns_type == 'float') {
                        $_gte = (float) $_gte;
                        $_lt = (float) $_lt;
                    }
                    if ($_gte) {
                        $where[] = ['>=', $name, $_gte];
                    }
                    if ($_lt) {
                        $where[] = ['<', $name, $_lt];
                    }
                } else {
                    $where[] = [$name => $this->_param[$name]];
                }
            }
        }
        //var_dump($where);
        return $where;
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
					<li><a csrfName="' .CRequest::getCsrfName(). '" csrfVal="' .CRequest::getCsrfValue(). '" title="' . Yii::$service->page->translate->__('Are you sure receive order return in bulk') . '?" target="selectedTodo" rel="'.$this->_primaryKey.'s" postType="string" href="'.$this->_auditAcceptUrl.'" class="edit"><span>' . Yii::$service->page->translate->__('Bulk Receive') . '</span></a></li>
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
        $bdmin_user_id = $identity->id;
        $where[] = [
            'bdmin_user_id' => $bdmin_user_id
        ];
        $where[] = ['in', 'status', [
            Yii::$service->order->afterSale->after_sale_status_return_dispatch,
            Yii::$service->order->afterSale->after_sale_status_return_received,
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
        $str .= '<th width="80" >' . Yii::$service->page->translate->__('View') . '</th>';
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
        
        foreach ($data as $one) {
            $str .= '<tr target="sid_user" rel="'.$one[$this->_primaryKey].'">';
            $str .= '<td><input name="'.$this->_primaryKey.'s" value="'.$one[$this->_primaryKey].'" type="checkbox"></td>';
            foreach ($fileds as $field) {
                $orderField = $field['orderField'];
                $display = $field['display'];
                $val = $one[$orderField];
                
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
						<a title="' . Yii::$service->page->translate->__('View') . '" target="dialog" class="btnEdit" mask="true" drawable="true" width="1200" height="680" href="'.$this->_editUrl.'?order_id='.$one['order_id'].'" ><i class="fa fa-eye"></i></a>
						 
					</td>';
            $str .= '</tr>';
        }

        return $str;
    }
}
