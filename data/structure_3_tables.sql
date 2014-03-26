-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Mer 26 Mars 2014 à 21:14
-- Version du serveur: 5.5.22
-- Version de PHP: 5.4.19-1+debphp.org~precise+3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `la13`
--

-- --------------------------------------------------------

--
-- Structure de la table `connectes`
--

CREATE TABLE IF NOT EXISTS `connectes` (
  `ip` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `la13_answer`
--

CREATE TABLE IF NOT EXISTS `la13_answer` (
  `idUser` int(11) NOT NULL,
  `idWord` int(11) NOT NULL,
  `correct` int(11) NOT NULL,
  KEY `idUser` (`idUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `la13_user`
--

CREATE TABLE IF NOT EXISTS `la13_user` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `pseudoUser` varchar(30) NOT NULL,
  PRIMARY KEY (`idUser`),
  KEY `pseudoUser` (`pseudoUser`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=738 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
