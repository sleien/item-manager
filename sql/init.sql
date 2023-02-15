CREATE DATABASE item_manager;

USE item_manager;

CREATE TABLE users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE items (
  id INT(11) NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  price DECIMAL(10, 2),
  PRIMARY KEY (id)
);

CREATE TABLE user_items (
  user_id INT(11) NOT NULL,
  item_id INT(11) NOT NULL,
  PRIMARY KEY (user_id, item_id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (item_id) REFERENCES items(id)
);

ALTER TABLE items ADD COLUMN quantity INT DEFAULT 0;
ALTER TABLE items ADD COLUMN main_user_id INT(11);
ALTER TABLE items ADD wishlist BOOLEAN NOT NULL DEFAULT 0;
ALTER TABLE items ADD link VARCHAR(255);
