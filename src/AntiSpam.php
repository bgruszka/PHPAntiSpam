<?php

namespace PHPAntiSpam;

use PHPAntiSpam\Method\MethodInterface;

class AntiSpam
{
    const GRAHAM_METHOD = 1;
    const BURTON_METHOD = 2;
    const ROBINSON_GEOMETRIC_MEAN_TEST_METHOD = 3;
    const FISHER_ROBINSONS_INVERSE_CHI_SQUARE_METHOD = 4;

    const ROBINSON_WINDOW = 15;
    const BURTON_WINDOW = 27;

    /** @var  MethodInterface */
    protected $method;

    public function setMethod(MethodInterface $method)
    {
        $this->method = $method;
    }

    public function isSpam($text, $useBigrams = false)
    {
        /*if($this->method != self::FISHER_ROBINSONS_INVERSE_CHI_SQUARE_METHOD) {
            $decisionMatrix = $this->__createDecisionMatrix($words);
        } else {
            $decisionMatrix = $this->__createFisherRobinsonDecisionMatrix($words);
        }*/

        $result = $this->method->calculate($text);

        /*switch($this->method) {
            case self::GRAHAM_METHOD:
            case self::BURTON_METHOD:
                $result = $this->method->calculate();
                var_dump($result); die;
                $result = $this->bayes($result);
                break;
            case self::ROBINSON_GEOMETRIC_MEAN_TEST_METHOD:
                //$result = $this->robinson_geometric_mean_test($decisionMatrix);
                break;
            case self::FISHER_ROBINSONS_INVERSE_CHI_SQUARE_METHOD:
                //$result = $this->fisher_robinsons_inverse_chi_square_test($decisionMatrix);
                break;
        }*/

        return $result;
    }
}

?>
