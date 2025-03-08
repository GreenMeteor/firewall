<?php

use humhub\libs\Html;

/** @var array $accessData */
/** @var array|null $loggedInData */
/** @var array $guestIps */

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('FirewallModule.base', '<strong>IP</strong> Monitoring') ?>
    </div>
    <div class="panel-body">
        <div class="row text-center">
            <div class="col-md-4">
                <?= Yii::t('FirewallModule.base', '<strong>IP Address</strong>') ?>
                <p><?= Html::encode($accessData['ip'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php if (!empty($guestIps)): ?>
                    <?= Yii::t('FirewallModule.base', '<strong>Guest IPs</strong>') ?>
                    <?php foreach ($guestIps as $guestIp): ?>
                        <p><em><?= Yii::t('FirewallModule.base', 'Guest IP:') ?> <?= Html::encode($guestIp, ENT_QUOTES, 'UTF-8') ?></em></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <?= Yii::t('FirewallModule.base', '<strong>User Status</strong>') ?>
                <p><?= $accessData['isGuest'] ? Yii::t('FirewallModule.base', 'Guest') : Yii::t('FirewallModule.base', 'Logged-In') ?></p>
            </div>
            <div class="col-md-4">
                <?= Yii::t('FirewallModule.base', '<strong>Username</strong>') ?>
                <p><?= Html::encode($accessData['username'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
    </div>
</div>
