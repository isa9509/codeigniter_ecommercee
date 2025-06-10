-- Criação do banco de dados e uso
CREATE DATABASE IF NOT EXISTS loja DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE loja;

-- Remoção da tabela se já existir
DROP TABLE IF EXISTS `clientes`;

-- Criação da tabela 'clientes'
CREATE TABLE `clientes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Chave primária',
  `nome` VARCHAR(100) NOT NULL COMMENT 'Nome do cliente',
  `sobrenome` VARCHAR(100) NOT NULL COMMENT 'Sobrenome do cliente',
  `rg` VARCHAR(20) NOT NULL COMMENT 'Registro Geral',
  `cpf` CHAR(11) NOT NULL COMMENT 'CPF (somente números)',
  `data_nascimento` DATE NOT NULL COMMENT 'Data de nascimento',
  `sexo` ENUM('M','F','O') NOT NULL COMMENT 'Sexo: M, F ou Outro',
  `rua` VARCHAR(100) NOT NULL COMMENT 'Nome da rua',
  `numero` VARCHAR(10) NOT NULL COMMENT 'Número da residência',
  `bairro` VARCHAR(60) NOT NULL COMMENT 'Bairro',
  `cidade` VARCHAR(60) NOT NULL COMMENT 'Cidade',
  `estado` CHAR(2) NOT NULL COMMENT 'Sigla do estado',
  `cep` CHAR(8) NOT NULL COMMENT 'CEP (somente números)',
  `telefone` VARCHAR(15) DEFAULT NULL COMMENT 'Telefone fixo',
  `celular` VARCHAR(15) DEFAULT NULL COMMENT 'Telefone celular',
  `email` VARCHAR(120) NOT NULL COMMENT 'E-mail do cliente',
  `senha_hash` CHAR(60) NOT NULL COMMENT 'Hash da senha (bcrypt)',
  `ativo` BOOLEAN NOT NULL DEFAULT TRUE COMMENT 'Cliente ativo',
  `cadastrado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de cadastro',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unq_cpf` (`cpf`),
  UNIQUE KEY `unq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
