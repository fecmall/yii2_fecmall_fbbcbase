<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appbdmin\modules\Sales\block\orderdispatch;

use fec\helpers\CUrl;
use Yii;

/**
 * block cms\article.
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Managerdispatch
{
    public $_saveUrl;

    public function init()
    {
        parent::init();
    }

    // 传递给前端的数据 显示编辑form
    public function getLastData()
    {
        $order_id = Yii::$app->request->get('order_id');
        //$order = Yii::$service->order->getByPrimaryKey($order_id);
        $order_info = Yii::$service->order->getOrderInfoById($order_id);
        $order_info = $this->getViewOrderInfo($order_info);
        return [
            'order' => $order_info,
            //'editBar' 	=> $this->getEditBar(),
            //'textareas'	=> $this->_textareas,
            //'lang_attr'	=> $this->_lang_attr,
            'saveUrl' 	    => Yii::$service->url->getUrl('sales/orderdispatch/managerdispatchsave'),
        ];
    }
    
    public function getViewOrderInfo($order_info){
        // 订单状态部分
        $orderStatusArr = Yii::$service->order->getStatusArr();
        //var_dump($orderStatusArr);exit;
        $order_info['order_status_options'] = $this->getOptions($orderStatusArr,$order_info['order_status']);
    
        // 货币部分
        $currencys = Yii::$service->page->currency->getCurrencys();
        $currencyArr = [];
        if(is_array($currencys)){
            foreach( $currencys as $code => $v){
                $currencyArr[$code] = $code;
            }
        }
        $order_info['order_currency_code_options'] = $this->getOptions($currencyArr,$order_info['order_currency_code']);
        // 支付类型
        $checkTypeArr = Yii::$service->order->getCheckoutTypeArr();
        $order_info['checkout_method_options'] = $this->getOptions($checkTypeArr,$order_info['checkout_method']);
        // 游客下单
        $customerOrderArr = [ 
            1 => Yii::$service->page->translate->__('Yes'),
            2 => Yii::$service->page->translate->__('No'),
        ];
        $order_info['customer_is_guest_options'] = $this->getOptions($customerOrderArr,$order_info['customer_is_guest']);
        // 省
        $order_info['customer_address_country_options'] = Yii::$service->helper->country->getCountryOptionsHtml($order_info['customer_address_country']);
        // 市
        $order_info['customer_address_state_options'] = Yii::$service->helper->country->getStateOptionsByContryCode($order_info['customer_address_country'],$order_info['customer_address_state']);
        // 支付方式label 货运方式label
        $order_info['shipping_method_label'] = Yii::$service->shipping->getShippingLabelByMethod($order_info['shipping_method']);
        $order_info['payment_method_label'] = Yii::$service->payment->getPaymentLabelByMethod($order_info['payment_method']);
        
        return $order_info;
    }
    
    public function getOptions($orderStatusArr,$order_status){
        $str = '';
        if (is_array($orderStatusArr)) {
            foreach ($orderStatusArr as $k => $v) {
                if ($order_status == $k ) {
                    $str .= '<option selected="selected" value="'.$k.'">'.$v.'</option>';
                } else {
                    $str .= '<option value="'.$k.'">'.$v.'</option>';
                }
            }
        }
        
        return $str;
    }
    
   
}
