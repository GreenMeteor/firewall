<?php

namespace humhub\modules\firewall\models\forms;

use Yii;
use yii\base\Model;
use humhub\modules\firewall\models\FirewallRule;

/**
 * FirewallSettingsForm handles the firewall module configuration
 */
class FirewallSettingsForm extends Model
{
    /**
     * @var boolean whether to enable the firewall
     */
    public $enabled;
    
    /**
     * @var boolean whether to log blocked requests
     */
    public $enableLogging;
    
    /**
     * @var boolean whether to notify admins on blocked requests
     */
    public $enableNotifications;
    
    /**
     * @var string default action (allow/deny) if no rule matches
     */
    public $defaultAction;
    
    /**
     * @var string custom message to show when access is denied
     */
    public $denyMessage;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $settings = Yii::$app->getModule('firewall')->settings;
        
        $this->enabled = $settings->get('enabled', true);
        $this->enableLogging = $settings->get('enableLogging', true);
        $this->enableNotifications = $settings->get('enableNotifications', false);
        $this->defaultAction = $settings->get('defaultAction', FirewallRule::ACTION_DENY);
        $this->denyMessage = $settings->get('denyMessage', Yii::t('FirewallModule.base', 'Access denied by firewall rules.'));
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled', 'enableLogging', 'enableNotifications'], 'boolean'],
            ['defaultAction', 'in', 'range' => [FirewallRule::ACTION_ALLOW, FirewallRule::ACTION_DENY]],
            ['denyMessage', 'string'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'enabled' => Yii::t('FirewallModule.base', 'Enable Firewall'),
            'enableLogging' => Yii::t('FirewallModule.base', 'Log Blocked Requests'),
            'enableNotifications' => Yii::t('FirewallModule.base', 'Send Notifications on Blocked Requests'),
            'defaultAction' => Yii::t('FirewallModule.base', 'Default Action'),
            'denyMessage' => Yii::t('FirewallModule.base', 'Deny Message'),
        ];
    }
    
    /**
     * Saves the settings
     * 
     * @return boolean whether the settings have been saved successfully
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $settings = Yii::$app->getModule('firewall')->settings;
        
        $settings->set('enabled', $this->enabled);
        $settings->set('enableLogging', $this->enableLogging);
        $settings->set('enableNotifications', $this->enableNotifications);
        $settings->set('defaultAction', $this->defaultAction);
        $settings->set('denyMessage', $this->denyMessage);
        
        return true;
    }
}