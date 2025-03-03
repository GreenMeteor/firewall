<?php

use yii\helpers\Html;
use humhub\widgets\ModalDialog;

/**
 * @var $this yii\web\View
 * @var $model humhub\modules\firewall\models\FirewallRule
 */

$this->title = Yii::t('FirewallModule.base', 'Create Firewall Rule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('FirewallModule.base', 'Firewall Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php ModalDialog::begin(['header' => $this->title]); ?>
    <div class="modal-body">
        <?= $this->render('_form', ['model' => $model]) ?>
    </div>
<?php ModalDialog::end(); ?>
