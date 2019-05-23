<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appserver\modules\Checkout\block\onepage;

use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Index extends \fecshop\app\appserver\modules\Checkout\block\onepage\Index
{
    protected $_shipping_method;
    protected $_default_address;
    protected $_cartAddress;
    protected $_cart_address;
    protected $_cart_info;
    protected $is_empty_cart = false;
    protected $bdmin_info;
    
    public function getLastData()
    {
        $this->_default_address = Yii::$service->customer->address->getDefaultAddress();
        if (!$this->_default_address) {
            $code = Yii::$service->helper->appserver->customer_default_address_is_null;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        
        // 获取购物车信息
        $cartInfo = Yii::$service->cart->getCartOrderInfo();
        
        // 如果购物车为空
        if (empty($cartInfo) || !is_array($cartInfo)) {
            $code = Yii::$service->helper->appserver->order_generate_cart_product_empty;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        
        $currency_info = Yii::$service->page->currency->getCurrencyInfo();
        // 获取处理后的购物车信息
        
        $last_cart_info = $this->getCartOrderInfo();
        
        $code = Yii::$service->helper->appserver->status_success;
        $data = [
            'cart_info'                 => $last_cart_info['cart_details'],  
            'all_count'   => $last_cart_info['all_count'],  
            'all_total'   => Yii::$service->helper->format->number_format($last_cart_info['all_total']), 
            'all_base_total'   => $last_cart_info['all_base_total'], 
            'bdmin_info'                 => $this->bdmin_info,  
            'currency_info'             => $currency_info,
            'default_address_list'              => $this->_default_address,
            'show_coupon' => Yii::$service->helper->canShowUseCouponCode(),
        ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
        
        return $responseData;
    }

    

    /**
     * @return $cart_info | Array
     *                    本函数为从数据库中得到购物车中的数据，然后结合产品表
     *                    在加入一些产品数据，最终补全所有需要的信息。
     */
    public function getCartOrderInfo()
    {
        $bdminArr = [];
        if (!$this->_cart_info) {
            $postShippingMethod = Yii::$app->request->post('shipping_method');
            $cartOrderInfo = Yii::$service->cart->getCartOrderInfo($postShippingMethod);
            
            $cart_info = $cartOrderInfo['cart_info'];
            if (!is_array($cart_info) || empty($cart_info)) {
                return null;
            }
            foreach ($cart_info as $bdmin_user_id => $bdminCart) {
                $products = $bdminCart['products'];
                $bdminArr[] = $bdmin_user_id;
                $cart_info[$bdmin_user_id]['grand_total'] = Yii::$service->helper->format->number_format($cart_info[$bdmin_user_id]['grand_total']);
                $cart_info[$bdmin_user_id]['product_total'] = Yii::$service->helper->format->number_format($cart_info[$bdmin_user_id]['product_total']);
                
                
                foreach ($products  as $k => $product_one) {
                    $cart_info[$bdmin_user_id]['products'][$k]['name'] = Yii::$service->store->getStoreAttrVal($product_one['product_name'], 'name');
                    // 设置图片
                    if (isset($product_one['product_image']['main']['image'])) {
                        $image = $product_one['product_image']['main']['image'];
                        $cart_info[$bdmin_user_id]['products'][$k]['imgUrl'] = Yii::$service->product->image->getResize($image,[100,100],false);
                    }
                    $custom_option = isset($product_one['custom_option']) ? $product_one['custom_option'] : '';
                    $custom_option_sku = $product_one['custom_option_sku'];
                    // 将在产品页面选择的颜色尺码等属性显示出来。
                    $custom_option_info_arr = $this->getProductOptions($product_one, $custom_option_sku);
                    $cart_info[$bdmin_user_id]['products'][$k]['custom_option_info'] = $custom_option_info_arr;
                    // 设置相应的custom option 对应的图片
                    $custom_option_image = isset($custom_option[$custom_option_sku]['image']) ? $custom_option[$custom_option_sku]['image'] : '';
                    if ($custom_option_image) {
                        $cart_info[$bdmin_user_id]['products'][$k]['image'] = $custom_option_image;
                    } 
                }
            }
            $this->_cart_info = [
                'cart_details' => $cart_info,
                'all_count'  => $cartOrderInfo['all_count'],
                'all_total' => Yii::$service->helper->format->number_format($cartOrderInfo['all_total']),
                'all_base_total' => Yii::$service->helper->format->number_format($cartOrderInfo['all_base_total']) ,
            ];
            if (!empty($bdminArr)) {
                $this->bdmin_info = Yii::$service->bdminUser->getIdAndNameArrByIds($bdminArr);
            }
        }
        
        return $this->_cart_info;
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
