<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
require 'vendor/autoload.php';

class WebSocketServer implements MessageComponentInterface {
	public function onOpen(ConnectionInterface $conn) {
		echo "New connection: {$conn->resourceId}\n";
	}

	public function onMessage(ConnectionInterface $from, $msg) {
		echo "Received: $msg\n";
		$from->send("Echo: " . $msg);
	}

	public function onClose(ConnectionInterface $conn) {
		echo "Connection {$conn->resourceId} has disconnected\n";
	}

	public function onError(ConnectionInterface $conn, \Exception $e) {
		echo "Error: {$e->getMessage()}\n";
		$conn->close();
	}
}

$server = Ratchet\App::factory("0.0.0.0", 8080);
$server->route('/ws', new WebSocketServer, ['*']);
$server->run();
