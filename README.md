# 0. vul-app1
- SQL injection.
- Deserialization vulnerability

# 1. Project 1: RCE & Container Escape
This project combines a critical container escape vulnerability, a server-side template injection leading to remote code execution, and a classic SQL injection. It demonstrates vulnerabilities at both the application and infrastructure levels.
Vulnerabilities Included:
- runc Container Escape (CVE-2024-21626): A vulnerability in runc (the underlying container runtime used by Docker) that allows a container to escape its sandbox and gain access to the host filesystem. This is an infrastructure-level vulnerability. We will provide a Proof of Concept to be run against a host with a vulnerable runc version.
- Peering Manager-style SSTI RCE (CVE-2024-28114): A Server-Side Template Injection (SSTI) vulnerability. The application improperly uses a template engine (Jinja2) to render user-supplied content, allowing an attacker to execute arbitrary code on the server.
- SQL Injection (Generic): A classic vulnerability where the application constructs a database query using unsanitized user input, allowing an attacker to manipulate the query to bypass authentication or exfiltrate data.

project1/
├── app/
│   ├── app.py
│   ├── requirements.txt
│   └── templates/
│       └── index.html
├── docker-compose.yml
├── Dockerfile
└── README.md

--- 
# Project 2: CSRF, XSS & Command Injection
This project is a simple PHP "Admin Panel & Guestbook" application. It is intentionally designed to be vulnerable to Cross-Site Request Forgery (CSRF) leading to privilege escalation, a Stored Cross-Site Scripting (XSS) flaw, and a classic Command Injection.

Vulnerabilities Included:
- CSRF + Privilege Escalation (CVE-2024-23831 style): The "Add Admin User" form in the admin panel lacks any CSRF protection (like anti-CSRF tokens). An attacker can craft a malicious webpage that, when visited by a logged-in administrator, will silently submit a request to create a new admin user controlled by the attacker.
- Stored XSS (CVE-2023-1119-1 style): The guestbook feature fails to sanitize user comments before storing them in the database. An attacker can submit a comment containing a malicious JavaScript payload. This script is then executed in the browser of anyone who views the comment, such as an administrator in the admin panel.
- Command Injection (Generic): The application provides a network utility to "ping" an IP address. It constructs and executes a shell command directly with user-supplied input, allowing an attacker to inject arbitrary system commands.

project2/
├── app/
│   ├── admin.php
│   ├── db.php
│   ├── guestbook.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── network.php
│   └── style.css
├── db_setup/
│   └── init.sql
├── docker-compose.yml
├── Dockerfile
├── cloud.json
└── README.md

