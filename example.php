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
$antispam = new Antispam($corpus);

$message = 'This promotion is sponsored exclusively by Vindale Research';

$spamProbability = $antispam->isSpam($message);

var_dump($spamProbability);

if($spamProbability < 0.9) {
	echo PHP_EOL . 'to nie jest spam' . PHP_EOL;
} else {
	echo PHP_EOL . 'to jest spam' . PHP_EOL;
}

?>