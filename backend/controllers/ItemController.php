<?php

namespace backend\controllers;

use backend\models\IncomingItem;
use Datetime;
use Yii;
use backend\models\Item;
use backend\models\ItemSearch;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\IntegrityException;
use backend\models\ItemPrice;
use backend\models\OutgoingItem;
use backend\models\PriceGroup;
use backend\models\UnitOfMeasurement;
use backend\models\Supplier;
use backend\models\StockHistory;
use backend\models\ViewItemPriceSearch;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use kartik\mpdf\Pdf;

/**
 * ItemController implements the CRUD actions for Item model.
 */
class ItemController extends Controller
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
     * Lists all Item models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        $searchModelVIP = new ViewItemPriceSearch();
        $dataProviderVIP = $searchModelVIP->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderVIP' => $dataProviderVIP,
        ]);
    }

    /**
     * Displays a single Item model.
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
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Item();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionDuplicate($id)
    {
        $modelOrigin = $this->findModel($id);
        $model = new Item();
        
        $model->name                    = $modelOrigin->name;
        $model->shortcode               = $modelOrigin->shortcode;
        $model->brand                   = $modelOrigin->brand;
        $model->type                    = $modelOrigin->type;
        $model->unit_of_measurement     = $modelOrigin->unit_of_measurement;
        $model->current_quantity        = $modelOrigin->current_quantity;
        $model->purchase_net_price      = $modelOrigin->purchase_net_price;
        $model->purchase_gross_price    = $modelOrigin->purchase_gross_price;
        $model->purchase_discount       = $modelOrigin->purchase_discount;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('duplicate', [
                'model' => $model,
                'modelOrigin' => $modelOrigin,
            ]);
        }
    }

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model  = $this->findModel($id);
        $post   = Yii::$app->request->post();

        if ($model->load($post)) {
            if ($model->save()) {
                $priceGroups = PriceGroup::find()->all();
                foreach ($priceGroups as $priceGroup) {
                    if (isset($post[$priceGroup->name . '-discount'])) {
                        $itemPrice = ItemPrice::findOne(['item_id' => $model->id, 'price_group_id' => $priceGroup->id]);
                        if ($itemPrice == null) {
                            $itemPrice = new ItemPrice();
                            $itemPrice->item_id = $model->id;
                            $itemPrice->price_group_id = $priceGroup->id;
                        }
                        $itemPrice->discount = $post[$priceGroup->name . '-discount'];
                        $itemPrice->price = $post[$priceGroup->name . '-price'];
                        $itemPrice->save();
                    }
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Item model.
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
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Item::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionImport() 
    {
        if ($post = Yii::$app->request->post()) {
            $packageFile    = UploadedFile::getInstanceByName('package-file');
            $reader         = ReaderFactory::create(Type::XLSX);
            $reader->open($packageFile->tempName);

            $unsaved_rows = [];
            $saved_count = 0;
            
            foreach ($reader->getSheetIterator() as $sheet) {
                $rowCount = 0;
                foreach ($sheet->getRowIterator() as $row) {
                    $rowCount++;
                    if ($rowCount >= 2) {

                        $model = new Item();
                        $model->name                    = $row[1] ? trim((string)$row[1]) : '-';
                        $model->shortcode               = trim((string)$row[2]);
                        $model->brand                   = trim((string)$row[3]);
                        $model->type                    = trim((string)$row[4]);
                        $model->unit_of_measurement     = trim((string)$row[5]);
                        $model->current_quantity        = trim((string)$row[6]);
                        $model->purchase_net_price      = trim((string)$row[7]);
                        $model->purchase_gross_price    = trim((string)$row[8]);
                        $model->purchase_discount       = trim((string)$row[9]);
                        
                        if ($model->save()) {
                            $saved_count++;

                            $priceGroups = PriceGroup::find()->all();
                            $i = 10;
                            foreach ($priceGroups as $priceGroup) {
                                if ($row[$i+1]) {
                                    $itemPrice = new ItemPrice();
                                    $itemPrice->item_id         = $model->id;
                                    $itemPrice->price_group_id  = $priceGroup->id;
                                    $itemPrice->discount        = trim((string)$row[$i]);
                                    $itemPrice->price           = trim((string)$row[($i+1)]);
                                    if (!$itemPrice->save()) {
                                        dd($itemPrice->errors);
                                    }
                                }
                                $i+= 2;
                            }
                        } else {
                            $unsaved_rows[] = $rowCount;
                        }
                    } 
                }
            }
            $reader->close();
            $unsaved_rows_str = implode(', ', $unsaved_rows);
            if ($unsaved_rows) Yii::$app->session->setFlash('warning', 
                $saved_count.' rows has been imported. 
                <br>You may want to re-check the following unsaved rows : '.$unsaved_rows_str);
            return $this->redirect(['index']);
        } else {
            return $this->render('import');
        }
    }

    public function actionStockHistory($item_id = '', $date_start = '', $date_end = '', $to_pdf = 0)
    {
        if ($date_start == '')  $date_start = date('Y-m-d', strtotime('-6 days'));
        if ($date_end == '')    $date_end   = date('Y-m-d');

        $from = $date_start;
        $to   = $date_end;

        $item = Item::findOne($item_id);

        $query = StockHistory::find();
        $query->where(['between', 'date', $from, $to]);
        $query->andWhere(['item_id' => $item_id]);
        $query->orderBy([
            'date' => SORT_ASC,
            'transaction_type' => SORT_ASC,
        ]);
        $models = $query->all();

        $title  = 'HISTORY STOK';
        $view   = 'stock-history';

        $pre_params = [
            'models'        => $models, 
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'item_id'       => $item_id,
            'title'         => $title,
            'view'          => $view,
            'to_pdf'        => $to_pdf,
            'item'          => $item,
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
                .stock-history th { width:1px; white-space:nowrap}
            ',
        ]);
        return $pdf->render();
    }

    public function actionItemReport($shelf = '', $shortcode = '', $name = '', $brand = '', $type = '', $ready_stock_only = 0, $to_pdf = 0)
    {
        $query = Item::find();
        /* if ($shelf) $query->andWhere(['or', 
            ['location' => $shelf],
            ['like', 'location', '%,'.$shelf, false],
            ['like', 'location', '%, '.$shelf, false],
            ['like', 'location', $shelf.',%', false],
            ['like', 'location', '%,'.$shelf.',%', false],
            ['like', 'location', '%, '.$shelf.',%', false],
        ]); */
        if ($shelf) $query->andWhere(['like', 'location', $shelf]);
        if ($shortcode) $query->andWhere(['like', 'shortcode', $shortcode]);
        if ($name) $query->andWhere(['like', 'name', $name]);
        if ($brand) $query->andWhere(['like', 'brand', $brand]);
        if ($type) $query->andWhere(['like', 'type', $type]);

        if ($ready_stock_only) $query->andWhere(['>', 'current_quantity', 0]);
        $query->orderBy('shortcode ASC, name ASC, brand ASC, type ASC');
        $models = $query->all();

        $title  = 'LAPORAN BARANG';
        $view   = 'item-report';

        $pre_params = [
            'models'           => $models,
            'shelf'            => $shelf,
            'shortcode'        => $shortcode,
            'name'             => $name,
            'brand'            => $brand,
            'type'             => $type,
            'ready_stock_only' => $ready_stock_only,
            'title'            => $title,
            'view'             => $view,
            'to_pdf'           => $to_pdf,
        ];
        $params = array_merge($pre_params, ['params' => $pre_params]);

        if ($to_pdf)
        return $this->generatePdf($title, $view, $params, 0);

        return $this->render($view, $params);
    }

    public function actionSetInitialQuantities()
    {
        $items = Item::find()->all();

        foreach ($items as $item) {
            $sum_incoming = IncomingItem::find()->where(['item_id' => $item->id])->sum('quantity');
            $sum_outgoing = OutgoingItem::find()->where(['item_id' => $item->id])->sum('quantity');
            $sum_diff     = $sum_incoming - $sum_outgoing;

            $item->initial_quantity = $item->current_quantity - $sum_diff;
            $item->save();
        }
        return $this->redirect(['index']);
    }

    public function actionCheckStock()
    {
        return $this->render('check-stock');
    }
}
