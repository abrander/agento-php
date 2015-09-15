<?php

namespace Agento;

class Message {
	protected $identifier;
	protected $probability;

	public function __construct($identifier, $probability) {
		$this->identifier = $identifier;
		$this->probability = $probability;
	}

	protected static function encodeFloat($number) {
		if (is_float($number)) {
			// Note that %f is locale specific
			$encoded = sprintf('%F', $number);

			// Remove excess traling zeroes
			$encoded = preg_replace('/(\.[0-9]+?)0*$/', '$1', $encoded);
		} else {
			$encoded = 'null';
		}

		return $encoded;
	}

	public function getBaseObject($type, $tags) {
		$obj = ['t' => $type, 'i' => $this->identifier, 'p' => $this->probability];

		if (count($tags) > 0) {
			$obj['T'] = $tags;
		}

		return $obj;
	}
}

?>
