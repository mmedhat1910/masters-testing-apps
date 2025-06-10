# Project 3: Node.js CMS

This project is a simple Content Management System (CMS) built with Node.js, Express, and SQLite.

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

### To Stop and Clean Up

Run the following command in the `project3` directory:
```bash
docker-compose down --volumes
```