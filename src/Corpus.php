<?php

namespace PHPAntiSpam;

use PHPAntiSpam\Tokenizer\TokenizerInterface;

class Corpus
{

    protected $messages = [];
    protected $tokenizer;
    public $lexemes = [];
    public $messagesCount = ['spam' => 0, 'nospam' => 0];

    public function __construct($messages, TokenizerInterface $tokenizer)
    {
        $this->messages = $messages;
        $this->tokenizer = $tokenizer;

        // next
        foreach ($this->messages as $message) {
            $this->messagesCount[$message['category']]++;

            $words = $tokenizer->tokenize($message['content']);

            foreach ($words as $key => $word) {
                $word = $this->normalizeWord($word);

                if (strlen($word) > 4) {
                    if (isset($this->lexemes[$word])) {
                        $this->lexemes[$word][$message['category']]++;
                    } else {
                        if ($message['category'] == 'spam') {
                            $this->lexemes[$word] = ['spam' => 1, 'nospam' => 0];
                        } else {
                            $this->lexemes[$word] = ['spam' => 0, 'nospam' => 1];
                        }
                    }
                }
            }
        }
    }

    public function getTokenizer()
    {
        return $this->tokenizer;
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
