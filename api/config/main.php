<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
        'rbac' => [
            'class' => 'wind\rest\modules'
        ],
        'oauth2' => [
            'class' => 'filsh\yii2\oauth2server\Module',
            'tokenParamName' => 'token',
            'tokenAccessLifetime' => 3600 * 24,
            'storageMap' => [
                'user_credentials' => 'common\models\User',
            ],
            'grantTypes' => [
                'user_credentials' => [
                    'class' => 'OAuth2\GrantType\UserCredentials',
                ],
                'client_credentials' => [
                    'class' => 'OAuth2\GrantType\ClientCredentials',
                ],
                'refresh_token' => [
                    'class' => 'OAuth2\GrantType\RefreshToken',
                    'always_issue_new_refresh_token' => true
                ],
                'authorization_code' => [
                    'class' => 'OAuth2\GrantType\AuthorizationCode'
                ],
            ]
        ],
    ],

    'components' => [
        'authManager' => [
            'class' => 'wind\rest\components\DbManager', //配置文件
        ],
        'request' => [
            'csrfParam' => '_csrf-api',
            'enableCookieValidation' => true,
            'enableCsrfValidation' => true,
            'cookieValidationKey' => '1465018095491432744-127-1-582-3319077789',
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $response->data = [
                    'code' => $response->getStatusCode(),
                    'data' => $response->data,
                    'message' => $response->statusText
                ];
                $response->format = yii\web\Response::FORMAT_JSON;
            },
        ],

    // api-token
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'enableSession'=>false
        ],
        'session' => [
            // this is the name of the session cookie used for login on the api
            'name' => 'advanced-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
//            'enableStrictParsing' =>true,
            'rules' => [
                //权限
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['rbac/permission'],
                    'extraPatterns' => [
                        'GET view' => 'view',
                        'DELETE delete' => 'delete',
                        'POST update' => 'update',
                        'POST assign' => 'assign',
                        'POST remove' => 'remove',
                        'GET assign-list' => 'assign-list',
                    ]
                ],
                //菜单
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['rbac/menu'],
                    'extraPatterns' => [
                        'GET parent' => 'parent',
                        'POST create' => 'create',
                        'POST update' => 'update',
                        'GET user' => 'user-menu'
                    ]
                ],
                //路由
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['rbac/route'],
                    'extraPatterns' => [
                        'POST remove' => 'remove',
                        'GET  all' => 'all',
                        'GET  parent' => 'parent',
                    ]
                ],
                //角色
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['rbac/role'],
                    'extraPatterns' => [
                        'GET view' => 'view',
                        'DELETE delete' => 'delete',
                        'POST update' => 'update',
                        'POST assign' => 'assign',
                        'GET assign-list' => 'assign-list',
                        'POST remove' => 'remove',
                    ]
                ],
                //分配
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['rbac/assignment'],
                    'extraPatterns' => [
                        'GET view' => 'view',
                        'POST assign' => 'assign',
                        'POST revoke' => 'revoke',
                        'GET assign-list' => 'assign-list',
                        'POST remove' => 'remove',
                        'POST assign-batch' => 'assign-batch',
                        'POST assign-remove' => 'remove-users',
                        'GET assign-users' => 'assign-users',
                    ]
                ],
                //用户
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['rbac/user'],
                    'extraPatterns' => [
                        'GET view' => 'view',
                        'POST activate' => 'activate',
                    ]
                ],
                //规则
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['rbac/rule'],
                    'extraPatterns' => [
                        'GET index' => 'get-rules',
                        'POST create' => 'create',
                        'POST delete' => 'delete',
                        'POST update' => 'update',
                    ]
                ],
                //分组
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['rbac/groups'],
                    'extraPatterns' => [
                        'POST assign' => 'assign',
                        'POST revoke' => 'revoke',
                        'GET assign-user' => 'assign-user',
                    ]
                ]
            ],
        ]
    ],
    'as access' => [
        'class' => 'wind\rest\components\AccessControl',
        'allowActions' => [
            'site/*',//允许访问的节点，可自行添加
            'rbac/menu/user-menu',
            'oauth2/*',
        ]
    ],
    'params' => $params,
];
