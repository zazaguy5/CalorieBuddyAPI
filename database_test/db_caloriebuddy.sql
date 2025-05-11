-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2025 at 05:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_caloriebuddy`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL COMMENT 'ไอดีกิจกรรม',
  `user_id` int(11) NOT NULL COMMENT 'ไอดีผู้ใช้',
  `workout_id` int(11) NOT NULL COMMENT 'ไอดีท่าออกกำลังกาย',
  `user_workout_id` int(11) NOT NULL COMMENT 'ไอดีท่าออกกำลังกายโปรดของผู้ใช้',
  `sets` int(11) NOT NULL COMMENT 'จำนวนเซ็ต',
  `reps` int(11) NOT NULL COMMENT 'จำนวนครั้งต่อเซ็ต',
  `weight` int(11) NOT NULL COMMENT 'น้ำหนักที่ใช้ (kg)',
  `duration` int(11) NOT NULL COMMENT 'ระยะเวลาที่ทำ (นาที) เช่น การเดิน การวิ่งต้องใส่ (อาจเป็น null ได้)',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่บันทึกข้อมูล',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่อัปเดตล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `foods`
--

CREATE TABLE `foods` (
  `id` int(11) NOT NULL COMMENT 'ไอดีอาหาร',
  `name` varchar(200) NOT NULL COMMENT 'ชื่ออาหาร',
  `calories` int(11) NOT NULL COMMENT 'แคลลอรี่',
  `protien` int(11) NOT NULL COMMENT 'โปรตีน',
  `carb` int(11) NOT NULL COMMENT 'คาร์โบไฮเดรต',
  `fat` int(11) NOT NULL COMMENT 'ไขมัน',
  `food_category` varchar(20) NOT NULL COMMENT 'หมวดหมู่อาหาร',
  `image` varchar(200) NOT NULL COMMENT 'ชื่อรูปอาหาร',
  `create_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่เพิ่มข้อมูล',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'อัพเดตล่าสุดวันไหน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL COMMENT 'ไอดีการแจ้งเตือน',
  `user_id` int(11) NOT NULL COMMENT 'ไอดีผู้ใช้',
  `type` varchar(20) NOT NULL COMMENT 'ประเภทการแจ้งเตือน',
  `message` varchar(255) NOT NULL COMMENT 'ข้อความแจ้งเตือน',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างการแจ้งเตือน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `display_name` varchar(100) NOT NULL COMMENT 'ชื่อแสดงผล',
  `accName` varchar(100) NOT NULL COMMENT 'ชื่อบัญชี',
  `password_hash` varchar(255) NOT NULL COMMENT 'รหัสผ่าน',
  `email` varchar(64) NOT NULL COMMENT 'อีเมล',
  `gender` varchar(6) NOT NULL COMMENT 'เพศ',
  `age` int(11) NOT NULL COMMENT 'อายุ',
  `height` int(11) NOT NULL COMMENT 'ส่วนสูง',
  `user_weight` float NOT NULL COMMENT 'น้ำหนัก',
  `profile_img` varchar(200) NOT NULL COMMENT 'ชื่อรูปโปรไฟล์',
  `activity_level` varchar(100) NOT NULL COMMENT 'ระดับกิจกรรม (น้อย, ปานกลาง, มาก)',
  `goal` varchar(100) NOT NULL COMMENT 'ป้าหมาย (ลดน้ำหนัก, เพิ่มน้ำหนัก, รักษาน้ำหนัก)',
  `daily_calorie_target` int(11) NOT NULL COMMENT 'เป้าหมายแคลอรี่ต่อวัน',
  `is_active` varchar(5) NOT NULL DEFAULT 'true',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'สร้างบัญชีตอนไหน',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'อัพเดตบัญชีล่าสุดตอนไหน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `display_name`, `accName`, `password_hash`, `email`, `gender`, `age`, `height`, `user_weight`, `profile_img`, `activity_level`, `goal`, `daily_calorie_target`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'จาตุรนต์', 'test1', '$2y$10$tO.jrkxp4r1mrXqOtkz4qu7LvvkWHv3/MWi470dENPZ4yAtGzMesq', 'test@hot.com', 'male', 25, 167, 60, '1234.jpg', 'min', 'เพิ่มกล้ามเนื้อ', 0, 'false', '2025-05-10 19:18:37', '2025-05-11 09:59:12');

-- --------------------------------------------------------

--
-- Table structure for table `users_eated`
--

CREATE TABLE `users_eated` (
  `id` int(11) NOT NULL COMMENT 'ไอดีอาหารที่กินไป',
  `user_id` int(11) NOT NULL COMMENT 'ไอดีผู้ใช้',
  `food_id` int(11) NOT NULL COMMENT 'ไอดีอาหาร',
  `create_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่เพิ่มข้อมูล',
  `meal_type` varchar(7) NOT NULL COMMENT 'ประเภทมื้ออาหาร (เช้า, กลางวัน, เย็น, ของว่าง)',
  `quantity` int(11) NOT NULL COMMENT 'จำนวน',
  `notes` int(11) NOT NULL COMMENT 'บันทึกเพิ่มเติม',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'อัพเดตล่าสุดวันไหน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_fav`
--

CREATE TABLE `users_fav` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'ไอดีผู้ใช้',
  `food_id` int(11) NOT NULL COMMENT 'ไอดีอาหาร',
  `create_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่เพิ่มข้อมูล'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_workout_fav`
--

CREATE TABLE `users_workout_fav` (
  `id` int(11) NOT NULL COMMENT 'ไอดีท่าออกกำลังกายของผู้ใช้',
  `user_id` int(11) NOT NULL COMMENT 'ไอดีผู้ใช้',
  `workout_id` int(11) NOT NULL COMMENT 'ไอดีท่าออกกำลังกาย',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่เพิ่มท่านี้เข้าคลังท่าของผู้ใช้',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่อัปเดตล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workout`
--

CREATE TABLE `workout` (
  `id` int(11) NOT NULL COMMENT 'ไอดีท่าออกกำลังกาย',
  `name` varchar(250) NOT NULL COMMENT 'ชื่อท่าการออกกำลังกาย',
  `detail` text NOT NULL COMMENT 'รายละเอียดท่าออกกำลังกาย',
  `muscle` varchar(50) NOT NULL COMMENT 'กล้ามเนื้อส่วนไหนที่โดน',
  `calorie_burn` int(11) NOT NULL COMMENT 'เผาผลาญแคลอรี่ (อาจเป็น null ในท่า body weight)',
  `difficulty` varchar(12) NOT NULL COMMENT 'ระดับความยาก (เช่น beginner, intermediate, advanced)',
  `equipment` varchar(30) NOT NULL COMMENT 'อุปกรณ์ที่ต้องใช้ (เช่น dumbbells, barbell, bodyweight)',
  `image` varchar(255) NOT NULL COMMENT 'ชื่อรูปภาพสาธิตท่า',
  `video` varchar(100) NOT NULL COMMENT 'ชื่อวิดีโอสาธิตท่า',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'เพิ่มเมื่อวันที่',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'อัพเดตล่าสุดวันไหน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `foods`
--
ALTER TABLE `foods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_eated`
--
ALTER TABLE `users_eated`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_fav`
--
ALTER TABLE `users_fav`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_workout_fav`
--
ALTER TABLE `users_workout_fav`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workout`
--
ALTER TABLE `workout`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ไอดีกิจกรรม';

--
-- AUTO_INCREMENT for table `foods`
--
ALTER TABLE `foods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ไอดีอาหาร';

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ไอดีการแจ้งเตือน';

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users_eated`
--
ALTER TABLE `users_eated`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ไอดีอาหารที่กินไป';

--
-- AUTO_INCREMENT for table `users_fav`
--
ALTER TABLE `users_fav`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_workout_fav`
--
ALTER TABLE `users_workout_fav`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ไอดีท่าออกกำลังกายของผู้ใช้';

--
-- AUTO_INCREMENT for table `workout`
--
ALTER TABLE `workout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ไอดีท่าออกกำลังกาย';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
