<?php

namespace humhub\modules\firewall\controllers;

use Yii;
use yii\web\HttpException;
use yii\data\ActiveDataProvider;
use humhub\modules\admin\components\Controller;
use humhub\modules\firewall\models\FirewallRule;
use humhub\modules\firewall\models\FirewallLog;
use humhub\modules\firewall\models\forms\FirewallSettingsForm;

/**
 * Admin controller for the firewall module
 */
class AdminController extends Controller
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->subLayout = '@admin/views/layouts/setting';
        return parent::init();
    }

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

        return $this->render('create', [
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

        return $this->render('update', [
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
        $model->save();

        return $this->redirect(['index', 'id' => $model->id]);
    }

    /**
     * View firewall access logs
     */
    public function actionLogs()
    {
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => FirewallLog::find(),
            'pagination' => ['pageSize' => 50],
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]],
        ]);

        return $this->render('logs', [
            'dataProvider' => $dataProvider,
        ]);
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