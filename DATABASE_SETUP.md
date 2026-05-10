# Database Setup Instructions

This guide explains how to set up the databases for both the vulnerable and secure web applications.

## Prerequisites

- MySQL/MariaDB server installed and running
- Access to MySQL command line or a GUI client (e.g., phpMyAdmin, MySQL Workbench)
- Your MySQL root credentials

## Setup Steps

### Step 1: Configure Environment Variables

1. Copy `.env.sample` to `.env` (if not already done):

   **Windows:**

   ```bash
   copy .env.sample .env
   ```

   **Linux/Mac:**

   ```bash
   cp .env.sample .env
   ```

2. Edit `.env` file and update the database credentials:

   ```env
   DB_HOST_VULN=localhost
   DB_USER_VULN=root
   DB_PASSWORD_VULN=your_mysql_password
   DB_NAME_VULN=vuln_web_app

   DB_HOST_SECURE=localhost
   DB_USER_SECURE=root
   DB_PASSWORD_SECURE=your_mysql_password
   DB_NAME_SECURE=secure_web_app
   ```

### Step 2: Run Database Setup Script

#### Option A: Using MySQL Command Line

```bash
mysql -u root -p < setup_databases.sql
```

Then enter your MySQL password when prompted.

#### Option B: Using phpMyAdmin

1. Open phpMyAdmin in your browser (usually `http://localhost/phpmyadmin`)
2. Go to the **SQL** tab
3. Copy and paste the contents of `setup_databases.sql`
4. Click **Go** to execute

#### Option C: Using MySQL Workbench

1. Open MySQL Workbench
2. Create a new SQL query tab
3. Copy and paste the contents of `setup_databases.sql`
4. Click **Execute** (or press Ctrl+Shift+Enter)

### Step 3: Verify Database Setup

```bash
mysql -u root -p
```

Then run these commands to verify:

```sql
SHOW DATABASES;
USE vuln_web_app;
SELECT * FROM users;
SELECT * FROM comments;

USE secure_web_app;
SELECT * FROM users;
SELECT * FROM comments;
```

## Sample Data

Both databases come with pre-populated data:

### Users Table

**Vulnerable App (plain text passwords):**

- Username: `admin` / Password: `admin123`
- Username: `user1` / Password: `password1`
- Username: `user2` / Password: `password2`

**Secure App (hashed passwords):**

- Username: `admin` / Password: `admin123`
- Username: `user1` / Password: `password1`
- Username: `user2` / Password: `password2`

### Comments Table

Both databases have 3 sample comments pre-populated.

## Troubleshooting

### "Connection failed" Error

- Ensure MySQL server is running
- Verify credentials in `.env` file
- Check that the database user has proper permissions

### "Access Denied" Error

- Verify username and password in `.env`
- Ensure the user has privileges to create databases and tables

### Database Already Exists

- The SQL script uses `CREATE DATABASE IF NOT EXISTS`, so it won't overwrite existing databases
- To reset, run: `DROP DATABASE vuln_web_app; DROP DATABASE secure_web_app;` and re-run the setup script

## Next Steps

1. Ensure both web applications are running
2. Test vulnerable app at `http://localhost/vuln-web-app/`
3. Test secure app at `http://localhost/secure-web-app/`
4. Try the sample credentials to verify the setup works
