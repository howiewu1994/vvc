CREATE DATABASE IF NOT EXISTS vvc DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE mysql;

DELETE FROM user WHERE password = '' AND user != 'root';

USE vvc;

CREATE USER IF NOT EXISTS vvc_admin@localhost identified by '123';
GRANT SELECT, INSERT, UPDATE, DELETE
ON vvc.* to vvc_admin@localhost identified by '123';

DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id int primary key auto_increment,
    username varchar(20) not null unique,
    password varchar(100) not null,
    role_id int not null,
    created_at datetime default current_timestamp
);
