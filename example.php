<?php 

require_once 'vendor/autoload.php';

use PHPAntiSpam\Corpus;
use PHPAntiSpam\AntiSpam;
use PHPAntiSpam\Tokenizer\WhitespaceTokenizer;

$messages = [
    ['category' => 'spam', 'content' => 'this is spam'],
    ['category' => 'nospam', 'content' => 'this is'],
];

$tokenizer = new WhitespaceTokenizer();
$corpus = new Corpus($messages, $tokenizer);

$antispam = new AntiSpam($corpus);
$antispam->setMethod(new \PHPAntiSpam\Method\GrahamMethod($corpus));

$spamProbability = $antispam->isSpam('This is spam');

echo sprintf('Spam probability: %s', $spamProbability) . PHP_EOL;
echo sprintf('Is spam: %s', $spamProbability < 0.9 ? 'NO' : 'YES') . PHP_EOL;