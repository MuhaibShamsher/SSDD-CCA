<?php

class BruteForceTest {
    private $vuln_url = "http://localhost/vuln-web-app/login.php";
    private $secure_url = "http://localhost/secure-web-app/login.php";
    private $results = [];
    private $max_attempts = 8;

    public function run() {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "TEST 5: BRUTE FORCE ATTACK (NO RATE LIMITING) VULNERABILITY\n";
        echo str_repeat("=", 70) . "\n";

        echo "\n[*] This test checks if the application limits login attempts\n";
        echo "    No Protection = All attempts allowed indefinitely\n";
        echo "    Protected = Blocked after 5 attempts for 60 seconds\n";

        echo "\n[+] Testing VULNERABLE app...\n";
        $this->testVulnerableApp();

        echo "\n[+] Testing SECURE app...\n";
        $this->testSecureApp();

        $this->displayResults();
    }

    private function testVulnerableApp() {
        try {
            echo "    Sending {$this->max_attempts} failed login attempts...\n";
            
            $blocked_count = 0;
            $allowed_count = 0;

            for ($i = 1; $i <= $this->max_attempts; $i++) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->vuln_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                    'username' => 'admin',
                    'password' => 'wrongpassword' . $i
                ]));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);

                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if (strpos($response, 'Too many') !== false || strpos($response, 'wait') !== false) {
                    $blocked_count++;
                    echo "      Attempt $i: BLOCKED\n";
                } else {
                    $allowed_count++;
                }
            }

            if ($allowed_count == $this->max_attempts && $blocked_count == 0) {
                echo "\n    [PASS] VULNERABLE: All {$this->max_attempts} attempts were allowed (no rate limiting)\n";
                $this->results['vuln'] = "FAIL (Vulnerable)";
                $this->results['vuln_detail'] = "No brute-force protection - all attempts allowed";
            } else if ($blocked_count > 0) {
                echo "\n    [FAIL] Some requests were blocked (unexpected)\n";
                $this->results['vuln'] = "PASS (Protected)";
                $this->results['vuln_detail'] = "Rate limiting is enabled";
            } else {
                echo "\n    ? Cannot determine status\n";
                $this->results['vuln'] = "INCONCLUSIVE";
                $this->results['vuln_detail'] = "Could not confirm vulnerability";
            }
        } catch (Exception $e) {
            echo "    [ERROR] Error: " . $e->getMessage() . "\n";
            $this->results['vuln'] = "ERROR";
            $this->results['vuln_detail'] = $e->getMessage();
        }
    }

    private function testSecureApp() {
        try {
            echo "    Sending {$this->max_attempts} failed login attempts...\n";
            
            $blocked_count = 0;
            $allowed_count = 0;

            for ($i = 1; $i <= $this->max_attempts; $i++) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->secure_url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                    'username' => 'admin',
                    'password' => 'wrongpassword' . $i
                ]));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);

                $response = curl_exec($ch);
                curl_close($ch);

                if (strpos($response, 'Too many') !== false || strpos($response, 'wait') !== false) {
                    $blocked_count++;
                    echo "      Attempt $i: BLOCKED - " . trim(strip_tags($response)) . "\n";
                } else {
                    $allowed_count++;
                    echo "      Attempt $i: Allowed\n";
                }
            }

            if ($blocked_count > 0 && $allowed_count > 0) {
                echo "\n    [PASS] PROTECTED: Rate limiting active (blocked after {$blocked_count} attempts)\n";
                $this->results['secure'] = "PASS (Protected)";
                $this->results['secure_detail'] = "Rate limiting prevents brute-force attacks";
            } else if ($blocked_count == 0) {
                echo "\n    [FAIL] VULNERABLE: No rate limiting detected (unexpected)\n";
                $this->results['secure'] = "FAIL";
                $this->results['secure_detail'] = "Brute-force protection not working";
            } else {
                echo "\n    ? Cannot determine status\n";
                $this->results['secure'] = "INCONCLUSIVE";
                $this->results['secure_detail'] = "Could not verify protection";
            }
        } catch (Exception $e) {
            echo "    [ERROR] Error: " . $e->getMessage() . "\n";
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

$test = new BruteForceTest();
$test->run();
?>
