<?php

namespace Agento;

class Metric extends Message {
	const TYPE = 1;

	private $value;

	public function __construct($identifier, $probability, $value) {
		parent::__construct($identifier, $probability);

		$this->value = $value;
	}

	public function getPayload($tags) {
		if (version_compare(PHP_VERSION, '5.6.6', '<')) {
			/*
			 * Here we would like to simply use json_encode() - but that's impossible on older
			 * PHP installations due to (at least) two bugs:
			 *
			 * 1) https://bugs.php.net/bug.php?id=40360 - current locale affects how json_encode encodes floats
			 * 2) https://bugs.php.net/bug.php?id=50224 - json_encode() does not always encode a float as a float
			 *
			 * We hack our way through this.
			 */

			$identifierAsJson = json_encode($this->identifier);
			$valueAsJson = parent::encodeFloat($this->value);
			$probabilityAsJson = parent::encodeFloat($this->probability);

			if (count($tags) > 0) {
				$tagsAsJson = json_encode($tags);
			} else {
				// JSON_FORCE_OBJECT is not available until PHP 5.3.0
				$tagsAsJson = '{}';
			}

			$json = sprintf("{\"t\":%d,\"i\":%s,\"p\":%s,\"v\":%s,\"T\":%s}",
				self::TYPE,
				$identifierAsJson,
				$probabilityAsJson,
				$valueAsJson,
				$tagsAsJson);

			return $json;
		} else {
			$obj = $this->getBaseObject(self::TYPE, $tags);
			$obj['v'] = $this->value;

			return json_encode($obj, JSON_PRESERVE_ZERO_FRACTION | JSON_FORCE_OBJECT);
		}
	}
}

?>
