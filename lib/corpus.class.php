<?php

class Corpus
{
	
	protected $messages = array();
	public $corpusList = array();
	public $messagesCount = array('spam' => 0, 'nospam' => 0);
	
	public function __construct($messages)
	{
		$this->messages = $messages;
		
		// next
		foreach($this->messages as $message) {
			$this->messagesCount[$message['category']]++;
			
			$message['content'] = str_replace(
				array(
					'.', ',', ';', '"', ':', '?', 
					'!', '+', '-', '/', '*', '=', 
					'<', '>', '|', '&', '~', '`', '@'
				), 
				' ',
				$message['content']
		    );
		    
			$words = explode(' ', $message['content']);
		
			foreach($words as $key => $word) {
				$word = strtolower(trim($word));
				if(strlen($word) > 4) {
					if(!empty($word)) {
						if(in_array($word, array_keys($this->corpusList))) {
								$this->corpusList[$word][$message['category']]++;
						} else {
							if($message['category'] == 'spam') {
								$this->corpusList[$word] = array('spam' => 1, 'nospam' => 0);
							} else {
								$this->corpusList[$word] = array('spam' => 0, 'nospam' => 1);
							}
						}
					}
				}
			}
		}	
	}
	
}

?>
