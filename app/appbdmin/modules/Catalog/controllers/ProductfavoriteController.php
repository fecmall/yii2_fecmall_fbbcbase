<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appbdmin\modules\Catalog\controllers;

use fbbcbase\app\appbdmin\modules\Catalog\CatalogController;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class ProductfavoriteController extends CatalogController
{
    public $enableCsrfValidation = true;
    public $blockNamespace = 'fbbcbase\\app\\appbdmin\\modules\\Catalog\\block';

    public function actionIndex()
    {
        $data = $this->getBlock()->getLastData();

        return $this->render($this->action->id, $data);
    }
}
