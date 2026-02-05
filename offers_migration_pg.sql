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
