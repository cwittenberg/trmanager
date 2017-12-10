
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `trmanager`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit`
--

CREATE TABLE `audit` (
  `eventID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `category` varchar(100) NOT NULL,
  `IP` varchar(16) NOT NULL,
  `description` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `connections`
--

CREATE TABLE `connections` (
  `ID` int(11) NOT NULL,
  `Name` varchar(250) NOT NULL,
  `SSHHostName` varchar(2000) NOT NULL,
  `SSHPort` int(8) NOT NULL,
  `SSHUser` varchar(100) NOT NULL,
  `SSHKey` text NOT NULL,
  `Compression` varchar(3) NOT NULL DEFAULT 'yes',
  `TCPKeepAlive` varchar(3) NOT NULL DEFAULT 'yes',
  `ServerAliveInterval` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `forwards`
--

CREATE TABLE `forwards` (
  `connectionID` int(11) NOT NULL,
  `forwardID` int(11) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `localPort` int(8) NOT NULL,
  `remoteTargetHost` varchar(2000) NOT NULL,
  `remoteTargetPort` int(8) NOT NULL,
  `type` int(1) NOT NULL,
  `virtualHost` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `forwards`
--

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `configurationID` int(11) NOT NULL,
  `configurationName` varchar(500) NOT NULL,
  `configurationValue` varchar(3000) DEFAULT '',
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`configurationID`, `configurationName`, `configurationValue`, `category`) VALUES
(3, 'PID directory', '/tmp', 'Paths'),
(5, 'Tunnel Relay Manager URL', 'http://trmanager.awesome.domain.com', 'Tunnel Relay Manager'),
(6, 'Proxy Host', '', 'Proxy configuration'),
(7, 'Proxy Port', '', 'Proxy configuration'),
(8, 'Tunnel open wait time (sec)', '3', 'Tunnel Relay Manager'),
(9, 'Path to Corkscrew', '/usr/bin/corkscrew', 'Paths'),
(10, 'Path to AutoSSH', '/usr/bin/autossh', 'Paths'),
(11, 'Path to ssh-add', '/usr/bin/ssh-add', 'Paths'),
(12, 'Path to Apache2 configs', '/etc/apache2/sites-available', 'Paths'),
(13, 'Virtual host domain', 'awesome.domain.com', 'Tunnel Relay Manager'),
(14, 'Path to Apache2 a2ensite', '/usr/sbin/a2ensite', 'Paths'),
(15, 'Path to Apache2 service', '/usr/sbin/service apache2', 'Paths'),
(16, 'Virtual host port', '80', 'Tunnel Relay Manager'),
(17, 'Path to Apache2 a2dissite', '/usr/sbin/a2dissite', 'Paths'),
(18, 'Path suffix for virtualhost', '.conf', 'Paths');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `connectionID` int(11) NOT NULL,
  `localForwardID` int(11) NOT NULL,
  `PID` int(11) NOT NULL,
  `errortext` varchar(2000) NOT NULL,
  `command` varchar(2000) NOT NULL,
  `killcommand` varchar(2000) NOT NULL,
  `activeSince` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `status`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `firstName` varchar(500) NOT NULL,
  `lastName` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `password` varchar(32) NOT NULL,
  `isAdmin` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `firstName`, `lastName`, `email`, `password`, `isAdmin`) VALUES
(1, 'TRManager', 'Admin', 'admin@admin.com', 'e432bfbd202ba2278698a0b6d86b68f5', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit`
--
ALTER TABLE `audit`
  ADD PRIMARY KEY (`eventID`);

--
-- Indexes for table `connections`
--
ALTER TABLE `connections`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Name` (`Name`);

--
-- Indexes for table `forwards`
--
ALTER TABLE `forwards`
  ADD PRIMARY KEY (`forwardID`);

--

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`configurationID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit`
--
ALTER TABLE `audit`
  MODIFY `eventID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;
--
-- AUTO_INCREMENT for table `connections`
--
ALTER TABLE `connections`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `forwards`
--
ALTER TABLE `forwards`
  MODIFY `forwardID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--

-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `configurationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
