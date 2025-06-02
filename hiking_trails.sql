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
INSERT INTO `location` (`ParentLocationID`, `Name`, `Description`) VALUES
(NULL, 'Washington', 'State in the Pacific Northwest region of the United States'),
(NULL, 'California', 'State on the West Coast of the United States'),
(1, 'Gold Bar', 'City in Snohomish County, Washington, known for outdoor activities'),
(1, 'Mount Rainier National Park', 'National park in Washington centered around Mount Rainier'),
(2, 'Yosemite National Park', 'Iconic national park in California with giant sequoias and granite cliffs'),
(2, 'Big Sur', 'Scenic region along California’s Central Coast'),
(1, 'Olympic National Park', 'Diverse park on the Olympic Peninsula, featuring mountains, rainforest, and coastline'),
(1, 'North Cascades National Park', 'Mountainous park in northern Washington with rugged peaks and alpine lakes'),
(2, 'Redwood National and State Parks', 'Northern California parks with towering redwood trees and coastal views'),
(2, 'Joshua Tree National Park', 'Desert park known for its unique Joshua trees and surreal rock formations'),
(2, 'Lake Tahoe', 'Large freshwater lake in the Sierra Nevada Mountains, straddling California and Nevada'),
(2, 'Sequoia National Park', 'Home to massive sequoia trees including General Sherman, the largest tree on Earth');
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

INSERT INTO `user` 
(`Username`, `UserType`, `Credibility`, `Email`, `PasswordHash`, `FirstName`, `LastName`) 
VALUES
('jdoe', NULL, 85, 'jdoe@example.com', UNHEX('5f4dcc3b5aa765d61d8327deb882cf99'), 'John', 'Doe'),
('asmith', 'PostModerator', 100, 'asmith@example.com', UNHEX('d8578edf8458ce06fbc5bb76a58c5ca4'), 'Alice', 'Smith'),
('kclark', NULL, 60, 'kai.clark@example.com', UNHEX('e99a18c428cb38d5f260853678922e03'), 'Kai', 'Clark'),
('elliot_b', NULL, 55, 'elliot.brown@example.com', UNHEX('25d55ad283aa400af464c76d713c07ad'), 'Elliot', 'Brown'),
('roryneal', 'TrailModerator', 100, 'rory.neal@example.com', UNHEX('5ebe2294ecd0e0f08eab7690d2a6ee69'), 'Rory', 'Neal'),
('awelch', NULL, 88, 'arianna.welch@example.com', UNHEX('6cb75f652a9b52798eb6cf2201057c73'), 'Ariana', 'Welch'),
('alana_s', NULL, 45, 'alana.shaw@example.com', UNHEX('8d3533d75ae2c3966d7e0d4fcc69216b'), 'Alana', 'Shaw'),
('d_everett', 'TrailModerator', 100, 'duncan.everett@example.com', UNHEX('03c7c0ace395d80182db07ae2c30f034'), 'Duncan', 'Everett'),
('arthur_c', NULL, 67, 'arthur.chen@example.com', UNHEX('e10adc3949ba59abbe56e057f20f883e'), 'Arthur', 'Chen'),
('hunter_b', 'PostModerator', 100, 'hunter.briggs@example.com', UNHEX('098f6bcd4621d373cade4e832627b4f6'), 'Hunter', 'Briggs');

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

INSERT INTO `profile` (`Username`, `Description`) VALUES
('jdoe', 'A regular guy with a passion for exploring new trails.'),
('kclark', 'Businessman by day but nighttime hiker.'),
('elliot_b', 'Journalist and nature lover. Often hikes in search of quiet and inspiration.'),
('awelch', 'Strong and adventurous, always seeking new paths to conquer.'),
('alana_s', 'Independent spirit with a love for urban trails and mountain views.'),
('arthur_c', 'I love hiking and pushing myself outdoors.');

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
INSERT INTO `trail` 
(`LocationID`, `Open`, `DogFriendly`, `RatingAverage`, `Description`, `BikeAllowed`, `Name`, `Difficulty`, `Duration`, `Length`)
VALUES 
(12, 1, NULL, 4.27, 'A popular and challenging trail with views of the Snoqualmie Valley.', 0, 'Mount Si Trail', 'Hard', '4:10:00', 8),
(12, 1, 0, 4.79, 'A short, steep hike with stunning views over Rattlesnake Lake.', 1, 'Rattlesnake Ledge', 'Easy', '1:25:00', 4),
(12, 0, 0, 4.42, 'A moderate hike to beautiful waterfalls in the Snoqualmie region.', 0, 'Twin Falls Trail', 'Easy', '1:10:00', 2.4),
(5, 0, 1, 4.86, 'Scenic alpine lake hike with mountain views and moderate difficulty.', 0, 'Snow Lake Trail', 'Hard', '3:30:00', 6.6),
(4, NULL, 0, 4.71, 'Strenuous hike with rewarding summit views and a mailbox at the top.', 0, 'Mailbox Peak', 'Hard', '4:25:00', 9.4),
(6, NULL, 0, 3.59, 'Loop trail with boardwalk sections around a pristine alpine lake.', 1, 'Lake 22 Trail', 'Medium', '2:45:00', 5.4),
(10, 0, 1, 4.17, 'Easy hike to a picturesque waterfall near Snoqualmie Pass.', 1, 'Franklin Falls Trail', 'Easy', '1:00:00', 2.0),
(6, 1, NULL, 4.39, 'Urban trail offering forest, beach, and views of Puget Sound.', 0, 'Discovery Park Loop', 'Easy', '1:30:00', 2.8),
(7, NULL, 1, 3.87, 'Spectacular views of Mount Rainier and wildflower-filled meadows.', 1, 'Naches Peak Loop', 'Medium', '2:00:00', 3.5),
(5, NULL, NULL, 4.73, 'Classic Mount Rainier trail with glaciers, wildflowers, and ridgelines.', 1, 'Skyline Trail', 'Medium', '2:45:00', 5.5),
(3, 1, 0, 3.51, 'Forested hike leading to a tranquil mountain lake.', 1, 'Heather Lake Trail', 'Medium', '2:20:00', 4.6),
(6, 1, NULL, 3.83, 'Lush moss-covered trail in Olympic National Park.', 0, 'Hoh Rain Forest Trail', 'Medium', '2:25:00', 5.0),
(6, 1, 0, 3.82, 'Short trail to the northwesternmost point in the contiguous U.S.', 0, 'Cape Flattery Trail', 'Easy', '0:55:00', 1.5),
(10, NULL, 1, 4.92, 'Moderate hike to a stunning cascading waterfall near Index.', 1, 'Bridal Veil Falls', 'Medium', '2:15:00', 4.0),
(5, 1, 1, 4.61, 'Iconic Enchantments hike with turquoise alpine lake views.', 1, 'Colchuck Lake', 'Hard', '4:10:00', 8),
(3, 1, 0, 4.83, 'Coastal bluff trail with views of Puget Sound and Olympic Mountains.', 1, 'Ebey’s Landing', 'Medium', '3:00:00', 5.6),
(6, NULL, 0, 3.7, 'Popular waterfall hike with several falls and a forested trail.', 0, 'Wallace Falls', 'Medium', '2:50:00', 5.6),
(4, 1, 0, 4.85, 'Urban loop around a lake, great for walking and jogging.', NULL, 'Green Lake Trail', 'Easy', '1:25:00', 2.8),
(4, 1, 1, 3.69, 'Steep alpine hike to a fire lookout with panoramic views.', NULL, 'Mount Pilchuck', 'Medium', '2:45:00', 5.4),
(3, NULL, NULL, 3.51, 'Challenging hike with a dramatic waterfall and forested trail.', NULL, 'Teneriffe Falls', 'Medium', '2:40:00', 5.4);
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

INSERT INTO `explored` (`Username`, `TrailID`) VALUES
('kclark', 1),
('kclark', 5),
('kclark', 7),
('jdoe', 2),
('jdoe', 5),
('jdoe', 8),
('jdoe', 11),
('jdoe', 14),
('elliot_b', 2),
('elliot_b', 4),
('elliot_b', 6),
('elliot_b', 8),
('elliot_b', 9),
('awelch', 1),
('awelch', 3),
('awelch', 7),
('awelch', 10),
('awelch', 15),
('awelch', 19),
('alana_s', 3),
('alana_s', 5),
('alana_s', 6),
('alana_s', 7),
('alana_s', 10),
('alana_s', 11),
('arthur_c', 1),
('arthur_c', 4),
('arthur_c', 6),
('arthur_c', 7),
('arthur_c', 8),
('arthur_c', 9);

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

INSERT INTO `post` (`Username`, `TrailID`, `Title`, `Description`) VALUES
('jdoe', 2, 'Morning Hike', 'Enjoyed a refreshing morning walk with beautiful scenery.'),
('kclark', 5, 'Loved MailBox Trail', 'Loved the hike and it had a great view at the top.'),
('elliot_b', 6, 'Great Chill Hike', 'A perfect place for some peaceful reflection and nature photos.'),
('alana_s', 8, 'Rooftop Views', 'The urban views from this trail are spectacular, especially at sunset.'),
('arthur_c', 7, 'Coastal Breeze', 'Felt the ocean breeze and explored underwater ecosystems along this trail.'),
('awelch', 1, 'Forest Walk', 'A serene trail that reconnects you with nature and calmness.');

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

INSERT INTO `weather` (`TrailID`, `TemperatureF`, `PrecipitationChance`, `Conditions`, `ForecastSource`, `ForDate`) VALUES
(1, 75, 10, 'Partly Cloudy', 'OpenWeather', '2025-06-04'),
(2, 82, 5, 'Sunny', 'OpenWeather', '2025-06-04'),
(3, 68, 20, 'Mostly Cloudy', 'OpenWeather', '2025-06-04'),
(4, 70, 30, 'Light Rain', 'OpenWeather', '2025-06-04'),
(5, 65, 50, 'Rain Showers', 'OpenWeather', '2025-06-04'),
(6, 78, 0, 'Clear', 'OpenWeather', '2025-06-04'),
(7, 74, 15, 'Partly Cloudy', 'OpenWeather', '2025-06-04'),
(8, 80, 10, 'Sunny', 'OpenWeather', '2025-06-04'),
(9, 69, 40, 'Overcast', 'OpenWeather', '2025-06-04'),
(10, 72, 25, 'Cloudy', 'OpenWeather', '2025-06-04'),
(11, 77, 5, 'Sunny', 'OpenWeather', '2025-06-04'),
(12, 64, 60, 'Heavy Rain', 'OpenWeather', '2025-06-04'),
(13, 71, 10, 'Partly Cloudy', 'OpenWeather', '2025-06-04'),
(14, 67, 35, 'Rain Showers', 'OpenWeather', '2025-06-04'),
(15, 73, 0, 'Clear', 'OpenWeather', '2025-06-04'),
(16, 76, 5, 'Sunny', 'OpenWeather', '2025-06-04'),
(17, 70, 20, 'Mostly Cloudy', 'OpenWeather', '2025-06-04'),
(18, 69, 30, 'Light Rain', 'OpenWeather', '2025-06-04'),
(19, 75, 10, 'Partly Cloudy', 'OpenWeather', '2025-06-04'),
(20, 68, 40, 'Overcast', 'OpenWeather', '2025-06-04');

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

INSERT INTO `comment` (`PostID`, `Username`, `Description`) VALUES
(1, 'kclark', 'Sounds like a great way to start the day!'),
(2, 'alana_s', 'That sounds fun, might have to try it soon.'),
(3, 'awelch', 'I also love taking pictures on a hike. Got any to share?'),
(4, 'arthur_c', 'Great place for reflection. Thanks for the rec!'),
(5, 'elliot_b', 'Urban trails are underrated. Nice find!'),
(6, 'jdoe', 'That breeze sounds amazing, I’ll check it out.');

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

INSERT INTO `review` (`Username`, `TrailID`, `Score`, `Description`, `Title`) VALUES
('kclark', 1, 5, 'Loved the solitude and the skyline views.', 'Great Views'),
('kclark', 5, 4, 'A relaxing afternoon walk with great scenery.', 'Calm & Clear'),
('jdoe', 2, 4, 'Nice terrain, good for a mid-week hike.', 'Solid Hike'),
('jdoe', 14, 3, 'Bit crowded but otherwise enjoyable.', 'Busy but Scenic'),
('elliot_b', 6, 5, 'Perfect trail to clear your mind.', 'Very Relaxing'),
('elliot_b', 9, 4, 'Loved the green coverage and calm atmosphere.', 'Nice Scenery'),
('awelch', 10, 5, 'Beautiful path, inspiring nature.', 'Nice path and Good Views'),
('awelch', 15, 4, 'Great terrain for long hikes.', 'Long but fun'),
('alana_s', 6, 4, 'Long hike, but a rewarding view and nice lake at the top.', 'Hard Hike with a Nice Reward'),
('alana_s', 7, 5, 'Easy hike with a great waterfall view.', 'Great hike for taking it easy.'),
('arthur_c', 4, 5, 'Great elevstion and nice snowy views.', 'Snowy but not too hard'),
('arthur_c', 8, 3, 'Could use more signage but overall solid.', 'Good Trail');

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
-- --------------------------------------------------------

INSERT INTO `report` 
(`ReporterUsername`, `Username`, `ReviewUsername`, `ReviewTrailID`, `CommentID`, `PostID`) 
VALUES
-- Reports targeting alana_s
('jdoe', 'alana_s', NULL, NULL, NULL, NULL),           	-- Profile
('elliot_b', NULL, 'alana_s', 6, NULL, NULL),           -- Review
('arthur_c', NULL, NULL, NULL, 3, NULL),                -- Comment
('kclark', 'alana_s', NULL, NULL, NULL, NULL),         	-- Profile
('jdoe', NULL, NULL, NULL, NULL, 4),             		    -- Post
('awelch', NULL, NULL, NULL, NULL, 4),                 	-- Post
-- Reports targeting elliot_b
('kclark', NULL, 'elliot_b', 9, NULL, NULL),            -- Review
('alana_s', NULL, NULL, NULL, 5, NULL),                	-- Comment
('jdoe', NULL, 'elliot_b', 6, NULL, NULL),              -- Review
('arthur_c', 'elliot_b', NULL, NULL, NULL, NULL),       -- Profile
('kclark', NULL, NULL, NULL, NULL, 3),               	  -- Post
-- Reports targeting kclark
('arthur_c', NULL, 'kclark', 5, NULL, NULL),            -- Review
('jdoe', 'kclark', NULL, NULL, NULL, NULL),             -- Profile
('arthur_c', NULL, NULL, NULL, NULL, 2),            	  -- Post
('alana_s', NULL, NULL, NULL, 1, NULL),             	  -- Comment
-- Reports targeting arthur_c
('elliot_b', NULL, NULL, NULL, 4, NULL),                -- Comment
('jdoe', NULL, 'arthur_c', 8, NULL, NULL),            	-- Review
('kclark', 'arthur_c', NULL, NULL, NULL, NULL),         -- Profile
-- Reports targeting jdoe
('alana_s', NULL, 'jdoe', 2, NULL, NULL);              	-- Review

-- --------------------------------------------------------
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

USE hiking_trials;
