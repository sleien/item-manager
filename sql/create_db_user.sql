CREATE USER 'item_manager'@'%' IDENTIFIED VIA mysql_native_password USING '***';GRANT USAGE ON *.* TO 'item_manager'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;GRANT ALL PRIVILEGES ON `item\_manager\_%`.* TO 'item_manager'@'%';

GRANT ALL PRIVILEGES ON `item_manager`.* TO 'item_manager'@'%' WITH GRANT OPTION;
