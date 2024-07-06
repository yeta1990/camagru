CREATE TABLE images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    url TEXT NOT NULL,
    caption TEXT,
    user_id INTEGER NOT NULL,
    likes TEXT DEFAULT '[]',
    date TEXT NOT NULL
);