<?php

namespace backend\controllers;

use Yii;
use DateTime;
use backend\models\Item;
use backend\models\ItemSearch;
use backend\models\OrderItemSearch;
use backend\models\OrderItem;
use backend\models\Order;
use backend\models\OrderSearch;
use backend\models\Supplier;
use kartik\mpdf\Pdf;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\IntegrityException;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
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
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all OrderItem models.
     * @return mixed
     */
    public function actionIndexItem()
    {
        $searchModel = new OrderItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index-item', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOrderToSupplier($date_start = '', $date_end = '', $supplier_id = '', $customer_name = '', $brand_supplier = '', $order_by_brand = 0, $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('d/m/Y', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('d/m/Y');

        $date_start_object  = Datetime::createFromFormat('d/m/Y', $date_start);
        $date_end_object    = Datetime::createFromFormat('d/m/Y', $date_end);
        $from               = $date_start_object->format('Y-m-d');
        $to                 = $date_end_object->format('Y-m-d');

        $query = OrderItem::find();
        $query->joinWith(['order']);
        $query->where(['between', 'date', $from, $to]);
        if ($customer_name) $query->andWhere(['customer_name' => $customer_name]);
        if ($supplier_id) $query->andWhere(['supplier_id' => $supplier_id]);
        if ($brand_supplier) $query->andWhere(['brand_supplier' => $brand_supplier]);

        if ($order_by_brand) $query->orderBy('brand_supplier ASC, item_name ASC');
        else $query->orderBy(['item_name' => SORT_ASC]);

        $models = $query->all();

        $query->joinWith(['item']);
        $total_net_price = $query->sum('purchase_net_price * order_item.to_be_ordered');
        $total_gross_price = $query->sum('purchase_gross_price * order_item.to_be_ordered');

        $supplier = Supplier::findOne($supplier_id);

        $title  = 'ORDER DISTRIBUTOR';
        if ($to_pdf && $supplier_id) $title = $supplier ? ' '.$supplier->name : '';
        if ($to_pdf && $customer_name) $title.= ' '.$customer_name;
        if ($to_pdf) $title.= ' '.date('d/m/Y');
        if ($to_pdf) $title.= '.pdf';
        $view   = 'order-to-supplier';

        $pre_params = [
            'models'            => $models, 
            'date_start'        => $date_start,
            'date_end'          => $date_end,
            'supplier_id'       => $supplier_id,
            'customer_name'     => $customer_name,
            'brand_supplier'    => $brand_supplier,
            'order_by_brand'    => $order_by_brand,
            'total_net_price'   => $total_net_price,
            'total_gross_price' => $total_gross_price,
            'title'             => $title,
            'view'              => $view,
            'to_pdf'            => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    public function actionOrderToStorage($date_start = '', $date_end = '', $customer_name = '', $brand_storage = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('d/m/Y', strtotime('-6 days'));
        if ($date_end == '') $date_end = date('d/m/Y');

        $date_start_object = Datetime::createFromFormat('d/m/Y', $date_start);
        $date_end_object = Datetime::createFromFormat('d/m/Y', $date_end);
        $from = $date_start_object->format('Y-m-d');
        $to = $date_end_object->format('Y-m-d');

        $query = OrderItem::find();
        $query->joinWith(['order']);
        $query->where(['between', 'date', $from, $to]);
        if ($customer_name) $query->andWhere(['customer_name' => $customer_name]);
        if ($brand_storage) $query->andWhere(['brand_storage' => $brand_storage]);
        $query->orderBy(['item_name' => SORT_ASC]);
        $models = $query->all();

        $query->joinWith(['item']);
        $total_net_price = $query->sum('purchase_net_price * order_item.quantity');
        $total_gross_price = $query->sum('purchase_gross_price * order_item.quantity');

        $title  = 'ORDER GUDANG';
        $view   = 'order-to-storage';

        $pre_params = [
            'models'            => $models, 
            'date_start'        => $date_start,
            'date_end'          => $date_end,
            'customer_name'     => $customer_name,
            'brand_storage'     => $brand_storage,
            'total_net_price'   => $total_net_price,
            'total_gross_price' => $total_gross_price,
            'title'             => $title,
            'view'              => $view,
            'to_pdf'            => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

        $model->date = date('Y-m-d');

        if ($model->load(Yii::$app->request->post())) {
            if (($existed = Order::findOne(['customer_name' => $model->customer_name, 'date' => $model->date])) === null ) {
                if ($model->save()) {
                    $post = Yii::$app->request->post();
                    if (isset($post['to_update_ajax']) && $post['to_update_ajax']) return $this->redirect(['update-ajax', 'id' => $model->id]);
                    return $this->redirect(['update', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($model->errors));
                }
            } else {
                return $this->redirect(['update', 'id' => $existed->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelItem = new OrderItem();
        $modelItem->order_id = $id;

        if (($order_item_id = Yii::$app->request->get('order_item_id')) !== null) {
            $modelItem = OrderItem::findOne($order_item_id);
        }

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->save()) Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($model->errors));
            return $this->redirect(['update', 'id' => $model->id]);
        } elseif ($modelItem->load(Yii::$app->request->post())) {
            if (($item = Item::findOne(['shortcode' => $modelItem->item_shortcode])) !== null) {
                $modelItem->item_id = $item->id;
            }
            if ($modelItem->save()) {
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($modelItem->errors));
            }
        }

        $searchModelItemMaster = new ItemSearch();
        $dataProviderItemMaster = $searchModelItemMaster->search(Yii::$app->request->queryParams);
        $dataProviderItemMaster->pagination->pageSize = 50;

        return $this->render('update', [
            'model' => $model,
            'modelItem' => $modelItem,
            'searchModelItemMaster' => $searchModelItemMaster,
            'dataProviderItemMaster' => $dataProviderItemMaster,
        ]);
    }

    public function actionUpdateAjax($id)
    {
        $post = Yii::$app->request->post();
        $model = $this->findModel($id);
        $modelItem = new OrderItem();
        $modelItem->order_id = $id;

        if (($order_item_id = Yii::$app->request->get('order_item_id')) !== null) {
            $modelItem = OrderItem::findOne($order_item_id);
        }
        
        if ($model->load($post)) {
            if (!$model->save()) Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($model->errors));
            return $this->redirect(['update', 'id' => $model->id]);
        } elseif ($modelItem->load($post)) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $response = [];
            $response['success'] = 0;
            $response['message'] = '';
            $response['total'] = null;

            if (($item = Item::findOne(['shortcode' => $modelItem->item_shortcode])) !== null) {
                $modelItem->item_id = $item->id;
            }
            if ($modelItem->save()) {
                $total_order = OrderItem::find()->joinWith(['order', 'item'])->where(['order_id' => $model->id])->sum('quantity * purchase_net_price');
                $response['success'] = 1;
                $response['total'] = Yii::$app->formatter->asDecimal($total_order, 0);
            } else {
                $response['message'] = array_values($modelItem->errors);
            }
            return $response;
        }
    
        $searchModelItemMaster = new ItemSearch();
        $dataProviderItemMaster = $searchModelItemMaster->search(Yii::$app->request->queryParams);
        $dataProviderItemMaster->pagination->pageSize = 50;

        return $this->render('update-ajax', [
            'model' => $model,
            'modelItem' => $modelItem,
            'searchModelItemMaster' => $searchModelItemMaster,
            'dataProviderItemMaster' => $dataProviderItemMaster,
        ]);
    }

    public function actionDeleteItem($id)
    {
        try {
            $model = OrderItem::findOne($id);
            $model->delete();
            return $this->redirect(['update', 'id' => $model->order_id]);
        } catch (IntegrityException $e) {
            throw new \yii\web\HttpException(500,"Integrity Constraint Violation. This data can not be deleted due to the relation.", 405);
        }
    }

    public function actionDeleteItemAjax($id)
    {
        try {
            $model = OrderItem::findOne($id);
            $model->delete();
            return $this->redirect(['update-ajax', 'id' => $model->order_id]);
        } catch (IntegrityException $e) {
            throw new \yii\web\HttpException(500,"Integrity Constraint Violation. This data can not be deleted due to the relation.", 405);
        }
    }

    /**
     * Deletes an existing Order model.
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
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetItemByShortcode()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $post = Yii::$app->request->post();

            $response = [];

            $item = \backend\models\Item::find()->where(['shortcode' => $post['item_shortcode']])->asArray()->one();
            
            $response = $item;
            $response['isFound'] = isset($item['id']) ? 1 : 0;

            $lastSunday     = date('w') === 0 ? date('Y-m-d') : date('Y-m-d', strtotime('last Sunday'));
            $nextSaturday   = date('w') === 6 ? date('Y-m-d') : date('Y-m-d', strtotime('next Saturday'));

            $response['lastSunday']     = $lastSunday;
            $response['nextSaturday']   = $nextSaturday;

            $response['total_order_quantity'] = OrderItem::find()->joinWith(['order'])->where(['item_id' => $item['id']])->andWhere([
                'and',
                ['>=', 'date', $lastSunday],
                ['<=', 'date', $nextSaturday],
            ])->sum('quantity');
            $response['total_order_value'] = OrderItem::find()->joinWith(['order', 'item'])->where(['item_id' => $item['id']])->andWhere([
                'and',
                ['>=', 'date', $lastSunday],
                ['<=', 'date', $nextSaturday],
            ])->sum('quantity * purchase_gross_price');
            
            if (!$response['total_order_quantity']) $response['total_order_quantity'] = 0;
            if (!$response['total_order_value']) $response['total_order_value'] = 0;
            
            return $response;
        }
    }

    public function actionGetTotalToBeOrdered()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $post = Yii::$app->request->post();

            $response = [];

            $item = \backend\models\Item::find()->where(['shortcode' => $post['item_shortcode']])->asArray()->one();

            $lastSunday     = date('w') === 0 ? date('Y-m-d') : date('Y-m-d', strtotime('last Sunday'));
            $nextSaturday   = date('w') === 6 ? date('Y-m-d') : date('Y-m-d', strtotime('next Saturday'));

            $response['lastSunday']     = $lastSunday;
            $response['nextSaturday']   = $nextSaturday;
            
            $response['total_to_be_ordered_quantity'] = OrderItem::find()->joinWith(['order', 'item'])->where(['item_id' => $item['id'], 'supplier_id' => $post['supplier_id']])->andWhere([
                'and',
                ['>=', 'date', $lastSunday],
                ['<=', 'date', $nextSaturday],
            ])->sum('quantity');
            $response['total_to_be_ordered_value'] = OrderItem::find()->joinWith(['order', 'item'])->where(['item_id' => $item['id'], 'supplier_id' => $post['supplier_id']])->andWhere([
                'and',
                ['>=', 'date', $lastSunday],
                ['<=', 'date', $nextSaturday],
            ])->sum('quantity * purchase_gross_price');

            if (!$response['total_to_be_ordered_quantity']) {
                $response['total_to_be_ordered_quantity'] = 0;
                $response['total_to_be_ordered_value'] = 0;
            }

            return $response;
        }
    }

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
}
