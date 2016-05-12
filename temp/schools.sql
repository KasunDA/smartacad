-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 11, 2016 at 06:27 PM
-- Server version: 5.6.26
-- PHP Version: 5.6.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `schools`
--

DELIMITER $$
--
-- Functions
--
DROP FUNCTION IF EXISTS `SPLIT_STR`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `SPLIT_STR`(
	x VARCHAR(255),
	delim VARCHAR(12),
	pos INT
) RETURNS varchar(255) CHARSET latin1
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
													 LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
								 delim, '')$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `lgas`
--

DROP TABLE IF EXISTS `lgas`;
CREATE TABLE IF NOT EXISTS `lgas` (
  `lga_id` int(3) unsigned NOT NULL,
  `lga` varchar(50) DEFAULT NULL,
  `state_id` int(3) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=781 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lgas`
--

INSERT INTO `lgas` (`lga_id`, `lga`, `state_id`) VALUES
(1, 'Aba North', 1),
(2, 'Aba South', 1),
(3, 'Arochukwu', 1),
(4, 'Bende', 1),
(5, 'Ikwuano', 1),
(6, 'Isiala-Ngwa North', 1),
(7, 'Isiala-Ngwa South', 1),
(8, 'Isuikwato', 1),
(9, 'Ngwa', 1),
(10, 'Obi Nwa', 1),
(11, 'Ohafia', 1),
(12, 'Osisioma', 1),
(13, 'Ugwunagbo', 1),
(14, 'Ukwa East', 1),
(15, 'Ukwa West', 1),
(16, 'Umuahia North', 1),
(17, 'Umuahia South', 1),
(18, 'Umu-Neochi', 1),
(19, 'Demsa', 2),
(20, 'Fufore', 2),
(21, 'Ganaye', 2),
(22, 'Gireri', 2),
(23, 'Gombi', 2),
(24, 'Guyuk', 2),
(25, 'Hong', 2),
(26, 'Jada', 2),
(27, 'Lamurde', 2),
(28, 'Madagali', 2),
(29, 'Maiha ', 2),
(30, 'Mayo-Belwa', 2),
(31, 'Michika', 2),
(32, 'Mubi North', 2),
(33, 'Mubi South', 2),
(34, 'Numan', 2),
(35, 'Shelleng', 2),
(36, 'Song', 2),
(37, 'Toungo', 2),
(38, 'Yola North', 2),
(39, 'Yola South', 2),
(40, 'Abak', 3),
(41, 'Eastern Obolo', 3),
(42, 'Eket', 3),
(43, 'Esit Eket', 3),
(44, 'Essien Udim', 3),
(45, 'Etim Ekpo', 3),
(46, 'Etinan', 3),
(47, 'Ibeno', 3),
(48, 'Ibesikpo Asutan', 3),
(49, 'Ibiono Ibom', 3),
(50, 'Ika', 3),
(51, 'Ikono', 3),
(52, 'Ikot Abasi', 3),
(53, 'Ikot Ekpene', 3),
(54, 'Ini', 3),
(55, 'Itu', 3),
(56, 'Mbo', 3),
(57, 'Mkpat Enin', 3),
(58, 'Nsit Atai', 3),
(59, 'Nsit Ibom', 3),
(60, 'Nsit Ubium', 3),
(61, 'Obot Akara', 3),
(62, 'Okobo', 3),
(63, 'Onna', 3),
(64, 'Oron ', 3),
(65, 'Oruk Anam', 3),
(66, 'Udung Uko', 3),
(67, 'Ukanafun', 3),
(68, 'Uruan', 3),
(69, 'Urue-Offong/Oruko', 3),
(70, 'Uyo', 3),
(71, 'Aguata', 4),
(72, 'Anambra East', 4),
(73, 'Anambra West', 4),
(74, 'Anaocha', 4),
(75, 'Awka North', 4),
(76, 'Awka South', 4),
(77, 'Ayamelum', 4),
(78, 'Dunukofia', 4),
(79, 'Ekwusigo', 4),
(80, 'Idemili North', 4),
(81, 'Idemili South', 4),
(82, 'Ihiala', 4),
(83, 'Njikoka', 4),
(84, 'Nnewi North', 4),
(85, 'Nnewi South', 4),
(86, 'Ogbaru', 4),
(87, 'Onitsha North', 4),
(88, 'Onitsha South', 4),
(89, 'Orumba North', 4),
(90, 'Orumba South', 4),
(91, 'Oyi ', 4),
(92, 'Alkaleri', 5),
(93, 'Bauchi', 5),
(94, 'Bogoro', 5),
(95, 'Damban', 5),
(96, 'Darazo', 5),
(97, 'Dass', 5),
(98, 'Ganjuwa', 5),
(99, 'Giade', 5),
(100, 'Itas/Gadau', 5),
(101, 'Jama''Are', 5),
(102, 'Katagum', 5),
(103, 'Kirfi', 5),
(104, 'Misau', 5),
(105, 'Ningi', 5),
(106, 'Shira', 5),
(107, 'Tafawa-Balewa', 5),
(108, 'Toro', 5),
(109, 'Warji', 5),
(110, 'Zaki ', 5),
(111, 'Brass', 32),
(112, 'Ekeremor', 32),
(113, 'Kolokuma/Opokuma', 32),
(114, 'Nembe', 32),
(115, 'Ogbia', 32),
(116, 'Sagbama', 32),
(117, 'Southern Jaw', 32),
(118, 'Yenegoa ', 32),
(119, 'Ado', 6),
(120, 'Agatu', 6),
(121, 'Apa', 6),
(122, 'Buruku', 6),
(123, 'Gboko', 6),
(124, 'Guma', 6),
(125, 'Gwer East', 6),
(126, 'Gwer West', 6),
(127, 'Katsina-Ala', 6),
(128, 'Konshisha', 6),
(129, 'Kwande', 6),
(130, 'Logo', 6),
(131, 'Makurdi', 6),
(132, 'Obi', 6),
(133, 'Ogbadibo', 6),
(134, 'Ohimini', 6),
(135, 'Oju', 6),
(136, 'Okpokwu', 6),
(137, 'Oturkpo', 6),
(138, 'Tarka', 6),
(139, 'Ukum', 6),
(140, 'Ushongo', 6),
(141, 'Vandeikya ', 6),
(142, 'Abadam', 7),
(143, 'Askira/Uba', 7),
(144, 'Bama', 7),
(145, 'Bayo', 7),
(146, 'Biu', 7),
(147, 'Chibok', 7),
(148, 'Damboa', 7),
(149, 'Dikwa', 7),
(150, 'Gubio', 7),
(151, 'Guzamala', 7),
(152, 'Gwoza', 7),
(153, 'Hawul', 7),
(154, 'Jere', 7),
(155, 'Kaga', 7),
(156, 'Kala/Balge', 7),
(157, 'Konduga', 7),
(158, 'Kukawa', 7),
(159, 'Kwaya Kusar', 7),
(160, 'Mafa', 7),
(161, 'Magumeri', 7),
(162, 'Maiduguri', 7),
(163, 'Marte', 7),
(164, 'Mobbar', 7),
(165, 'Monguno', 7),
(166, 'Ngala', 7),
(167, 'Nganzai', 7),
(168, 'Shani ', 7),
(169, 'Abi', 8),
(170, 'Akamkpa', 8),
(171, 'Akpabuyo', 8),
(172, 'Bakassi', 8),
(173, 'Bekwara', 8),
(174, 'Biase', 8),
(175, 'Boki', 8),
(176, 'Calabar Municipality', 8),
(177, 'Calabar South', 8),
(178, 'Etung', 8),
(179, 'Ikom', 8),
(180, 'Obanliku', 8),
(181, 'Obudu', 8),
(182, 'Odubra', 8),
(183, 'Odukpani', 8),
(184, 'Ogoja', 8),
(185, 'Yala', 8),
(186, 'Yarkur', 8),
(187, 'Aniocha', 9),
(188, 'Aniocha South', 9),
(189, 'Bomadi', 9),
(190, 'Burutu', 9),
(191, 'Ethiope East', 9),
(192, 'Ethiope West', 9),
(193, 'Ika North-East', 9),
(194, 'Ika South', 9),
(195, 'Isoko North', 9),
(196, 'Isoko South', 9),
(197, 'Ndokwa East', 9),
(198, 'Ndokwa West', 9),
(199, 'Okpe', 9),
(200, 'Oshimili', 9),
(201, 'Oshimili North', 9),
(202, 'Patani', 9),
(203, 'Sapele', 9),
(204, 'Udu', 9),
(205, 'Ughelli North', 9),
(206, 'Ughelli South', 9),
(207, 'Ukwani', 9),
(208, 'Uvwie', 9),
(209, 'Warri Central', 9),
(210, 'Warri North', 9),
(211, 'Warri South', 9),
(212, 'Abakaliki', 37),
(213, 'Afikpo North', 37),
(214, 'Afikpo South', 37),
(215, 'Ebonyi', 37),
(216, 'Ezza', 37),
(217, 'Ezza South', 37),
(218, 'Ishielu', 37),
(219, 'Ivo ', 37),
(220, 'Lkwo', 37),
(221, 'Ohaozara', 37),
(222, 'Ohaukwu', 37),
(223, 'Onicha', 37),
(224, 'Central', 10),
(225, 'Egor', 10),
(226, 'Esan Central', 10),
(227, 'Esan North-East', 10),
(228, 'Esan South-East ', 10),
(229, 'Esan West', 10),
(230, 'Etsako Central', 10),
(231, 'Etsako East ', 10),
(232, 'Igueben', 10),
(233, 'Oredo', 10),
(234, 'Orhionwon', 10),
(235, 'Ovia South-East', 10),
(236, 'Ovia Southwest', 10),
(237, 'Uhunmwonde', 10),
(238, 'Ukpoba', 10),
(239, 'Ado', 36),
(240, 'Efon', 36),
(241, 'Ekiti South-West', 36),
(242, 'Ekiti-East', 36),
(243, 'Ekiti-West ', 36),
(244, 'Emure/Ise/Orun', 36),
(245, 'Gbonyin', 36),
(246, 'Ido/Osi', 36),
(247, 'Ijero', 36),
(248, 'Ikare', 36),
(249, 'Ikole', 36),
(250, 'Ilejemeje.', 36),
(251, 'Irepodun', 36),
(252, 'Ise/Orun ', 36),
(253, 'Moba', 36),
(254, 'Oye', 36),
(255, 'Aninri', 11),
(256, 'Enugu Eas', 11),
(257, 'Enugu North', 11),
(258, 'Enugu South', 0),
(259, 'Ezeagu', 11),
(260, 'Igbo-Ekiti', 11),
(261, 'Igboeze North', 11),
(262, 'Igbo-Eze South', 11),
(263, 'Isi-Uzo', 11),
(264, 'Nkanu', 11),
(265, 'Nkanu East', 11),
(266, 'Nsukka', 11),
(267, 'Oji-River', 11),
(268, 'Udenu. ', 11),
(269, 'Udi Agwu', 11),
(270, 'Uzo-Uwani', 11),
(271, 'Abaji', 31),
(272, 'Abuja Municipal', 31),
(273, 'Bwari', 31),
(274, 'Gwagwalada', 31),
(275, 'Kuje', 31),
(276, 'Kwali', 31),
(277, 'Akko', 33),
(278, 'Balanga', 33),
(279, 'Billiri', 33),
(280, 'Dukku', 33),
(281, 'Funakaye', 33),
(282, 'Gombe', 33),
(283, 'Kaltungo', 33),
(284, 'Kwami', 33),
(285, 'Nafada/Bajoga ', 33),
(286, 'Shomgom', 33),
(287, 'Yamaltu/Delta. ', 33),
(288, 'Aboh-Mbaise', 12),
(289, 'Ahiazu-Mbaise', 12),
(290, 'Ehime-Mbano', 12),
(291, 'Ezinihitte', 12),
(292, 'Ideato North', 12),
(293, 'Ideato South', 12),
(294, 'Ihitte/Uboma', 12),
(295, 'Ikeduru', 12),
(296, 'Isiala Mbano', 12),
(297, 'Isu', 12),
(298, 'Mbaitoli', 12),
(299, 'Mbaitoli', 12),
(300, 'Ngor-Okpala', 12),
(301, 'Njaba', 12),
(302, 'Nkwerre', 12),
(303, 'Nwangele', 12),
(304, 'Obowo', 12),
(305, 'Oguta', 12),
(306, 'Ohaji/Egbema', 12),
(307, 'Okigwe', 12),
(308, 'Orlu', 12),
(309, 'Orsu', 12),
(310, 'Oru East', 12),
(311, 'Oru West', 12),
(312, 'Owerri North', 12),
(313, 'Owerri West ', 12),
(314, 'Owerri-Municipal', 12),
(315, 'Auyo', 13),
(316, 'Babura', 13),
(317, 'Biriniwa', 13),
(318, 'Birni Kudu', 13),
(319, 'Buji', 13),
(320, 'Dutse', 13),
(321, 'Gagarawa', 13),
(322, 'Garki', 13),
(323, 'Gumel', 13),
(324, 'Guri', 13),
(325, 'Gwaram', 13),
(326, 'Gwiwa', 13),
(327, 'Hadejia', 13),
(328, 'Jahun', 13),
(329, 'Kafin Hausa', 13),
(330, 'Kaugama Kazaure', 13),
(331, 'Kiri Kasamma', 13),
(332, 'Kiyawa', 13),
(333, 'Maigatari', 13),
(334, 'Malam Madori', 13),
(335, 'Miga', 13),
(336, 'Ringim', 13),
(337, 'Roni', 13),
(338, 'Sule-Tankarkar', 13),
(339, 'Taura ', 13),
(340, 'Yankwashi ', 13),
(341, 'Birni-Gwari', 15),
(342, 'Chikun', 15),
(343, 'Giwa', 15),
(344, 'Igabi', 15),
(345, 'Ikara', 15),
(346, 'Jaba', 15),
(347, 'Jema''A', 15),
(348, 'Kachia', 15),
(349, 'Kaduna North', 15),
(350, 'Kaduna South', 15),
(351, 'Kagarko', 15),
(352, 'Kajuru', 15),
(353, 'Kaura', 15),
(354, 'Kauru', 15),
(355, 'Kubau', 15),
(356, 'Kudan', 15),
(357, 'Lere', 15),
(358, 'Makarfi', 15),
(359, 'Sabon-Gari', 15),
(360, 'Sanga', 15),
(361, 'Soba', 15),
(362, 'Zango-Kataf', 15),
(363, 'Zaria ', 15),
(364, 'Ajingi', 17),
(365, 'Albasu', 17),
(366, 'Bagwai', 17),
(367, 'Bebeji', 17),
(368, 'Bichi', 17),
(369, 'Bunkure', 17),
(370, 'Dala', 17),
(371, 'Dambatta', 17),
(372, 'Dawakin Kudu', 17),
(373, 'Dawakin Tofa', 17),
(374, 'Doguwa', 17),
(375, 'Fagge', 17),
(376, 'Gabasawa', 17),
(377, 'Garko', 17),
(378, 'Garum', 17),
(379, 'Gaya', 17),
(380, 'Gezawa', 17),
(381, 'Gwale', 17),
(382, 'Gwarzo', 17),
(383, 'Kabo', 17),
(384, 'Kano Municipal', 17),
(385, 'Karaye', 17),
(386, 'Kibiya', 17),
(387, 'Kiru', 17),
(388, 'Kumbotso', 17),
(389, 'Kunchi', 17),
(390, 'Kura', 17),
(391, 'Madobi', 17),
(392, 'Makoda', 17),
(393, 'Mallam', 17),
(394, 'Minjibir', 17),
(395, 'Nasarawa', 17),
(396, 'Rano', 17),
(397, 'Rimin Gado', 17),
(398, 'Rogo', 17),
(399, 'Shanono', 17),
(400, 'Sumaila', 17),
(401, 'Takali', 17),
(402, 'Tarauni', 17),
(403, 'Tofa', 17),
(404, 'Tsanyawa', 17),
(405, 'Tudun Wada', 17),
(406, 'Ungogo', 17),
(407, 'Warawa', 17),
(408, 'Wudil', 17),
(409, 'Bakori', 18),
(410, 'Batagarawa', 18),
(411, 'Batsari', 18),
(412, 'Baure', 18),
(413, 'Bindawa', 18),
(414, 'Charanchi', 18),
(415, 'Dan Musa', 18),
(416, 'Dandume', 18),
(417, 'Danja', 18),
(418, 'Daura', 18),
(419, 'Dutsi', 18),
(420, 'Dutsin-Ma', 18),
(421, 'Faskari', 18),
(422, 'Funtua', 18),
(423, 'Ingawa', 18),
(424, 'Jibia', 18),
(425, 'Kafur', 18),
(426, 'Kaita', 18),
(427, 'Kankara', 18),
(428, 'Kankia', 18),
(429, 'Katsina', 18),
(430, 'Kurfi', 18),
(431, 'Kusada', 18),
(432, 'Mai''Adua', 18),
(433, 'Malumfashi', 18),
(434, 'Mani', 18),
(435, 'Mashi', 18),
(436, 'Matazuu', 18),
(437, 'Musawa', 18),
(438, 'Rimi', 18),
(439, 'Sabuwa', 18),
(440, 'Safana', 18),
(441, 'Sandamu', 18),
(442, 'Zango ', 18),
(443, 'Aleiro', 14),
(444, 'Arewa-Dandi', 14),
(445, 'Argungu', 14),
(446, 'Augie', 14),
(447, 'Bagudo', 14),
(448, 'Birnin Kebbi', 14),
(449, 'Bunza', 14),
(450, 'Dandi ', 14),
(451, 'Fakai', 14),
(452, 'Gwandu', 14),
(453, 'Jega', 14),
(454, 'Kalgo ', 14),
(455, 'Koko/Besse', 14),
(456, 'Maiyama', 14),
(457, 'Ngaski', 14),
(458, 'Sakaba', 14),
(459, 'Shanga', 14),
(460, 'Suru', 14),
(461, 'Wasagu/Danko', 14),
(462, 'Yauri', 14),
(463, 'Zuru ', 14),
(464, 'Adavi', 16),
(465, 'Ajaokuta', 16),
(466, 'Ankpa', 16),
(467, 'Bassa', 16),
(468, 'Dekina', 16),
(469, 'Ibaji', 16),
(470, 'Idah', 16),
(471, 'Igalamela-Odolu', 16),
(472, 'Ijumu', 16),
(473, 'Kabba/Bunu', 16),
(474, 'Kogi', 16),
(475, 'Lokoja', 16),
(476, 'Mopa-Muro', 16),
(477, 'Ofu', 16),
(478, 'Ogori/Mangongo', 16),
(479, 'Okehi', 16),
(480, 'Okene', 16),
(481, 'Olamabolo', 16),
(482, 'Omala', 16),
(483, 'Yagba East ', 16),
(484, 'Yagba West', 16),
(485, 'Asa', 19),
(486, 'Baruten', 19),
(487, 'Edu', 19),
(488, 'Ekiti', 19),
(489, 'Ifelodun', 19),
(490, 'Ilorin East', 19),
(491, 'Ilorin West', 19),
(492, 'Irepodun', 19),
(493, 'Isin', 19),
(494, 'Kaiama', 19),
(495, 'Moro', 19),
(496, 'Offa', 19),
(497, 'Oke-Ero', 19),
(498, 'Oyun', 19),
(499, 'Pategi ', 19),
(500, 'Agege', 20),
(501, 'Ajeromi-Ifelodun', 20),
(502, 'Alimosho', 20),
(503, 'Amuwo-Odofin', 20),
(504, 'Apapa', 20),
(505, 'Badagry', 20),
(506, 'Epe', 20),
(507, 'Eti-Osa', 20),
(508, 'Ibeju/Lekki', 20),
(509, 'Ifako-Ijaye ', 20),
(510, 'Ikeja', 20),
(511, 'Ikorodu', 20),
(512, 'Kosofe', 20),
(513, 'Lagos Island', 20),
(514, 'Lagos Mainland', 20),
(515, 'Mushin', 20),
(516, 'Ojo', 20),
(517, 'Oshodi-Isolo', 20),
(518, 'Shomolu', 20),
(519, 'Surulere', 20),
(520, 'Akwanga', 34),
(521, 'Awe', 34),
(522, 'Doma', 34),
(523, 'Karu', 34),
(524, 'Keana', 34),
(525, 'Keffi', 34),
(526, 'Kokona', 34),
(527, 'Lafia', 34),
(528, 'Nasarawa', 34),
(529, 'Nasarawa-Eggon', 34),
(530, 'Obi', 34),
(531, 'Toto', 34),
(532, 'Wamba ', 34),
(533, 'Agaie', 21),
(534, 'Agwara', 21),
(535, 'Bida', 21),
(536, 'Borgu', 21),
(537, 'Bosso', 21),
(538, 'Chanchaga', 21),
(539, 'Edati', 21),
(540, 'Gbako', 21),
(541, 'Gurara', 21),
(542, 'Katcha', 21),
(543, 'Kontagora ', 21),
(544, 'Lapai', 21),
(545, 'Lavun', 21),
(546, 'Magama', 21),
(547, 'Mariga', 21),
(548, 'Mashegu', 21),
(549, 'Mokwa', 21),
(550, 'Muya', 21),
(551, 'Paikoro', 21),
(552, 'Rafi', 21),
(553, 'Rijau', 21),
(554, 'Shiroro', 21),
(555, 'Suleja', 21),
(556, 'Tafa', 21),
(557, 'Wushishi', 21),
(558, 'Abeokuta North', 23),
(559, 'Abeokuta South', 23),
(560, 'Ado-Odo/Ota', 23),
(561, 'Egbado North', 23),
(562, 'Egbado South', 23),
(563, 'Ewekoro', 23),
(564, 'Ifo', 23),
(565, 'Ijebu East', 23),
(566, 'Ijebu North', 23),
(567, 'Ijebu North East', 23),
(568, 'Ijebu Ode', 23),
(569, 'Ikenne', 23),
(570, 'Imeko-Afon', 23),
(571, 'Ipokia', 23),
(572, 'Obafemi-Owode', 23),
(573, 'Odeda', 23),
(574, 'Odogbolu', 23),
(575, 'Ogun Waterside', 23),
(576, 'Remo North', 23),
(577, 'Shagamu', 23),
(578, 'Akoko North East', 22),
(579, 'Akoko North West', 22),
(580, 'Akoko South Akure East', 22),
(581, 'Akoko South West', 22),
(582, 'Akure North', 22),
(583, 'Akure South', 22),
(584, 'Ese-Odo', 22),
(585, 'Idanre', 22),
(586, 'Ifedore', 22),
(587, 'Ilaje', 22),
(588, 'Ile-Oluji', 22),
(589, 'Irele', 22),
(590, 'Odigbo', 22),
(591, 'Okeigbo', 22),
(592, 'Okitipupa', 22),
(593, 'Ondo East', 22),
(594, 'Ondo West', 22),
(595, 'Ose', 22),
(596, 'Owo ', 22),
(597, 'Aiyedade', 24),
(598, 'Aiyedire', 24),
(599, 'Atakumosa East', 24),
(600, 'Atakumosa West', 24),
(601, 'Boluwaduro', 24),
(602, 'Boripe', 24),
(603, 'Ede North', 24),
(604, 'Ede South', 24),
(605, 'Egbedore', 24),
(606, 'Ejigbo', 24),
(607, 'Ife Central', 24),
(608, 'Ife East', 24),
(609, 'Ife North', 24),
(610, 'Ife South', 24),
(611, 'Ifedayo', 24),
(612, 'Ifelodun', 24),
(613, 'Ila', 24),
(614, 'Ilesha East', 24),
(615, 'Ilesha West', 24),
(616, 'Irepodun', 24),
(617, 'Irewole', 24),
(618, 'Isokan', 24),
(619, 'Iwo', 24),
(620, 'Obokun', 24),
(621, 'Odo-Otin', 24),
(622, 'Ola-Oluwa', 24),
(623, 'Olorunda', 24),
(624, 'Oriade', 24),
(625, 'Orolu', 24),
(626, 'Osogbo', 24),
(627, 'Afijio', 25),
(628, 'Akinyele', 25),
(629, 'Atiba', 25),
(630, 'Atigbo', 25),
(631, 'Egbeda', 25),
(632, 'Ibadan North', 25),
(633, 'Ibadan North West', 25),
(634, 'Ibadan South East', 25),
(635, 'Ibadan South West', 25),
(636, 'Ibadan Central', 25),
(637, 'Ibarapa Central', 25),
(638, 'Ibarapa East', 25),
(639, 'Ibarapa North', 25),
(640, 'Ido', 25),
(641, 'Irepo', 25),
(642, 'Iseyin', 25),
(643, 'Itesiwaju', 25),
(644, 'Iwajowa', 25),
(645, 'Kajola', 25),
(646, 'Lagelu Ogbomosho North', 25),
(647, 'Ogbmosho South', 25),
(648, 'Ogo Oluwa', 25),
(649, 'Olorunsogo', 25),
(650, 'Oluyole', 25),
(651, 'Ona-Ara', 25),
(652, 'Orelope', 25),
(653, 'Ori Ire', 25),
(654, 'Oyo East', 25),
(655, 'Oyo West', 25),
(656, 'Saki East', 25),
(657, 'Saki West', 25),
(658, 'Surulere', 25),
(659, 'Barikin Ladi', 26),
(660, 'Bassa', 26),
(661, 'Bokkos', 26),
(662, 'Jos East', 26),
(663, 'Jos North', 26),
(664, 'Jos South', 26),
(665, 'Kanam', 26),
(666, 'Kanke', 26),
(667, 'Langtang North', 26),
(668, 'Langtang South', 26),
(669, 'Mangu', 26),
(670, 'Mikang', 26),
(671, 'Pankshin', 26),
(672, 'Qua''An Pan', 26),
(673, 'Riyom', 26),
(674, 'Shendam', 26),
(675, 'Wase', 26),
(676, 'Abua/Odual', 27),
(677, 'Ahoada East', 27),
(678, 'Ahoada West', 27),
(679, 'Akuku Toru', 27),
(680, 'Andoni', 27),
(681, 'Asari-Toru', 27),
(682, 'Bonny', 27),
(683, 'Degema', 27),
(684, 'Eleme', 27),
(685, 'Emohua', 27),
(686, 'Etche', 27),
(687, 'Gokana', 27),
(688, 'Ikwerre', 27),
(689, 'Khana', 27),
(690, 'Obia/Akpor', 27),
(691, 'Ogba/Egbema/Ndoni', 27),
(692, 'Ogu/Bolo', 27),
(693, 'Okrika', 27),
(694, 'Omumma', 27),
(695, 'Opobo/Nkoro', 27),
(696, 'Oyigbo', 27),
(697, 'Port-Harcourt', 27),
(698, 'Tai ', 27),
(699, 'Binji', 28),
(700, 'Bodinga', 28),
(701, 'Dange-Shnsi', 28),
(702, 'Gada', 28),
(703, 'Gawabawa', 28),
(704, 'Goronyo', 28),
(705, 'Gudu', 28),
(706, 'Illela', 28),
(707, 'Isa', 28),
(708, 'Kebbe', 28),
(709, 'Kware', 28),
(710, 'Rabah', 28),
(711, 'Sabon Birni', 28),
(712, 'Shagari', 28),
(713, 'Silame', 28),
(714, 'Sokoto North', 28),
(715, 'Sokoto South', 28),
(716, 'Tambuwal', 28),
(717, 'Tangaza', 28),
(718, 'Tureta', 28),
(719, 'Wamako', 28),
(720, 'Wurno', 28),
(721, 'Yabo', 28),
(722, 'Ardo-Kola', 29),
(723, 'Bali', 29),
(724, 'Cassol', 29),
(725, 'Donga', 29),
(726, 'Gashaka', 29),
(727, 'Ibi', 29),
(728, 'Jalingo', 29),
(729, 'Karin-Lamido', 29),
(730, 'Kurmi', 29),
(731, 'Lau', 29),
(732, 'Sardauna', 29),
(733, 'Takum', 29),
(734, 'Ussa', 29),
(735, 'Wukari', 29),
(736, 'Yorro', 29),
(737, 'Zing', 29),
(738, 'Bade', 30),
(739, 'Bursari', 30),
(740, 'Damaturu', 30),
(741, 'Fika', 30),
(742, 'Fune', 30),
(743, 'Geidam', 30),
(744, 'Gujba', 30),
(745, 'Gulani', 30),
(746, 'Jakusko', 30),
(747, 'Karasuwa', 30),
(748, 'Karawa', 30),
(749, 'Machina', 30),
(750, 'Nangere', 30),
(751, 'Nguru Potiskum', 30),
(752, 'Tarmua', 30),
(753, 'Yunusari', 30),
(754, 'Yusufari', 30),
(755, 'Anka ', 35),
(756, 'Bakura', 35),
(757, 'Birnin Magaji', 35),
(758, 'Bukkuyum', 35),
(759, 'Bungudu', 35),
(760, 'Gummi', 35),
(761, 'Gusau', 35),
(762, 'Kaura', 35),
(763, 'Maradun', 35),
(764, 'Maru', 35),
(765, 'Namoda', 35),
(766, 'Shinkafi', 35),
(767, 'Talata Mafara', 35),
(768, 'Tsafe', 35),
(769, 'Zurmi ', 35),
(770, 'Akoko Edo', 10),
(771, 'Etsako West', 10),
(772, 'Potiskum', 30),
(773, 'Owan East', 10),
(774, 'Ilorin South', 19),
(775, 'Kazaure', 13),
(776, 'Gamawa', 5),
(777, 'Owan West', 10),
(778, 'Awgu', 11),
(779, 'Ogbomosho-North', 25),
(780, 'Yamaltu Deba', 33);

-- --------------------------------------------------------

--
-- Table structure for table `marital_statuses`
--

DROP TABLE IF EXISTS `marital_statuses`;
CREATE TABLE IF NOT EXISTS `marital_statuses` (
  `marital_status_id` int(10) unsigned NOT NULL,
  `marital_status` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `marital_status_abbr` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `marital_statuses`
--

INSERT INTO `marital_statuses` (`marital_status_id`, `marital_status`, `marital_status_abbr`, `created_at`, `updated_at`) VALUES
(1, 'Married', 'M', NULL, NULL),
(3, 'Single', 'S', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `salutations`
--

DROP TABLE IF EXISTS `salutations`;
CREATE TABLE IF NOT EXISTS `salutations` (
  `salutation_id` int(10) unsigned NOT NULL,
  `salutation` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `salutation_abbr` varchar(15) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `salutations`
--

INSERT INTO `salutations` (`salutation_id`, `salutation`, `salutation_abbr`) VALUES
(1, 'Mister', 'Mr.'),
(2, 'Mistress', 'Mrs.'),
(3, 'Doctor', 'Dr.'),
(4, 'Miss', 'Miss.');

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
CREATE TABLE IF NOT EXISTS `schools` (
  `school_id` int(10) unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `phone_no` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `motto` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `logo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_id` int(11) unsigned DEFAULT NULL,
  `status_id` int(10) unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`school_id`, `name`, `full_name`, `phone_no`, `email`, `motto`, `website`, `address`, `logo`, `admin_id`, `status_id`, `created_at`, `updated_at`) VALUES
(1, 'SolidSteps', 'Solid Steps International School', '+2348061539278', 'nondefyde@gmail.com', 'taking solid steps to our vision', 'www.solidsteps.com', '4 ikuna Street Liasu Rd.', '3_logo.png', NULL, 1, '2016-04-19 11:42:16', '2016-04-19 11:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `schools_subjects`
--

DROP TABLE IF EXISTS `schools_subjects`;
CREATE TABLE IF NOT EXISTS `schools_subjects` (
  `school_id` int(10) unsigned NOT NULL,
  `subject_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_databases`
--

DROP TABLE IF EXISTS `school_databases`;
CREATE TABLE IF NOT EXISTS `school_databases` (
  `school_database_id` int(10) unsigned NOT NULL,
  `host` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `database` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `school_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
CREATE TABLE IF NOT EXISTS `states` (
  `state_id` int(3) unsigned NOT NULL,
  `state` varchar(30) DEFAULT NULL,
  `state_code` varchar(5) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`state_id`, `state`, `state_code`) VALUES
(1, 'Abia', 'ABI\r'),
(2, 'Adamawa', 'ADA\r'),
(3, 'Akwa Ibom', 'AKW\r'),
(4, 'Anambra', 'ANA\r'),
(5, 'Bauchi', 'BAU\r'),
(6, 'Benue', 'BEN\r'),
(7, 'Borno', 'BOR\r'),
(8, 'Cross-River', 'CRO\r'),
(9, 'Delta', 'DEL\r'),
(10, 'Edo', 'EDO\r'),
(11, 'Enugu', 'ENU\r'),
(12, 'Imo', 'IMO\r'),
(13, 'Jigawa', 'JIG\r'),
(14, 'Kebbi', 'KEB\r'),
(15, 'Kaduna', 'KAD\r'),
(16, 'Kogi', 'KOG\r'),
(17, 'Kano', 'KAN\r'),
(18, 'Katsina', 'KAT\r'),
(19, 'Kwara', 'KWA\r'),
(20, 'Lagos', 'LAG\r'),
(21, 'Niger', 'NIG\r'),
(22, 'Ondo', 'OND\r'),
(23, 'Ogun', 'OGU\r'),
(24, 'Osun', 'OSU\r'),
(25, 'Oyo', 'OYO\r'),
(26, 'Plateau', 'PLA\r'),
(27, 'Rivers', 'RIV\r'),
(28, 'Sokoto', 'SOK\r'),
(29, 'Taraba', 'TAR\r'),
(30, 'Yobe', 'YOB\r'),
(31, 'FCT', 'FCT\r'),
(32, 'Bayelsa', 'BAY\r'),
(33, 'Gombe', 'GOM\r'),
(34, 'Nasarawa', 'NAS\r'),
(35, 'Zamfara', 'ZAM\r'),
(36, 'Ekiti', 'EKI\r'),
(37, 'Ebonyi', 'EBO\r');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `subject_id` int(10) unsigned NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject_abbr` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject_group_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject`, `subject_abbr`, `subject_group_id`, `created_at`, `updated_at`) VALUES
(1, 'English Language', 'ENG.LANG', 2, NULL, '2016-05-11 14:10:36'),
(2, 'Mathematics', 'MAT', 1, NULL, NULL),
(3, 'Basic Science', 'B. SCI', 3, NULL, NULL),
(4, 'Basic Technology', 'B. TECH', 3, NULL, NULL),
(5, 'Business Studies', 'BUS. STDS', 6, NULL, NULL),
(6, 'Social Studies', 'SOC STD', 5, NULL, NULL),
(7, 'French Language', 'FRE.LANG', 2, NULL, '2016-05-11 14:11:02'),
(8, 'Physical & Health Education', 'PHE', 3, NULL, NULL),
(9, 'Computer  Science', 'COMP.SCI', 1, NULL, NULL),
(10, 'Visual Arts', 'V.ARTS', 4, NULL, NULL),
(11, 'Hausa Language', 'HAU.LANG', 2, NULL, '2016-05-11 14:11:02'),
(12, 'Igbo Language', 'IGB.LANG', 2, NULL, '2016-05-11 14:11:02'),
(13, 'Yoruba Language', 'YOR.LANG', 2, NULL, '2016-05-11 14:11:02'),
(14, 'Agricultural Science', 'AGR SCI', 3, NULL, NULL),
(15, 'Home Economics', 'H.ECONS', 4, NULL, NULL),
(16, 'Christain Religious Studies', 'C.R.S.', 5, NULL, NULL),
(17, 'Islamic Religious Studies', 'I.R.S', 5, NULL, NULL),
(18, 'Geography', 'GEO', 5, NULL, NULL),
(19, 'Literature-In-English', 'LIT', 2, NULL, NULL),
(20, 'History ', 'HIS', 5, NULL, NULL),
(21, 'Physics', 'PHY', 3, NULL, NULL),
(22, 'Chemistry', 'CHEM', 3, NULL, NULL),
(23, 'Biology', 'BIO', 3, NULL, NULL),
(24, 'Foods & Nutrition', 'F&N', 4, NULL, NULL),
(25, 'Technical Drawing', 'T.D', 4, NULL, NULL),
(26, 'Music', 'MUS', 4, NULL, NULL),
(27, 'Metal Work', 'M.WRK', 4, NULL, NULL),
(28, 'Electronics', 'ELECT', 4, NULL, NULL),
(29, 'Wood Work', 'WD WRK', 4, NULL, NULL),
(30, 'Commerce', 'COM', 6, NULL, NULL),
(31, 'Accounting', 'ACC', 6, NULL, NULL),
(32, 'Economics', 'ECONS', 6, NULL, NULL),
(33, 'Government', 'GOV', 5, NULL, NULL),
(34, 'Further Mathematics', 'F.MATHS', 1, NULL, NULL),
(35, 'Animal Husbandry', 'ANI. HUS', 3, NULL, NULL),
(36, 'Data Processing', 'DAT', 1, NULL, NULL),
(37, 'ICT', 'ICT', 1, NULL, NULL),
(38, 'Civic Education', 'CIV', 5, NULL, NULL),
(39, 'Fine Arts', 'F.ARTS', 4, NULL, NULL),
(40, 'Catering Craft', 'Cat. Craft', 4, NULL, NULL),
(41, 'Paint & Decoration', 'P&D', 4, NULL, NULL),
(42, 'Chinese', 'CHIN', 2, NULL, NULL),
(43, 'Building Construction', 'BLD CONSTR', 4, NULL, NULL),
(44, 'Arabic ', 'ARA', 2, NULL, NULL),
(45, 'Auto Mechanic', 'AUTO', 4, NULL, NULL),
(46, 'Health Science', 'H. SCI', 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subject_groups`
--

DROP TABLE IF EXISTS `subject_groups`;
CREATE TABLE IF NOT EXISTS `subject_groups` (
  `subject_group_id` int(10) unsigned NOT NULL,
  `subject_group` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `subject_groups`
--

INSERT INTO `subject_groups` (`subject_group_id`, `subject_group`) VALUES
(1, 'Mathematics & Computer'),
(2, 'Languages'),
(3, 'Sciences'),
(4, 'Vocational Studies'),
(5, 'Humanities'),
(6, 'Business Studies');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lgas`
--
ALTER TABLE `lgas`
  ADD PRIMARY KEY (`lga_id`),
  ADD KEY `state_id` (`state_id`);

--
-- Indexes for table `marital_statuses`
--
ALTER TABLE `marital_statuses`
  ADD PRIMARY KEY (`marital_status_id`);

--
-- Indexes for table `salutations`
--
ALTER TABLE `salutations`
  ADD PRIMARY KEY (`salutation_id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`school_id`),
  ADD KEY `status_id` (`status_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `schools_subjects`
--
ALTER TABLE `schools_subjects`
  ADD PRIMARY KEY (`school_id`,`subject_id`),
  ADD KEY `schools_subjects_school_id_index` (`school_id`),
  ADD KEY `schools_subjects_subject_id_index` (`subject_id`);

--
-- Indexes for table `school_databases`
--
ALTER TABLE `school_databases`
  ADD PRIMARY KEY (`school_database_id`),
  ADD KEY `school_databases_schools_id_index` (`school_id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`state_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD KEY `subjects_subject_group_id_index` (`subject_group_id`);

--
-- Indexes for table `subject_groups`
--
ALTER TABLE `subject_groups`
  ADD PRIMARY KEY (`subject_group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lgas`
--
ALTER TABLE `lgas`
  MODIFY `lga_id` int(3) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=781;
--
-- AUTO_INCREMENT for table `marital_statuses`
--
ALTER TABLE `marital_statuses`
  MODIFY `marital_status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `salutations`
--
ALTER TABLE `salutations`
  MODIFY `salutation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `school_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `school_databases`
--
ALTER TABLE `school_databases`
  MODIFY `school_database_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `state_id` int(3) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=47;
--
-- AUTO_INCREMENT for table `subject_groups`
--
ALTER TABLE `subject_groups`
  MODIFY `subject_group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `schools_subjects`
--
ALTER TABLE `schools_subjects`
  ADD CONSTRAINT `schools_subjects_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schools_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_subject_group_id_foreign` FOREIGN KEY (`subject_group_id`) REFERENCES `subject_groups` (`subject_group_id`) ON DELETE CASCADE ON UPDATE CASCADE;
