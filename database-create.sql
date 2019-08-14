CREATE TABLE serial_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    serial CHAR(16) UNIQUE NOT NULL,
    user_email VARCHAR(255) DEFAULT NULL,
    product ENUM('mensal', 'anual') DEFAULT NULL
);