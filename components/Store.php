<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */

namespace fbbcbase\components;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Component;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class  Store extends \fecshop\components\Store  implements BootstrapInterface
{
    public $appName;

    public function bootstrap($app)
    {
        if ($this->appName == 'appadmin') {
            Yii::$service->admin->bootstrap($app);
            // 设置third theme： bbc base theme
            $thirdThemeDir = Yii::$service->page->theme->thirdThemeDir;
            if ($bbcBaseThemeDir = Yii::$app->params['bbcBaseThemeDir']) {
                if (is_array($thirdThemeDir) && !empty($thirdThemeDir)) {
                    $thirdThemeDir[] = $bbcBaseThemeDir;
                    Yii::$service->page->theme->thirdThemeDir = $thirdThemeDir;
                } else {
                    Yii::$service->page->theme->thirdThemeDir = [$bbcBaseThemeDir];
                }
            }
        } else if ($this->appName == 'appbdmin'){    
            Yii::$service->bdmin->bootstrap($app);
        } else {
            Yii::$service->store->bootstrap($app);
        }
    }
}
