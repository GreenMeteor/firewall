<?php

use humhub\helpers\Html;

$this->title = Yii::t('FirewallModule.base', 'Access Denied');

?>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card border-danger shadow-lg" style="max-width: 500px;">
        <div class="card-header bg-danger text-white text-center">
            <h3 class="mb-0"><?= Yii::t('FirewallModule.base', 'Access Denied') ?></h3>
        </div>
        <div class="card-body text-center">
            <p class="fs-5 text-danger">
                <i class="fas fa-exclamation-triangle"></i> 
                <?= Yii::t('FirewallModule.base', 'Your IP address ({ip}) has been blocked by the firewall.', ['ip' => Html::encode($ip)]) ?>
            </p>
            <p class="text-muted">
                <?= Yii::t('FirewallModule.base', 'If you believe this is in error, please contact the site administrator.') ?>
            </p>
        </div>
    </div>
</div>
