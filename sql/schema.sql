CREATE DATABASE IF NOT EXISTS certificados
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE certificados;

CREATE TABLE IF NOT EXISTS certificados (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  hash          VARCHAR(64) UNIQUE NOT NULL,
  nome          VARCHAR(150) NOT NULL,
  funcao        VARCHAR(150) NULL,
  data_emissao  DATE NOT NULL,
  carga_horaria VARCHAR(10) NULL,
  atividade     VARCHAR(150) NULL,
  instrutor     VARCHAR(150) NULL,
  criado_em     DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

