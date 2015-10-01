<?php

namespace Agento;

class Client {
	private $host;
	private $port;
	private $sock = NULL;
	private $tags = [];

	/**
	 * @param {string} $agentoHost The host to send metrics to
	 * @param {int16} $agentoUdpPort The UDP port to use at the Agento host
	 */
	public function __construct($agentoHost, $agentoUdpPort) {
		// FIXME: Add sanity checks
		$this->host = $agentoHost;
		$this->port = $agentoUdpPort;
	}

	/**
	 * @param {string} $key
	 * @param {string} $value
	 */
	public function addTag($key, $value) {
		$this->tags[$key] = $value;
	}

	/**
	 * Do not leak sockets :)
	 */
	public function __destruct() {
		if ($this->sock != NULL) {
			socket_close($this->sock);
			$this->sock = NULL;
		}
	}

	/**
	 * @param {string} $identifier A string representing the metric
	 * @param {float} $value A value representing the metric
	 * @param {float} $probability A probability for the metric to propagate (1.0 means always)
	 */
	public function metric($identifier, $probability, $value) {
		$rnd = mt_rand() / mt_getrandmax();

		if ($probability < $rnd)
			return;

		$event = new Metric($identifier, $probability, $value);
		$this->send($event);
	}

	private function getSock() {
		if ($this->sock == NULL) {
			$this->sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		}

		return $this->sock;
	}

	private function send(Message $message) {
		$payload = $message->getPayload($this->tags);
		$length = strlen($payload);

		socket_sendto($this->getSock(), $payload, $length, 0, $this->host, $this->port);
	}
}

?>
