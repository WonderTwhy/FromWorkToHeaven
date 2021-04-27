<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\services_groups;
use app\models\services;

class ServicesController extends Controller
{
    public function actionIndex()
    {
        $services_groups = services_groups::find()->all();
     
    
        $services = services::find()->all();
        return $this->render('index', compact("services", "services_groups"));
}

public function actionQuest1()
    {
        $curr = (new \yii\db\Query())
        ->select(['priceService'])
        ->from('services')
        ->limit(10)
        ->all();

        $servicesGroupsTableArray = (new \yii\db\Query())
        ->select(['id' ,'nameServiceGroup'])
        ->from('services_groups')
        ->limit(10)
        ->all();
        

        $servicesTableArray = (new \yii\db\Query())
        ->select(['id' ,'nameService', 'priceService', 'serviceInfo', 'idServiceGroup'])
        ->from('services')
        ->limit(10)
        ->all();    
        return $this->renderPartial('quest1', compact("servicesTableArray", "servicesGroupsTableArray", "curr"));

}


}



/*public function actionGrid(){

    return $this -> render('index') ;
        
    }

?>*/