<?php

namespace app\modules\notification\models;

/**
 * This is the model class for table "user_notification".
 *
 * @property int $id ID
 * @property int $user_id User Link
 * @property string $title Заголовок уведомления
 * @property string $body Тело уведомления(контент)
 * @property string $content_link Текст ссылки(контент)
 * @property string $link Url ссылки
 * @property int $viewed Флаг просмотра
 */
class UserNotification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'title', 'body', 'content_link', 'link', 'type'], 'required'],
            [['user_id', 'viewed'], 'integer'],
            [['title', 'body', 'content_link', 'link', 'type'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User Link',
            'title' => 'Заголовок уведомления',
            'body' => 'Тело уведомления(контент)',
            'content_link' => 'Текст ссылки(контент)',
            'link' => 'Url ссылки',
            'viewed' => 'Флаг просмотра',
            'type' => 'Тип',
        ];
    }
}
