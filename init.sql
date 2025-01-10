CREATE TABLE IF NOT EXISTS posts (
    uuid TEXT PRIMARY KEY,
    author_uuid TEXT,
    title TEXT,
    text TEXT,
    FOREIGN KEY (author_uuid) REFERENCES users (uuid)
);

CREATE TABLE IF NOT EXISTS comments (
    uuid TEXT PRIMARY KEY,
    post_uuid TEXT,
    author_uuid TEXT,
    text TEXT,
    FOREIGN KEY (post_uuid) REFERENCES posts (uuid),
    FOREIGN KEY (author_uuid) REFERENCES users (uuid)
);

CREATE TABLE IF NOT EXISTS users (
    uuid TEXT PRIMARY KEY,
    first_name TEXT,
    last_name TEXT
);

INSERT INTO users (uuid, first_name, last_name) VALUES
('53106969-d5b7-4156-a425-886a805977f8', 'Имя', 'Фамилия');

INSERT INTO posts (uuid, author_uuid, title, text) VALUES
('7ec2ad88-9455-45b0-9976-cf147acb6f34', '53106969-d5b7-4156-a425-886a805977f8', 'Заголовок поста', 'Текст поста');