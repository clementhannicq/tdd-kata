<?php
/**
* Class that perform operations on numbers
*/

function cleanDelimiter($input)
{
	if(strlen($input) > 1){
		return preg_quote(substr($input, 1, strlen($input) - 2));
	} else {
		return preg_quote($input);
	}
}

class Calculator
{
	private $logger;
	private $webservice;

	function __construct(Logger $logger = null, Webservice $webservice = null)
	{
		$logger ? $this->logger = $logger : $this->logger = new Logger();
		$webservice ? $this->webservice = $webservice : $this->webservice = new Webservice();
	}

	private function analyse($input){
		$custom = "#^//(.+)\n(.*)$#";
		$separator = "#([^\[]|\[[^\]]+\])#";
		if(preg_match($custom, $input, $match)) {
			preg_match_all($separator, $match[1], $customDelimiters);
			return Array(
				"content" => $match[2],
				"delimiters" => $customDelimiters[0]
				);
		} else {
			return Array(
				"content" => $input,
				"delimiters" => Array(",", "\n")
				);
		}
	}

	function add($string)
	{
		$sum = 0;
		$input = $this->analyse($string);
		$regex = "#(".implode("|", array_map("cleanDelimiter", $input['delimiters'])).")#";
		$tokens = preg_split($regex, $input['content']);
		$negatives = Array();

		for($i = 0; $i < count($tokens); $i++) {
			$tok = $tokens[$i];
			if($tok < 0) {
				$negatives[] = $tok;
			} else if($tok > 1000) {
				$tok = 0;
			}

			$sum += $tok;
		}

		if(count($negatives) > 0) {
			throw new NegativeNumberException("Negative numbers : " + implode(",",$negatives));
		}
		try {
			$this->logger->log($sum);
		} catch(Exception $e) {
			$this->webservice->notify();
		}
		

		return $sum;
	}
}

class NegativeNumberException extends Exception
{}

class Logger
{
	public function log($input){}
}

class Webservice
{
	public function notify(){}
}