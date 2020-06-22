<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\helpers\StringHelper;
use yii\db\ActiveRecordInterface;


/* @var $this yii\web\View */
/* @var $generator \wodrow\wajaxcrud\generators\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$formModelClass = StringHelper::basename($generator->formModelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
    $searchModelAlias = $searchModelClass . 'Search';
}
if ($modelClass === $formModelClass) {
    $formModelAlias = $formModelClass . 'Form';
}

/* @var $class ActiveRecordInterface */
$class = $generator->modelClass;
$pks = $class::primaryKey();
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();
$statusField = $generator->statusField;

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
use <?= ltrim($generator->formModelClass, '\\') . (isset($formModelClass) ? " as $formModelAlias" : "") ?>;
use yii\data\ActiveDataProvider;
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use kartik\grid\EditableColumnAction;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $formModelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    public function actions()
    {
        return [
            'editable-edit' => [
                'class' => EditableColumnAction::class,
                'modelClass' => <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>::class,                // the model for the record being edited
                'scenario' => <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>::SCENARIO_EDITABLE,
                'outputValue' => function ($model, $attribute, $key, $index) {
                    return (int)$model->$attribute / 100;      // return any custom output value if desired
                },
                'outputMessage' => function ($model, $attribute, $key, $index) {
                    return '';                                  // any custom error to return after model save
                },
                'showModelErrors' => true,                        // show model validation errors after save
                'errorOptions' => ['header' => ''],              // error summary HTML options
                'postOnly' => true,
                'ajaxOnly' => true,
                // 'findModel' => function($id, $action) {},
                // 'checkAccess' => function($action, $model) {}
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulkdelete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all <?= $modelClass ?> models.
     * @return mixed
     */
    public function actionIndex()
    {    
        <?php if (!empty($generator->searchModelClass)): ?>$searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
<?php else: ?>
        $dataProvider = new ActiveDataProvider([
            'query' => <?= $modelClass ?>::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
<?php endif; ?>
    }


    /**
     * Displays a single <?= $formModelClass ?> model.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionView(<?= $actionParams ?>)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title' => "<?= $formModelClass ?>({<?= $actionParams ?>})",
                    'content' => $this->renderAjax('view', [
                        'model' => $this->findModel(<?= $actionParams ?>),
                    ]),
                    'footer' => Html::button('关闭', ['class' => 'btn btn-default pull-left','data-dismiss' => "modal"]).
                            Html::a('编辑', ['update','<?= substr($actionParams,1) ?>' => <?= $actionParams ?>], ['class' => 'btn btn-primary','role' => 'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel(<?= $actionParams ?>),
            ]);
        }
    }

    /**
     * Creates a new <?= $formModelAlias ?> model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new <?= $formModelAlias ?>();

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title' => "新建 <?= $formModelAlias ?>",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('关闭', ['class' => 'btn btn-default pull-left','data-dismiss' => "modal"]).
                                Html::button('保存', ['class' => 'btn btn-primary','type' => "submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "Create new <?= $formModelClass ?>",
                    'content' => '<span class="text-success">Create <?= $formModelClass ?> success</span>',
                    'footer' => Html::button('关闭', ['class' => 'btn btn-default pull-left','data-dismiss' => "modal"]).
                            Html::a('新建更多', ['create'], ['class' => 'btn btn-primary','role' => 'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title' => "新建 <?= $formModelClass ?>",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('关闭', ['class' => 'btn btn-default pull-left','data-dismiss' => "modal"]).
                                Html::button('保存', ['class' => 'btn btn-primary','type' => "submit"])
        
                ];         
            }
        }else{
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', <?= $urlParams ?>]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Updates an existing <?= $formModelAlias ?> model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionUpdate(<?= $actionParams ?>)
    {
        $request = Yii::$app->request;
        $model = $this->findModel(<?= $actionParams ?>);       

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title' => "修改 <?= $formModelClass ?>({<?= $actionParams ?>})",
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('关闭', ['class' => 'btn btn-default pull-left','data-dismiss' => "modal"]).
                                Html::button('保存', ['class' => 'btn btn-primary','type' => "submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "<?= $formModelClass ?> #".<?= $actionParams ?>,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('关闭', ['class' => 'btn btn-default pull-left','data-dismiss' => "modal"]).
                            Html::a('编辑', ['update','<?= substr($actionParams,1) ?>' => <?= $actionParams ?>], ['class' => 'btn btn-primary','role' => 'modal-remote'])
                ];    
            }else{
                 return [
                    'title' => "修改 <?= $formModelClass ?> #".<?= $actionParams ?>,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('关闭', ['class' => 'btn btn-default pull-left','data-dismiss' => "modal"]).
                                Html::button('保存', ['class' => 'btn btn-primary','type' => "submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', <?= $urlParams ?>]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing <?= $formModelClass ?> model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionDelete(<?= $actionParams ?>, $type = 'hard')
    {
        $request = Yii::$app->request;
        $model = $this->findModel(<?= $actionParams ?>);
        switch($type){
            case 'hard':
                $model->delete();
                break;
            <?php if($statusField): ?>case 'soft':
                $model->status = <?= $formModelClass ?>::STATUS_DELETE;
                $model->save();
                break;
            <?php endif; ?>default:
                break;
        }

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true,'forceReload' => '#crud-datatable-pjax'];
        }else{
            return $this->redirect(['index']);
        }
    }

     /**
     * Delete multiple existing <?= $formModelClass ?> model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionBulkdelete($type = 'hard')
    {        
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' )); // Array or selected records primary keys
        foreach ( $pks as $pk ) {
            $model = $this->findModel($pk);
            switch($type){
                case 'hard':
                    $model->delete();
                    break;
                <?php if($statusField): ?>case 'soft':
                    $model->status = <?= $formModelClass ?>::STATUS_DELETE;
                    $model->save();
                    break;
                <?php endif; ?>default:
                    break;
            }
        }

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true,'forceReload' => '#crud-datatable-pjax'];
        }else{
            return $this->redirect(['index']);
        }
    }

    public function actionTest(<?= $actionParams ?>)
    {
        $request = Yii::$app->request;
        $model = $this->findModel(<?= $actionParams ?>);

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                'title' => "test <?= $formModelClass ?>({<?= $actionParams ?>})",
                    'content' => $this->renderAjax('test', [
                    'model' => $model,
                ]),
                'footer' =>
                    Html::button('关闭', ['class' => 'btn btn-default pull-left','data-dismiss' => "modal"]).
                    Html::button('test', ['class' => 'btn btn-primary', 'type' => "submit"]),
                ];
            }elseif($model->load($request->post()) && $model->validate()){
                # TO DO test
                return ['forceClose' => true,'forceReload' => '#crud-datatable-pjax'];
            }else{
                return [
                    'title' => "test <?= $formModelClass ?>({<?= $actionParams ?>})",
                    'content' => $this->renderAjax('test', [
                    'model' => $model,
                ]),
                'footer' =>
                    Html::button('关闭', ['class' => 'btn btn-default pull-left','data-dismiss' => "modal"]).
                    Html::button('test', ['class' => 'btn btn-primary', 'type' => "submit"]),
                ];
            }
        }else{
            if ($model->load($request->post())) {
                return $this->redirect(['view', <?= $urlParams ?>]);
            } else {
                return $this->render('test', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete multiple existing <?= $modelClass ?> model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return mixed
     */
    public function actionBulktest()
    {
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' ));
        foreach ( $pks as $pk ) {
            $model = $this->findModel($pk);
            # TO DO
        }
        if($request->isAjax){
        Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true,'forceReload' => '#crud-datatable-pjax'];
        }else{
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the <?= $formModelAlias ?> model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
     * @return <?=                   $formModelAlias ?> the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(<?= $actionParams ?>)
    {
        <?php
        if (count($pks) === 1) {
            $condition = '$id';
        } else {
            $condition = [];
            foreach ($pks as $pk) {
                $condition[] = "'$pk' => \$$pk";
            }
            $condition = '[' . implode(', ', $condition) . ']';
        }
        ?>if (($model = <?= $formModelAlias ?>::findOne(<?= $condition ?>)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
