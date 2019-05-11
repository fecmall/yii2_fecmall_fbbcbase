<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appserver\modules\Checkout\block\payment;

use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Index 
{
    protected $_payment_method;
    protected $bdmin_info = [];
    
    public function getLastData()
    {
        $getOrderInfos = $this->getOrderInfos();
        if (empty($getOrderInfos) || !is_array($getOrderInfos)) {
            $data = [];
            $code = Yii::$service->helper->appserver->status_invalid_param;
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        
        $data = [
            'payments' => $this->getPayment(),
            'current_payment_method' => $this->_payment_method,
            'order_infos' => $getOrderInfos,
            'bdmin_info'                 => $this->bdmin_info,  
        ];
        $code = Yii::$service->helper->appserver->status_success;
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
        
        return $responseData;
    }
    
    /**
     * @return 得到所有的支付方式
     *                                     在获取的同时，判断$this->_payment_method 是否存在，不存在则取
     *                                     第一个支付方式，作为$this->_payment_method的值。
     */
    public function getPayment()
    {
        $paymentArr = Yii::$service->payment->getStandardPaymentArr();
        $i = 0;
        foreach ($paymentArr as $k => $v) {
            if (!$i) {
                $this->_payment_method = $k;
                $paymentArr[$k]['checked'] = true;
                $i++;
            }
            $paymentArr[$k]['label'] = Yii::$service->page->translate->__($paymentArr[$k]['label']);
        }

        return $paymentArr;
    }
    // 得到订单的信息。
    public function getOrderInfos(){
        $order_increment_id = Yii::$app->request->get('order_increment_id');
        $orderArr = [];
        if ($order_increment_id) {
            $order = Yii::$service->order->getOrderInfoByIncrementId($order_increment_id);
            $orderArr = [$order];
        } else if ($pay_no = Yii::$service->order->getSessionTradeNo()){
            $orderArr = Yii::$service->order->getOrdersInfoByPayNo($pay_no);
        } else {
            return null;
        }
        $order_infos = [];
        $bdminArr = [];
        if (is_array($orderArr) && !empty($orderArr)) {
            foreach ($orderArr as $order) {
                $order_info = [];
                $order_info['product_total'] = $order['subtotal'];
                $order_info['shipping_cost'] =  $order['shipping_total'];
                $order_info['coupon_cost'] = $order['subtotal_with_discount'];
                $order_info['grand_total'] = $order['grand_total'];
                $order_info['bdmin_user_id'] = $order['bdmin_user_id'];
                $bdminArr[] = $order['bdmin_user_id'];
                $products = $order['products'];
                $product_arr = [];
                if (is_array($products) && !empty($products)) {
                    foreach ($products as $product) {
                        $custom_option_info_arr = $this->getProductOptions($product, $product['custom_option_sku']);
                        $product_arr[] = [
                            'imgUrl' => Yii::$service->product->image->getResize($product['image'], [100, 100], false),
                            'name' => $product['name'],
                            'custom_option_info' => $custom_option_info_arr,
                            'qty' => $product['qty'],
                            'product_row_price' => $product['row_total'],
                            'product_id' => (string)$product['product_id'],
                        ];
                    }
                }
                $order_info['products'] = $product_arr;
                $order_info['currency_symbol'] = $order['currency_symbol'];
                $order_info['increment_id'] = $order['increment_id'];
                $order_infos[] = $order_info;
            }
        }
        if (!empty($bdminArr)) {
            $this->bdmin_info = Yii::$service->bdminUser->getIdAndNameArrByIds($bdminArr);
        }
        
        return $order_infos;
    }
    
    
    /**
     * 将产品页面选择的颜色尺码等显示出来，包括custom option 和spu options部分的数据.
     */
    public function getProductOptions($product_one, $custom_option_sku)
    {
        $custom_option_info_arr = [];
        $custom_option = isset($product_one['custom_option']) ? $product_one['custom_option'] : '';
        $custom_option_sku = $product_one['custom_option_sku'];
        if (isset($custom_option[$custom_option_sku]) && !empty($custom_option[$custom_option_sku])) {
            $custom_option_info = $custom_option[$custom_option_sku];
            foreach ($custom_option_info as $attr=>$val) {
                if (!in_array($attr, ['qty', 'sku', 'price', 'image'])) {
                    $attr = str_replace('_', ' ', $attr);
                    $attr = ucfirst($attr);
                    $custom_option_info_arr[$attr] = $val;
                }
            }
        }

        $spu_options = $product_one['spu_options'];
        if (is_array($spu_options) && !empty($spu_options)) {
            foreach ($spu_options as $label => $val) {
                $custom_option_info_arr[$label] = $val;
            }
        }

        return $custom_option_info_arr;
    }

}
