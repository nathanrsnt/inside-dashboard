<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function socketInitializer() {
        $address = "127.0.0.1";
        $port = 27908;

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (!$socket) {
            throw new \Exception("Failed to create socket");
        }

        if (!socket_connect($socket, $address, $port)) {
            throw new \Exception("Failed to connect to server");
        }

        return $socket;
    }

    public function sendRequestToSocket($command) {

        try {
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
        } catch (\Exception e) {
            return response()->json(['error', => $e], 500);
        }
    }

    public function showNmap() {
        return view('scans.nmap');  
    }

    public function runNmap(Request $request) {
        $ipAddresses = explode(';', $request->input('ip'));
        $args = explode(' ', $request->input('checkedValues'));
        $allArgs = array('-p-', '-P', '-sV', '-T5', '-A', '-iR', '-sn', '-v');
        $result = [];
        $validArgs = '';

        foreach ($args as $arg) {
            if (!in_array($arg, $allArgs)) {
                return response()->json(['error' => "Invalid arguments!"], 500);
            } else {
                $validArgs .= $arg . ' ';
            }
        }

        foreach ($ipAddresses as $ip) {
            $command = [
                'action' => 'nmap-scan',
                'ip' => $ip,
                'args' => $validArgs
            ];

            try {
                $response = $this->sendRequestToSocket($command);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }

            $result[] = [
                'ip' => $ip,
                'output' => $response
            ];
        }

        return response()->json($result);
    }

    public function showFeroxbuster() {
        return view('scans.feroxbuster');
    }

    public function runFeroxbuster(Request $request) { 
        $url = $request->input('ip');

        $command = [
            'action' => 'feroxbuster-scan',
            'url' => $url,
            'args' => '--depth 2'
        ];

        $response = $this->sendRequestToSocket($command);

        return $response;
    }
}
