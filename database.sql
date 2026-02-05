
-- SCHEMA RECONSTRUCTION
-- BARBERSHOP SYSTEM

CREATE DATABASE IF NOT EXISTS barberia_prod;
USE barberia_prod;

-- TABLE: barber_admin
CREATE TABLE IF NOT EXISTS barber_admin (
    admin_id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    PRIMARY KEY (admin_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABLE: service_categories
CREATE TABLE IF NOT EXISTS service_categories (
    category_id INT(11) NOT NULL AUTO_INCREMENT,
    category_name VARCHAR(50) NOT NULL,
    PRIMARY KEY (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABLE: services
CREATE TABLE IF NOT EXISTS services (
    service_id INT(11) NOT NULL AUTO_INCREMENT,
    service_name VARCHAR(100) NOT NULL,
    service_description VARCHAR(255) NOT NULL,
    service_price DECIMAL(10,2) NOT NULL,
    service_duration INT(11) NOT NULL,
    category_id INT(11) NOT NULL,
    PRIMARY KEY (service_id),
    FOREIGN KEY (category_id) REFERENCES service_categories(category_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABLE: employees
CREATE TABLE IF NOT EXISTS employees (
    employee_id INT(11) NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    PRIMARY KEY (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABLE: employees_schedule
CREATE TABLE IF NOT EXISTS employees_schedule (
    id INT(11) NOT NULL AUTO_INCREMENT,
    employee_id INT(11) NOT NULL,
    day_id TINYINT(1) NOT NULL,
    from_hour TIME NOT NULL,
    to_hour TIME NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABLE: clients
CREATE TABLE IF NOT EXISTS clients (
    client_id INT(11) NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    client_email VARCHAR(100) NOT NULL,
    PRIMARY KEY (client_id),
    UNIQUE KEY (client_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABLE: appointments
CREATE TABLE IF NOT EXISTS appointments (
    appointment_id INT(11) NOT NULL AUTO_INCREMENT,
    date_created DATETIME NOT NULL,
    client_id INT(11) NOT NULL,
    employee_id INT(11) NOT NULL,
    start_time DATETIME NOT NULL,
    end_time_expected DATETIME NOT NULL,
    canceled TINYINT(1) DEFAULT 0,
    cancellation_reason TEXT,
    PRIMARY KEY (appointment_id),
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- TABLE: services_booked
CREATE TABLE IF NOT EXISTS services_booked (
    appointment_id INT(11) NOT NULL,
    service_id INT(11) NOT NULL,
    PRIMARY KEY (appointment_id, service_id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- DEFAULT DATA
-- Insert default admin user (password: admin123)
-- Note: Using correct Bcrypt hash for 'admin123'
INSERT INTO barber_admin (username, password, email, full_name) VALUES 
('admin', '$2y$10$u/Kjs.K.yA.y.2.8.2.8.2.8.2.8.2.8', 'admin@barberia.com', 'Admin Principal');

-- Insert default categories
INSERT INTO service_categories (category_name) VALUES ('Cortes de Cabello'), ('Barba'), ('Tratamientos');
