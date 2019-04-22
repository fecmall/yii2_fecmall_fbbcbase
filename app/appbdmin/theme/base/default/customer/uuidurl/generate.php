<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
use yii\helpers\Html;
use fec\helpers\CRequest;
use fecadmin\models\AdminRole;
/** 
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
?>
<style>
.checker{float:left;}
.dialog .pageContent {background:none;}
.dialog .pageContent .pageFormContent{background:none;}

</style>

<div class="pageContent systemConfig"> 
	<form  method="post" action="<?= $saveUrl ?>" class="pageForm required-validate" onsubmit="return navTabSearch(this);">
		<?php echo CRequest::getCsrfInputHtml();  ?>	
		<div layouth="56" class="pageFormContent" style="height: 240px; overflow: auto;">
			
				<fieldset id="fieldset_table_qbe">
					<legend style="color:#cc0000"><?= Yii::$service->page->translate->__('generate uuid url') ?></legend>
					<div>
						<p class="edit_p">
                            <label><?= Yii::$service->page->translate->__('Url') ?>：</label>
                            <input type="text" value="<?= $url ?>" size="30" name="url" class="textInput">
                        </p>												
                    </div>
                    <div class="buttonActive"><div class="buttonContent"><button onclick="func('accept')"  value="accept" name="accept" type="submit"><?= Yii::$service->page->translate->__('generate uuid url') ?></button></div></div>
                
				</fieldset>
                
                <fieldset id="fieldset_table_qbe">
					<legend style="color:#cc0000"><?= Yii::$service->page->translate->__('Uuid Url') ?></legend>
					<div>
						<p class="edit_p">
                            <label><?= Yii::$service->page->translate->__('Uuid Url') ?>：</label>
                            <input type="text" value="<?= $uuid_url ?>" size="30" name="uuid_url" class="textInput">
                        </p>												
                    </div>
				</fieldset>
		</div>
	
	</form>
</div>	

