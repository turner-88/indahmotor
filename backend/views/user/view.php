<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view">

    <p>
    <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> '. Yii::t('app', 'Update'), ['update', 'id' => $model->id], [
        'class' => 'btn btn-warning',
    ]) ?>
    <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
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
                'username',
                // 'auth_key',
                // 'password_hash',
                // 'password_reset_token',
                'email:email',
                [
                    'label' => 'Status',
                    'value' => function($model) {
                        return $model->status ? 'Active' : 'Inactive';
                    }
                ],
                [
                    'label' => 'Role',
                    'value' => function($model) {
                        $rtr = [];
                        foreach ($model->roles as $role) {
                            $rtr[] = $role->item_name;
                        }
                        return implode(', ', $rtr);
                    }
                ],
                'created_at:datetime',
                'updated_at:datetime',
            ],
        ]) ?>
    </div>
</div>
