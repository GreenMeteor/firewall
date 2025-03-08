<?php

use humhub\libs\Html;
use yii\helpers\TimeAgo;

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
                    <div class="guest-ips-container">
                        <h5><?= Yii::t('FirewallModule.base', '<strong>Recent Guest IPs</strong>') ?></h5>
                        <div class="guest-ips-list" style="max-height: 200px; overflow-y: auto;">
                            <?php foreach ($guestIps as $guestIpData): ?>
                                <div class="guest-ip-entry">
                                    <small>
                                        <?= Html::encode($guestIpData['ip'], ENT_QUOTES, 'UTF-8') ?>
                                        <span class="text-muted">
                                            (<?= Yii::t('FirewallModule.base', 'last seen {timeAgo}', [
                                                'timeAgo' => Yii::$app->formatter->asRelativeTime($guestIpData['timestamp'])
                                            ]) ?>)
                                        </span>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
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
