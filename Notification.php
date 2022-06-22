<?php

namespace app\modules\notification\models;

use yii\base\BaseObject;

class Notification extends BaseObject
{
    public $title;
    public $body;
    public $link;
    public $contentLink;
    public $type;
}
