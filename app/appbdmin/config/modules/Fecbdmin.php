<?php
/**
 * FecMall file.
 *
 * @link http://www.fecmall.com/
 * @copyright Copyright (c) 2016 FecMall Software LLC
 * @license http://www.fecmall.com/license
 */
return [
    'fecbdmin' => [
        'class' => '\fecadmin\Module',
        'controllerMap' => [
            'login' => [
        		'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\LoginController',
        	],
            'logout' => [
        		'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\LogoutController',
        	],
            'account' => [
        		'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\AccountController',
        	],
        	'cache' => [
        		'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\CacheController',
        	],
            'config' => [
				'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\ConfigController',
			],
            'logtj' => [
				'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\LogtjController',
			],
            'log' => [
				'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\LogController',
			],
            'myaccount' => [
				'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\MyaccountController',
			],
            'index' => [
        		'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\IndexController',
        	],
            'error' => [
        		'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\ErrorController',
        	],
            'systemlog' => [
        		'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\SystemlogController',
        	],
			'resource' => [
				'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\ResourceController',
			],
			'role' => [
				'class' => 'fbbcbase\app\appbdmin\modules\Fecbdmin\controllers\RoleController',
			],
        ],
    ],
];
