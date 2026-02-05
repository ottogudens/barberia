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

-- Enforce Constraints
ALTER TABLE barber_admin ALTER COLUMN tenant_id SET NOT NULL;
ALTER TABLE barber_admin ADD CONSTRAINT fk_admin_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);

ALTER TABLE service_categories ALTER COLUMN tenant_id SET NOT NULL;
ALTER TABLE service_categories ADD CONSTRAINT fk_categories_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);

ALTER TABLE services ALTER COLUMN tenant_id SET NOT NULL;
ALTER TABLE services ADD CONSTRAINT fk_services_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);

ALTER TABLE employees ALTER COLUMN tenant_id SET NOT NULL;
ALTER TABLE employees ADD CONSTRAINT fk_employees_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);

ALTER TABLE clients ALTER COLUMN tenant_id SET NOT NULL;
ALTER TABLE clients ADD CONSTRAINT fk_clients_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);

ALTER TABLE appointments ALTER COLUMN tenant_id SET NOT NULL;
ALTER TABLE appointments ADD CONSTRAINT fk_appointments_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);
