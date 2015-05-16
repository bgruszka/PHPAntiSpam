<?php

namespace PHPAntiSpam;

class Corpus
{
	
	protected $messages = array();
	public $separators = null;
	public $lexemes = array();
	public $messagesCount = array('spam' => 0, 'nospam' => 0);
	
	public function __construct($messages, $separators, $useBigrams = false)
	{
		$this->messages = $messages;
		$this->separators = $separators;
		
		// next
		foreach($this->messages as $message) {
			$this->messagesCount[$message['category']]++;

            $words = preg_split($this->separators, $message['content'], null, PREG_SPLIT_NO_EMPTY);

            if($useBigrams) {
                $bigrams = array();

                for($i = 0; $i < count($words) - 1; $i++) {
                    $bigrams[] = $words[$i].' '.$words[$i+1];
                }

                $words = $bigrams;
            }

			foreach($words as $key => $word) {
				$word = $this->normalizeWord($word);
				if(strlen($word) > 4) {
                    if(isset($this->lexemes[$word])) {
                        $this->lexemes[$word][$message['category']]++;
                    } else {
                        if($message['category'] == 'spam') {
                            $this->lexemes[$word] = array('spam' => 1, 'nospam' => 0);
                        } else {
                            $this->lexemes[$word] = array('spam' => 0, 'nospam' => 1);
                        }
                    }
                }
            }
        }
    }
	
	/**
	 * Normalize word
	 * 
	 * @param string $word
	 * @return string
	 */
	private function normalizeWord($word)
	{
		return strtolower(trim($word));
	}
	
}

?>
