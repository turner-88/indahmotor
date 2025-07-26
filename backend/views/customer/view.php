<?php

use backend\models\Outgoing;
use backend\models\Payment;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Customer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Pelanggan', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="customer-view box-- box-info--">

    <div class="box-body--">
        <p>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> '. 'Update', ['update', 'id' => $model->id], [
            'class' => 'btn btn-warning',
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . 'Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        </p>

        <div class="detail-view-container">
        <?= DetailView::widget([
            'options' => ['class' => 'table detail-view'],
            'model' => $model,
            'attributes' => [
                // 'id',
                'name',
                'address:ntext',
                'phone',
                'person_in_charge',
                'priceGroup.name:text:Kelompok Harga',
                'salesman.name:text:Salesman',
                'payment_limit_duration:integer',
                'initial_debt:integer',
                'debt:integer',
                // 'created_at:datetime',
                // 'updated_at:datetime',
                // 'createdBy.username:text:Created By',
                // 'updatedBy.username:text:Updated By',
            ],
        ]) ?>
        </div>
    </div>
</div>

<?php
    $outgoing_total = Outgoing::find()->where(['customer_id' => $model->id])->sum('total');
    $payment = 0;
    $payment+= Payment::find()->where(['customer_id' => $model->id])->sum('amount');
    $payment+= Payment::find()->where(['customer_id' => $model->id])->sum('adjustment');
    $payment+= Payment::find()->where(['customer_id' => $model->id])->sum('`return`');

    $credit = $outgoing_total - $payment + $model->initial_debt;

    $last_unpaid_id = null;
    $credit_remaining = $credit;
    $outgoings = Outgoing::find()->where(['customer_id' => $model->id])->orderBy('id DESC')->all();

    foreach ($outgoings as $outgoing) {
        if ($credit_remaining) {
            $outgoing->total_payment = ($credit_remaining >= $outgoing->total) ? 0 : ($outgoing->total - $credit_remaining);
            $outgoing->payment_status = $outgoing->total_payment ? 2 : 1;
            $outgoing->save();
            $last_unpaid_id = $outgoing->id;
        }
        $credit_remaining-= $outgoing->total;
        if ($credit_remaining <= 0) break;
    }
    if ($last_unpaid_id) Yii::$app->db->createCommand("update outgoing set total_payment = outgoing.total, payment_status = 3 where customer_id = '".$model->id."' and id < ".$last_unpaid_id)->execute();
    else Yii::$app->db->createCommand("update outgoing set total_payment = outgoing.total, payment_status = 3 where customer_id = '".$model->id."'")->execute();
    
?>

<table>
    <tr><td>Total Belanja</td>  <td>&nbsp;:&nbsp;</td> <td class="text-right"><b><?= Yii::$app->formatter->asInteger($outgoing_total) ?></b></td>                                     <td>&nbsp;-&nbsp;</td> <td><?= Outgoing::find()->where(['customer_id' => $model->id])->count() ?></td></tr>
    <tr><td>Total Bayar</td>    <td>&nbsp;:&nbsp;</td> <td class="text-right"><b><?= Yii::$app->formatter->asInteger($payment) ?></b></td>                                            <td>&nbsp;-&nbsp;</td> <td><?= Outgoing::find()->where(['customer_id' => $model->id, 'payment_status' => 3])->count() ?></td></tr>
    <tr><td>Piutang</td>        <td>&nbsp;:&nbsp;</td> <td class="text-right"><b><?= Yii::$app->formatter->asInteger($outgoing_total - $payment) ?></b></td>                          <td>&nbsp;-&nbsp;</td> <td></td></tr>
    <tr><td>Piutang Awal</td>   <td>&nbsp;:&nbsp;</td> <td class="text-right"><b><?= Yii::$app->formatter->asInteger($model->initial_debt) ?></b></td>                                <td>&nbsp;-&nbsp;</td> <td></td></tr>
    <tr><td>Total Piutang</td>  <td>&nbsp;:&nbsp;</td> <td class="text-right"><b><?= Yii::$app->formatter->asInteger($outgoing_total - $payment + $model->initial_debt) ?></b></td>   <td>&nbsp;-&nbsp;</td> <td><?= Outgoing::find()->where(['customer_id' => $model->id])->andWhere(['or', ['payment_status' => 1], ['payment_status' => 2]])->count() ?></td></tr>
</table>

