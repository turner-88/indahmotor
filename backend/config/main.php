<?php

use kartik\datecontrol\Module;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'indahmotor-backend',
    'name' => 'indah motor',
    'language' => 'id-ID',
    'sourceLanguage' => 'en-US',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    // 'defaultRoute' => 'hospital/index',
    'bootstrap' => ['log'],
    'modules' => [
        'acf' => [
            'class' => 'mdm\admin\Module',
        ],
        'datecontrol' =>  [
            'class' => '\kartik\datecontrol\Module',
            'displaySettings' => [
                Module::FORMAT_DATE => 'dd MMM yyyy',
                Module::FORMAT_TIME => 'hh:mm:ss a',
                Module::FORMAT_DATETIME => 'dd-MM-yyyy hh:mm:ss a', 
            ],
            'saveSettings' => [
                Module::FORMAT_DATE => 'php:Y-m-d',
                Module::FORMAT_TIME => 'php:H:i:s',
                Module::FORMAT_DATETIME => 'php:U',
            ],
            'displayTimezone' => 'Asia/Jakarta',
            'saveTimezone' => 'UTC',
            'autoWidget' => true,
            'autoWidgetSettings' => [
                Module::FORMAT_DATE => ['type' => 2, 'pluginOptions' => ['autoclose' => true]], 
                Module::FORMAT_DATETIME => [], 
                Module::FORMAT_TIME => [], 
            ],
            // custom widget settings that will be used to render the date input instead of kartik\widgets,
            // this will be used when autoWidget is set to false at module or widget level.
            'widgetSettings' => [
                Module::FORMAT_DATE => [
                    'class' => 'kartik\widgets\DatePicker', 'type' => 1
                ]
            ]                    
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource'
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => false,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            // 'cache' => 'cache',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@mdm/admin/views' => '@backend/views/acf',
                ],
            ],
        ],
        'formatter' => [
            'nullDisplay' => '',
            'dateFormat' => 'php:d/m/Y',
        ],
    ],
    'params' => $params,
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'site/*',
            'gridview/*',
            'datecontrol/*',
			// 'debug/*',
            // '*',
        ]
    ],
];
