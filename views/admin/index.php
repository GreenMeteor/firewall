<?php

use humhub\helpers\Html;
use humhub\widgets\bootstrap\Button;
use yii\widgets\DetailView;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\firewall\widgets\IPMonitor;
use humhub\modules\firewall\assets\FirewallAssets;

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

FirewallAssets::register($this);

$this->title = Yii::t('FirewallModule.base', 'Firewall Rules');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="panel">
    <div class="panel-heading">
        <?= Yii::t('FirewallModule.base', '<strong>Manage</strong> Firewall Rules'); ?>
    </div>
    <div class="panel-body">
        <?= Html::a(Icon::get('plus') . 'Create Rule', ['create'], [
            'class' => 'btn btn-success',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#globalModal'
        ]) ?>

        <?= Html::a(Icon::get('cogs') . 'Settings', ['settings'], [
            'class' => 'btn btn-primary',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#globalModal'
        ]) ?>

        <?= Html::a(Icon::get('list') . 'Logs', ['logs'], [
            'class' => 'btn btn-danger',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#globalModal'
        ]) ?>
        <hr>

        <?= IPMonitor::widget(); ?>

        <?php if ($dataProvider->getCount() == 0): ?>
            <div class="alert alert-info text-center">
                <strong><?= Yii::t('FirewallModule.base', 'No firewall rules have been added yet.'); ?></strong>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($dataProvider->getModels() as $model): ?>
                    <div class="col-md-4">
                        <div class="firewall-rule-card">
                            <div class="firewall-rule-header">
                                <h4><?= Yii::t('FirewallModule.base', 'Firewall Rule #{id}', ['id' => $model->id]); ?></h4>
                            </div>

                            <div class="firewall-rule-body">
                                <?= DetailView::widget([
                                    'model' => $model,
                                    'options' => ['class' => 'detail-view-custom'],
                                    'template' => '<div class="detail-item"><span class="label">{label}:</span> <span class="value">{value}</span></div>',
                                    'attributes' => [
                                        'ip_range',
                                        [
                                            'attribute' => 'action',
                                            'format' => 'raw',
                                            'value' => $model->getActionLabel(),
                                        ],
                                        'description',
                                        'priority',
                                        [
                                            'attribute' => 'status',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                $statusLabel = $model->getStatusLabel();
                                                $toggleButton = Html::a(
                                                    '<i class="fa fa-toggle-' . ($model->status ? 'on' : 'off') . '"></i>',
                                                    ['toggle-status', 'id' => $model->id],
                                                    [
                                                        'class' => 'btn btn-sm btn-' . ($model->status ? 'primary' : 'secondary'),
                                                        'title' => Yii::t('FirewallModule.base', 'Toggle Status'),
                                                        'data-pjax' => '0',
                                                    ]
                                                );
                                                return $statusLabel . ' ' . $toggleButton;
                                            },
                                        ],
                                        'created_at:datetime',
                                    ],
                                ]); ?>
                            </div>

                            <div class="firewall-rule-footer">
                                <?= Html::a(Icon::get('pencil') . 'Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary', 'data-bs-toggle' => 'modal', 'data-bs-target' => '#globalModal']) ?>
                                <?= Button::danger(Yii::t('FirewallModule.base', 'Delete'))
                                    ->link(['delete', 'id' => $model->id])
                                    ->icon('trash')
                                    ->confirm(Yii::t('FirewallModule.base', 'Are you sure you want to delete this rule?')); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
