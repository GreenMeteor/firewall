<?php

use yii\helpers\Html;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalDialog;
use humhub\modules\firewall\models\FirewallRule;

/**
 * @var $this yii\web\View
 * @var $model humhub\modules\firewall\models\forms\FirewallSettingsForm
 */

$this->title = Yii::t('FirewallModule.base', 'Firewall Settings');

?>
<?php ModalDialog::begin(['header' => $this->title]); ?>
    <div class="modal-body">
        <div class="firewall-settings-form">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'enabled')->checkbox(); ?>

            <?= $form->field($model, 'enableLogging')->checkbox(); ?>

            <?= $form->field($model, 'enableNotifications')->checkbox(); ?>

            <?= $form->field($model, 'defaultAction')->dropDownList([
                FirewallRule::ACTION_ALLOW => Yii::t('FirewallModule.base', 'Allow'),
                FirewallRule::ACTION_DENY => Yii::t('FirewallModule.base', 'Deny'),
            ])->hint(Yii::t('FirewallModule.base', 'Default action when no rule matches')); ?>

            <?= $form->field($model, 'denyMessage')->textarea(['rows' => 3]); ?>

            <div class="model-footer">
                <?= Html::submitButton(Yii::t('FirewallModule.base', 'Save'), ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
<?php ModalDialog::end(); ?>
