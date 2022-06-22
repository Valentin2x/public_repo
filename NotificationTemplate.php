<?php

namespace app\modules\notification\models;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * Шаблон уведомления.
 *
 * Позволяет создать уведомление подставив значения параметров в шаблон
 *
 * @property string $templateTitle Шаблон заголовка
 * @property string $templateBody Шаблон тела
 * @property string $templateLink Шаблон ссылки
 * @property array $params[] Набор параметров шаблона $k => $v
 *
 * @method Notification createNotification() Создаёт уведомление
 * @method static Notification getNotification(string $tempalteClass, array $templateParams, array $constructParams = []) Создаёт уведомление
 */
abstract class NotificationTemplate extends BaseObject
{
    const ADD_CABINET_TYPE = 'addCabinet';
    const ADD_FLOOR_TYPE = 'addFloor';
    const OPEN_BUILDING_TYPE = 'openBuilding';
    const OPEN_GAME_TYPE = 'openGame';
    const ANNOUNCEMENT_TYPE = 'announcement';

    const CLASS_TYPES = [
        self::ADD_CABINET_TYPE => AddCabinetTemplateNotification::class,
        self::ADD_FLOOR_TYPE => AddFloorTemplateNotification::class,
        self::OPEN_BUILDING_TYPE => OpenBuildingTemplateNotification::class,
        self::OPEN_GAME_TYPE => OpenGameTemplateNotification::class,
        self::ANNOUNCEMENT_TYPE => AnnouncementTemplateNotification::class,
    ];

    public $templateTitle;
    public $templateBody;
    public $templateLink;

    private $params = [];

    /**
     * Возвращает имя класса.
     *
     * @param string $type Тип шаблона уведомления
     */
    public static function getClassNameByType(string $type)
    {
        if (isset(self::CLASS_TYPES[$type])) {
            return self::CLASS_TYPES[$type];
        }

        throw new InvalidConfigException('Указанные тип не существует!');
    }

    /**
     * Возвращает уведомление.
     *
     * Создаёт уведомление из шаблона
     *
     * @param string $templateClass Имя класса шаблона
     * @param array $templateParams Параметры шаблона
     * @param array $constructParams Параметры конструктора класса шаблона
     *
     * @throws InvalidConfigException
     *
     * @return Notification
     */
    public static function getNotification(string $templateClass, array $templateParams, array $constructParams = [])
    {
        $template = \Yii::createObject(
            ArrayHelper::merge(['class' => $templateClass], $constructParams)
        );

        foreach ($templateParams as $k => $v) {
            $template->setParam($k, $v);
        }

        return $template->createNotification();
    }

    public static function getNotificationByTemplateType(string $temaplateType, array $templateParams, array $constructParams = [])
    {
        $class = self::getClassNameByType($temaplateType);

        return self::getNotification($class, $templateParams, $constructParams);
    }

    /**
     * Геттер параметров шаблона.
     *
     * @param null|mixed $k
     */
    public function getParams($k = null)
    {
        if (is_null($k)) {
            return $this->params;
        }

        return $this->params[$k];
    }

    /**
     * Сеттер параметров шаблона.
     *
     * @param mixed $k
     * @param mixed $v
     */
    public function setParam($k, $v)
    {
        $templateParams = $this->getTemplateParams();
        if (!in_array($k, $templateParams)) {
            throw new InvalidConfigException("Недопустимый параметр шаблона {$k}!");
        }

        if (is_null($v)) {
            throw new InvalidConfigException("Недопустимое значение параметра {$k}!");
        }

        $this->params[$k] = $v;

        return $this;
    }

    /**
     * Проверяет, заполнены ли все параметры шаблона.
     */
    public function isFillParams()
    {
        $tParams = static::getTemplateParams();
        $keys = array_keys($this->getParams());

        $diff = array_diff($tParams, $keys);

        return empty($diff);
    }

    /**
     * Создаёт уведомление, подставляя параметры в шаблон.
     */
    abstract public function createNotification(): Notification;

    /**
     * Возвращает набор ключей-парамеров шаблона.
     */
    abstract protected static function getTemplateParams(): array;
}
