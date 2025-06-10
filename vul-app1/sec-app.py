from flask import Flask, request, jsonify
import sqlite3
import base64
import json

app = Flask(__name__)

# In-memory SQLite DB for demo purposes
def init_db():
    conn = sqlite3.connect("users2.db")
    c = conn.cursor()
    c.execute("DROP TABLE IF EXISTS users")
    c.execute("""
        CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT,
            password TEXT
        )
    """)
    c.execute("INSERT INTO users (username, password) VALUES (?, ?)", ('alice', 'password123'))
    c.execute("INSERT INTO users (username, password) VALUES (?, ?)", ('bob', 'admin123'))
    conn.commit()
    conn.close()

@app.route('/users', methods=['GET'])
def get_users():
    username = request.args.get('username', '')
    conn = sqlite3.connect("users.db")
    c = conn.cursor()
    try:
        # Secure parameterized query
        c.execute("SELECT * FROM users WHERE username = ?", (username,))
        result = c.fetchall()
        users = [{"id": row[0], "username": row[1], "password": row[2]} for row in result]
        return jsonify(users)
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        conn.close()

@app.route('/broker', methods=['POST'])
def safe_deserialize():
    data = request.json.get("data", "")
    try:
        decoded = base64.b64decode(data)
        # Use JSON instead of pickle for safe deserialization
        obj = json.loads(decoded.decode('utf-8'))
        return jsonify({"result": obj})
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    init_db()
    app.run(host='0.0.0.0', port=5002)
