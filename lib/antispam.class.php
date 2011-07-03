<?php

class Antispam 
{
	protected $corpus;
	
	public function __construct(Corpus $corpus) {
		$this->corpus = $corpus;
	}
	
	/**
	 * Calculate lexem value with Paul Graham method
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
	 * Calculate lexem value with Gary Robinson method
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
	
	public function isSpam($text)
	{	
		// next
		$przydatnosciArray = array();
		foreach($this->corpus->corpusList as $word => $value) {
			$graham = $this->graham(
				$value['spam'], 
				$value['nospam'], 
				$this->corpus->messagesCount['spam'], 
				$this->corpus->messagesCount['nospam']
			);
			
			$probability = $this->robinson($value['spam'] + $value['nospam'], $graham);
			$this->corpus->corpusList[$word]['probability'] = $probability;
		}
		
		// next
		$words = explode(' ', $text);
		
		foreach($words as $word) {
			$word = trim($word);
			if(strlen($word) > 0) {
				if(!isset($this->corpus->corpusList[$word])) {
					$probability = 0.5;
				} else {
					$probability = $this->corpus->corpusList[$word]['probability'];
				}
				
				$przydatnosc = abs(0.5 - $probability);
				$needed[$word]['probability'] = $probability;
				$needed[$word]['przydatnosc'] = $przydatnosc;
				$przydatnosciArray[] = $przydatnosc;
			}
		}
		
		// next
		
		array_multisort($przydatnosciArray, SORT_DESC, $needed);
		
		$needed = array_slice($needed, 0, 15);
		
		$licznik = 1;
		$mianownik = 1;
		foreach($needed as $word) {
			$licznik *= $word['probability'];
			$mianownik *= 1 - $word['probability'];
		}
		
		$result = $licznik / ($licznik + $mianownik);
		
		return $result;
	}
}

?>
