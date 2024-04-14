<?php
public function sendRequestToSocket($command) {
        $socket = $this->socketInitializer();

        // Send JSON data to the server
        $jsonCommand = json_encode($command);

        $bytesWritten = @socket_write($socket, $jsonCommand, strlen($jsonCommand));
        
        if ($bytesWritten === false) {
            $errorCode = socket_last_error();
            $errorMessage = socket_strerror($errorCode);
            throw new \Exception("Failed to write to socket: [$errorCode] $errorMessage");
        }

        if ($bytesWritten < strlen($jsonCommand)) {
            throw new \Exception("Incomplete write to socket. Only $bytesWritten bytes written out of " . strlen($jsonCommand));
        }

        $response = socket_read($socket, 1024);
        socket_close($socket);

        return $response;
    }