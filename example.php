<?php 

require_once 'lib/antispam.class.php';
require_once 'lib/corpus.class.php';

$dirs = array('spam', 'nospam');

$messages = array();

foreach($dirs as $dir) {
    $files = glob($dir.'/*.txt');

    foreach($files as $file) {
    	$message = array();
    	
		$content = file_get_contents($file);
        $content = str_replace(
            array('.', ',', ';', '"', ':', '?', '!', '+', '-', '/', '*', '=', '<', '>', '|', '&', '~', '`', '@'),
            ' ',
            $content
        );

        $message['content'] = $content;
        $message['category'] = $dir;
        
        $messages[] = $message;
    }
}

$corpus = new Corpus($messages);

$przydatnosciArray = array();
foreach($corpus->corpusList as $word => $value) {
	$graham = Antispam::graham(
		$value['spam'], 
		$value['nospam'], 
		$corpus->messagesCount['spam'], 
		$corpus->messagesCount['nospam']
	);
	
	$probability = Antispam::robinson($value['spam'] + $value['nospam'], $graham);
	$corpus->corpusList[$word]['probability'] = $probability;
}

//$wiadomosc = 'to jest spam';
$wiadomosc = 'This promotion is sponsored exclusively by Vindale Research';

var_dump(Antispam::isSpam($wiadomosc, $corpus));

if(Antispam::isSpam($wiadomosc, $corpus) < 0.9) {
	echo PHP_EOL . 'to nie jest spam' . PHP_EOL;
} else {
	echo PHP_EOL . 'to jest spam' . PHP_EOL;
}

?>