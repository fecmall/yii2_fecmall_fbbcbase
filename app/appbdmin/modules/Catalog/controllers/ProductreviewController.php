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
class ProductreviewController extends CatalogController
{
    public $enableCsrfValidation = true;

     public function actionIndex()
    {
         $data = $this->getBlock()->getLastData();

         return $this->render($this->action->id, $data);
    }

    public function actionManageredit()
    {
        $primaryKey = Yii::$service->product->review->getPrimaryKey();
        $review_id = Yii::$app->request->get($primaryKey);
        $this->productReviewHasRole($review_id);
        
        $data = $this->getBlock()->getLastData();
        return $this->render($this->action->id, $data);
    }

    public function actionManagereditsave()
    {
        $primaryKey = Yii::$service->product->review->getPrimaryKey();
        $review_id = Yii::$app->request->post($primaryKey);
        $this->productReviewHasRole($review_id);
        
        $data = $this->getBlock('manageredit')->save();
    }

    public function actionManagerdelete()
    {
        $this->productReviewHasMutilRole();
        
        $this->getBlock('manageredit')->delete();
    }

    public function actionManageraudit()
    {
        $this->productReviewHasMutilRole();
        
        $this->getBlock('manageredit')->audit();
    }

    public function actionManagerauditrejected()
    {
        $this->productReviewHasMutilRole();
        
        $this->getBlock('manageredit')->auditRejected();
    }
    
    public function productReviewHasMutilRole(){
        // 操作权限
        $primaryKey = Yii::$service->product->review->getPrimaryKey();
        $review_id = Yii::$app->request->get($primaryKey);
        $review_ids = Yii::$app->request->post($primaryKey.'s');
        if (!$review_id && !$review_ids) {
            echo json_encode([
                'statusCode' => '300',
                'message' => Yii::$service->page->translate->__('You do not have role to remove this product review') ,
            ]);
            exit;
        }else if ($review_id) {
            // 是否有操作权限
            $this->productReviewHasRole($review_id);
        } else if ($review_ids ) {
            $ids = explode(',', $review_ids);
            if (is_array($ids) && !empty($ids)) {
                foreach ($ids as $review_id) {
                    // 是否有操作这个产品的权限
                    $this->productReviewHasRole($review_id);
                }
            }
        }
    }
    
    /**
     * 当前用户是否有操作该产品的权限
     */
    public function productReviewHasRole($review_id){
        $productReview = Yii::$service->product->review->getByPrimaryKey($review_id);
        $review_bdmin_user_id = isset($productReview['bdmin_user_id']) ? $productReview['bdmin_user_id'] : '';
        $identity = Yii::$app->user->identity;
        $currentUserId = $identity->id;
        if (!$review_bdmin_user_id || $currentUserId != $review_bdmin_user_id) {
            echo  json_encode([
                'statusCode' => '300',
                'message' => Yii::$service->page->translate->__('You do not have role to edit this product review'),
            ]);
            exit;
        }
    }
}
