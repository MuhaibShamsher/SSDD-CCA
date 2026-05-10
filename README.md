# SSDD-CCA: Secure Software Design & Development

**NED University of Engineering & Technology | CT-477 | Spring 2026**

Demonstrates secure coding by building a PHP/MySQL web app with 5 intentional vulnerabilities and their fixes, plus automated tests.

---

## 1. Quick Start (XAMPP)

```powershell
Copy-Item -Recurse D:\SSDD-CCA\vuln-web-app C:\xampp\htdocs\
Copy-Item -Recurse D:\SSDD-CCA\secure-web-app C:\xampp\htdocs\
Copy-Item D:\SSDD-CCA\.env C:\xampp\htdocs\.env
```

1. Open XAMPP Control Panel and start Apache and MySQL.
2. Open phpMyAdmin at http://localhost/phpmyadmin.
3. Import `D:\SSDD-CCA\setup_databases.sql`.
4. Open the apps:
   - Vulnerable: http://localhost/vuln-web-app/login.php
   - Secure: http://localhost/secure-web-app/login.php
5. Use `admin` / `admin123` to sign in.

---

## 2. Project Structure

```
SSDD-CCA/
├── .env, setup_databases.sql
├── vuln-web-app/          # Vulnerable version
├── secure-web-app/        # Fixed version
└── tests/                 # 5 automated tests + runner
    ├── run_all_tests.php
    ├── test_sql_injection.php
    ├── test_insecure_passwords.php
    ├── test_idor.php
    ├── test_xss.php
    ├── test_brute_force.php
    └── TESTS_README.md
```

---

## 3. Deliverables

- Vulnerable web app with 5 intentional flaws
- Secure web app with the fixes applied
- Automated test suite with 5 tests
- Database setup script
- Project documentation
- Demo video to record

---

## 4. Technologies

- Backend: PHP 7.0+
- Database: MySQL / MariaDB
- Frontend: HTML5, CSS3
- Testing: cURL and automated PHP tests

---

## 5. Credentials

Test users for both apps:

- `admin` / `admin123`
- `user1` / `password1`
- `user2` / `password2`

---

## 6. Vulnerabilities Table (5 Total)

| #   | Vulnerability             | Vulnerability File          | Vulnerability                                      | Attack                                               | Fix                                               |
| --- | ------------------------- | --------------------------- | -------------------------------------------------- | ---------------------------------------------------- | ------------------------------------------------- |
| 1   | SQL Injection             | `vuln-web-app/login.php`    | Direct string concatenation in SQL login query     | Use `' OR '1'='1' -- ` to bypass authentication      | Use prepared statements                           |
| 2   | Insecure Password Storage | `vuln-web-app/register.php` | Plain text passwords stored and displayed          | Read the database or profile output to see passwords | Use `password_hash()` and never display passwords |
| 3   | IDOR                      | `vuln-web-app/profile.php`  | User-controlled `id` parameter selects any profile | Change `profile.php?id=2` to view another user       | Use the authenticated session user ID             |
| 4   | XSS                       | `vuln-web-app/form.php`     | Comment output is not escaped                      | Submit HTML or JavaScript in a comment               | Use `htmlspecialchars()` on output                |
| 5   | Brute Force               | `vuln-web-app/login.php`    | No limit on failed login attempts                  | Send repeated wrong passwords                        | Add rate limiting and cooldown tracking           |

### SQL Injection

- Vulnerability file: `vuln-web-app/login.php`
- Vulnerability: Direct string concatenation in the SQL login query
- Attack: Use `' OR '1'='1' -- ` to bypass authentication
- Fix: Use prepared statements

### Insecure Password Storage

- Vulnerability file: `vuln-web-app/register.php`
- Vulnerability: Plain text passwords are stored and displayed
- Attack: Read the database or profile output to see passwords
- Fix: Use `password_hash()` and never display passwords

### IDOR

- Vulnerability file: `vuln-web-app/profile.php`
- Vulnerability: User-controlled `id` parameter selects any profile
- Attack: Change `profile.php?id=2` to view another user
- Fix: Use the authenticated session user ID

### XSS

- Vulnerability file: `vuln-web-app/form.php`
- Vulnerability: Comment output is not escaped
- Attack: Submit HTML or JavaScript in a comment
- Fix: Use `htmlspecialchars()` on output

### Brute Force

- Vulnerability file: `vuln-web-app/login.php`
- Vulnerability: No limit on failed login attempts
- Attack: Send repeated wrong passwords
- Fix: Add rate limiting and cooldown tracking

---

## 7. Run Automated Tests

```powershell
cd D:\SSDD-CCA\tests
php run_all_tests.php
```

You can also run individual tests:

```powershell
php test_sql_injection.php
php test_insecure_passwords.php
php test_idor.php
php test_xss.php
php test_brute_force.php
```

---

## 8. Expected Outputs

The sample output below shows the result of running `run_all_tests.php`.
For detailed per-test output, run each test script individually.

```
======================================================================
SSDD-CCA PROJECT - SECURITY VULNERABILITY TEST SUITE
======================================================================

[Running Test: SQL Injection]
[PASS] TEST PASSED: Vulnerability confirmed in vulnerable app, fixed in secure app
File: `test_sql_injection.php`

[Running Test: Insecure Password Storage]
[PASS] TEST PASSED: Vulnerability confirmed in vulnerable app, fixed in secure app
File: `test_insecure_passwords.php`

[Running Test: IDOR (Insecure Direct Object Reference)]
[PASS] TEST PASSED: Vulnerability confirmed in vulnerable app, fixed in secure app
File: `test_idor.php`

[Running Test: Cross-Site Scripting (XSS)]
[PASS] TEST PASSED: Vulnerability confirmed in vulnerable app, fixed in secure app
File: `test_xss.php`

[Running Test: Brute Force Attack]
[PASS] TEST PASSED: Vulnerability confirmed in vulnerable app, fixed in secure app
File: `test_brute_force.php`

======================================================================
TEST EXECUTION SUMMARY
======================================================================
[PASS] SQL Injection (`test_sql_injection.php`)
[PASS] Insecure Password Storage (`test_insecure_passwords.php`)
[PASS] IDOR (Insecure Direct Object Reference) (`test_idor.php`)
[PASS] Cross-Site Scripting (XSS) (`test_xss.php`)
[PASS] Brute Force Attack (`test_brute_force.php`)

Results: 5 Passed | 0 Failed | 0 Errors (Total: 5)
======================================================================
[PASS] ALL TESTS PASSED!
```

---

## 9. Academic Information

- Institution: NED University of Engineering & Technology
- Department: Computer Science & Information Technology
- Course: CT-477 - Secure Software Design & Development
- Semester: Spring 2026
- Submission Deadline: May 17th, 2026
