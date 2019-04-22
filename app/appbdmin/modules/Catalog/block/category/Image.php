<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appbdmin\modules\Catalog\block\category;

use Yii;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Image
{
    public function upload()
    {
        foreach ($_FILES as $FILE) {
            list($imgSavedRelativePath, $imgUrl, $imgPath) = Yii::$service->category->image->saveCategoryUploadImg($FILE);
        }
        echo json_encode([
            'return_status' => 'success',
            'relative_path' => $imgSavedRelativePath,
            'img_url'        => $imgUrl,
        ]);
        exit;
    }
}
