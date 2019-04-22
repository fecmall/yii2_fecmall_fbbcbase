<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\models\xunsearch;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Search extends \fecshop\models\xunsearch\Search
{
    public static function projectName()
    {
        return 'search';    // 这将使用 @fecshop/config/xunsearch/search.ini 作为项目名
    }
}
