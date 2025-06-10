import os
import sqlite3
from flask import Flask, request, render_template, render_template_string, g

app = Flask(__name__)
DATABASE = 'database.db'

def get_db():
    db = getattr(g, '_database', None)
    if db is None:
        db = g._database = sqlite3.connect(DATABASE)
    return db

@app.teardown_appcontext
def close_connection(exception):
    db = getattr(g, '_database', None)
    if db is not None:
        db.close()

def init_db():
    if not os.path.exists(DATABASE):
        with app.app_context():
            db = get_db()
            with app.open_resource('schema.sql', mode='r') as f:
                db.cursor().executescript(f.read())
            db.commit()
            # Add some dummy data
            cursor = db.cursor()
            cursor.execute("INSERT INTO users (username, password) VALUES (?, ?)", ('admin', 'password123'))
            cursor.execute("INSERT INTO users (username, password) VALUES (?, ?)", ('alice', 'supersecret'))
            db.commit()
            print("Initialized the database.")

# Helper to create schema.sql in memory if it doesn't exist
def create_schema_if_not_exists():
    if not os.path.exists('schema.sql'):
        with open('schema.sql', 'w') as f:
            f.write("""
            DROP TABLE IF EXISTS users;
            CREATE TABLE users (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              username TEXT NOT NULL,
              password TEXT NOT NULL
            );
            """)

@app.route('/')
def index():
    return render_template('index.html')

# VULNERABILITY 2: Peering Manager-style SSTI RCE (CVE-2024-28114)
@app.route('/render', methods=['POST'])
def render():
    template_content = request.form.get('template', '')
    # Directly rendering user-supplied string is dangerous!
    # This is the core of the SSTI vulnerability.
    try:
        rendered_output = render_template_string(template_content)
        return rendered_output
    except Exception as e:
        return f"Template Error: {e}", 400

# VULNERABILITY 3: SQL Injection
@app.route('/search')
def search():
    query = request.args.get('query', '')
    db = get_db()
    cursor = db.cursor()

    # Unsafe query construction using f-string
    # This is the SQL Injection vulnerability.
    sql_query = f"SELECT username FROM users WHERE username = '{query}'"
    
    try:
        cursor.execute(sql_query)
        users = cursor.fetchall()
        
        results = "<h3>Search Results:</h3><ul>"
        if users:
            for user in users:
                results += f"<li>{user[0]}</li>"
        else:
            results += "<li>No users found.</li>"
        results += "</ul>"
        return results
    except sqlite3.Error as e:
        return f"<h1>Database Error</h1><p>Your query was: {sql_query}</p><p>Error: {e}</p>", 500

if __name__ == '__main__':
    create_schema_if_not_exists()
    init_db()
    app.run(host='0.0.0.0', port=5000)