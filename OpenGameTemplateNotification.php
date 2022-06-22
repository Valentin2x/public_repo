<?php

namespace app\modules\notification\models;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class OpenGameTemplateNotification extends NotificationTemplate
{
    public function createNotification(): Notification
    {
        if ($this->isFillParams()) {
            $gameName = $this->getParams('gameName');
            $cabinetId = $this->getParams('cabinetId');

            $title = \Yii::t('app', 'Привет!');
            $body = \Yii::t(
                'app',
                'Игра {gameName} доступна.<br/>Сможете повторить уникальную формулу средства марки?',
                compact('gameName')
            );
            $contentLink = \Yii::t('app', 'Играть');
            $link = Url::to(['cabinet', 'id' => $cabinetId]);
            $type = NotificationTemplate::OPEN_GAME_TYPE;

            return \Yii::createObject(
                ArrayHelper::merge(
                    ['class' => Notification::class],
                    compact('title', 'body', 'contentLink', 'link', 'type')
                )
            );
        }

        throw new InvalidConfigException('Параметры шаблона не заполнены');
    }

    protected static function getTemplateParams(): array
    {
        return [
            'gameName',
            'cabinetId',
        ];
    }
}
