<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appbdmin\modules\Sales\block\orderexport;

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
        $this->_editUrl = CUrl::getUrl('sales/orderexport/manageredit');
        /*
         * delete data url 
         */
        $this->_exportExcelUrl = CUrl::getUrl('sales/orderexport/managerexport');
        
        /*
         * service component, data provider
         */
        $this->_service = Yii::$service->order;
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
        $orderStatus = [
            Yii::$service->order->status_processing => Yii::$service->page->translate->__(Yii::$service->order->status_processing),
        ];
        $data = [
            [    // 字符串类型
                'type' => 'inputtext',
                'title'  => Yii::$service->page->translate->__('Increment Id'),
                'name' => 'increment_id',
                'columns_type' => 'string',
            ],
            [    // selecit的Int 类型
                'type' => 'select',
                'title'  => Yii::$service->page->translate->__('Order Status')
                ,
                'name' => 'order_status',
                'columns_type' => 'string',  // int使用标准匹配， string使用模糊查询
                'value' => $orderStatus,
            ],
            
            [    // 字符串类型
                'type' => 'inputtext',
                'title'  => Yii::$service->page->translate->__('Order Email'),
                'name' => 'customer_email',
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
                'orderField'   => 'increment_id',
                'label'          => Yii::$service->page->translate->__('Increment Id'),
                'width'         => '50',
                'align'          => 'left',
                //'lang'		  => true,
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
                'orderField'   => 'order_status',
                'label'          => Yii::$service->page->translate->__('Order Status'),
                'width'         => '50',
                'align'          => 'left',
                //'lang'        => true,
            ],
            [
                'orderField'   => 'items_count',
                'label'          => Yii::$service->page->translate->__('Itmes Count'),
                'width'         => '50',
                'align'          => 'left',
                //'lang'        => true,
            ],
            [
                'orderField'   => 'total_weight',
                'label'          => Yii::$service->page->translate->__('Total Weight') ,
                'width'         => '50',
                'align'          => 'left',
                //'lang'        => true,
            ],
            [
                'orderField'    => 'base_grand_total',
                'label'           => Yii::$service->page->translate->__('Base Grand Total'),
                'width'          => '50',
                'align'           => 'left',
                //'lang'		   => true,
            ],
            [
                'orderField'    => 'payment_method',
                'label'           => Yii::$service->page->translate->__('Payment Method'),
                'width'          => '50',
                'align'           => 'left',
                'display'        => Yii::$service->payment->getPaymentLabels(),
                //'lang'		   => true,
            ],
            [
                'orderField'   => 'shipping_method',
                'label'          => Yii::$service->page->translate->__('Shipping Method'),
                'width'         => '50',
                'align'          => 'left',
                //'lang'		  => true,
            ],
            [
                'orderField'    => 'base_shipping_total',
                'label'           => Yii::$service->page->translate->__('Base Shipping Total'),
                'width'          => '50',
                'align'           => 'left',
                //'lang'		   => true,
            ],
            [
                'orderField'   => 'customer_address_country',
                'label'          => Yii::$service->page->translate->__('Country'),
                'width'         => '50',
                'align'          => 'left',
                //'lang'		  => true,
            ],
            [
                'orderField'    => 'customer_email',
                'label'           => Yii::$service->page->translate->__('Customer Email'),
                'width'          => '50',
                'align'           => 'left',
                //'lang'		   => true,
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
            // 处理订单状态的搜索
            if ($name == 'order_status') {
                $order_status = $this->_param[$name];
                if ($order_status) {
                    $operate_status_arr = Yii::$service->order->getOperateStatusArr();
                    if (isset($operate_status_arr[$order_status])) {
                        $where[] = [
                            'order_operate_status' => $order_status
                        ];
                    } else {
                        $where[] = [
                            'order_status' => $order_status
                        ];
                    }
                    
                    continue;
                }
            }

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
					<li class="line">line</li>
                    <li><a class="icon exportOrderExcel" href="javascript:void()"  postType="string"  target="_blank" title="' . Yii::$service->page->translate->__('Are you sure you want to export the selected order') . '?"><span>' . Yii::$service->page->translate->__('Export Excel') . '</span></a></li>
				</ul>
                <script>
                    $(document).ready(function(){
                        $(".exportOrderExcel").click(function(){
                            var selectOrderIds = \'\';
                            $(\'.grid input:checkbox[name=order_ids]:checked\').each(function(k){
                                if(k == 0){
                                    selectOrderIds = $(this).val();
                                }else{
                                    selectOrderIds += \',\'+$(this).val();
                                }
                            });
                            if (!selectOrderIds) {
                                var message = "' . Yii::$service->page->translate->__('Choose at least one order') . '";
                                alertMsg.error(message);
                            } else {
                                url = "'.$this->_exportExcelUrl.'" ;
                                doPost(url, {"order_ids": selectOrderIds, "'.CRequest::getCsrfName().'": "'.CRequest::getCsrfValue() .'"});
                            }
                        });
                    });
                </script> 
        ';
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
        $where[] = ['in', 'order_status', Yii::$service->order->info->orderStatusProcessingOrderAcceptArr];
        $where[] = ['in', 'order_operate_status', Yii::$service->order->info->orderOperateStatusProcessingOrderAcceptArr];
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
                
                if ($orderField == 'order_status') {
                    $order_status = $val;
                    $order_operate_status = $one['order_operate_status'];
                    if ($order_operate_status != Yii::$service->order->operate_status_normal) {
                        $str .= '<td>'.Yii::$service->page->translate->__($order_operate_status).'</td>';
                    } else {
                        $str .= '<td>'.Yii::$service->page->translate->__($order_status).'</td>';
                    }
                    
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
						<a title="' . Yii::$service->page->translate->__('View') . '" target="dialog" class="btnEdit" mask="true" drawable="true" width="1200" height="680" href="'.$this->_editUrl.'?'.$this->_primaryKey.'='.$one[$this->_primaryKey].'" ><i class="fa fa-eye"></i></a>
						
					</td>';
            $str .= '</tr>';
        }

        return $str;
    }
}
