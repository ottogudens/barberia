-- SAAS MIGRATION SCRIPT
-- Phase 1: Structural Changes

USE barberia_prod;

-- 1. Create table for Tenants (Barbershops)
CREATE TABLE IF NOT EXISTS tenants (
    tenant_id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) NOT NULL UNIQUE,
    owner_email VARCHAR(100) NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Create Super Admin table (Apps Owners)
CREATE TABLE IF NOT EXISTS super_admins (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create Default Tenant to hold existing data
-- We check if it exists to avoid duplicates on re-run
INSERT INTO tenants (name, slug, owner_email) 
SELECT * FROM (SELECT 'Gold Luk Barbershop', 'gold-luk', 'admin@goldluk.com') AS tmp
WHERE NOT EXISTS (
    SELECT slug FROM tenants WHERE slug = 'gold-luk'
) LIMIT 1;

-- Store the default tenant ID in a variable for use
SET @default_tenant_id = (SELECT tenant_id FROM tenants WHERE slug = 'gold-luk');

-- 4. Add tenant_id to existing tables
-- We use a stored procedure-like block or separate ALTER statements. 
-- For simplicity in this script, we assume tables exists.

-- Helper procedure to safely add column if not exists (MySQL doesn't have IF NOT EXISTS for columns easily in one line without procedure)
-- Instead, we will run ADD COLUMN. If it fails due to duplicate, we ignore error or use a safe method.
-- Safe method: Check simple ADD, if it exists it errors but manageable. Better approach for script:

-- barber_admin
SET @dbname = DATABASE();
SET @tablename = "barber_admin";
SET @columnname = "tenant_id";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " INT(11) DEFAULT NULL")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Update existing admins to default tenant
UPDATE barber_admin SET tenant_id = @default_tenant_id WHERE tenant_id IS NULL;
-- Enforce Data Integrity
ALTER TABLE barber_admin MODIFY COLUMN tenant_id INT(11) NOT NULL;
ALTER TABLE barber_admin ADD CONSTRAINT fk_admin_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);


-- service_categories
SET @tablename = "service_categories";
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @columnname)) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " INT(11) DEFAULT NULL")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

UPDATE service_categories SET tenant_id = @default_tenant_id WHERE tenant_id IS NULL;
ALTER TABLE service_categories MODIFY COLUMN tenant_id INT(11) NOT NULL;
ALTER TABLE service_categories ADD CONSTRAINT fk_categories_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);


-- services
SET @tablename = "services";
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @columnname)) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " INT(11) DEFAULT NULL")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

UPDATE services SET tenant_id = @default_tenant_id WHERE tenant_id IS NULL;
ALTER TABLE services MODIFY COLUMN tenant_id INT(11) NOT NULL;
ALTER TABLE services ADD CONSTRAINT fk_services_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);


-- employees
SET @tablename = "employees";
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @columnname)) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " INT(11) DEFAULT NULL")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

UPDATE employees SET tenant_id = @default_tenant_id WHERE tenant_id IS NULL;
ALTER TABLE employees MODIFY COLUMN tenant_id INT(11) NOT NULL;
ALTER TABLE employees ADD CONSTRAINT fk_employees_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);


-- clients
SET @tablename = "clients";
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @columnname)) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " INT(11) DEFAULT NULL")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

UPDATE clients SET tenant_id = @default_tenant_id WHERE tenant_id IS NULL;
ALTER TABLE clients MODIFY COLUMN tenant_id INT(11) NOT NULL;
ALTER TABLE clients ADD CONSTRAINT fk_clients_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);


-- appointments
SET @tablename = "appointments";
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE (table_name = @tablename) AND (table_schema = @dbname) AND (column_name = @columnname)) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " INT(11) DEFAULT NULL")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

UPDATE appointments SET tenant_id = @default_tenant_id WHERE tenant_id IS NULL;
ALTER TABLE appointments MODIFY COLUMN tenant_id INT(11) NOT NULL;
ALTER TABLE appointments ADD CONSTRAINT fk_appointments_tenant FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id);

-- Note: employees_schedule does NOT need tenant_id directly if it's strictly child of employees, 
-- but for query speed/simplicity sometimes it's added. 
-- For strict normalization, we reach it via employee_id -> tenant_id.
-- Let's stick to strict normalization for schedules for now.

SELECT "Migration Completed Successfully";
