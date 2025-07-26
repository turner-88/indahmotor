<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\PasswordForm;
use common\models\User;

/**
 * Site controller
 */
class DatabaseController extends Controller
{
    public function actionBackup() {
        $username   = 'root';
        $password   = '';
        $database   = 'indahmotor';
        $tables     = '';
        // $dump_name  = '/home/remorac/127.0.0.1/active/indahmotor72/backend/web/backup/indahmotor.sql';
        $dump_name  = 'E:\\indahmotor_backup_'.date('Y-m-d').'.sql';

        // exec("(/usr/bin/mysqldump -u$username -p$password $database $tables -r $dump_name) 2>&1", $output, $result);
        exec("(C:\\xampp\\mysql\\bin\\mysqldump -u$username -p$password $database $tables -r $dump_name) 2>&1", $output, $result);

        /* var_dump($result);
        echo "<br />";
        var_dump($output);
        echo "<br />"; */

        // $destination = Yii::getAlias('@web/backup/indahmotor.sql');
        // return exec('/usr/bin/mysqldump --user=root --password=toor --host=localhost indah_motor -r "'. $destination .'"');
        Yii::$app->getSession()->setFlash('success', 'Backup database selesai.');
        return $this->redirect(['/site/index']);
    }
}
