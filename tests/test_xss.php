<?php

class XSSTest {
    private $vuln_form = "http://localhost/vuln-web-app/form.php";
    private $secure_form = "http://localhost/secure-web-app/form.php";
    private $vuln_submit = "http://localhost/vuln-web-app/submit_form.php";
    private $secure_submit = "http://localhost/secure-web-app/submit_form.php";
    private $vuln_login = "http://localhost/vuln-web-app/login.php";
    private $secure_login = "http://localhost/secure-web-app/login.php";
    private $results = [];

    public function run() {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "TEST 4: CROSS-SITE SCRIPTING (XSS) VULNERABILITY\n";
        echo str_repeat("=", 70) . "\n";

        echo "\n[*] This test checks if user input is properly escaped\n";
        echo "    XSS = JavaScript code is executed in the page\n";
        echo "    Protected = JavaScript code is escaped and displayed as text\n";

        $xss_payload = "<img src=x onerror=\"alert('XSS_Vulnerability')\">";

        echo "\n[*] Payload being used:\n";
        echo "    " . $xss_payload . "\n";

        echo "\n[+] Testing VULNERABLE app...\n";
        $this->testVulnerableApp($xss_payload);

        echo "\n[+] Testing SECURE app...\n";
        $this->testSecureApp($xss_payload);

        $this->displayResults();
    }

    private function testVulnerableApp($payload) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->vuln_login);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['username' => 'admin', 'password' => 'admin123']));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir() . '/vuln_xss_cookies.txt');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            
            curl_exec($ch);
            curl_close($ch);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->vuln_submit);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['comment' => $payload]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/vuln_xss_cookies.txt');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            
            curl_exec($ch);
            curl_close($ch);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->vuln_form);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/vuln_xss_cookies.txt');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            curl_close($ch);

            if (strpos($response, $payload) !== false) {
                echo "    [PASS] VULNERABLE: XSS payload is NOT escaped (raw HTML/JavaScript present)\n";
                echo "      -> Payload found in page: " . htmlspecialchars(substr($payload, 0, 50)) . "...\n";
                $this->results['vuln'] = "FAIL (Vulnerable)";
                $this->results['vuln_detail'] = "XSS vulnerability - user input not escaped";
            } else if (strpos($response, htmlspecialchars($payload)) !== false) {
                echo "    [FAIL] Payload is escaped (unexpected)\n";
                $this->results['vuln'] = "PASS (Protected)";
                $this->results['vuln_detail'] = "User input is properly escaped";
            } else {
                echo "    [FAIL] Cannot confirm XSS (payload not found in response)\n";
                $this->results['vuln'] = "INCONCLUSIVE";
                $this->results['vuln_detail'] = "Could not confirm vulnerability";
            }

            @unlink(sys_get_temp_dir() . '/vuln_xss_cookies.txt');
        } catch (Exception $e) {
            echo "    [ERROR] Error: " . $e->getMessage() . "\n";
            $this->results['vuln'] = "ERROR";
            $this->results['vuln_detail'] = $e->getMessage();
        }
    }

    private function testSecureApp($payload) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->secure_login);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['username' => 'admin', 'password' => 'admin123']));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir() . '/secure_xss_cookies.txt');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            
            curl_exec($ch);
            curl_close($ch);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->secure_submit);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['comment' => $payload]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/secure_xss_cookies.txt');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            
            curl_exec($ch);
            curl_close($ch);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->secure_form);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir() . '/secure_xss_cookies.txt');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            curl_close($ch);

            $escaped_default = htmlspecialchars($payload);
            $escaped_quotes = htmlspecialchars($payload, ENT_QUOTES, 'UTF-8');
            $escaped_compat = htmlspecialchars($payload, ENT_COMPAT, 'UTF-8');

            if (
                strpos($response, $escaped_default) !== false ||
                strpos($response, $escaped_quotes) !== false ||
                strpos($response, $escaped_compat) !== false ||
                strpos($response, '&lt;img src=x onerror=') !== false
            ) {
                echo "    [PASS] PROTECTED: XSS payload is properly ESCAPED (htmlspecialchars working)\n";
                echo "      -> Payload safely displayed as text, not executed\n";
                $this->results['secure'] = "PASS (Protected)";
                $this->results['secure_detail'] = "htmlspecialchars() prevents XSS attacks";
            } else if (strpos($response, $payload) !== false) {
                echo "    [FAIL] VULNERABLE: XSS payload is NOT escaped (unexpected)\n";
                $this->results['secure'] = "FAIL";
                $this->results['secure_detail'] = "XSS vulnerability still exists - fix failed";
            } else {
                echo "    [FAIL] Cannot confirm protection (payload not found)\n";
                $this->results['secure'] = "INCONCLUSIVE";
                $this->results['secure_detail'] = "Could not verify protection";
            }

            @unlink(sys_get_temp_dir() . '/secure_xss_cookies.txt');
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

$test = new XSSTest();
$test->run();
?>
