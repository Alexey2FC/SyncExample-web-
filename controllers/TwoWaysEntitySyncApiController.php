<?php
namespace app\controllers;
use app\models\TwoWaysEntity;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use app\classes\RestApiController;

class TwoWaysEntitySyncApiController extends RestApiController
{
    /**
     * @return ActiveQuery
     */
    private function getTwoWaysEntitiesOfCurrentUserQuery()
    {
        return TwoWaysEntity::find()->where(["user_id" => $this->user->id]);
    }


    public function actionGetEntities()
    {
        return $this->getTwoWaysEntitiesOfCurrentUserQuery()->all();
    }

    /**
     * Синхронизация сущностей в сторону клиент -> сервер
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionPushEntities()
    {
        $success = true;

        // Распознаем массив отправленных (pushed) Android-приложением сущностей из JSON
        $pushedEntities = json_decode($_POST["entities"], true);
        if ($pushedEntities === null) {
            throw new Exception(self::JSON_DECODE_FAIL);
        }

        // Подгружаем массив текущих (current) сущностей текущего пользователя
        /** @var $currenEntities \app\models\TwoWaysEntity[] */
        $currentEntities = $this->getTwoWaysEntitiesOfCurrentUserQuery()->indexBy("id")->all();

        // Проходимся по массиву отправленных желаний
        foreach ($pushedEntities as $pushedEntity) {

            $tempEntity = new TwoWaysEntity();
            $tempEntity->loadFromRemote($pushedEntity, $this->user->id); // помечаем модель как загруженную с андройд-устройства
            Yii::warning("Обрабатываем сущность с ID ".$tempEntity->id);
            Yii::warning("и last_time_update ".$tempEntity->last_time_update);

            // Если отправленная (pushed) сущность существует в массиве текущих (current) сущностей
            if (array_key_exists($tempEntity->id, $currentEntities)) {
                // и если обновления отправленной сущности БОЛЬШЕ времени обновления соответстующей текущей сущности
                if ($tempEntity->last_time_update > $currentEntities[$tempEntity->id]->last_time_update ) {
                    // то обновляем текущую сущность данными из отправленной сущности
                    $tempEntity = $currentEntities[$tempEntity->id]; // помечаем модель как загруженную с андройд-устройства
                    $tempEntity->loadFromRemote($pushedEntity, $this->user->id);
                    Yii::warning("Обновляем сущность ".$tempEntity->id." ".$tempEntity->last_time_update);
                    $success = $tempEntity->save() && $success;
                } else {
                    // иначе - ничего не делаем
                    Yii::warning("Сущность не изменилась ".$tempEntity->id);
                }
            } else {
                // Если отправленная (pushed) сущность НЕ существует в массиве текущих (current) сущностей - то добавляем ее
                Yii::warning("Сохраняем сущность ".$tempEntity->id);
                $success = $tempEntity->save() && $success;
            }

        }

        return $success ? self::DEFAULT_OK : self::DEFAULT_FAIL;
    }

}
