<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
namespace fbbcbase\app\appserver\modules\Checkout\controllers;

use fecshop\app\appserver\modules\AppserverController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class CartController extends \fecshop\app\appserver\modules\Checkout\controllers\CartController
{
    public $enableCsrfValidation = false;

    

    /**
     * 把产品加入到购物车.
     */
    public function actionAdd()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $guestCart = Yii::$app->controller->module->params['guestCart'];
        if(!$guestCart && Yii::$app->user->isGuest){
            $code = Yii::$service->helper->appserver->account_no_login_or_login_token_timeout;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            
            return $responseData;
        } 
        //echo 1;exit; 
        $custom_option = Yii::$app->request->post('custom_option');
        $product_id = Yii::$app->request->post('product_id');
        $qty = Yii::$app->request->post('qty');
        //$custom_option  = \Yii::$service->helper->htmlEncode($custom_option);
        $product_id = \Yii::$service->helper->htmlEncode($product_id);
        $qty = \Yii::$service->helper->htmlEncode($qty);
        $qty = abs(ceil((int) $qty));
        $return = [];
        $code = 400;
        if ($qty && $product_id) {
            if ($custom_option) {
                $custom_option_sku = json_decode($custom_option, true);
            }
            if (empty($custom_option_sku)) {
                $custom_option_sku = null;
            }
            $item = [
                'product_id' => $product_id,
                'qty'        =>  $qty,
                'custom_option_sku' => $custom_option_sku,
            ];
            // 判断是否需要 custom_option_sku参数，如果该产品需要此参数，而没有传递，则报错返回
            
            
            $innerTransaction = Yii::$app->db->beginTransaction();
            try {
                $addToCart = Yii::$service->cart->addProductToCart($item);
                if ($addToCart) {
                    $innerTransaction->commit();
                    $code = Yii::$service->helper->appserver->status_success;
                    $data = [
                        'items_count' => Yii::$service->cart->quote->getCartItemCount(),
                    ];
                    $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

                    return $responseData;
                } else {
                    $innerTransaction->rollBack();
                    $code = Yii::$service->helper->appserver->cart_product_add_fail;
                    $data = Yii::$service->helper->errors->get(',');
                    $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

                    return $responseData;
                }
            } catch (\Exception $e) {
                $innerTransaction->rollBack();
            }
        } else {
            $code = Yii::$service->helper->appserver->cart_product_add_param_invaild;
            $data = '';
            $message = 'request post param: \'product_id\' and \'qty\' can not empty';
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data,$message);

            return $responseData;

        }

    }
    
    public function actionActivecount(){
        $cartQty = Yii::$service->cart->getCartItemQty();
        $code = Yii::$service->helper->appserver->status_success;
        $data = [
            'cart_active_count' => $cartQty,
        ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
    }
    
    
    
    public function actionUpdateinfo()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $item_id = Yii::$app->request->post('item_id');
        $up_type = Yii::$app->request->post('up_type');
        $innerTransaction = Yii::$app->db->beginTransaction();
        try {
            if ($up_type == 'add_one') {
                $status = Yii::$service->cart->addOneItem($item_id);
            } elseif ($up_type == 'less_one') {
                $status = Yii::$service->cart->lessOneItem($item_id);
            } elseif ($up_type == 'remove') {
                $status = Yii::$service->cart->removeItem($item_id);
            }
            if ($status) {
                $innerTransaction->commit();
                $code = Yii::$service->helper->appserver->status_success;
                $data = [
                    'items_count' => Yii::$service->cart->getCartItemQty(),
                ];
                $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

                return $responseData;
            } else {
                $innerTransaction->rollBack();
            }
        } catch (\Exception $e) {
            $innerTransaction->rollBack();
        }
        $code = Yii::$service->helper->appserver->cart_product_update_qty_fail;
        $data = [ ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
    }

    public function actionSelectone()
    {
        $item_id = Yii::$app->request->get('item_id');
        $checked = Yii::$app->request->get('checked');
        $checked = $checked == 1 ? true : false;
        $innerTransaction = Yii::$app->db->beginTransaction();
        try {
            $status = Yii::$service->cart->selectOneItem($item_id, $checked);
            if ($status) {
                $innerTransaction->commit();
                $code = Yii::$service->helper->appserver->status_success;
                $data = [
                    'items_count' => Yii::$service->cart->getCartItemQty(),
                ];
                $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            } else {
                $innerTransaction->rollBack();
                $code = Yii::$service->helper->appserver->cart_product_select_fail;
                $data = [ ];
                $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            }
        } catch (\Exception $e) {
            $innerTransaction->rollBack();
        }

        return $responseData;
    }

    public function actionSelectall()
    {
        $checked = Yii::$app->request->get('checked');
        $checked = $checked == 1 ? true : false;
        $innerTransaction = Yii::$app->db->beginTransaction();
        try {
            $status = Yii::$service->cart->selectAllItem($checked);
            if ($status) {
                $innerTransaction->commit();
                $code = Yii::$service->helper->appserver->status_success;
                $data = [
                    'items_count' => Yii::$service->cart->getCartItemQty()
                ];
                $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            } else {
                $innerTransaction->rollBack();
                $code = Yii::$service->helper->appserver->cart_product_select_fail;
                $data = [ ];
                $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
            }
        } catch (\Exception $e) {
            $innerTransaction->rollBack();
        }

        return $responseData;
    }
    
    
    
    public function actionIndex()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $currency_info = Yii::$service->page->currency->getCurrencyInfo();
        $code = Yii::$service->helper->appserver->status_success;
        $cart_info = $this->getCartInfo();

        $data = [
            'cart_info' => $cart_info,
            'currency'  => $currency_info,
            'show_coupon' => Yii::$service->helper->canShowUseCouponCode(),
        ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
    }
    
}
