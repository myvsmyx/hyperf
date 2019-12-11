<?php
declare (strict_types=1);

namespace App\Service;

interface UserServiceInterface
{
    /**
     * 创建新用户
     * @param array $param
     * @return mixed
     */
    public function createUser( array $param );
}