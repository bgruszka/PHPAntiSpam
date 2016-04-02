<?php 

require_once 'vendor/autoload.php';

use PHPAntiSpam\Corpus;
use PHPAntiSpam\AntiSpam;
use PHPAntiSpam\Tokenizer\WhitespaceTokenizer;

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
$tokenizer = new WhitespaceTokenizer();
$corpus = new Corpus($messages, $tokenizer);
$antispam = new AntiSpam($corpus);
$antispam->setMethod(new \PHPAntiSpam\Method\BurtonMethod($corpus));

$message = 'This promotion is sponsored exclusively by Vindale Research';

$spamProbability = $antispam->isSpam($message);

var_dump($spamProbability);

if($spamProbability < 0.9) {
	echo PHP_EOL . 'no spam' . PHP_EOL;
} else {
	echo PHP_EOL . 'spam' . PHP_EOL;
}

?>