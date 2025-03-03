<?php

namespace humhub\modules\firewall\controllers;

use Yii;
use humhub\components\Controller;

/**
 * Handles access denied responses
 */
class DenyController extends Controller
{
    /**
     * Skip firewall checks for this controller to avoid infinite loops
     */
    public $firewall = false;
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'acl' => [
                'class' => \humhub\components\behaviors\AccessControl::class,
                'guestAllowedActions' => ['index']
            ]
        ];
    }
    
    /**
     * Renders the access denied view
     */
    public function actionIndex()
    {
        Yii::$app->response->statusCode = 403;
        
        return $this->render('index', [
            'ip' => Yii::$app->request->userIP
        ]);
    }
}
