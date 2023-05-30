<?php

namespace Asfop\HasOne;

use Asfop\HasOne\attribute\Config;
use Asfop\HasOne\attribute\Info;
use Asfop\HasOne\attribute\Info2;
use Asfop\HasOne\contract\UserInterface;
use InvalidArgumentException;

class Factory
{
    /**
     * @param $attr
     * @return UserInterface
     */
    public static function analysis($attr)
    {
        /**
         * @var UserInterface $class
         */
        $class = Config::getConfigs()[$attr] ?? null;

        if (is_null($class)) {
            throw new InvalidArgumentException(
                "Authentication user provider [{$attr}] is not defined."
            );
        }
        return new $class;
    }
}
