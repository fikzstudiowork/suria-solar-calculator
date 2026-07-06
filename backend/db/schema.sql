-- Suria Solar Calculator — Database Schema
-- Import via phpMyAdmin or: mysql -u user -p dbname < schema.sql

CREATE TABLE IF NOT EXISTS suria_calc_leads (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME NOT NULL,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(191) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  state VARCHAR(60) NULL,
  property_type VARCHAR(60) NULL,
  monthly_bill DECIMAL(10,2) NULL,
  roof_exposure VARCHAR(30) NULL,
  recommended_kwp DECIMAL(6,2) NULL,
  est_monthly_savings DECIMAL(10,2) NULL,
  est_annual_savings DECIMAL(10,2) NULL,
  payback_years DECIMAL(5,2) NULL,
  consent_at DATETIME NOT NULL,
  privacy_policy_version VARCHAR(20) DEFAULT '1.0',
  utm_source VARCHAR(100) NULL,
  utm_campaign VARCHAR(100) NULL,
  status VARCHAR(20) DEFAULT 'new',
  ip_hash VARCHAR(64) NULL,
  INDEX idx_created_at (created_at),
  INDEX idx_status (status),
  INDEX idx_state (state)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS suria_calc_config (
  config_key VARCHAR(60) PRIMARY KEY,
  config_value VARCHAR(255) NOT NULL,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO suria_calc_config (config_key, config_value) VALUES
  ('tariff_rate', '0.571'),
  ('cost_per_kwp', '4200'),
  ('sun_hours', '4.5'),
  ('derate', '0.85'),
  ('offset_percent', '0.90'),
  ('exposure_excellent', '1.0'),
  ('exposure_good', '1.15'),
  ('exposure_moderate', '1.3'),
  ('company_name', 'Suria Infiniti'),
  ('company_tagline', 'Malaysia\'s trusted solar partner'),
  ('contact_email', 'taufik@suriainfiniti.com'),
  ('contact_phone', '+60 12-707 5391'),
  ('whatsapp_number', '60127075391'),
  ('sales_email', 'taufik@suriainfiniti.com'),
  ('logo_url', '/images/logo-suria.svg'),
  ('privacy_policy_url', 'https://suriainfiniti.com/privacy-policy/'),
  ('installations_count', '500+'),
  ('avg_savings_percent', '80%'),
  ('customer_rating', '4.9/5')
ON DUPLICATE KEY UPDATE config_value = VALUES(config_value);

CREATE TABLE IF NOT EXISTS admin_users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Run backend/setup.php after import to create admin user

CREATE TABLE IF NOT EXISTS rate_limits (
  ip_hash VARCHAR(64) PRIMARY KEY,
  request_count INT UNSIGNED NOT NULL DEFAULT 1,
  window_start DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
