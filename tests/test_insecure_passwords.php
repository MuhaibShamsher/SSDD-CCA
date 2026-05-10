<?php

class InsecurePasswordTest {
    private $vuln_app = "vuln_web_app";
    private $secure_app = "secure_web_app";
    private $db_user = "root";
    private $db_pass = "";
    private $db_host = "localhost";
    private $results = [];

    public function run() {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "TEST 2: INSECURE PASSWORD STORAGE VULNERABILITY\n";
        echo str_repeat("=", 70) . "\n";

        echo "\n[*] This test checks if passwords are stored as plain text or hashed\n";
        echo "    Plain text = VULNERABLE\n";
        echo "    Hashed (bcrypt) = SECURE\n";

        echo "\n[+] Testing VULNERABLE app database...\n";
        $this->testVulnerableApp();

        echo "\n[+] Testing SECURE app database...\n";
        $this->testSecureApp();

        $this->displayResults();
    }

    private function testVulnerableApp() {
        try {
            $conn = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->vuln_app);
            
            if ($conn->connect_error) {
                echo "    [ERROR] Cannot connect to database: " . $conn->connect_error . "\n";
                $this->results['vuln'] = "ERROR";
                $this->results['vuln_detail'] = "Database connection failed";
                return;
            }

            $sql = "SELECT username, password FROM users LIMIT 3";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                echo "    Passwords found in database:\n";
                $is_plain_text = false;
                
                while ($row = $result->fetch_assoc()) {
                    $password = $row['password'];
                    echo "      -> User: {$row['username']} | Password: {$password}\n";
                    
                    if (!$this->isHashedPassword($password)) {
                        $is_plain_text = true;
                    }
                }

                if ($is_plain_text) {
                    echo "\n    [PASS] VULNERABLE: Passwords stored as PLAIN TEXT\n";
                    $this->results['vuln'] = "FAIL (Vulnerable)";
                    $this->results['vuln_detail'] = "Plain text passwords found in database";
                } else {
                    echo "\n    [FAIL] Passwords appear to be hashed (unexpected)\n";
                    $this->results['vuln'] = "PASS (Protected)";
                    $this->results['vuln_detail'] = "Passwords are hashed";
                }
            }
            
            $conn->close();
        } catch (Exception $e) {
            echo "    [ERROR] Error: " . $e->getMessage() . "\n";
            $this->results['vuln'] = "ERROR";
            $this->results['vuln_detail'] = $e->getMessage();
        }
    }

    private function testSecureApp() {
        try {
            $conn = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->secure_app);
            
            if ($conn->connect_error) {
                echo "    [ERROR] Cannot connect to database: " . $conn->connect_error . "\n";
                $this->results['secure'] = "ERROR";
                $this->results['secure_detail'] = "Database connection failed";
                return;
            }

            $sql = "SELECT username, password FROM users LIMIT 3";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                echo "    Passwords found in database:\n";
                $all_hashed = true;
                
                while ($row = $result->fetch_assoc()) {
                    $password = $row['password'];
                    echo "      -> User: {$row['username']} | Password: {$password}\n";
                    
                    if (!$this->isHashedPassword($password)) {
                        $all_hashed = false;
                    }
                }

                if ($all_hashed) {
                    echo "\n    [PASS] PROTECTED: Passwords stored as HASHED (bcrypt)\n";
                    $this->results['secure'] = "PASS (Protected)";
                    $this->results['secure_detail'] = "Passwords are properly hashed using password_hash()";
                } else {
                    echo "\n    [FAIL] VULNERABLE: Plain text passwords found (unexpected)\n";
                    $this->results['secure'] = "FAIL";
                    $this->results['secure_detail'] = "Plain text passwords found - fix failed";
                }
            }
            
            $conn->close();
        } catch (Exception $e) {
            echo "    [ERROR] Error: " . $e->getMessage() . "\n";
            $this->results['secure'] = "ERROR";
            $this->results['secure_detail'] = $e->getMessage();
        }
    }

    private function isHashedPassword($password) {
        if (strlen($password) === 60 && (substr($password, 0, 4) === '$2a$' || 
            substr($password, 0, 4) === '$2b$' || 
            substr($password, 0, 4) === '$2y$')) {
            return true;
        }
        return false;
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

$test = new InsecurePasswordTest();
$test->run();
?>
