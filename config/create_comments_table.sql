CREATE TABLE comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    comment TEXT,
    user_id INTEGER NOT NULL,
    image_id INTEGER NOT NULL,
    date TEXT NOT NULL
);