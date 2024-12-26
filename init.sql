CREATE TABLE posts (
    uuid TEXT PRIMARY KEY,
    author_uuid TEXT,
    title TEXT,
    text TEXT,
    FOREIGN KEY (author_uuid) REFERENCES users (uuid)
);

CREATE TABLE likes (
    uuid TEXT PRIMARY KEY,
    entity_uuid TEXT NOT NULL,
    user_uuid TEXT NOT NULL,
    UNIQUE(entity_uuid, user_uuid),
    FOREIGN KEY (user_uuid) REFERENCES users (uuid)
);

CREATE TABLE comments (
    uuid TEXT PRIMARY KEY,
    post_uuid TEXT,
    author_uuid TEXT,
    text TEXT,
    FOREIGN KEY (post_uuid) REFERENCES posts (uuid),
    FOREIGN KEY (author_uuid) REFERENCES users (uuid)
);

CREATE TABLE users (
    uuid TEXT PRIMARY KEY,
    first_name TEXT,
    last_name TEXT
);
