<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\appbdmin\interfaces\base;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
interface AppbdminbaseBlockEditInterface
{
    /**
     * set Service ,like $this->_service 	= Yii::$service->cms->article;.
     */
    public function setService();

    /**
     * config edit array.
     */
    public function getEditArr();
}
