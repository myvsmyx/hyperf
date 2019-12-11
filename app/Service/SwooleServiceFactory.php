<?php
declare (strict_types=1);

namespace App\Service;

class SwooleServiceFactory
{
    public function __invoke( $config )
    {
        return make(SwooleService::class, compact( $config ) );
    }
}