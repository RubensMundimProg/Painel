-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.0.27-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table inep.eventos
DROP TABLE IF EXISTS `eventos`;
CREATE TABLE IF NOT EXISTS `eventos` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Titulo` varchar(255) NOT NULL,
  `Municipio` varchar(255) NOT NULL,
  `Uf` char(2) NOT NULL,
  `Descricao` text NOT NULL,
  `Descricao_Risco` text NOT NULL,
  `ProvidenciasAdotadas` varchar(255) DEFAULT NULL,
  `InformacoesAdicionais` varchar(255) DEFAULT NULL,
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
  `OrigemInformacao` varchar(50) NOT NULL DEFAULT 'Interface',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- Dumping data for table inep.eventos: ~0 rows (approximately)
DELETE FROM `eventos`;
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
INSERT INTO `eventos` (`ID`, `Titulo`, `Municipio`, `Uf`, `Descricao`, `Descricao_Risco`, `ProvidenciasAdotadas`, `InformacoesAdicionais`, `Nivel_Alerta`, `Categoria`, `SubCategoria`, `Ocorrencia`, `Coordenacao`, `ImpactoAplicacao`, `NroProcesso`, `Anexo`, `Usuario`, `UsuarioTriagem`, `DataHora`, `Status`, `OrigemInformacao`) VALUES
	(4, 'GO - VALPARAISO DE GOIAS - 4169 Centro de Ensino Superior do Brasil (CESB) - Eliminação de Participantes - Recusa à coleta de dado biométrico', 'GO - VALPARAISO DE GOIAS', 'GO', 'O Participante João da Silva Sousa  inscrição 111.111.111.111  recusou a fazer a coleta de dado biométrico , infringindo o descrito no edital. O Participante foi encaminhado a Coordenação para o preenchimento do Termo de Eliminação do Participante.', '', NULL, NULL, '', 'Eliminação de Participantes', 'Recusa à coleta de dado biométrico', NULL, '4169 Centro de Ensino Superior do Brasil (CESB)', 'Não', NULL, NULL, 'Inês Borges - iborges@modulo.com.br', 'Inês Borges - iborges@modulo.com.br', '20/10/2016 17:59:16', 2, 'Interface'),
	(6, 'PR - PONTA GROSSA - 55358 UEPG - CAMPUS CENTRAL - BLOCO B - Aplicação - Boletim de ocorrência', 'PR - PONTA GROSSA', 'PR', 'o Participante Jose da Silva Sousa inscrição nr. 1111.1111.1111 apresentou um boletim de ocorrência com data de vencimento em  20/10/2016. O participante está causando tumulto dentro da sala de coordenação, quebrando o mobiliário, agredindo os fiscais, acesso de furia.', '', NULL, NULL, '', 'Aplicação', 'Boletim de ocorrência', NULL, '55358 UEPG - CAMPUS CENTRAL - BLOCO B', 'Não', '', NULL, 'Inês Borges - iborges@modulo.com.br', 'Inês Borges - iborges@modulo.com.br', '21/10/2016 13:47:38', 2, 'Interface'),
	(8, 'MG - POCOS DE CALDAS - Selecione', 'MG - POCOS DE CALDAS', 'MG', 'Teste obrigatoriedade dos campos.', '', NULL, NULL, '', 'Abastecimento de Água', 'Comportamento inadequado (participante ou acompanhante de lactante)', NULL, 'Selecione', 'Não', '', NULL, 'Fabiana Menezes - fmenezes@modulo.com', NULL, '23/10/2016 16:41:21', 1, 'Interface'),
	(9, 'MG - BRASILIA DE MINAS - Aplicação - Demanda Judicial', 'MG - BRASILIA DE MINAS', 'MG', 'teste nº do processo', '', NULL, NULL, '', 'Aplicação', 'Demanda Judicial', NULL, 'Selecione', NULL, '97987987897VCXSDFDHKII', NULL, 'Fabiana Menezes - fmenezes@modulo.com', 'Inês Borges - iborges@modulo.com.br', '23/10/2016 17:02:37', 2, 'Interface'),
	(10, 'AL - PENEDO - 54569 EE DR ALCIDES ANDRADE - Abastecimento de Água', 'AL - PENEDO', 'AL', 'Teste de Ocorrência arquivada', '', NULL, NULL, '', 'Abastecimento de Água', NULL, NULL, '54569 EE DR ALCIDES ANDRADE', 'Não', NULL, NULL, 'Fabiana Menezes - fmenezes@modulo.com', 'Inês Borges - iborges@modulo.com.br', '23/10/2016 17:29:06', 2, 'Interface'),
	(11, 'DF - BRASILIA - 9092 Centro Universitário de Brasília - UniCEUB - Emergências Médicas - Doença Infectocontagiosa', 'DF - BRASILIA', 'DF', 'Descrição do Teste', '', 'Teste de Upload', NULL, '', 'Emergências Médicas', 'Doença Infectocontagiosa', NULL, '9092 Centro Universitário de Brasília - UniCEUB', 'Não', NULL, '/anexos/o5ebg7_580e2e02bcbd6.jpg', 'Bruno Silva - bruno.silva@modulo.com', 'Inês Borges - iborges@modulo.com.br', '24/10/2016 13:51:30', 2, 'Interface'),
	(12, 'MS - PONTA PORA - 3683 Escola Estadual Adê Marques - Malote - Atraso na entrega de malotes', 'MS - PONTA PORA', 'MS', 'As 10:50h o malote que deveria ser entregue pelo Correio, ainda não chegou ao local de provas (Escola Estadual Adê Marques) - Ponta Porã - MS', '', 'O consórcio já foi comunicado.', NULL, '', 'Malote', 'Atraso na entrega de malotes', NULL, '3683 Escola Estadual Adê Marques', 'Sim', NULL, NULL, 'Inês Borges - iborges@modulo.com.br', NULL, '24/10/2016 13:54:54', 1, 'Interface'),
	(13, 'DF - BRASILIA - 9090 Centro Universitário de Brasília - UniCEUB - Abastecimento de Água', 'DF - BRASILIA', 'DF', 'Abastecimento de água suspenso às 11:55', '', 'contactado o CICCR', '', '', 'Abastecimento de Água', NULL, NULL, '9090 Centro Universitário de Brasília - UniCEUB', 'Sim', '', '/anexos/f01_580e31c329035.png', 'Inês Borges - iborges@modulo.com.br', 'Fabiana Menezes - fmenezes@modulo.com', '24/10/2016 14:07:31', 2, 'Interface'),
	(14, 'AC - BRASILEIA - 139 EEEF Coronel Manoel Fontenele de Castro - Abastecimento de Água', 'AC - BRASILEIA', 'AC', 'fornecimento de água suspenso as 11:30h', '', 'comunicado ao centro regional.', 'teste as 14:30', '', 'Abastecimento de Água', NULL, NULL, '139 EEEF Coronel Manoel Fontenele de Castro', 'Sim', '', '/anexos/f30_580e372463f96.png|/anexos/f01_580e324bd15ac.png', 'Inês Borges - iborges@modulo.com.br', 'Inês Borges - iborges@modulo.com.br', '24/10/2016 14:09:47', 2, 'Interface'),
	(15, 'AC - BRASILEIA - Aplicação - Abertura e fechamento dos portões', 'AC - BRASILEIA', 'AC', 'teste', '', 'teste', 'teste 2  teste 2', '', 'Aplicação', 'Abertura e fechamento dos portões', NULL, '139 EEEF Coronel Manoel Fontenele de Castro', 'Não', '', '/anexos/f25_580e368882776.png', 'Inês Borges - iborges@modulo.com.br', 'Inês Borges - iborges@modulo.com.br', '24/10/2016 14:16:00', 2, 'Interface'),
	(16, 'AC - BRASILEIA - 139 EEEF Coronel Manoel Fontenele de Castro - Abastecimento de Água', 'AC - BRASILEIA', 'AC', 'teste1', '', 'teste1', NULL, '', 'Abastecimento de Água', NULL, NULL, '139 EEEF Coronel Manoel Fontenele de Castro', 'Sim', NULL, '/anexos/f27_580e343bcf9a4.png', 'Inês Borges - iborges@modulo.com.br', 'Inês Borges - iborges@modulo.com.br', '24/10/2016 14:18:03', 2, 'Interface'),
	(17, 'AC - BRASILEIA - Clima e Tempo - Inundação no local de prova', 'AC - BRASILEIA', 'AC', 'teste2', '', 'teste', 'teste1', '', 'Clima e Tempo', 'Inundação no local de prova', NULL, '139 EEEF Coronel Manoel Fontenele de Castro', 'Sim', '', '/anexos/f23_580e35221f854.png', 'Inês Borges - iborges@modulo.com.br', 'Bruno Silva - bruno.silva@modulo.com', '24/10/2016 14:21:54', 2, 'Interface'),
	(18, 'AC - ACRELANDIA - 143 EEEF Professor Pedro de Castro Meireles - Clima e Tempo - Tempestade torrencial', 'AC - ACRELANDIA', 'AC', 'asdasdas', '', 'dadasd', NULL, '', 'Clima e Tempo', 'Tempestade torrencial', NULL, '143 EEEF Professor Pedro de Castro Meireles', 'Sim', NULL, NULL, 'Bruno Silva - bruno.silva@modulo.com', 'Bruno Silva - bruno.silva@modulo.com', '24/10/2016 14:36:34', 2, 'Interface'),
	(21, 'MG - POCOS DE CALDAS - Abastecimento de Água', 'MG - POCOS DE CALDAS', 'MG', 'teste 5', '', '', NULL, '', 'Abastecimento de Água', NULL, NULL, '', 'Não', NULL, NULL, 'Inês Borges - iborges@modulo.com.br', NULL, '24/10/2016 14:49:57', 1, 'Interface'),
	(22, 'MT - JURUENA - Aplicação', 'MT - JURUENA', 'MT', 'teste 6', '', '', NULL, '', 'Aplicação', '', NULL, '', 'Não', NULL, NULL, 'Inês Borges - iborges@modulo.com.br', NULL, '24/10/2016 15:06:41', 1, 'Interface'),
	(23, 'AC - ACRELANDIA - Clima e Tempo - Tempestade torrencial', 'AC - ACRELANDIA', 'AC', 'Descricao', '', 'Providencias', '', '', 'Clima e Tempo', 'Tempestade torrencial', NULL, '', 'Não', '', '/anexos/3_580e4ffb2c3a4.png|/anexos/0_580e4feb8fa16.png', 'Bruno Silva - bruno.silva@modulo.com', 'Inês Borges - iborges@modulo.com.br', '24/10/2016 16:16:11', 2, 'Interface'),
	(24, 'MT - BRASNORTE - Clima e Tempo - Inundação no local de prova', 'MT - BRASNORTE', 'MT', 'Escola totalmente alagada.', '', '', NULL, '', 'Clima e Tempo', 'Inundação no local de prova', NULL, '', 'Sim', NULL, NULL, 'Inês Borges - iborges@modulo.com.br', NULL, '24/10/2016 17:02:04', 1, 'Interface'),
	(25, 'DF - BRASILIA - Energia Elétrica', 'DF - BRASILIA', 'DF', 'PREJUDICA os sabatistas', '', 'Entrar em contato com a CIA Eletrica.', NULL, '', 'Energia Elétrica', NULL, NULL, '', 'Sim', NULL, NULL, 'Jemima Mendes - jemima.mendes@modulo.com', 'Jemima Mendes - jemima.mendes@modulo.com', '24/10/2016 17:08:24', 2, 'Interface'),
	(26, 'PA - BUJARU - Emergências Médicas - Doença Infectocontagiosa', 'PA - BUJARU', 'PA', 'CACHUMBA', '', 'SALA EXTRA', NULL, '', 'Emergências Médicas', 'Doença Infectocontagiosa', NULL, NULL, 'Não', NULL, NULL, 'Jemima Mendes - jemima.mendes@modulo.com', 'Jemima Mendes - jemima.mendes@modulo.com', '24/10/2016 17:11:31', 2, 'Interface'),
	(27, 'BA - AMARGOSA - Malote - Atraso na coleta de malotes', 'BA - AMARGOSA', 'BA', 'teste', '', 'teste', NULL, '', 'Malote', 'Atraso na coleta de malotes', NULL, '', 'Sim', NULL, NULL, 'Jemima Mendes - jemima.mendes@modulo.com', 'Jemima Mendes - jemima.mendes@modulo.com', '24/10/2016 17:26:20', 2, 'Interface'),
	(28, 'CE - ACARAU - Segurança Pública - CVP (Crimes Violentos contra o Patrimônio)', 'CE - ACARAU', 'CE', 'teste', '', 'teste', NULL, '', 'Segurança Pública', 'CVP (Crimes Violentos contra o Patrimônio)', NULL, '', 'Sim', NULL, NULL, 'Jemima Mendes - jemima.mendes@modulo.com', 'Inês Borges - iborges@modulo.com.br', '24/10/2016 17:26:49', 2, 'Interface'),
	(29, 'GO - TRINDADE - Malote - Danificado', 'GO - TRINDADE', 'GO', 'teste', '', 'teste', NULL, '', 'Malote', 'Danificado', NULL, '', 'Não', NULL, NULL, 'Jemima Mendes - jemima.mendes@modulo.com', 'Jemima Mendes - jemima.mendes@modulo.com', '24/10/2016 17:41:51', 2, 'Interface'),
	(30, 'GO - TRINDADE - Aplicação - Nome Social (travestis e transexuais)', 'GO - TRINDADE', 'GO', 'rrrrrrrrrrrr', '', 'rrrrrrrrr', NULL, '', 'Aplicação', 'Nome Social (travestis e transexuais)', NULL, '', 'Não', NULL, NULL, 'Jemima Mendes - jemima.mendes@modulo.com', 'Jemima Mendes - jemima.mendes@modulo.com', '24/10/2016 17:46:24', 2, 'Interface'),
	(31, 'GO - TRINDADE - Eliminação de Participantes - Comportamento inadequado (participante ou acompanhante de lactante)', 'GO - TRINDADE', 'GO', 'tttttttttttttttt', '', 'tttttttttt', NULL, '', 'Eliminação de Participantes', 'Comportamento inadequado (participante ou acompanhante de lactante)', NULL, '', 'Não', NULL, NULL, 'Jemima Mendes - jemima.mendes@modulo.com', 'Jemima Mendes - jemima.mendes@modulo.com', '24/10/2016 17:50:07', 2, 'Interface'),
	(32, 'GO - CATALAO - Clima e Tempo - Tempestade torrencial', 'GO - CATALAO', 'GO', 'snkdnfknslf', '', 'jjjlk', NULL, '', 'Clima e Tempo', 'Tempestade torrencial', NULL, '', 'Não', NULL, NULL, 'Jemima Mendes - jemima.mendes@modulo.com', 'Jemima Mendes - jemima.mendes@modulo.com', '24/10/2016 17:53:40', 2, 'Interface'),
	(34, 'MG - VARGINHA - 7007 Colégio Batista de Varginha - Energia Elétrica', 'MG - VARGINHA', 'MG', 'Teste da aplicação - anexo de arquivos', '', 'A Cia de Energia Elétrica já foi acionada. Foi contatado o sr. XPTO que informou o retorno da energia elétrica em 30 minutos.', NULL, '', 'Energia Elétrica', NULL, NULL, '7007 Colégio Batista de Varginha', 'Não', NULL, '/anexos/Captura de Tela 2016-10-24 às 20.23.48_580e8cd8a39a2.png', 'Fabiana Menezes - fmenezes@modulo.com', NULL, '24/10/2016 20:36:08', 1, 'Interface'),
	(35, 'SP - SAO CARLOS - 53577 COL OBJETIVO - UNIDADE SAO JOAQUIM - Aplicação - Abertura e fechamento dos portões', 'SP - SAO CARLOS', 'SP', 'Teste de cadastro de ocorrência', '', '', NULL, '', 'Aplicação', 'Abertura e fechamento dos portões', NULL, '53577 COL OBJETIVO - UNIDADE SAO JOAQUIM', 'Não', NULL, NULL, 'Fabiana Menezes - fmenezes@modulo.com', 'Fabiana Menezes - fmenezes@modulo.com', '25/10/2016 01:04:24', 2, 'Interface'),
	(36, 'MA - BALSAS - Malote - Abertura indevida', 'MA - BALSAS', 'MA', 'Teste nova ocorrência', '', '', NULL, '', 'Malote', 'Abertura indevida', NULL, '', 'Não', NULL, '/anexos/Captura de Tela 2016-10-25 às 01.13.07_580f365aa3e5b.png|/anexos/Captura de Tela 2016-10-25 às 01.07.46_580f365aa416a.png', 'Fabiana Menezes - fmenezes@modulo.com', 'Fabiana Menezes - fmenezes@modulo.com', '25/10/2016 08:39:22', 2, 'Interface'),
	(37, 'PA - TUCURUI - 4888 COLÉGIO CASTRO ALVES - Eliminação de Participantes - Porte de equipamento eletrônico e/ou de comunicação', 'PA - TUCURUI', 'PA', 'O Participante de Jose da Silva inscrição 111.1111.111 durante a realização da prova foi pego utilizando equipamento eletrônico ponto eletrônico.', '', 'O Participante violou o item 19 do Edital. Foi eliminado e foi  acionada a polícia local para providências  em relação a cola eletrônica.', NULL, '', 'Eliminação de Participantes', 'Porte de equipamento eletrônico e/ou de comunicação', NULL, '4888 COLÉGIO CASTRO ALVES', 'Não', NULL, NULL, 'Inês Borges - iborges@modulo.com.br', NULL, '25/10/2016 10:33:09', 1, 'Interface'),
	(38, 'RS - ALEGRETE - Clima e Tempo - Inundação no local de prova', 'RS - ALEGRETE', 'RS', 'Descricao', '', 'Providencias', '', '', 'Clima e Tempo', 'Inundação no local de prova', NULL, '', 'Não', '', '/anexos/i_580f53892ecdc.png|/anexos/0_580f522448007.png|/anexos/1_580f522448370.png|/anexos/i_580f5224485f4.png', 'Bruno Silva - bruno.silva@modulo.com', 'Bruno Silva - bruno.silva@modulo.com', '25/10/2016 10:37:56', 2, 'Interface');
/*!40000 ALTER TABLE `eventos` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
