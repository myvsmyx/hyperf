<?php

declare(strict_types=1);

namespace App\Event;

/**
 * 用户修改相关的事件
 * Class UpdateUserLoginInfo
 * @package App\Event
 */
class UpdateUserLoginInfo
{

    public $updateInfo;

    public function __construct( $updateInfo )
    {
        $this->updateInfo = $updateInfo;
    }

}