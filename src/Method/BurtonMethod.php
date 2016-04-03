<?php

namespace PHPAntiSpam\Method;

use PHPAntiSpam\DecisionMatrix\DefaultDecisionMatrix;

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
        $this->setLexemesProbability();

        return $this->bayes($this->decisionMatrix->getMostImportantLexemes(true));
    }

    protected function setDecisionMatrix($text)
    {
        $this->decisionMatrix = new DefaultDecisionMatrix(
            $this->getWordsFromText($text),
            $this->corpus,
            self::WINDOW_SIZE
        );
        $this->decisionMatrix->setDoubleWords(true);
    }
}