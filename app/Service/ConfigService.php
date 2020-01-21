<?php

declare (strict_types=1);

namespace App\Service;

use Hyperf\Contract\ConfigInterface;

class ConfigService implements ConfigInterface
{

    public function set(string $key, $value)
    {

    }

    /**
     * 获取配置
     * @param string $key
     * @param null $default
     * @return mixed|void
     */
    public function get(string $key, $default = null)
    {

    }

    public function has(string $keys)
    {
        // TODO: Implement has() method.
    }

}