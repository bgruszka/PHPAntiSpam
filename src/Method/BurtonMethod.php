<?php

namespace PHPAntiSpam\Method;

use PHPAntiSpam\DecisionMatrix\DefaultDecisionMatrix;
use PHPAntiSpam\Math;
use PHPAntiSpam\Corpus;

/**
 * Class BurtonMethod
 * @package PHPAntiSpam\Method
 */
class BurtonMethod extends Method implements MethodInterface
{
    const WINDOW_SIZE = 27;

    public function calculate($text)
    {
        $this->setDecisionMatrix($text);

        return $this->bayes($this->decisionMatrix->getMostImportantLexemes());
    }

    private function setDecisionMatrix($text)
    {
        $this->decisionMatrix = new DefaultDecisionMatrix(
            $this->getWordsFromText($text),
            $this->corpus,
            self::WINDOW_SIZE
        );
    }
}