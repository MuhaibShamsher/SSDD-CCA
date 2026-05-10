<?php

class IDORTest {
    private $vuln_url = "http://localhost/vuln-web-app/profile.php";
    private $secure_url = "http://localhost/secure-web-app/profile.php";
    private $vuln_login = "http://localhost/vuln-web-app/login.php";
    private $secure_login = "http://localhost/secure-web-app/login.php";
    private $results = [];

    public function run() {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "TEST 3: INSECURE DIRECT OBJECT REFERENCE (IDOR) VULNERABILITY\n";
        echo str_repeat("=", 70) . "\n";

        echo "\n[*] This test checks if users can access other users' profiles\n";
        echo "    IDOR = Can access any user's profile by changing ID parameter\n";
        echo "    Protected = Can only access own profile\n";

        echo "\n[+] Testing VULNERABLE app...\n";
        $this->testVulnerableApp();

        echo "\n[+] Testing SECURE app...\n";
        $this->testSecureApp();

        $this->displayResults();
    }

    private function testVulnerableApp() {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->vuln_login);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['username' => 'user1', 'password' => 'password1']));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir() . '/vuln_cookies.txt');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            
            curl_exec($ch);
            curl_close($ch);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->vuln_url . "?id=3");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/vuln_cookies.txt');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            curl_close($ch);

            if (strpos($response, 'user2') !== false) {
                echo "    [PASS] VULNERABLE: Successfully accessed user 2's profile while logged in as user 1\n";
                echo "      -> User 2's username is visible in the response\n";
                $this->results['vuln'] = "FAIL (Vulnerable)";
                $this->results['vuln_detail'] = "IDOR vulnerability - accessed other user's profile via ID parameter";
            } else {
                echo "    [FAIL] Cannot confirm IDOR (unexpected)\n";
                $this->results['vuln'] = "INCONCLUSIVE";
                $this->results['vuln_detail'] = "Could not confirm vulnerability";
            }

            @unlink(sys_get_temp_dir() . '/vuln_cookies.txt');
        } catch (Exception $e) {
            echo "    [ERROR] Error: " . $e->getMessage() . "\n";
            $this->results['vuln'] = "ERROR";
            $this->results['vuln_detail'] = $e->getMessage();
        }
    }

    private function testSecureApp() {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->secure_login);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['username' => 'user1', 'password' => 'password1']));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir() . '/secure_cookies.txt');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            
            curl_exec($ch);
            curl_close($ch);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->secure_url . "?id=3");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/secure_cookies.txt');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            curl_close($ch);

            if (strpos($response, 'user2') === false && strpos($response, 'user1') !== false) {
                echo "    [PASS] PROTECTED: Can only see own profile (user1)\n";
                echo "      -> Attempted access to user 2's profile was blocked\n";
                $this->results['secure'] = "PASS (Protected)";
                $this->results['secure_detail'] = "Session-based access control prevents IDOR";
            } else if (strpos($response, 'user2') !== false) {
                echo "    [FAIL] VULNERABLE: Successfully accessed user 2's profile (unexpected)\n";
                $this->results['secure'] = "FAIL";
                $this->results['secure_detail'] = "IDOR vulnerability still exists - fix failed";
            } else {
                echo "    [FAIL] Cannot confirm protection (unexpected response)\n";
                $this->results['secure'] = "INCONCLUSIVE";
                $this->results['secure_detail'] = "Could not verify protection";
            }

            @unlink(sys_get_temp_dir() . '/secure_cookies.txt');
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

$test = new IDORTest();
$test->run();
?>
