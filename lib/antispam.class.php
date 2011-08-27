<?php

class Antispam 
{
	const GRAHAM_METHOD = 1;
	const BURTON_METHOD = 2;
	
	const GRAHAM_WINDOW = 15;
	const BURTON_WINDOW = 27;
	
	protected $corpus, $method;
	
	private $neutral = 0.5;
	
	public function __construct(Corpus $corpus) 
	{
		$this->corpus = $corpus;
	}
	
	/**
	 * Set window size of decision matrix
	 * 
	 * @param int $windowSize
	 */
	private function __setWindow($windowSize)
	{
		$this->window = $windowSize;
	}
	
	public function setMethod($method)
	{
		$this->method = $method;
		
		switch($this->method) {
			case self::GRAHAM_METHOD:
				$this->__setWindow(self::GRAHAM_WINDOW);
				break;
			case self::BURTON_METHOD:
				$this->__setWindow(self::BURTON_WINDOW);
				break;
		}
	}
	
	/**
	 * Calculate lexeme value with Paul Graham method. 
	 * To prevent over-interpreting the messages as spam, 
	 * the number of instances of innocent lexemes is 
	 * multiplied by 2.
	 * @link http://www.paulgraham.com/spam.html
	 * 
	 * @param int $wordSpamCount
	 * @param int $wordNoSpamCount
	 * @param int $spamMessagesCount
	 * @param int $noSpamMessagesCount
	 * 
	 * @return float
	 */
	public static function graham($wordSpamCount, $wordNoSpamCount, $spamMessagesCount, $noSpamMessagesCount) 
	{
		$value = ($wordSpamCount / $spamMessagesCount) / (($wordSpamCount/$spamMessagesCount) + ((2 * $wordNoSpamCount) / $noSpamMessagesCount));
		
		return $value;
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
		$usefulnessArray[] = $usefulness;
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
	 * Create decision matrix
	 * 
	 * @param array $words
	 * 
	 * @return array
	 */
	public function createDecisionMatrix(array $words)
	{
		$usefulnessArray	= array();
		$decisionMatrix		= array();
		$processedWords		= array();	
		$wordOcurrencies	= array_count_values($words);
		
		foreach($words as $word) {
			$word = trim($word);
			if(strlen($word) > 0 && !in_array($word, $processedWords)) {
				// first occurence of lexeme (unit lexeme)
				if(!isset($this->corpus->lexemes[$word])) {
					// set default / neutral lexeme probability
					$probability = $this->neutral;
				} else {
					$probability = $this->corpus->lexemes[$word]['probability'];
				}
				
				if($this->method == self::BURTON_METHOD && $wordOcurrencies[$word] > 1) {
					$this->__add_double_word_in_matrix($decisionMatrix, $usefulnessArray, $word, $probability);
				} else {
					$this->__add_one_word_in_matrix($decisionMatrix, $usefulnessArray, $word, $probability);
				}
				
				$processedWords[] = $word;
			}
		}
		
		// sort by usefulness
		array_multisort($usefulnessArray, SORT_DESC, $decisionMatrix);
		
		return $decisionMatrix;
	}
	
	public function isSpam($text)
	{	
		foreach($this->corpus->lexemes as $word => $value) {
			$graham = $this->graham(
				$value['spam'], 
				$value['nospam'], 
				$this->corpus->messagesCount['spam'], 
				$this->corpus->messagesCount['nospam']
			);
			
			$probability = $this->robinson($value['spam'] + $value['nospam'], $graham);
			$this->corpus->lexemes[$word]['probability'] = $probability;
		}
		
		$words = preg_split($this->corpus->separators, $text);
		
		$decisionMatrix = $this->createDecisionMatrix($words);
		
		$mostImportantLexemes = array_slice($decisionMatrix, 0, $this->window);

		$result = $this->bayes($mostImportantLexemes);
		
		return $result;
	}
}

?>
