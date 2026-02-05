USE barberia_prod;

-- Add password column to clients table
ALTER TABLE clients ADD COLUMN password VARCHAR(255) DEFAULT NULL;
