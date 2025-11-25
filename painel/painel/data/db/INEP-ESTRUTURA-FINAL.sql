-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           5.7.10-log - MySQL Community Server (GPL)
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Copiando estrutura do banco de dados para inep
DROP DATABASE IF EXISTS `inep`;
CREATE DATABASE IF NOT EXISTS `inep` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `inep`;


-- Copiando estrutura para tabela inep.escolas
DROP TABLE IF EXISTS `escolas`;
CREATE TABLE IF NOT EXISTS `escolas` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NOME` varchar(255) DEFAULT NULL,
  `UF` varchar(255) DEFAULT NULL,
  `MUNICIPIO` varchar(255) DEFAULT NULL,
  `LATITUDE` varchar(255) DEFAULT NULL,
  `LONGITUDE` varchar(255) DEFAULT NULL,
  `ENDERECO` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.


-- Copiando estrutura para tabela inep.eventos
DROP TABLE IF EXISTS `eventos`;
CREATE TABLE IF NOT EXISTS `eventos` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Titulo` varchar(255) NOT NULL,
  `Municipio` varchar(255) NOT NULL,
  `Uf` char(2) NOT NULL,
  `Descricao` text NOT NULL,
  `Descricao_Risco` text NOT NULL,
  `Nivel_Alerta` varchar(255) NOT NULL,
  `Categoria` varchar(255) NOT NULL,
  `SubCategoria` varchar(255) DEFAULT NULL,
  `Ocorrencia` varchar(255) DEFAULT NULL,
  `Coordenacao` varchar(255) DEFAULT NULL,
  `ImpactoAplicacao` varchar(255) DEFAULT NULL,
  `NroProcesso` varchar(255) DEFAULT NULL,
  `Anexo` varchar(255) DEFAULT NULL,
  `Usuario` varchar(255) NOT NULL,
  `UsuarioTriagem` varchar(255) DEFAULT NULL,
  `DataHora` varchar(255) NOT NULL,
  `Status` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.


-- Copiando estrutura para tabela inep.municipios
DROP TABLE IF EXISTS `municipios`;
CREATE TABLE IF NOT EXISTS `municipios` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PATCH` varchar(255) NOT NULL,
  `NOME` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.


-- Copiando estrutura para tabela inep.ultima_milha
DROP TABLE IF EXISTS `ultima_milha`;
CREATE TABLE IF NOT EXISTS `ultima_milha` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UF` varchar(255) DEFAULT NULL,
  `NOME_ESCOLA` varchar(255) DEFAULT NULL,
  `STATUS` varchar(255) DEFAULT NULL,
  `DATA_HORA` varchar(255) DEFAULT NULL,
  `LATITUDE` varchar(255) DEFAULT NULL,
  `LONGITUDE` varchar(255) DEFAULT NULL,
  `ID_ESCOLA` varchar(255) DEFAULT NULL,
  `MUNICIPIO` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_ESCOLA_INDEX` (`ID_ESCOLA`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
