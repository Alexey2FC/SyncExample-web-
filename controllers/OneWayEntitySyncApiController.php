<?php
namespace app\controllers;
use app\models\OneWayEntity;
use app\models\TwoWaysEntity;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use app\classes\RestApiController;

class OneWayEntitySyncApiController extends RestApiController
{
    public function actionGetEntities()
    {
        return OneWayEntity::find()->all();
    }

}
