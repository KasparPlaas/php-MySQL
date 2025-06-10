-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: hoggar.elkdata.ee
-- Generation Time: Jun 10, 2025 at 06:06 PM
-- Server version: 11.4.5-MariaDB-log
-- PHP Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vhost137852s2`
--

-- --------------------------------------------------------

--
-- Table structure for table `sport2025`
--

CREATE TABLE `sport2025` (
  `id` int(11) NOT NULL,
  `fullname` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `age` int(2) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `category` varchar(20) NOT NULL,
  `reg_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `sport2025`
--

INSERT INTO `sport2025` (`id`, `fullname`, `email`, `age`, `gender`, `category`, `reg_time`) VALUES
(8, 'Elberta Culley', 'eculley7@sphinn.com', 54, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(9, 'Bird Sewart', 'bsewart8@taobao.com', 38, 'helikopter', 'kaugus', '2025-05-28 15:30:47'),
(10, 'Jerry Cansfield', 'jcansfield9@sina.com.cn', 26, 'naine', 'ujumine', '2025-05-28 15:30:47'),
(11, 'Farica Storton', 'fstortona@foxnews.com', 45, 'helikopter', 'kaugus', '2025-05-28 15:30:47'),
(12, 'Hermine Gawthorp', 'hgawthorpb@craigslist.org', 44, 'naine', 'ujumine', '2025-05-28 15:30:47'),
(13, 'Agneta Mullan', 'amullanc@furl.net', 39, 'naine', 'jooks', '2025-05-28 15:30:47'),
(14, 'Courtnay Lotte', 'clotted@prlog.org', 57, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(16, 'Yves Lount', 'ylountf@skyrock.com', 22, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(17, 'Larry Palphreyman', 'lpalphreymang@bbc.co.uk', 43, 'helikopter', 'kaugus', '2025-05-28 15:30:47'),
(18, 'Bree Dabling', 'bdablingh@liveinternet.ru', 47, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(19, 'Carmita Tesyro', 'ctesyroi@purevolume.com', 27, 'naine', 'kaugus', '2025-05-28 15:30:47'),
(20, 'Erl Rainforth', 'erainforthj@unc.edu', 23, 'naine', 'ujumine', '2025-05-28 15:30:47'),
(21, 'Aundrea Staning', 'astaningk@dailymail.co.uk', 48, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(22, 'Ellsworth Farrar', 'efarrarl@cocolog-nifty.com', 17, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(23, 'Bealle Lyst', 'blystm@last.fm', 56, 'mees', 'jooks', '2025-05-28 15:30:47'),
(24, 'Ellyn Rhodus', 'erhodusn@cyberchimps.com', 47, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(25, 'Vinnie Haggas', 'vhaggaso@vimeo.com', 58, 'naine', 'ujumine', '2025-05-28 15:30:47'),
(26, 'Theresa Stump', 'tstumpp@cyberchimps.com', 54, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(28, 'Marney Tolcharde', 'mtolcharder@who.int', 23, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(29, 'Adriano Kettle', 'akettles@dmoz.org', 43, 'mees', 'ujumine', '2025-05-28 15:30:47'),
(30, 'Isabelle Dandy', 'idandyt@themeforest.net', 38, 'helikopter', 'jooks', '2025-05-28 15:30:47'),
(31, 'Marley Judd', 'mjuddu@ning.com', 48, 'mees', 'ujumine', '2025-05-28 15:30:47'),
(32, 'Montgomery Blackley', 'mblackleyv@hexun.com', 46, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(33, 'Benyamin Dickie', 'bdickiew@forbes.com', 46, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(34, 'Lurlene Jeanes', 'ljeanesx@utexas.edu', 50, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(35, 'Nev Digger', 'ndiggery@google.cn', 47, 'naine', 'ujumine', '2025-05-28 15:30:47'),
(36, 'Ethe Rann', 'erannz@unesco.org', 23, 'helikopter', 'kaugus', '2025-05-28 15:30:47'),
(37, 'Reube De Filippo', 'rde10@joomla.org', 58, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(38, 'Sidney Linck', 'slinck11@icq.com', 28, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(39, 'Lisle Lyle', 'llyle12@redcross.org', 17, 'helikopter', 'kaugus', '2025-05-28 15:30:47'),
(40, 'Iain Glasscoe', 'iglasscoe13@fema.gov', 59, 'helikopter', 'jooks', '2025-05-28 15:30:47'),
(41, 'Gerhardt Van Merwe', 'gvan14@nbcnews.com', 18, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(42, 'Lanie Tunny', 'ltunny15@jugem.jp', 17, 'naine', 'ujumine', '2025-05-28 15:30:47'),
(43, 'Putnam Bussen', 'pbussen16@live.com', 53, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(44, 'Aimil Buswell', 'abuswell17@china.com.cn', 18, 'mees', 'jooks', '2025-05-28 15:30:47'),
(45, 'Ingra Liversley', 'iliversley18@hubpages.com', 49, 'mees', 'ujumine', '2025-05-28 15:30:47'),
(46, 'Carolynn McTrustrie', 'cmctrustrie19@ucsd.edu', 52, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(47, 'Jacqueline Boydell', 'jboydell1a@4shared.com', 47, 'mees', 'jooks', '2025-05-28 15:30:47'),
(48, 'Opalina Aldin', 'oaldin1b@theguardian.com', 31, 'naine', 'kaugus', '2025-05-28 15:30:47'),
(49, 'Raddie Klimpt', 'rklimpt1c@accuweather.com', 28, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(50, 'Melinda Ferby', 'mferby1d@yellowbook.com', 32, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(51, 'Peggie Emmet', 'pemmet1e@smh.com.au', 56, 'helikopter', 'jooks', '2025-05-28 15:30:47'),
(52, 'Galven Fontes', 'gfontes1f@msn.com', 43, 'mees', 'ujumine', '2025-05-28 15:30:47'),
(53, 'Waneta Skyrme', 'wskyrme1g@time.com', 30, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(54, 'Der Ickeringill', 'dickeringill1h@tripadvisor.com', 25, 'helikopter', 'kaugus', '2025-05-28 15:30:47'),
(55, 'Mindy Tourot', 'mtourot1i@umn.edu', 60, 'helikopter', 'jooks', '2025-05-28 15:30:47'),
(56, 'Leslie Belin', 'lbelin1j@sitemeter.com', 58, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(57, 'Loren Chateau', 'lchateau1k@shinystat.com', 31, 'mees', 'ujumine', '2025-05-28 15:30:47'),
(58, 'Dynah Peter', 'dpeter1l@irs.gov', 17, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(59, 'Alex Reis', 'areis1m@msu.edu', 59, 'helikopter', 'jooks', '2025-05-28 15:30:47'),
(60, 'Henrieta Crosskell', 'hcrosskell1n@sphinn.com', 47, 'mees', 'ujumine', '2025-05-28 15:30:47'),
(61, 'Shannon Shutler', 'sshutler1o@list-manage.com', 21, 'helikopter', 'jooks', '2025-05-28 15:30:47'),
(62, 'Federica Cowdray', 'fcowdray1p@angelfire.com', 27, 'naine', 'kaugus', '2025-05-28 15:30:47'),
(63, 'Stephen Greser', 'sgreser1q@home.pl', 32, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(64, 'Stephine Espinet', 'sespinet1r@pen.io', 36, 'naine', 'jooks', '2025-05-28 15:30:47'),
(65, 'Frederick Lowmass', 'flowmass1s@list-manage.com', 34, 'naine', 'kaugus', '2025-05-28 15:30:47'),
(66, 'Reynard Ibbs', 'ribbs1t@tinypic.com', 30, 'mees', 'ujumine', '2025-05-28 15:30:47'),
(67, 'Barby Butterly', 'bbutterly1u@exblog.jp', 35, 'helikopter', 'jooks', '2025-05-28 15:30:47'),
(68, 'Rivy Waiton', 'rwaiton1v@unesco.org', 20, 'mees', 'ujumine', '2025-05-28 15:30:47'),
(69, 'Willyt Maulin', 'wmaulin1w@who.int', 55, 'mees', 'jooks', '2025-05-28 15:30:47'),
(70, 'Viola Dilston', 'vdilston1x@newsvine.com', 40, 'helikopter', 'kaugus', '2025-05-28 15:30:47'),
(71, 'Helyn Bolland', 'hbolland1y@merriam-webster.com', 18, 'helikopter', 'kaugus', '2025-05-28 15:30:47'),
(72, 'Gene Juhruke', 'gjuhruke1z@mashable.com', 32, 'naine', 'kaugus', '2025-05-28 15:30:47'),
(73, 'Barris Danher', 'bdanher20@moonfruit.com', 26, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(74, 'Virginie Hearse', 'vhearse21@spotify.com', 46, 'helikopter', 'kaugus', '2025-05-28 15:30:47'),
(75, 'Lindy Rawlin', 'lrawlin22@ustream.tv', 54, 'naine', 'ujumine', '2025-05-28 15:30:47'),
(76, 'Gretchen Simeonov', 'gsimeonov23@deliciousdays.com', 41, 'mees', 'jooks', '2025-05-28 15:30:47'),
(77, 'Clair Pammenter', 'cpammenter24@statcounter.com', 60, 'naine', 'kaugus', '2025-05-28 15:30:47'),
(78, 'Edsel Mc Carroll', 'emc25@photobucket.com', 24, 'helikopter', 'jooks', '2025-05-28 15:30:47'),
(79, 'Tawnya Maccrae', 'tmaccrae26@rambler.ru', 23, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(80, 'Mandy Cabera', 'mcabera27@house.gov', 30, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(81, 'El Geach', 'egeach28@digg.com', 20, 'naine', 'kaugus', '2025-05-28 15:30:47'),
(82, 'Iorgo Flounders', 'iflounders29@netvibes.com', 28, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(83, 'Lesya Dalziell', 'ldalziell2a@about.com', 44, 'helikopter', 'kaugus', '2025-05-28 15:30:47'),
(84, 'Holly-anne Hutchins', 'hhutchins2b@comsenz.com', 17, 'mees', 'jooks', '2025-05-28 15:30:47'),
(85, 'Sidonia Hart', 'shart2c@blogger.com', 22, 'helikopter', 'jooks', '2025-05-28 15:30:47'),
(86, 'Murdoch Kimbell', 'mkimbell2d@ft.com', 59, 'helikopter', 'kaugus', '2025-05-28 15:30:47'),
(87, 'Tripp Banaszewski', 'tbanaszewski2e@exblog.jp', 43, 'naine', 'jooks', '2025-05-28 15:30:47'),
(88, 'Haskel McGeown', 'hmcgeown2f@xinhuanet.com', 48, 'naine', 'kaugus', '2025-05-28 15:30:47'),
(89, 'Mona Novkovic', 'mnovkovic2g@amazonaws.com', 28, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(90, 'Giffard Videneev', 'gvideneev2h@nyu.edu', 19, 'mees', 'ujumine', '2025-05-28 15:30:47'),
(91, 'Chucho Kubas', 'ckubas2i@sitemeter.com', 31, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(92, 'Marylin Arnowitz', 'marnowitz2j@mozilla.org', 34, 'helikopter', 'jooks', '2025-05-28 15:30:47'),
(93, 'Donnie Roset', 'droset2k@tuttocitta.it', 34, 'helikopter', 'jooks', '2025-05-28 15:30:47'),
(94, 'Lu Emanuel', 'lemanuel2l@senate.gov', 29, 'mees', 'ujumine', '2025-05-28 15:30:47'),
(95, 'Bobbette Kerans', 'bkerans2m@princeton.edu', 38, 'mees', 'ujumine', '2025-05-28 15:30:47'),
(96, 'Ellis Watton', 'ewatton2n@angelfire.com', 42, 'mees', 'kaugus', '2025-05-28 15:30:47'),
(97, 'Willabella Ziemke', 'wziemke2o@usda.gov', 50, 'helikopter', 'ujumine', '2025-05-28 15:30:47'),
(98, 'Marquita Medcraft', 'mmedcraft2p@soundcloud.com', 54, 'naine', 'kaugus', '2025-05-28 15:30:47'),
(99, 'Harper Ennion', 'hennion2q@uiuc.edu', 57, 'naine', 'ujumine', '2025-05-28 15:30:47'),
(100, 'Alistair Monery', 'amonery2r@issuu.com', 45, 'mees', 'jooks', '2025-05-28 15:30:47'),
(101, 'Peeter Kalbus', 'peeter.onameeter@gmail.com', 21, 'kahejalgne', 'korvpall', '2025-06-04 12:42:21'),
(102, 'Mario Metshein', 'metsheinmario@mario.mario', 20, 'meheline m', 'php WorldChampion', '2025-06-04 12:44:28'),
(103, 'Jarno Orusalu', 'orusalu.jarno@hkhk.edu.ee', 18, 'trans', 'diivanisport', '2025-06-04 12:50:37'),
(104, 'roomet', 'qwer@adfsdg.ee', 18, 'sdf', 'sdgf', '2025-06-05 12:00:59'),
(105, 'tehvan marjapuu', 'tehvan.marjapuu@hkhk.edu.ee', 18, 'puudulik', 'pihumine', '2025-06-06 07:39:34'),
(106, 'anti', 'anti.meere@gmiil.ee', 69, 'isane adap', 'Ã¼henduse loomine em', '2025-06-06 09:55:56'),
(107, 'Rasmus AltmÃ¤e', 'lohh@lohh.ee', 17, 'unidentifi', 'pihumine', '2025-06-06 11:31:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user`, `password`) VALUES
(3, 'admin', '$2y$10$RzwpR/Pz2ULBu3HoCjSPJertKX00czbypsKXnxO5VRmK9h9KaGzxS');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sport2025`
--
ALTER TABLE `sport2025`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sport2025`
--
ALTER TABLE `sport2025`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
