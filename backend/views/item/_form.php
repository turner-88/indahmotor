<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use backend\models\UnitOfMeasurement;
use backend\models\PriceGroup;
use backend\models\ItemPrice;

/* @var $this yii\web\View */
/* @var $model backend\models\Item */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-md-4">

        <?= $form->field($model, 'shortcode')->textInput(['maxlength' => true, /* 'disabled' => Yii::$app->user->can('salesman') */]) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true, /* 'disabled' => Yii::$app->user->can('salesman') */]) ?>

        <?= $form->field($model, 'brand')->textInput(['maxlength' => true, /* 'disabled' => Yii::$app->user->can('salesman') */]) ?>

        <?= $form->field($model, 'type')->textInput(['maxlength' => true, /* 'disabled' => Yii::$app->user->can('salesman') */]) ?>

        <?= $form->field($model, 'unit_of_measurement')->textInput(['maxlength' => true, /* 'disabled' => Yii::$app->user->can('salesman') */]) ?>

    </div>

    <div class="col-md-4">

        <?= $form->field($model, 'current_quantity')->textInput(['disabled' => Yii::$app->user->can('salesman')]) ?>

        <?= $form->field($model, 'purchase_net_price')->textInput(['disabled' => Yii::$app->user->can('salesman')]) ?>

        <?= $form->field($model, 'purchase_discount')->textInput(['onkeyup' => 'calculateGrossPrice()', 'disabled' => Yii::$app->user->can('salesman')]) ?>

        <?= $form->field($model, 'purchase_gross_price')->textInput(['disabled' => Yii::$app->user->can('salesman')]) ?>
        
        <?= $form->field($model, 'location')->textInput() ?>

    </div>

    <div class="col-md-4">       

        <label>PRICE GROUP</label>
        <div class="detail-view-container" style="background:transparent">
            <table class="table" style="margin-bottom:10">
                <tr>
                    <td></td>
                    <td>Discount</td>
                    <td>Price</td>
                </tr>
            <?php 
                foreach(($priceGroups = PriceGroup::find()->all()) as $priceGroup) {
                $value_discount = (($itemPrice = ItemPrice::findOne (['item_id' => $model->id, 'price_group_id' => $priceGroup->id])) !== null) ? $itemPrice->discount : null;
                $value_price    = (($itemPrice = ItemPrice::findOne (['item_id' => $model->id, 'price_group_id' => $priceGroup->id])) !== null) ? $itemPrice->price : null;
            ?>
                <tr>
                    <td class="text-right action-column" style="vertical-align:middle">&nbsp;&nbsp;<b><?= $priceGroup->name ?></b></td>
                    <td>
                        <?= Html::textInput($priceGroup->name . '-discount', $value_discount, ['id' => $priceGroup->name . '-discount','class' => 'form-control price-groups', 'placeholder' => 'discount', 'disabled' => Yii::$app->user->can('salesman')]) ?>
                    </td>
                    <td width="60%">
                        <?= Html::textInput($priceGroup->name . '-price', $value_price, ['id' => $priceGroup->name . '-price','class' => 'form-control price-groups', 'placeholder' => 'price', 'disabled' => Yii::$app->user->can('salesman')]) ?>
                    </td>
                </tr>
            <?php } ?>
            </table>
        </div>
    </div>
</div>

<div class="form-panel">
    <div class="row">
        <div class="col-sm-12">
            <?= Html::submitButton('<i class="glyphicon glyphicon-ok"></i> ' . ($model->isNewRecord ? 'Create' : 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>    
</div>

<?php ActiveForm::end(); ?>


<?php 
$this->registerJsFile(
    '@web/js/item.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
?>