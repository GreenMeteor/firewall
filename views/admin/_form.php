<?php

use yii\helpers\Html;
use humhub\widgets\ActiveForm;
use humhub\modules\firewall\models\FirewallRule;

/**
 * @var $this yii\web\View
 * @var $model humhub\modules\firewall\models\FirewallRule
 * @var $form humhub\widgets\ActiveForm
 */
?>

<div class="firewall-rule-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ip_range')->textInput(['maxlength' => true])->hint(Yii::t('FirewallModule.base', 'Can be a single IP (e.g. 192.168.1.1), a CIDR range (e.g. 192.168.1.0/24), a wildcard range (e.g. 192.168.1.*), or an IP range (e.g. 192.168.1.1-192.168.1.100)')); ?>

    <?= $form->field($model, 'action')->dropDownList([
        FirewallRule::ACTION_ALLOW => Yii::t('FirewallModule.base', 'Allow'),
        FirewallRule::ACTION_DENY => Yii::t('FirewallModule.base', 'Deny'),
    ]); ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 3]); ?>

    <?= $form->field($model, 'priority')->textInput(['type' => 'number'])->hint(Yii::t('FirewallModule.base', 'Lower numbers have higher priority')); ?>

    <?= $form->field($model, 'status')->checkbox(); ?>

    <div class="form-group">
        <?= Html::submitButton(
            $model->isNewRecord ? 
                Yii::t('FirewallModule.base', 'Create') : 
                Yii::t('FirewallModule.base', 'Update'), 
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>