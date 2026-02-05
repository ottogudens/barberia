-- SCHEMA RECONSTRUCTION (PostgreSQL Version)
-- BARBERSHOP SYSTEM

-- Note: In Railway, the database is often already created for you.
-- Use this schema to create tables.

-- TABLE: barber_admin
CREATE TABLE IF NOT EXISTS barber_admin (
    admin_id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL
);

-- TABLE: service_categories
CREATE TABLE IF NOT EXISTS service_categories (
    category_id SERIAL PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL
);

-- TABLE: services
CREATE TABLE IF NOT EXISTS services (
    service_id SERIAL PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    service_description VARCHAR(255) NOT NULL,
    service_price DECIMAL(10,2) NOT NULL,
    service_duration INT NOT NULL,
    category_id INT NOT NULL,
    FOREIGN KEY (category_id) REFERENCES service_categories(category_id) ON DELETE CASCADE
);

-- TABLE: employees
CREATE TABLE IF NOT EXISTS employees (
    employee_id SERIAL PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL
);

-- TABLE: employees_schedule
CREATE TABLE IF NOT EXISTS employees_schedule (
    id SERIAL PRIMARY KEY,
    employee_id INT NOT NULL,
    day_id SMALLINT NOT NULL,
    from_hour TIME NOT NULL,
    to_hour TIME NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE
);

-- TABLE: clients
CREATE TABLE IF NOT EXISTS clients (
    client_id SERIAL PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    client_email VARCHAR(100) NOT NULL UNIQUE
);

-- TABLE: appointments
CREATE TABLE IF NOT EXISTS appointments (
    appointment_id SERIAL PRIMARY KEY,
    date_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    client_id INT NOT NULL,
    employee_id INT NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time_expected TIMESTAMP NOT NULL,
    canceled SMALLINT DEFAULT 0,
    cancellation_reason TEXT,
    FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE
);

-- TABLE: services_booked
CREATE TABLE IF NOT EXISTS services_booked (
    appointment_id INT NOT NULL,
    service_id INT NOT NULL,
    PRIMARY KEY (appointment_id, service_id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(appointment_id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(service_id) ON DELETE CASCADE
);

-- DEFAULT DATA
-- Insert default categories
INSERT INTO service_categories (category_name) VALUES ('Cortes de Cabello'), ('Barba'), ('Tratamientos');
