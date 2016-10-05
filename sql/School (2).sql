-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 12, 2016 at 04:53 AM
-- Server version: 5.5.47
-- PHP Version: 5.4.45-0+deb7u2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `School`
--

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE IF NOT EXISTS `class` (
  `class_number` varchar(8) NOT NULL COMMENT 'unique number for class',
  `teacher` varchar(13) NOT NULL COMMENT 'main teacher of this class',
  `description` varchar(1000) NOT NULL COMMENT 'description of this class',
  `school_id` int(5) DEFAULT NULL,
  PRIMARY KEY (`class_number`),
  KEY `teacher` (`teacher`),
  KEY `school_id` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_number`, `teacher`, `description`, `school_id`) VALUES
('A1', '8745696525364', '3 year olds', 1),
('A2', '8745696525364', '4 year olds', 1),
('A3', '6457849854213', '', 1),
('A5', '6457849854213', '', 1),
('B1', '8905116173081', 'Baby Class', 2),
('b3', '8745696525364', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE IF NOT EXISTS `parents` (
  `id_number` varchar(13) NOT NULL COMMENT 'Parent''s ID Number',
  `childs_birth_certificate` varchar(13) DEFAULT NULL COMMENT 'Birth certificate as on child''s birth certificate',
  `first_name` varchar(20) DEFAULT NULL COMMENT 'Parent''s first Name',
  `last_name` varchar(20) DEFAULT NULL COMMENT 'Parent''s last name',
  `home_number` varchar(10) DEFAULT NULL COMMENT 'Parent''s home phone number',
  `work_number` varchar(10) DEFAULT NULL COMMENT 'Parent''s work number',
  `cell_number` varchar(10) DEFAULT NULL COMMENT 'Parent''s cellphone number',
  `address` varchar(1000) DEFAULT NULL COMMENT 'Home address of parent',
  `primary_guardian` int(1) NOT NULL COMMENT '0 for secondary guardian, 1 for primary',
  `current` int(1) NOT NULL COMMENT '1 = current parent, 0 = ex-parent',
  PRIMARY KEY (`id_number`),
  UNIQUE KEY `id_number` (`id_number`),
  KEY `childs_birth_certificate` (`childs_birth_certificate`),
  KEY `address` (`address`(255)),
  KEY `childs_birth_certificate_2` (`childs_birth_certificate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`id_number`, `childs_birth_certificate`, `first_name`, `last_name`, `home_number`, `work_number`, `cell_number`, `address`, `primary_guardian`, `current`) VALUES
('0000000000000', '1007280706080', 'Ntsoaki', 'Make', '0787022003', '', '0787022003', 'Ext 19 Windmill Park', 0, 1),
('0000000000001', '1310200133082', 'Tebello', 'Khoza', NULL, '0110391523', '0713744120', NULL, 1, 1),
('0744392627', '1103310343083', 'Doctor', 'Banyane', '011620077', NULL, '0744392627', NULL, 0, 1),
('19850810', '892428', 'Renaissance', 'Madidi', '', '', '0715337615', '56 Pansy Street Villa Liza', 1, 1),
('19941220', '20120912', 'Paulin Kahunda', 'Mamimami', '', '', '0621380245', '27Hare Street villa Liza', 1, 1),
('6012100820082', '1007196358083', 'Cleopatra Nomalady', 'Mphake', '', '', '0788317582', '13 Camel street Villa Liza', 1, 1),
('7108200902081', '1101035516082', 'Thandi ', 'Msimango', '', '', '0823060039', '108 Pansy Street Villa Liza', 1, 1),
('7109231040081', '0910081378085', 'Nonsekelelo', 'Msimango', '', '', '0722219561', '7 Hartbeest Street Villa liza', 1, 1),
('7202250573081', '1305190164089', 'Rosina', 'Kgomo', NULL, NULL, '0843051452', NULL, 1, 1),
('7311120270081', '1103310343083', 'Mpume', 'Banyane', '0119990630', NULL, '0822470848', NULL, 1, 1),
('7402010637081', '1102015448080', 'Ntombozuyo', 'Mbayo', '', '0116221245', '0839591408', '149 Pansy Street Villa Liza', 1, 1),
('7406105451084', '1212050738087', 'Lotta', 'Letsoalo', NULL, '0118202316', '0822138814', NULL, 1, 1),
('7701091100095', '1108261256085', 'Nelisiwe ', 'Simelani', '', '', '0783506369', '4730 Mapholoba Street', 1, 1),
('7709150621083', '1405040451080', 'Polleth', 'Ramabulana', '0119061993', '0118982574', '0829206077', '23 Parakeet Street Villa Liza', 1, 1),
('7806250914085', '1011106502087', 'Pulane', 'Masolane', '', '0799672606', '0749993853', '19 Kangaroo Street Villal iza', 1, 1),
('7850121042708', '1109131322081', 'Thembi ', 'Nyembe', '', '0825545450', '0720124914', '2323 Charville Street Villa Liza', 1, 1),
('7905100699084', '0909295546081', 'Nondumiso', 'Magubane', '', '', '0731623579', 'Dafodile Villa Liza', 1, 1),
('8108200860081', '1309250386087', 'Nthabiseng', 'Monosi', '', '', '0607357863', '23 Kangaroo Street Villa Liza', 1, 1),
('8110280764084', '1107225498080', 'Gloria ', 'Rantsieng ', '', '0716879672', '0761157585', '80 Primrose Street Villa Liza', 1, 1),
('8202265606081', '1106181226089', 'Joseph', 'Lerata', '0114702122', NULL, '0788232837', NULL, 1, 1),
('8302021178084', '1110225887088', 'Phindile', 'Makasela', '', '', '0789091225', '2307 Engelica Street Villa Liza', 1, 1),
('8303155268089', '1307025343086', 'Fadel', 'Moola', '', '0118632833', '0742963135', '57Marigold Street Villa Liza', 1, 1),
('8412116017087', '1301201238087', 'Lucky', 'Dhlamini', NULL, '0825432919', '0810497913', NULL, 1, 1),
('8606201533086', '1009245273081', 'Nthabeleng ', 'Mgidi', '', '', '0787403633', '2118 Ginger Street ', 1, 1),
('8607280347083', '1107126144080', 'Lungile ', 'Mavuso', '', '0877428768', '0849531407', '2005 Chamomile Street Villa Liza', 1, 1),
('860780421088', '1307255379081', 'Nomsa', 'Myeni', '', '0117103517', '0837922933', '31 Squirrel street Villa Liza', 1, 1),
('8612301252080', '1108311079081', 'Busi Zawabi', 'Hadebe', '0118622256', NULL, '0730227552', NULL, 1, 1),
('8702121213089', '1209135367080', 'Lerato', 'Kumalo', NULL, NULL, '0713744120', NULL, 1, 1),
('8703260442083', '1210030919082', 'Mahlatsi', 'Matlala', '', '', '0724791851', 'Unit2 Protea Windmill Park', 1, 1),
('8704010588074', '1007200595084', 'Nompumelelo Wendy', 'Buthelezi', NULL, NULL, '0714054754', NULL, 1, 1),
('8704010588084', '1007200595084', 'Nompumelelo Wndy', 'Buthelezi', NULL, NULL, '0714054754', NULL, 1, 1),
('8705135228084', '1506135903081', 'Kagiso', 'Kale', '0119833200', NULL, '0837794288', NULL, 1, 1),
('8707255229082', '1007280706080', 'Neo', 'Motlokoa', '0787022003', '0110330231', '0626330454', 'Ext 19 Windmill Park', 1, 1),
('8801100182081', '1301295936083', 'Lindiwe', 'Maduna', '', '', '0633318536', '15Steenbok Street Villa Liza', 1, 1),
('8903080370080', '1312056424086', 'Nonkululeko', 'Mthembu', '', '', '0839261754', '15 Jackel Street Villa Liza ', 1, 1),
('8908115235083', '1106110582081', 'Zann', 'Hendricks', '0119028866', NULL, '0835179851', NULL, 0, 1),
('8909045469081', '1211160923084', 'Sibusiso', 'Icubhelca', NULL, '0113260108', '0783539202', NULL, 1, 1),
('9001010434081', '1401155665085', 'Nthabiseng', 'Msibi', '', '', '0835087682', '135 Pansy Street', 1, 1),
('9009140551088', '1108050513084', 'Ntombikayise', 'Mnisi', '', '', '0711181645', '21 Cuckoo Street Villa Liza', 1, 1),
('9012170295087', '1106110582081', 'Maryka', 'Coetzee', '0118234055', NULL, '0783285960', NULL, 1, 1),
('9203040350085', '1506285709080', 'Dieketseng ', 'Monama', '0119061993', '0110391523', '0799894619', '492 Chiloane Street Vosloorus', 1, 1),
('9311100216086', '1110125369033', 'Felicia', 'Mabaso', '', '', '0788409259', '30 Platberg Street Rondebult', 1, 1),
('9405360514080', '1209085223085', 'Lungile Mbali', 'Kunene', NULL, NULL, '0839896540', NULL, 1, 1),
('9602190136089', '1401315761089', 'Nelisiwe', 'Mngomezulu', '', '', '0842534342', '30 Bauhinia Street Dawn Park', 1, 1),
('BN593816', '1107056193081', 'Nelita', 'Sithole', '', '', '0746105311', '43 Hare Street Villa Liza', 1, 1),
('Bright', '1007200595084', 'Bright', 'Mavuso', NULL, NULL, NULL, NULL, 0, 0),
('MUSOD00048011', '200222014', 'Daniel ', 'Bongo', '', '', '0620270526', '19 Kangaro street Villa Liza', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

CREATE TABLE IF NOT EXISTS `school` (
  `school_id` int(5) NOT NULL AUTO_INCREMENT,
  `school_name` varchar(20) NOT NULL,
  PRIMARY KEY (`school_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `school`
--

INSERT INTO `school` (`school_id`, `school_name`) VALUES
(1, 'school1'),
(2, 'Phuthasechaba');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `id_number` varchar(13) NOT NULL COMMENT 'ID number of staff member',
  `first_name` varchar(20) NOT NULL COMMENT 'staff members first name',
  `last_name` varchar(20) NOT NULL COMMENT 'staff members last name',
  `gender` varchar(6) NOT NULL COMMENT 'Gender of the staff member',
  `date_of_employment` date NOT NULL COMMENT 'date employee was hired',
  `home_number` varchar(10) DEFAULT NULL COMMENT 'staff members home phone number',
  `cell_number` varchar(10) DEFAULT NULL COMMENT 'staff members cellphone number',
  `address` varchar(1000) DEFAULT NULL COMMENT 'staff member''s home address',
  `current` int(1) DEFAULT NULL COMMENT '1 = current, 0 = ex employee',
  `nok_first_name` varchar(20) NOT NULL COMMENT 'Next of kin first name',
  `nok_last_name` varchar(20) NOT NULL COMMENT 'Next of kin last name',
  `nok_home_number` varchar(10) DEFAULT NULL COMMENT 'Nex of kin home number',
  `nok_work_number` varchar(10) DEFAULT NULL COMMENT 'next of kin work number',
  `nok_cell_number` varchar(10) NOT NULL COMMENT 'next of kin cellphone number',
  `nok_address` varchar(1000) DEFAULT NULL COMMENT 'next of kin address',
  `school_id` int(5) DEFAULT NULL,
  PRIMARY KEY (`id_number`),
  UNIQUE KEY `id_number` (`id_number`),
  KEY `address` (`address`(255)),
  KEY `school_id` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id_number`, `first_name`, `last_name`, `gender`, `date_of_employment`, `home_number`, `cell_number`, `address`, `current`, `nok_first_name`, `nok_last_name`, `nok_home_number`, `nok_work_number`, `nok_cell_number`, `nok_address`, `school_id`) VALUES
('6457849854213', 'Hellen', 'Hodgings', 'Female', '2016-04-04', '0134659864', '0215487569', '																																2 State Street', 1, 'Harry', 'Hodgings', '0258458753', '', '5854544415', '', 1),
('8745696525364', 'gill', 'bryson', 'Female', '2016-02-23', '', '0215487569', '', 1, 'bob', 'bryson', '0112589968', '', '8526975425', '', 1),
('8905116173081', 'Lesego ', 'Lekhethe', 'male', '2016-07-14', '', '0833867058', '', 1, 'Lerato', 'Monama', '', '', '0713744120', '', 2);

-- --------------------------------------------------------

--
-- Table structure for table `staff_attendance`
--

CREATE TABLE IF NOT EXISTS `staff_attendance` (
  `staff_id_number` varchar(13) CHARACTER SET utf8 NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  PRIMARY KEY (`staff_id_number`,`date`),
  UNIQUE KEY `staff_id_number_2` (`staff_id_number`,`date`),
  KEY `staff_id_number` (`staff_id_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `staff_attendance`
--

INSERT INTO `staff_attendance` (`staff_id_number`, `date`, `time_in`, `time_out`) VALUES
('6457849854213', '2016-08-25', '11:14:00', '13:37:00'),
('6457849854213', '2016-09-05', '07:49:00', '14:00:00'),
('6457849854213', '2016-09-13', '08:20:00', '10:00:00'),
('6457849854213', '2016-09-26', '07:50:00', '17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE IF NOT EXISTS `students` (
  `birth_certificate_number` varchar(13) NOT NULL COMMENT 'Birth certificate number as on birth certificate',
  `first_name` varchar(20) NOT NULL COMMENT 'Child''s first name',
  `last_name` varchar(20) NOT NULL COMMENT 'Child''s last name',
  `birth_date` date NOT NULL COMMENT 'Child''s birth date as on birth certiicate',
  `gender` varchar(6) NOT NULL COMMENT 'Gender of the student',
  `date_of_admission` date NOT NULL COMMENT 'date the student was admitted ',
  `class` varchar(8) DEFAULT NULL COMMENT 'number of the class to which the student belongs',
  `school_id` int(5) DEFAULT NULL,
  PRIMARY KEY (`birth_certificate_number`),
  UNIQUE KEY `birth_certificate_number` (`birth_certificate_number`),
  KEY `class` (`class`),
  KEY `class_2` (`class`),
  KEY `class_3` (`class`),
  KEY `class_4` (`class`),
  KEY `class_5` (`class`),
  KEY `school_id` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table contains all students';

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`birth_certificate_number`, `first_name`, `last_name`, `birth_date`, `gender`, `date_of_admission`, `class`, `school_id`) VALUES
('0909295546081', 'Nkolo', 'Magubane', '2009-09-29', 'male', '2012-01-09', 'B1', NULL),
('0910081378085', 'Thandeka', 'Msimango', '2009-10-08', 'female', '2016-04-19', NULL, NULL),
('1007196358083', 'Abulele', 'Mfihlo', '2010-07-19', 'male', '2014-06-04', 'B1', NULL),
('1007200595084', 'Luthando', 'Buthelezi', '2010-07-20', 'Male', '2015-02-05', NULL, 2),
('1007280706080', 'Itumeleng', 'Mabe', '2010-07-28', 'Male', '2016-05-01', NULL, 2),
('1009245273081', 'Mpho Sean ', 'Mgidi', '2010-09-24', 'male', '2013-02-04', NULL, NULL),
('1011106502087', 'Tumelo Nelson', 'Masolane', '2010-11-10', 'male', '2012-02-01', NULL, NULL),
('1012106652088', 'Wandile', 'Khoza', '2010-12-10', 'male', '2016-06-13', NULL, NULL),
('1101035516082', 'Bonga', 'Msimango', '2011-01-03', 'male', '2014-08-11', NULL, NULL),
('1102015448080', 'Thabo', 'Mofokeng', '2011-02-01', 'male', '2014-01-28', NULL, NULL),
('1103210343083', 'Onkutlwile Mbali', 'Banyane ', '2011-03-31', 'female', '2016-05-16', NULL, NULL),
('1103310343083', 'Clivered', 'Adams', '2013-08-21', 'male', '2016-05-16', NULL, NULL),
('1105300258080', 'Zusakhe Noxolo', 'Gcuma', '2011-05-30', 'female', '2015-02-01', NULL, NULL),
('1106110582081', 'Merishka', 'Coetzee ', '2011-06-11', 'female', '2015-06-22', NULL, NULL),
('1106181226089', 'Naledi Boitelo', 'Lerata', '2011-06-18', 'female', '2014-01-30', NULL, NULL),
('1107056193081', 'Mbongeni', 'Kunene', '2011-07-05', 'male', '2016-01-27', NULL, NULL),
('1107126144080', 'Thaembalethu Sihle', 'Mavuso', '2011-07-12', 'female', '2016-01-30', NULL, NULL),
('1107225498080', 'Abenathi Tshepang ', 'Mfuku', '2011-07-22', 'male', '2016-01-30', NULL, NULL),
('1107225499088', 'Athenkosi Tshepo', 'Mfuku', '2011-07-22', 'male', '2016-01-30', NULL, NULL),
('1108050513084', 'Gugulethu', 'Mnisi', '2011-08-05', 'female', '2014-09-04', NULL, NULL),
('1108261256085', 'Amanda', 'Dhlamini', '2011-08-26', 'female', '2016-02-01', NULL, NULL),
('1108311079081', 'Asante Angel', 'Khanyi', '2011-08-31', 'female', '2016-01-29', NULL, NULL),
('1109131322081', 'Dineo', 'Mokwena', '2011-09-13', 'female', '2016-01-30', NULL, NULL),
('1110125369033', 'Sibusiso ', 'Mabaso', '2011-10-12', 'male', '2016-01-30', NULL, NULL),
('1110225887088', 'Nyiko Jonathan', 'Makasela', '2011-10-22', 'male', '2016-01-25', NULL, NULL),
('1209085223085', 'Samkelo Khulekani', 'Kunene', '2012-09-08', 'male', '2015-02-01', NULL, NULL),
('1209135367080', 'Unathi', 'Kumalo', '2012-09-13', 'male', '2016-05-23', NULL, NULL),
('1210030919082', 'Ntokozo', 'Matlala', '2012-10-03', 'female', '2016-01-30', NULL, NULL),
('1211160923084', 'Nozizwe ', 'Kubheka', '2012-11-16', 'female', '2016-01-14', NULL, NULL),
('1212050738087', 'Hlompho', 'Kabane', '2012-05-12', 'female', '2014-01-13', NULL, NULL),
('1301201238087', 'Reabetswe ', 'Dlamini', '2013-01-20', 'female', '2014-01-23', NULL, NULL),
('1301295936083', 'Thando', 'Maduna', '2013-01-29', 'male', '2016-02-29', NULL, NULL),
('1305190164089', 'Lebogang Tshwene', 'Kgomo', '2013-05-19', 'female', '2014-01-30', NULL, NULL),
('1307025343086', 'Tiras', 'Moola', '2013-07-02', 'male', '2016-01-11', NULL, NULL),
('1307255379081', 'Myeni', 'Mpilo Kgotso', '2013-07-25', 'male', '2015-02-12', NULL, NULL),
('1307255381087', 'Myeni', 'Phila Tshepo', '2013-07-25', 'male', '2016-07-27', NULL, NULL),
('1309250386087', 'Swazi Lindokuhle', 'Monosi', '2013-09-25', 'female', '2016-07-06', NULL, NULL),
('1310200133082', 'Nkazimulo', 'Khoza', '2013-10-20', 'male', '2015-10-05', NULL, NULL),
('1311191308089', 'Ntokozo', 'Mnisi', '2013-11-19', 'female', '2016-01-31', NULL, NULL),
('1312056424086', 'Malik', 'Mthembu', '2013-12-05', 'male', '2016-02-01', NULL, NULL),
('1401155665085', 'Nqobile', 'Msibi', '2014-01-15', 'Male', '2016-02-02', NULL, 2),
('1401315761089', 'Bandile', 'Mngomezulu', '2014-01-31', 'male', '2016-02-02', NULL, NULL),
('1405040451080', 'Rendani', 'Malugana', '2014-05-04', 'female', '2016-01-12', NULL, NULL),
('1506135903081', 'Botshelo', 'Kale', '2015-06-13', 'male', '2015-10-02', 'B1', NULL),
('1506285709080', 'Lehumo Omphile', 'Makgeru', '2015-06-28', 'male', '2016-02-26', NULL, NULL),
('200222014', 'Manunga ', 'Bongo', '2014-02-20', 'female', '2015-08-31', NULL, 2),
('20120912', 'Mariah', 'Mamimami', '2012-09-12', 'female', '2016-05-31', NULL, NULL),
('8885566564212', 'greg', 'gregopolos', '2000-07-13', 'male', '2016-02-25', 'A1', NULL),
('892428', 'Princess', 'Madidi', '2013-01-12', 'female', '2016-02-08', NULL, NULL),
('8989898989898', 'Phill', 'Phillips', '2009-10-25', 'male', '2016-04-11', 'A1', NULL),
('E7644186', 'Cliverd Oupa', 'Adams', '2013-08-21', 'male', '2016-01-29', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `student_attendance`
--

CREATE TABLE IF NOT EXISTS `student_attendance` (
  `birth_certificate_number` varchar(13) NOT NULL COMMENT 'Student''s unique number',
  `date` date NOT NULL COMMENT 'Date for which this attendance applies',
  `attended` varchar(1) NOT NULL COMMENT 'Y or N',
  PRIMARY KEY (`birth_certificate_number`,`date`),
  KEY `birth_certificate_number` (`birth_certificate_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `student_attendance`
--

INSERT INTO `student_attendance` (`birth_certificate_number`, `date`, `attended`) VALUES
('0909295546081', '2016-07-14', 'Y'),
('0909295546081', '2016-09-05', 'Y'),
('1007196358083', '2016-07-14', 'Y'),
('1007196358083', '2016-09-05', 'N'),
('1506135903081', '2016-07-14', 'Y'),
('1506135903081', '2016-09-05', 'N'),
('8885566564212', '2016-07-14', 'Y'),
('8989898989898', '2016-07-14', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `student_balance`
--

CREATE TABLE IF NOT EXISTS `student_balance` (
  `balance_id` int(5) NOT NULL AUTO_INCREMENT,
  `birth_certificate_number` varchar(13) NOT NULL,
  `balance` double NOT NULL,
  PRIMARY KEY (`balance_id`),
  KEY `birth_certificate_number` (`birth_certificate_number`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

--
-- Dumping data for table `student_balance`
--

INSERT INTO `student_balance` (`balance_id`, `birth_certificate_number`, `balance`) VALUES
(1, '1007200595084', -4800),
(2, '1007280706080', 700),
(3, '1103210343083', 899),
(4, '1103310343083', 0),
(5, '1105300258080', 0),
(6, '1106110582081', 0),
(7, '1209085223085', 0),
(8, '1209135367080', 0),
(9, '1211160923084', 0),
(10, '1212050738087', 0),
(11, '1301201238087', 0),
(12, '1506135903081', 0),
(13, '8885566564212', 448.99),
(14, '8989898989898', 0),
(15, 'E7644186', -9500),
(16, '200222014', 0),
(17, '1307025343086', 0),
(18, '1107056193081', 0),
(19, '1108261256085', 0),
(20, '1309250386087', 0),
(21, '1109131322081', 0),
(22, '1102015448080', 0),
(23, '1210030919082', 0),
(24, '1107126144080', 0),
(25, '892428', 0),
(26, '1110125369033', 0),
(27, '1110225887088', 0),
(28, '1301295936083', 0),
(29, '1506285709080', 0),
(30, '1405040451080', 0),
(31, '0909295546081', 0),
(32, '1011106502087', 0),
(33, '20120912', 0),
(34, '1009245273081', 0),
(35, '1007196358083', 0),
(36, '1107225498080', 0),
(37, '1107225499088', 0),
(38, '1401315761089', -400),
(39, '1108050513084', 0),
(40, '1311191308089', 0),
(41, '1311191308089', 0),
(42, '1312056424086', 0),
(43, '1401155665085', -4100),
(44, '0910081378085', 0),
(45, '1101035516082', 0),
(46, '1307255379081', 0),
(47, '1307255381087', 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_history`
--

CREATE TABLE IF NOT EXISTS `student_history` (
  `birth_certificate_number` varchar(13) NOT NULL COMMENT 'birth cert number of ex-student',
  `first_name` varchar(20) NOT NULL COMMENT 'first name of ex-student',
  `last_name` varchar(20) NOT NULL COMMENT 'last_name of ex-student',
  `birth_date` date NOT NULL COMMENT 'birth date of ex-student',
  `gender` varchar(6) NOT NULL COMMENT 'gender of ex-student',
  `admission_date` date NOT NULL COMMENT 'date ex-student was admitted',
  `removal_date` date NOT NULL COMMENT 'date ex-student was removed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_medical`
--

CREATE TABLE IF NOT EXISTS `student_medical` (
  `medical_number` int(5) NOT NULL AUTO_INCREMENT COMMENT 'A unique number for this students condition',
  `child_birth_certificate` varchar(13) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT 'Name the condition',
  `discription` varchar(500) DEFAULT NULL COMMENT 'Discription of the condition',
  PRIMARY KEY (`medical_number`),
  KEY `child_birth_certificate` (`child_birth_certificate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `student_notes`
--

CREATE TABLE IF NOT EXISTS `student_notes` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `student_birth_certificate_number` varchar(13) CHARACTER SET utf8 NOT NULL,
  `note` varchar(500) NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_birth_certificate_number` (`student_birth_certificate_number`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `student_notes`
--

INSERT INTO `student_notes` (`id`, `student_birth_certificate_number`, `note`, `time_stamp`) VALUES
(1, '1007200595084', 'Good student all round.', '2016-07-01 06:21:21'),
(2, '1007200595084', 'A very naughty boy!!', '2015-10-01 05:18:34'),
(3, '1007200595084', 'The good, the bad and the ugly...\r\n', '2016-08-18 15:44:08'),
(4, '1007200595084', 'Slacker', '2016-08-19 06:57:18'),
(5, '0909295546081', 'lkjalskdjfasfd', '2016-09-05 07:55:02');

-- --------------------------------------------------------

--
-- Table structure for table `student_transactions`
--

CREATE TABLE IF NOT EXISTS `student_transactions` (
  `transaction_id` int(10) NOT NULL AUTO_INCREMENT,
  `birth_certificate_number` varchar(13) NOT NULL COMMENT 'Child''s student number',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'timestamp for promary key',
  `receipt_number` varchar(10) NOT NULL,
  `type` varchar(10) NOT NULL COMMENT 'debit or credit',
  `date` date NOT NULL COMMENT 'date of transaction',
  `amount` double NOT NULL COMMENT 'transaction amount',
  `balance` double NOT NULL COMMENT 'working balance',
  `description` varchar(500) NOT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `birth_certificate_number` (`birth_certificate_number`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;

--
-- Dumping data for table `student_transactions`
--

INSERT INTO `student_transactions` (`transaction_id`, `birth_certificate_number`, `timestamp`, `receipt_number`, `type`, `date`, `amount`, `balance`, `description`) VALUES
(1, '1007280706080', '2016-05-01 11:57:36', '4229', 'credit', '2016-05-01', 350, 350, ''),
(2, '1007280706080', '2016-05-16 02:58:12', '2178', 'credit', '2016-05-16', 350, 700, 'May Fees'),
(3, '1103210343083', '2016-06-21 05:09:43', '4048', 'credit', '2016-06-21', 350, 350, '1500'),
(4, '1103210343083', '2016-06-21 05:43:47', '4048', 'credit', '2016-06-21', 350, 700, ''),
(14, '8885566564212', '2016-03-03 16:01:33', '', 'debit', '2016-03-03', 100, -100, 'March Fees'),
(15, '8885566564212', '2016-03-03 16:03:25', '', 'credit', '2016-03-03', 200, 100, 'Payment March'),
(16, '8885566564212', '2016-03-08 07:20:14', '', 'debit', '2016-03-08', 300, -200, 'March fees'),
(17, '8885566564212', '2016-03-08 07:20:34', '', 'credit', '2016-03-08', 200, 0, 'March payment'),
(18, '8885566564212', '2016-03-08 10:10:07', '', 'debit', '2016-03-08', 500, -500, ''),
(19, '8885566564212', '2016-03-08 10:10:17', '', 'credit', '2016-03-08', 500, 0, ''),
(20, '8885566564212', '2016-03-08 10:10:58', '', 'debit', '2016-03-08', 200, -200, ''),
(21, '8885566564212', '2016-03-08 10:11:04', '', 'credit', '2016-03-08', 200, 0, ''),
(22, '8885566564212', '2016-04-05 07:07:41', '', 'credit', '2016-04-05', 500, 500, ''),
(23, '8885566564212', '2016-04-05 07:08:08', '', 'credit', '2016-04-05', 299.99, 799, ''),
(24, '8885566564212', '2016-04-05 08:08:55', '', 'credit', '2016-04-05', 299.99, 1098.99, ''),
(25, '8885566564212', '2016-04-05 12:55:52', '', 'debit', '2016-04-05', 450, 648.99, 'April Fees'),
(26, '8885566564212', '2016-04-05 12:56:45', '', 'debit', '2016-04-05', 200, 448.99, 'April Fees'),
(27, '1401315761089', '2016-07-14 01:32:04', '1190', 'credit', '2016-07-14', 400, 400, 'February'),
(28, '1401315761089', '2016-07-14 01:32:48', '1190', 'debit', '2016-07-14', 400, 0, 'Payment Received For February'),
(29, '1401315761089', '2016-07-14 01:36:30', '1190', 'debit', '2016-07-14', 400, -400, 'March Fees'),
(30, '1401155665085', '2016-07-14 01:40:24', '1189', 'debit', '2016-07-14', 4800, -4800, 'School Fees'),
(31, '1401155665085', '2016-07-14 01:41:05', '1189', 'credit', '2016-07-14', 400, -4400, 'January Payment Received'),
(32, '1401155665085', '2016-07-14 01:42:40', '1190', 'credit', '2016-07-14', 400, -4000, 'February Payment Received'),
(33, '1401155665085', '2016-07-14 01:43:02', 'March', 'credit', '2016-07-14', 400, -3600, 'March Payment Received'),
(34, '1401155665085', '2016-07-14 01:48:10', '1190', 'debit', '2016-07-14', 500, -4100, 'Graduation Ceremony'),
(35, '1401155665085', '2016-07-14 01:50:26', '1190', 'debit', '2016-07-14', 50, -4150, 'Heritage Day Celebration'),
(36, '1401155665085', '2016-07-14 01:51:17', '1190', 'credit', '2016-07-14', 50, -4100, 'Heritage Celebration Payment Received'),
(37, '1007200595084', '2016-07-14 01:58:09', '1146', 'debit', '2016-07-14', 4800, -4800, 'School Fees'),
(38, 'E7644186', '2016-07-14 01:58:59', '1123', 'debit', '2016-07-14', 4800, -4800, 'School Fees'),
(39, 'E7644186', '2016-07-14 01:59:59', '1123', 'debit', '2016-07-14', 4800, -9600, 'School Fees'),
(40, 'E7644186', '2016-07-14 02:41:00', '1190', 'debit', '2016-07-14', 400, -10000, 'July School Fees'),
(41, 'E7644186', '2016-09-05 07:51:46', '123', 'credit', '2016-09-05', 500, -9500, ''),
(42, '1103210343083', '2016-09-12 04:43:07', '123456', 'credit', '2016-09-12', 199, 899, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) NOT NULL,
  `user` varchar(15) NOT NULL COMMENT 'Users able to access this DB',
  `password` varchar(15) NOT NULL COMMENT 'Users passwords',
  `first_name` varchar(15) NOT NULL COMMENT 'Users first name',
  `last_name` varchar(15) NOT NULL COMMENT 'Users last name',
  `administrator` int(1) NOT NULL COMMENT 'This value dictates level of administrative access',
  `school_id` int(5) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user` (`user`),
  KEY `school_id` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user`, `password`, `first_name`, `last_name`, `administrator`, `school_id`) VALUES
(0, 'Lesego', '2446', 'Lesego', 'Lekhethe', 1, NULL),
(1, 'admin', '2aQ73bH29', '', '', 3, NULL),
(2, 'bill', 'bill123', 'Bill', 'Billings', 1, 1),
(3, 'bob', 'bob123', 'bob', 'bobbington', 2, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `class`
--
ALTER TABLE `class`
  ADD CONSTRAINT `class_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `class_staff` FOREIGN KEY (`teacher`) REFERENCES `staff` (`id_number`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `staff_attendance`
--
ALTER TABLE `staff_attendance`
  ADD CONSTRAINT `staff_attendance_staff` FOREIGN KEY (`staff_id_number`) REFERENCES `staff` (`id_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `student_class` FOREIGN KEY (`class`) REFERENCES `class` (`class_number`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `student_attendance`
--
ALTER TABLE `student_attendance`
  ADD CONSTRAINT `birth_cert_number_foreign_key_student_attendance` FOREIGN KEY (`birth_certificate_number`) REFERENCES `students` (`birth_certificate_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_balance`
--
ALTER TABLE `student_balance`
  ADD CONSTRAINT `student_balance_ibfk_1` FOREIGN KEY (`birth_certificate_number`) REFERENCES `students` (`birth_certificate_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_medical`
--
ALTER TABLE `student_medical`
  ADD CONSTRAINT `student_medical_ibfk_1` FOREIGN KEY (`child_birth_certificate`) REFERENCES `students` (`birth_certificate_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_notes`
--
ALTER TABLE `student_notes`
  ADD CONSTRAINT `student_note` FOREIGN KEY (`student_birth_certificate_number`) REFERENCES `students` (`birth_certificate_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_transactions`
--
ALTER TABLE `student_transactions`
  ADD CONSTRAINT `student_transactions_ibfk_1` FOREIGN KEY (`birth_certificate_number`) REFERENCES `students` (`birth_certificate_number`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
