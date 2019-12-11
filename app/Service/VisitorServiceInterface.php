<?php
declare (strict_types=1);

namespace App\Service;

interface VisitorServiceInterface
{
    public function getUidBySuid( string $suid );

    public function recordUidSuid ( int $uid, string $suid );
}