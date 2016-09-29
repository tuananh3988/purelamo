<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notification-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'reserve_date')->textInput(['id' => 'notification-date']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
      jQuery(document).ready(function() {
        $('#notification-date').daterangepicker({
            timePicker: true,
            singleDatePicker: true,
            format: 'YYYY-MM-DD HH:mm:ss',
            timePickerIncrement: 1,
            timePicker12Hour: false,
            "autoApply": true,
        });
      });
</script>