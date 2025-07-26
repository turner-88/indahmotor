<?php

namespace backend\controllers;

use Yii;
use Datetime;
use backend\models\Config;
use backend\models\Customer;
use backend\models\Outgoing;
use backend\models\OutgoingSearch;
use backend\models\OutgoingItem;
use backend\models\OutgoingItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\IntegrityException;
use kartik\mpdf\Pdf;
use backend\models\Item;
use backend\models\ItemSearch;
use backend\models\ItemSearchReadyStock;
use backend\models\ItemPrice;
use backend\models\PriceGroup;
use yii\helpers\Url;

/**
 * OutgoingSaleController implements the CRUD actions for Outgoing model.
 */
class OutgoingSaleController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-item' => ['POST'],
                    'delete-item-ajax' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Outgoing models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (($outgoings = Outgoing::findAll(['count_of_items' => null])) !== null) {
            foreach ($outgoings as $outgoing) {
                $outgoing->setTotal();
            }
        }
        $searchModel = new OutgoingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Outgoing model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->setTotal();

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Outgoing model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Outgoing();

        if (Outgoing::find()->count('id') == 0) {
            $model->id = isset(Yii::$app->params['firstInvoice']) ? Yii::$app->params['firstInvoice'] : 1;
        }

        $model->outgoing_type_id = 1;
        $model->date = date('Y-m-d');
        $model->total = 0;
        $model->serial = microtime(true) * 10000;
        $model->is_unlimited = 1;
        
        if (Config::findOne(['key' => 'GlobalPaymentLimit'])->value != '0') {
            $model->due_date = date('Y-m-d', strtotime($model->date . ' +' . Config::findOne(['key' => 'GlobalPaymentLimit'])->value . ' day'));
        }

        if ($model->load(Yii::$app->request->post())) {
            /* $customer = Customer::findOne($model->customer_id);
            if ($customer->payment_limit_duration) {
                $count_of_days_late = 0;
                $outgoing = Outgoing::find()->where(['customer_id' => $customer->id])->andWhere(['!=', 'payment_status', Outgoing::PAYMENT_ALL])->orderBy('due_date ASC')->one();
                if ($outgoing && $outgoing->due_date < date('Y-m-d')) {
                    $count_of_days_late = (new DateTime($outgoing->due_date))->diff(new DateTime(date('Y-m-d')))->days;
                }
                if ($count_of_days_late > 0) {
                    Yii::$app->session->setFlash(
                        'error', 
                        '<big>Gagal menyimpan data.</big> 
                        <br>Customer ini memiliki hutang yang telah lewat jatuh tempo selama <b>'.$count_of_days_late.'</b> hari pada faktur <b>'.$outgoing->id.'</b>
                        <br><br><a style="text-decoration:none" class="btn btn-default btn-text-danger" href="'.Url::to(['/outgoing-sale/view', 'id' => $outgoing->id]).'">Lihat Faktur</a>
                    ');
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            } */

            if (!$model->salesman_id) {
                $model->salesman_id = $model->customer->salesman_id;
            }
            if ($model->save()) {
                $post = Yii::$app->request->post();
                if (isset($post['to_update_ajax']) && $post['to_update_ajax']) return $this->redirect(['update-ajax', 'id' => $model->id]);
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($model->errors));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Outgoing model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($id);
        $modelItem = new OutgoingItem();
        $modelItem->outgoing_id = $id;

        if (($outgoing_item_id = Yii::$app->request->get('outgoing_item_id')) !== null) {
            $modelItem = OutgoingItem::findOne($outgoing_item_id);
            $modelItemOld = OutgoingItem::findOne($outgoing_item_id);
        }

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->save()) Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($model->errors));
            return $this->redirect(['update', 'id' => $model->id]);

        } elseif ($modelItem->load(Yii::$app->request->post())) {

            if (count($model->outgoingItems) >= 20 && $modelItem->isNewRecord && !$model->is_unlimited) {
                $modelOld = clone $model;
                $model                   = new Outgoing();
                $model->total            = 0;
                $model->outgoing_type_id = 1;
                $model->serial           = $modelOld->serial;
                $model->date             = $modelOld->date;
                $model->due_date         = $modelOld->due_date;
                $model->customer_id      = $modelOld->customer_id;
                $model->salesman_id      = $modelOld->salesman_id;
                $model->remark           = $modelOld->remark;
                if ($model->save()) {
                    $modelItem->outgoing_id = $model->id;
                } else {
                    Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($model->errors));
                }
            }

            if (($item = Item::findOne(['shortcode' => trim($post['item_shortcode'])])) !== null) {
                $modelItem->item_id = $item->id;
            
                $item->shortcode            = $post['item_shortcode'];
                $item->name                 = $post['item_name'];
                $item->brand                = $post['item_brand'];
                $item->type                 = $post['item_type'];
                if (!$item->save()) Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($item->errors));

                $flag_save = 0;
                if ($modelItem->isNewRecord) {
                    if ($item->current_quantity >= $modelItem->quantity) $flag_save = 1;
                } else {
                    if (($item->current_quantity + $modelItemOld->quantity) >= $modelItem->quantity) $flag_save = 1;
                }
                if ($flag_save) {
                    if ($modelItem->save()) {
                        return $this->redirect(['update', 'id' => $model->id]);
                    } else {
                        Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($modelItem->errors));
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Quantity tidak cukup: '.$item->current_quantity);
                }
            }
        }

        $searchModelItemMaster = new ItemSearch();
        $dataProviderItemMaster = $searchModelItemMaster->search(Yii::$app->request->queryParams);
        $dataProviderItemMaster->pagination->pageSize = 50;

        $searchModelItemMasterReadyStock = new ItemSearchReadyStock();
        $dataProviderItemMasterReadyStock = $searchModelItemMasterReadyStock->search(Yii::$app->request->queryParams);
        $dataProviderItemMasterReadyStock->pagination->pageSize = 50;

        $model->setTotal();
                        
        return $this->render('update', [
            'model' => $model,
            'modelItem' => $modelItem,
            'searchModelItemMaster' => $searchModelItemMaster,
            'dataProviderItemMaster' => $dataProviderItemMaster,
            'searchModelItemMasterReadyStock' => $searchModelItemMasterReadyStock,
            'dataProviderItemMasterReadyStock' => $dataProviderItemMasterReadyStock,
        ]);
    }

    public function actionUpdateAjax($id)
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($id);
        $modelItem = new OutgoingItem();
        $modelItem->outgoing_id = $id;

        if (($outgoing_item_id = Yii::$app->request->get('outgoing_item_id')) !== null) {
            $modelItem = OutgoingItem::findOne($outgoing_item_id);
            $modelItemOld = OutgoingItem::findOne($outgoing_item_id);
        }

        if ($model->load($post)) {
            if (!$model->save()) Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($model->errors));
            return $this->redirect(['update-ajax', 'id' => $model->id]);
        } elseif ($modelItem->load($post)) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $response = [];
            $response['success'] = 0;
            $response['message'] = '';
            $response['total'] = null;

            $flag_redirect = 0;

            if (count($model->outgoingItems) >= 20 && $modelItem->isNewRecord && !$model->is_unlimited) {
                $flag_redirect = 1;

                $modelOld = clone $model;
                $model                   = new Outgoing();
                $model->total            = 0;
                $model->outgoing_type_id = 1;
                $model->serial           = $modelOld->serial;
                $model->date             = $modelOld->date;
                $model->due_date         = $modelOld->due_date;
                $model->customer_id      = $modelOld->customer_id;
                $model->salesman_id      = $modelOld->salesman_id;
                $model->remark           = $modelOld->remark;
                if ($model->save()) {
                    $modelItem->outgoing_id = $model->id;
                } else {
                    Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($model->errors));
                }
            }

            if (($item = Item::findOne(['shortcode' => trim($post['item_shortcode'])])) !== null) {
                $modelItem->item_id = $item->id;
            
                $item->shortcode            = $post['item_shortcode'];
                $item->name                 = $post['item_name'];
                $item->brand                = $post['item_brand'];
                $item->type                 = $post['item_type'];
                if (!$item->save()) Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($item->errors));
                
                $flag_save = 0;
                if ($modelItem->isNewRecord) {
                    if ($item->current_quantity >= $modelItem->quantity) $flag_save = 1;
                } else {
                    if (($item->current_quantity + $modelItemOld->quantity) >= $modelItem->quantity) $flag_save = 1;
                }
                if ($flag_save) {
                    if ($modelItem->save()) {
                        $model->setTotal();
                        $response['success'] = 1;
                        $response['total'] = Yii::$app->formatter->asDecimal($model->total, 0);
                    } else {
                        $response['message'] = array_values($modelItem->errors);
                    }
                } else {
                    $response['message'] = 'Quantity tidak cukup: '.$item->current_quantity;
                }
            } else {
                $response['message'] = $post['item_shortcode'] ? 'Barang tidak ditemukan.' : 'Barang tidak boleh kosong.';
            }

            if ($flag_redirect) {
                return $this->redirect(['update-ajax', 'id' => $model->id]);
            } else {
                return $response;
            }
        }

        $searchModelItemMaster = new ItemSearch();
        $dataProviderItemMaster = $searchModelItemMaster->search(Yii::$app->request->queryParams);
        $dataProviderItemMaster->pagination->pageSize = 50;

        $searchModelItemMasterReadyStock = new ItemSearchReadyStock();
        $dataProviderItemMasterReadyStock = $searchModelItemMasterReadyStock->search(Yii::$app->request->queryParams);
        $dataProviderItemMasterReadyStock->pagination->pageSize = 50;

        $model->setTotal();
                        
        return $this->render('update-ajax', [
            'model' => $model,
            'modelItem' => $modelItem,
            'searchModelItemMaster' => $searchModelItemMaster,
            'dataProviderItemMaster' => $dataProviderItemMaster,
            'searchModelItemMasterReadyStock' => $searchModelItemMasterReadyStock,
            'dataProviderItemMasterReadyStock' => $dataProviderItemMasterReadyStock,
        ]);
    }

    public function actionToggleLimit($id) 
    {
        if (Yii::$app->request->post()) 
        {
            $model = $this->findModel($id);
            $model->is_unlimited = $model->is_unlimited ? 0 : 1;
            $model->save();
        }
        return $this->redirect(['update', 'id' => $id]);
    }

    /**
     * Deletes an existing Outgoing model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
            return $this->redirect(['index']);
        } catch (IntegrityException $e) {
            throw new \yii\web\HttpException(500,"Integrity Constraint Violation. This data can not be deleted due to the relation.", 405);
        }
    }

    /**
     * Deletes an existing Outgoing Item model.
     * If deletion is successful, the browser will be redirected to the 'update' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteItem($id)
    {
        try {
            $modelItem = OutgoingItem::findOne($id);
            $modelItem->delete();
            $modelItem->outgoing->setTotal();
            return $this->redirect(['update', 'id' => $modelItem->outgoing_id]);
        } catch (IntegrityException $e) {
            throw new \yii\web\HttpException(500, "Integrity Constraint Violation. This data can not be deleted due to the relation.", 405);
        }
    }

    public function actionDeleteItemAjax($id)
    {
        try {
            $modelItem = OutgoingItem::findOne($id);
            $modelItem->delete();
            $modelItem->outgoing->setTotal();
            return $this->redirect(['update-ajax', 'id' => $modelItem->outgoing_id]);
        } catch (IntegrityException $e) {
            throw new \yii\web\HttpException(500, "Integrity Constraint Violation. This data can not be deleted due to the relation.", 405);
        }
    }

    /**
     * Finds the Outgoing model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Outgoing the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Outgoing::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetUnitOfMeasurement()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $unit_of_measurement = '';
            if (($item = \backend\models\Item::findOne(Yii::$app->request->post('item_id'))) !== null) {
                if ($item->unitOfMeasurement) $unit_of_measurement = $item->unitOfMeasurement->name;
            }

            $response = [
                'unit_of_measurement' => $unit_of_measurement,
                'success' => true,
            ];

            return $response;
        }
    }

    public function actionGetItem()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $price = '';
            $unit_of_measurement = '';

            if (($item = \backend\models\Item::findOne(Yii::$app->request->post('item_id'))) !== null) {
                if ($item->default_price) $price = $item->default_price;
                if ($item->unitOfMeasurement) $unit_of_measurement = $item->unitOfMeasurement->name;
            }
            
            $response = [
                'price' => $price,
                'unit_of_measurement' => $unit_of_measurement,
                'success' => true,
            ];

            return $response;
        }
    }

    public function actionGetItemByShortcode($outgoing_item_id = null)
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $post = Yii::$app->request->post();
            
            $response = [];

            $item = \backend\models\Item::find()->where(['shortcode' => $post['item_shortcode']])->asArray()->one();
            $item['price_groups'] = ItemPrice::find()->joinWith(['priceGroup'])->where(['item_id' => $item['id']])->asArray()->all();

            $response = $item;
            $response['isFound'] = isset($item['id']) ? 1 : 0;

            $basePrice = null;
            $lastPrice = null;
            $lastDiscount = null;
            $lastTransaction = OutgoingItem::find()->joinWith(['outgoing'])->where(['item_id' => $item['id'], 'customer_id' => $post['customer_id']])->orderBy('id DESC')->one();
            if ($lastTransaction) $lastPrice = $lastTransaction->price;
            if ($lastTransaction) $lastDiscount = $lastTransaction->discount;
            if ($lastTransaction) {
                if ($lastTransaction->incomingItemBefore) $basePrice = $lastTransaction->incomingItemBefore->price;
            }
            
            $response['lastPrice'] = $lastPrice;
            $response['basePrice'] = $basePrice;
            $response['lastDiscount'] = $lastDiscount;

            return $response;
        }
    }

    public function actionGetCustomer()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $response = [];
            $customer = \backend\models\Customer::find()->where(['id' => Yii::$app->request->post('customer_id')])->asArray()->one();
            $response = $customer;
            $response['isFound'] = isset($customer['id']) ? 1 : 0;
            $response['outgoingsUnpaid'] = [];

            if ($customer['payment_limit_duration']) {
                $count_of_days_late = 0;
                $outgoing = Outgoing::find()->where(['customer_id' => $customer['id']])->andWhere(['!=', 'payment_status', Outgoing::PAYMENT_ALL])->orderBy('due_date ASC')->asArray()->one();
                if ($outgoing && $outgoing['due_date'] < date('Y-m-d')) {
                    $count_of_days_late = (new DateTime($outgoing['due_date']))->diff(new DateTime(date('Y-m-d')))->days;
                    $response['outgoingsUnpaid'][] = [
                        'outgoing_id' => $outgoing['id'],
                        'count_of_days_late' => $count_of_days_late,
                    ];
                }
            }
            
            return $response;
        }
    }

    public function actionPrint($id, $to_pdf = 0)
    {
        $model  = $this->findModel($id);
        $title  = 'IM' . $model->idText;
        $view   = 'print';
        $pre_params = [
            'model'     => $model,
            'title'     => $title,
            'view'      => $view,
            'to_pdf'    => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdfAll($title, $view, $params, 1);

        return $this->render($view, $params);
    }

    public function actionPrintResume($date_start = '', $date_end = '', $customer_id = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('d/m/Y', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('d/m/Y');

        $date_start_object  = Datetime::createFromFormat('d/m/Y', $date_start);
        $date_end_object    = Datetime::createFromFormat('d/m/Y', $date_end);
        $from               = $date_start_object->format('Y-m-d');
        $to                 = $date_end_object->format('Y-m-d');

        $query = Outgoing::find();
        $query->where(['between', 'date', $from, $to]);
        if ($customer_id) $query->andWhere(['customer_id' => $customer_id]);
        $query->orderBy(['id' => SORT_ASC]);
        $models = $query->all();

        $title  = 'Rekap Faktur';
        $view   = 'print-resume';

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
        return $this->generatePdfResume($title, $view, $params, 1);

        return $this->render($view, $params);
    }

    public function actionPrintAll($date_start = '', $date_end = '', $customer_id = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('d/m/Y', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('d/m/Y');

        $date_start_object  = Datetime::createFromFormat('d/m/Y', $date_start);
        $date_end_object    = Datetime::createFromFormat('d/m/Y', $date_end);
        $from               = $date_start_object->format('Y-m-d');
        $to                 = $date_end_object->format('Y-m-d');

        $query = Outgoing::find();
        $query->where(['between', 'date', $from, $to]);
        if ($customer_id) $query->andWhere(['customer_id' => $customer_id]);
        $query->orderBy(['id' => SORT_ASC]);
        $models = $query->all();

        $title  = 'Rekap Faktur';
        $view   = 'print-all';

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
        return $this->generatePdfAll($title, $view, $params, 1);

        return $this->render($view, $params);
    }



    public function generatePdf($title, $view, $params = [], $landscape = false) {
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            // 'format' => [250, 350],
            'format' => [160, 210],
            // 'defaultFont' => 'courier',
            'orientation' => $landscape ? 'L' : 'P',
            'marginTop' => '17',
            'marginLeft' => '7',
            'marginRight' => '7',
            'marginBottom' => '2',
            'marginHeader' => '5',
            'marginFooter' => '2',
            'filename' => $title,
            'options' => ['title' => $title],
            'content' => $this->renderPartial($view, $params),
            'methods' => [
                'SetHeader' => \backend\helpers\ReportHelper::header($params),
                // 'SetFooter' => ['Print date: ' . date('d/m/Y') . '||Page {PAGENO} of {nbpg}'],
                'SetFooter' => [
                    ' <span style="font-style:normal; font-weight:normal"><big><big>INDAH MOTOR </big> <br> Selama belum lunas, barang ini dianggap barang titipan. </big> </span>
                    | <span style="font-style:normal; font-weight:normal"><big><big>'. count($params['model']->outgoingItems) .' ITEM</big></big> </span>
                    | <span style="font-style:normal; font-weight:normal"><big><big><big>TOTAL : Rp <b> ' . Yii::$app->formatter->asDecimal($params['model']->total, 0) . ' </b></big></big></big> </span>'
                ],
                
            ],
            // 'cssFile' => '@web/css/site.css',
            'cssInline' => '
                body, .printable, .table-report, .table-report-footer, .kv-grid-table { font-size: 11pt; letter-spacing: -1}
                thead { display: table-header-group }
                th, .kv-grid-table th, thead td {text-transform:uppercase; font-weight:bold}
                th, td, .kv-grid-table th, .kv-grid-table td { padding:0px 3px !important }
                .kv-grid-table td, .table td { border:none !important; }
                .table-report-footer td { border:none; padding:0px 3px; }
                .table-report td, .table-report th, .table-report-header td, .table-report-footer td {; padding-top:3px; letter-spacing: -1}
                .table-report thead td {border-bottom:1px solid #000 }
                .table-report-footer { border-top:1px solid #000;}
                .ellipsis {position: relative; }
                .ellipsis:before { content: \'&nbsp;\';visibility: hidden; }
                .ellipsis span { position: absolute; left: 0; right: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            ',
        ]);
        return $pdf->render();
    }

    public function generatePdfResume($title, $view, $params = [], $landscape = false) {
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            // 'format' => [250, 350],
            'format' => [160, 210],
            // 'defaultFont' => 'courier',
            'orientation' => $landscape ? 'L' : 'P',
            'marginTop' => '22',
            'marginLeft' => '7',
            'marginRight' => '7',
            'marginBottom' => '2',
            'marginHeader' => '5',
            'marginFooter' => '2',
            'filename' => $title,
            'options' => ['title' => $title],
            'content' => $this->renderPartial($view, $params),
            'options' => [
                'defaultfooterline' => 0,
            ],
            'methods' => [
                'SetHeader' => \backend\helpers\ReportHelper::header($params),
                // 'SetFooter' => ['Print date: ' . date('d/m/Y') . '||Page {PAGENO} of {nbpg}'],
                'SetFooter' => ['
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <big>Penerima</big>
                    <br><br><br><br><br><br>
                    ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ) 
                    <br> &nbsp; ||'],
            ],
            // 'cssFile' => '@web/css/site.css',
            'cssInline' => '
                body, .printable, .table-report, .table-report-footer, .kv-grid-table { font-size: 11pt; letter-spacing: -1}
                thead { display: table-header-group }
                th, .kv-grid-table th, thead td {text-transform:uppercase; font-weight:bold}
                th, td, .kv-grid-table th, .kv-grid-table td { padding:0px 3px !important }
                .kv-grid-table td, .table td { border:none !important; }
                .table-report-footer td { border:none; padding:0px 3px; }
                .table-report td, .table-report th, .table-report-header td, .table-report-footer td {; padding-top:3px; letter-spacing: -1}
                .table-report thead td {border-bottom:1px solid #000 }
                .table-report-footer { border-top:1px solid #000;}
            ',
        ]);
        return $pdf->render();
    }

    public function generatePdfAll($title, $view, $params = [], $landscape = false) {
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            // 'format' => [250, 350],
            'format' => [160, 210],
            // 'defaultFont' => 'courier',
            'orientation' => $landscape ? 'L' : 'P',
            'marginTop' => '3',
            'marginLeft' => '7',
            'marginRight' => '7',
            'marginBottom' => '2',
            'marginHeader' => '5',
            'marginFooter' => '2',
            'filename' => $title,
            'options' => ['title' => $title],
            'content' => $this->renderPartial($view, $params),
            'options' => [
                'defaultfooterline' => 0,
            ],
            /* 'methods' => [
                // 'SetHeader' => \backend\helpers\ReportHelper::header($params),
                // 'SetFooter' => ['Print date: ' . date('d/m/Y') . '||Page {PAGENO} of {nbpg}'],
            ], */
            // 'cssFile' => '@web/css/site.css',
            'cssInline' => '
                body, .printable, .table-report, .table-report-footer, .kv-grid-table { font-size: 11pt; letter-spacing: -1}
                thead { display: table-header-group }
                th, .kv-grid-table th, thead td {text-transform:uppercase; font-weight:bold}
                th, td, .kv-grid-table th, .kv-grid-table td { padding:0px 3px !important }
                .kv-grid-table td, .table td { border:none !important; }
                .table-report-footer td { border:none; padding:0px 3px; }
                .table-report td, .table-report th, .table-report-header td, .table-report-footer td {; padding-top:3px; letter-spacing: -1}
                .table-report thead td {border-bottom:1px solid #000 }
                .table-report-footer { border-top:1px solid #000;}
            ',
        ]);
        return $pdf->render();
    }

    public function actionSetPaymentStatuses()
    {
        $customers = Customer::find()->all();
        foreach ($customers as $customer) {
            $customer->setPaymentStatus();
        }
        return $this->redirect(['index']);
    }
}

