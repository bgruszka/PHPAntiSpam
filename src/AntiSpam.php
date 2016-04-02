<?php

namespace PHPAntiSpam;

use PHPAntiSpam\Method\MethodInterface;

class AntiSpam
{
    const ROBINSON_WINDOW = 15;
    const BURTON_WINDOW = 27;

    /** @var  MethodInterface */
    protected $method;

    public function setMethod(MethodInterface $method)
    {
        $this->method = $method;
    }

    public function isSpam($text)
    {
        return $this->method->calculate($text);
    }
}

?>
