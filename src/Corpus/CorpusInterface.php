<?php

namespace PHPAntiSpam\Corpus;


interface CorpusInterface {
    public function getTokenizer();
    public function getLexemesForGivenWords(array $words);
    public function setLexemes(array $lexemes);
    public function getLexemes();
    public function getMessagesCount();
}