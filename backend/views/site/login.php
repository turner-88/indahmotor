<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'indah motor';
$model->rememberMe = 0;

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback default-focus'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<div class="login-box">
    <div class="login-logo">
        <h4 style="font-size: 50px; font-weight: bold;">indah motor</h4><p style="margin-top:-20px; font-size: 20px"><br>Padang, Sumatera Barat, Indonesia</p>
    </div>
    <!-- /.login-logo -->
    <div class="boxxx boxxx-danger">
    <div class="login-boxxx-body">
        <!-- <p class="login-box-msg">Sign in to start your session</p> -->

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <?= Html::submitButton('Sign in', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>


        <?php ActiveForm::end(); ?>

        <!-- <br><a href="#">I forgot my password</a><br> -->

    </div>
    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->

<?php
$js = 
<<<JAVASCRIPT
$('#loginform-username').focus();
JAVASCRIPT;
$this->registerJs($js, \yii\web\VIEW::POS_READY);
?>