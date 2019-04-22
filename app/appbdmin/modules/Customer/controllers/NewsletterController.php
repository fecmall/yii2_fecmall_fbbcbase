<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appbdmin\modules\Customer\controllers;

use fbbcbase\app\appbdmin\modules\Customer\CustomerController;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class NewsletterController extends CustomerController
{
    public $enableCsrfValidation = true;
    
    public function actionIndex()
    {
        $data = $this->getBlock()->getLastData();

        return $this->render($this->action->id, $data);
    }


}
