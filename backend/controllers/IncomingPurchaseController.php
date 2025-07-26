<?php

namespace backend\controllers;

use Yii;
use backend\models\Config;
use backend\models\IncomingItem;
use backend\models\Incoming;
use backend\models\IncomingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\IntegrityException;
use backend\models\ItemPrice;
use backend\models\Item;
use backend\models\ItemSearch;
use backend\models\PriceGroup;
use backend\models\Supplier;
use kartik\mpdf\Pdf;

/**
 * IncomingPurchaseController implements the CRUD actions for Incoming model.
 */
class IncomingPurchaseController extends Controller
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
     * Lists all Incoming models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (($incomings = Incoming::findAll(['count_of_items' => null])) !== null) {
            foreach ($incomings as $incoming) {
                $incoming->setTotal();
            }
        }

        $searchModel = new IncomingSearch();
        $queryParams = Yii::$app->request->queryParams;
        // if (!isset($queryParams['IncomingSearch'])) $queryParams['IncomingSearch'] = [];
        // $queryParams['IncomingSearch'] = array_replace($queryParams['IncomingSearch'], ['incoming_type_id' => 1]);
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Incoming model.
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
     * Creates a new Incoming model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Incoming();
        $model->incoming_type_id = 1;
        $model->date = date('Y-m-d');
        $model->total = 0;
        if (Config::findOne(['key' => 'AutomaticPurchaseSerial'])->value == '1') {
            $model->serial = microtime(true)*10000;
        }
        if (Config::findOne(['key' => 'GlobalPaymentLimit'])->value != '0') {
            $model->due_date = date('Y-m-d', strtotime($model->date . ' +' . Config::findOne(['key' => 'GlobalPaymentLimit'])->value . ' day'));
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                $post = Yii::$app->request->post();
                if (isset($post['to_update_ajax']) && $post['to_update_ajax']) return $this->redirect(['update-ajax', 'id' => $model->id]);
                return $this->redirect(['update', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($model->errors));
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Incoming model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $post       = Yii::$app->request->post();
        $model      = $this->findModel($id);
        $modelItem  = new IncomingItem();
        $modelItem->incoming_id = $id;

        if (($incoming_item_id = Yii::$app->request->get('incoming_item_id')) !== null) {
            $modelItem = IncomingItem::findOne($incoming_item_id);
        }

        if ($model->load($post)) {
            if (!$model->save()) Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($model->errors));
            return $this->redirect(['update-ajax', 'id' => $model->id]);
        } elseif ($modelItem->load(Yii::$app->request->post())) {
            if (($item = Item::findOne(['shortcode' => $post['item_shortcode']])) !== null) {
                $modelItem->item_id = $item->id;
            } else {
                $item = new Item();
            }
            $item->shortcode            = $post['item_shortcode'];
            $item->name                 = $post['item_name'];
            $item->brand                = $post['item_brand'];
            $item->type                 = $post['item_type'];
            $item->unit_of_measurement  = $post['item_unit_of_measurement'];
            $item->purchase_net_price   = $modelItem->price;
            $item->purchase_gross_price = $modelItem->gross_price;
            $item->purchase_discount    = $modelItem->discount;
            $item->location             = $post['item_location'];
            if ($item->save()) {
                $priceGroups = PriceGroup::find()->all();
                foreach ($priceGroups as $priceGroup) {
                    $itemPrice = ItemPrice::findOne(['item_id' => $item->id, 'price_group_id' => $priceGroup->id]);
                    if ($itemPrice == null) {
                        $itemPrice                  = new ItemPrice();
                        $itemPrice->item_id         = $item->id;
                        $itemPrice->price_group_id  = $priceGroup->id;
                    }
                    $itemPrice->discount    = $post[$priceGroup->name . '-discount'];
                    $itemPrice->price       = $post[$priceGroup->name . '-price'];
                    $itemPrice->save();
                }
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

        $model->setTotal();

        return $this->render('update', [
            'model' => $model,
            'modelItem' => $modelItem,
            'searchModelItemMaster' => $searchModelItemMaster,
            'dataProviderItemMaster' => $dataProviderItemMaster,
        ]);
    }
    
    public function actionUpdateAjax($id)
    {
        $post       = Yii::$app->request->post();
        $model      = $this->findModel($id);
        $modelItem  = new IncomingItem();
        $modelItem->incoming_id = $id;

        if (($incoming_item_id = Yii::$app->request->get('incoming_item_id')) !== null) {
            $modelItem = IncomingItem::findOne($incoming_item_id);
        } /* else {
			$modelItem = IncomingItem::find()->where(['incoming_id' => $id])->orderBy('id DESC')->one();
			if ($modelItem) {
				$incoming_item_id = $modelItem->id;
				$this->redirect(['update-ajax', 'id' => $id, 'incoming_item_id' => $incoming_item_id]);
			}
		} */

        if ($model->load($post)) {
            if (!$modelItem->save()) Yii::$app->session->setFlash('error', \yii\helpers\Json::encode($modelItem->errors));
            $model->setTotal();
            return $this->redirect(['update-ajax', 'id' => $model->id]);
        } elseif ($modelItem->load($post)) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $response = [];
            $response['success'] = 0;
            $response['message'] = '';
            $response['total'] = null;
            
            if (($item = Item::findOne(['shortcode' => $post['item_shortcode']])) !== null) {
                $modelItem->item_id = $item->id;
            } else {
                $item = new Item();
            }
            $item->shortcode            = $post['item_shortcode'];
            $item->name                 = $post['item_name'];
            $item->brand                = $post['item_brand'];
            $item->type                 = $post['item_type'];
            $item->unit_of_measurement  = $post['item_unit_of_measurement'];
            $item->purchase_net_price   = $modelItem->price;
            $item->purchase_gross_price = $modelItem->gross_price;
            $item->purchase_discount    = $modelItem->discount;
            $item->location             = $post['item_location'];
            if ($item->save()) {
                $priceGroups = PriceGroup::find()->all();
                foreach ($priceGroups as $priceGroup) {
                    $itemPrice = ItemPrice::findOne(['item_id' => $item->id, 'price_group_id' => $priceGroup->id]);
                    if ($itemPrice == null) {
                        $itemPrice                  = new ItemPrice();
                        $itemPrice->item_id         = $item->id;
                        $itemPrice->price_group_id  = $priceGroup->id;
                    }
                    $itemPrice->discount    = $post[$priceGroup->name . '-discount'];
                    $itemPrice->price       = $post[$priceGroup->name . '-price'];
                    $itemPrice->save();
                }
                $modelItem->item_id = $item->id;
            }
            if ($modelItem->save()) {
                $model->setTotal();
                $response['success'] = 1;
                $response['total'] = Yii::$app->formatter->asDecimal($model->total, 0);
            } else {
                $response['message'] = array_values($modelItem->errors);
            }
            return $response;
        }

        $searchModelItemMaster = new ItemSearch();
        $dataProviderItemMaster = $searchModelItemMaster->search(Yii::$app->request->queryParams);
        $dataProviderItemMaster->pagination->pageSize = 50;

        $model->setTotal();

        return $this->render('update-ajax', [
            'model' => $model,
            'modelItem' => $modelItem,
            'searchModelItemMaster' => $searchModelItemMaster,
            'dataProviderItemMaster' => $dataProviderItemMaster,
        ]);
    }

    /**
     * Deletes an existing Incoming model.
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
     * Deletes an existing Incoming Item model.
     * If deletion is successful, the browser will be redirected to the 'update' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteItem($id)
    {
        try {
            $modelItem = IncomingItem::findOne($id);
            $modelItem->delete();
            $modelItem->incoming->setTotal();
            return $this->redirect(['update', 'id' => $modelItem->incoming_id]);
        } catch (IntegrityException $e) {
            throw new \yii\web\HttpException(500, "Integrity Constraint Violation. This data can not be deleted due to the relation.", 405);
        }
    }

    public function actionDeleteItemAjax($id)
    {
        try {
            $modelItem = IncomingItem::findOne($id);
            $modelItem->delete();
            $modelItem->incoming->setTotal();
            return $this->redirect(['update-ajax', 'id' => $modelItem->incoming_id]);
        } catch (IntegrityException $e) {
            throw new \yii\web\HttpException(500, "Integrity Constraint Violation. This data can not be deleted due to the relation.", 405);
        }
    }

    /**
     * Finds the Incoming model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Incoming the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Incoming::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSimple()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $response = array(
                'body' => date('Y-m-d H:i:s'),
                'success' => true,
            );

            return $response;
        }
    }

    public function actionGetItemByShortcode()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $response = [];

            $item = Item::find()->where(['shortcode' => Yii::$app->request->post('item_shortcode')])->asArray()->one();
            if ($item !== null) {
                $item['price_groups'] = ItemPrice::find()->joinWith(['priceGroup'])->where(['item_id' => $item['id']])->asArray()->all();
                $response = $item;
            }
            $response['isFound'] = $item !== null ? 1 : 0;
            
            return $response;
        }
    }

    public function actionSetPaymentStatuses()
    {
        $suppliers = Supplier::find()->all();
        foreach ($suppliers as $supplier) {
            $supplier->setPaymentStatus();
        }
        return $this->redirect(['index']);
    }

    public function actionPrint($id, $to_pdf = 0)
    {
        $model  = $this->findModel($id);
        $title  = $this->id;
        $view   = 'incoming-print';
        $pre_params = [
            'model'     => $model,
            'title'     => $title,
            'view'      => $view,
            'to_pdf'    => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }
    
    public function generatePdf($title, $view, $params = [], $landscape = false) {
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => 'A4',
            'orientation' => $landscape ? 'L' : 'P',
            'marginTop' => '33',
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
