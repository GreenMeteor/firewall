<?php

namespace humhub\modules\firewall;

use Yii;
use humhub\components\Module as BaseModule;
use humhub\modules\firewall\models\FirewallRule;
use humhub\modules\firewall\components\FirewallManager;

/**
 * Firewall Module
 *
 * Provides IP-based firewall functionality for HumHub installations
 */
class Module extends BaseModule
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
    public $denyView = '@firewall/views/deny/index';

    /**
     * @var array List of route patterns that should be excluded from firewall checks
     */
    public $excludedRoutes = [
        'admin/*',
        'firewall/*'
    ];

    /**
     * @var array List of routes that should be protected by the firewall
     */
    public $protectedRoutes = [
        'dashboard',
        'user/auth/login',
    ];

    public function init()
    {
        parent::init();

        if (!Yii::$app->has('manager')) {
            Yii::$app->set('manager', [
                'class' => FirewallManager::class,
            ]);
        }

        if ($this->enableFirewall) {
            Yii::$app->on(\yii\web\Application::EVENT_BEFORE_REQUEST, [$this, 'checkFirewall']);
        }

        if ($tourModule = Yii::$app->getModule('tour')) {
            $tourFile = __DIR__ . '/tours/firewall-admin.php';
            if (file_exists($tourFile) && !in_array($tourFile, $tourModule->tourConfigFiles)) {
                $tourModule->tourConfigFiles[] = $tourFile;
            }
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
     * Checks if the firewall is enabled
     *
     * @return bool whether the firewall is enabled
     */
    protected function isFirewallEnabled()
    {
        return Yii::$app->getModule('firewall')->settings->get('enabled', true);
    }

    /**
     * Checks if the current IP is allowed according to firewall rules
     * 
     * @param string|null $ip IP address to check (defaults to current user IP)
     * @return bool whether access is allowed
     */
    public function checkAccess($ip = null)
    {
        if (!$this->isFirewallEnabled()) {
            return true;
        }

        if ($ip === null) {
            $ip = Yii::$app->request->userIP;
        }

        $currentRoute = $this->getCurrentRoute();
        if ($currentRoute !== null) {
            foreach ($this->excludedRoutes as $excludedRoute) {
                if (fnmatch($excludedRoute, $currentRoute)) {
                    return true;
                }
            }
        }

        $firewallManager = Yii::$app->get('manager');
        if (!$firewallManager) {
            Yii::error("Firewall manager component not found", 'firewall');
            return true;
        }

        return $firewallManager->checkAccess($ip);
    }

    /**
     * Gets the current route in a reliable way
     * 
     * @return string|null The current route or null if not available
     */
    protected function getCurrentRoute()
    {
        if (Yii::$app->controller) {
            return Yii::$app->controller->route;
        }
        
        return Yii::$app->requestedRoute;
    }

    /**
     * Event handler for checking firewall rules
     */
    public function checkFirewall()
    {
        if (Yii::$app instanceof \yii\console\Application) {
            return;
        }

        $ip = Yii::$app->request->userIP;
        $currentRoute = $this->getCurrentRoute();

        if ($currentRoute !== null) {
            foreach ($this->excludedRoutes as $excludedRoute) {
                if (fnmatch($excludedRoute, $currentRoute)) {
                    return;
                }
            }
        }

        $isProtectedRoute = false;
        if ($currentRoute !== null) {
            foreach ($this->protectedRoutes as $protectedRoute) {
                if (fnmatch($protectedRoute, $currentRoute)) {
                    $isProtectedRoute = true;
                    break;
                }
            }
        }

        if (!$isProtectedRoute) {
            return;
        }

        $firewallManager = Yii::$app->get('manager');
        if (!$firewallManager) {
            Yii::error("Firewall manager not found", 'firewall');
            return;
        }

        $allowed = $firewallManager->checkAccess($ip);

        if (!$allowed) {
            Yii::$app->response->statusCode = 403;
            Yii::$app->response->content = Yii::$app->view->render($this->denyView, [
                'ip' => $ip,
                'route' => $currentRoute
            ]);
            Yii::$app->response->send();
            Yii::$app->end();
        }
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
