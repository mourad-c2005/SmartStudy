-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 12 déc. 2025 à 22:44
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `smartstudy`
--

-- --------------------------------------------------------

--
-- Structure de la table `forums`
--

CREATE TABLE `forums` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `views` int(11) DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `profile`
--

CREATE TABLE `profile` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `text` text DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `etablissement` varchar(100) DEFAULT NULL,
  `niveau` varchar(50) DEFAULT NULL,
  `twitter` varchar(50) DEFAULT NULL,
  `linkedin` varchar(50) DEFAULT NULL,
  `github` varchar(50) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `img_per` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `profile`
--

INSERT INTO `profile` (`id`, `nom`, `email`, `text`, `date_naissance`, `etablissement`, `niveau`, `twitter`, `linkedin`, `github`, `date_creation`, `img_per`) VALUES
(23, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-22', NULL),
(28, 'qfqsfq', 'adminqdqdqdqsdqsdqdqsdqs@smartstudy.com', 'je suis un etudiant', '2001-04-18', 'universite', '5eme', '', '', '', '2025-11-22', NULL),
(35, 'qsxqxq', 'adminaaaaa@smartstudy.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-28', NULL),
(36, 'amine', 'amine@smartstudy.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-28', NULL),
(37, 'cqscqcqc', 'n@smartstudy.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-28', NULL),
(38, 'ayoub', 'ayoubcherif18042005@gmail.com', '', '0000-00-00', '', '', '', '', '', '2025-11-29', 'pic/profile_38_1765039540.jpg'),
(39, 'labib', 'smartstudyemail@gmail.com', NULL, '2005-04-18', NULL, NULL, NULL, NULL, NULL, '2025-11-30', NULL),
(42, 'labib nibrase', 'smartstudyemail@gmail.com', '', '2001-04-18', 'universite de lac', '', '', '', '', '2025-12-06', NULL),
(43, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-12', NULL),
(44, '', '', '', NULL, NULL, NULL, '', '', '', '2025-12-07', 'pic/profile_44_1765129959.png'),
(48, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-08', NULL),
(49, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-08', NULL),
(50, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-08', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `rapport`
--

CREATE TABLE `rapport` (
  `id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `titre` varchar(250) NOT NULL,
  `message` text NOT NULL,
  `vu` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` date NOT NULL,
  `pin` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rapport`
--

INSERT INTO `rapport` (`id`, `email`, `titre`, `message`, `vu`, `created_at`, `pin`) VALUES
(0, 'admin@smartstudy.com', 'j ai un probleme du connection', 'qdq^dqiuoBFIUbncfIjbC', 0, '2025-11-28', 0),
(0, 'admin@smartstudy.com', 'j ai un probleme du connection', 'qdq^dqiuoBFIUbncfIjbC', 0, '2025-11-28', 0),
(0, 'admin@smartstudy.com', 'j ai un probleme du connection', 'qdq^dqiuoBFIUbncfIjbC', 0, '2025-11-28', 0),
(0, 'admin@smartstudy.com', 'ssxQWS', 'XxQXxQXqsSsS', 0, '2025-11-28', 0),
(0, 'admin@smartstudy.com', 'j ai un probleme du connection', 'CDSFSJFNMoqfn%', 0, '2025-12-01', 0),
(0, 'admin@smartstudy.com', 'j ai un probleme du connection', 'NIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGANIGA', 0, '2025-12-02', 0);

-- --------------------------------------------------------

--
-- Structure de la table `replies`
--

CREATE TABLE `replies` (
  `id` int(11) NOT NULL,
  `forum_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `author` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_solution` tinyint(1) DEFAULT 0,
  `likes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `reply_id` int(11) NOT NULL,
  `reporter_name` varchar(100) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','reviewed','dismissed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('etudiant','professeur','admin') DEFAULT 'etudiant',
  `date_naissance` date DEFAULT NULL,
  `etablissement` varchar(100) DEFAULT NULL,
  `niveau` varchar(50) DEFAULT NULL,
  `twitter` varchar(150) DEFAULT NULL,
  `linkedin` varchar(150) DEFAULT NULL,
  `github` varchar(150) DEFAULT NULL,
  `date_creation` date DEFAULT curdate(),
  `autorisation` tinyint(1) NOT NULL DEFAULT 1,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `remember_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `role`, `date_naissance`, `etablissement`, `niveau`, `twitter`, `linkedin`, `github`, `date_creation`, `autorisation`, `reset_token_hash`, `reset_token_expires_at`, `remember_token`) VALUES
(18, 'eya', 'cwxcwxc', '$2y$10$MWb1bwCufu.Crc4h4P4XF.qAJuVG2wYmylO.cD24Csv0h/LlIYNrK', 'etudiant', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-20', 1, NULL, NULL, NULL),
(22, 'admin@smartstudy.com', 'qsffsqqffs@gamil.com', '$2y$10$LIpyAbE0aHkMTPl70dslmuCbgcI2Q8Kb.lTZudNdLVPbC.jJYC/nq', 'etudiant', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-20', 1, NULL, NULL, NULL),
(24, 'sasa', 'ayoubcherif15@gamil.com', '$2y$10$Kfod01fTpcRgeuOdE2Av9eKKNvLKvGHLf1/NspewOsrUcfuJDaPVK', 'etudiant', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-21', 1, NULL, NULL, NULL),
(25, 'dqsdqsd', 'admidqqsdn@smartstudy.com', '$2y$10$RdIBx8GAaPK2XhUWBSrZ3efGO2BOk.t4lS5jCRw9ODsM63uRqngGm', 'professeur', '2001-04-18', 'universite', '5eme', NULL, NULL, NULL, '2025-11-21', 1, NULL, NULL, NULL),
(27, 'qfqsfq', 'adminqdqdqd@smartstudy.com', '$2y$10$F8zac8AwGLGO6z3A8Qnpbu4OMKnUZko9uRvfyNCb/U1FC8JRzsNau', 'professeur', '2001-04-18', 'universite', '5eme', NULL, NULL, NULL, '2025-11-21', 1, NULL, NULL, NULL),
(28, 'qfqsfq', 'adminqdqdqdqsdqsdqdqsdqs@smartstudy.com', '$2y$10$PNzpbJtqjjUya5BNExjxmORSNl3XtQvAP1njyfjDaGGYnSnfcoeg.', NULL, '2001-04-18', 'universite', '5eme', NULL, NULL, NULL, '2025-11-21', 1, NULL, NULL, NULL),
(29, 'ayoub', 'admindqdqsd@smartstudy.com', '$2y$10$Qvk3AfA5qjxBuiiSzJCiU.ph4vd7JG/QvZy7CspvnSWMhK1U8f.RS', 'etudiant', '2001-04-18', 'ecole', '2eme', NULL, NULL, NULL, '2025-11-21', 1, NULL, NULL, NULL),
(30, 'samir', 'ayoubcherif@gamil.com', '$2y$10$FWY478RIHvEhXA/ji4toVO0c8s.iXLFuL0WPf3uZunaG1laEFhmfi', 'etudiant', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-23', 1, NULL, NULL, NULL),
(31, 'samir', 'alo@smartstudy.com', '$2y$10$1cEnsgcbYO.8CFJ8Qfc0ve/SSJ1PajCId5i53gRiQSZ.gHCaTtxJO', 'etudiant', '2001-04-18', 'ecole', '5eme', NULL, NULL, NULL, '2025-11-24', 1, NULL, NULL, NULL),
(32, 'eadqcqc', 'admidqsqscqn@smartstudy.com', '$2y$10$0asoPgJQarCpz9oVeLdpCuFHPQpamfNTt931.bFqb1M/d0wf9M2Za', 'etudiant', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-27', 1, NULL, NULL, NULL),
(33, 'zdqxcq', 'adminxqscqc@smartstudy.com', '$2y$10$TMF8kOu4apAJ9O2NgjHzY.89JuMozBRzIhN0Uw7dntX1jUjBiPAQm', 'etudiant', NULL, 'lycee', '2eme', NULL, NULL, NULL, '2025-11-28', 1, NULL, NULL, NULL),
(34, 'cqscqcq', 'admincqscqcqscqc@smartstudy.com', '$2y$10$HquBRf7q5etdGMiQGV9HJeRS1b8JYRYkiZ4TZjFavtO0AufJE1QMy', 'etudiant', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-28', 1, NULL, NULL, NULL),
(36, 'amine', 'amine@smartstudy.com', '$2y$10$iYlJqS9NKNgC.MkZyHJi9upTmECPDk35Ny6ynKA/45WJs.t9BkkES', 'etudiant', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-28', 1, NULL, NULL, NULL),
(43, 'labib', 'smartstudyemail@gmail.com', '$2y$10$0bQv37CN86g6Qe1HF5XRg.0Ne2dxYPMaijq.Q6X/L7DfBLnWIlnvm', 'etudiant', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-06', 1, NULL, NULL, NULL),
(44, 'admin', 'admin@smartstudy.com', '$2y$10$K/3o9wm9IXbT.ypZO9zRU..O9XHF7BY9EbEFGoF1qmqXoVbckGZz2', 'admin', '2000-04-18', 'sxqq', NULL, NULL, NULL, NULL, '2025-12-06', 1, NULL, NULL, NULL),
(50, 'Marie Curie', 'ayoubcherif18042005@gmail.com', '$2y$10$WcdUkIjSQVDCTvtKO8SRzeXRu0lxrNOjroCnm7h3f8xQ6Tzv7J48i', 'etudiant', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-08', 1, NULL, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `forums`
--
ALTER TABLE `forums`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `profile`
--
ALTER TABLE `profile`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `forum_id` (`forum_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Index pour la table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reply_id` (`reply_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `reset_token_hash` (`reset_token_hash`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `forums`
--
ALTER TABLE `forums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `replies`
--
ALTER TABLE `replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `replies_parent_fk` FOREIGN KEY (`parent_id`) REFERENCES `replies` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reply_id`) REFERENCES `replies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
