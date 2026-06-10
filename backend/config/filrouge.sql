-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Version du serveur : 10.4.32-MariaDB / MySQL 8.0
--
-- Dump complet de la base `filrouge` (5 tables : assets, audit_logs,
-- password_resets, tickets, users). La table `assets`, absente du dump
-- d'origine, a été ajoutée pour que la restauration recrée l'intégralité
-- du schéma attendu par l'application.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `filrouge`
--

-- --------------------------------------------------------

--
-- Structure de la table `assets`
--

CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `name` varchar(190) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `marque` varchar(100) DEFAULT NULL,
  `modele` varchar(100) DEFAULT NULL,
  `serial_number` varchar(190) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `mac_address` varchar(17) DEFAULT NULL,
  `statut` varchar(50) DEFAULT 'en_service',
  `assigned_to` int(11) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(190) NOT NULL,
  `entity` varchar(100) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `ip`, `user_agent`, `created_at`) VALUES
(1, 1, 'update_status', 'ticket', 4, '::1', 'Mozilla/5.0', '2026-03-26 11:00:35'),
(2, 1, 'update_status', 'ticket', 2, '::1', 'Mozilla/5.0', '2026-03-26 11:00:49'),
(3, 1, 'update_status', 'ticket', 3, '::1', 'Mozilla/5.0', '2026-03-26 11:01:04'),
(4, NULL, 'create', 'ticket', 5, '::1', 'Mozilla/5.0', '2026-03-26 11:01:33'),
(5, 1, 'update_status', 'ticket', 5, '::1', 'Mozilla/5.0', '2026-03-26 11:02:00'),
(6, 1, 'delete', 'ticket', 4, '::1', 'Mozilla/5.0', '2026-03-26 11:36:42'),
(7, NULL, 'create', 'ticket', 6, '::1', 'Mozilla/5.0', '2026-03-26 11:37:12'),
(8, NULL, 'create', 'ticket', 7, '::1', 'Mozilla/5.0', '2026-03-26 11:37:21'),
(9, 1, 'delete', 'ticket', 6, '::1', 'Mozilla/5.0', '2026-03-26 11:37:41'),
(10, 1, 'update_status', 'ticket', 7, '::1', 'Mozilla/5.0', '2026-03-26 11:37:44'),
(11, 1, 'delete', 'ticket', 7, '::1', 'Mozilla/5.0', '2026-03-26 11:37:48'),
(12, NULL, 'register', 'user', NULL, '::1', 'Mozilla/5.0', '2026-03-26 11:38:29'),
(22, 1, 'login', 'user', 1, '::1', 'Mozilla/5.0', '2026-03-26 11:51:35'),
(23, 1, 'delete', 'user', 6, '::1', 'Mozilla/5.0', '2026-03-26 11:51:42'),
(24, 1, 'logout', 'user', 1, '::1', 'Mozilla/5.0', '2026-03-26 11:52:01'),
(28, 1, 'login', 'user', 1, '::1', 'Mozilla/5.0', '2026-03-26 11:52:56'),
(39, 1, 'update_status', 'ticket', 8, '::1', 'Mozilla/5.0', '2026-04-22 18:18:35'),
(41, 1, 'update', 'ticket', 8, '::1', 'Mozilla/5.0', '2026-04-22 18:18:47'),
(42, 1, 'delete', 'ticket', 8, '::1', 'Mozilla/5.0', '2026-04-22 18:18:53'),
(45, 1, 'update_status', 'ticket', 5, '::1', 'Mozilla/5.0', '2026-04-22 18:25:26'),
(52, 1, 'update_status', 'ticket', 9, '::1', 'Mozilla/5.0', '2026-04-22 19:00:00'),
(57, NULL, 'create', 'ticket', 10, '::1', 'Mozilla/5.0', '2026-04-22 20:05:09'),
(59, NULL, 'register', 'user', 7, '::1', 'Mozilla/5.0', '2026-04-22 20:18:25'),
(81, 1, 'delete', 'user', 5, '::1', 'Mozilla/5.0', '2026-04-22 20:48:40'),
(92, 1, 'refuse_access_request', 'access_request', 1, '::1', 'Mozilla/5.0', '2026-04-27 11:29:44'),
(95, NULL, 'create', 'ticket', 11, '::1', 'Mozilla/5.0', '2026-05-06 15:41:42'),
(96, NULL, 'create', 'ticket', 12, '::1', 'Mozilla/5.0', '2026-05-14 22:16:14'),
(99, 1, 'delete', 'ticket', 11, '::1', 'Mozilla/5.0', '2026-05-15 02:53:44');

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(190) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `status` enum('en_cours','traitee','refusee') NOT NULL DEFAULT 'en_cours',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tickets`
--

INSERT INTO `tickets` (`id`, `user_id`, `title`, `description`, `priority`, `status`, `created_at`, `updated_at`) VALUES
(2, 1, 'r', 'e', 'medium', 'refusee', '2026-03-11 15:33:00', '2026-04-22 19:00:11'),
(3, 1, 'saf', '4', 'high', 'traitee', '2026-03-16 16:11:45', '2026-04-22 19:00:04'),
(13, 15, 'd', 'd', 'medium', 'en_cours', '2026-05-15 03:02:36', NULL),
(14, 18, 'x', 'x', 'medium', 'en_cours', '2026-05-15 03:17:13', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(190) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `must_change_password` tinyint(1) DEFAULT 1,
  `role` varchar(50) DEFAULT 'user',
  `reset_token` varchar(255) DEFAULT NULL,
  `two_factor_code` varchar(10) DEFAULT NULL,
  `two_factor_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `full_name`, `is_active`, `created_at`, `must_change_password`, `role`, `reset_token`, `two_factor_code`, `two_factor_expires`) VALUES
(1, 'safaazemmar@gmail.com', '$2y$10$VYCE5IsNLZT4l7V.vXKNt.vxHvp9vprMrQArouLro0nYixWBeo9Oe', 'Admin', 1, '2026-03-11 13:52:57', 0, 'admin', NULL, NULL, NULL),
(15, 'safouzemmar@gmail.com', '$2y$10$DUkCuFTnvO4RZWprHvEmXug8F5uSMow45qaeJBEKvWyp1l37MJITW', 'Safaa Zemmar', 1, '2026-05-14 22:34:31', 0, 'user', NULL, NULL, NULL),
(18, 'sef54094@gmail.com', '$2y$10$4eQBjp6YBMqNgSkYUOdP..TNGvIiXRQPr2q6ZGZ7JiPBWF/Vn3LX6', 'Safaa Zemmar', 1, '2026-05-15 03:15:06', 0, 'user', NULL, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Index pour la table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT pour la table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `assets`
--
ALTER TABLE `assets`
  ADD CONSTRAINT `assets_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
