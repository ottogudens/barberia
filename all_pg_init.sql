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
-- Insert default categories safely
INSERT INTO service_categories (category_name)
SELECT 'Cortes de Cabello' WHERE NOT EXISTS (SELECT 1 FROM service_categories WHERE category_name = 'Cortes de Cabello');
INSERT INTO service_categories (category_name)
SELECT 'Barba' WHERE NOT EXISTS (SELECT 1 FROM service_categories WHERE category_name = 'Barba');
INSERT INTO service_categories (category_name)
SELECT 'Tratamientos' WHERE NOT EXISTS (SELECT 1 FROM service_categories WHERE category_name = 'Tratamientos');
-- SAAS MIGRATION SCRIPT (PostgreSQL Version)
-- Phase 1: Structural Changes

-- 1. Create table for Tenants (Barbershops)
CREATE TABLE IF NOT EXISTS tenants (
    tenant_id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    owner_email VARCHAR(100) NOT NULL,
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'suspended')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Create Super Admin table (Apps Owners)
CREATE TABLE IF NOT EXISTS super_admins (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100)
);

-- 3. Create Default Tenant
INSERT INTO tenants (name, slug, owner_email) 
SELECT 'Gold Luk Barbershop', 'gold-luk', 'admin@goldluk.com'
WHERE NOT EXISTS (SELECT 1 FROM tenants WHERE slug = 'gold-luk');

-- 4. Add tenant_id to existing tables
-- In PostgreSQL, we can use DO blocks for safe migrations if needed, 
-- but since this is a clean migration, we can just define them.

-- Function to safely add tenant_id Column
DO $$ 
BEGIN 
    -- barber_admin
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='barber_admin' AND column_name='tenant_id') THEN
        ALTER TABLE barber_admin ADD COLUMN tenant_id INT;
    END IF;

    -- service_categories
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='service_categories' AND column_name='tenant_id') THEN
        ALTER TABLE service_categories ADD COLUMN tenant_id INT;
    END IF;

    -- services
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='services' AND column_name='tenant_id') THEN
        ALTER TABLE services ADD COLUMN tenant_id INT;
    END IF;

    -- employees
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='employees' AND column_name='tenant_id') THEN
        ALTER TABLE employees ADD COLUMN tenant_id INT;
    END IF;

    -- clients
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='clients' AND column_name='tenant_id') THEN
        ALTER TABLE clients ADD COLUMN tenant_id INT;
    END IF;

    -- appointments
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='appointments' AND column_name='tenant_id') THEN
        ALTER TABLE appointments ADD COLUMN tenant_id INT;
    END IF;
END $$;

-- Update existing data to default tenant
UPDATE barber_admin SET tenant_id = (SELECT tenant_id FROM tenants WHERE slug = 'gold-luk') WHERE tenant_id IS NULL;
UPDATE service_categories SET tenant_id = (SELECT tenant_id FROM tenants WHERE slug = 'gold-luk') WHERE tenant_id IS NULL;
UPDATE services SET tenant_id = (SELECT tenant_id FROM tenants WHERE slug = 'gold-luk') WHERE tenant_id IS NULL;
UPDATE employees SET tenant_id = (SELECT tenant_id FROM tenants WHERE slug = 'gold-luk') WHERE tenant_id IS NULL;
UPDATE clients SET tenant_id = (SELECT tenant_id FROM tenants WHERE slug = 'gold-luk') WHERE tenant_id IS NULL;
UPDATE appointments SET tenant_id = (SELECT tenant_id FROM tenants WHERE slug = 'gold-luk') WHERE tenant_id IS NULL;

-- Enforce Constraints Safely
DO $$ 
BEGIN 
    -- barber_admin
    ALTER TABLE barber_admin ALTER COLUMN tenant_id SET NOT NULL;
    IF NOT EXISTS (SELECT 1 FROM information_schema.table_constraints WHERE constraint_name = 'fk_admin_tenant') THEN
        ALTER TABLE barber_admin ADD CONSTRAINT fk_admin_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);
    END IF;

    -- service_categories
    ALTER TABLE service_categories ALTER COLUMN tenant_id SET NOT NULL;
    IF NOT EXISTS (SELECT 1 FROM information_schema.table_constraints WHERE constraint_name = 'fk_categories_tenant') THEN
        ALTER TABLE service_categories ADD CONSTRAINT fk_categories_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);
    END IF;

    -- services
    ALTER TABLE services ALTER COLUMN tenant_id SET NOT NULL;
    IF NOT EXISTS (SELECT 1 FROM information_schema.table_constraints WHERE constraint_name = 'fk_services_tenant') THEN
        ALTER TABLE services ADD CONSTRAINT fk_services_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);
    END IF;

    -- employees
    ALTER TABLE employees ALTER COLUMN tenant_id SET NOT NULL;
    IF NOT EXISTS (SELECT 1 FROM information_schema.table_constraints WHERE constraint_name = 'fk_employees_tenant') THEN
        ALTER TABLE employees ADD CONSTRAINT fk_employees_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);
    END IF;

    -- clients
    ALTER TABLE clients ALTER COLUMN tenant_id SET NOT NULL;
    IF NOT EXISTS (SELECT 1 FROM information_schema.table_constraints WHERE constraint_name = 'fk_clients_tenant') THEN
        ALTER TABLE clients ADD CONSTRAINT fk_clients_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);
    END IF;

    -- appointments
    ALTER TABLE appointments ALTER COLUMN tenant_id SET NOT NULL;
    IF NOT EXISTS (SELECT 1 FROM information_schema.table_constraints WHERE constraint_name = 'fk_appointments_tenant') THEN
        ALTER TABLE appointments ADD CONSTRAINT fk_appointments_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);
    END IF;
END $$;
-- FINANCIAL MIGRATION SCRIPT (PostgreSQL Version)

-- 1. Modify employees table
ALTER TABLE employees ADD COLUMN commission_percentage DECIMAL(5,2) DEFAULT 0.00;

-- 2. Modify appointments table
ALTER TABLE appointments ADD COLUMN is_paid SMALLINT DEFAULT 0;
ALTER TABLE appointments ADD COLUMN payment_method VARCHAR(50) DEFAULT NULL;
ALTER TABLE appointments ADD COLUMN total_amount DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE appointments ADD COLUMN paid_at TIMESTAMP DEFAULT NULL;

-- 3. Create employee_payouts table
CREATE TABLE IF NOT EXISTS employee_payouts (
    payout_id SERIAL PRIMARY KEY,
    tenant_id INT NOT NULL,
    employee_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE
);
-- OFFERS MIGRATION SCRIPT (PostgreSQL Version)

CREATE TABLE IF NOT EXISTS offers (
    offer_id SERIAL PRIMARY KEY,
    tenant_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    discount_percentage INT DEFAULT 0,
    start_date DATE,
    end_date DATE,
    active SMALLINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE
);
-- GALLERY MIGRATION SCRIPT (PostgreSQL Version)

CREATE TABLE IF NOT EXISTS gallery_images (
    image_id SERIAL PRIMARY KEY,
    tenant_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE
);
-- WEBSITE SETTINGS MIGRATION SCRIPT (PostgreSQL Version)

CREATE TABLE IF NOT EXISTS website_settings (
    setting_id SERIAL PRIMARY KEY,
    tenant_id INT NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    UNIQUE (tenant_id, setting_key),
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE
);

-- Insert default settings for existing tenant (Gold Luk)
DO $$ 
DECLARE 
    v_tenant_id INT;
BEGIN
    SELECT tenant_id INTO v_tenant_id FROM tenants WHERE slug = 'gold-luk';

    IF v_tenant_id IS NOT NULL THEN
        INSERT INTO website_settings (tenant_id, setting_key, setting_value) VALUES
        (v_tenant_id, 'hero_title', 'Un Corte de Cabello para cada Ocasión'),
        (v_tenant_id, 'hero_subtitle', 'Somos la mejor peluquería del Mundo, te queremos bien !!\nAcá te puedes sentir muy cómodo.'),
        (v_tenant_id, 'about_title', 'Somos tu Peluquería Desde 1982'),
        (v_tenant_id, 'about_text', 'Somos una peluquería enfocada en nuestros clientes, antes de empezar analizamos tu fisonomía para recomendarte tu mejor corte. Como siempre respetando como máxima tu criterio, tus gustos y preferencias ante todos.'),
        (v_tenant_id, 'contact_address', 'Calle 90 N 23 24\nCali, Colombia'),
        (v_tenant_id, 'contact_email', 'hola@configuroweb.com'),
        (v_tenant_id, 'contact_phone', '+57 316 243 00 81'),
        (v_tenant_id, 'primary_color', '#D4AF37'),
        (v_tenant_id, 'secondary_color', '#111111')
        ON CONFLICT (tenant_id, setting_key) DO NOTHING;
    END IF;
END $$;
