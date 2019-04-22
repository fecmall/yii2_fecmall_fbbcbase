<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appbdmin\modules\Catalog\controllers;

use fbbcbase\app\appbdmin\modules\Catalog\CatalogController;
use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class ProductinfoController extends CatalogController
{
    public $enableCsrfValidation = true;

    public function actionIndex()
    {
        $data = $this->getBlock()->getLastData();

        return $this->render($this->action->id, $data);
    }

    public function actionManageredit()
    {
        /**
         * role
         */
        $primaryKey = Yii::$service->product->getPrimaryKey();
        $product_id = Yii::$app->request->get($primaryKey);
        !$product_id || $this->productHasRole($product_id);
        
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id, $data);
    }
    /**
     * 当前用户是否有操作该产品的权限
     */
    public function productHasRole($product_id){
        $product = Yii::$service->product->getByPrimaryKey($product_id);
        $product_bdmin_user_id = isset($product['bdmin_user_id']) ? $product['bdmin_user_id'] : '';
        $identity = Yii::$app->user->identity;
        $currentUserId = $identity->id;
        if (!$product_bdmin_user_id || $currentUserId != $product_bdmin_user_id) {
            echo  json_encode([
                'statusCode' => '300',
                'message' => Yii::$service->page->translate->__('You do not have role to edit this product'),
            ]);
            exit;
        }
    }

    // catalog
    public function actionImageupload()
    {
        $this->getBlock()->upload();
    }

    // catalog product
    public function actionGetproductcategory()
    {
        $this->getBlock()->getProductCategory();
    }

    public function actionManagereditsave()
    {
        
        $primaryKey = Yii::$service->product->getPrimaryKey();
        $editFormData = Yii::$app->request->post('editFormData');
        $product_id = isset($editFormData[$primaryKey]) ? $editFormData[$primaryKey] : null;
        // 是否有操作这个产品的权限
        !$product_id || $this->productHasRole($product_id);
        
        $data = $this->getBlock('manageredit')->save();
        return $this->render($this->action->id, $data);
    }
    // 
    public function actionManagerdelete()
    {
        $primaryKey = Yii::$service->product->getPrimaryKey();
        $product_id = Yii::$app->request->get($primaryKey);
        $product_ids = Yii::$app->request->post($primaryKey.'s');
        if (!$product_id && !$product_ids) {
            echo json_encode([
                'statusCode' => '300',
                'message' => Yii::$service->page->translate->__('You do not have role to remove this product') ,
            ]);
            exit;
        }else if ($product_id) {
            // 是否有操作这个产品的权限
            $this->productHasRole($product_id);
        } else if ($product_ids ) {
            // 批量删除产品，各个产品的权限判断。
            $ids = explode(',', $product_ids);
            if (is_array($ids) && !empty($ids)) {
                foreach ($ids as $product_id) {
                    // 是否有操作这个产品的权限
                    $this->productHasRole($product_id);
                }
            }
        }
        // 上面的代码都是权限判断，存在权限，才进行下面删除的操作。
        $data = $this->getBlock('manageredit')->delete();
    }
}
