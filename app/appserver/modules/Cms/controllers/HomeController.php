<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appserver\modules\Cms\controllers;

use fecshop\app\appserver\modules\AppserverController;
use Yii;
 
/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class HomeController extends \fecshop\app\appserver\modules\Cms\controllers\HomeController
{
    protected $_title;
    
    public function actionIndex(){
        if(Yii::$app->request->getMethod() === 'OPTIONS'){
            return [];
        }
        $advertiseImg = $this->getAdvertise();
        $productList  = $this->getProduct();
        $language = $this->getLang();
        $currency = $this->getCurrency();
        $code = Yii::$service->helper->appserver->status_success;
        $data = [
                'productList' => $productList,
                'advertiseImg'=> $advertiseImg,
                'language'    => $language,
                'currency'    => $currency,
                'title'   => $this->_title,
            ];
        $responseData = Yii::$service->helper->appserver->getResponseData($code, $data);
        
        return $responseData;
    }
    
    public function getAdvertise(){
        
        $bigImg1 = Yii::$service->image->getImgUrl('custom/home_img_1.jpg','apphtml5');
        $bigImg2 = Yii::$service->image->getImgUrl('custom/home_img_2.jpg','apphtml5');
        $bigImg3 = Yii::$service->image->getImgUrl('custom/home_img_3.jpg','apphtml5');
        $smallImg1 = Yii::$service->image->getImgUrl('custom/home_small_1.jpg','apphtml5');
        $smallImg2 = Yii::$service->image->getImgUrl('custom/home_small_2.jpg','apphtml5');
        
        return [
            'bigImgList' => [
                ['imgUrl' => $bigImg1],
                ['imgUrl' => $bigImg2],
                ['imgUrl' => $bigImg3],
            ],
            'smallImgList' => [
                ['imgUrl' => $smallImg1],
                ['imgUrl' => $smallImg2],
            ],
        ];
    }
    
    public function getProduct(){
        //$featured_skus = Yii::$app->controller->module->params['homeFeaturedSku'];
        $homePageConfig = Yii::$service->systemConfig->getCustomerFrontHomePageConfig();
        $skus = isset($homePageConfig['skus']) ? $homePageConfig['skus'] : [];
        $this->_title = isset($homePageConfig['title']) ? $homePageConfig['title'] : '';
        return $this->getProductBySkus($skus);
    }
    
    

    //public function getBestSellerProduct(){
    //	$best_skus = Yii::$app->controller->module->params['homeBestSellerSku'];
    //	return $this->getProductBySkus($best_skus);
    //}

    public function getProductBySkus($skus)
    {
        if (is_array($skus) && !empty($skus)) {
            $filter['select'] = [
                'sku', 'spu', 'name', 'image',
                'price', 'special_price',
                'special_from', 'special_to',
                'url_key', 'score',
            ];
            $filter['where'] = [
                'and',
                ['in', 'sku', $skus]
            ];
            
            // 供应商权限
            if (Yii::$service->helper->isLoginCustomerOnlySeeSupplierProduct()) {
                $identity = Yii::$app->user->identity;
                $bdmin_user_id = $identity['bdmin_user_id'];
                $filter['where'][] = ['bdmin_user_id' => $bdmin_user_id];
            }
            // 推广store uuid权限
            if ($bdmin_user_id = Yii::$service->helper->getGuestUrlParamRelateBdminUserId()) {
                $filter['where'][] = ['bdmin_user_id' => $bdmin_user_id];
            }
            // 仓库权限过滤
            if (Yii::$service->helper->isLoginCustomerOnlySeeSelectedWarehouseProduct()) {
                $identity = Yii::$app->user->identity; 
                $warehouses = $identity['warehouses']; 
                $warehouseArr = explode(',', $warehouses);
                if (empty($warehouseArr) || is_array($warehouseArr)) {
                    $filter['where'][] = ['in', 'warehouse', $warehouseArr];
                }
            }
            
            $products = Yii::$service->product->getProducts($filter);
            //var_dump($products);
            $products = Yii::$service->category->product->convertToCategoryInfo($products);
            $i = 1;
            $product_return = [];
            if(is_array($products) && !empty($products)){
                
                foreach($products as $k=>$v){
                    $i++;
                    $products[$k]['url'] = '/catalog/product/'.$v['product_id'];
                    $products[$k]['image'] = Yii::$service->product->image->getResize($v['image'],296,false);
                    $priceInfo = Yii::$service->product->price->getCurrentCurrencyProductPriceInfo($v['price'], $v['special_price'],$v['special_from'],$v['special_to']);
                    if (Yii::$service->helper->canShowProductPrice()) {
                        $products[$k]['price'] = isset($priceInfo['price']) ? $priceInfo['price'] : '';
                        $products[$k]['special_price'] = isset($priceInfo['special_price']) ? $priceInfo['special_price'] : '';
                        if (isset($products[$k]['special_price']['value'])) {
                            $products[$k]['special_price']['value'] = Yii::$service->helper->format->number_format($products[$k]['special_price']['value']);
                        }
                        if (isset($products[$k]['price']['value'])) {
                            $products[$k]['price']['value'] = Yii::$service->helper->format->number_format($products[$k]['price']['value']);
                        }
                    } else {
                        $products[$k]['price'] = '' ;
                        $products[$k]['special_price'] = '';
                    }
                    
                    if($i%2 === 0){
                        $arr = $products[$k];
                    }else{
                        $product_return[] = [
                            'one' => $arr,
                            'two' => $products[$k],
                        ];
                    }
                }
                if($i%2 === 0){
                    $product_return[] = [
                        'one' => $arr,
                        'two' => [],
                    ];
                }
            }
            return $product_return;
        }
    }
    
    // 语言
    public function getLang()
    {
        $langs = Yii::$service->store->serverLangs;
        $currentLangCode = Yii::$service->store->currentLangCode;
        
        return [
            'langList' => $langs,
            'currentLang' => $currentLangCode
        ];
    }
    // 货币
    public function getCurrency()
    {
        $currencys = Yii::$service->page->currency->getCurrencys();
        $currentCurrencyCode = Yii::$service->page->currency->getCurrentCurrency();
        
        return [
            'currencyList' => $currencys,
            'currentCurrency' => $currentCurrencyCode
        ];
    }
   
    
}