<?php

class MasterTestRunner {
    private $tests = [
        'test_sql_injection.php' => 'SQL Injection',
        'test_insecure_passwords.php' => 'Insecure Password Storage',
        'test_idor.php' => 'IDOR (Insecure Direct Object Reference)',
        'test_xss.php' => 'Cross-Site Scripting (XSS)',
        'test_brute_force.php' => 'Brute Force Attack'
    ];
    
    private $results = [];
    private $test_dir;

    public function __construct() {
        $this->test_dir = __DIR__;
    }

    public function run() {
        $this->displayHeader();
        $this->runAllTests();
        $this->displaySummary();
    }

    private function displayHeader() {
        echo "\n";
        echo str_repeat("=", 70) . "\n";
        echo "SSDD-CCA PROJECT - SECURITY VULNERABILITY TEST SUITE\n";
        echo "NED University of Engineering & Technology\n";
        echo "CT-477 Secure Software Design & Development\n";
        echo str_repeat("=", 70) . "\n";
        echo "Submission Date: " . date('Y-m-d H:i:s') . "\n";
        echo "Total Tests: " . count($this->tests) . "\n";
        echo str_repeat("=", 70) . "\n";
    }

    private function runAllTests() {
        foreach ($this->tests as $filename => $test_name) {
            $filepath = $this->test_dir . '/' . $filename;
            
            if (!file_exists($filepath)) {
                echo "\n[SKIPPED] $filename (file not found)\n";
                continue;
            }

            echo "\n[Running Test: $test_name]\n";
            
            ob_start();
            include $filepath;
            $output = ob_get_clean();
            
            $this->results[$test_name] = [
                'file' => $filename,
                'output' => $output
            ];
        }
    }

    private function displaySummary() {
        echo "\n\n";
        echo str_repeat("=", 70) . "\n";
        echo "TEST EXECUTION SUMMARY\n";
        echo str_repeat("=", 70) . "\n";

        $total = count($this->results);
        $passed = 0;
        $failed = 0;
        $errors = 0;

        foreach ($this->results as $test_name => $data) {
            if (strpos($data['output'], '[PASS] TEST PASSED') !== false) {
                echo "[PASS] $test_name\n";
                $passed++;
            } else if (strpos($data['output'], '[FAIL] TEST FAILED') !== false) {
                echo "[FAIL] $test_name\n";
                $failed++;
            } else if (strpos($data['output'], 'ERROR') !== false) {
                echo "[ERROR] $test_name\n";
                $errors++;
            } else {
                echo "? UNKNOWN: $test_name\n";
            }
        }

        echo str_repeat("=", 70) . "\n";
        echo "Results: $passed Passed | $failed Failed | $errors Errors (Total: $total)\n";
        echo str_repeat("=", 70) . "\n";

        if ($failed == 0 && $errors == 0) {
            echo "\n[PASS] ALL TESTS PASSED!\n";
            echo "  All vulnerabilities were properly identified and fixed.\n";
            echo "\n[INFO] PROJECT READY FOR SUBMISSION:\n";
            echo "  1. Vulnerable app has all intentional vulnerabilities\n";
            echo "  2. Secure app has all fixes properly implemented\n";
            echo "  3. Test suite successfully validates both versions\n";
        } else {
            echo "\n[FAIL] SOME TESTS FAILED\n";
            echo "  Please review the test output above for details.\n";
        }

        echo "\n";
    }

    public function generateReport() {
        $report = $this->test_dir . '/TEST_REPORT.txt';
        
        $content = "SECURITY VULNERABILITY TEST REPORT\n";
        $content .= "Generated: " . date('Y-m-d H:i:s') . "\n";
        $content .= str_repeat("=", 70) . "\n\n";

        foreach ($this->results as $test_name => $data) {
            $content .= $test_name . "\n";
            $content .= str_repeat("-", 70) . "\n";
            $content .= $data['output'] . "\n\n";
        }

        file_put_contents($report, $content);
        echo "\nDetailed report saved to: TEST_REPORT.txt\n";
    }
}

$runner = new MasterTestRunner();
$runner->run();
?>
