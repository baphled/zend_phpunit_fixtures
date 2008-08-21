-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 24, 2008 at 12:37 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `framework_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(12) NOT NULL auto_increment,
  `title` varchar(100) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `pagetext` text,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `alias` (`alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `alias`, `pagetext`, `last_modified`) VALUES
(1, 'First Wiki Page', 'first-wiki-page', 'This is the first page for the wiki', '2008-06-23 15:00:16'),
(2, 'Second Wiki Page', 'second-wiki-page', 'This is the second page for the wiki', '2008-06-23 15:01:34'),
(3, 'Third Wiki Page', 'third-wiki-page', 'This is the third page for the wiki', '2008-06-23 15:01:34'),
(4, 'Final Wiki Page', 'final-wiki-page', 'This is the final page for the wiki', '2008-06-23 15:02:15');
