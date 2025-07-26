<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Change Password';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-changepassword">

<div class="row">
<div class="col-md-8 col-sm-12">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->
    <div class="wow fadeIn" data-wow-delay="800ms">
    <p>Please fill out the following fields to change your password: </p>
    <br>

    <?php $form = ActiveForm::begin([
        'id'=>'changepassword-form',
    ]); ?>

    <?= $form->field($model,'oldpass',['inputOptions'=>[
        'placeholder'=>''
    ]])->passwordInput() ?>
   
    <?= $form->field($model,'newpass',['inputOptions'=>[
        'placeholder'=>''
    ]])->passwordInput() ?>
   
    <?= $form->field($model,'repeatnewpass',['inputOptions'=>[
        'placeholder'=>''
    ]])->passwordInput() ?>
   
    <div class="form-panel">
        <div class="row">
    	    <div class="col-sm-12">
    	        <?= Html::submitButton('Change password', [
                    'class' => 'btn btn-primary'
                ]) ?>
            </div>
	    </div>
    </div>
    
    <?php ActiveForm::end(); ?>
    </div>

</div>
</div>

</div>