<?php

namespace humhub\modules\firewall\models;

use Yii;
use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use humhub\modules\firewall\components\FirewallManager;

/**
 * This is the model class for table "firewall_rule".
 *
 * @property int $id
 * @property string $ip_range
 * @property string $action
 * @property string $description
 * @property int $priority
 * @property bool $status
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class FirewallRule extends ActiveRecord
{
    /**
     * Action constants
     */
    const ACTION_ALLOW = 'allow';
    const ACTION_DENY = 'deny';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'firewall_rule';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip_range', 'action'], 'required'],
            ['action', 'in', 'range' => [self::ACTION_ALLOW, self::ACTION_DENY]],
            ['description', 'string'],
            ['priority', 'integer'],
            ['status', 'boolean'],
            ['status', 'default', 'value' => true],
            ['priority', 'default', 'value' => 100],
            ['ip_range', 'validateIpRange'],
        ];
    }
    
    /**
     * Validates the IP range format
     * 
     * @param string $attribute the attribute being validated
     * @param array $params additional parameters
     */
    public function validateIpRange($attribute, $params)
    {
        $value = $this->$attribute;
        
        if (strpos($value, '/') !== false) {
            // Validate CIDR
            list($ip, $bits) = explode('/', $value);
            if (!filter_var($ip, FILTER_VALIDATE_IP) || !is_numeric($bits) || $bits < 0 || $bits > 32) {
                $this->addError($attribute, Yii::t('FirewallModule.base', 'Invalid CIDR notation'));
            }
        } else if (strpos($value, '-') !== false) {
            // Validate IP range
            list($start, $end) = explode('-', $value);
            if (!filter_var($start, FILTER_VALIDATE_IP) || !filter_var($end, FILTER_VALIDATE_IP)) {
                $this->addError($attribute, Yii::t('FirewallModule.base', 'Invalid IP range'));
            }
            
            // Check if start is less than end
            if (ip2long($start) > ip2long($end)) {
                $this->addError($attribute, Yii::t('FirewallModule.base', 'Start IP must be less than end IP'));
            }
        } else if (strpos($value, '*') !== false) {
            // Validate wildcard notation
            $pattern = str_replace('*', '0', $value);
            if (!filter_var($pattern, FILTER_VALIDATE_IP)) {
                $this->addError($attribute, Yii::t('FirewallModule.base', 'Invalid wildcard IP format'));
            }
        } else {
            // Validate single IP
            if (!filter_var($value, FILTER_VALIDATE_IP)) {
                $this->addError($attribute, Yii::t('FirewallModule.base', 'Invalid IP address'));
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('FirewallModule.base', 'ID'),
            'ip_range' => Yii::t('FirewallModule.base', 'IP Range'),
            'action' => Yii::t('FirewallModule.base', 'Action'),
            'description' => Yii::t('FirewallModule.base', 'Description'),
            'priority' => Yii::t('FirewallModule.base', 'Priority'),
            'status' => Yii::t('FirewallModule.base', 'Status'),
            'created_at' => Yii::t('FirewallModule.base', 'Created At'),
            'created_by' => Yii::t('FirewallModule.base', 'Created By'),
            'updated_at' => Yii::t('FirewallModule.base', 'Updated At'),
            'updated_by' => Yii::t('FirewallModule.base', 'Updated By'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
    
    /**
     * Returns a formatted action label
     * 
     * @return string the action label
     */
    public function getActionLabel()
    {
        if ($this->action === self::ACTION_ALLOW) {
            return '<span class="label label-success">' . Yii::t('FirewallModule.base', 'Allow') . '</span>';
        } else {
            return '<span class="label label-danger">' . Yii::t('FirewallModule.base', 'Deny') . '</span>';
        }
    }
    
    /**
     * Returns a formatted status label
     * 
     * @return string the status label
     */
    public function getStatusLabel()
    {
        if ($this->status) {
            return '<span class="label label-success">' . Yii::t('FirewallModule.base', 'Active') . '</span>';
        } else {
            return '<span class="label label-default">' . Yii::t('FirewallModule.base', 'Inactive') . '</span>';
        }
    }
    
    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Clear the cache
        Yii::$app->getModule('firewall')->get('manager')->clearCache();
    }
    
    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        
        // Clear the cache
        Yii::$app->getModule('firewall')->get('manager')->clearCache();
    }
}