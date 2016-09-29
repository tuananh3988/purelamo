<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Notification;
/* @var $this yii\web\View */
/* @var $searchModel common\models\SearchNotification */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notifications';
$this->params['breadcrumbs'][] = $this->title;
//var_dump(Notification::$STATUS);die;
?>
<div class="notification-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Notification', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'subject',
            'message:ntext',
            'status' => [
                'label' => 'Status',
                'content' => function ($data) {
                    return Notification::$STATUS[$data['status']];
                }
            ],
            //'delete_flag',
            'reserve_date',
            // 'send_begin_date',
            // 'send_end_date',
            'create_date',
            // 'last_update_date',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', 'Update'),
                            'aria-label' => Yii::t('yii', 'Update'),
                            'data-pjax' => '0',
                        ];
                        return $model->status !== 1 ? Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options) : '';
                    },
                    'delete' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', 'Delete'),
                            'aria-label' => Yii::t('yii', 'Delete'),
                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ];
                        return $model->status !== 1 ? Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, $options) : '';
                    },
                ]
            ],
        ],
    ]); ?>
</div>
