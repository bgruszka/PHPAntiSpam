<?php

class Antispam 
{
	public static function graham($sh, $ih, $ts, $ti) 
	{
		$p = ($sh/$ts)/(($sh/$ts) + ((2*$ih)/$ti));
		return $p;
	}
	
	public static function robinson($n, $graham) 
	{
		$s = 1;
		$x = 0.5;
		$fw = ($s*$x + $n*$graham) / ($s + $n);
	
		return $fw;
	}
	
	public static function isSpam($text, Corpus $corpus)
	{
		$words = explode(' ', $text);
		
		foreach($words as $word) {
			$word = trim($word);
			if(strlen($word) > 0) {
				if(!isset($corpus->corpusList[$word])) {
					$probability = 0.5;
				} else {
					$probability = $corpus->corpusList[$word]['probability'];
				}
				
				$przydatnosc = abs(0.5 - $probability);
				$needed[$word]['probability'] = $probability;
				$needed[$word]['przydatnosc'] = $przydatnosc;
				$przydatnosciArray[] = $przydatnosc;
			}
		}
		
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
