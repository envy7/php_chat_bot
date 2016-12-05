-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 10, 2016 at 09:45 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chatbot`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_history`
--

CREATE TABLE `chat_history` (
  `id` bigint(30) DEFAULT NULL,
  `user_message` varchar(30) DEFAULT NULL,
  `reply` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `chat_history`
--

INSERT INTO `chat_history` (`id`, `user_message`, `reply`) VALUES
(1024371260995371, 'hey', 'Hi there.'),
(1024371260995371, 'how are you', 'Wonderful as always. Thanks fo'),
(1024371260995371, 'thank you', 'No problem.'),
(1093201560757586, 'hey', 'Good day!'),
(1093201560757586, 'how are you', 'Lovely, thanks.'),
(1093201560757586, 'what are you?', 'Your personal digital assistan'),
(1024371260995371, 'hey', 'Howdy.'),
(1024371260995371, 'who are you', 'I am Jarvis'),
(1024371260995371, 'hello', 'Howdy.'),
(1093201560757586, 'Hello', 'Hi there, friend!'),
(1093201560757586, 'Hows it going', 'Lovely, thanks.'),
(1024371260995371, 'hello', 'Hey!'),
(1024371260995371, 'hello', 'Hi there.'),
(1024371260995371, 'hello', 'Greetings!'),
(1024371260995371, 'hi', 'Good to see you!'),
(1024371260995371, 'hi', 'Good to see you!'),
(1024371260995371, 'hello', 'Greetings!'),
(1024371260995371, 'hey', 'Hey!'),
(1024371260995371, 'hello', 'Hi there, friend!'),
(1024371260995371, 'welcome', 'Thanks for having me.'),
(1024371260995371, 'hello', 'Good day!'),
(1024371260995371, 'hello', 'Hello.'),
(1024371260995371, 'hello', 'Greetings!'),
(1024371260995371, 'hello', 'Hello.'),
(1024371260995371, 'hello', 'Good to see you!'),
(1024371260995371, 'hello', 'Howdy.'),
(1024371260995371, 'hello', 'Howdy.'),
(1024371260995371, 'hi', 'Howdy.'),
(1024371260995371, 'hello', 'Hey.'),
(1024371260995371, 'hello', 'Greetings!'),
(1024371260995371, 'hi', 'Hi there, friend!'),
(1024371260995371, 'hello', 'Good day!'),
(1024371260995371, 'how are you', 'Wonderful as always. Thanks fo'),
(1024371260995371, 'noel mc', 'I think I may have misundersto'),
(1024371260995371, 'hi', 'Hello.'),
(1024371260995371, 'hello', 'Hey!'),
(1024371260995371, 'hello', 'Good to see you!'),
(1024371260995371, 'hello', 'Hi there.'),
(1024371260995371, 'hello', 'Howdy.'),
(1024371260995371, 'hi', 'Good to see you!'),
(1024371260995371, 'hello', 'Howdy.'),
(1024371260995371, 'hi', 'Good to see you!'),
(1024371260995371, 'hello', 'Howdy.'),
(1024371260995371, 'hi', 'Hey.'),
(1024371260995371, 'hi', 'Good day!'),
(1024371260995371, 'hi', 'Greetings!'),
(1024371260995371, 'hi', 'Hi there, friend!'),
(1024371260995371, 'hello', 'Hello.'),
(1024371260995371, 'hi', 'Greetings!'),
(1024371260995371, 'hello', 'Greetings!'),
(1024371260995371, 'hi', 'Greetings!'),
(1024371260995371, 'hi', 'Greetings!'),
(1024371260995371, 'hello', 'Hi there.'),
(1024371260995371, 'hello', 'Good to see you!'),
(1024371260995371, 'hi', 'Hey.'),
(1024371260995371, 'hello', 'Hi there, friend!'),
(1024371260995371, 'hello', 'Greetings!'),
(1024371260995371, 'hi', 'Greetings!'),
(1024371260995371, 'hi', 'Hi there, friend!'),
(1024371260995371, 'hello', 'Hi there, friend!'),
(1024371260995371, 'hello', 'Good to see you!'),
(1024371260995371, 'hello', 'Hi there, friend!'),
(1024371260995371, 'how old are you', 'I am born everyday B-)'),
(1024371260995371, 'hello', 'Greetings!'),
(1093201560757586, 'Hello', 'Good to see you!'),
(1093201560757586, 'Hello', 'Howdy.'),
(1093201560757586, 'Hello', 'Hey.'),
(1093201560757586, 'Hey', 'Good to see you!'),
(1024371260995371, 'hello', 'Greetings!'),
(1093201560757586, 'Hello', 'Hello.'),
(1093201560757586, 'Hello', 'Greetings!'),
(1093201560757586, 'Hello', 'Hey!'),
(1024371260995371, 'ok', 'Great!'),
(1024371260995371, 'how old are you', 'I am born everyday B-)'),
(1024371260995371, 'hey', 'Greetings!'),
(1024371260995371, 'hey', 'Good day!');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(30) NOT NULL,
  `replies` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `replies`) VALUES
(1024371260995371, 3),
(1093201560757586, 3);

-- --------------------------------------------------------

--
-- Table structure for table `scheduler`
--

CREATE TABLE `scheduler` (
  `serial` mediumint(10) NOT NULL,
  `id` bigint(30) DEFAULT NULL,
  `title` varchar(30) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `updated` varchar(2) NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `scheduler`
--

INSERT INTO `scheduler` (`serial`, `id`, `title`, `date`, `updated`) VALUES
(1, 1024371260995371, 'meeting with noel on report', '2016-10-06', 'Y'),
(2, 1024371260995371, 'meeting with noel on report', '2016-09-15', 'Y'),
(3, 1024371260995371, 'meeting with noel on report', '2016-10-06', 'Y'),
(16, 1024371260995371, 'meeting with noel on report', '2016-09-15', 'Y'),
(17, 1024371260995371, 'meeting with noel on report', '2016-10-07', 'Y'),
(18, 1024371260995371, 'meeting with noel on report', '2016-09-15', 'Y'),
(19, 1093201560757586, 'meeting with noel on report', '2016-10-07', 'Y'),
(20, 1093201560757586, 'meeting with noel on report', '2016-10-07', 'Y'),
(21, 1093201560757586, 'meeting with ajeet on report', '2016-10-07', 'Y'),
(22, 1093201560757586, 'meeting with jay on report', '2016-10-07', 'Y'),
(23, 1024371260995371, 'meeting with jay on report', '2016-09-15', 'Y'),
(24, 1024371260995371, 'meeting with ajeet on report', '2016-09-15', 'Y'),
(25, 1024371260995371, 'meeting with haggu on report', '2016-09-15', 'Y'),
(26, 1024371260995371, 'meeting with jay on report', '2016-09-15', 'Y'),
(27, 1024371260995371, 'meeting with jay on report', '2016-09-15', 'Y'),
(28, 1024371260995371, 'nighout with friends', '2016-10-07', 'Y'),
(29, 1024371260995371, 'nighout with friends', '2016-10-07', 'Y'),
(30, 1024371260995371, 'nighout with nigga', '2016-10-07', 'Y'),
(31, 1024371260995371, 'nighout with frds', '2016-10-07', 'Y'),
(32, 1024371260995371, 'nighout with friends', '2016-10-07', 'Y'),
(33, 1024371260995371, 'nighout with bhatti', '2016-10-07', 'Y'),
(34, 1024371260995371, 'nighout with bhatti', '2016-10-10', 'Y'),
(35, 1024371260995371, 'nighout with friends', '2016-10-07', 'Y'),
(36, 1024371260995371, 'nighout with friends', '2016-10-07', 'Y'),
(37, 1024371260995371, 'nighout with adi', '2016-10-10', 'Y'),
(38, 1024371260995371, 'nighout with adi', '2016-10-11', 'N'),
(39, 1024371260995371, 'nighout with adi', '2016-10-10', 'Y'),
(40, 1024371260995371, 'nighout with adi', '2016-10-11', 'N'),
(41, 1024371260995371, 'nighout with adi', '2016-10-10', 'Y'),
(42, 1024371260995371, 'nighout with adi', '2016-10-08', 'Y'),
(43, 1024371260995371, 'nighout with adi', '2016-10-08', 'Y'),
(44, 1024371260995371, 'nighout with adi', '2016-10-08', 'Y'),
(45, 1024371260995371, 'nighout with adi', '2016-10-08', 'Y'),
(46, 1024371260995371, 'nighout with adi', NULL, 'N'),
(47, 1024371260995371, 'nighout with adi', NULL, 'N'),
(48, 1024371260995371, 'nighout with adi', '2016-10-08', 'Y'),
(49, 1024371260995371, 'nighout with haggu', '2016-10-08', 'Y'),
(50, 1024371260995371, 'nighout with haggu', '2016-10-08', 'Y'),
(51, 1024371260995371, 'fix meet', '2016-10-10', 'Y'),
(52, 1024371260995371, 'meeting with family and outing', '2016-10-12', 'N'),
(53, 1024371260995371, 'meeting with college friends,f', '2016-10-12', 'N'),
(54, 1093201560757586, 'fix meet with abc', '2016-10-10', 'Y'),
(55, 1024371260995371, 'meet with CEO', '2016-10-11', 'N');

-- --------------------------------------------------------

--
-- Table structure for table `status_table_metadata`
--

CREATE TABLE `status_table_metadata` (
  `serial` int(11) NOT NULL DEFAULT '1',
  `updated_on` date DEFAULT NULL,
  `is_updated` varchar(1) NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `status_table_metadata`
--

INSERT INTO `status_table_metadata` (`serial`, `updated_on`, `is_updated`) VALUES
(1, '2016-10-10', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `user_record`
--

CREATE TABLE `user_record` (
  `id` bigint(30) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `interests` varchar(50) DEFAULT NULL,
  `updated` varchar(1) NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_record`
--

INSERT INTO `user_record` (`id`, `name`, `interests`, `updated`) VALUES
(1024371260995371, 'Anshul', 'police,cricket,football,india,games', 'Y'),
(1093201560757586, 'Noel', 'Football, Travelling, Food, Coding, Rock', 'Y');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scheduler`
--
ALTER TABLE `scheduler`
  ADD PRIMARY KEY (`serial`);

--
-- Indexes for table `status_table_metadata`
--
ALTER TABLE `status_table_metadata`
  ADD PRIMARY KEY (`serial`);

--
-- Indexes for table `user_record`
--
ALTER TABLE `user_record`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `scheduler`
--
ALTER TABLE `scheduler`
  MODIFY `serial` mediumint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
