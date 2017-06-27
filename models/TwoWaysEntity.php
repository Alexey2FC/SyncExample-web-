<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "two_ways_entity".
 *
 * @property integer $server_id
 * @property integer $id
 * @property string $name
 * @property string $last_time_update
 * @property integer $user_id
 */
class TwoWaysEntity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'two_ways_entity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'user_id'], 'required'],
            [['id', 'last_time_update', 'user_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'server_id' => 'Server ID',
            'id' => 'ID',
            'name' => 'Name',
            'last_time_update' => 'Last Time Update',
            'user_id' => 'User ID',
        ];
    }



    public function beforeSave($insert)
    {
        if (!$this->isRemoteLoaded) { // если модель загружена НЕ ИЗ Android-приложения - то меняем таймстамп последнего обновления на текущее время
            $this->last_time_update = time();
            if ($insert) { $this->id = $this->last_time_update; } // если к тому же это еще и INSERT - то генерим айдишник сущности на сервере
        }
        return parent::beforeSave($insert);
    }

    /**
     * Загрузить модель из массива данных, которые приходят из Android-приложения
     * @param $row
     * @return bool
     */
    public function loadFromRemote($row, $user_id)
    {
        $this->isRemoteLoaded = true;
        $this->user_id = $user_id;
        $this->setAttributes($row, false);
        return true;
    }

    private $isRemoteLoaded = false;
}
