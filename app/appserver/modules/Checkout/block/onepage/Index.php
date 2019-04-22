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
    //protected $_address_view_file;
    //protected $_address_id;
    //protected $_address_list;
    //protected $_country;
    //protected $_state;
    //protected $stateArr;
    //protected $_stateHtml;
    protected $_default_address;
    protected $_cartAddress;
    protected $_cart_address;
    protected $_cart_info;
    protected $is_empty_cart = false;
    public function getLastData()
    {
        $this->_default_address = Yii::$service->customer->address->getDefaultAddress();
        if (!$this->_default_address) {
            $code = Yii::$service->helper->appserver->customer_default_address_is_null;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        
        $cartInfo = Yii::$service->cart->getCartInfo(true);
        if (!isset($cartInfo['products']) || !is_array($cartInfo['products']) || empty($cartInfo['products'])) {
            $code = Yii::$service->helper->appserver->order_generate_cart_product_empty;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        }
        $currency_info = Yii::$service->page->currency->getCurrencyInfo();
        //$this->initAddress();
        //$this->initCountry();
        //$this->initState();
        $shippings = $this->getShippings();
        $last_cart_info = $this->getCartInfo(true, $this->_shipping_method, $this->_default_address['country'], $this->_default_address['state']);
        $isGuest = 1;
        if(!Yii::$app->user->isGuest){
            $isGuest = 0;
        }
        $code = Yii::$service->helper->appserver->status_success;
        $data = [
            'shippings'                 => $shippings,
            'current_shipping_method'   => $this->_shipping_method,
            'cart_info'                 => $last_cart_info,  
            'currency_info'             => $currency_info,
            //'address_view_file'       => $this->_address_view_file,
            //'is_empty_cart'             => $this->is_empty_cart,
            'isGuest'                   => $isGuest,
            'default_address_list'              => $this->_default_address,
            
            //'cart_address'              => $this->_address,
            //'cart_address_id'           => $this->_address_id,
            //'countryArr'                => $this->_countrySelect,
            //'country'                   => $this->_country,
            'show_coupon' => Yii::$service->helper->canShowUseCouponCode(),
        ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
        
        return $responseData;
    }

    
    /**
     * 当改变国家的时候，ajax获取省市信息.
     */
    public function ajaxChangecountry()
    {
        $country = Yii::$app->request->get('country');
        $country = \Yii::$service->helper->htmlEncode($country);
        $state = $this->initState($country);
        $code = Yii::$service->helper->appserver->status_success;
        $data = [
            'stateArr' => $this->stateArr,
        ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
        
        return $responseData;
    }

    /**
     * @return $cart_info | Array
     *                    本函数为从数据库中得到购物车中的数据，然后结合产品表
     *                    在加入一些产品数据，最终补全所有需要的信息。
     */
    public function getCartInfo($activeProduct, $shipping_method, $country, $state)
    {
        if (!$this->_cart_info) {
            $cart_info = Yii::$service->cart->getCartInfo($activeProduct, $shipping_method, $country, $state);
            if (isset($cart_info['products']) && is_array($cart_info['products'])) {
                foreach ($cart_info['products'] as $k=>$product_one) {
                    // 设置名字，得到当前store的语言名字。
                    $cart_info['products'][$k]['name'] = Yii::$service->store->getStoreAttrVal($product_one['product_name'], 'name');
                    // 设置图片
                    if (isset($product_one['product_image']['main']['image'])) {
                        $image = $product_one['product_image']['main']['image'];
                        $cart_info['products'][$k]['imgUrl'] = Yii::$service->product->image->getResize($image,[100,100],false);
                    }
                    // 产品的url
                    //$cart_info['products'][$k]['url'] = Yii::$service->url->getUrl($product_one['product_url']);
                    $custom_option = isset($product_one['custom_option']) ? $product_one['custom_option'] : '';
                    $custom_option_sku = $product_one['custom_option_sku'];
                    // 将在产品页面选择的颜色尺码等属性显示出来。
                    $custom_option_info_arr = $this->getProductOptions($product_one, $custom_option_sku);
                    $cart_info['products'][$k]['custom_option_info'] = $custom_option_info_arr;
                    // 设置相应的custom option 对应的图片
                    $custom_option_image = isset($custom_option[$custom_option_sku]['image']) ? $custom_option[$custom_option_sku]['image'] : '';
                    if ($custom_option_image) {
                        $cart_info['products'][$k]['image'] = $custom_option_image;
                    }
                }
            }else{
                $this->is_empty_cart = true;
            }
            $this->_cart_info = $cart_info;
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

    /**
     * @param $current_shipping_method | String  当前选择的货运方式
     * @return Array，数据格式为：
     * [
     *      'method'=> $method,
     *      'label' => $label,
     *      'name'  => $name,
     *      'cost'  => $symbol.$currentCurrencyCost,
     *      'check' => $check,
     *      'shipping_i' => $shipping_i,
     * ]
     * 得到所有的，有效shipping method数组。
     */
    public function getShippings($custom_shipping_method = '')
    {
        $country = $this->_default_address['country'];
        $state = $this->_default_address['state'];
        if (!$state) {
            $region = '*';
        } else {
            $region = $state;
        }
        $cartProductInfo = Yii::$service->cart->quoteItem->getCartProductInfo();
        $product_weight = $cartProductInfo['product_weight'];
        $product_volume_weight = $cartProductInfo['product_volume_weight'];
        $product_final_weight = max($product_weight, $product_volume_weight);
        $cartShippingMethod = $this->_cart_info['shipping_method'];
        // 当前的货运方式 
        $current_shipping_method = Yii::$service->shipping->getCurrentShippingMethod($custom_shipping_method, $cartShippingMethod, $country, $state, $product_final_weight);
        $this->_shipping_method = $current_shipping_method;
        // 得到所有，有效的shipping method
        $shippingArr = $this->getShippingArr($product_final_weight, $current_shipping_method, $country, $state);
        
        return $shippingArr;
    }

    
    /**
     * @param $weight | Float , 总量
     * @param $shipping_method | String  $shipping_method key
     * @param $country | String  国家
     * @return array ， 通过上面的三个参数，得到各个运费方式对应的运费等信息。
     */
    public function getShippingArr($weight, $current_shipping_method, $country, $region)
    {
        $available_shipping = Yii::$service->shipping->getAvailableShippingMethods($country, $region, $weight);
        $sr = '';
        $shipping_i = 1;
        $arr = [];
        if (is_array($available_shipping) && !empty($available_shipping)) {
            foreach ($available_shipping as $method=>$shipping) {
                $label = $shipping['label'];
                $name = $shipping['name'];
                // 得到运费的金额
                $cost = Yii::$service->shipping->getShippingCost($method, $shipping, $weight, $country, $region);
                $currentCurrencyCost = $cost['currCost'];
                $symbol = Yii::$service->page->currency->getCurrentSymbol();
                if ($current_shipping_method == $method) {
                    $checked = true;
                } else {
                    $checked = '';
                }
                $arr[] = [
                    'method'=> $method,
                    'label' => Yii::$service->page->translate->__($label),
                    'name'  => Yii::$service->page->translate->__($name),
                    'cost'  => $currentCurrencyCost,
                    'symbol' => $symbol,
                    'checked' => $checked,
                    'shipping_i' => $shipping_i,
                ];

                $shipping_i++;
            }
        }
        return $arr;
    }

    /**
     * js函数 ajaxreflush() 执行后，就会执行这个函数
     * 在
     * 1.切换address list,
     * 2.取消coupon，
     * 3.切换国家和省市信息，
     * 4.更改货运方式等
     * 集中情况下，就会触发执行当前函数，
     * 该函数会根据传递的参数，重新计算shipping 和order 部分信息，返回
     * 给前端。
     * @proeprty Array，
     * @return json_encode(Array)，Array格式如下：
     *                                                   [
     *                                                   'status' 		=> 'success',
     *                                                   'shippingHtml' 	=> $shippingHtml,
     *                                                   'reviewOrderHtml' 	=> $reviewOrderHtml,
     *                                                   ]
     *                                                   返回给js后，js根据数据将信息更新到相应的部分。
     */
    public function ajaxUpdateOrderAndShipping()
    {
        $this->_default_address = Yii::$service->customer->address->getDefaultAddress();
        $state = $this->_default_address['state'];
        $country = $this->_default_address['country'];
        $address_id = $this->_default_address['address_id'];
        
        $shipping_method = Yii::$app->request->get('shipping_method');
        $shipping_method = \Yii::$service->helper->htmlEncode($shipping_method);
        
        if ($country && $state) {
            $shippings = $this->getShippings($shipping_method);
            
            $quoteItem = Yii::$service->cart->quoteItem->getCartProductInfo();
            $product_weight = $quoteItem['product_weight'];
            // 计算运费。
            $avaiable_method = Yii::$service->shipping->getAvailableShippingMethods($country,$state,$product_weight);
            $shippingInfo = $avaiable_method[$shipping_method];
            $shippingCost = Yii::$service->shipping->getShippingCost($shipping_method, $shippingInfo, $product_weight, $country, $state);
            Yii::$service->cart->quote->setShippingCost($shippingCost);

            $last_cart_info = $this->getCartInfo(true, $shipping_method, $country, $state);
            
            $code = Yii::$service->helper->appserver->status_success;
            $data = [
                'cart_info'                 => $last_cart_info,
                'shippings'                 => $shippings,
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        } else {
            $code = Yii::$service->helper->appserver->order_shipping_country_empty;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
            
        }
    }
}
