<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
return [
    'bdmin' => [
        'class' => 'fbbcbase\services\Bdmin',
        // 子服务
        'childService' => [
            'menu' => [
                'class'        => 'fbbcbase\services\bdmin\Menu',
                'menuConfig' => [
                    // 一级大类
                    'catalog' => [
                        'label' => 'Category & Prodcut',
                        'child' => [
                            // 二级类
                            'product_manager' => [
                                'label' => 'Manager Product',
                                'child' => [
                                    // 三级类
                                    'product_info_manager' => [
                                        'label' => 'Product Info',
                                        'url_key' => '/catalog/productinfo/index',
                                    ],
                                    // 三级类
                                    'product_review_manager' => [
                                        'label' => 'Product Reveiew',
                                        'url_key' => '/catalog/productreview/index',
                                    ],
                                    
                                    'product_favorite_manager' => [
                                        'label' => 'Product Favorite',
                                        'url_key' => '/catalog/productfavorite/index',
                                    ],
                                ]
                            ]
                        ]
                    ],
                    
                    'CMS' => [
                        'label' => 'CMS',
                        'child' => [
                            'base_info' => [
                                'label' => 'Base Info Config',
                                'url_key' => '/cms/baseinfo/manager',
                            ],
                            'shipping_theme_info' => [
                                'label' => 'Shipping Theme Config',
                                'url_key' => '/cms/shipping/manager',
                            ],
                            /*
                            'home_page' => [
                                'label' => 'Home Page Config',
                                'url_key' => '/cms/homepage/manager',
                            ],
                            */
                            
                        ],
                    ],
                    
                    
                    'sales' => [
                        'label' => 'Sales',
                        'child' => [
                            'order_info' => [
                                'label' => 'Order Info',
                                'child' => [
                                    'order_manager' => [
                                        'label' => 'All Order List',
                                        'url_key' => '/sales/orderinfo/manager',
                                    ],
                                ],
                            ],
                            'order' => [
                                'label' => 'Order Process',
                                'child' => [
                                    'order_pending_edit_manager' => [
                                        'label' => 'Pending Order Edit',
                                        'url_key' => '/sales/orderedit/manager',
                                    ],
                                    'order_cancel_manager' => [
                                        'label' => 'Order Cancel Audit',
                                        'url_key' => '/sales/ordercancel/manager',
                                    ],
                                    'order_audit_manager' => [
                                        'label' => 'Order Info Audit',
                                        'url_key' => '/sales/orderaudit/manager',
                                    ],
                                    'order_export' => [
                                        'label' => 'Dispatch Order Export',
                                        'url_key' => '/sales/orderexport/manager',
                                    ],
                                    'order_dispatch' => [
                                        'label' => 'Order Dispatch',
                                        'url_key' => '/sales/orderdispatch/manager',
                                    ],
                                    'order_received' => [
                                        'label' => 'Order Received List',
                                        'url_key' => '/sales/orderreceived/manager',
                                    ],
                                ],
                            ],
                            
                            
                            'order_after_sale' => [
                                'label' => 'Order After Sale',
                                'child' => [
                                    'order_return_wainting' => [
                                        'label' => 'Return Wainting',
                                        'url_key' => '/sales/returnwaiting/manager',
                                    ],
                                    'order_return_received' => [
                                        'label' => 'Return Receive',
                                        'url_key' => '/sales/returnreceive/manager',
                                    ],
                                    'order_return_refund' => [
                                        'label' => 'Return Refund',
                                        'url_key' => '/sales/returnrefund/manager',
                                    ],
                                ],
                            ],
                            
                            'refund' => [
                                'label' => 'Refund',
                                'child' => [
                                    'bdmin_refund' => [
                                        'label' => 'Bdmin Refund',
                                        'url_key' => '/sales/refund/manager',
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
                    /*
                    'customer' => [
                        'label' => 'Manager User',
                        'child' => [
                            'account' => [
                                'label' => 'Manager Account',
                                'url_key' => '/customer/account/index',
                            ],
                            'uuidurl' => [
                                'label' => 'Uuid Url Generate',
                                'url_key' => '/customer/uuidurl/generate',
                            ],
                        ],
                    ],
                    */
                    'dashboard' => [
                        'label' => 'Dashboard',
                        'child' => [
                            'adminuser' => [
                                'label' => 'Admin User',
                                'child' => [
                                    'myaccount' => [
                                        'label' => 'My Account',
                                        'url_key' => '/fecbdmin/myaccount/index',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];