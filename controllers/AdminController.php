<?php

namespace humhub\modules\firewall\controllers;

use Yii;
use yii\web\HttpException;
use yii\data\ActiveDataProvider;
use humhub\modules\firewall\models\FirewallLog;
use humhub\modules\admin\components\Controller;
use humhub\modules\firewall\models\FirewallRule;
use humhub\modules\firewall\models\forms\FirewallSettingsForm;

/**
 * Admin controller for the firewall module
 */
class AdminController extends Controller
{
    /**
     * Show details of a single firewall rule
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => FirewallRule::find(),
            'pagination' => false,
            'sort' => ['defaultOrder' => ['priority' => SORT_ASC, 'id' => SORT_ASC]],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Create a new firewall rule
     */
    public function actionCreate()
    {
        $model = new FirewallRule();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->success(Yii::t('FirewallModule.base', 'Firewall rule created successfully'));
            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Update an existing firewall rule
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->success(Yii::t('FirewallModule.base', 'Firewall rule updated successfully'));
            return $this->redirect(['index', 'id' => $model->id]);
        }

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Delete a firewall rule
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        $this->view->success(Yii::t('FirewallModule.base', 'Firewall rule deleted successfully'));
        return $this->redirect(['index']);
    }

    /**
     * Toggle the status of a firewall rule
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = !$model->status;
        
        if ($model->save()) {
            $this->view->success(Yii::t('FirewallModule.base', 'Firewall rule status toggled successfully'));
        } else {
            $this->view->error(Yii::t('FirewallModule.base', 'Failed to toggle rule status'));
        }

        return $this->redirect(['index']);
    }

    /**
     * View firewall access logs
     */
    public function actionLogs()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => FirewallLog::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->renderAjax('logs', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Clear all firewall logs
     */
    public function actionClearLogs()
    {
        if (Yii::$app->request->isPost) {
            try {
                $deleted = FirewallLog::deleteAll();

                if ($deleted > 0) {
                    $this->view->success(Yii::t('FirewallModule.base', '{count} firewall logs have been cleared', ['count' => $deleted]));
                } else {
                    $this->view->info(Yii::t('FirewallModule.base', 'No logs to clear'));
                }
            } catch (\Exception $e) {
                Yii::error('Error clearing logs: ' . $e->getMessage());
                $this->view->error(Yii::t('FirewallModule.base', 'Error clearing logs'));
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Configure firewall settings
     */
    public function actionSettings()
    {
        $model = new FirewallSettingsForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->view->success(Yii::t('FirewallModule.base', 'Settings saved successfully'));
            return $this->redirect(['settings']);
        }

        return $this->render('settings', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the FirewallRule model based on its primary key value.
     * @param integer $id
     * @return FirewallRule the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FirewallRule::findOne($id)) !== null) {
            return $model;
        }

        throw new HttpException(404, Yii::t('FirewallModule.base', 'The requested firewall rule does not exist.'));
    }
}
