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
