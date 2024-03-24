<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScanController extends Controller
{
    //nmap
    public function showNmap() {
        return view('scans.nmap');
    }

    public function runNmap(Request $request) {
        $ipAddresses = explode(';', $request->input('ip'));
        $args = explode(' ', $request->input('checkedValues'));
        $allArgs = array('-p-', '-P', '-sV', '-T5', '-A', '-iR', '-sn', '-v');
        $serverAddress = "127.0.0.1";
        $serverPort = 27908;
        $result = [];
        $validArgs = '';

        foreach ($args as $arg) {
            if (!in_array($arg, $allArgs)) {
                return response()->json(['error' => "Invalid arguments!"], 400);
            } else {
                $validArgs .= $arg . ' ';
            }
        }

        $wrappedArgs = '"'.trim($validArgs,'"').'"';


        foreach ($ipAddresses as $ip) {
            $command = [
                'action' => 'nmap_scan',
                'ip' => $ip,
                'args' => $wrappedArgs
            ];

            try {
                // Send JSON data to the server
                $response = Http::post("http://{$serverAddress}:{$serverPort}", $command);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }

            if ($response->successful()) {
                $responseData = $response->body();
            }

            $result[] = [
                'ip' => $ip,
                'output' => $responseData
            ];
        }

        return response()->json($result);
    }

    public function showGobuster() {
        return view('scans.gobuster');
    }

    public function runGosbuter(Request $request) {

        
        
    }
}
