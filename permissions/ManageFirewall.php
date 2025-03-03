<?php

namespace humhub\modules\firewall\permissions;

use Yii;
use humhub\modules\user\models\User;
use humhub\modules\user\models\Group;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\admin\components\BaseAdminPermission;

/**
 * ManageFirewall permission allows admins to configure firewall rules
 */
class ManageFirewall extends BaseAdminPermission
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
    public function getDefaultState($userId, $contentContainer = null)
    {
        $user = User::findOne(['id' => $userId]);

        if ($user !== null && Group::getAdminGroup()->isMember($user)) {
            return self::STATE_ALLOW;
        }

        return self::STATE_DENY;
    }

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        User::USERGROUP_SELF,
        User::USERGROUP_FRIEND,
        User::USERGROUP_USER,
        User::USERGROUP_GUEST
    ];

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
