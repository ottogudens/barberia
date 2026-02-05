USE barberia_prod;

CREATE TABLE IF NOT EXISTS website_settings (
    setting_id INT(11) NOT NULL AUTO_INCREMENT,
    tenant_id INT(11) NOT NULL,
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT,
    PRIMARY KEY (setting_id),
    UNIQUE KEY unique_setting (tenant_id, setting_key),
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default settings for existing tenant (Gold Luk)
SET @tenant_id = (SELECT tenant_id FROM tenants WHERE slug = 'gold-luk');

INSERT IGNORE INTO website_settings (tenant_id, setting_key, setting_value) VALUES
(@tenant_id, 'hero_title', 'Un Corte de Cabello para cada Ocasión'),
(@tenant_id, 'hero_subtitle', 'Somos la mejor peluquería del Mundo, te queremos bien !!\nAcá te puedes sentir muy cómodo.'),
(@tenant_id, 'about_title', 'Somos tu Peluquería Desde 1982'),
(@tenant_id, 'about_text', 'Somos una peluquería enfocada en nuestros clientes, antes de empezar analizamos tu fisonomía para recomendarte tu mejor corte. Como siempre respetando como máxima tu criterio, tus gustos y preferencias ante todos.'),
(@tenant_id, 'contact_address', 'Calle 90 N 23 24\nCali, Colombia'),
(@tenant_id, 'contact_email', 'hola@configuroweb.com'),
(@tenant_id, 'contact_phone', '+57 316 243 00 81'),
(@tenant_id, 'primary_color', '#D4AF37'),
(@tenant_id, 'secondary_color', '#111111');
