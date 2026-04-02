CREATE DATABASE IF NOT EXISTS certificados
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE certificados;

CREATE TABLE IF NOT EXISTS certificados (
  id                      INT AUTO_INCREMENT PRIMARY KEY,
  hash                    VARCHAR(64) UNIQUE NOT NULL,
  nome                    VARCHAR(150) NOT NULL,
  funcao                  VARCHAR(150) NULL,
  data_emissao            DATE NOT NULL,
  carga_horaria           VARCHAR(10) NULL,
  has_assinatura_adicional TINYINT(1) DEFAULT 0,
  criado_em               DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS certificados_atividades (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  atividade     VARCHAR(120) NOT NULL,
  criado_em     DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS certificados_atividades_grupo (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  id_cer_atividade    INT NULL,
  id_certificado      INT NULL,
  FOREIGN KEY (id_cer_atividade) REFERENCES certificados_atividades(id) ON DELETE SET NULL,
  FOREIGN KEY (id_certificado) REFERENCES certificados(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS assinatura_adicional (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  id_certificado      INT NULL,
  nome_instrutor      VARCHAR(150) NOT NULL,
  funcao              VARCHAR(150) NOT NULL,
  numero_registro     VARCHAR(100) NULL,
  criado_em           DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_certificado) REFERENCES certificados(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;