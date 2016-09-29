<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Notification;
/* @var $this yii\web\View */
/* @var $model common\models\Notification */

$this->title = 'Notification View';
$this->params['breadcrumbs'][] = ['label' => 'Notifications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-view">

    <h1><?= Html::encode($this->title) ?></h1>

    

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'subject',
            'message:ntext',
            [                      // the owner name of the model
                'label' => 'Status',
                'value' => Notification::$STATUS[$model['status']],
            ],
            //'delete_flag',
            'reserve_date',
            'send_begin_date',
            'send_end_date',
            'create_date',
            'last_update_date',
        ],
    ]) ?>
    
    
    <p>
        <?= Html::a('Back to list', ['index'], ['class' => 'btn btn-dark']) ?>
        <?php if ($model->status != 1): ?>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?php endif; ?>
    </p>
    
</div>
