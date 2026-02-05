USE barberia_prod;

-- 1. Modify employees table
ALTER TABLE employees ADD COLUMN commission_percentage DECIMAL(5,2) DEFAULT 0.00;

-- 2. Modify appointments table
ALTER TABLE appointments ADD COLUMN is_paid TINYINT(1) DEFAULT 0;
ALTER TABLE appointments ADD COLUMN payment_method VARCHAR(50) DEFAULT NULL;
ALTER TABLE appointments ADD COLUMN total_amount DECIMAL(10,2) DEFAULT 0.00;
ALTER TABLE appointments ADD COLUMN paid_at DATETIME DEFAULT NULL;

-- 3. Create employee_payouts table
CREATE TABLE IF NOT EXISTS employee_payouts (
    payout_id INT(11) NOT NULL AUTO_INCREMENT,
    tenant_id INT(11) NOT NULL,
    employee_id INT(11) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (payout_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
