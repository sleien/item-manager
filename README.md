# Item Manager
This project is a simple web application for managing a list of items that can be shared among multiple users.

## Getting Started
To get started with this project, you'll need to have a web server running with PHP and a MySQL database. Here are the steps to set it up:

1. Clone this repository to your web server's document root (e.g. /var/www/html).
1. Create a MySQL database and user for this project. You can use the SQL script provided in `sql/init.sql` to set up the necessary tables. You can use `sql/create_db_user.sql` to set up the permissions for a user.
1. Create a file called `config.php` in the root of the project with your MySQL database credentials, like this:

```php
<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'item_manager');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

?>
```
4. Start your web server and navigate to the project's URL to use the application.

## Features
### Login and Registration
The application allows users to create accounts and log in to the system. Passwords are hashed for security.

### Item Management
Logged-in users can add, edit, and delete items in their list. Each item has a name, description, and quantity. Users can also share items with other users by entering their email address.

### Security
The application prevents users from viewing or modifying items that they do not have permission to access. It also uses prepared statements to prevent SQL injection attacks.

### Cleaning up User-Item Connections
Users can also unshare the items they have shared. The item will not be deleted, just the connection of the user and the item will be removed.

### Authors
Noah Schneider - Inital work

### License
This project is licensed under the MIT License - see the LICENSE.md file for details.