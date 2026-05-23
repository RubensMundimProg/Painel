-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           5.7.10 - MySQL Community Server (GPL)
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Copiando estrutura para tabela inep.ultima_milha_uf
DROP TABLE IF EXISTS `ultima_milha_uf`;
CREATE TABLE IF NOT EXISTS `ultima_milha_uf` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `AC` varchar(50) NOT NULL,
  `AL` varchar(50) NOT NULL,
  `AP` varchar(50) NOT NULL,
  `AM` varchar(50) NOT NULL,
  `BA` varchar(50) NOT NULL,
  `CE` varchar(50) NOT NULL,
  `DF` varchar(50) NOT NULL,
  `ES` varchar(50) NOT NULL,
  `GO` varchar(50) NOT NULL,
  `MA` varchar(50) NOT NULL,
  `MG` varchar(50) NOT NULL,
  `MS` varchar(50) NOT NULL,
  `MT` varchar(50) NOT NULL,
  `PA` varchar(50) NOT NULL,
  `PB` varchar(50) NOT NULL,
  `PE` varchar(50) NOT NULL,
  `PI` varchar(50) NOT NULL,
  `PR` varchar(50) NOT NULL,
  `RJ` varchar(50) NOT NULL,
  `RN` varchar(50) NOT NULL,
  `RO` varchar(50) NOT NULL,
  `RR` varchar(50) NOT NULL,
  `RS` varchar(50) NOT NULL,
  `SC` varchar(50) NOT NULL,
  `SE` varchar(50) NOT NULL,
  `SP` varchar(50) NOT NULL,
  `TO` varchar(50) NOT NULL,
  `DATA_HORA` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Copiando dados para a tabela inep.ultima_milha_uf: ~0 rows (aproximadamente)
DELETE FROM `ultima_milha_uf`;
/*!40000 ALTER TABLE `ultima_milha_uf` DISABLE KEYS */;
INSERT INTO `ultima_milha_uf` (`ID`, `AC`, `AL`, `AP`, `AM`, `BA`, `CE`, `DF`, `ES`, `GO`, `MA`, `MG`, `MS`, `MT`, `PA`, `PB`, `PE`, `PI`, `PR`, `RJ`, `RN`, `RO`, `RR`, `RS`, `SC`, `SE`, `SP`, `TO`, `DATA_HORA`) VALUES
	(1, '10|10', '1|0', '1|0', '1|0', '2|4', '1|0', '1|0', '1|0', '1|0', '1|0', '1|0', '1|0', '1|0', '1|0', '1|2', '1|', '1|0', '1|0', '1|0', '1|0', '1|0', '1|0', '1|0', '1|0', '1|0', '1|0', '1|0', '2016-10-31 16:42:04');
/*!40000 ALTER TABLE `ultima_milha_uf` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
