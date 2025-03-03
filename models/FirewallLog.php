<?php

namespace humhub\modules\firewall\models;

use Yii;
use humhub\components\ActiveRecord;

/**
 * This is the model class for table "firewall_log".
 *
 * @property int $id
 * @property string $ip
 * @property string $url
 * @property string $user_agent
 * @property string $created_at
 */
class FirewallLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'firewall_log';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip'], 'required'],
            [['ip', 'url', 'user_agent'], 'string'],
            [['created_at'], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('FirewallModule.base', 'ID'),
            'ip' => Yii::t('FirewallModule.base', 'IP Address'),
            'url' => Yii::t('FirewallModule.base', 'URL'),
            'user_agent' => Yii::t('FirewallModule.base', 'User Agent'),
            'created_at' => Yii::t('FirewallModule.base', 'Timestamp'),
        ];
    }
}