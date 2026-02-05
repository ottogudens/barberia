CREATE TABLE IF NOT EXISTS expenses (
    expense_id INT NOT NULL AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL, -- e.g., 'Utilidades', 'Proveedores', 'Arriendo', 'Sueldos', 'Otros'
    description TEXT,
    expense_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (expense_id),
    KEY (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
