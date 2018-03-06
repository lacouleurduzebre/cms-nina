-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  mar. 06 mars 2018 à 15:50
-- Version du serveur :  5.7.17
-- Version de PHP :  7.1.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `ninasf4`
--

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`) VALUES
('20180228102732'),
('20180228103105');

-- --------------------------------------------------------

--
-- Structure de la table `nina_categorie`
--

CREATE TABLE `nina_categorie` (
  `id` int(11) NOT NULL,
  `type_categorie_id` int(11) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `categorieParent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `nina_categorie`
--

INSERT INTO `nina_categorie` (`id`, `type_categorie_id`, `nom`, `description`, `url`, `categorieParent`) VALUES
(1, 1, 'exemple', 'page d\'exemple', 'exemple', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `nina_champ`
--

CREATE TABLE `nina_champ` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `nina_champ`
--

INSERT INTO `nina_champ` (`id`, `nom`, `type`) VALUES
(1, 'source', 'url'),
(2, 'description', 'text'),
(3, 'texte', 'textarea');

-- --------------------------------------------------------

--
-- Structure de la table `nina_commentaire`
--

CREATE TABLE `nina_commentaire` (
  `id` int(11) NOT NULL,
  `auteur` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `site` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `contenu` longtext COLLATE utf8_unicode_ci NOT NULL,
  `corbeille` tinyint(1) NOT NULL,
  `valide` tinyint(1) NOT NULL,
  `idPage` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `nina_commentaire`
--

INSERT INTO `nina_commentaire` (`id`, `auteur`, `email`, `site`, `date`, `contenu`, `corbeille`, `valide`, `idPage`) VALUES
(1, 'nadege', 'nadege@lacouleurduzebre.com', NULL, '2018-03-01', '<p>blabla commentaire 1</p>', 0, 1, 1),
(2, 'nadege', 'nadege@lacouleurduzebre.com', NULL, '2018-03-01', '<p>blabla commentaire 2</p>', 0, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `nina_configuration`
--

CREATE TABLE `nina_configuration` (
  `id` int(11) NOT NULL,
  `langue_defaut_id` int(11) DEFAULT NULL,
  `url` longtext COLLATE utf8_unicode_ci NOT NULL,
  `logo` longtext COLLATE utf8_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `emailContact` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `emailMaintenance` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `emailNewsletter` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `analytics` longtext COLLATE utf8_unicode_ci NOT NULL,
  `editeur` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `nina_configuration`
--

INSERT INTO `nina_configuration` (`id`, `langue_defaut_id`, `url`, `logo`, `nom`, `emailContact`, `emailMaintenance`, `emailNewsletter`, `analytics`, `editeur`) VALUES
(1, 1, 'http://127.0.0.1/NinaSF4/public/index.php', '../assets/img/front/logoNina.png', 'CMS Nina 3.0', 'contact@lacouleurduzebre.com', 'contact@lacouleurduzebre.com', 'contact@lacouleurduzebre.com', 'code google ici', 'la couleur du Zèbre');

-- --------------------------------------------------------

--
-- Structure de la table `nina_langue`
--

CREATE TABLE `nina_langue` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `abreviation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `nina_langue`
--

INSERT INTO `nina_langue` (`id`, `nom`, `abreviation`, `active`) VALUES
(1, 'Français', 'fr', 1),
(2, 'Anglais', 'en', 1),
(3, 'Japonais', 'jp', 1);

-- --------------------------------------------------------

--
-- Structure de la table `nina_menu`
--

CREATE TABLE `nina_menu` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `region` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nina_menu_page`
--

CREATE TABLE `nina_menu_page` (
  `id` int(11) NOT NULL,
  `page_parent_id` int(11) DEFAULT NULL,
  `page_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `position` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nina_module`
--

CREATE TABLE `nina_module` (
  `id` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  `contenu` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nina_page`
--

CREATE TABLE `nina_page` (
  `id` int(11) NOT NULL,
  `auteur_id` int(11) DEFAULT NULL,
  `auteur_derniere_modification_id` int(11) DEFAULT NULL,
  `page_parent_id` int(11) DEFAULT NULL,
  `page_originale_id` int(11) DEFAULT NULL,
  `langue_id` int(11) DEFAULT NULL,
  `seo_id` int(11) DEFAULT NULL,
  `titre` longtext COLLATE utf8_unicode_ci NOT NULL,
  `contenu` longtext COLLATE utf8_unicode_ci NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_publication` datetime NOT NULL,
  `date_depublication` datetime DEFAULT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `corbeille` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `nina_page`
--

INSERT INTO `nina_page` (`id`, `auteur_id`, `auteur_derniere_modification_id`, `page_parent_id`, `page_originale_id`, `langue_id`, `seo_id`, `titre`, `contenu`, `date_creation`, `date_publication`, `date_depublication`, `image`, `position`, `active`, `corbeille`) VALUES
(1, 1, 1, NULL, NULL, 1, 3, 'Première page', '<p>blablabla contenu</p>', '2018-02-28 14:03:26', '2013-01-28 15:00:00', NULL, NULL, 0, 1, 0),
(2, 1, 1, 1, NULL, 1, 5, 'Deuxième page bis', '<p>blablablabla</p>\r\n<p><img src=\"../../../../assets/uploads/WP_20170316_07_53_47_Pro_LI.jpg?1520329232148\" alt=\"WP_20170316_07_53_47_Pro_LI\" /></p>', '2018-02-28 15:38:10', '2018-01-28 00:00:00', NULL, NULL, 0, 1, 0),
(5, 1, 1, 1, NULL, 1, 7, 'Deuxième page bis', '<p>blablablabla</p>\r\n<p><img src=\"../../../../assets/uploads/WP_20170316_07_53_47_Pro_LI.jpg?1520329232148\" alt=\"WP_20170316_07_53_47_Pro_LI\" /></p>', '2018-02-28 15:38:10', '2018-01-28 00:00:00', NULL, NULL, 0, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `nina_page_categorie`
--

CREATE TABLE `nina_page_categorie` (
  `nina_page_id` int(11) NOT NULL,
  `nina_categorie_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `nina_page_categorie`
--

INSERT INTO `nina_page_categorie` (`nina_page_id`, `nina_categorie_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `nina_page_module`
--

CREATE TABLE `nina_page_module` (
  `nina_page_id` int(11) NOT NULL,
  `nina_module_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `nina_seo`
--

CREATE TABLE `nina_seo` (
  `id` int(11) NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `metaTitre` longtext COLLATE utf8_unicode_ci NOT NULL,
  `metaDescription` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `nina_seo`
--

INSERT INTO `nina_seo` (`id`, `url`, `metaTitre`, `metaDescription`) VALUES
(3, 'premiere-page', 'Première page', '<p>blablabla contenu</p>'),
(5, 'deuxieme-page', 'Deuxième page', '<p>deuxi&egrave;me page blabla</p>'),
(7, 'deuxieme-page_copie', 'Deuxième page', '<p>deuxi&egrave;me page blabla</p>');

-- --------------------------------------------------------

--
-- Structure de la table `nina_type_categorie`
--

CREATE TABLE `nina_type_categorie` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `nina_type_categorie`
--

INSERT INTO `nina_type_categorie` (`id`, `nom`, `description`, `url`) VALUES
(1, 'Tag', 'tags associés aux pages', 'tag');

-- --------------------------------------------------------

--
-- Structure de la table `nina_type_module`
--

CREATE TABLE `nina_type_module` (
  `id` int(11) NOT NULL,
  `modules_id` int(11) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `nina_type_module`
--

INSERT INTO `nina_type_module` (`id`, `modules_id`, `nom`) VALUES
(1, NULL, 'Image'),
(2, NULL, 'Texte');

-- --------------------------------------------------------

--
-- Structure de la table `nina_type_module_champ`
--

CREATE TABLE `nina_type_module_champ` (
  `nina_type_module_id` int(11) NOT NULL,
  `nina_champ_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `nina_type_module_champ`
--

INSERT INTO `nina_type_module_champ` (`nina_type_module_id`, `nina_champ_id`) VALUES
(1, 1),
(1, 2),
(2, 3);

-- --------------------------------------------------------

--
-- Structure de la table `nina_utilisateur`
--

CREATE TABLE `nina_utilisateur` (
  `id` int(11) NOT NULL,
  `username` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `confirmation_token` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `nina_utilisateur`
--

INSERT INTO `nina_utilisateur` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `confirmation_token`, `password_requested_at`, `roles`) VALUES
(1, 'nadege', 'nadege', 'nadege@lacouleurduzebre.com', 'nadege@lacouleurduzebre.com', 1, NULL, '$2y$13$4rABh42aH/Oj8g.TmSni0ug/gz2ZZyCYbcPLA9gXrI4b.iYFK26tG', '2018-03-06 09:38:37', 'oX96gkFolLZQ9RQzhTJA39F9WQZi3jA1MfyWRy9sgF4', '2018-03-06 09:35:24', 'a:0:{}');

-- --------------------------------------------------------

--
-- Structure de la table `nina_zone`
--

CREATE TABLE `nina_zone` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `region` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `contenu` longtext COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `nina_categorie`
--
ALTER TABLE `nina_categorie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A8C99A693BB65D28` (`type_categorie_id`);

--
-- Index pour la table `nina_champ`
--
ALTER TABLE `nina_champ`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `nina_commentaire`
--
ALTER TABLE `nina_commentaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_178FA7AA67F7E8BE` (`idPage`);

--
-- Index pour la table `nina_configuration`
--
ALTER TABLE `nina_configuration`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_B2A1BF5E968B03E4` (`langue_defaut_id`);

--
-- Index pour la table `nina_langue`
--
ALTER TABLE `nina_langue`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_3B3F940886B470F8` (`abreviation`);

--
-- Index pour la table `nina_menu`
--
ALTER TABLE `nina_menu`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `nina_menu_page`
--
ALTER TABLE `nina_menu_page`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3DF10A33499475BF` (`page_parent_id`),
  ADD KEY `IDX_3DF10A33C4663E4` (`page_id`),
  ADD KEY `IDX_3DF10A33CCD7E912` (`menu_id`);

--
-- Index pour la table `nina_module`
--
ALTER TABLE `nina_module`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `nina_page`
--
ALTER TABLE `nina_page`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_2A202F6997E3DD86` (`seo_id`),
  ADD KEY `IDX_2A202F6960BB6FE6` (`auteur_id`),
  ADD KEY `IDX_2A202F69F6698E1C` (`auteur_derniere_modification_id`),
  ADD KEY `IDX_2A202F69499475BF` (`page_parent_id`),
  ADD KEY `IDX_2A202F69320E6035` (`page_originale_id`),
  ADD KEY `IDX_2A202F692AADBACD` (`langue_id`);

--
-- Index pour la table `nina_page_categorie`
--
ALTER TABLE `nina_page_categorie`
  ADD PRIMARY KEY (`nina_page_id`,`nina_categorie_id`),
  ADD KEY `IDX_E6E011658352D10` (`nina_page_id`),
  ADD KEY `IDX_E6E0116548512DB3` (`nina_categorie_id`);

--
-- Index pour la table `nina_page_module`
--
ALTER TABLE `nina_page_module`
  ADD PRIMARY KEY (`nina_page_id`,`nina_module_id`),
  ADD KEY `IDX_138D1F208352D10` (`nina_page_id`),
  ADD KEY `IDX_138D1F204E76F9CC` (`nina_module_id`);

--
-- Index pour la table `nina_seo`
--
ALTER TABLE `nina_seo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `nina_type_categorie`
--
ALTER TABLE `nina_type_categorie`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `nina_type_module`
--
ALTER TABLE `nina_type_module`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_5F8C82066C6E55B5` (`nom`),
  ADD KEY `IDX_5F8C820660D6DC42` (`modules_id`);

--
-- Index pour la table `nina_type_module_champ`
--
ALTER TABLE `nina_type_module_champ`
  ADD PRIMARY KEY (`nina_type_module_id`,`nina_champ_id`),
  ADD KEY `IDX_78496CD08F778C1F` (`nina_type_module_id`),
  ADD KEY `IDX_78496CD069FEEC45` (`nina_champ_id`);

--
-- Index pour la table `nina_utilisateur`
--
ALTER TABLE `nina_utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_6D63ACA592FC23A8` (`username_canonical`),
  ADD UNIQUE KEY `UNIQ_6D63ACA5A0D96FBF` (`email_canonical`),
  ADD UNIQUE KEY `UNIQ_6D63ACA5C05FB297` (`confirmation_token`);

--
-- Index pour la table `nina_zone`
--
ALTER TABLE `nina_zone`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `nina_categorie`
--
ALTER TABLE `nina_categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `nina_champ`
--
ALTER TABLE `nina_champ`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `nina_commentaire`
--
ALTER TABLE `nina_commentaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `nina_configuration`
--
ALTER TABLE `nina_configuration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `nina_langue`
--
ALTER TABLE `nina_langue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `nina_menu`
--
ALTER TABLE `nina_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `nina_menu_page`
--
ALTER TABLE `nina_menu_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `nina_module`
--
ALTER TABLE `nina_module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `nina_page`
--
ALTER TABLE `nina_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `nina_seo`
--
ALTER TABLE `nina_seo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `nina_type_categorie`
--
ALTER TABLE `nina_type_categorie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `nina_type_module`
--
ALTER TABLE `nina_type_module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `nina_utilisateur`
--
ALTER TABLE `nina_utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `nina_zone`
--
ALTER TABLE `nina_zone`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `nina_categorie`
--
ALTER TABLE `nina_categorie`
  ADD CONSTRAINT `FK_A8C99A693BB65D28` FOREIGN KEY (`type_categorie_id`) REFERENCES `nina_type_categorie` (`id`);

--
-- Contraintes pour la table `nina_commentaire`
--
ALTER TABLE `nina_commentaire`
  ADD CONSTRAINT `FK_178FA7AA67F7E8BE` FOREIGN KEY (`idPage`) REFERENCES `nina_page` (`id`);

--
-- Contraintes pour la table `nina_configuration`
--
ALTER TABLE `nina_configuration`
  ADD CONSTRAINT `FK_B2A1BF5E968B03E4` FOREIGN KEY (`langue_defaut_id`) REFERENCES `nina_langue` (`id`);

--
-- Contraintes pour la table `nina_menu_page`
--
ALTER TABLE `nina_menu_page`
  ADD CONSTRAINT `FK_3DF10A33499475BF` FOREIGN KEY (`page_parent_id`) REFERENCES `nina_page` (`id`),
  ADD CONSTRAINT `FK_3DF10A33C4663E4` FOREIGN KEY (`page_id`) REFERENCES `nina_page` (`id`),
  ADD CONSTRAINT `FK_3DF10A33CCD7E912` FOREIGN KEY (`menu_id`) REFERENCES `nina_menu` (`id`);

--
-- Contraintes pour la table `nina_page`
--
ALTER TABLE `nina_page`
  ADD CONSTRAINT `FK_2A202F692AADBACD` FOREIGN KEY (`langue_id`) REFERENCES `nina_langue` (`id`),
  ADD CONSTRAINT `FK_2A202F69320E6035` FOREIGN KEY (`page_originale_id`) REFERENCES `nina_page` (`id`),
  ADD CONSTRAINT `FK_2A202F69499475BF` FOREIGN KEY (`page_parent_id`) REFERENCES `nina_page` (`id`),
  ADD CONSTRAINT `FK_2A202F6960BB6FE6` FOREIGN KEY (`auteur_id`) REFERENCES `nina_utilisateur` (`id`),
  ADD CONSTRAINT `FK_2A202F6997E3DD86` FOREIGN KEY (`seo_id`) REFERENCES `nina_seo` (`id`),
  ADD CONSTRAINT `FK_2A202F69F6698E1C` FOREIGN KEY (`auteur_derniere_modification_id`) REFERENCES `nina_utilisateur` (`id`);

--
-- Contraintes pour la table `nina_page_categorie`
--
ALTER TABLE `nina_page_categorie`
  ADD CONSTRAINT `FK_E6E0116548512DB3` FOREIGN KEY (`nina_categorie_id`) REFERENCES `nina_categorie` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_E6E011658352D10` FOREIGN KEY (`nina_page_id`) REFERENCES `nina_page` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `nina_page_module`
--
ALTER TABLE `nina_page_module`
  ADD CONSTRAINT `FK_138D1F204E76F9CC` FOREIGN KEY (`nina_module_id`) REFERENCES `nina_module` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_138D1F208352D10` FOREIGN KEY (`nina_page_id`) REFERENCES `nina_page` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `nina_type_module`
--
ALTER TABLE `nina_type_module`
  ADD CONSTRAINT `FK_5F8C820660D6DC42` FOREIGN KEY (`modules_id`) REFERENCES `nina_module` (`id`);

--
-- Contraintes pour la table `nina_type_module_champ`
--
ALTER TABLE `nina_type_module_champ`
  ADD CONSTRAINT `FK_78496CD069FEEC45` FOREIGN KEY (`nina_champ_id`) REFERENCES `nina_champ` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_78496CD08F778C1F` FOREIGN KEY (`nina_type_module_id`) REFERENCES `nina_type_module` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
