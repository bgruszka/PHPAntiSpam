<?php

namespace PHPAntiSpam\Tokenizer;

class RegexpTokenizer implements TokenizerInterface
{
    private $regexp;

    public function __construct($regexp)
    {
        $this->regexp = $regexp;
    }

    public function tokenize($text)
    {
        return preg_split($this->regexp, $text, -1, PREG_SPLIT_NO_EMPTY);
    }
}
