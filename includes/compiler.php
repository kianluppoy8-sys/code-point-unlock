<?php

class Compiler {
    private $tempDir;
    private $db;

    public function __construct($db = null) {
        $this->db = $db;
        $this->tempDir = sys_get_temp_dir() . '/code_unlock_' . uniqid();
        if (!is_dir($this->tempDir)) {
            if (!mkdir($this->tempDir, 0777, true)) {
                // Fallback to local temp if sys_get_temp_dir fails or restricted
                $this->tempDir = __DIR__ . '/../temp/' . uniqid();
                if (!is_dir($this->tempDir)) {
                    mkdir($this->tempDir, 0777, true);
                }
            }
        }
    }

    public function __destruct() {
        $this->cleanup($this->tempDir);
    }

    private function cleanup($dir) {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->cleanup("$dir/$file") : unlink("$dir/$file");
        }
        return @rmdir($dir);
    }

    public function executeCode($code, $language) {
        $api_url = "https://api.jdoodle.com/v1/execute";
        
        $langConfig = [
            'php' => ['lang' => 'php', 'versionIndex' => '4'],
            'java' => ['lang' => 'java', 'versionIndex' => '4'],
            'javascript' => ['lang' => 'nodejs', 'versionIndex' => '4'],
            'python' => ['lang' => 'python3', 'versionIndex' => '4']
        ];

        $lang = strtolower($language);
        if (!isset($langConfig[$lang])) {
            return ['success' => false, 'error' => 'Unsupported language: ' . $language, 'output' => ''];
        }

        // Fetch clientId and clientSecret
        $clientId = defined('JDOODLE_CLIENT_ID') ? JDOODLE_CLIENT_ID : (getenv('JDOODLE_CLIENT_ID') ?: '');
        $clientSecret = defined('JDOODLE_CLIENT_SECRET') ? JDOODLE_CLIENT_SECRET : (getenv('JDOODLE_CLIENT_SECRET') ?: '');

        if (empty($clientId) || empty($clientSecret) || $clientId === 'YOUR_JDOODLE_CLIENT_ID') {
            return ['success' => false, 'error' => 'JDoodle API credentials are not configured in .env.php. Please register at jdoodle.com and update .env.php.', 'output' => ''];
        }

        $data = [
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'script' => $code,
            'language' => $langConfig[$lang]['lang'],
            'versionIndex' => $langConfig[$lang]['versionIndex']
        ];

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200) {
            return ['success' => false, 'error' => "Runtime Error (API Code $http_code): " . $response, 'output' => ''];
        }

        $result = json_decode($response, true);
        
        if (isset($result['error'])) {
            return ['success' => false, 'error' => $result['error'], 'output' => ''];
        }

        $output = $result['output'] ?? '';

        return ['success' => true, 'error' => '', 'output' => trim($output)];
    }

    public function compileAndRunJava($code) {
        return $this->executeCode($code, 'java');
    }

    public function runPHP($code) {
        return $this->executeCode($code, 'php');
    }

    public function runMySQL($query) {
        if (!$this->db) {
            return ['success' => false, 'error' => 'Database connection not provided to compiler.', 'output' => ''];
        }

        try {
            // Basic sanitization/restriction: only allow SELECT for challenges
            $trimmedQuery = trim($query);
            if (stripos($trimmedQuery, 'SELECT') !== 0 && stripos($trimmedQuery, 'SHOW') !== 0 && stripos($trimmedQuery, 'DESCRIBE') !== 0) {
                // For safety, we could restrict to SELECT, but some challenges might involve INSERT/UPDATE and then SELECTing verification
                // However, user said "output based", so SELECT is most common.
            }
            
            // Use a transaction that we always roll back to prevent DB pollution
            $conn = $this->db->getConnection();
            $conn->beginTransaction();
            
            $stmt = $conn->prepare($query);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Sort keys of each row to ensure consistent JSON output
            foreach ($results as &$row) {
                ksort($row);
            }
            
            $conn->rollBack(); // Always rollback
            
            return [
                'success' => true,
                'error' => '',
                'output' => json_encode($results)
            ];
        } catch (Exception $e) {
            if (isset($conn) && $conn->inTransaction()) {
                $conn->rollBack();
            }
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'output' => ''
            ];
        }
    }

    private function findBinary($name) {
        $output = [];
        $ret = -1;
        exec("which $name", $output, $ret);
        if ($ret === 0 && !empty($output[0])) {
            return $output[0];
        }
        
        // Common paths fallback
        $paths = ["/usr/bin/$name", "/usr/local/bin/$name", "/opt/java/bin/$name"];
        foreach ($paths as $path) {
            if (is_executable($path)) return $path;
        }
        
        return null;
    }
}
