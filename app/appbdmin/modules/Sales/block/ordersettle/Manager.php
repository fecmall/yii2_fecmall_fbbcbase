<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appbdmin\modules\Sales\block\ordersettle;

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
    protected $_exportExcelUrl;
    /**
     * init param function ,execute in construct.
     */
    public function init()
    { 
        /*
         * edit data url
         */
        $this->_editUrl = CUrl::getUrl('sales/ordersettle/manageredit');
        /*
         * delete data url 
         */
        $this->_exportExcelUrl = CUrl::getUrl('sales/ordersettle/managerexport');
        
        /*
         * service component, data provider
         */
        $this->_service = Yii::$service->statistics->bdminMonth;
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
        $bdminArr = Yii::$service->bdminUser->getAllActiveUserArr();
        $data = [
            /*
            [    // selecit的Int 类型
                'type' => 'select',
                'title'  => Yii::$service->page->translate->__('Bdmin User Id')
                ,
                'name' => 'bdmin_user_id',
                'columns_type' => 'string',  // int使用标准匹配， string使用模糊查询
                'value' => $bdminArr,
            ],
            */
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
            /*
            [
                'orderField'   => 'bdmin_user_id',
                'label'          => Yii::$service->page->translate->__('Bdmin User'),
                'width'         => '50',
                'align'          => 'left',
                'display'       => Yii::$service->bdminUser->getAllActiveUserArr(),
                //'lang'		  => true,
            ],
            */
            [
                'orderField'   => 'year',
                'label'          => Yii::$service->page->translate->__('year'),
                'width'         => '50',
                'align'          => 'left',
                //'lang'		  => true,
            ],
            [
                'orderField'    => 'month',
                'label'           => Yii::$service->page->translate->__('month'),
                'width'          => '50',
                'align'           => 'left',
                'display'        => Yii::$service->payment->getPaymentLabels(),
                //'lang'		   => true,
            ],
            
             [
                'orderField'    => 'month_total',
                'label'           => Yii::$service->page->translate->__('Month Total'),
                'width'          => '50',
                'align'           => 'left',
                //'lang'		   => true,
            ],
            
            
            [
                'orderField'   => 'complete_order_total',
                'label'          => Yii::$service->page->translate->__('Complete Order Total'),
                'width'         => '50',
                'align'          => 'left',
            ],
            [
                'orderField'   => 'admin_refund_return_total',
                'label'          => Yii::$service->page->translate->__('Admin Refund Return Total'),
                'width'         => '50',
                'align'          => 'left',
                //'lang'        => true,
            ],
            [
                'orderField'   => 'bdmin_refund_return_total',
                'label'          => Yii::$service->page->translate->__('Bdmin Refund Return Total') ,
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
                'orderField'   => 'updated_at',
                'label'          => Yii::$service->page->translate->__('Updated At'),
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
        return '<ul class="toolBar"></ul>';
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
						<a title="' . Yii::$service->page->translate->__('View') . '" target="dialog" class="btnEdit" mask="true" drawable="true" width="1200" height="680" href="'.$this->_editUrl.'?'.$this->_primaryKey.'='.$one[$this->_primaryKey].'" ><i class="fa fa-eye"></i></a>
						
					</td>';
            $str .= '</tr>';
        }

        return $str;
    }
}
