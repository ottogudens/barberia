
-- Create user and grant privileges
CREATE USER IF NOT EXISTS 'barberia_user'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON barberia.* TO 'barberia_user'@'localhost';
FLUSH PRIVILEGES;
