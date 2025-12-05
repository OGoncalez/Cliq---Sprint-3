-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/12/2025 às 19:53
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `db_cliq`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `assinaturas`
--

CREATE TABLE `assinaturas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `plano` varchar(20) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'ativa',
  `data_inicio` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_expiracao` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `assinaturas`
--

INSERT INTO `assinaturas` (`id`, `usuario_id`, `plano`, `preco`, `status`, `data_inicio`, `data_expiracao`, `created_at`) VALUES
(1, 3, 'padrao', 24.99, 'cancelada', '2025-10-20 13:05:35', '2025-11-19 18:05:35', '2025-10-20 13:05:35'),
(3, 5, 'VIP', 69.99, 'ativa', '2025-10-20 13:14:33', '2025-11-19 18:14:33', '2025-10-20 13:14:33'),
(9, 3, 'Padrão', 24.99, 'cancelada', '2025-11-14 14:25:53', '2025-12-14 18:25:53', '2025-11-14 14:25:53'),
(10, 3, 'Padrão', 24.99, 'cancelada', '2025-11-14 14:38:11', '2025-12-14 18:38:11', '2025-11-14 14:38:11'),
(11, 3, 'Super', 49.99, 'ativa', '2025-11-14 14:39:35', '2025-12-14 18:39:35', '2025-11-14 14:39:35'),
(14, 19, 'padrao', 24.99, 'ativa', '2025-12-01 12:20:16', '2025-12-31 16:20:16', '2025-12-01 12:20:16');

-- --------------------------------------------------------

--
-- Estrutura para tabela `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `genre` varchar(100) NOT NULL,
  `synopsis` text NOT NULL,
  `image` varchar(500) NOT NULL,
  `category` varchar(50) DEFAULT 'novidades',
  `movie_link` varchar(500) DEFAULT NULL,
  `trailer_link` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `movies`
--

INSERT INTO `movies` (`id`, `title`, `name`, `year`, `genre`, `synopsis`, `image`, `category`, `movie_link`, `trailer_link`, `created_at`, `updated_at`) VALUES
(4, 'Missão Pet', 'Falcon Express', 2025, 'Animação', 'Uma equipe de bandidos animais embarca em um golpe de rotina e se veem envolvidos em um assalto a trem. Cabe a Falcon, um guaxinim ladrão de pouca monta, e Rex, um cão policial justo, salvar os animais neste trem em alta velocidade.', 'uploads/movies/691b2b420601c.jpg', 'animacao_kids', 'https://youtu.be/AXmoo8xSTow?si=-QE9iSJmDhun1LdE', 'https://youtu.be/AXmoo8xSTow?si=-QE9iSJmDhun1LdE', '2025-11-17 14:03:46', '2025-11-17 14:03:46'),
(5, 'Up: Altas Aventuras', 'Up', 2009, 'Aventura', 'Carl Fredricksen é um vendedor de balões que, aos 78 anos, está prestes a perder a casa em que sempre viveu com sua esposa, a falecida Ellie. Após um incidente, Carl é considerado uma ameaça pública e forçado a ser internado. Para evitar que isto aconteça, ele põe balões em sua casa, fazendo com que ela levante voo. Carl quer viajar para uma floresta na América do Sul, onde ele e Ellie sempre desejaram morar, mas descobre que um problema embarcou junto: Russell, um menino de 8 anos.', 'uploads/movies/691b2bec30444.jpg', 'novidades', 'https://www.youtube.com/watch?v=HWEW_qTLSEE', 'https://www.youtube.com/watch?v=HWEW_qTLSEE', '2025-11-17 14:06:36', '2025-11-17 14:06:36'),
(6, 'A Origem dos Guardiões', 'Rise of the Guardians', 2012, 'Aventura', 'Jack Frost, um garoto que controla o inverno, se junta ao seleto time dos Guardiões Imortais para impedir Breu, o bicho-papão, de transformar todos os sonhos das crianças em pesadelo e usar seus poderes maquiavélicos para governar o mundo.', 'uploads/movies/691b2d0cbc46c.jpg', 'aventura_kids', 'https://www.youtube.com/watch?v=0gk9cySqUCI', 'https://www.youtube.com/watch?v=0gk9cySqUCI', '2025-11-17 14:11:24', '2025-11-17 14:11:24'),
(7, 'A hora do Mal', 'Weapons', 2025, 'Terror', 'Todas as crianças da mesma sala de aula, exceto uma, desaparecem misteriosamente na mesma noite e exatamente no mesmo horário. A comunidade fica se perguntando quem ou o que está por trás do desaparecimento.', 'uploads/movies/691b2da30dc7b.jpg', 'lancamentos', 'https://youtu.be/OpThntO9ixc?si=Hl4pAIJauwxEK0Gu', 'https://youtu.be/OpThntO9ixc?si=Hl4pAIJauwxEK0Gu', '2025-11-17 14:13:55', '2025-11-17 14:13:55'),
(8, 'Bailarina: Do Universo de John Wick', 'Ballerina', 2025, 'Ação', 'companha Eve Macarro, uma assassina treinada pela organização Ruska Roma, que busca vingança pela morte de seu pai. O filme se passa durante os eventos de John Wick 3: Parabellum e mergulha no mundo do submundo dos assassinos com ação coreografada e elementos que exploram as tradições da Ruska Roma.', 'uploads/movies/691b2e6a9dd08.jpg', 'lancamentos', 'https://youtu.be/0FSwsrFpkbw?si=wtSIpisfyu_Hb4lb', 'https://youtu.be/vhiZlyCh7mI?si=02cPhjMM6iUFJx1q', '2025-11-17 14:17:14', '2025-11-28 11:37:43'),
(9, 'Breaking Bad', 'Breaking Bad', 2008, 'Drama', 'Breaking Bad é uma série sobre Walter White, um professor de química de ensino médio diagnosticado com câncer terminal. Para garantir o futuro financeiro de sua família, ele usa seus conhecimentos para produzir metanfetamina com seu ex-aluno Jesse Pinkman, entrando no mundo do crime.', 'uploads/movies/691b2fa6c3220.jpg', 'novidades', 'https://youtu.be/K8xcPhsaris?si=V4St0Q1VzYx0npX9', 'https://youtu.be/XZ8daibM3AE?si=8n5wMCvkCq-Ypm2i', '2025-11-17 14:22:30', '2025-11-17 14:22:30'),
(10, 'A Felicidade é um Cobertor Quente, Charlie Brown', 'Happiness Is a Warm Blanket, Charlie Brown', 2011, 'Animação', 'Charlie Brown e a turma tentam ajudar Lino abandonar sua manta.', 'uploads/movies/6929970378887.webp', 'animacao_kids', 'https://youtu.be/9f7qzAR8nMc?si=ltvXWmdZeUXY7t30', 'https://youtu.be/9f7qzAR8nMc?si=ltvXWmdZeUXY7t30', '2025-11-17 14:26:18', '2025-11-28 12:35:15'),
(11, 'Sing - Quem Canta Seus Males Espanta', 'Sing', 2016, 'Comédia', 'Para salvar seu teatro à beira da falência, O koala Buster Moon organiza uma competição de canto. Um erro na promoção faz com que o prêmio de $1.000 se torne $100.000, atraindo diversos animais com seus próprios sonhos e problemas.', 'uploads/movies/691b331f39466.avif', 'musical_kids', 'https://youtu.be/y2joDOunHBo?si=Q0i5Rm7zW5XgoheL', 'https://youtu.be/y2joDOunHBo?si=Q0i5Rm7zW5XgoheL', '2025-11-17 14:36:21', '2025-11-17 14:37:19'),
(12, 'O iluminado', 'The Shining', 1980, 'Terror', 'Jack Torrance se torna caseiro de inverno do isolado Hotel Overlook, nas montanhas do Colorado, na esperança de curar seu bloqueio de escritor. Ele se instala com a esposa Wendy e o filho Danny, que é atormentando por premonições. Jack não consegue escrever e as visões de Danny se tornam mais perturbadoras. O escritor descobre os segredos sombrios do hotel e começa a se transformar em um maníaco homicida, aterrorizando sua família.', 'uploads/movies/691b33c7d075f.jpg', 'recomendacoes', 'https://youtu.be/FkzmxFrotLM?si=gsIg3cytj3WDESKi', 'https://youtu.be/dSQ3yN5yJ0g?si=9bUZigopR9Jj3vQY', '2025-11-17 14:39:54', '2025-11-28 11:13:12'),
(13, 'O agente secreto', 'O agente secreto', 2025, 'Suspense', 'Em 1977, Marcelo trabalha como professor especializado em tecnologia. Ele decide fugir de seu passado violento e misterioso se mudando de São Paulo para Recife com a intenção de recomeçar sua vida. Marcelo chega na capital pernambucana em plena semana do Carnaval e percebe que atraiu para si todo o caos do qual ele sempre quis fugir. Para piorar a situação, ele começa a ser espionado pelos vizinhos. Inesperadamente, a cidade que ele acreditou que o acolheria ficou longe de ser o seu refúgio.', 'uploads/movies/691b5a4ac04a3.jpg', 'novidades', 'https://youtu.be/AOBPXs_euPA?si=gO_y0790QBItc0Wt', 'https://youtu.be/JB6Wf4DM9GY?si=kGyBGEner2nBLrUW', '2025-11-17 17:24:26', '2025-11-17 17:38:13'),
(14, 'Cloud - Nuvem de Vingança', 'Kuraudo', 2024, 'Suspense', 'Ryosuke Yoshii é um homem comum que se sustenta vendendo coisas na Internet. Pouco a pouco, ele atrai o ressentimento das pessoas ao seu redor e tem que lutar por sua vida.', 'uploads/movies/691b5e83a3720.png', 'novidades', 'https://youtu.be/3bYR_dGtN7c?si=R9_md4bJX7A7D0TN', 'https://youtu.be/6g3RRS1P6DA?si=oW_bfU-GfwSNSA2Z', '2025-11-17 17:42:27', '2025-11-17 17:42:27'),
(15, 'Corra Que a Polícia Vem Aí', 'The Naked Gun', 2025, 'Comédia', 'Seguindo os passos de seu pai atrapalhado, o detetive Frank Drebin Jr. deve resolver um caso de assassinato para evitar que o departamento de polícia seja fechado.', 'uploads/movies/691b60de4d0e3.jpg', 'novidades', 'https://www.youtube.com/watch?v=Z5h13hNL-LU', 'https://www.youtube.com/watch?v=Z5h13hNL-LU', '2025-11-17 17:52:30', '2025-11-17 17:52:30'),
(17, 'Drive', 'Drive', 2011, 'Ação', 'Um habilidoso motorista, dublê em cenas de perseguição em filmes de Hollywood, também é piloto de fuga em assaltos. Sua vida muda quando ele se apaixona por uma mulher cujo marido está prestes a sair da prisão, enquanto seu chefe planeja uma corrida.', 'uploads/movies/69297f0eafc73.jpg', 'novidades', 'https://youtu.be/KBiOF3y1W0Y?si=fOkyILSvA5WHGssU', 'https://youtu.be/FZ20TSHrqOg?si=meo20ANgGVCsQRg2', '2025-11-28 10:53:02', '2025-11-28 10:53:02'),
(18, 'Dois caras legais', 'The Nice Guys', 2016, 'Comédia', 'A filha de uma funcionária do Departamento de Justiça é sequestrada. Sua mãe contrata o detetive Healy para investigar o caso. Ao lado do desajeitado oficial March, eles descobrem uma conspiração ligada à morte de uma estrela de filmes adultos.', 'uploads/movies/692980151863b.jpg', 'recomendacoes', 'https://youtu.be/8VRRMYmSEC8?si=VCRLQhv4OMOz5MkV', 'https://youtu.be/xUSPouE-b6U?si=XYuV7VwaIE4Cu4yo', '2025-11-28 10:57:25', '2025-11-28 10:57:25'),
(20, 'Clube da Luta', 'The Fight Club', 1999, 'Ação', 'Um homem deprimido que sofre de insônia conhece um estranho vendedor chamado Tyler Durden e se vê morando em uma casa suja depois que seu perfeito apartamento é destruído. A dupla forma um clube com regras rígidas onde homens lutam. A parceria perfeita é comprometida quando uma mulher, Marla, atrai a atenção de Tyler.', 'uploads/movies/6929813667353.jpg', 'recomendacoes', 'https://youtu.be/DHo91Zl0swc?si=RkplA3fDXLvT8MoJ', 'https://youtu.be/BdJKm16Co6M?si=qnP8JNL_LflAQp0I', '2025-11-28 11:02:14', '2025-11-28 11:02:14'),
(21, 'Pulp Fiction', 'Pulp Fiction', 1994, 'Suspense', 'Assassino que trabalha para a máfia se apaixona pela esposa de seu chefe quando é convidado a acompanhá-la, um boxeador descumpre sua promessa de perder uma luta e um casal tenta um assalto que rapidamente sai do controle.', 'uploads/movies/69298245d927d.jpg', 'recomendacoes', 'https://youtu.be/WSLMN6g_Od4?si=bDwxZLYAia4etGFQ', 'https://youtu.be/TjhZuh-6MTQ?si=8hQnbad6W60GOHyG', '2025-11-28 11:05:41', '2025-11-28 11:06:45'),
(22, 'Pânico', 'Scream', 1996, 'Terror', 'Na pequena cidade de Woodsboro, um grupo de jovens do ensino médio enfrenta um assassino mascarado que testa seus conhecimentos sobre filmes de terror. Sidney Prescott, traumatizada após o brutal assassinato de sua mãe, é o alvo preferido do misterioso homicida que aterroriza a outrora pacata comunidade.', 'uploads/movies/69298365324b6.jpg', 'recomendacoes', 'https://youtu.be/OJ7WnkJr2rs?si=fR1WR9E4lnU1OGp4', 'https://youtu.be/s3ZJmQocUaM?si=XttiRqrzYVf6EMOD', '2025-11-28 11:11:33', '2025-11-28 11:11:33'),
(23, 'A Longa Marcha', 'The Long Walk', 2025, 'Terror', '50 jovens de um regime autoritário participam de uma competição mortal de caminhada televisionada. Os participantes devem manter uma velocidade mínima de 5 km/h, caso contrário, são executados por soldados. O último a permanecer de pé vence um grande prêmio, e o filme explora a resistência, a exaustão e a luta pela sobrevivência. ', 'uploads/movies/692987d35c029.webp', 'lancamentos', 'https://youtu.be/vAtUHeMQ1F8?si=XcZI9mCWU-zfPFhY', 'https://youtu.be/Toj3Zxun7aQ?si=7c2sR8oxG6ELnEuo', '2025-11-28 11:30:27', '2025-11-28 11:30:27'),
(24, 'Capitão América: Admirável Mundo Novo', 'Captain America: Brave New World', 2025, 'Ficção Científica', 'Sam se vê no meio de um incidente internacional após se encontrar com o presidente Thaddeus Ross. Ele precisa descobrir a razão por trás de um nefasto complô global antes que o verdadeiro mentor faça o mundo inteiro ver vermelho.', 'uploads/movies/69298a312bdc1.webp', 'lancamentos', 'https://youtu.be/U7JG6FMoEdM?si=5Xc9pvJ9Rb884aUZ', 'https://youtu.be/NP2xle2cmf0?si=TLP2tctQoF9qZ6fS', '2025-11-28 11:40:33', '2025-11-28 11:40:33'),
(25, 'F1', 'F1', 2025, 'Ação', 'Na década de 1990, Sonny Hayes era o piloto mais promissor da Fórmula 1 até que um acidente na pista quase encerrou sua carreira. Trinta anos depois, o proprietário de uma equipe de Fórmula 1 em dificuldades convence Sonny a voltar a correr e se tornar o melhor do mundo.', 'uploads/movies/69298b17bbde4.jpg', 'lancamentos', 'https://youtu.be/ZiDphkXCZsQ?si=CQP0ixarL3zAhO4u', 'https://youtu.be/69ffwl-8pCU?si=E3NADPwY_1Nt3c5B', '2025-11-28 11:44:23', '2025-11-28 11:44:23'),
(26, 'Up: Altas Aventuras', 'Up', 2009, 'Aventura', 'Carl Fredricksen é um vendedor de balões que, aos 78 anos, está prestes a perder a casa em que sempre viveu com sua esposa, a falecida Ellie. Após um incidente, Carl é considerado uma ameaça pública e forçado a ser internado. Para evitar que isto aconteça, ele põe balões em sua casa, fazendo com que ela levante voo. Carl quer viajar para uma floresta na América do Sul, onde ele e Ellie sempre desejaram morar, mas descobre que um problema embarcou junto: Russell, um menino de 8 anos.', 'uploads/movies/69298b9908278.jpg', 'aventura_kids', 'https://youtu.be/6mZM-W8zJro?si=uD33yeuJ2CMDkQlO', 'https://youtu.be/ODanPYf1VLI?si=3PKO1sp8Bv5_2koi', '2025-11-28 11:46:33', '2025-11-28 11:46:33'),
(27, 'O Telefone Preto 2', 'Black Phone 2', 2025, 'Terror', 'Quatro anos após os eventos do primeiro filme, Finney tenta seguir a vida enquanto sua irmã Gwen passa a receber chamadas do telefone preto em seus sonhos. ', 'uploads/movies/69298d1813851.jpg', 'lancamentos', 'https://youtu.be/wz1275EUIko?si=oxHIOEzS9SOioMqW', 'https://youtu.be/yMZLeFcftyM?si=zf4LPCF5twS5Oqzl', '2025-11-28 11:52:56', '2025-11-28 11:52:56'),
(28, 'Laranja Mecânica', 'A Clockwork Orange', 1971, 'Ficção Científica', 'O filme acompanha Alex, o líder de uma gangue de adolescentes que comete crimes violentos em uma sociedade futurista distópica. Após ser preso, ele é submetido a um tratamento experimental de aversão, conhecido como \"Técnica Ludovico\", em troca de uma pena reduzida.', 'uploads/movies/69298dfca3a0a.webp', 'recomendacoes', 'https://youtu.be/OP157WMfOqo?si=zpH3pk8GGJ-4SoUn', 'https://youtu.be/T54uZPI4Z8A?si=-BSGrKDouwYJRLlE', '2025-11-28 11:56:44', '2025-11-28 11:56:44'),
(29, 'Mune, O Guardião da Lua', 'Mune, le Gardien de la Lune', 2014, 'Aventura', 'Mune é um pequeno fauno lunar muito tímido e pouco seguro de si. Quando é nomeado Guardião da Lua, responsável por trazer a noite e tomar conta do mundo dos sonhos, vê-se obrigado a aceitar a responsabilidade. Mas, quando o Guardião das Trevas decide roubar o Sol, o pequeno fauno descobre em si uma coragem que nunca imaginou possuir.', 'uploads/movies/6929911cf0c7f.jpg', 'animacao_kids', 'https://youtu.be/XnBm7hgeHsw?si=VHoqOpZTaXO3byBo', 'https://youtu.be/L3yueR9fkFM?si=e86xlJgmB2fEc6ed', '2025-11-28 12:10:04', '2025-11-28 12:10:04'),
(30, 'Klaus', 'Klaus', 2019, 'Comédia', 'Em Klaus vemos uma reimaginação da origem do Papai Noel através da improvável amizade entre Jesper, um carteiro mimado e egoísta, e Klaus, um lenhador solitário que fabrica brinquedos', 'uploads/movies/692991d6e037e.jpg', 'animacao_kids', 'https://youtu.be/ILy2vKcI6fo?si=eA7p7GGqazt0W1b4', 'https://youtu.be/ILy2vKcI6fo?si=eA7p7GGqazt0W1b4', '2025-11-28 12:12:04', '2025-11-28 12:13:10'),
(31, 'Por Água Abaixo', 'Flushed Away', 2006, 'Comédia', 'Roddy é um ratinho acostumado a um bairro luxuoso de Londres. Sem querer, ele dá uma descarga infeliz e acaba nos esgotos, onde terá de aprender a viver de uma forma completamente diferente.', 'uploads/movies/692992b086e8b.webp', 'aventura_kids', 'https://youtu.be/ZZN2BRY1Hb8?si=j792LjeyauPivcyL', 'https://youtu.be/ZZN2BRY1Hb8?si=j792LjeyauPivcyL', '2025-11-28 12:16:48', '2025-11-28 12:35:39'),
(32, 'Toy Story', 'Toy Story', 1995, 'Comédia', 'O aniversário do garoto Andy está chegando e seus brinquedos ficam nervosos, temendo que ele ganhe novos brinquedos que possam substituí-los. ', 'uploads/movies/692993e620dfa.jpg', 'animacao_kids', 'https://youtu.be/v-PjgYDrg70?si=GgADjqkc3rm2IWRo', 'https://youtu.be/v-PjgYDrg70?si=GgADjqkc3rm2IWRo', '2025-11-28 12:21:58', '2025-11-28 12:21:58'),
(33, 'Zootopia', 'Zootopia', 2016, 'Comédia', 'Em uma cidade de animais, uma raposa falante se torna uma fugitiva ao ser acusada de um crime que não cometeu. O principal policial do local, o incontestável coelho, sai em sua busca.', 'uploads/movies/692994a43a1ab.jpg', 'animacao_kids', 'https://youtu.be/ljBuf7PI0zM?si=VXsI7m3c90X-Q6Vz', 'https://youtu.be/ljBuf7PI0zM?si=VXsI7m3c90X-Q6Vz', '2025-11-28 12:25:08', '2025-11-28 12:25:08'),
(34, 'Jumanji', 'Jumanji', 1995, 'Aventura', 'Alan Parrish desapareceu quando era menino e ninguém acredita na história de seu amigo de que ele foi sugado por um jogo de tabuleiro. Vinte e seis anos depois, duas crianças acham o jogo no sótão e, quando começam a jogar, Alan é libertado. Mas a disputa ainda não acabou e Alan precisa terminar antes de ser realmente solto.', 'uploads/movies/69299641a4a2c.jpg', 'aventura_kids', 'https://youtu.be/3_V917FKYcY?si=JjKO-EFYmVwDohKE', 'https://youtu.be/eTjDsENDZ6s?si=jQEJZJarg0LE37fK', '2025-11-28 12:32:01', '2025-11-28 12:32:01'),
(35, 'Operação Presente', 'Arthur Christmas', 2011, 'Aventura', 'O Papai Noel entrega presentes para todas as crianças do mundo. Porém, neste Natal, alguém corre o risco de ficar sem eles. Então, Arthur, o filho do Papai Noel, tem uma missão urgente: entregar o presente de uma garotinha o mais rápido possível.', 'uploads/movies/6929979d75d89.jpg', 'aventura_kids', 'https://youtu.be/7tk-WZSqIGQ?si=M6pUb75I4TDgdd5j', 'https://youtu.be/7tk-WZSqIGQ?si=M6pUb75I4TDgdd5j', '2025-11-28 12:37:25', '2025-11-28 12:37:49'),
(36, 'O Rei da Montanha', 'The Ash Lad: In the Hall of the Mountain King', 2017, 'Aventura', 'Espen Ash Lad, filho de um pobre agricultor, embarca em uma missão perigosa com seus irmãos para salvar a princesa de um vilão conhecido como o Rei da Montanha. O objetivo é ganhar uma recompensa e salvar a fazenda da sua família da ruína.', 'uploads/movies/6929983d85679.jpg', 'aventura_kids', 'https://youtu.be/wIK0-vZ2nI0?si=bvYpZj7D77eANIVJ', 'https://youtu.be/zUbal5GO84Q?si=jS-rnfvNPBDkfg4h', '2025-11-28 12:40:29', '2025-11-28 12:40:29'),
(37, ' A Bailarina', 'Ballerina', 2016, 'Musical', 'Uma sonhadora menina órfã toma uma atitude arriscada: fugir para Paris e realizar o sonho de ser uma grande bailarina. Lá, ela decide se passar por outra pessoa, e consegue uma vaga no Grand Opera, onde vai aprontar muitas aventuras.', 'uploads/movies/692998e427744.jpg', 'musical_kids', 'https://youtu.be/XD_mrJpWFwI?si=MOKQ-t0IyGJjg1Em', 'https://youtu.be/L0RA1vmlpwQ?si=ocaMtK74QyAeN12U', '2025-11-28 12:43:16', '2025-11-28 12:43:16'),
(38, 'Arca de Noé', 'Arca de Noé', 2024, 'Musical', 'Vini é um ratinho poeta muito tímido enquanto Tito é um grande músico. Os dois sonham em espalhar alegria com suas canções, mas o dilúvio acaba com todos os seus planos. Assim, os dois armam um plano mirabolante para entrar na arca de Noé.', 'uploads/movies/692999a0dac56.webp', 'musical_kids', 'https://youtu.be/hPYRmPkG8rE?si=oXIY6ZI04ZsrMuFZ', 'https://youtu.be/hPYRmPkG8rE?si=oXIY6ZI04ZsrMuFZ', '2025-11-28 12:46:24', '2025-11-28 12:46:24'),
(39, 'Viva - A Vida é uma Festa', 'COCO', 2017, 'Musical', 'pesar da proibição da música por gerações de sua família, o jovem Miguel sonha em se tornar um músico talentoso como seu ídolo Ernesto de la Cruz. Desesperado para provar seu talento, Miguel se encontra na deslumbrante e colorida Terra dos Mortos.', 'uploads/movies/69299a292525e.jpg', 'musical_kids', 'https://youtu.be/iLmZZV-Nkuk?si=rKws5FxrZQYMn2Bi', 'https://youtu.be/iLmZZV-Nkuk?si=rKws5FxrZQYMn2Bi', '2025-11-28 12:48:41', '2025-11-28 12:48:41'),
(40, 'Escola de Rock', 'School of rock', 2003, 'Musical', 'Um músico desempregado, Dewey Finn se passa por professor substituto em uma escola rigorosa. Descobre talentos musicais entre os alunos e secretamente forma uma banda com eles, transformando suas vidas enquanto esconde a verdade dos pais e diretores.', 'uploads/movies/69299afe4a0e6.jpg', 'musical_kids', 'https://youtu.be/CpiYC1089y8?si=axZW9GNvPFryG7zt', 'https://youtu.be/TExoc0MG4I4?si=FYHvofIKCZvyv5zK', '2025-11-28 12:52:14', '2025-11-28 12:52:14'),
(41, 'O som do Coração', 'August Rush', 2007, 'Musical', 'Evan, um músico talentoso, foge do orfanato e vai a Nova York em busca de seus pais. Em sua procura, ele encontra Wizard, um sem-teto que vive em um teatro abandonado. Depois de descobrir seu talento, Wizard dá ao garoto o nome de August Rush e traça um plano para lucrar às custas dele. ', 'uploads/movies/69299b6ae6ae7.webp', 'musical_kids', 'https://youtu.be/Ow0R7n94hzI?si=D7YMBQjrSBZiPSEO', 'https://youtu.be/ww0HQFE4wHU?si=u8oy2wjiID0tRIFh', '2025-11-28 12:54:02', '2025-11-28 12:54:02'),
(42, 'teste edição', 'bring her back', 2025, 'Terror', 'teste exemplo', 'uploads/movies/692d88f79f855.jpg', 'novidades', 'https://youtu.be/etWl1Tc-PUE?si=7AGEkXdF_zzjOQb3', 'https://www.youtube.com/watch?v=PqbcR06rHu8', '2025-12-01 12:24:23', '2025-12-01 12:25:09');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passHash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `perfil` enum('admin','usuario') DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `passHash`, `created_at`, `perfil`) VALUES
(3, 'Admin', 'admin@gmail.com', '$2y$10$nF.CK0IcgwxYxbXZCIHvT.ufeGlOH.9kgbjjNefruHd6niiI5cwAi', '2025-10-20 12:53:28', 'admin'),
(5, 'Greggori', 'Greggori@gmail.com', '$2y$10$68S7LHfwLICQgA.d2G5lPuKktqTrMDpcpzfly9Gs/gOD7tIerYgvC', '2025-10-20 13:13:01', 'usuario'),
(19, 'Joãozinho', 'joao@gmail.com', '$2y$10$c.HGD4wqTh2CY75nTqH1eOMSyxz1rLcwCP0dm7NI0AYnF92.1z4DK', '2025-11-28 13:00:34', 'usuario');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `assinaturas`
--
ALTER TABLE `assinaturas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_status` (`usuario_id`,`status`);

--
-- Índices de tabela `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `assinaturas`
--
ALTER TABLE `assinaturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `assinaturas`
--
ALTER TABLE `assinaturas`
  ADD CONSTRAINT `assinaturas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
