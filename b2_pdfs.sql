-- phpMyAdmin SQL Dump
-- version 3.3.7deb5
-- http://www.phpmyadmin.net
--
-- Machine: localhost
-- Genereertijd: 01 Jun 2016 om 12:51
-- Serverversie: 5.1.49
-- PHP-Versie: 5.3.3-7+squeeze19

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `app_eeb2`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `b2_pdfs`
--

CREATE TABLE IF NOT EXISTS `b2_pdfs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `VragenPDF` varchar(100) NOT NULL,
  `AntwoordenPDF` varchar(100) NOT NULL,
  `Offset` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

--
-- Gegevens worden uitgevoerd voor tabel `b2_pdfs`
--

INSERT INTO `b2_pdfs` (`ID`, `VragenPDF`, `AntwoordenPDF`, `Offset`) VALUES
(38, 'http://localhost:63342/1.1.pdf', 'http://localhost:63342/Answer Star book.pdf', 6);
