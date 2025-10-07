<?php

namespace App\Libraries;

use App\Models\MonitorModel;

class MonitoringEngine
{
    protected $monitorModel;
    
    public function __construct()
    {
        $this->monitorModel = new MonitorModel();
    }

    /**
     * Run all monitors that are due for checking
     */
    public function runChecks(): array
    {
        $monitors = $this->monitorModel->getMonitorsDueForCheck();
        $results = [];

        foreach ($monitors as $monitor) {
            $result = $this->checkMonitor($monitor);
            $results[] = [
                'monitor_id' => $monitor['id'],
                'name'       => $monitor['name'],
                'status'     => $result['status'],
                'response_time' => $result['response_time'] ?? null,
            ];
        }

        return $results;
    }

    /**
     * Check a single monitor
     */
    public function checkMonitor(array $monitor): array
    {
        $result = [
            'status'        => 'down',
            'response_time' => null,
            'status_code'   => null,
            'error_message' => null,
        ];

        try {
            switch ($monitor['type']) {
                case 'http':
                case 'https':
                    $result = $this->checkHttp($monitor);
                    break;
                case 'tcp':
                    $result = $this->checkTcp($monitor);
                    break;
                case 'ssl':
                    $result = $this->checkSsl($monitor);
                    break;
                case 'ping':
                    $result = $this->checkPing($monitor);
                    break;
            }
        } catch (\Exception $e) {
            $result['error_message'] = $e->getMessage();
        }

        // Record the check result
        $success = ($result['status'] === 'up');
        $this->monitorModel->recordCheck(
            $monitor['id'],
            $success,
            $result['response_time'],
            $result['status_code'],
            $result['error_message']
        );

        return $result;
    }

    /**
     * Check HTTP/HTTPS endpoint
     */
    protected function checkHttp(array $monitor): array
    {
        $startTime = microtime(true);
        
        // SSL verification - enabled by default for security
        // Can be disabled per-monitor for self-signed certificates
        $verifySSL = $monitor['verify_ssl'] ?? true;
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $monitor['target'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => $monitor['timeout'],
            CURLOPT_CONNECTTIMEOUT => $monitor['timeout'],
            CURLOPT_SSL_VERIFYPEER => $verifySSL,
            CURLOPT_SSL_VERIFYHOST => $verifySSL ? 2 : 0,
            CURLOPT_USERAGENT      => 'UpWatch Monitor/1.0',
            CURLOPT_CUSTOMREQUEST  => $monitor['method'],
        ]);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $endTime = microtime(true);
        
        curl_close($ch);

        $responseTime = round(($endTime - $startTime) * 1000); // Convert to milliseconds

        // Check if request was successful
        $success = false;
        $errorMessage = null;

        if ($error) {
            $errorMessage = $error;
        } elseif ($statusCode != $monitor['expected_status_code']) {
            $errorMessage = "Expected status code {$monitor['expected_status_code']}, got {$statusCode}";
        } elseif (!empty($monitor['keyword_match']) && stripos($response, $monitor['keyword_match']) === false) {
            $errorMessage = "Keyword '{$monitor['keyword_match']}' not found in response";
        } else {
            $success = true;
        }

        return [
            'status'        => $success ? 'up' : 'down',
            'response_time' => $responseTime,
            'status_code'   => $statusCode,
            'error_message' => $errorMessage,
        ];
    }

    /**
     * Check TCP port connectivity
     */
    protected function checkTcp(array $monitor): array
    {
        $startTime = microtime(true);
        
        // Parse host:port from target
        $parts = explode(':', $monitor['target']);
        $host = $parts[0];
        $port = $parts[1] ?? 80;

        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($host, $port, $errno, $errstr, $monitor['timeout']);
        
        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000);

        if ($socket) {
            fclose($socket);
            return [
                'status'        => 'up',
                'response_time' => $responseTime,
                'status_code'   => null,
                'error_message' => null,
            ];
        }

        return [
            'status'        => 'down',
            'response_time' => $responseTime,
            'status_code'   => null,
            'error_message' => "Connection failed: {$errstr} (Error {$errno})",
        ];
    }

    /**
     * Check SSL certificate expiration
     */
    protected function checkSsl(array $monitor): array
    {
        $startTime = microtime(true);
        
        $url = parse_url($monitor['target']);
        $host = $url['host'] ?? $monitor['target'];
        $port = $url['port'] ?? 443;

        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer'       => false,
                'verify_peer_name'  => false,
            ],
        ]);

        $client = @stream_socket_client(
            "ssl://{$host}:{$port}",
            $errno,
            $errstr,
            $monitor['timeout'],
            STREAM_CLIENT_CONNECT,
            $context
        );

        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000);

        if (!$client) {
            return [
                'status'        => 'down',
                'response_time' => $responseTime,
                'status_code'   => null,
                'error_message' => "SSL connection failed: {$errstr}",
            ];
        }

        $params = stream_context_get_params($client);
        $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
        fclose($client);

        if (!$cert) {
            return [
                'status'        => 'down',
                'response_time' => $responseTime,
                'status_code'   => null,
                'error_message' => 'Unable to parse SSL certificate',
            ];
        }

        $expiryDate = $cert['validTo_time_t'];
        $daysUntilExpiry = ceil(($expiryDate - time()) / 86400);

        // Warn if certificate expires within 30 days
        if ($daysUntilExpiry < 0) {
            return [
                'status'        => 'down',
                'response_time' => $responseTime,
                'status_code'   => null,
                'error_message' => 'SSL certificate expired',
            ];
        } elseif ($daysUntilExpiry < 30) {
            return [
                'status'        => 'up',
                'response_time' => $responseTime,
                'status_code'   => null,
                'error_message' => "SSL certificate expires in {$daysUntilExpiry} days",
            ];
        }

        return [
            'status'        => 'up',
            'response_time' => $responseTime,
            'status_code'   => null,
            'error_message' => null,
        ];
    }

    /**
     * Check ping (ICMP)
     */
    protected function checkPing(array $monitor): array
    {
        $startTime = microtime(true);
        
        $host = $monitor['target'];
        
        // Use system ping command (platform-specific)
        if (stristr(PHP_OS, 'WIN')) {
            $cmd = "ping -n 1 -w " . ($monitor['timeout'] * 1000) . " {$host}";
        } else {
            $cmd = "ping -c 1 -W {$monitor['timeout']} {$host}";
        }

        exec($cmd, $output, $returnVar);
        
        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000);

        $success = ($returnVar === 0);

        return [
            'status'        => $success ? 'up' : 'down',
            'response_time' => $responseTime,
            'status_code'   => null,
            'error_message' => $success ? null : 'Ping failed',
        ];
    }
}
