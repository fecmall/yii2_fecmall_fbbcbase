<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appserver\modules\Customer\controllers;

use fecshop\app\appserver\modules\AppserverTokenController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class OrderController extends \fecshop\app\appserver\modules\Customer\controllers\OrderController
{
    public $enableCsrfValidation = false ;
    protected $numPerPage = 10;
    protected $pageNum;
    protected $orderBy;
    protected $customer_id;
    protected $_page = 'p';

    public function actionIndex()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $identity = Yii::$app->user->identity;
        $this->customer_id = $identity['id'];
        $this->pageNum = (int) Yii::$app->request->get('p');
        $this->pageNum = ($this->pageNum >= 1) ? $this->pageNum : 1;
        $this->orderBy = ['created_at' => SORT_DESC];
        $return_arr = [];
        if ($this->customer_id) { 
            $filter = [
                'numPerPage'    => $this->numPerPage,
                'pageNum'        => $this->pageNum,
                'orderBy'        => $this->orderBy,
                'where'            => [
                    ['customer_id' => $this->customer_id],
                ],
                'asArray' => true,
            ];

            $customer_order_list = Yii::$service->order->coll($filter);
            $order_list = $customer_order_list['coll'];
            $count = $customer_order_list['count'];
            $orderArr = [];
            if(is_array($order_list)){
                foreach($order_list as $k=>$order){
                    $currencyCode = $order['order_currency_code'];
                    $order['currency_symbol'] = Yii::$service->page->currency->getSymbol($currencyCode);
                    $orderArr[] = $this->getOrderArr($order);
                }
            }
            $code = Yii::$service->helper->appserver->status_success;
            $data = [
                'orderList'     => $orderArr,
                'count'         => $count,
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
    }

    public function getOrderArr($order){
        $order_status = Yii::$service->order->info->getLabelStatus($order);
        $orderInfo = [];
        $orderInfo['created_at'] = date('Y-m-d H:i:s',$order['created_at']);
        $orderInfo['updated_at'] = date('Y-m-d H:i:s',$order['updated_at']);
        $orderInfo['increment_id'] = $order['increment_id'];
        $orderInfo['order_id'] = $order['order_id'];
        $orderInfo['order_status'] = Yii::$service->page->translate->__($order_status);
        $orderInfo['items_count'] = $order['items_count'];
        $orderInfo['total_weight'] = $order['total_weight'];
        $orderInfo['order_currency_code'] = $order['order_currency_code'];
        $orderInfo['order_to_base_rate'] = $order['order_to_base_rate'];
        $orderInfo['grand_total'] = $order['grand_total'];
        $orderInfo['base_grand_total'] = $order['base_grand_total'];
        $orderInfo['subtotal'] = $order['subtotal'];
        $orderInfo['base_subtotal'] = $order['base_subtotal'];
        $orderInfo['subtotal_with_discount'] = $order['subtotal_with_discount'];
        $orderInfo['base_subtotal_with_discount'] = $order['base_subtotal_with_discount'];
        $orderInfo['checkout_method'] = $order['checkout_method'];
        $orderInfo['customer_id'] = $order['customer_id'];
        $orderInfo['customer_group'] = $order['customer_group'];
        $orderInfo['customer_email'] = $order['customer_email'];
        $orderInfo['customer_firstname'] = $order['customer_firstname'];
        $orderInfo['customer_lastname'] = $order['customer_lastname'];
        $orderInfo['customer_is_guest'] = $order['customer_is_guest'];
        $orderInfo['coupon_code'] = $order['coupon_code'];
        $orderInfo['payment_method'] = Yii::$service->page->translate->__($order['payment_method']);
        $orderInfo['shipping_method'] = Yii::$service->page->translate->__($order['shipping_method']);
        $orderInfo['tracking_number'] = $order['tracking_number'];

        $orderInfo['shipping_total'] = $order['shipping_total'];
        $orderInfo['base_shipping_total'] = $order['base_shipping_total'];
        $orderInfo['customer_telephone'] = $order['customer_telephone'];
        $orderInfo['customer_address_country'] = $order['customer_address_country'];
        $orderInfo['customer_address_state'] = $order['customer_address_state'];
        $orderInfo['customer_address_city'] = $order['customer_address_city'];
        $orderInfo['customer_address_zip'] = $order['customer_address_zip'];
        $orderInfo['customer_address_street1'] = $order['customer_address_street1'];
        $orderInfo['customer_address_street2'] = $order['customer_address_street2'];
        $orderInfo['customer_address_state_name'] = $order['customer_address_state_name'];
        $orderInfo['customer_address_country_name'] = $order['customer_address_country_name'];
        $orderInfo['currency_symbol'] = $order['currency_symbol'];
        $orderInfo['products'] = $order['products'];
        $orderInfo['can_payment'] = Yii::$service->order->info->isCustomerCanPayment($order);
        $orderInfo['can_cancel'] = Yii::$service->order->info->isCustomerCanCancel($order);
        $orderInfo['can_receive'] = Yii::$service->order->info->isCustomerCanReceive($order);
        $orderInfo['can_after_sale'] = Yii::$service->order->info->isCustomerCanAfterSale($order);
        $orderInfo['can_cancel_back'] = Yii::$service->order->info->isCustomerCanCancelBack($order);
        $orderInfo['can_delay_receive'] = Yii::$service->order->info->isCustomerCanDelayReceiveOrder($order);

        return $orderInfo;
    }
    
    public function getCustomerId()
    {
        $identity = Yii::$app->user->identity;
        
        return $identity->id;
    }
    
    // 用户在账户中心取消订单
    public function actionCancel()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        
        $increment_id = Yii::$app->request->get('increment_id');
        $customer_id = $this->getCustomerId();
        // 事务处理
        $innerTransaction = Yii::$app->db->beginTransaction();
        try { 
            $requestCancelStatus = Yii::$service->order->process->customerRequestCancelByIncrementId($increment_id, $customer_id);
            if (!$requestCancelStatus) {
                throw new \Exception('pay refund fail');
            }
            $innerTransaction->commit();
        } catch (\Exception $e) {
            $innerTransaction->rollBack();
            
            $code = Yii::$service->helper->appserver->customer_order_cancel_request_fail;
            $data = [
                'error'     => Yii::$service->helper->errors->get(),
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        
        
        $code = Yii::$service->helper->appserver->status_success;
        $data = [ ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
    }
    
    
    // 用户在账户中心取消订单
    public function actionCancelback()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        
        $increment_id = Yii::$app->request->get('increment_id');
        $customer_id = $this->getCustomerId();
        $cancelBackStatus = Yii::$service->order->process->customerCancelBackByIncrementId($increment_id, $customer_id);
        if (!$cancelBackStatus) {
            $code = Yii::$service->helper->appserver->customer_order_cancel_back_fail;
            $data = [
                'error'     => Yii::$service->helper->errors->get(),
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        
        $code = Yii::$service->helper->appserver->status_success;
        $data = [ ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
    }
    
    
    // 用户在账户中心延迟收货时间
    public function actionDelayreceive()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        
        $order_id = Yii::$app->request->get('order_id');
        $customer_id = $this->getCustomerId();
        $delayReceiveStatus = Yii::$service->order->process->customerDelayReceiveOrderById($order_id, $customer_id);
        if (!$delayReceiveStatus) {
            $code = Yii::$service->helper->appserver->customer_order_delay_receive_fail;
            $data = [
                'error'     => Yii::$service->helper->errors->get(),
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        
        $code = Yii::$service->helper->appserver->status_success;
        $data = [ ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
    }
    
    // 用户在账户中心订单确认收货
    public function actionReceive()
    {
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        
        $increment_id = Yii::$app->request->get('increment_id');
        $customer_id = $this->getCustomerId();
        $requestCancelStatus = Yii::$service->order->process->customerReceiveOrderByIncrementId($increment_id, $customer_id );
        if (!$requestCancelStatus) {
            $code = Yii::$service->helper->appserver->customer_order_cancel_request_fail;
            $data = [
                'error'     => Yii::$service->helper->errors->get(),
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        
        $code = Yii::$service->helper->appserver->status_success;
        $data = [ ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
    }
    


    public function actionView(){
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $order_id = Yii::$app->request->get('order_id');
        if ($order_id) {
            $order_info = Yii::$service->order->getOrderInfoById($order_id);
            if (isset($order_info['customer_id']) && !empty($order_info['customer_id'])) {
                $identity = Yii::$app->user->identity;
                $customer_id = $identity->id;
                if ($order_info['customer_id'] == $customer_id) {
                    $order_info = $this->getOrderArr($order_info);
                    $productArr = [];
                    //var_dump($order_info);exit;
                    if(is_array($order_info['products'])){
                        foreach($order_info['products'] as $product){
                            $productArr[] = [
                                'imgUrl' => Yii::$service->product->image->getResize($product['image'],[100,100],false),
                                'name' => $product['name'],
                                'sku' => $product['sku'],
                                'item_id' => $product['item_id'],
                                'qty' => $product['qty'],
                                'row_total' => $product['row_total'],
                                'product_id' => $product['product_id'],
                                'custom_option_info' => $product['custom_option_info'],
                            ];

                        }
                    }
                    $order_info['products'] = $productArr;
                    $order_info['tracking_number'] = $order_info['tracking_number'] ? $order_info['tracking_number'] : '';
                    $order_info['shipping_method'] = $order_info['shipping_method'];
                    $order_info['payment_method'] = $order_info['payment_method'];
                    $order_info['customer_address_country_name'] = Yii::$service->page->translate->__($order_info['customer_address_country_name']);
                    
                    $code = Yii::$service->helper->appserver->status_success;
                    $data = [
                        'order'=> $order_info,
                    ];
                    $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

                    return $responseData;

                }
            }
        }
    }
    
    
    public function actionReturnstatus(){
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $as_id = Yii::$app->request->get('as_id');
        
        // 参数
        if (!$as_id) {
            $code = Yii::$service->helper->appserver->status_invalid_param;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }    
        
        $afterSaleOne = Yii::$service->order->afterSale->getInfoByPrimaryKey($as_id);
        $customer_id = $this->getCustomerId();
        // 判断当前的customer_id是否等于订单中的customer_id
        if ($customer_id && $afterSaleOne['customer_id'] != $customer_id) {
            Yii::$service->helper->errors->add('you donot have role operate this order after sale, current customer_id:{customer_id} is not equel to order after sale customer_id:{order_customer_id}', [
                'customer_id' => $customer_id, 
                'order_customer_id' => $afterSaleOne['customer_id'] 
            ]);
            
            $code = Yii::$service->helper->appserver->status_role_refuse;
            $data = [
                'errors' =>  Yii::$service->helper->errors->get(),
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        if (!is_array($afterSaleOne) || empty($afterSaleOne)) {
            
            $code = Yii::$service->helper->appserver->customer_order_return_is_empty;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        // 退货表中查询， item_id 查找是否存在
        $code = Yii::$service->helper->appserver->status_success;
        $data = [
            'after_sale'=> [
                'id' => $afterSaleOne['id'],
                'product_id' => $afterSaleOne['product_id'],
                'image' => Yii::$service->product->image->getResize($afterSaleOne['image'],[100,100],false),
                'status' => Yii::$service->page->translate->__($afterSaleOne['status']),
                'can_cancel' => Yii::$service->order->info->isCustomerCanCancelAfterSaleReturndOrder($afterSaleOne),
                'sku' => $afterSaleOne['sku'],
                'custom_option_info' => $afterSaleOne['custom_option_info'],
                'base_price' => $afterSaleOne['base_price'],
                'price' => $afterSaleOne['price'],
                'qty' => $afterSaleOne['qty'],
                'currency_symbol' => Yii::$service->page->currency->getSymbol($afterSaleOne['currency_code']),
                'tracking_number' => $afterSaleOne['tracking_number'],
                'show_dispatch' => Yii::$service->order->info->isCustomerCanDispatchAfterSaleReturndOrder($afterSaleOne),
            ],
        ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
        
        
    }    
    
    
    
    public function actionReturncancel()
    {
        $as_id = Yii::$app->request->get('as_id');
        $customer_id = $this->getCustomerId();
        if (!Yii::$service->order->afterSale->customerCancelReturnByAsId($as_id, $customer_id)) {
            $code = Yii::$service->helper->appserver->customer_order_return_cancel_fail;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        
        $code = Yii::$service->helper->appserver->status_success;
        $data = [];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
        
        
    }
    
    public function actionReturndispatch()
    {
        $as_id = Yii::$app->request->get('as_id');
        $customer_id = $this->getCustomerId();
        $tracking_number = Yii::$app->request->get('tracking_number');
        if (!Yii::$service->order->afterSale->customerDispatchReturnByAsId($as_id, $tracking_number, $customer_id)) {
            $code = Yii::$service->helper->appserver->customer_order_return_dispatch_fail;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        
        $code = Yii::$service->helper->appserver->status_success;
        $data = [];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
        
    }
    
    // 查看
    public function actionReturnview(){
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $item_id = Yii::$app->request->get('item_id');
        // 参数
        if (!$item_id) {
            $code = Yii::$service->helper->appserver->status_invalid_param;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }    
        $order_item = Yii::$service->order->item->getByPrimaryKey($item_id);
        // 找到order
        $order_id = $order_item['order_id'];
        if (!$order_id) {
            $code = Yii::$service->helper->appserver->status_invalid_param;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        // 判断权限，是否是当前用户的order
        $order_info = Yii::$service->order->getOrderInfoById($order_id);
        $identity = Yii::$app->user->identity;
        $customer_id = $identity->id;
        if ($order_info['customer_id'] != $customer_id) { 
            $code = Yii::$service->helper->appserver->status_role_refuse;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        
        // 退货表中查询， item_id 查找是否存在
        $afterSaleOne = Yii::$service->order->afterSale->GetByItemId($item_id);
        if ($afterSaleOne['item_id']) {
            $code = Yii::$service->helper->appserver->status_success;
            $data = [
                'after_sale'=> [
                    'id' => $afterSaleOne['id'],
                    'order_id' => $afterSaleOne['order_id'],
                    'item_id' => $afterSaleOne['item_id'],
                    'status' => $afterSaleOne['status'],
                    'sku' => $afterSaleOne['sku'],
                    'price' => $afterSaleOne['base_price'],
                    'qty' => $afterSaleOne['qty'],
                    'order_id' => $afterSaleOne['order_id'],
                    'tracking_number' => $afterSaleOne['tracking_number'],
                ],
                'order' => '',
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        
        $order_info = $this->getOrderArr($order_info);
        $productArr = [];
        //var_dump($order_info);exit;
        if(is_array($order_info['products'])){
            foreach($order_info['products'] as $product){
                if ($item_id != $product['item_id']) {
                    continue;
                }
                $productArr[] = [
                    'imgUrl' => Yii::$service->product->image->getResize($product['image'],[100,100],false),
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'item_id' => $product['item_id'],
                    'qty' => $product['qty'],
                    'row_total' => $product['row_total'],
                    'product_id' => $product['product_id'],
                    'custom_option_info' => $product['custom_option_info'],
                ];
            }
        }
        $order_info['products'] = $productArr;
        $code = Yii::$service->helper->appserver->status_success;
        $data = [
            'order'=> $order_info,
            'after_sale' => '',
        ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;

    }
    
    public function actionReturnsubmit(){
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $item_id = Yii::$app->request->get('item_id');
        $return_qty = Yii::$app->request->get('return_qty');
        // 参数判断。
        if (!$item_id || !$return_qty) {
            $code = Yii::$service->helper->appserver->status_invalid_param;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }    
        $orderItemModel = Yii::$service->order->item->getByPrimaryKey($item_id);
        $order_id = $orderItemModel['order_id'];
        // 参数
        if (!$order_id) {
            $code = Yii::$service->helper->appserver->status_invalid_param;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        // 订单的customer_id 是否是当前用户
        $identity = Yii::$app->user->identity;
        if ($orderItemModel['customer_id'] != $identity->id) {
            $code = Yii::$service->helper->appserver->status_role_refuse;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        
        $orderModel = Yii::$service->order->getByPrimaryKey($order_id);
        // 退款请求处理
        if (!Yii::$service->order->afterSale->requestReturn($orderModel, $orderItemModel, $return_qty)) {
            $code = Yii::$service->helper->appserver->customer_order_return_request_fail;
            $data = [
                'errors' => Yii::$service->helper->errors->get(),
            ];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
            
        }
        
        $code = Yii::$service->helper->appserver->status_success;
        $data = [];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
    }
    
    /*
    public function actionReorder()
    {
        $order_id = Yii::$app->request->get('order_id');
        $errorArr = [];
        if (!$order_id) {
            $errorArr[] = 'The order id is empty';
        }
        $order = Yii::$service->order->getByPrimaryKey($order_id);
        if (!$order['increment_id']) {
            $errorArr[] = 'The order is not exist';
        }
        $customer_id = Yii::$app->user->identity->id;
        if (!$order['customer_id'] || ($order['customer_id'] != $customer_id)) {
            $errorArr[] = 'The order does not belong to you';
        }
        if(!empty($errorArr)){
            $code = Yii::$service->helper->appserver->account_reorder_order_id_invalid;
            $data = [];
            $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

            return $responseData;
        }
        $this->addOrderProductToCart($order_id);

        $code = Yii::$service->helper->appserver->status_success;
        $data = [];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);

        return $responseData;
    }


    public function addOrderProductToCart($order_id)
    {
        $items = Yii::$service->order->item->getByOrderId($order_id);
        //var_dump($items);
        if (is_array($items) && !empty($items)) {
            foreach ($items as $one) {
                $item = [
                    'product_id'        => $one['product_id'],
                    'custom_option_sku' => $one['custom_option_sku'],
                    'qty'                => (int) $one['qty'],
                ];
                //var_dump($item);exit;
                Yii::$service->cart->addProductToCart($item);
            }
        }
    }
    */
}
