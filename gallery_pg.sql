-- GALLERY MIGRATION SCRIPT (PostgreSQL Version)

CREATE TABLE IF NOT EXISTS gallery_images (
    image_id SERIAL PRIMARY KEY,
    tenant_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE
);
