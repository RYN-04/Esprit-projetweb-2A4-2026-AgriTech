-- AgriTech database schema (diagram: docs/diagram.md)
-- Import: phpMyAdmin > Import > schema.sql, or: mysql -u root < schema.sql

CREATE DATABASE IF NOT EXISTS agritech
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE agritech;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS Traitement, Plante, Commentaire, Publication,
  Production_Consommation, Energie, Categorie_Energie,
  Materiel, CategorieMateriel, MissionDrone, Drone, User;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------
-- Users
-- ---------------------------------------------------------------
CREATE TABLE User (
  id_user  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom      VARCHAR(100) NOT NULL,
  prenom   VARCHAR(100) NOT NULL,
  email    VARCHAR(190) NOT NULL,
  password VARCHAR(255) NOT NULL COMMENT 'password_hash() output, never plain text',
  role     ENUM('admin','agriculteur') NOT NULL DEFAULT 'agriculteur',
  statut   ENUM('actif','inactif') NOT NULL DEFAULT 'actif' COMMENT 'inactif = cannot log in',
  PRIMARY KEY (id_user),
  UNIQUE KEY uq_user_email (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Drones module
-- ---------------------------------------------------------------
CREATE TABLE Drone (
  id_drone  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom_drone VARCHAR(100) NOT NULL,
  modele    VARCHAR(100) NOT NULL,
  etat      VARCHAR(50)  NOT NULL,
  PRIMARY KEY (id_drone)
) ENGINE=InnoDB;

CREATE TABLE MissionDrone (
  id_mission   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user      INT UNSIGNED NOT NULL,
  id_drone     INT UNSIGNED NOT NULL,
  date_mission DATE         NOT NULL,
  zone         VARCHAR(150) NOT NULL,
  statut       VARCHAR(50)  NOT NULL,
  PRIMARY KEY (id_mission),
  KEY idx_mission_user (id_user),
  KEY idx_mission_drone (id_drone),
  CONSTRAINT fk_mission_user  FOREIGN KEY (id_user)  REFERENCES User (id_user)   ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT fk_mission_drone FOREIGN KEY (id_drone) REFERENCES Drone (id_drone) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Materiels module
-- ---------------------------------------------------------------
CREATE TABLE CategorieMateriel (
  id_categorie  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom_categorie VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_categorie),
  UNIQUE KEY uq_catmat_nom (nom_categorie)
) ENGINE=InnoDB;

CREATE TABLE Materiel (
  id_materiel  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user      INT UNSIGNED NOT NULL,
  id_categorie INT UNSIGNED NOT NULL,
  nom          VARCHAR(150) NOT NULL,
  etat         VARCHAR(50)  NOT NULL,
  PRIMARY KEY (id_materiel),
  KEY idx_materiel_user (id_user),
  KEY idx_materiel_cat (id_categorie),
  CONSTRAINT fk_materiel_user FOREIGN KEY (id_user)      REFERENCES User (id_user)                 ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT fk_materiel_cat  FOREIGN KEY (id_categorie) REFERENCES CategorieMateriel (id_categorie) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Energie module
-- ---------------------------------------------------------------
CREATE TABLE Categorie_Energie (
  id_categorie  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nom_categorie VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_categorie),
  UNIQUE KEY uq_catener_nom (nom_categorie)
) ENGINE=InnoDB;

CREATE TABLE Energie (
  id_energie   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_categorie INT UNSIGNED NOT NULL,
  nom_energie  VARCHAR(100) NOT NULL,
  type         VARCHAR(50)  NOT NULL,
  PRIMARY KEY (id_energie),
  KEY idx_energie_cat (id_categorie),
  CONSTRAINT fk_energie_cat FOREIGN KEY (id_categorie) REFERENCES Categorie_Energie (id_categorie) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Production_Consommation (
  id_pc          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user        INT UNSIGNED NOT NULL,
  id_energie     INT UNSIGNED NOT NULL,
  type_operation ENUM('Production','Consommation') NOT NULL,
  date_operation DATE NOT NULL,
  PRIMARY KEY (id_pc),
  KEY idx_pc_user (id_user),
  KEY idx_pc_energie (id_energie),
  CONSTRAINT fk_pc_user    FOREIGN KEY (id_user)    REFERENCES User (id_user)       ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT fk_pc_energie FOREIGN KEY (id_energie) REFERENCES Energie (id_energie) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Forum module
-- ---------------------------------------------------------------
CREATE TABLE Publication (
  id_publication INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user        INT UNSIGNED NOT NULL,
  contenu        TEXT         NOT NULL,
  date_pub       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  image          VARCHAR(255) NULL,
  PRIMARY KEY (id_publication),
  KEY idx_pub_user (id_user),
  CONSTRAINT fk_pub_user FOREIGN KEY (id_user) REFERENCES User (id_user) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Commentaire (
  id_commentaire INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user        INT UNSIGNED NOT NULL,
  id_publication INT UNSIGNED NOT NULL,
  contenu        TEXT         NOT NULL,
  date_com       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_commentaire),
  KEY idx_com_user (id_user),
  KEY idx_com_pub (id_publication),
  CONSTRAINT fk_com_user FOREIGN KEY (id_user)        REFERENCES User (id_user)               ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_com_pub  FOREIGN KEY (id_publication) REFERENCES Publication (id_publication) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Plantes module
-- ---------------------------------------------------------------
CREATE TABLE Plante (
  id_plante   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_user     INT UNSIGNED NOT NULL,
  nom         VARCHAR(150) NOT NULL,
  description TEXT         NULL,
  type        VARCHAR(50)  NOT NULL,
  PRIMARY KEY (id_plante),
  KEY idx_plante_user (id_user),
  CONSTRAINT fk_plante_user FOREIGN KEY (id_user) REFERENCES User (id_user) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE Traitement (
  id_traitement   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_plante       INT UNSIGNED NOT NULL,
  description     TEXT         NOT NULL,
  date_traitement DATE         NOT NULL,
  statut          VARCHAR(50)  NOT NULL,
  PRIMARY KEY (id_traitement),
  KEY idx_trait_plante (id_plante),
  CONSTRAINT fk_trait_plante FOREIGN KEY (id_plante) REFERENCES Plante (id_plante) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
