<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use kartik\grid\GridView;
use kartik\datecontrol\DateControl;
use backend\models\IncomingType;
use backend\models\Supplier;
use backend\models\Storage;
use backend\models\Customer;
use backend\models\ReturnPlan;
use backend\models\OutgoingItem;
use backend\models\Salesman;
use backend\models\IncomingItem;
use backend\models\IncomingItemPrice;
use backend\models\PriceGroup;
use backend\models\Item;
use backend\models\ItemPrice;

/* @var $this yii\web\View */
/* @var $model backend\models\Incoming */
/* @var $form yii\widgets\ActiveForm */

$shortcode = is_object($modelItem->item) ? $modelItem->item->shortcode : null;
?>

<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="headingOne" style="border-bottom: none">
        <h4 class="panel-title">
            <?= 'Rp <span class="text-total">' . Yii::$app->formatter->asDecimal($model->total, 0) . '</span> : <b>' . $model->supplier->shortText .'</b>, <small>'. Yii::$app->formatter->asDate($model->date) .' &raquo; '. Yii::$app->formatter->asDate($model->due_date).'</small>' ?>
            <a class="pull-right btn btn-default btn-text-warning btn-xs" style="margin-top:-2px" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                <small><i class="glyphicon glyphicon-pencil text-warning"></i></small>
            </a>
        </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body" style="background: #fdfdfd">
            
            <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

            <?php 
                if (\backend\models\Config::findOne(['key' => 'AutomaticPurchaseSerial'])->value == '0') {
                    echo $form->field($model, 'serial')->textInput(['maxlength' => true]);
                }
            ?>

            <?= $form->field($model, 'supplier_id')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Supplier::find()->all(), 'id', 'shortText'),
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
    'id' => 'form-update-ajax',
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


<?= Html::activeHiddenInput($modelItem, 'incoming_id') ?>
<?= Html::activeHiddenInput($modelItem, 'id') ?>
<?= Html::activeHiddenInput($modelItem, 'isNewRecord') ?>

<div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            <label class="control-label col-sm-3">Code</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <?= Html::textInput('item_shortcode', $shortcode, ['class' => 'form-control', 'id' => 'item_shortcode']) ?>
                    <span class="input-group-btn" style="">
                        <a id="btn-grid" href="#" class="btn btn-default form-control" data-toggle="modal" data-target="#myModal"><i class="fa fa-th-list"></i></a>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Nama Brg</label>
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
            <label class="control-label col-sm-4">Qty</label>
            <div class="col-sm-8">
                <?= Html::activeTextInput($modelItem, 'quantity', ['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Satuan</label>
            <div class="col-sm-8">
                <?= Html::textInput('item_unit_of_measurement', null, ['class' => 'form-control', 'id' => 'item_unit_of_measurement']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Type</label>
            <div class="col-sm-8">
                <?= Html::textInput('item_type', null, ['class' => 'form-control', 'id' => 'item_type']) ?>
            </div>
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group">
            <label class="control-label col-sm-4">Hrg Net</label>
            <div class="col-sm-8">
                <?= Html::activeTextInput($modelItem, 'price', ['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Disc</label>
            <div class="col-sm-8">
                <?= Html::activeTextInput($modelItem, 'discount', ['class' => 'form-control', 'onkeyup' => 'calculateGrossPrice()']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Hrg List</label>
            <div class="col-sm-8">
                <?= Html::activeTextInput($modelItem, 'gross_price', ['class' => 'form-control']) ?>
            </div>
        </div>
    </div>

    <div class="col-sm-2">

        <!-- <label>PRICE GROUP</label> -->
        <div class="detail-view-container" style="background:#f4f4f4">
            <table class="table table-condensed" style="margin:3px 0 4px 0;">
                <!-- <tr>
                    <td></td>
                    <td>Discount</td>
                    <td>Price</td>
                </tr> -->
            <?php 
            foreach (($priceGroups = PriceGroup::find()->all()) as $priceGroup) {
                $value_discount = (($incomingItemPrice = IncomingItemPrice::findOne(['incoming_item_id' => $model->id, 'price_group_id' => $priceGroup->id])) !== null) ? $incomingItemPrice->discount : null;
                $value_price = (($incomingItemPrice = IncomingItemPrice::findOne(['incoming_item_id' => $model->id, 'price_group_id' => $priceGroup->id])) !== null) ? $incomingItemPrice->price : null;
                ?>
                <tr>
                    <td class="text-right action-column" style="vertical-align:middle">&nbsp;&nbsp;<b><?= $priceGroup->name ?></b></td>
                    <td>
                        <?= Html::textInput($priceGroup->name . '-discount', $value_discount, ['id' => $priceGroup->name . '-discount', 'class' => 'input-sm form-control price-groups', 'placeholder' => 'disc']) ?>
                    </td>
                    <td width="55%">
                        <?= Html::textInput($priceGroup->name . '-price', $value_price, ['id' => $priceGroup->name . '-price', 'class' => 'input-sm form-control price-groups', 'placeholder' => 'price']) ?>
                    </td>
                </tr>
            <?php 
        } ?>
            </table>
        </div>
    </div>

    <div class="col-sm-2">
        <div>
            Dus
            <?= Html::textInput('item_location', null, ['class' => 'form-control', 'id' => 'item_location']) ?>
        </div>
        <div class="" style="width:100%">
            <b>Subtotal</b>
            <span id="info_new_item" class="text-danger pull-right small" style="display:none;"><b>** NEW ITEM **</b></span>
        </div>
        <div style="margin-bottom:10px">
            <?= Html::activeTextInput($modelItem, 'subtotal', ['class' => 'form-control']) ?>
        </div>
        <div class="form-panel">
        <?php 
        if ($modelItem->isNewRecord) {
            echo Html::button('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Add'), ['class' => 'btn btn-success', 'id' => 'btn-add']);
        } else {
            echo '<div class="input-group">';
            echo Html::a('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Cancel'), ['update-ajax', 'id' => $model->id], [
                'class' => 'btn btn-default delete-item',
                'id' => 'cancel',
            ])
            .' '.Html::button('<i class="glyphicon glyphicon-ok"></i> ' . Yii::t('app', 'Edit'), ['class' => 'btn btn-primary', 'id' => 'btn-edit']);
            echo "</div>";
        }
        ?>
        </div>
    </div>
</div>

<div id="errorbox" class="form-panel small text-danger" style="display:none; margin-top:10px; background:#f0e0ea"></div>
<p></p>

<?php ActiveForm::end(); ?>

<div class="row">
    <div class="col-sm-12">
        <?php             
            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => IncomingItem::find()->joinWith(['item'])->where(['incoming_id' => $model->id]),
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
                'pjax' => true,
                'hover' => true,
                'striped' => false,
                'bordered' => false,
                'summary' => false,
                'toolbar'=> [
                    // Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Create'), ['create'], ['class' => 'btn btn-success']),
                    Html::a('<i class="fa fa-repeat"></i> ' . Yii::t('app', 'Reload'), ['index'], ['data-pjax'=>0, 'class'=>'btn btn-default']),
                    '{toggleData}',
                    // $exportMenu,
                ],
                'panel' => false,
                'pjaxSettings' => [
                    'options' => ['id' => 'grid'],
                    'neverTimeout' => true,
                ],
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
                        'contentOptions' => ['class' => 'action-column nowrap text-right', 'style' => 'padding:3px 5px 0 5px'],
                        'attribute' => '',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return 
                            Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['update-ajax', 'id' => $data->incoming_id, 'incoming_item_id' => $data->id], [
                                'class' => 'btn btn-xs btn-default btn-text-warning',
                                'style' => 'padding:0px 5px; margin:0px',
                                'data-pjax' => 0, 
                            ])
                            .' '.
                            Html::a('<i class="glyphicon glyphicon-trash"></i>', ['delete-item-ajax', 'id' => $data->id], [
                                'class' => 'btn btn-xs btn-default btn-text-danger',
                                'data-confirm' => 'Are you sure you want to delete this item?',
                                'data-method' => 'post',
                                'data-pjax' => 0, 
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
            <?= '<span class="btn btn-xs"><big><big>TOTAL: <b>Rp <span class="text-total">' . Yii::$app->formatter->asDecimal($model->total, 0) . '</span></b></big></big></span>' ?>
            <?= Html::a('<i class="glyphicon glyphicon-stop"></i> '. Yii::t('app', 'Selesai'), ['view', 'id' => $model->id], [
                'class' => 'btn btn-default btn-text-danger pull-right',
            ]) ?>
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
                        'value' => function ($data) {
                            return '&nbsp;&nbsp;<a href="#" class="btn btn-xs btn-default btn-text-success" id="item-selector" onclick="retrieveItem(\''.$data->shortcode. '\')"><i class="fa fa-check"></i></a>&nbsp;&nbsp;';
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
                    'location',
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
                    'pjaxSettings' => [
                        'options' => ['id' => 'grid-item-search'],
                        'neverTimeout' => true,
                    ],
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


<?php 
    $this->registerJsFile(
        '@web/js/incoming-purchase-ajax.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>