<?php

namespace PHPAntiSpam\Corpus;

use PHPAntiSpam\Tokenizer\TokenizerInterface;

class ArrayCorpus implements CorpusInterface
{
    protected $lexemes = [];
    public $messagesCount = ['spam' => 0, 'nospam' => 0];

    public function __construct(array $lexemes = null, array $messagesCount = null)
    {
        if (!is_null($lexemes)) {
            $this->lexemes = $lexemes;
        }

        if (!is_null($messagesCount)) {
            $this->messagesCount = $messagesCount;
        }
    }

    public static function create($messages, TokenizerInterface $tokenizer, $options = [])
    {
        $corpus = new self();

        // next
        foreach ($messages as $message) {
            $corpus->messagesCount[$message['category']]++;

            $words = $tokenizer->tokenize($message['content']);

            foreach ($words as $word) {
                $word = $corpus->normalizeWord($word);

                if (isset($options['min_word_length']) && strlen($word) < $options['min_word_length']) {
                    continue;
                }

                $corpus->updateLexem($word, $message['category']);
            }
        }

        return $corpus;
    }

    public function updateLexem($word, $category)
    {
        if (!isset($this->lexemes[$word])) {
            $this->lexemes[$word] = ['spam' => 0, 'nospam' => 0];
        }

        $this->lexemes[$word][$category]++;
    }

    public function getMessagesCount()
    {
        return $this->messagesCount;
    }

    public function getLexemes()
    {
        return $this->lexemes;
    }

    public function getLexemesForGivenWords(array $words)
    {
        $lexemes = [];

        foreach($words as $word) {
            if (!isset($this->lexemes[$word])) {
                continue;
            }

            $lexemes[$word] = $this->lexemes[$word];
        }

        return $lexemes;
    }

    public function setLexemes(array $lexemes)
    {
        $this->lexemes = $lexemes;
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

    public function getDataForSerialization()
    {

    }

    public function __sleep()
    {
        return ['lexemes', 'messagesCount'];
    }

    public function writeToFile($filename)
    {
        $serialized = serialize($this);

        file_put_contents($filename, $serialized, LOCK_EX);
    }

    public static function readFromFile($filename)
    {
        $serialized = file_get_contents($filename);

        return unserialize($serialized);
    }
}

?>
