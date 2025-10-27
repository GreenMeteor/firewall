<?php

use humhub\widgets\form\ActiveForm;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;
use humhub\modules\firewall\models\FirewallRule;

/**
 * @var $this yii\web\View
 * @var $model humhub\modules\firewall\models\forms\FirewallSettingsForm
 */

$this->title = Yii::t('FirewallModule.base', 'Firewall Settings');

?>
<?php $form = Modal::beginFormDialog([
    'title' => $this->title,
    'footer' => ModalButton::cancel() . ' ' . ModalButton::save()->submit(),
]); ?>

    <div class="firewall-settings-form">
        <?= $form->field($model, 'enabled')->checkbox(); ?>

        <?= $form->field($model, 'enableLogging')->checkbox(); ?>

        <?= $form->field($model, 'enableNotifications')->checkbox(); ?>

        <?= $form->field($model, 'defaultAction')->dropDownList([
            FirewallRule::ACTION_ALLOW => Yii::t('FirewallModule.base', 'Allow'),
            FirewallRule::ACTION_DENY => Yii::t('FirewallModule.base', 'Deny'),
        ])->hint(Yii::t('FirewallModule.base', 'Default action when no rule matches')); ?>

        <?= $form->field($model, 'denyMessage')->textarea(['rows' => 3]); ?>
    </div>

<?php Modal::endFormDialog(); ?>