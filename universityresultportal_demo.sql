-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 25, 2026 at 09:04 AM
-- Server version: 8.0.46
-- PHP Version: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `universityresultportal`
--

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `dept_id` int NOT NULL,
  `dept_name` varchar(100) NOT NULL,
  `hod_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`dept_id`, `dept_name`, `hod_name`) VALUES
(1, 'Computer Science', 'Dr. Sharma'),
(2, 'Mechanical', 'Dr. Patil'),
(3, 'Electrical', 'Dr. Joshi');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `enrollment_id` int NOT NULL,
  `student_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `semester_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `enrollment`
--

INSERT INTO `enrollment` (`enrollment_id`, `student_id`, `subject_id`, `semester_id`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 2, 1, 1),
(5, 2, 2, 1),
(6, 3, 1, 1),
(7, 3, 3, 1),
(8, 4, 4, 1),
(9, 5, 5, 1),
(10, 6, 1, 2),
(11, 6, 2, 2),
(12, 6, 6, 2),
(13, 7, 1, 1),
(19, 7, 2, 1),
(25, 7, 3, 1),
(14, 8, 1, 1),
(20, 8, 2, 1),
(26, 8, 3, 1),
(31, 9, 4, 1),
(40, 9, 7, 1),
(49, 9, 8, 1),
(32, 10, 4, 1),
(41, 10, 7, 1),
(50, 10, 8, 1),
(33, 11, 4, 1),
(42, 11, 7, 1),
(51, 11, 8, 1),
(34, 12, 4, 1),
(43, 12, 7, 1),
(52, 12, 8, 1),
(58, 13, 5, 1),
(67, 13, 9, 1),
(76, 13, 10, 1),
(59, 14, 5, 1),
(68, 14, 9, 1),
(77, 14, 10, 1),
(60, 15, 5, 1),
(69, 15, 9, 1),
(78, 15, 10, 1),
(15, 16, 1, 1),
(21, 16, 2, 1),
(27, 16, 3, 1),
(16, 17, 1, 1),
(22, 17, 2, 1),
(28, 17, 3, 1),
(17, 18, 1, 1),
(23, 18, 2, 1),
(29, 18, 3, 1),
(18, 19, 1, 1),
(24, 19, 2, 1),
(30, 19, 3, 1),
(35, 20, 4, 1),
(44, 20, 7, 1),
(53, 20, 8, 1),
(36, 21, 4, 1),
(45, 21, 7, 1),
(54, 21, 8, 1),
(37, 22, 4, 1),
(46, 22, 7, 1),
(55, 22, 8, 1),
(38, 23, 4, 1),
(47, 23, 7, 1),
(56, 23, 8, 1),
(39, 24, 4, 1),
(48, 24, 7, 1),
(57, 24, 8, 1),
(61, 25, 5, 1),
(70, 25, 9, 1),
(79, 25, 10, 1),
(62, 26, 5, 1),
(71, 26, 9, 1),
(80, 26, 10, 1),
(63, 27, 5, 1),
(72, 27, 9, 1),
(81, 27, 10, 1),
(64, 28, 5, 1),
(73, 28, 9, 1),
(82, 28, 10, 1),
(65, 29, 5, 1),
(74, 29, 9, 1),
(83, 29, 10, 1),
(66, 30, 5, 1),
(75, 30, 9, 1),
(84, 30, 10, 1);

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `faculty_id` int NOT NULL,
  `faculty_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `dept_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`faculty_id`, `faculty_name`, `email`, `dept_id`) VALUES
(1, 'Amit Sharma', 'amit@university.edu', 1),
(2, 'Neha Patil', 'neha@university.edu', 1),
(3, 'Rahul Joshi', 'rahul@university.edu', 2),
(4, 'Priya Mehta', 'priya@university.edu', 3);

-- --------------------------------------------------------

--
-- Table structure for table `result`
--

CREATE TABLE `result` (
  `result_id` int NOT NULL,
  `enrollment_id` int NOT NULL,
  `internal_marks` decimal(5,2) NOT NULL,
  `external_marks` decimal(5,2) NOT NULL
) ;

--
-- Dumping data for table `result`
--

INSERT INTO `result` (`result_id`, `enrollment_id`, `internal_marks`, `external_marks`) VALUES
(1, 1, 25.00, 60.00),
(2, 2, 24.00, 55.00),
(3, 3, 27.00, 62.00),
(4, 4, 22.00, 58.00),
(5, 5, 26.00, 59.00),
(6, 6, 28.00, 65.00),
(7, 7, 23.00, 57.00),
(8, 8, 21.00, 54.00),
(9, 9, 25.00, 61.00),
(10, 10, 27.00, 63.00),
(11, 11, 24.00, 60.00),
(12, 12, 26.00, 64.00),
(13, 13, 22.00, 58.00),
(14, 14, 23.00, 59.00),
(15, 15, 24.00, 60.00),
(16, 16, 25.00, 61.00),
(17, 17, 27.00, 61.00),
(18, 18, 27.00, 63.00),
(19, 19, 28.00, 64.00),
(20, 20, 29.00, 65.00),
(21, 21, 30.00, 66.00),
(22, 22, 20.00, 67.00),
(23, 23, 21.00, 68.00),
(24, 24, 22.00, 69.00),
(25, 25, 23.00, 70.00),
(26, 26, 24.00, 45.00),
(27, 27, 25.00, 46.00),
(28, 28, 26.00, 47.00),
(29, 29, 27.00, 48.00),
(30, 30, 28.00, 49.00),
(31, 31, 29.00, 50.00),
(32, 32, 30.00, 51.00),
(33, 33, 20.00, 52.00),
(34, 34, 21.00, 53.00),
(35, 35, 22.00, 54.00),
(36, 36, 23.00, 55.00),
(37, 37, 24.00, 56.00),
(38, 38, 25.00, 57.00),
(39, 39, 26.00, 58.00),
(40, 40, 27.00, 59.00),
(41, 41, 28.00, 60.00),
(42, 42, 29.00, 61.00),
(43, 43, 30.00, 62.00),
(44, 44, 20.00, 63.00),
(45, 45, 21.00, 64.00),
(46, 46, 22.00, 65.00),
(47, 47, 23.00, 66.00),
(48, 48, 24.00, 67.00),
(49, 49, 25.00, 68.00),
(50, 50, 26.00, 69.00),
(51, 51, 27.00, 70.00),
(52, 52, 28.00, 45.00),
(53, 53, 29.00, 46.00),
(54, 54, 30.00, 47.00),
(55, 55, 20.00, 48.00),
(56, 56, 21.00, 49.00),
(57, 57, 22.00, 50.00),
(58, 58, 23.00, 51.00),
(59, 59, 24.00, 52.00),
(60, 60, 25.00, 53.00),
(61, 61, 26.00, 54.00),
(62, 62, 27.00, 55.00),
(63, 63, 28.00, 56.00),
(64, 64, 29.00, 57.00),
(65, 65, 30.00, 58.00),
(66, 66, 20.00, 59.00),
(67, 67, 21.00, 60.00),
(68, 68, 22.00, 61.00),
(69, 69, 23.00, 62.00),
(70, 70, 24.00, 63.00),
(71, 71, 25.00, 64.00),
(72, 72, 26.00, 65.00),
(73, 73, 27.00, 66.00),
(74, 74, 28.00, 67.00),
(75, 75, 29.00, 68.00),
(76, 76, 30.00, 69.00),
(77, 77, 20.00, 70.00),
(78, 78, 21.00, 45.00),
(79, 79, 22.00, 46.00),
(80, 80, 23.00, 47.00),
(81, 81, 24.00, 48.00),
(82, 82, 25.00, 49.00),
(83, 83, 26.00, 50.00),
(84, 84, 27.00, 51.00);

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `semester_id` int NOT NULL,
  `semester_number` int NOT NULL,
  `academic_year` varchar(9) NOT NULL
) ;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`semester_id`, `semester_number`, `academic_year`) VALUES
(1, 3, '2026-2027'),
(2, 4, '2026-2027');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `student_id` int NOT NULL,
  `prn` varchar(20) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `admission_year` year DEFAULT NULL,
  `dept_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`student_id`, `prn`, `student_name`, `email`, `phone`, `admission_year`, `dept_id`) VALUES
(1, 'PRN001', 'Rahul Sharma', 'rahul@gmail.com', '9876543210', '2025', 1),
(2, 'PRN002', 'Aman Verma', 'aman@gmail.com', '9876543211', '2025', 1),
(3, 'PRN003', 'Priya Singh', 'priya@gmail.com', '9876543212', '2025', 1),
(4, 'PRN004', 'Rohan Patil', 'rohan@gmail.com', '9876543213', '2025', 2),
(5, 'PRN005', 'Sneha Joshi', 'sneha@gmail.com', '9876543214', '2025', 3),
(6, 'PRN006', 'Karan Mehta', 'karan@gmail.com', '9876543215', '2025', 1),
(7, 'PRN007', 'Arjun Desai', 'arjun@gmail.com', '9876543216', '2025', 1),
(8, 'PRN008', 'Ananya Rao', 'ananya@gmail.com', '9876543217', '2025', 1),
(9, 'PRN009', 'Vikram Shah', 'vikram@gmail.com', '9876543218', '2025', 2),
(10, 'PRN010', 'Kavya Nair', 'kavya@gmail.com', '9876543219', '2025', 2),
(11, 'PRN011', 'Aditya Kulkarni', 'aditya@gmail.com', '9876543220', '2025', 2),
(12, 'PRN012', 'Meera Joshi', 'meera@gmail.com', '9876543221', '2025', 2),
(13, 'PRN013', 'Sahil Gupta', 'sahil@gmail.com', '9876543222', '2025', 3),
(14, 'PRN014', 'Isha Shah', 'isha@gmail.com', '9876543223', '2025', 3),
(15, 'PRN015', 'Dev Malhotra', 'dev@gmail.com', '9876543224', '2025', 3),
(16, 'PRN016', 'Riya Sharma', 'riya@gmail.com', '9876543225', '2025', 1),
(17, 'PRN017', 'Nikhil Verma', 'nikhil@gmail.com', '9876543226', '2025', 1),
(18, 'PRN018', 'Aarav Patel', 'aarav@gmail.com', '9876543227', '2025', 1),
(19, 'PRN019', 'Simran Kaur', 'simran@gmail.com', '9876543228', '2025', 1),
(20, 'PRN020', 'Kunal Desai', 'kunal@gmail.com', '9876543229', '2025', 2),
(21, 'PRN021', 'Pooja Rao', 'pooja@gmail.com', '9876543230', '2025', 2),
(22, 'PRN022', 'Harsh Mehta', 'harsh@gmail.com', '9876543231', '2025', 2),
(23, 'PRN023', 'Tanvi Shah', 'tanvi@gmail.com', '9876543232', '2025', 2),
(24, 'PRN024', 'Yash Kulkarni', 'yash@gmail.com', '9876543233', '2025', 2),
(25, 'PRN025', 'Ishita Gupta', 'ishita@gmail.com', '9876543234', '2025', 3),
(26, 'PRN026', 'Om Joshi', 'om@gmail.com', '9876543235', '2025', 3),
(27, 'PRN027', 'Aditi Nair', 'aditi@gmail.com', '9876543236', '2025', 3),
(28, 'PRN028', 'Rohan Gupta', 'rohan2@gmail.com', '9876543237', '2025', 3),
(29, 'PRN029', 'Diya Mehta', 'diya@gmail.com', '9876543238', '2025', 3),
(30, 'PRN030', 'Manav Shah', 'manav@gmail.com', '9876543239', '2025', 3),
(31, 'DEMO031', 'Demo Student 31', 'demo31@example.com', '0000000031', '2025', 1),
(32, 'DEMO032', 'Demo Student 32', 'demo32@example.com', '0000000032', '2025', 1);

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `subject_id` int NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `credits` int NOT NULL,
  `dept_id` int NOT NULL,
  `faculty_id` int DEFAULT NULL
) ;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`subject_id`, `subject_code`, `subject_name`, `credits`, `dept_id`, `faculty_id`) VALUES
(1, 'CS301', 'DBMS', 4, 1, 1),
(2, 'CS302', 'Data Structures', 4, 1, 2),
(3, 'CS303', 'Java Programming', 3, 1, 1),
(4, 'ME301', 'Thermodynamics', 4, 2, 3),
(5, 'EE301', 'Circuit Theory', 4, 3, 4),
(6, 'CS304', 'Design and Analysis of Algorithms', 4, 1, 2),
(7, 'ME302', 'Fluid Mechanics', 4, 2, 3),
(8, 'ME303', 'Engineering Mechanics', 3, 2, 3),
(9, 'EE302', 'Power Systems', 4, 3, 4),
(10, 'EE303', 'Electrical Machines', 3, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('student','faculty') NOT NULL,
  `student_id` int DEFAULT NULL,
  `faculty_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `role`, `student_id`, `faculty_id`, `created_at`) VALUES
(1, 'PRN001', '$2y$12$1xh3urI81kMxV047x.XSUefHZ8SnI4tzcxq3L5wiJrteTrKMwvpUe', 'student', 1, NULL, '2026-11-19 15:21:07'),
(2, 'amit.sharma', '$2y$12$MVracfNawLP1stYu7495pOTsus1KuB5q/zPt6ydlspK2zzKx.C5A.', 'faculty', NULL, 1, '2026-11-19 15:21:08'),
(3, 'PRN002', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 2, NULL, '2026-11-19 17:13:07'),
(4, 'DEMO032', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 32, NULL, '2026-11-19 17:21:31'),
(5, 'DEMO031', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 31, NULL, '2026-11-19 17:21:31'),
(6, 'PRN003', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 3, NULL, '2026-11-19 17:21:31'),
(7, 'PRN004', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 4, NULL, '2026-11-19 17:21:31'),
(8, 'PRN005', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 5, NULL, '2026-11-19 17:21:31'),
(9, 'PRN006', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 6, NULL, '2026-11-19 17:21:31'),
(10, 'PRN007', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 7, NULL, '2026-11-19 17:21:31'),
(11, 'PRN008', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 8, NULL, '2026-11-19 17:21:31'),
(12, 'PRN009', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 9, NULL, '2026-11-19 17:21:31'),
(13, 'PRN010', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 10, NULL, '2026-11-19 17:21:31'),
(14, 'PRN011', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 11, NULL, '2026-11-19 17:21:31'),
(15, 'PRN012', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 12, NULL, '2026-11-19 17:21:31'),
(16, 'PRN013', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 13, NULL, '2026-11-19 17:21:31'),
(17, 'PRN014', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 14, NULL, '2026-11-19 17:21:31'),
(18, 'PRN015', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 15, NULL, '2026-11-19 17:21:31'),
(19, 'PRN016', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 16, NULL, '2026-11-19 17:21:31'),
(20, 'PRN017', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 17, NULL, '2026-11-19 17:21:31'),
(21, 'PRN018', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 18, NULL, '2026-11-19 17:21:31'),
(22, 'PRN019', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 19, NULL, '2026-11-19 17:21:31'),
(23, 'PRN020', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 20, NULL, '2026-11-19 17:21:31'),
(24, 'PRN021', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 21, NULL, '2026-11-19 17:21:31'),
(25, 'PRN022', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 22, NULL, '2026-11-19 17:21:31'),
(26, 'PRN023', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 23, NULL, '2026-11-19 17:21:31'),
(27, 'PRN024', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 24, NULL, '2026-11-19 17:21:31'),
(28, 'PRN025', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 25, NULL, '2026-11-19 17:21:31'),
(29, 'PRN026', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 26, NULL, '2026-11-19 17:21:31'),
(30, 'PRN027', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 27, NULL, '2026-11-19 17:21:31'),
(31, 'PRN028', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 28, NULL, '2026-11-19 17:21:31'),
(32, 'PRN029', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 29, NULL, '2026-11-19 17:21:31'),
(33, 'PRN030', '$2y$12$XBGAlA28spNjDZBl2I6HIucEvU1.DARpB.eQwCf/egyjhAIs59ywK', 'student', 30, NULL, '2026-11-19 17:21:31'),
(35, 'neha.patil', '$2y$12$7W6TrTFIVbxJo8mnkmxOveOlB.bd2iXNto6.5BUZ2ubBcT2AICCQ2', 'faculty', NULL, 2, '2026-11-19 17:24:25'),
(36, 'rahul.joshi', '$2y$12$7W6TrTFIVbxJo8mnkmxOveOlB.bd2iXNto6.5BUZ2ubBcT2AICCQ2', 'faculty', NULL, 3, '2026-11-19 17:24:25'),
(37, 'priya.mehta', '$2y$12$7W6TrTFIVbxJo8mnkmxOveOlB.bd2iXNto6.5BUZ2ubBcT2AICCQ2', 'faculty', NULL, 4, '2026-11-19 17:24:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`dept_id`),
  ADD UNIQUE KEY `dept_name` (`dept_name`);

--
-- Indexes for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`subject_id`,`semester_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`faculty_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `result`
--
ALTER TABLE `result`
  ADD PRIMARY KEY (`result_id`),
  ADD UNIQUE KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`semester_id`),
  ADD UNIQUE KEY `semester_number` (`semester_number`,`academic_year`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `prn` (`prn`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `dept_id` (`dept_id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`),
  ADD KEY `dept_id` (`dept_id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `faculty_id` (`faculty_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `dept_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `enrollment`
--
ALTER TABLE `enrollment`
  MODIFY `enrollment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `faculty_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `result`
--
ALTER TABLE `result`
  MODIFY `result_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `semester_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `student_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `subject_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`),
  ADD CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subject` (`subject_id`),
  ADD CONSTRAINT `enrollment_ibfk_3` FOREIGN KEY (`semester_id`) REFERENCES `semester` (`semester_id`);

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`);

--
-- Constraints for table `result`
--
ALTER TABLE `result`
  ADD CONSTRAINT `result_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollment` (`enrollment_id`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`);

--
-- Constraints for table `subject`
--
ALTER TABLE `subject`
  ADD CONSTRAINT `subject_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `department` (`dept_id`),
  ADD CONSTRAINT `subject_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`),
  ADD CONSTRAINT `fk_user_student` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
