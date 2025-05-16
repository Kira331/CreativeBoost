-- SQLite does not support CREATE DATABASE, so this line is removed.

-- Use statements are not needed in SQLite.

CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password TEXT NOT NULL
);
