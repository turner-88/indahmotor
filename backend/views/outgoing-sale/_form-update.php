<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use kartik\grid\GridView;
use kartik\datecontrol\DateControl;
use backend\models\OutgoingType;
use backend\models\Supplier;
use backend\models\Storage;
use backend\models\Customer;
use backend\models\ReturnPlan;
use backend\models\OutgoingItem;
use backend\models\Salesman;
use backend\models\IncomingItem;
use backend\models\Item;
use backend\models\ItemPrice;
use backend\models\PriceGroup;
use kartik\number\NumberControl;

/* @var $this yii\web\View */
/* @var $model backend\models\Incoming */
/* @var $form yii\widgets\ActiveForm */

$shortcode = is_object($modelItem->item) ? $modelItem->item->shortcode : null;
?>

<input type="hidden" id="is_new_record" value="<?= $modelItem->isNewRecord ?>">
<input type="hidden" id="customer_group" value="<?= $model->customer->priceGroup ? $model->customer->priceGroup->name : null ?>">

<div class="incoming-form">

    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne" style="border-bottom: none">
            <h4 class="panel-title">
            <?= Html::a('<i class="glyphicon glyphicon-print"></i>', ['print', 'id' => $model->id, 'to_pdf' => 1], [
                    'class' => 'btn btn-xs btn-default',
                    'target' => '_blank',
                ]) ?>
                <a class="btn btn-default btn-text-warning btn-xs" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <i class="glyphicon glyphicon-pencil text-warning"></i>
                </a>
                &nbsp;
                <?= 
                    'Rp ' . Yii::$app->formatter->asDecimal($model->total, 0) 
                    . ' : <b>' . $model->customer->shortText 
                    . '</b>, ' . ($model->customer->salesman ? $model->customer->salesman->name : '')
                    .', '. Yii::$app->formatter->asDate($model->date) .' &raquo; '. Yii::$app->formatter->asDate($model->due_date) 
                ?>

                <span class="pull-right">
                <?php 
                if (!$model->is_unlimited)
                echo 'Batas: 20 item ' . Html::a('<i class=""></i> ' . 'Buka Batas', ['toggle-limit', 'id' => $model->id], [
                    'class' => 'btn btn-default btn-xs',
                    'data' => [
                        'method' => 'post',
                    ],
                ]);
                else 
                echo 'Batas: Tidak Ada ' . Html::a('<i class=""></i> ' . 'Tutup Batas', ['toggle-limit', 'id' => $model->id], [
                    'class' => 'btn btn-default btn-xs',
                    'data' => [
                        'method' => 'post',
                    ],
                ]);
                ?>
                </span>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body" style="background: #fdfdfd">
                
                <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

                <?php 
                    /* if (\backend\models\Config::findOne(['key' => 'AutomaticPurchaseSerial'])->value == '0') {
                        echo $form->field($model, 'serial')->textInput(['maxlength' => true]);
                    } */
                ?>

                <?= $form->field($model, 'customer_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(Customer::find()->all(), 'id', 'shortText'),
                    'options' => ['placeholder' => ''],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>

                <?= $form->field($model, 'date')->widget(DateControl::classname(), [
                    'type' => DateControl::FORMAT_DATE,
                    'readonly' => true,
                    'ajaxConversion' => false,
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'autoclose' => true
                        ]
                    ]
                ]); ?>

                <?= $form->field($model, 'due_date')->widget(DateControl::classname(), [
                    'type' => DateControl::FORMAT_DATE,
                    'readonly' => true,
                    'ajaxConversion' => false,
                    'widgetOptions' => [
                        'pluginOptions' => [
                            'autoclose' => true
                        ]
                    ]
                ]); ?>

                <?= $form->field($model, 'salesman_id')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(Salesman::find()->all(), 'id', 'name'),
                    'options' => ['placeholder' => ''],
                    'pluginOptions' => ['allowClear' => true],
                ]); ?>

                <?= $form->field($model, 'remark')->textInput(); ?>
                
                <div class="form-panel">
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3">
                            <?= Html::submitButton('<i class="glyphicon glyphicon-ok"></i> ' . Yii::t('app', 'Update'), ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
    
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
        /* 'horizontalCssClasses' => [
            'label' => 'col-sm-3',
            'offset' => 'col-sm-offset-3',
            'wrapper' => 'col-sm-9',
            'error' => '',
            'hint' => '',
        ], */
        ],
    ]); ?>

    <?= Html::activeHiddenInput($modelItem, 'item_id') ?>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label class="control-label col-sm-3">Kode</label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <?= Html::textInput('item_shortcode', $shortcode, ['class' => 'form-control autofocus', 'id' => 'item_shortcode']) ?>
                        <span class="input-group-btn" style="">
                            <a href="#" class="btn btn-default form-control" data-toggle="modal" data-target="#myModal"><i class="fa fa-th-list"></i></a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Nama</label>
                <div class="col-sm-9">
                    <?= Html::textInput('item_name', null, ['class' => 'form-control', 'id' => 'item_name']) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Merk</label>
                <div class="col-sm-9">
                    <?= Html::textInput('item_brand', null, ['class' => 'form-control', 'id' => 'item_brand']) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-2">
            <?php // echo $form->field($modelItem, 'quantity')->textInput()->label('Quantity <span id="unitofmeasurement-label"></span>') ?>
            <div class="form-group">
                <label class="control-label col-sm-4">Qty <span id="unitofmeasurement-label"></span></label>
                <div class="col-sm-8">
                    <?= Html::activeTextInput($modelItem, 'quantity', ['class' => 'form-control']) ?>
                </div>
            </div>
            <!-- <div class="form-group">
                <label class="control-label col-sm-4">Satuan</label>
                <div class="col-sm-8">
                    <?= Html::textInput('item_unit_of_measurement', null, ['class' => 'form-control', 'id' => 'item_unit_of_measurement']) ?>
                </div>
            </div> -->
            <div class="form-group">
                <label class="control-label col-sm-4">Type</label>
                <div class="col-sm-8">
                    <?= Html::textInput('item_type', null, ['class' => 'form-control', 'id' => 'item_type']) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4">Hrg Terakhir</label>
                <div class="col-sm-8">
                    <?= Html::textInput('last_price', null, ['class' => 'form-control', 'id' => 'last_price', 'readonly' => true, 'style' => 'font-weight:bold; color:red']) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-2">
            <div class="form-group">
                <label class="control-label col-sm-4">Disc</label>
                <div class="col-sm-8">
                    <?= Html::activeTextInput($modelItem, 'discount', ['class' => 'form-control']) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4">Harga</label>
                <div class="col-sm-8">
                    <?php // Html::activeTextInput($modelItem, 'price', ['class' => 'form-control']) ?>
                    <?= NumberControl::widget([
                        'model' => $modelItem, 
                        'attribute' => 'price',
                    ]) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4">Box</label>
                <div class="col-sm-8">
                    <?= Html::activeTextInput($modelItem, 'box_number', ['class' => 'form-control']) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-2">

        <div>Net: <span id="net_price">-</span><br><span>List: <span id="gross_price">-</span></span></div>

        <!-- <label>PRICE GROUP</label> -->
        <div class="detail-view-container" style="background:#f4f4f4">
            <table class="table table-condensed small" style="margin:0">
                <!-- <tr>
                    <td></td>
                    <td>Discount</td>
                    <td>Price</td>
                </tr> -->
            <?php 
            foreach (($priceGroups = PriceGroup::find()->all()) as $priceGroup) {
                $value_discount = null;
                $value_price = null;
                $background = $model->customer->priceGroup ? ($model->customer->priceGroup->name == $priceGroup->name ? 'yellow' : 'none') : 'none';
                ?>
                <tr class="price-group-<?= $priceGroup->name ?>" style="background: <?= $background ?>">
                    <td class="text-center action-column">&nbsp;&nbsp;<b><?= $priceGroup->name ?></b></td>
                    <td class="text-right">
                        <span id="<?= $priceGroup->name . '-discount' ?>"></span>
                    </td>
                    <td class="text-right" width="55%">
                        <span id="<?= $priceGroup->name . '-price' ?>"></span>
                    </td>
                </tr>
            <?php 
        } ?>
            </table>
        </div>
    </div>

        <div class="col-sm-2">
            <div class="" style="width:100%">
                <b>Subtotal</b>
                <span id="info_new_item" class="text-danger pull-right small" style="display:none;"><b>** NEW ITEM **</b></span>
            </div>
            <div style="margin-bottom:10px">
                <?php // Html::activeTextInput($modelItem, 'subtotal', ['class' => 'form-control']) ?>
                <?= NumberControl::widget([
                        'model' => $modelItem, 
                        'attribute' => 'subtotal',
                    ]) ?>
            </div>
            <div class="form-panel">
            <?php 
            if ($modelItem->isNewRecord) {
                if (count($model->outgoingItems) == 20) {
                    echo Html::submitButton('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Add in new sale'), ['class' => 'btn btn-success']);
                } else {
                    echo Html::submitButton('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Add'), ['class' => 'btn btn-success']);
                }
            } else {
                echo '<div class="input-group">';
                echo Html::submitButton('<i class="glyphicon glyphicon-ok"></i> ' . Yii::t('app', 'Edit'), ['class' => 'btn btn-primary'])
                    . ' ' . Html::a('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Cancel'), ['update', 'id' => $model->id], [
                    'class' => 'btn btn-default delete-item',
                ]);
                echo "</div>";
            }
            ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="row">
        <div class="col-sm-12">
            <?php             
                $dataProvider = new \yii\data\ActiveDataProvider([
                    'query' => OutgoingItem::find()->joinWith(['item'])->where(['outgoing_id' => $model->id]),
                    'pagination' => false,
                    'sort' => [
                        'defaultOrder' => [
                            'id' => SORT_DESC,
                        ]
                    ],
                ]);
                $dataProvider->sort->attributes['item_name'] = [
                    'asc' => ['item.name' => SORT_ASC],
                    'desc' => ['item.name' => SORT_DESC],
                ];

                echo \kartik\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    // 'pjax' => true,
                    'hover' => true,
                    'striped' => true,
                    'bordered' => false,
                    'summary' => false,
                    'toolbar'=> [
                        Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']),
                        Html::a('<i class="fa fa-repeat"></i> ' . Yii::t('app', 'Reload'), ['index'], ['data-pjax'=>0, 'class'=>'btn btn-default']),
                        '{toggleData}',
                        // $exportMenu,
                    ],
                    'panel' => false,
                    'pjaxSettings' => ['options' => ['id' => 'grid']],
                    // 'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-condensed'],
                    'rowOptions' => function($model) use ($modelItem) {
                        return $model->id == $modelItem->id ? ['style' => 'background:#ffff77'] : ['style' => ''];
                    },
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                        [
                            'header' => 'Kode',
                            'value' => 'item.shortcode',
                        ],
                        [
                            'attribute' => 'item_name',
                            'value' => 'item.name',
                            'label' => 'Nama Barang',
                        ],
                        [
                            'header' => 'Merk',
                            'value' => 'item.brand',
                        ],
                        [
                            'header' => 'Type',
                            'value' => 'item.type',
                        ],
                        [
                            'header' => 'Lokasi',
                            'value' => 'item.location',
                        ],
                        [
                            'header' => 'Satuan',
                            'value' => 'item.unit_of_measurement',
                        ],
                        [
                            'attribute' => 'quantity',
                            'format' => ['decimal', 0],
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                        [
                            'attribute' => 'discount',
                            'label' => 'Disc (%)',
                            'format' => ['decimal', 2],
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                        [
                            'attribute' => 'price',
                            'format' => ['decimal', 0],
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                        [
                            'attribute' => 'subtotal',
                            'format' => ['decimal', 0],
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                        [
                            'attribute' => 'box_number',
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                        [
                            'contentOptions' => ['class' => 'action-column nowrap text-right'],
                            'attribute' => '',
                            'format' => 'raw',
                            'value' => function ($data) {
                                return 
                                Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['update', 'id' => $data->outgoing_id, 'outgoing_item_id' => $data->id], [
                                    'class' => 'btn btn-xs btn-default btn-text-warning',
                                ])
                                .' '.
                                Html::a('<i class="glyphicon glyphicon-trash"></i>', ['delete-item', 'id' => $data->id], [
                                    'class' => 'btn btn-xs btn-default btn-text-danger',
                                    'data-confirm' => 'Are you sure you want to delete this item?',
                                    'data-method' => 'post',
                                ]);
                            }
                        ], 
                    ],
                ]);
            ?>
        </div>
    </div>

    <div class="form-panel">
        <div class="row">
            <div class="col-sm-12">
                <?= '<span class="btn btn-xs"><big><big>TOTAL: <b>Rp ' . Yii::$app->formatter->asDecimal($model->total, 0) . '</b></big></big></span>' ?>
                <?= Html::a('<i class="glyphicon glyphicon-stop"></i> '. Yii::t('app', 'Selesai'), ['view', 'id' => $model->id], [
                    'class' => 'btn btn-default btn-text-danger pull-right',
                ]) ?>
            </div>
        </div>
    </div>

</div>


<div class="modal fade" id="myModal" tabindex="-1">
    <div class="modal-dialog" role="document" style="width:auto">
        <div class="modal-content" style="margin-left:auto; margin-right:auto; width:90%">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select Item</h4>
            </div>
            <div class="modal-body">

                <?php 
                $gridColumns = [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['class' => 'text-right serial-column'],
                        'contentOptions' => ['class' => 'text-right serial-column'],
                    ],
                    [
                        'contentOptions' => ['class' => 'action-column nowrap', 'style' => 'padding:0;vertical-align: middle;text-align: center;'],
                        'attribute' => '',
                        'format' => 'raw',
                        'value' => function ($data) use ($model) {
                            return '&nbsp;&nbsp;<a href="#" class="btn btn-xs btn-default btn-text-success" id="item-selector" onclick="retrieveItem(\'' . $data->shortcode . '\', \'' . $model->customer_id . '\', \'' . ($model->customer->priceGroup ? $model->customer->priceGroup->name : '') . '\')"><i class="fa fa-check"></i></a>&nbsp;&nbsp;';
                        }
                    ],
                    'shortcode:text:Kode',
                    'name:text:Nama Barang',
                    'brand:text:Merk',
                    'type',
                    'unit_of_measurement:text:Satuan',
                    [
                        'attribute' => 'current_quantity',
                        'label' => 'Qty',
                        'format' => ['decimal', 0],
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'location',
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'purchase_net_price',
                        'label' => 'Harga Net',
                        'format' => ['decimal', 0],
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'purchase_gross_price',
                        'label' => 'Harga List',
                        'format' => ['decimal', 0],
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'purchase_discount',
                        'label' => 'Disc',
                        'format' => ['decimal', 2],
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                ];

                foreach ($priceGroups = PriceGroup::find()->all() as $priceGroup) {
                    $gridColumns = array_merge($gridColumns, [
                        [
                            'header' => $priceGroup->name . '-dsc ',
                            'contentOptions' => ['class' => 'text-right'],
                            'value' => function ($model) use ($priceGroup) {
                                $prices = ItemPrice::findOne(['item_id' => $model->id, 'price_group_id' => $priceGroup->id]);
                                $priceText = $prices ? Yii::$app->formatter->asDecimal($prices->discount, 0) : null;
                                return $priceText;
                            }
                        ],
                        [
                            'header' => $priceGroup->name . '-price ',
                            'contentOptions' => ['class' => 'text-right'],
                            'value' => function ($model) use ($priceGroup) {
                                $prices = ItemPrice::findOne(['item_id' => $model->id, 'price_group_id' => $priceGroup->id]);
                                $priceText = $prices ? Yii::$app->formatter->asDecimal($prices->price, 0) : null;
                                return $priceText;
                            }
                        ],
                    ]);
                }
                ?>

                <?= GridView::widget([
                    'id' => 'grid-id',
                    'dataProvider' => $dataProviderItemMaster,
                    'pjax' => true,
                    'hover' => true,
                    'striped' => false,
                    'bordered' => false,
                    'toolbar' => [
                        '{toggleData}',
                        // $exportMenu,
                    ],
                    'panel' => [
                        'type' => 'no-border transparent',
                        'heading' => false,
                        'before' => '{summary}',
                        'after' => false,
                    ],
                    'panelBeforeTemplate' => '
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="btn-toolbar kv-grid-toolbar" role="toolbar">
                                    {toolbar}
                                </div> 
                            </div>
                            <div class="col-sm-4">
                                <div class="pull-right">
                                    {before}
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    ',
                    'pjaxSettings' => ['options' => ['id' => 'grid-pjax']],
                    'filterModel' => $searchModelItemMaster,
                    'columns' => $gridColumns,
                    'tableOptions' => ['class' => 'table table-condensed small']
                ]); ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




<!-- ready stock -->
<div class="modal fade" id="myModal2" tabindex="-1">
    <div class="modal-dialog" role="document" style="width:auto">
        <div class="modal-content" style="margin-left:auto; margin-right:auto; width:90%">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select Item</h4>
            </div>
            <div class="modal-body">

                <?php 
                $gridColumns = [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['class' => 'text-right serial-column'],
                        'contentOptions' => ['class' => 'text-right serial-column'],
                    ],
                    [
                        'contentOptions' => ['class' => 'action-column nowrap', 'style' => 'padding:0;vertical-align: middle;text-align: center;'],
                        'attribute' => '',
                        'format' => 'raw',
                        'value' => function ($data) use ($model) {
                            return '&nbsp;&nbsp;<a href="#" class="btn btn-xs btn-default btn-text-success" id="item-selector2" onclick="retrieveItem(\'' . $data->shortcode . '\', \'' . $model->customer_id . '\', \'' . ($model->customer->priceGroup ? $model->customer->priceGroup->name : '') . '\')"><i class="fa fa-check"></i></a>&nbsp;&nbsp;';
                        }
                    ],
                    'shortcode:text:Kode',
                    'name:text:Nama Barang',
                    'brand:text:Merk',
                    'type',
                    'unit_of_measurement:text:Satuan',
                    [
                        'attribute' => 'current_quantity',
                        'label' => 'Qty',
                        'format' => ['decimal', 0],
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'location',
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'purchase_net_price',
                        'label' => 'Harga Net',
                        'format' => ['decimal', 0],
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'purchase_gross_price',
                        'label' => 'Harga List',
                        'format' => ['decimal', 0],
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'purchase_discount',
                        'label' => 'Disc',
                        'format' => ['decimal', 2],
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                ];

                foreach ($priceGroups = PriceGroup::find()->all() as $priceGroup) {
                    $gridColumns = array_merge($gridColumns, [
                        [
                            'header' => $priceGroup->name . '-dsc ',
                            'contentOptions' => ['class' => 'text-right'],
                            'value' => function ($model) use ($priceGroup) {
                                $prices = ItemPrice::findOne(['item_id' => $model->id, 'price_group_id' => $priceGroup->id]);
                                $priceText = $prices ? Yii::$app->formatter->asDecimal($prices->discount, 0) : null;
                                return $priceText;
                            }
                        ],
                        [
                            'header' => $priceGroup->name . '-price ',
                            'contentOptions' => ['class' => 'text-right'],
                            'value' => function ($model) use ($priceGroup) {
                                $prices = ItemPrice::findOne(['item_id' => $model->id, 'price_group_id' => $priceGroup->id]);
                                $priceText = $prices ? Yii::$app->formatter->asDecimal($prices->price, 0) : null;
                                return $priceText;
                            }
                        ],
                    ]);
                }
                ?>

                <?= GridView::widget([
                    'id' => 'grid2-id',
                    'dataProvider' => $dataProviderItemMasterReadyStock,
                    'pjax' => true,
                    'hover' => true,
                    'striped' => false,
                    'bordered' => false,
                    'toolbar' => [
                        '{toggleData}',
                        // $exportMenu,
                    ],
                    'panel' => [
                        'type' => 'no-border transparent',
                        'heading' => false,
                        'before' => '{summary}',
                        'after' => false,
                    ],
                    'panelBeforeTemplate' => '
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="btn-toolbar kv-grid-toolbar" role="toolbar">
                                    {toolbar}
                                </div> 
                            </div>
                            <div class="col-sm-4">
                                <div class="pull-right">
                                    {before}
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    ',
                    'pjaxSettings' => ['options' => ['id' => 'grid2-pjax']],
                    'filterModel' => $searchModelItemMasterReadyStock,
                    'columns' => $gridColumns,
                    'tableOptions' => ['class' => 'table table-condensed small']
                ]); ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<?php 
    $this->registerJsFile(
        '@web/js/outgoing-sale.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>