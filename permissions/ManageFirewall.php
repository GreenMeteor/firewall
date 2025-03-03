<?php

namespace humhub\modules\firewall\permissions;

use humhub\libs\BasePermission;
use humhub\modules\admin\permissions\ManageModules;
use Yii;

/**
 * ManageFirewall permission allows admins to configure firewall rules
 */
class ManageFirewall extends BasePermission
{
    /**
     * @inheritdoc
     */
    protected $id = 'manage_firewall';

    /**
     * @inheritdoc
     */
    protected $title = 'Manage Firewall';

    /**
     * @inheritdoc
     */
    protected $description = 'Can manage firewall rules and settings';

    /**
     * @inheritdoc
     */
    protected $moduleId = 'firewall';

    /**
     * @inheritdoc
     */
    protected $defaultState = self::STATE_ALLOW;

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('FirewallModule.base', 'Manage Firewall');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('FirewallModule.base', 'Can manage firewall rules and settings');
    }

    /**
     * @inheritdoc
     */
    public function requiredGroups()
    {
        return [
            ['name' => 'admin', 'class' => ManageModules::class]
        ];
    }
}