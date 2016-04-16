# PHPAntiSpam

[![Build Status](https://travis-ci.org/bgruszka/PHPAntiSpam.svg?branch=master)](https://travis-ci.org/bgruszka/PHPAntiSpam)
[![Code Climate](https://codeclimate.com/github/bgruszka/PHPAntiSpam/badges/gpa.svg)](https://codeclimate.com/github/bgruszka/PHPAntiSpam)

## PHPAntiSpam is a library that recognize if documents / messages / texts are spam or not. The library use statistical analysis.

## Explanation in 4 steps:
* Create tokenizer
* Create corpus (with lexemes) from historical messages
* Choose method to use in classification
* Classify message

## Implemented methods:
* Paul Graham method
* Brian Burton method
* Robinson Geometric Mean Test method
* Fisher-Robinson's Inverse Chi-Square Test method