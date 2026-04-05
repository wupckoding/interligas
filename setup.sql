-- ============================================
-- INTERLIGA PÁDEL - Esquema de Base de Datos
-- Sistema de gestión de liga de pádel
-- Costa Rica 🇨🇷
-- ============================================
-- 
-- INSTRUCCIONES:
-- 1. Crea una base de datos MySQL (o deja que este script la cree)
-- 2. Importa este archivo desde phpMyAdmin o CLI:
--    mysql -u usuario -p < setup.sql
-- 3. O usa install.php desde el navegador
-- ============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ============================================
-- CREAR BASE DE DATOS
-- ============================================
CREATE DATABASE IF NOT EXISTS interliga_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE interliga_db;

-- ============================================
-- TABLAS PRINCIPALES
-- ============================================

-- Jornadas (días de juego)
DROP TABLE IF EXISTS audit_log;
DROP TABLE IF EXISTS resultados;
DROP TABLE IF EXISTS equipos;
DROP TABLE IF EXISTS lista_espera;
DROP TABLE IF EXISTS inscripciones;
DROP TABLE IF EXISTS partidos;
DROP TABLE IF EXISTS jornadas;
DROP TABLE IF EXISTS admins;

CREATE TABLE jornadas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(150) NOT NULL,
    fecha       DATE NOT NULL,
    ubicacion   VARCHAR(200) DEFAULT '',
    estado      ENUM('abierta','cerrada','finalizada') DEFAULT 'abierta',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Partidos dentro de cada jornada
CREATE TABLE partidos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    jornada_id  INT NOT NULL,
    categoria   VARCHAR(80) NOT NULL,
    genero      ENUM('masculino','femenino','mixto') NOT NULL DEFAULT 'mixto',
    hora        TIME NOT NULL,
    cancha      VARCHAR(80) DEFAULT '',
    cupos       INT NOT NULL DEFAULT 4 COMMENT 'Número de parejas (cada pareja = 2 jugadores)',
    estado      ENUM('abierto','lleno','cerrado') DEFAULT 'abierto',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jornada_id) REFERENCES jornadas(id) ON DELETE CASCADE,
    INDEX idx_jornada (jornada_id),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inscripciones de jugadores
CREATE TABLE inscripciones (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    partido_id          INT NOT NULL,
    nombre              VARCHAR(120) NOT NULL,
    telefono            VARCHAR(30) DEFAULT '',
    tipo                ENUM('solo','pareja') NOT NULL DEFAULT 'solo',
    pareja_nombre       VARCHAR(120) DEFAULT NULL,
    pareja_telefono     VARCHAR(30)  DEFAULT NULL,
    estado              ENUM('confirmado','esperando','cancelado') DEFAULT 'confirmado',
    pareja_auto_id      INT DEFAULT NULL COMMENT 'ID de inscripción del jugador solo emparejado',
    es_reserva          TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=reserva/suplente',
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (partido_id) REFERENCES partidos(id) ON DELETE CASCADE,
    INDEX idx_partido_estado (partido_id, estado),
    INDEX idx_nombre (nombre),
    INDEX idx_telefono (telefono)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lista de espera
CREATE TABLE lista_espera (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    partido_id  INT NOT NULL,
    nombre      VARCHAR(120) NOT NULL,
    telefono    VARCHAR(30) DEFAULT '',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (partido_id) REFERENCES partidos(id) ON DELETE CASCADE,
    INDEX idx_partido (partido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Administradores
CREATE TABLE admins (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario     VARCHAR(50) NOT NULL UNIQUE,
    clave       VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Equipos (clasificación)
CREATE TABLE equipos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(120) NOT NULL,
    logo_emoji  VARCHAR(10) DEFAULT '🏓',
    activo      TINYINT(1) NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Resultados de partidos entre equipos
CREATE TABLE resultados (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    jornada_id          INT NOT NULL,
    equipo_local_id     INT NOT NULL,
    equipo_visitante_id INT NOT NULL,
    puntos_local        INT NOT NULL DEFAULT 0,
    puntos_visitante    INT NOT NULL DEFAULT 0,
    observaciones       VARCHAR(255) DEFAULT '',
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jornada_id) REFERENCES jornadas(id) ON DELETE CASCADE,
    FOREIGN KEY (equipo_local_id) REFERENCES equipos(id),
    FOREIGN KEY (equipo_visitante_id) REFERENCES equipos(id),
    INDEX idx_jornada (jornada_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Log de auditoría
CREATE TABLE audit_log (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    usuario     VARCHAR(50) NOT NULL DEFAULT 'sistema',
    accion      VARCHAR(100) NOT NULL,
    detalle     TEXT DEFAULT NULL,
    ip          VARCHAR(45) DEFAULT '',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuario (usuario),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DATOS INICIALES
-- ============================================

-- Admin padrão (usuario: admin / contraseña: interliga2024)
-- IMPORTANTE: Cambiar la contraseña después del primer login
INSERT INTO admins (usuario, clave) VALUES
('admin', '$2y$10$jHPWxuqzO1vAlV/R8PyFXuer5Nco..TSWTuAk5cUNa0qFguNEuJ.y');

-- Equipos de ejemplo (13 equipos de la liga)
INSERT INTO equipos (nombre, logo_emoji) VALUES
('Ajó Padel',        '🌶️'),
('Atardeceres Padel','🌅'),
('BN Padel',         '🏦'),
('Café Padel',       '☕'),
('Colosos',          '💪'),
('El Hueco',         '🕳️'),
('Los Pericos',      '🦜'),
('Milán Padel',      '🇮🇹'),
('Monarcas',         '👑'),
('Pádel Vibes CR',   '🎵'),
('QPS',              '⚡'),
('San Rafa',         '🙏'),
('TigoPadel',        '🐯');

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- NOTAS DE DEPLOY
-- ============================================
-- 
-- En hosting compartido (Hostinger, etc):
-- 1. Crear base de datos desde el panel (hPanel > MySQL)
-- 2. Anotar: host, nombre_db, usuario, contraseña
-- 3. Editar config.php con esos datos
-- 4. Importar este archivo desde phpMyAdmin
-- 5. O acceder a install.php desde el navegador
-- 
-- El hash del admin se genera automáticamente
-- via install.php - no necesitas tocarlo aquí.
-- ============================================
