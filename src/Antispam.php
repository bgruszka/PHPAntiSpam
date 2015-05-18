<?php

namespace PHPAntiSpam;

use PHPAntiSpam\DecisionMatrix\DecisionMatrix;
use PHPAntiSpam\Method\MethodInterface;

class AntiSpam
{
	const GRAHAM_METHOD = 1;
	const BURTON_METHOD = 2;
	const ROBINSON_GEOMETRIC_MEAN_TEST_METHOD = 3;
	const FISHER_ROBINSONS_INVERSE_CHI_SQUARE_METHOD = 4;
	
	const GRAHAM_WINDOW		= 15;
	const ROBINSON_WINDOW	= 15;
	const BURTON_WINDOW		= 27;
	
	protected $corpus;
	
	private $neutral = 0.5;

    /** @var  MethodInterface */
    protected $method;
	
	public function setMethod(MethodInterface $method)
	{
		$this->method = $method;
	}
	
	/**
	 * Calculate lexeme value with Gary Robinson method
	 * 
	 * @param int $wordOccurrences Number of occurrences in corpus (in spam and nospam)
	 * @param float $graham Word value calculated by Graham method
	 * 
	 * @return float
	 */
	public function robinson($wordOccurrences, $wordGrahamValue) 
	{
		$s = 1;
		$x = 0.5;
		
		$value = ($s * $x + $wordOccurrences * $wordGrahamValue) / ($s + $wordOccurrences);
		
		return $value;
	}
	
	/**
	 * Calculate bayes probability
	 * 
	 * @param array $lexemes
	 * 
	 * @return float
	 */
	public function bayes(array $lexemes)
	{
		$numerator = 1;
		$denominator = 1;
		foreach($lexemes as $lexeme) {
			$numerator *= $lexeme['probability'];
			$denominator *= 1 - $lexeme['probability'];
		}
		
		$result = $numerator / ($numerator + $denominator);
		
		return $result;
	}
	
	/**
	 * Ribinson's geometric mean test measures both the "spamminess" and "hamminess" of 
	 * the data in the decision matrix and also provides more granular results ranging 
	 * between 0 percen and 100 percent. Generally, a result of round 55 percent or 
	 * higher using Robinson's algorithm is an indicator of spam
	 * 
	 * @param array $lexemes
	 * 
	 * @return array
	 */
	public function robinson_geometric_mean_test(array $lexemes)
	{
		$spamminess = 1;
		$hamminess = 1;
		
		foreach($lexemes as $lexeme) {
			$spamminess *= (1 - $lexeme['probability']);
			$hamminess *= $lexeme['probability'];
		}
		
		$spamminess	= 1 - pow($spamminess, 1 / count($lexemes));
		$hamminess	= 1 - pow($hamminess, 1 / count($lexemes));
		$combined	= (1 + (($spamminess - $hamminess) / ($spamminess + $hamminess))) / 2;
		
		return array('spamminess' => $spamminess, 'hamminess' => $hamminess, 'combined' => $combined);
	}
	
	/**
	 * The inverse chi-square statistic
	 * 
	 * @param float $x
	 * @param int $v
	 * 
	 * @return float
	 */
	private function __chi2Q($x, $v)
	{
		$m = $x / 2;
		$s = exp(-$m);
		$t = $s;
		
		for($i = 1; $i < ($v/2); $i++) {
			$t *= $m / $i;
			$s += $t;
		}
		
		return ($s < 1) ? $s : 1;
	}
	
	/**
	 * Calculate probability used Fisher's chi-square distribution of combining 
	 * individual probabilities. The chi-square algorithm provides the added 
	 * benefit of being very sensitive to uncertainty. It produces granular 
	 * results similar to Robinson's geometric mean test, in which the result
	 * of calculation may fall within midrange of values to indicate a level 
	 * of uncertainty.
	 * @link http://www.linuxjournal.com/article/6467
	 * 
	 * @param array $lexemes
	 * 
	 * @return array
	 */
	public function fisher_robinsons_inverse_chi_square_test(array $lexemes)
	{
		$wordsProductProbability = 1;
		$wordsProductProbabilitySubstraction = 1;
		
		foreach($lexemes as $lexeme) {
			$wordsProductProbability *= $lexeme['probability'];
			$wordsProductProbabilitySubstraction *= 1- $lexeme['probability'];
		}		
		
		$hamminess = $this->__chi2Q(-2 * log($wordsProductProbability), 2 * count($lexemes));
		$spamminess = $this->__chi2Q(-2 * log($wordsProductProbabilitySubstraction), 2 * count($lexemes));
		
		$combined = (1 + $hamminess - $spamminess) / 2;
		
		return array('spamminess' => $spamminess, 'hamminess' => $hamminess, 'combined' => $combined);	
	}
	
	
	/**
	 * Add one word in decision matrix
	 * 
	 * @param array $decisionMatrix
	 * @param array $usefulnessArray
	 * @param string $word
	 * @param float $probability
	 */
	private function __add_one_word_in_matrix(array &$decisionMatrix, array &$usefulnessArray, $word, $probability)
	{
		// distance from neutral value
		$usefulness = abs($this->neutral - $probability);
		
		$decisionMatrix[$word]['probability'] = $probability;
		$decisionMatrix[$word]['usefulness'] = $usefulness;
		$usefulnessArray[$word] = $usefulness;
	}
	
	/**
	 * Add double word in decision matrix
	 * 
	 * @param array $decisionMatrix
	 * @param array $usefulnessArray
	 * @param string $word
	 * @param float $probability
	 */
	private function __add_double_word_in_matrix(array &$decisionMatrix, array &$usefulnessArray, $word, $probability)
	{
		for($i = 1; $i <= 2; $i++) {
			$wordForMatrix = $word . $i;
			$this->__add_one_word_in_matrix($decisionMatrix, $usefulnessArray, $wordForMatrix, $probability);
		}
	}
	
	/**
	 * Create decision matrix for all methods (except Fisher-Robinson method)
	 * 
	 * @param array $words
	 * 
	 * @return array
	 */
	private function __createDecisionMatrix(array $words)
	{
	}
	
	/**
	 * Create decision matrix used by Fisher-Robinson chi-square method.
	 * The chi-square algorithm's decision matrix is different from that
	 * of Bayesian combination in that it includes all tokens within a 
	 * specific range of probability (usually 0.0 through 0.1 and 0.9 
	 * through 1.0) and doesn't require sorting.
	 * 
	 * @param array $words
	 * 
	 * @return array
	 */
	private function __createFisherRobinsonDecisionMatrix(array $words)
	{
		$decisionMatrix = array();
		$processedWords	= array();	
		
		foreach($words as $word) {
			$word = trim($word);
			if(strlen($word) > 0 && !in_array($word, $processedWords)) {
				if(isset($this->corpus->lexemes[$word])) {
					$isInRanges = $this->corpus->lexemes[$word]['probability'] <= 0.1 || $this->corpus->lexemes[$word]['probability'] >= 0.9;
					if($isInRanges) {
						$decisionMatrix[$word]['probability'] = $this->corpus->lexemes[$word]['probability'];
						$processedWords[] = $word;
					}
				}
			}
		}
		
		return $decisionMatrix;
	}
	
	public function isSpam($text, $useBigrams = false)
	{


		/*if($this->method != self::FISHER_ROBINSONS_INVERSE_CHI_SQUARE_METHOD) {
			$decisionMatrix = $this->__createDecisionMatrix($words);
		} else {
			$decisionMatrix = $this->__createFisherRobinsonDecisionMatrix($words);
		}*/

		switch($this->method) {
			case self::GRAHAM_METHOD:
			case self::BURTON_METHOD:
				$result = $this->method->calculate();
                $result = $this->bayes($result);
				break;
			case self::ROBINSON_GEOMETRIC_MEAN_TEST_METHOD:
				//$result = $this->robinson_geometric_mean_test($decisionMatrix);
				break;
			case self::FISHER_ROBINSONS_INVERSE_CHI_SQUARE_METHOD:
				//$result = $this->fisher_robinsons_inverse_chi_square_test($decisionMatrix);
				break;
		}
			
		return $result;
	}
}

?>
