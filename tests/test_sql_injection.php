<?php

class SQLInjectionTest {
    private $vuln_url = "http://localhost/vuln-web-app/login.php";
    private $secure_url = "http://localhost/secure-web-app/login.php";
    private $results = [];

    public function run() {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "TEST 1: SQL INJECTION VULNERABILITY\n";
        echo str_repeat("=", 70) . "\n";

        $payload = [
            'username' => "' OR '1'='1' -- ",
            'password' => "anything"
        ];

        echo "\n[*] Payload being used:\n";
        echo "    Username: " . $payload['username'] . "\n";
        echo "    Password: " . $payload['password'] . "\n";

        echo "\n[+] Testing VULNERABLE app...\n";
        $this->testVulnerableApp($payload);

        echo "\n[+] Testing SECURE app...\n";
        $this->testSecureApp($payload);

        $this->displayResults();
    }

    private function testVulnerableApp($payload) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->vuln_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code == 302 || strpos($response, 'dashboard') !== false) {
                echo "    [PASS] VULNERABLE: SQL Injection SUCCESSFUL (attacker logged in)\n";
                $this->results['vuln'] = "FAIL (Vulnerable)";
                $this->results['vuln_detail'] = "SQL Injection succeeded - unauthorized access granted";
            } else {
                echo "    [FAIL] SQL Injection BLOCKED (unexpected)\n";
                $this->results['vuln'] = "PASS (Protected)";
                $this->results['vuln_detail'] = "SQL Injection was blocked";
            }
        } catch (Exception $e) {
            echo "    [ERROR] Error testing vulnerable app: " . $e->getMessage() . "\n";
            $this->results['vuln'] = "ERROR";
            $this->results['vuln_detail'] = $e->getMessage();
        }
    }

    private function testSecureApp($payload) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->secure_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code == 302 || strpos($response, 'dashboard') !== false) {
                echo "    [FAIL] VULNERABLE: SQL Injection SUCCESSFUL (unexpected)\n";
                $this->results['secure'] = "FAIL";
                $this->results['secure_detail'] = "SQL Injection succeeded - fix failed";
            } else {
                echo "    [PASS] PROTECTED: SQL Injection BLOCKED (prepared statements working)\n";
                $this->results['secure'] = "PASS (Protected)";
                $this->results['secure_detail'] = "Prepared statements prevented SQL Injection";
            }
        } catch (Exception $e) {
            echo "    [ERROR] Error testing secure app: " . $e->getMessage() . "\n";
            $this->results['secure'] = "ERROR";
            $this->results['secure_detail'] = $e->getMessage();
        }
    }

    private function displayResults() {
        echo "\n" . str_repeat("-", 70) . "\n";
        echo "RESULTS:\n";
        echo str_repeat("-", 70) . "\n";
        echo "Vulnerable App: " . $this->results['vuln'] . "\n";
        echo "  -> " . $this->results['vuln_detail'] . "\n\n";
        echo "Secure App: " . $this->results['secure'] . "\n";
        echo "  -> " . $this->results['secure_detail'] . "\n";
        echo str_repeat("-", 70) . "\n";

        if ($this->results['vuln'] == "FAIL (Vulnerable)" && $this->results['secure'] == "PASS (Protected)") {
            echo "[PASS] TEST PASSED: Vulnerability confirmed in vulnerable app, fixed in secure app\n";
            return true;
        } else {
            echo "[FAIL] TEST FAILED: Unexpected results\n";
            return false;
        }
    }
}

$test = new SQLInjectionTest();
$test->run();
?>
