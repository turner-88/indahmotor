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
use backend\models\OrderItem;
use backend\models\Item;
use backend\models\ItemPrice;
use backend\models\PriceGroup;

/* @var $this yii\web\View */
/* @var $model backend\models\Incoming */
/* @var $form yii\widgets\ActiveForm */

$shortcode = is_object($modelItem->item) ? $modelItem->item->shortcode : null;

$total_order = OrderItem::find()->joinWith(['order', 'item'])->where(['order_id' => $model->id])->sum('quantity * purchase_net_price');
?>

<input type="hidden" id="is_new_record" value="<?= $modelItem->isNewRecord ?>">

<div class="incoming-form">

    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne" style="border-bottom: none">
            <h4 class="panel-title">
                <?= '<b>'.$model->customer_name .'</b>, '. Yii::$app->formatter->asDate($model->date) . ', Total : <span id="text-total">' . Yii::$app->formatter->asDecimal($total_order, 0) . '</span>' ?>
                <a class="pull-right btn btn-default btn-text-warning btn-xs" style="margin-top:-2px" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <small><i class="glyphicon glyphicon-pencil text-warning"></i></small>
                </a>
            </h4>
        </div>
        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body" style="background: #fdfdfd">
                
                <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

                <?= $form->field($model, 'customer_name')->textInput(['maxlength' => true]) ?>

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

    <?= Html::activeHiddenInput($modelItem, 'order_id') ?>
    <?= Html::activeHiddenInput($modelItem, 'id') ?>
    <?= Html::activeHiddenInput($modelItem, 'isNewRecord') ?>

    <div class="row">
        <div class="col-sm-4">
            <div class="info">&nbsp;</div>

            <div class="form-group">
                <label class="control-label col-sm-3">Kode</label>
                <div class="col-sm-9">
                    <div class="input-group">
                        <?= Html::activeTextInput($modelItem, 'item_shortcode', ['class' => 'form-control']) ?>
                        <span class="input-group-btn" style="">
                            <a href="#" class="btn btn-default form-control" data-toggle="modal" data-target="#myModal"><i class="fa fa-th-list"></i></a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Nama Barang</label>
                <div class="col-sm-9">
                    <?= Html::activeTextInput($modelItem, 'item_name', ['class' => 'form-control']) ?>
                </div>
            </div>        
            <div class="form-group">
                <label class="control-label col-sm-3">Satuan</label>
                <div class="col-sm-9">
                    <?= Html::activeTextInput($modelItem, 'unit_of_measurement', ['class' => 'form-control']) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="info text-right text-muted" id="subtotal" style="padding:0 15px; font-weight:bold">&nbsp;</div>
            <input type="hidden" id="net_price" value="0">

            <div class="form-group">
                <label class="control-label col-sm-4">Order Plgn <span id="unitofmeasurement-label"></span></label>
                <div class="col-sm-8">
                    <?= Html::activeTextInput($modelItem, 'quantity', ['class' => 'form-control']) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4">Order Dist <span id="unitofmeasurement-label"></span></label>
                <div class="col-sm-8">
                    <?= Html::activeTextInput($modelItem, 'to_be_ordered', ['class' => 'form-control']) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-4">Dist</label>
                <div class="col-sm-8">
                    <?= Html::activeDropDownList($modelItem, 'supplier_id', ArrayHelper::map(Supplier::find()->all(), 'id', 'name'), ['class' => 'form-control', 'prompt' => '']) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="info">&nbsp;</div>

            <div class="form-group">
                <label class="control-label col-sm-3">Type</label>
                <div class="col-sm-9">
                    <?= Html::activeTextInput($modelItem, 'type', ['class' => 'form-control']) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Merk Gdg</label>
                <div class="col-sm-9">
                    <?= Html::activeTextInput($modelItem, 'brand_storage', ['class' => 'form-control']) ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Merk Dist</label>
                <div class="col-sm-9">
                    <?= Html::activeTextInput($modelItem, 'brand_supplier', ['class' => 'form-control']) ?>
                </div>
            </div>
        </div>
        
        <div class="col-sm-2">
            <div class="info">&nbsp;</div>

            <div class="form-panel">
            <?php 
            if ($modelItem->isNewRecord) {
                // echo Html::submitButton('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Add'), ['class' => 'btn btn-success']);
                echo Html::button('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Add'), ['class' => 'btn btn-success', 'id' => 'btn-add']);
            } else {
                echo '<div class="input-group">';
                echo Html::a('<i class="glyphicon glyphicon-remove"></i> ' . Yii::t('app', 'Cancel'), ['update-ajax', 'id' => $model->id], [
                    'class' => 'btn btn-default delete-item',
                    'id' => 'cancel',
                ])
                . ' ' . Html::button('<i class="glyphicon glyphicon-ok"></i> ' . Yii::t('app', 'Edit'), ['class' => 'btn btn-primary', 'id' => 'btn-edit']);
                echo "</div>";
            }
            ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <div id="infobox" class="form-panel">
        <table width="100%">
            <tr>
                <td class="text-right" style="padding-right:10px">Stock</td>
                <td width="50%" id="current_quantity"></td>
            </tr>
            <tr>
                <td class="text-right" style="padding-right:10px">Total Qty Order Item</td>
                <td width="50%" id="total_order_quantity"></td>
            </tr>
            <tr>
                <td class="text-right" style="padding-right:10px">Total Qty Order Distributor</td>
                <td width="50%" id="total_to_be_ordered_quantity"></td>
            </tr>
            <tr>
                <td class="text-right" style="padding-right:10px">Total Hrg Order Distributor</td>
                <td width="50%" id="total_to_be_ordered_value"></td>
            </tr>
        </table>
    </div>

    <div id="errorbox" class="form-panel small text-danger" style="display:none; margin-top:10px; background:#f0e0ea"></div>
    <p></p>

    <div class="row">
        <div class="col-sm-12">
            <?php             
                $dataProvider = new \yii\data\ActiveDataProvider([
                    'query' => OrderItem::find()->where(['order_id' => $model->id]),
                    'pagination' => false,
                    'sort' => [
                        'defaultOrder' => [
                            'id' => SORT_DESC,
                        ]
                    ],
                ]);

                echo \kartik\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'pjax' => true,
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
                        'item_shortcode',
                        'item_name',
                        'unit_of_measurement',
                        [
                            'attribute' => 'quantity',
                            'format' => 'integer',
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                        [
                            'attribute' => 'to_be_ordered',
                            'format' => 'integer',
                            'headerOptions' => ['class' => 'text-right'],
                            'contentOptions' => ['class' => 'text-right'],
                        ],
                        [
                            'attribute' => 'supplier_id',
                            'value' => 'supplier.name',
                        ],
                        'type',
                        'brand_storage',
                        'brand_supplier',
                        [
                            'contentOptions' => ['class' => 'action-column nowrap text-right'],
                            'attribute' => '',
                            'format' => 'raw',
                            'value' => function ($data) {
                                return 
                                Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['update-ajax', 'id' => $data->order_id, 'order_item_id' => $data->id], [
                                    'class' => 'btn btn-xs btn-default btn-text-warning',
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
                <?= Html::a('<i class="glyphicon glyphicon-stop"></i> '. Yii::t('app', 'Done'), ['view', 'id' => $model->id], [
                    'class' => 'btn btn-default btn-text-danger',
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
                            return '&nbsp;&nbsp;<a href="#" class="btn btn-xs btn-default btn-text-success" id="item-selector" onclick="retrieveItem(\'' . $data->shortcode . '\')"><i class="fa fa-check"></i></a>&nbsp;&nbsp;';
                        }
                    ],
                    'shortcode',
                    'name',
                    'brand',
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
                        'label' => 'Net Price',
                        'format' => ['decimal', 0],
                        'headerOptions' => ['class' => 'text-right'],
                        'contentOptions' => ['class' => 'text-right'],
                    ],
                    [
                        'attribute' => 'purchase_gross_price',
                        'label' => 'Gross Price',
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
        '@web/js/order-ajax.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>