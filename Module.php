<?php

namespace humhub\modules\firewall;

use Yii;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\firewall\models\FirewallRule;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

/**
 * Firewall Module
 *
 * Provides IP-based firewall functionality for HumHub installations
 */
class Module extends \humhub\components\Module
{
    /**
     * @inheritdoc
     */
    public $resourcesPath = 'resources';

    /**
     * @inheritdoc
     */
    public $isCoreModule = true;

    /**
     * @var bool Whether to enable the firewall on every request
     */
    public $enableFirewall = true;

    /**
     * @var string Default action for IPs not matching any rule ('allow' or 'deny')
     */
    public $defaultAction = 'allow';

    /**
     * @var string The view to render when access is denied
     */
    public $denyView = '@firewall/views/deny';

    /**
     * @var array List of route patterns that should be excluded from firewall checks
     */
    public $excludedRoutes = [
        'admin/*', // Don't block admin routes to avoid lockouts
        'firewall/*' // Don't block the firewall module itself
    ];

    public function init()
    {
        parent::init();

        // Register the firewall manager component if it's not already set
        if (!Yii::$app->has('manager')) {
            Yii::$app->set('manager', [
                'class' => \humhub\modules\firewall\components\FirewallManager::class,
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Yii::$app->createUrl('/firewall/admin');
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        // This is a core module and cannot be disabled
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer === null) {
            return [
                new permissions\ManageFirewall(),
            ];
        }

        return [];
    }

    /**
     * Checks if the current IP is allowed according to firewall rules
     * 
     * @param string $ip IP address to check (defaults to current user IP)
     * @return bool whether access is allowed
     */
    public function checkAccess($ip = null)
    {
        if (!$this->enableFirewall) {
            return true;
        }

        if ($ip === null) {
            $ip = Yii::$app->request->userIP;
        }

        // Check if current route should be excluded
        $currentRoute = Yii::$app->controller ? Yii::$app->controller->route : null;
        if ($currentRoute !== null) {
            foreach ($this->excludedRoutes as $excludedRoute) {
                if (fnmatch($excludedRoute, $currentRoute)) {
                    return true;
                }
            }
        }

        // Get firewall manager component
        $firewallManager = Yii::$app->getModule('firewall')->get('manager');
        return $firewallManager->checkAccess($ip);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return Yii::t('FirewallModule.base', 'Firewall');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('FirewallModule.base', 'Provides IP-based access control for your HumHub installation');
    }
}