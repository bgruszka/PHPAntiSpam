<?php

class Antispam 
{
	protected $corpus;
	
	public function __construct(Corpus $corpus) {
		$this->corpus = $corpus;
	}
	
	public static function graham($sh, $ih, $ts, $ti) 
	{
		$p = ($sh/$ts)/(($sh/$ts) + ((2*$ih)/$ti));
		return $p;
	}
	
	public function robinson($n, $graham) 
	{
		$s = 1;
		$x = 0.5;
		$fw = ($s*$x + $n*$graham) / ($s + $n);
	
		return $fw;
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
