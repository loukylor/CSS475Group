-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: vergil.u.washington.edu:42069
-- Generation Time: May 29, 2025 at 12:06 AM
-- Server version: 5.7.29
-- PHP Version: 7.4.24
-- Edited by: Kyler Li

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hiking_trails`
--
-- DROP DATABASE `hiking_trials`;
-- CREATE DATABASE `hiking_trials` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
-- USE `hiking_trials`;

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `location`;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE `location` (
  `LocationID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `ParentLocationID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Description` text NOT NULL,
  FOREIGN KEY (`ParentLocationID`) REFERENCES `location`(`LocationID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `user`;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE `user` (
  `Username` varchar(64) NOT NULL PRIMARY KEY,
  `UserType` text DEFAULT NULL,
  `Credibility` int(11) NOT NULL,
  `Email` varchar(254) NOT NULL COMMENT 'According to the internet, emails have a max length of 254',
  `PasswordHash` blob NOT NULL,
  `FirstName` varchar(64) NOT NULL,
  `LastName` varchar(64) NOT NULL,
  CHECK (`UserType` = 'TrailModerator' OR `UserType` = 'PostModerator' OR `UserType` IS NULL)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `profile`;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE `profile` (
  `Username` varchar(64) NOT NULL PRIMARY KEY,
  `Description` text NOT NULL,
  FOREIGN KEY (`Username`) REFERENCES `user`(`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trail`
--

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `trail`;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE `trail` (
  `TrailID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `LocationID` int(11) NOT NULL,
  `Open` bit(1) DEFAULT NULL,
  `DogFriendly` bit(1) DEFAULT NULL,
  `RatingAverage` float NOT NULL,
  `Description` text NOT NULL,
  `BikeAllowed` bit(1) DEFAULT NULL,
  `Name` text NOT NULL,
  `Difficulty` varchar(6) NOT NULL DEFAULT 'Medium' COMMENT '"Easy", "Medium", and "Hard"',
  `Duration` time NOT NULL,
  `Length` float NOT NULL,
  FOREIGN KEY (`LocationID`) REFERENCES `location`(`LocationID`),
  CHECK (`Difficulty` = 'Easy' OR `Difficulty` = 'Medium' OR `Difficulty` = 'Hard')
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `explored`
--

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `explored`;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE `explored` (
  `Username` varchar(64) NOT NULL,
  `TrailID` int(11) NOT NULL,
  FOREIGN KEY (`Username`) REFERENCES `user`(`Username`),
  FOREIGN KEY (`TrailID`) REFERENCES `trail`(`TrailID`),
  CONSTRAINT `PK_EXPLORED` PRIMARY KEY (`TrailID`, `Username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `post`;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE `post` (
  `PostID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Username` varchar(64) NOT NULL,
  `TrailID` int(11) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `PostDate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Title` varchar(32) NOT NULL,
  FOREIGN KEY (`Username`) REFERENCES `profile`(`Username`),
  FOREIGN KEY (`TrailID`) REFERENCES `trail`(`TrailID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `weather`
--

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `weather`;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE `weather` (
  `WeatherID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `TrailID` int(11) NOT NULL COMMENT 'Weather on the trail itself, as a location may be quite large',
  `TemperatureF` smallint(6) NOT NULL COMMENT 'Temp in Fahrenheit',
  `PrecipitationChance` int(11) NOT NULL COMMENT 'Chance from [0-100]',
  `Conditions` text NOT NULL COMMENT 'e.g. "Mostly Cloudy", "Rainy", etc.',
  `ForecastSource` text NOT NULL COMMENT 'The place the data is from',
  `ForDate` date NOT NULL COMMENT 'The date the weather data is for. MySQL 5.7 doesnt have a way to give it current date as default value',
  FOREIGN KEY (`TrailID`) REFERENCES `trail`(`TrailID`),
  CHECK (PrecipitationChance >= 0 AND PrecipitationChance <= 100)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `comment`;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE `comment` (
  `CommentID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `PostID` int(11) NOT NULL,
  `Username` varchar(64) NOT NULL,
  `Description` varchar(255) NOT NULL,
  FOREIGN KEY (`PostID`) REFERENCES `post`(`PostID`),
  FOREIGN KEY (`Username`) REFERENCES `profile`(`Username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `image`;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE `image` (
  `ImageURL` varchar(255) NOT NULL PRIMARY KEY 
    COMMENT 'URLs can theoretically be longer, 
      but our image CDN is assumed to not generate URLs that exceed 255 chars',
  `Username` varchar(64) NOT NULL,
  `PostID` int(11) NOT NULL,
  `FileSize` int(11) UNSIGNED NOT NULL COMMENT 'File size in bytes',
  `UploadedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`Username`) REFERENCES `profile`(`Username`),
  FOREIGN KEY (`PostID`) REFERENCES `post`(`PostID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `review`;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE `review` (
  `Username` varchar(64) NOT NULL,
  `TrailID` int(11) NOT NULL,
  `Score` int(11) NOT NULL COMMENT '[1-5]',
  `Description` varchar(255) NOT NULL,
  `Title` varchar(32) NOT NULL,
  FOREIGN KEY (`Username`) REFERENCES `user`(`Username`),
  FOREIGN KEY (`TrailID`) REFERENCES `trail`(`TrailID`),
  CONSTRAINT `REVIEW_PK` PRIMARY KEY (`Username`, `TrailID`),
  CHECK (`Score` >= 1 AND `Score` <= 5)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `report`;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE `report` (
  `ReportID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
    COMMENT 'Fake PK. A composite PK of all attributes would work, but MySQL
      doesnt allow for NULL in composite PKs',
  `ReporterUsername` varchar(64) NOT NULL,
  `Username` varchar(64) DEFAULT NULL COMMENT 'If a profile is reported',
  `ReviewUsername` varchar(64) DEFAULT NULL,
  `ReviewTrailID` int(11) DEFAULT NULL,
  `CommentID` int(11) DEFAULT NULL,
  `PostID` int(11) DEFAULT NULL,
  FOREIGN KEY (`ReporterUsername`) REFERENCES `user`(`Username`),
  FOREIGN KEY (`Username`) REFERENCES `profile`(`Username`),
  FOREIGN KEY (`ReviewUsername`, `ReviewTrailID`) REFERENCES `review`(`Username`, `TrailID`),
  FOREIGN KEY (`CommentID`) REFERENCES `comment`(`CommentID`),
  FOREIGN KEY (`PostID`) REFERENCES `post`(`PostID`),
  CHECK ((`Username` IS NULL) + (`CommentID` IS NULL) + (`PostID` IS NULL) = 1 
    OR (`ReviewUsername` IS NOT NULL AND `ReviewTrailID` IS NOT NULL 
      AND `Username` IS NULL AND `CommentID` IS NULL AND `PostID` IS NULL))
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- Sample data for hiking_trials schema

USE hiking_trials;

-- Insert into `user`
INSERT INTO user (Username, UserType, Credibility, Email, PasswordHash, FirstName, LastName)
VALUES ('jdoe', 'TrailModerator', 80, 'jdoe@example.com', 'fakehash123', 'John', 'Doe');

INSERT INTO user (Username, UserType, Credibility, Email, PasswordHash, FirstName, LastName)
VALUES ('sSmith', NULL, 81, 'susan@example.com', 'fakehash123', 'Susan', 'Smith');

-- Insert into `profile`
INSERT INTO profile (Username, Description)
VALUES ('jdoe', 'Nature lover and experienced trail runner');

-- Insert into `location` (root location — no valid ParentLocationID yet)
INSERT INTO location (LocationID, ParentLocationID, Name, Description)
VALUES (1, 1, 'Washington State', 'Top-level test location');

-- Insert into `trail`
INSERT INTO trail (TrailID, LocationID, Open, DogFriendly, RatingAverage, Description, BikeAllowed, Name, Difficulty, Duration, Length)
VALUES (1, 1, b'1', b'1', 4.5, 'A scenic trail through the forest.', b'0', 'Evergreen Loop', 'Easy', '01:30:00', 3.2);

-- Insert into `post`
INSERT INTO post (PostID, Username, TrailID, Description, Title)
VALUES (1, 'jdoe', 1, 'Beautiful day on the trail!', 'First Hike');

-- Insert into `comment`
INSERT INTO comment (CommentID, PostID, Username, Description)
VALUES (1, 1, 'jdoe', 'Great trail!');

-- Insert into `review`
INSERT INTO review (Username, TrailID, Score, Description, Title)
VALUES ('jdoe', 1, 5, 'Absolutely stunning views.', 'Highly Recommend');

-- Insert into `weather`
INSERT INTO weather (WeatherID, TrailID, TemperatureF, PrecipitationChance, Conditions, ForecastSource, ForDate)
VALUES (1, 1, 65, 10, 'Sunny', 'NOAA', '2025-06-01');
