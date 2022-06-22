<?php

namespace app\modules\notification\services;

use app\modules\notification\models\Notification;
use app\modules\notification\models\NotificationTemplate;
use app\modules\notification\models\UserNotification;
use convergent\yii2\smartcontent\components\SmartModel;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\base\StaticInstanceInterface;
use yii\base\StaticInstanceTrait;

class UserNotificationService extends BaseObject implements StaticInstanceInterface
{
    use StaticInstanceTrait;

    /**
     * Сохраняет уведомление для пользователя.
     *
     * @param mixed $userId
     *
     * @throws \yii\base\InvalidConfigException
     *
     * @return UserNotification
     */
    public function registerUserNotificationByTempalte(NotificationTemplate $notificationTemplate, $userId)
    {
        $notification = $notificationTemplate->createNotification();

        return $this->registerUserNotification($notification, $userId);
    }

    public function registerUserNotification(Notification $notification, $userId)
    {
        $userNotification = new UserNotification([
            'user_id' => $userId,
            'title' => $notification->title,
            'body' => $notification->body,
            'content_link' => $notification->contentLink,
            'link' => $notification->link,
            'type' => $notification->type,
        ]);

        $userNotification->save();

        return $userNotification;
    }

    public function getSmartContentNotification(string $notificationTemplateType, SmartModel $smartModel)
    {
        $notificationParams = $this->getNotificationTemplateParams($notificationTemplateType, $smartModel);

        return $this->createNotification($notificationTemplateType, $notificationParams);
    }

    public function getNotificationTemplateParams(string $notificationTemplateType, SmartModel $smartModel)
    {
        switch ($notificationTemplateType) {
            case NotificationTemplate::ADD_CABINET_TYPE:
                $this->checkSmartModelName('cabinet', $smartModel);
                $service = \app\modules\homepage\services\SmartContentService::instance();
                $smartModelBuilding = $service->findModel('building', ['uuid' => $smartModel->building]);
                $buildingName = trim((strip_tags($smartModelBuilding->name)));

                return [
                    'buildingName' => $buildingName,
                    'cabinetId' => $smartModel->id,
                ];

            break;

            case NotificationTemplate::ADD_FLOOR_TYPE:
                $this->checkSmartModelName('floor', $smartModel);
                $service = \app\modules\homepage\services\SmartContentService::instance();
                $smartModelBuilding = $service->findModel('building', ['uuid' => $smartModel->building]);
                $buildingName = trim((strip_tags($smartModelBuilding->name)));

                return [
                    'buildingName' => $buildingName,
                    'floorId' => $smartModel->id,
                ];

            break;

            case NotificationTemplate::OPEN_BUILDING_TYPE:
                $this->checkSmartModelName('building', $smartModel);

                return [
                    'buildingName' => $smartModel->name,
                    'buildingId' => $smartModel->id,
                ];

            break;
        }

        throw new InvalidConfigException('Указанный тип шаблона отсутствует!');
    }

    public function createNotification(string $notificationTemplateType, array $templateParams, array $constructParams = [])
    {
        return NotificationTemplate::getNotificationByTemplateType($notificationTemplateType, $templateParams, $constructParams);
    }

    public function getUserNotifications($userId)
    {
        return UserNotificationEntityManger::getActiveRecordsBy(['user_id' => $userId]);
    }

    private function checkSmartModelName($modelName, SmartModel $model)
    {
        if ($modelName != $model->getModel()->name) {
            throw new InvalidConfigException('Недопустимая модель смарт-контента');
        }
    }
}
