<?php
/**
 * FecShop file.
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */
return [
    'admin' => [
        // 子服务
        'childService' => [
            'urlKey' => [
                'urlKeyTags' => [
                    'supplier-account' 							=> 'Supplier Account',
                    'order-after-sale' 							=> 'Order After Sale',
                    'order-refund' 							    => 'Refund',
                    'order-statistics' 							=> 'Order Statistics',
                    'config-homepage' 							=> 'Config Home Page',
                    'config-baseinfo' 							=> 'Config Base Info',
                    'order-log' 							            => 'Order Log',
                ],
                
            ],
            
                            
            'menu' => [
                'menuConfig' => [
                    'Supplier' => [
                        'label' => 'Manager Supplier',
                        'child' => [
                            'account' => [
                                'label' => 'Manager Supplier',
                                'url_key' => '/supplier/account/index',
                            ],
                        ],
                    ],
                    
                    'sales' => [
                        'label' => 'Sales',
                        'child' => [
                            'order' => [
                                'label' => 'Order Info',
                                'child' => [
                                    'order_manager' => [
                                        'label' => 'All Order List',
                                        'url_key' => '/sales/orderinfo/manager',
                                    ],
                                ],
                            ],
                            
                            
                            'order_after_sale' => [
                                'label' => 'Order After Sale',
                                'child' => [
                                    'order_return_list' => [
                                        'label' => 'Return List',
                                        'url_key' => '/sales/returnlist/manager',
                                    ],
                                    
                                ],
                            ],
                            
                            'refund' => [
                                'label' => 'Refund',
                                'child' => [
                                    'admin_refund' => [
                                        'label' => 'Admin Refund',
                                        'url_key' => '/sales/refund/manager',
                                    ],
                                    'bdmin refund' => [
                                        'label' => 'Bdmin Refund',
                                        'url_key' => '/sales/refundbdmin/manager',
                                    ],
                                    
                                    
                                ],
                            ],
                            
                            'order_statistics' => [
                                'label' => 'Order Statistics',
                                'child' => [
                                    'order_month_settle' => [
                                        'label' => 'Order Month Settle',
                                        'url_key' => '/sales/ordersettle/manager',
                                    ],
                                    
                                ],
                            ],
                            
                            'order_log' => [
                                'label' => 'Order Log',
                                'child' => [
                                    'order_operate_log' => [
                                        'label' => 'Order Operate Log',
                                        'url_key' => '/sales/orderlog/manager',
                                    ],
                                    
                                ],
                            ],
                        ],
                    ],
                    
                    'cms' => [
                        'child' => [
                            
                            'base_info' => [
                                'label' => 'Base Info Config',
                                'url_key' => '/cms/baseinfo/manager',
                            ],
                            
                            'home_page' => [
                                'label' => 'Home Page Config',
                                'url_key' => '/cms/homepage/manager',
                            ],
                            
                        ],
                    ],
                    
                ],
            ],
            
        ],
    ],
];