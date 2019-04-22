<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\app\console\modules\Statistics;

use fecshop\app\console\modules\ConsoleModule;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Module extends ConsoleModule
{
    public $blockNamespace;

    public function init()
    {
        // 以下代码必须指定
        $nameSpace = __NAMESPACE__;
        $this->controllerNamespace = $nameSpace . '\\controllers';
        $this->blockNamespace = $nameSpace . '\\block';
        parent::init();
    }
}
