<?php

use humhub\widgets\modal\Modal;

/**
 * @var $this yii\web\View
 * @var $model humhub\modules\firewall\models\FirewallRule
 */

$this->title = Yii::t('FirewallModule.base', 'Create Firewall Rule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('FirewallModule.base', 'Firewall Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php Modal::beginDialog(['title' => $this->title]); ?>
    <?= $this->render('_form', ['model' => $model]) ?>
<?php Modal::endDialog(); ?>