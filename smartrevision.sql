-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 13 nov. 2025 à 18:43
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
-- Base de données : `smartrevision`
--

-- --------------------------------------------------------

--
-- Structure de la table `chapitres`
--

CREATE TABLE `chapitres` (
  `id` int(11) NOT NULL,
  `id_matiere` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `chapitres`
--

INSERT INTO `chapitres` (`id`, `id_matiere`, `titre`) VALUES
(1, 1, 'Algèbre - Équations'),
(2, 1, 'Analyse - Dérivées'),
(3, 2, 'Mécanique - Lois du mouvement');

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
  `id` int(11) NOT NULL,
  `id_chapitre` int(11) NOT NULL,
  `titre` varchar(200) DEFAULT NULL,
  `contenu` text DEFAULT NULL,
  `lien_video` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`id`, `id_chapitre`, `titre`, `contenu`, `lien_video`) VALUES
(1, 1, 'Équations du 1er degré', 'Contenu: résolutions, exemples.', 'https://www.youtube.com/embed/dQw4w9WgXcQ'),
(2, 2, 'Dérivées - Introduction', 'Contenu: notion de limite et dérivée.', ''),
(3, 3, 'Newton & Co', 'Contenu: lois de Newton et exemples.', '');

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

CREATE TABLE `matieres` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id`, `nom`, `description`) VALUES
(1, 'Mathématiques', 'Cours de base: algèbre et analyse'),
(2, 'Physique', 'Mécanique et électromagnétisme');

-- --------------------------------------------------------

--
-- Structure de la table `quiz`
--

CREATE TABLE `quiz` (
  `id` int(11) NOT NULL,
  `id_chapitre` int(11) NOT NULL,
  `question` text DEFAULT NULL,
  `rep1` varchar(200) DEFAULT NULL,
  `rep2` varchar(200) DEFAULT NULL,
  `rep3` varchar(200) DEFAULT NULL,
  `rep4` varchar(200) DEFAULT NULL,
  `correcte` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `quiz`
--

INSERT INTO `quiz` (`id`, `id_chapitre`, `question`, `rep1`, `rep2`, `rep3`, `rep4`, `correcte`) VALUES
(1, 1, '2 + 2 = ?', '3', '4', '5', '6', 1),
(2, 1, '5 * 6 = ?', '30', '35', '26', '25', 0),
(3, 1, '10 - 4 = ?', '7', '5', '6', '4', 2),
(4, 1, '7 + 8 = ?', '14', '15', '16', '13', 1),
(5, 1, '9 / 3 = ?', '2', '3', '4', '1', 1),
(6, 1, 'Résoudre: x+3=7 → x = ?', '4', '3', '5', '2', 0),
(7, 1, 'Résoudre: 2x=8 → x = ?', '2', '3', '4', '5', 2),
(8, 1, 'Quel est le produit 4*7 ?', '21', '24', '28', '32', 2),
(9, 1, 'Somme de 12 et 13 ?', '24', '25', '26', '23', 1),
(10, 1, '8+5-3 = ?', '10', '11', '9', '8', 1),
(11, 1, '3^2 = ?', '6', '9', '8', '5', 1),
(12, 1, '√16 = ?', '2', '4', '8', '6', 1),
(13, 1, 'Quel est 100/25 ?', '2', '4', '5', '10', 2),
(14, 1, '5+5+5 = ?', '10', '15', '20', '12', 1),
(15, 1, '6*6 = ?', '36', '30', '42', '32', 0),
(16, 1, '15-7 = ?', '7', '8', '9', '6', 1),
(17, 1, '14/2 = ?', '6', '7', '8', '10', 1),
(18, 1, '3*5 = ?', '15', '10', '8', '12', 0),
(19, 1, '11+9 = ?', '19', '20', '21', '18', 0),
(20, 1, '20 - 4 = ?', '16', '15', '14', '18', 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `chapitres`
--
ALTER TABLE `chapitres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_matiere` (`id_matiere`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_chapitre` (`id_chapitre`);

--
-- Index pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_chapitre` (`id_chapitre`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `chapitres`
--
ALTER TABLE `chapitres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `matieres`
--
ALTER TABLE `matieres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `chapitres`
--
ALTER TABLE `chapitres`
  ADD CONSTRAINT `chapitres_ibfk_1` FOREIGN KEY (`id_matiere`) REFERENCES `matieres` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `cours`
--
ALTER TABLE `cours`
  ADD CONSTRAINT `cours_ibfk_1` FOREIGN KEY (`id_chapitre`) REFERENCES `chapitres` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`id_chapitre`) REFERENCES `chapitres` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
