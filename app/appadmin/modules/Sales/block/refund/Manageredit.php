<?php
/**
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fbbcbase\app\appadmin\modules\Sales\block\refund;

use fec\helpers\CUrl;
use Yii;
use fecshop\app\appadmin\interfaces\base\AppadminbaseBlockEditInterface;
use fecshop\app\appadmin\modules\AppadminbaseBlockEdit;

/**
 * block cms\article.
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Manageredit extends AppadminbaseBlockEdit implements AppadminbaseBlockEditInterface
{
    public $_saveUrl;

    public function init()
    {
        $this->_saveUrl = CUrl::getUrl('sales/refund/managereditsave');
        parent::init();
    }

    // 传递给前端的数据 显示编辑form
    public function getLastData()
    {
        return [
            'editBar'      => $this->getEditBar(),
            'saveUrl'     => $this->_saveUrl,
        ];
    }
    public function setService()
    {
        $this->_service = Yii::$service->refund;
    }
    
    
    public function getEditArr()
    {
        $deleteStatus = Yii::$service->customer->getStatusDeleted();
        $activeStatus = Yii::$service->customer->getStatusActive();

        return [
            [
                'label'  => Yii::$service->page->translate->__('Id'),
                'name' => 'id',
                'display' => [
                    'type' => 'stringText',

                ],
                'require' => 0,
            ],
            [
                'label'  => Yii::$service->page->translate->__('AS Id'),
                'name' => 'as_id',
                'display' => [
                    'type' => 'stringText',
                ],
                'require' => 0,
            ],
            [
                'label'  => Yii::$service->page->translate->__('Increment Id'),
                'name' => 'increment_id',
                'display' => [
                    'type' => 'stringText',
                ],
                'require' => 1,
            ],

            [
                'label'  => Yii::$service->page->translate->__('Price'),
                'name' => 'price',
                'display' => [
                    'type' => 'stringText',
                ],
                'require' => 0,
            ],
            [
                'label'  => Yii::$service->page->translate->__('Base Price'),
                'name' => 'base_price',
                'display' => [
                    'type' => 'stringText',
                ],
                'require' => 0,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Currency Code'),
                'name' => 'currency_code',
                'display' => [
                    'type' => 'stringText',
                ],
                'require' => 0,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Currency Rate'),
                'name' => 'order_to_base_rate',
                'display' => [
                    'type' => 'stringText',
                ],
                'require' => 0,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Customer Id'),
                'name' => 'customer_id',
                'display' => [
                    'type' => 'stringText',
                ],
                'require' => 0,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Customer Email'),
                'name' => 'customer_email',
                'display' => [
                    'type' => 'stringText',
                ],
                'require' => 0,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Customer Bank'),
                'name' => 'customer_bank',
                'display' => [
                    'type' => 'inputString',
                ],
                'require' => 0,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Customer Bank Name'),
                'name' => 'customer_bank_name',
                'display' => [
                    'type' => 'inputString',
                ],
                'require' => 0,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Customer Bank Account'),
                'name' => 'customer_bank_account',
                'display' => [
                    'type' => 'inputString',
                ],
                'require' => 0,
            ],
            
            
            [
                'label'  => Yii::$service->page->translate->__('Status'),
                'name' => 'status',
                'display' => [
                    'type' => 'select',
                    'data' => Yii::$service->refund->getAllRefundStatusArr(),
                ],
                'require' => 1,
                'default' => 1,
            ],
            
            [
                'label'  => Yii::$service->page->translate->__('Type'),
                'name' => 'type',
                'display' => [
                    'type' => 'select',
                    'data' => [
                        Yii::$service->refund->type_system_admin_refund_order_return  => Yii::$service->page->translate->__(Yii::$service->refund->type_system_admin_refund_order_return),
                        Yii::$service->refund->type_system_admin_refund_order_cancel  => Yii::$service->page->translate->__(Yii::$service->refund->type_system_admin_refund_order_cancel),
       
                    ],
                ],
            ],
            
        ];
    }
    
    public function save(){
        $editForm = Yii::$app->request->post('editFormData');
        $refund_id = $editForm['id'];
        $refundModel = Yii::$service->refund->getByPrimaryKey($refund_id);
        $status = $refundModel['status'];
        if (is_array($editForm) && $refundModel['id']) {
            $refundModel['customer_bank'] = $editForm['customer_bank'];
            $refundModel['customer_bank_name'] = $editForm['customer_bank_name'];
            $refundModel['customer_bank_account'] = $editForm['customer_bank_account'];
            $refundModel->save();
            if (
                $status == Yii::$service->refund->status_payment_pending
                && $editForm['status'] == Yii::$service->refund->status_payment_confirmed
            ) {
                if (!Yii::$service->refund->payReturnRefund($refund_id, 'admin')) {
                    $errors = Yii::$service->helper->errors->get();
                    echo json_encode([
                        'statusCode' => '300',
                        'message' => $errors ,
                        // order after sale return after_sale_id: , status: can not refund
                    ]);
                    exit;
                }
            }
            
            
        }
        echo  json_encode([
            'statusCode' => '200',
            'message' => Yii::$service->page->translate->__('Save Success'),
        ]);
        exit;
    }
   
}
