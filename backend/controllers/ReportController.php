<?php

namespace backend\controllers;

use Datetime;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use kartik\mpdf\Pdf;
use backend\models\Incoming;
use backend\models\IncomingItem;
use backend\models\Outgoing;
use backend\models\OutgoingItem;
use backend\models\Payment;
use backend\models\Customer;
use backend\models\DebtHistory;
use backend\models\StockHistory;
use backend\models\Expense;
use backend\models\BalanceHistory;
use backend\models\Item;
use backend\models\StockHistoryDaily;
use yii\web\ForbiddenHttpException;

/**
 * LogController implements the CRUD actions for Log model.
 */
class ReportController extends Controller
{
    public function generatePdf($title, $view, $params = [], $landscape = false) {
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => 'A4',
            'orientation' => $landscape ? 'L' : 'P',
            'marginTop' => '22',
            'marginBottom' => '10',
            'marginLeft' => '5',
            'marginRight' => '5',
            'filename' => $title,
            'options' => ['title' => $title],
            'content' => $this->renderPartial($view, $params),
            'methods' => [
                'SetHeader' => \backend\helpers\ReportHelper::header($params),
                'SetFooter' => ['Print date: ' . date('d/m/Y') . '||Page {PAGENO} of {nbpg}'],
            ],
            'cssInline' => '
                body, .printable, .table-report { font-size: 10pt }
                .table-report { margin-bottom:10px }
                .table-report td { border-bottom:1px solid #ccc; vertical-align:top; padding:0px 10px }
                .table-report tr.thead td { vertical-align:bottom; padding:2px 5px }
                .table-report tr.thead td { font-weight: bold; text-transform: uppercase; border-bottom:2px solid #ccc; border-top:none }
                thead { display: table-header-group }
                .table-report-footer td { border:none; padding:0px 5px }
            ',
        ]);
        return $pdf->render();
    }

    public function actionIncoming($date_start = '', $date_end = '', $supplier_id = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = Incoming::find();
        $query->where(['between', 'date', $date_start, $date_end]);
        if ($supplier_id) $query->andWhere(['supplier_id' => $supplier_id]);
        $query->orderBy(['id' => SORT_DESC]);
        $models = $query->all();

        $title  = 'LAPORAN PEMBELIAN';
        $view   = 'incoming';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'supplier_id'   => $supplier_id,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }



    public function actionIncomingItem($date_start = '', $date_end = '', $supplier_id = '', $item_id = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = IncomingItem::find();
        $query->joinWith(['incoming']);
        $query->where(['between', 'date', $date_start, $date_end]);
        if ($supplier_id) $query->andWhere(['supplier_id' => $supplier_id]);
        if ($item_id) $query->andWhere(['item_id' => $item_id]);
        $query->orderBy([
            'incoming_id' => SORT_DESC,
            'id' => SORT_DESC,
        ]);
        $models = $query->all();

        $title  = 'LAPORAN PEMBELIAN ITEM';
        $view   = 'incoming-item';
        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'supplier_id'   => $supplier_id,
            'item_id'       => $item_id,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 1);

        return $this->render($view, $params);
    }

    public function actionOutgoing($date_start = '', $date_end = '', $salesman_id = '', $customer_id = '', $with_profit = 0, $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = Outgoing::find();
        $query->where(['between', 'date', $date_start, $date_end]);
        if ($salesman_id) $query->andWhere(['salesman_id' => $salesman_id]);
        if ($customer_id) $query->andWhere(['customer_id' => $customer_id]);
        $query->orderBy(['id' => SORT_DESC]);
        $models = $query->all();

        $title  = 'LAPORAN PENJUALAN';
        $view   = 'outgoing';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'salesman_id'   => $salesman_id,
            'customer_id'   => $customer_id,
            'with_profit'   => $with_profit,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }



    public function actionOutgoingItem($date_start = '', $date_end = '', $salesman_id = '', $customer_id = '', $item_id = '', $brand = '', $with_profit = 0, $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = OutgoingItem::find();
        $query->joinWith(['outgoing', 'item']);
        $query->where(['between', 'date', $date_start, $date_end]);
        if ($salesman_id) $query->andWhere(['salesman_id' => $salesman_id]);
        if ($customer_id) $query->andWhere(['customer_id' => $customer_id]);
        if ($item_id) $query->andWhere(['item_id' => $item_id]);
        if ($brand) $query->andWhere(['brand' => $brand]);
        $query->orderBy([
            'serial' => SORT_DESC,
            'outgoing_id' => SORT_DESC,
            'id' => SORT_DESC,
        ]);
        $models = $query->all();

        $title  = 'LAPORAN PENJUALAN ITEM';
        $view   = 'outgoing-item';
        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'salesman_id'   => $salesman_id,
            'customer_id'   => $customer_id,
            'item_id'       => $item_id,
            'brand'         => $brand,
            'with_profit'   => $with_profit,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 1);

        return $this->render($view, $params);
    }

    public function actionPayment($date_start = '', $date_end = '', $customer_id = '', $salesman_id = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = Payment::find();
        $query->where(['between', 'date', $date_start, $date_end]);
        if ($customer_id) $query->andWhere(['customer_id' => $customer_id]);
        if ($salesman_id) $query->joinWith(['customer.salesman'])->andWhere(['salesman_id' => $salesman_id]);
        $query->orderBy(['id' => SORT_DESC]);
        $models = $query->all();

        $title  = 'LAPORAN PEMBAYARAN';
        $view   = 'payment';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'customer_id'   => $customer_id,
            'salesman_id'   => $salesman_id,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    public function actionPaymentIn($date_start = '', $date_end = '', $customer_id = '', $salesman_id = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = Payment::find();
        $query->where(['between', 'date', $date_start, $date_end]);
        $query->andWhere(['is not', 'customer_id', null]);
        if ($customer_id) $query->andWhere(['customer_id' => $customer_id]);
        if ($salesman_id) $query->joinWith(['customer.salesman'])->andWhere(['salesman_id' => $salesman_id]);
        $query->orderBy(['id' => SORT_DESC]);
        $models = $query->all();

        $title  = 'LAPORAN PEMBAYARAN DARI PELANGGAN';
        $view   = 'payment-in';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'customer_id'   => $customer_id,
            'salesman_id'   => $salesman_id,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    public function actionPaymentOut($date_start = '', $date_end = '', $supplier_id = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = Payment::find();
        $query->where(['between', 'date', $date_start, $date_end]);
        $query->andWhere(['is not', 'supplier_id', null]);
        if ($supplier_id) $query->andWhere(['supplier_id' => $supplier_id]);
        $query->orderBy(['id' => SORT_DESC]);
        $models = $query->all();

        $title  = 'LAPORAN PEMBAYARAN KE DISTRIBUTOR';
        $view   = 'payment-out';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'supplier_id'   => $supplier_id,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    public function actionExpense($date_start = '', $date_end = '', $salesman_id = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = Expense::find();
        $query->where(['between', 'date', $date_start, $date_end]);
        if ($salesman_id) $query->andWhere(['salesman_id' => $salesman_id]);
        $query->orderBy(['id' => SORT_DESC]);
        $models = $query->all();

        $title  = 'LAPORAN PENGELUARAN';
        $view   = 'expense';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'salesman_id'   => $salesman_id,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    public function actionDebt($salesman_id = '', $hide_zero = 0, $to_pdf = 0)
    {
        $query = Customer::find();
        if ($salesman_id) $query->andWhere(['salesman_id' => $salesman_id]);
        $query->orderBy(['name' => SORT_ASC]);
        $models = $query->all();

        $title  = 'LAPORAN PIUTANG';
        $view   = 'debt';

        $pre_params = [
            'models'        => $models,
            'salesman_id'   => $salesman_id,
            'hide_zero'     => $hide_zero,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    public function actionDebtHistory($date_start = '', $date_end = '', $customer_id = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = DebtHistory::find();
        $query->where(['between', 'date', $date_start, $date_end]);
        $query->andWhere(['customer_id' => $customer_id]);
        $query->orderBy(['date' => SORT_ASC]);
        $models = $query->all();

        $title  = 'LAPORAN HISTORY PIUTANG PER PELANGGAN';
        $view   = 'debt-history';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'customer_id'   => $customer_id,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    public function actionStockHistory($date_start = '', $date_end = '', $item_id = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = StockHistory::find();
        $query->where(['between', 'date', $date_start, $date_end]);
        $query->andWhere(['item_id' => $item_id]);
        $query->orderBy(['date' => SORT_ASC]);
        $models = $query->all();

        $title  = 'LAPORAN HISTORY STOK PER BARANG';
        $view   = 'stock-history';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'item_id'       => $item_id,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    public function actionBalance($date_start = '', $date_end = '', $salesman_id = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $sum_expense            = Expense::find()->where(['between', 'date', $date_start, $date_end])->sum('amount');
        $sum_incoming           = Incoming::find()->where(['between', 'date', $date_start, $date_end])->sum('total');
        $sum_outgoing           = Outgoing::find()->where(['between', 'date', $date_start, $date_end])->sum('total');
        $sum_payment_amount     = Payment::find()->where(['between', 'date', $date_start, $date_end])->sum('amount');
        $sum_payment_return     = Payment::find()->where(['between', 'date', $date_start, $date_end])->sum('`return`');
        $sum_payment_adjustment = Payment::find()->where(['between', 'date', $date_start, $date_end])->sum('adjustment');

        $total_amount       = DebtHistory::find()->sum('credit');
        $total_adjustment   = DebtHistory::find()->sum('adjustment');
        $total_return       = DebtHistory::find()->sum('`return`');
        $total_outgoing     = DebtHistory::find()->sum('debt');
        $total_initial_debt = Customer::find()->sum('initial_debt');

        if ($salesman_id) {
            $sum_outgoing             = Outgoing::find()->where(['between', 'date', $date_start, $date_end])->andWhere(['salesman_id' => $salesman_id])->sum('total');
            $sum_payment_amount       = Payment::find()->joinWith(['customer'])->where(['between', 'date', $date_start, $date_end])->andWhere(['salesman_id' => $salesman_id])->sum('amount');
            $sum_payment_return       = Payment::find()->joinWith(['customer'])->where(['between', 'date', $date_start, $date_end])->andWhere(['salesman_id' => $salesman_id])->sum('`return`');
            $sum_payment_adjustment   = Payment::find()->joinWith(['customer'])->where(['between', 'date', $date_start, $date_end])->andWhere(['salesman_id' => $salesman_id])->sum('adjustment');

            $total_amount       = DebtHistory::find()->joinWith(['customer'])->where(['salesman_id' => $salesman_id])->sum('credit');
            $total_adjustment   = DebtHistory::find()->joinWith(['customer'])->where(['salesman_id' => $salesman_id])->sum('adjustment');
            $total_return       = DebtHistory::find()->joinWith(['customer'])->where(['salesman_id' => $salesman_id])->sum('`return`');
            $total_outgoing     = DebtHistory::find()->joinWith(['customer'])->where(['salesman_id' => $salesman_id])->sum('debt');
            $total_initial_debt = Customer::find()->where(['salesman_id' => $salesman_id])->sum('initial_debt');
        }

        $total_debt = $total_outgoing - $total_amount - $total_adjustment - $total_return + $total_initial_debt;

        $title  = 'RESUME';
        $view   = 'balance';

        $model = [
            'total_amount'      => $sum_payment_amount,
            'total_adjustment'  => $sum_payment_adjustment,
            'total_return'      => $sum_payment_return,
            'total_incoming'    => $sum_incoming,
            'total_outgoing'    => $sum_outgoing,
            'total_debt'        => $total_debt,
            'total_expense'     => $sum_expense,
        ];

        $pre_params = [
            'model'        => $model, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'salesman_id'   => $salesman_id,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    public function actionBalanceHistory($date_start = '', $date_end = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = BalanceHistory::find();
        $query->where(['between', 'date', $date_start, $date_end]);
        // $query->orderBy(['date' => SORT_ASC]);
        $models = $query->all();

        $title  = 'LAPORAN HISTORY SALDO';
        $view   = 'balance-history';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }


    public function actionBalanceHistoryReverted($date_start = '', $date_end = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = BalanceHistory::find();
        $query->where(['between', 'date', $date_start, $date_end]);
        $query->orderBy('date DESC, transaction_type DESC');
        $models = $query->all();

        $title  = 'LAPORAN HISTORY SALDO';
        $view   = 'balance-history-reverted';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    public function actionTransactionHistory($date_start = '', $date_end = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = StockHistory::find();
        $query->where(['between', 'date', $date_start, $date_end]);
        $query->orderBy(['date' => SORT_ASC, 'item_id' => SORT_ASC]);
        $query->groupBy('item_id, date');
        $models = $query->all();

        $title  = 'LAPORAN HISTORY STOCK';
        $view   = 'transaction-history';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    public function actionStockHistoryDaily($date_start = '', $date_end = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $query = Item::find();
        $query->joinWith(['stockHistoryDailies']);
        $query->select([
            'item.id as id',
            'item.shortcode as shortcode',
            'item.name as name',
            'item.brand as brand',
            'item.type as type',
            'item.current_quantity as current_quantity',
            'sum(quantity_in) as sum_quantity_in',
            'sum(quantity_out) as sum_quantity_out',
        ]);
        $query->where(['between', 'date', $date_start, $date_end]);
        $query->orderBy(['item.shortcode' => SORT_ASC]);
        $query->groupBy('item_id');
        $models = $query->asArray()->all();

        $title  = 'LAPORAN HISTORY STOCK';
        $view   = 'stock-history-daily';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

}
