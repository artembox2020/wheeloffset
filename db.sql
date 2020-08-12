RENAME TABLE `product_bulbs` TO `donor_product_bulb`;
-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Час створення: Вер 03 2019 р., 19:30
-- Версія сервера: 5.5.61
-- Версія PHP: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `auto_model_year_bulb` (
  `model_year_id` int(11) UNSIGNED NOT NULL,
  `bulb_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `product_bulb` (
  `id` int(11) UNSIGNED NOT NULL,
  `app` varchar(100) NOT NULL,
  `part` varchar(20) NOT NULL,
  `alias` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `auto_model_year_bulb`
  ADD PRIMARY KEY (`model_year_id`,`bulb_id`),
  ADD KEY `bulb_id` (`bulb_id`);
ALTER TABLE `product_bulb`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alias` (`alias`);
ALTER TABLE `product_bulb`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `auto_model_year_bulb`
  ADD CONSTRAINT `auto_model_year_bulb_ibfk_2` FOREIGN KEY (`bulb_id`) REFERENCES `product_bulb` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `auto_model_year_bulb_ibfk_1` FOREIGN KEY (`model_year_id`) REFERENCES `auto_model_year` (`id`) ON DELETE CASCADE;
COMMIT;


INSERT INTO `settings` (`key`, `value`) VALUES
('product_bulb_seo_header_text', ''),
('product_bulb_seo_footer_text', ''),
('product_bulb_meta_description', ''),
('product_bulb_meta_title', ''),
('product_bulb_meta_keywords', ''),

('product_bulb_type_seo_header_text', ''),
('product_bulb_type_seo_footer_text', ''),
('product_bulb_type_meta_description', ''),
('product_bulb_type_meta_title', ''),
('product_bulb_type_meta_keywords', ''),

('product_bulb_make_seo_header_text', '[make]'),
('product_bulb_make_seo_footer_text', '[make]'),
('product_bulb_make_meta_description', '[make]'),
('product_bulb_make_meta_title', '[make]'),
('product_bulb_make_meta_keywords', '[make]'),

('product_bulb_make_model_seo_header_text', '[make], [model]'),
('product_bulb_make_model_seo_footer_text', '[make], [model]'),
('product_bulb_make_model_meta_description', '[make], [model]'),
('product_bulb_make_model_meta_title', '[make], [model]'),
('product_bulb_make_model_meta_keywords', '[make], [model]'),

('product_bulb_make_model_year_seo_header_text', '[make], [model], [year]'),
('product_bulb_make_model_year_seo_footer_text', '[make], [model], [year]'),
('product_bulb_make_model_year_meta_description', '[make], [model], [year]'),
('product_bulb_make_model_year_meta_title', '[make], [model], [year]'),
('product_bulb_make_model_year_meta_keywords', '[make], [model], [year]');
