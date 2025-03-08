<?php

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\modules\firewall\models\FirewallRule;

/** @var $model humhub\modules\firewall\models\FirewallRule */

$this->title = Yii::t('FirewallModule.base', 'Block IP Address');

?>

<?php ModalDialog::begin(['header' => $this->title]); ?>
    <?php $form = ActiveForm::begin(['id' => 'block-ip-form']); ?>
    <div class="modal-body">
        <?= $form->field($model, 'ip_range')->textInput(['maxlength' => true]); ?>
        <?= $form->field($model, 'description')->textarea(['rows' => 3]); ?>
        <?= $form->field($model, 'priority')->textInput(['type' => 'number']); ?>
        <?= $form->field($model, 'status')->checkbox(); ?>
    </div>
    <div class="modal-footer">
        <?= ModalButton::submitModal(Url::to(['block-ip']), Yii::t('FirewallModule.base', 'Block'), ['data-form-id' => 'block-ip-form']); ?>
        <?= ModalButton::cancel(); ?>
    </div>
    <?php ActiveForm::end(); ?>
<?php ModalDialog::end(); ?>
