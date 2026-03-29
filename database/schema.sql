-- =============================================
-- SkinLab – Schema MySQL 8.0
-- =============================================

-- Usuarios del sistema
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(30)  NOT NULL UNIQUE,
    email       VARCHAR(255) DEFAULT NULL,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('admin','editor','guest') NOT NULL DEFAULT 'guest',
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Proyectos
CREATE TABLE IF NOT EXISTS projects (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug            VARCHAR(64)  NOT NULL UNIQUE,
    name            VARCHAR(100) NOT NULL,
    description     TEXT         DEFAULT NULL,
    user_id         INT UNSIGNED DEFAULT NULL,
    color_primary   VARCHAR(7)   NOT NULL DEFAULT '#0374B5',
    color_secondary VARCHAR(7)   NOT NULL DEFAULT '#2D3B45',
    org_type        ENUM('none','semanas','modulos','unidades') NOT NULL DEFAULT 'none',
    org_count       TINYINT UNSIGNED NOT NULL DEFAULT 0,
    cdns            JSON         DEFAULT NULL,
    is_active       TINYINT(1)   NOT NULL DEFAULT 1,
    is_protected    TINYINT(1)   NOT NULL DEFAULT 0,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_projects_active (is_active),
    INDEX idx_projects_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configuración global (key-value)
CREATE TABLE IF NOT EXISTS settings (
    setting_key   VARCHAR(50) PRIMARY KEY,
    setting_value TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rate limiting por IP
CREATE TABLE IF NOT EXISTS rate_limits (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip           VARCHAR(45)  NOT NULL,
    action       VARCHAR(50)  NOT NULL,
    attempted_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_rate_lookup (ip, action, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configuración inicial
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('app_installed', '0');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('recovery_email', '');
INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('locale', 'es');

-- Evento para limpiar rate_limits antiguos (cada 5 minutos)
DROP EVENT IF EXISTS evt_clean_rate_limits;
CREATE EVENT evt_clean_rate_limits
    ON SCHEDULE EVERY 5 MINUTE
    DO DELETE FROM rate_limits WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE)
