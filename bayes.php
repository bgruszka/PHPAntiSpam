<?php

function graham($sh, $ih, $ts, $ti) {
	$p = ($sh/$ts)/(($sh/$ts) + ((2*$ih)/$ti));
	return $p;
}

function robinson($n, $graham) {
	$s = 1;
	$x = 0.5;
	$fw = ($s*$x + $n*$graham) / ($s + $n);

	return $fw;
}

$dirs = array('spam', 'nospam');

$corpus = array();
foreach($dirs as $dir) {
    $files = glob($dir.'/*.txt');

	if($dir == 'spam') {
		$spamMessagesCount = count($files);
	} else {
		$nospamMessagesCount = count($files);
	}

	foreach($files as $file) {
		$content = file_get_contents($file);
		$content = str_replace(
			array('.', ',', ';', '"', ':', '?', '!', '+', '-', '/', '*', '=', '<', '>', '|', '&', '~', '`', '@'),
			' ',
			$content
        );

		$words = explode(' ', $content);

		foreach($words as $key => $word) {
			$word = strtolower(trim($word));
			if(strlen($word) > 4) {
				if(!empty($word)) {
					if(in_array($word, array_keys($corpus))) {
						if($dir == 'spam') {
							$corpus[$word]['spam']++;
						} else {
							$corpus[$word]['nospam']++;
						}
					} else {
						if($dir == 'spam') {
							$corpus[$word] = array('spam' => 1, 'nospam' => 0);
						} else {
							$corpus[$word] = array('spam' => 0, 'nospam' => 1);
						}
					}
					$corpus[$word]['count']++;
				}
			}
		}
	}
}

$przydatnosciArray = array();
foreach($corpus as $word => $value) {
	$graham = graham($value['spam'], $value['nospam'], $spamMessagesCount, $nospamMessagesCount);
	$probability = robinson($value['spam']+$value['nospam'], $graham);
	$corpus[$word]['probability'] = $probability;
}

$wiadomosc = 'to jest spam'; //$_GET['message'];

if(strlen($wiadomosc) == 0) {
	die('Podaj tresc wiadomosci');
}

$words = explode(' ', $wiadomosc);

foreach($words as $word) {
	$word = trim($word);
	if(strlen($word) > 0) {
		if(!isset($corpus[$word])) {
			$probability = 0.5;
		} else {
			$probability = $corpus[$word]['probability'];
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

var_dump($result);

if($result < 0.9) {
	echo PHP_EOL . 'to nie jest spam' . PHP_EOL;
} else {
	echo PHP_EOL . 'to jest spam' . PHP_EOL;
}

?>
