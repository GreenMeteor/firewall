<?php

use yii\helpers\Url;
use humhub\helpers\Html;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;
use humhub\modules\firewall\models\FirewallRule;

/** @var $model humhub\modules\firewall\models\FirewallRule */

$this->title = Yii::t('FirewallModule.base', 'Block IP Address');

?>

<?php $form = Modal::beginFormDialog([
    'title' => $this->title,
    'footer' => ModalButton::cancel() . ' ' . ModalButton::save(Yii::t('FirewallModule.base', 'Block'))->submit(),
    'form' => ['id' => 'block-ip-form'],
]); ?>

    <?= $form->field($model, 'ip_range')->textInput(['maxlength' => true]); ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]); ?>
    <?= $form->field($model, 'priority')->textInput(['type' => 'number']); ?>
    <?= $form->field($model, 'status')->checkbox(); ?>


<?php Modal::endFormDialog(); ?>
