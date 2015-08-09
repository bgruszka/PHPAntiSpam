<?php 

require_once 'vendor/autoload.php';

use PHPAntiSpam\Corpus;
use PHPAntiSpam\AntiSpam;

function getMessages() {
	$dirs = array('spam', 'nospam');
	
	$messages = array();
	
	foreach($dirs as $dir) {
	    $files = glob($dir.'/*.txt');
	
	    foreach($files as $file) {
	    	$message = array();
	    	
			$content = file_get_contents($file);
	
	        $message['content'] = $content;
	        $message['category'] = $dir;
	        
	        $messages[] = $message;
	    }
	}
	
	return $messages;
}

$messages = getMessages();
$separators = '/[-, ]/';

$corpus = new Corpus($messages, $separators);
$antispam = new Antispam($corpus);
$antispam->setMethod(new \PHPAntiSpam\Method\GrahamMethod($corpus));

$message = 'This promotion is sponsored exclusively by Vindale Research';

$spamProbability = $antispam->isSpam($message);

var_dump($spamProbability);

if($spamProbability < 0.9) {
	echo PHP_EOL . 'no spam' . PHP_EOL;
} else {
	echo PHP_EOL . 'spam' . PHP_EOL;
}

?>