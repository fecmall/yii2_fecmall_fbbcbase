<?php

/*
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\services\product;

//use fecshop\models\mongodb\product\Review as ReviewModel;
use fecshop\services\Service;
use Yii;
use yii\base\InvalidValueException;

/**
 * Product Review Service
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Review extends \fecshop\services\product\Review
{
    public $filterByLang;

    // 用户购物过的产品才能评论。
    public $reviewOnlyOrderedProduct = true;

    // 订单创建后，多久内可以进行评论，超过这个期限将不能评论产品（单位为月）
    public $reviewMonth = 6;

    protected $_reviewModelName = '\fbbcbase\models\mongodb\product\Review';

    protected $_reviewModel;
    
}
