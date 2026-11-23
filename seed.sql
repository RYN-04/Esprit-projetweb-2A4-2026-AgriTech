-- AgriTech — donnees de test (a importer APRES schema.sql)
-- Compte admin :  admin@agritech.tn  /  Admin1234
USE agritech;

INSERT INTO User (nom, prenom, email, password, role, statut)
VALUES ('Admin', 'Test', 'admin@agritech.tn',
        '$2y$10$3n4FkoH3NuCmod3MrHC1WO2HTD4AnnnGfjKjxWiUGTtAD5uWukU1q',
        'admin', 'actif');
