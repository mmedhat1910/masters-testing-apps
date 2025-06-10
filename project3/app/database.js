const sqlite3 = require('sqlite3').verbose();
const db = new sqlite3.Database('./db/cms.db');

db.serialize(() => {
  // Create users table and default admin
  db.run(
    'CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, username TEXT, password TEXT)'
  );
  db.run(
    "INSERT OR IGNORE INTO users (id, username, password) VALUES (1, 'admin', 'complexpassword')"
  );

  // Create pages table and a default page
  db.run(
    'CREATE TABLE IF NOT EXISTS pages (id INTEGER PRIMARY KEY, title TEXT, content TEXT)'
  );
  db.run(
    "INSERT OR IGNORE INTO pages (id, title, content) VALUES (1, 'Home Page', 'Welcome to the vulnerable CMS!')"
  );
  db.run(
    "INSERT OR IGNORE INTO pages (id, title, content) VALUES (2, 'About Us', 'This is the about page.')"
  );
});

module.exports = db;
