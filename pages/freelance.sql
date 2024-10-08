-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 07, 2024 at 04:00 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `freelance`
--

-- --------------------------------------------------------

--
-- Table structure for table `applied_jobs`
--

CREATE TABLE `applied_jobs` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `freelancer_id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `apply_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applied_jobs`
--

INSERT INTO `applied_jobs` (`id`, `job_id`, `freelancer_id`, `employer_id`, `apply_date`, `status`, `is_read`) VALUES
(1, 4, 2, 0, '2024-09-25 20:31:00', NULL, 0),
(3, 2, 1, 0, '2024-10-06 01:48:52', NULL, 0),
(4, 3, 1, 0, '2024-10-06 01:49:39', NULL, 0),
(5, 4, 1, 0, '2024-10-06 01:56:36', NULL, 0),
(6, 5, 1, 0, '2024-10-07 01:44:18', NULL, 0),
(7, 5, 1, 0, '2024-10-07 01:44:18', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `employers`
--

CREATE TABLE `employers` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiration` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employers`
--

INSERT INTO `employers` (`id`, `username`, `email`, `password`, `name`, `description`, `company`, `reset_token`, `token_expiration`) VALUES
(2, 'kamoso', 'honorekamoso@gmail.com', '12345', 'Honore MUGISHA', 'Digital Marketing / Design', 'Com agency', NULL, NULL),
(3, 'xxx', 'honore@gmail.com', '123', 'COM ONLINE', 'Design / Content Creation / Development', 'Com agency', NULL, NULL),
(4, 'tester', 'test@employer.com', '12345', 'tester', 'we do e commerce', 'Testing company', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `freelancers`
--

CREATE TABLE `freelancers` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `skills` text NOT NULL,
  `degree` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiration` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `freelancers`
--

INSERT INTO `freelancers` (`id`, `username`, `email`, `password`, `name`, `description`, `skills`, `degree`, `location`, `category`, `reset_token`, `token_expiration`) VALUES
(1, 'Immort', 'aimebruce@gmail.com', '12345', 'Bruce Immort', 'I am a programmer', 'HTML, CSS, JavaScript', 'highschool', 'kigali', 'it', '3acde5b0bb61aad65e58673c4a2e49d1a920f2e0454978035b102715c7bf4cfd49da65a8662a7bdc792b75d1ce4dd6a3caa5', '2024-07-29 16:37:41'),
(2, 'Bruce', 'bruce@gmail.com', '12345', 'Bruce', 'I am a passionate programer', 'HTML, CSS', 'highschool', 'kigali', 'it', NULL, NULL),
(3, 'Kamoso', 'kamoso@gmail.com', '12345', 'Kamoso', 'I am a passionate programer', 'HTML, CSS', 'highschool', 'kigali', 'it', NULL, NULL),
(5, 'Mugabo', 'mugabo@gmail.com', '12345', 'Mugabo', 'I am a passionate programer', 'HTML, CSS', 'highschool', 'kigali', 'it', NULL, NULL),
(8, 'Vanessa', 'vanessa@gmail.com', '12345', 'Vanessa', 'I am a passionate programer', 'HTML, CSS', 'highschool', 'kigali', 'it', NULL, NULL),
(9, 'Jack', 'jack@gmail.com', '12345', 'Jack', 'I am a passionate programer', 'HTML, CSS', 'highschool', 'kigali', 'it', NULL, NULL),
(10, 'Aime', 'aime@gmail.com', '12345', 'Aime', 'I am a passionate designer', 'JS, CSS', 'highschool', 'kigali', 'design', NULL, NULL),
(11, 'Doe', 'doe@gmail.com', '12345', 'Doe', 'I am a passionate designer', 'JS, CSS', 'highschool', 'kigali', 'design', NULL, NULL),
(12, 'John', 'john@gmail.com', '12345', 'John', 'I am a passionate designer', 'JS, CSS', 'highschool', 'west', 'design', NULL, NULL),
(13, 'Jane', 'jane@gmail.com', '12345', 'Jane', 'I am a passionate writer', 'MS word, Google docs', 'highschool', 'east', 'writing', NULL, NULL),
(14, 'test', 'test@user.com', '12345', 'tester', 'I like coding!', 'python', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hires`
--

CREATE TABLE `hires` (
  `id` int(11) NOT NULL,
  `freelancer_id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `money` decimal(10,2) NOT NULL,
  `job_category` varchar(255) NOT NULL,
  `period_time` varchar(255) NOT NULL,
  `hired_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `cancel_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hires`
--

INSERT INTO `hires` (`id`, `freelancer_id`, `employer_id`, `status`, `money`, `job_category`, `period_time`, `hired_at`, `cancel_reason`) VALUES
(1, 1, 0, '', 100000.00, 'it', '1 month', '2024-07-29 15:35:45', NULL),
(2, 1, 0, '', 100000.00, 'it', '1 month', '2024-07-29 15:37:56', NULL),
(3, 5, 2, 'pending', 100000.00, 'it', '2 month', '2024-08-12 13:12:23', NULL),
(5, 8, 2, 'pending', 200000.00, 'it', '1 week', '2024-08-12 13:21:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_offers`
--

CREATE TABLE `job_offers` (
  `job_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `job_description` text NOT NULL,
  `freelancer_email` varchar(255) NOT NULL,
  `status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `job_offers`
--

INSERT INTO `job_offers` (`job_id`, `job_title`, `job_description`, `freelancer_email`, `status`, `created_at`) VALUES
(1, 'Web Design', 'Front-End Development / Back-End Development', 'aimebruce@gmail.com', 'Pending', '2024-07-25 11:01:50'),
(2, 'Architecture', 'Enterior Design', 'honorekamoso@gmail.com', 'Pending', '2024-07-25 11:39:00');

-- --------------------------------------------------------

--
-- Table structure for table `job_posts`
--

CREATE TABLE `job_posts` (
  `id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `job_type` varchar(50) NOT NULL,
  `payment_range` varchar(100) NOT NULL,
  `job_description` text NOT NULL,
  `project_duration` varchar(100) NOT NULL,
  `skills_required` text NOT NULL,
  `employer_id` int(11) DEFAULT NULL,
  `job_rules` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_posts`
--

INSERT INTO `job_posts` (`id`, `job_title`, `job_type`, `payment_range`, `job_description`, `project_duration`, `skills_required`, `employer_id`, `job_rules`) VALUES
(2, 'School Site', 'part-time', '100000-200000', 'It is a site for school to help them get students', '2 monts', 'HTML, CSS', 3, NULL),
(3, 'Game in web', 'full-time', '100000-200000', 'On time', '2 monts', 'HTML, CSS', NULL, 'Not after the deadline lease'),
(4, 'Game in web', 'part-time', '100000-300000', 'Brooo', '2 monts', 'HTML, CSS', NULL, 'I want'),
(5, 'UI designer', 'full-time', '500-1000', 'design something good for me.', '1 month', 'Figma, Adobe suite', 4, 'nothing.');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

CREATE TABLE `newsletter` (
  `id` int(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applied_jobs`
--
ALTER TABLE `applied_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `freelancer_id` (`freelancer_id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- Indexes for table `employers`
--
ALTER TABLE `employers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `freelancers`
--
ALTER TABLE `freelancers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `hires`
--
ALTER TABLE `hires`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_offers`
--
ALTER TABLE `job_offers`
  ADD PRIMARY KEY (`job_id`);

--
-- Indexes for table `job_posts`
--
ALTER TABLE `job_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- Indexes for table `newsletter`
--
ALTER TABLE `newsletter`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applied_jobs`
--
ALTER TABLE `applied_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `employers`
--
ALTER TABLE `employers`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `freelancers`
--
ALTER TABLE `freelancers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `hires`
--
ALTER TABLE `hires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `job_offers`
--
ALTER TABLE `job_offers`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_posts`
--
ALTER TABLE `job_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `newsletter`
--
ALTER TABLE `newsletter`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applied_jobs`
--
ALTER TABLE `applied_jobs`
  ADD CONSTRAINT `applied_jobs_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `job_posts` (`id`),
  ADD CONSTRAINT `applied_jobs_ibfk_2` FOREIGN KEY (`freelancer_id`) REFERENCES `freelancers` (`id`),
  ADD CONSTRAINT `applied_jobs_ibfk_3` FOREIGN KEY (`employer_id`) REFERENCES `employers` (`id`);

--
-- Constraints for table `job_posts`
--
ALTER TABLE `job_posts`
  ADD CONSTRAINT `job_posts_ibfk_1` FOREIGN KEY (`employer_id`) REFERENCES `employers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;