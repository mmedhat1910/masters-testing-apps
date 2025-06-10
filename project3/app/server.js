const express = require('express');
const bodyParser = require('body-parser');
const cookieSession = require('cookie-session');
const db = require('./database');
const app = express();

app.set('view engine', 'ejs');
app.use(express.static('public'));
app.use(bodyParser.urlencoded({ extended: true }));
app.use(
  cookieSession({
    name: 'session',
    keys: ['key1', 'key2'],
    maxAge: 24 * 60 * 60 * 1000,
  })
);

// Middleware to check if user is admin
const requireAdmin = (req, res, next) => {
  if (req.session.isLoggedIn) {
    next();
  } else {
    res.redirect('/login');
  }
};

// --- Public Routes ---
app.get('/', (req, res) => {
  db.get('SELECT * FROM pages WHERE id = 1', [], (err, page) => {
    res.render('index', { page });
  });
});

app.get('/login', (req, res) => res.render('login', { error: null }));
app.get('/logout', (req, res) => {
  req.session = null;
  res.redirect('/');
});

app.post('/login', (req, res) => {
  const { username, password } = req.body;
  // UNSAFE: Raw query string concatenation.
  const sql = `SELECT * FROM users WHERE username = '${username}' AND password = '${password}'`;
  db.get(sql, [], (err, user) => {
    if (user) {
      req.session.isLoggedIn = true;
      res.redirect('/dashboard');
    } else {
      res.render('login', { error: 'Invalid credentials!' });
    }
  });
});

// --- Admin Routes ---
app.get('/dashboard', requireAdmin, (req, res) => {
  db.all('SELECT * FROM pages', [], (err, pages) => {
    res.render('dashboard', { pages });
  });
});

app.get('/edit/:id', requireAdmin, (req, res) => {
  db.get('SELECT * FROM pages WHERE id = ?', [req.params.id], (err, page) => {
    res.render('edit-page', { page });
  });
});

app.post('/edit/:id', requireAdmin, (req, res) => {
  const { title, content } = req.body;
  db.run(
    'UPDATE pages SET title = ?, content = ? WHERE id = ?',
    [title, content, req.params.id],
    () => {
      res.redirect('/dashboard');
    }
  );
});

app.listen(3000, () => console.log('Server running on port 3000'));
