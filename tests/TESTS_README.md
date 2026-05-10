# Security Vulnerability Test Suite

This directory contains automated test scripts for the SSDD-CCA project. Each test validates one of the five identified security vulnerabilities across both the vulnerable and secure web applications.

## Overview

The test suite includes 5 individual tests, each targeting a specific vulnerability:

| Test                          | Vulnerability                           | Expected Behavior                                                 |
| ----------------------------- | --------------------------------------- | ----------------------------------------------------------------- |
| `test_sql_injection.php`      | SQL Injection                           | Vulnerable app fails, Secure app passes                           |
| `test_insecure_passwords.php` | Insecure Password Storage               | Plain text found in vulnerable, hashed in secure                  |
| `test_idor.php`               | IDOR (Insecure Direct Object Reference) | Can access other users' profiles in vulnerable, blocked in secure |
| `test_xss.php`                | Cross-Site Scripting (XSS)              | Unescaped HTML in vulnerable, escaped in secure                   |
| `test_brute_force.php`        | Brute Force (No Rate Limiting)          | All attempts allowed in vulnerable, blocked after 5 in secure     |

## Prerequisites

### System Requirements

- PHP 7.0 or higher
- cURL extension enabled (`curl` must be available)
- MySQL/MariaDB server running with both databases set up
- Both web applications (vulnerable and secure) running and accessible

### Database Setup

Before running tests, ensure both databases are initialized:

```bash
mysql -u root -p < ../setup_databases.sql
```

For manual setup, see [DATABASE_SETUP.md](../DATABASE_SETUP.md)

### Web Server Configuration

Both applications must be accessible at:

- **Vulnerable App**: `http://localhost/vuln-web-app/`
- **Secure App**: `http://localhost/secure-web-app/`

If your setup uses different URLs/ports, modify the URLs in each test file accordingly.

## Running Tests

### Run All Tests (Recommended)

Execute the master test runner:

```bash
php run_all_tests.php
```

[PASS] SQL Injection
[PASS] Insecure Password Storage
[PASS] IDOR (Insecure Direct Object Reference)
[PASS] Cross-Site Scripting (XSS)
[PASS] Brute Force Attack 4. Indicate overall pass/fail status

**Example Output:**

```
=============================================
SSDD-CCA PROJECT - SECURITY VULNERABILITY TEST SUITE
=============================================
...
[Running Test: SQL Injection]
...
TEST EXECUTION SUMMARY
=============================================
✓ PASS: SQL Injection
✓ PASS: Insecure Password Storage
✓ PASS: IDOR (Insecure Direct Object Reference)
✓ PASS: Cross-Site Scripting (XSS)
✓ PASS: Brute Force Attack

Results: 5 Passed | 0 Failed | 0 Errors (Total: 5)
=============================================
```

### Run Individual Tests

To run a specific test:

```bash
php test_sql_injection.php
php test_insecure_passwords.php
php test_idor.php
php test_xss.php
php test_brute_force.php
```

## Test Details

### 1. SQL Injection Test (`test_sql_injection.php`)

**Vulnerability**: Direct concatenation of user input in SQL queries

**Attack Vector**: Inject SQL code to bypass authentication

- Payload: `' OR '1'='1' -- ` (username)
- Expected: Bypass login without valid credentials

**Expected Results**:

- ✓ Vulnerable App: **FAILS** - SQL Injection succeeds
- ✓ Secure App: **PASSES** - Prepared statements block the attack

**How to Fix** (already done in secure app):

```php
// Vulnerable:
$query = "SELECT * FROM users WHERE username = '$username'";

// Secure:
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
```

---

### 2. Insecure Password Storage Test (`test_insecure_passwords.php`)

**Vulnerability**: Passwords stored as plain text in database

**Risk**: If database is compromised, all passwords are exposed immediately

**Expected Results**:

- ✓ Vulnerable App: **FAILS** - Passwords stored as plain text
  - Example: `admin` / `admin123`
- ✓ Secure App: **PASSES** - Passwords hashed with bcrypt
  - Example: `admin` / `$2y$10$N9qo8uLOickgx2ZMRZoMye...`

**How to Fix** (already done in secure app):

```php
// Vulnerable:
$password = $_POST['password'];  // Stored as-is

// Secure:
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
// Verify: password_verify($input, $hashedPassword)
```

---

### 3. IDOR Test (`test_idor.php`)

**Vulnerability**: Insecure Direct Object Reference - accessing other users' profiles

**Attack Vector**: Modify URL parameter to access unauthorized data

- Example: `profile.php?id=2` while logged in as user 1

**Expected Results**:

- ✓ Vulnerable App: **FAILS** - Can view any user's profile by changing ID
- ✓ Secure App: **PASSES** - Only own profile accessible via session

**How to Fix** (already done in secure app):

```php
// Vulnerable:
$user_id = $_GET['id'];  // User-controlled

// Secure:
$user_id = $_SESSION['user_id'];  // From session only
```

---

### 4. XSS Test (`test_xss.php`)

**Vulnerability**: User input not escaped before output to HTML

**Attack Vector**: Inject JavaScript that executes in other users' browsers

- Payload: `<img src=x onerror="alert('XSS')">`

**Expected Results**:

- ✓ Vulnerable App: **FAILS** - JavaScript code executed/visible
- ✓ Secure App: **PASSES** - Code escaped, displayed as safe text

**How to Fix** (already done in secure app):

```php
// Vulnerable:
echo $user_comment;  // Direct output

// Secure:
echo htmlspecialchars($user_comment, ENT_QUOTES, 'UTF-8');
```

---

### 5. Brute Force Test (`test_brute_force.php`)

**Vulnerability**: No protection against repeated failed login attempts

**Attack Vector**: Try many password combinations without being blocked

- Attempt: 10 consecutive failed logins

**Expected Results**:

- ✓ Vulnerable App: **FAILS** - All 10 attempts allowed
- ✓ Secure App: **PASSES** - Blocked after 5 attempts for 60 seconds

**How to Fix** (already done in secure app):

```php
// Secure app implementation:
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SESSION['login_attempts'] >= 5) {
    die("Too many login attempts. Please wait 60 seconds.");
}

// Increment on failed attempt
$_SESSION['login_attempts']++;
```

## Test Output Interpretation

### Each Test Produces:

1. **Vulnerability Description** - What is being tested
2. **Attack Payload** - The actual malicious input
3. **Vulnerable App Results** - Should show vulnerability exploited
4. **Secure App Results** - Should show vulnerability blocked
5. **Overall Result** - PASS or FAIL

### Example Output:

```
======================================================================
TEST 1: SQL INJECTION VULNERABILITY
======================================================================

[*] Payload being used:
    Username: ' OR '1'='1' --
    Password: anything

[+] Testing VULNERABLE app...
    ✓ VULNERABLE: SQL Injection SUCCESSFUL (attacker logged in)

[+] Testing SECURE app...
    ✓ PROTECTED: SQL Injection BLOCKED (prepared statements working)

----------------------------------------------------------------------
RESULTS:
----------------------------------------------------------------------
Vulnerable App: FAIL (Vulnerable)
  └─ SQL Injection succeeded - unauthorized access granted

Secure App: PASS (Protected)
  └─ Prepared statements prevented SQL Injection

✓ TEST PASSED: Vulnerability confirmed in vulnerable app, fixed in secure app
======================================================================
```

## Troubleshooting

### "Connection refused" Error

**Problem**: Cannot connect to localhost

- Ensure web server (Apache, Nginx, etc.) is running
- Ensure PHP is configured to serve the applications
- Verify port number if using non-standard ports

**Solution**:

```bash
# Modify test URLs if needed
# Change: http://localhost/vuln-web-app/
# To:     http://localhost/vuln-web-app/ (example)
```

### "Database connection failed" Error

**Problem**: Cannot connect to MySQL

- Ensure MySQL server is running
- Verify credentials in `.env` file
- Check database names match

**Solution**:

```bash
# Test database connection
mysql -u root -p -h localhost -e "SHOW DATABASES;"
```

### Session/Cookie Issues

**Problem**: Tests failing due to session handling

- Clear browser cookies
- Ensure session directory is writable
- Verify PHP session settings

### Tests Show "INCONCLUSIVE"

**Problem**: Results cannot be determined

- Verify both web applications are running
- Check URL paths are accessible
- Ensure sample data exists in databases
- Review application logs for errors

### How to Check Application Status

```bash
# Check if vulnerable app is accessible
curl -I http://localhost/vuln-web-app/login.php

# Check if secure app is accessible
curl -I http://localhost/secure-web-app/login.php

# Test database connectivity
mysql -u root -p -h localhost vuln_web_app -e "SELECT COUNT(*) FROM users;"
```

## Customizing Tests

### Change Application URLs

Edit the URL variables at the top of each test file:

```php
private $vuln_url = "http://localhost/vuln-web-app/login.php";
private $secure_url = "http://localhost/secure-web-app/login.php";
```

### Modify Database Connection Details

Edit the connection parameters:

```php
private $db_user = "root";
private $db_pass = "";
private $db_host = "localhost";
```

### Adjust Brute Force Attempt Count

Modify `$max_attempts` in `test_brute_force.php`:

```php
private $max_attempts = 10;  // Change to 20, 50, etc.
```

## Integration with CI/CD

These tests can be integrated into continuous integration pipelines:

```bash
#!/bin/bash
# Run tests and capture exit code
php /path/to/tests/run_all_tests.php

# Check result
if [ $? -eq 0 ]; then
    echo "All security tests passed"
    exit 0
else
    echo "Security tests failed"
    exit 1
fi
```

## For Project Submission

1. ✓ Ensure all tests pass (5/5)
2. Include test scripts in repository
3. Document any custom URLs or configuration changes
4. Include this README with instructions
5. Add test results screenshot/output to documentation

## Support

For issues or questions about the tests:

1. Check the troubleshooting section above
2. Review the test file comments
3. Verify both applications are running correctly
4. Check MySQL database connectivity
5. Review web server logs for errors

---

**Last Updated**: May 2026  
**Project**: SSDD-CCA - Secure Software Design & Development  
**Institution**: NED University of Engineering & Technology
