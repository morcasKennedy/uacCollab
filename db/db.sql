CREATE TABLE etudiant (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    matricule TEXT,
    nom TEXT,
    postnom TEXT,
    prenom TEXT,
    genre TEXT,
    date_naissance DATE,
    lieu_naissance TEXT,
    adresse TEXT,
    image TEXT,
    telephone TEXT,
    email TEXT,
    mot_de_passe TEXT,
    status int
);

CREATE TABLE annee (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    description TEXT,
    status INT
);

CREATE TABLE departement(
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    description TEXT,
    status INT
);

CREATE TABLE promotion (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    description TEXT,
    departement BIGINT,
    status INT
);

CREATE TABLE inscription (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    dates DATE,
    description TEXT,
    etudiant BIGINT,
    promotion BIGINT,
    annee BIGINT,
    status INT
);

CREATE TABLE encadreur (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    nom TEXT,
    postnom TEXT,
    prenom TEXT,
    genre TEXT,
    date_naissance DATE,
    lieu_naissance TEXT,
    adresse TEXT,
    image TEXT,
    telephone TEXT,
    email TEXT,
    mot_de_passe TEXT,
    status INT
);

CREATE TABLE affectation (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    dates DATETIME,
    etudiant BIGINT,
    encadreur BIGINT,
    annee BIGINT,
    promotion BIGINT,
    status INT
);

CREATE TABLE projet (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    titre TEXT,
    description TEXT,
    encadreur BIGINT,
    backgroud TEXT,
    running INT,
    status INT
);

CREATE TABLE projet_encadreur (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    projet BIGINT,
    encadreur BIGINT,
    status INT
);

CREATE TABLE fichiers_projet (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    dates DATETIME,
    projet BIGINT,
    fichier TEXT,
    user BIGINT,
    commentaire TEXT,
    type TEXT,
    version TEXT,
    status INT
);


-- DONNEES DE TEST
INSERT INTO etudiant (matricule, nom, postnom, prenom, genre, date_naissance, lieu_naissance, adresse, image, telephone, email, mot_de_passe, status) VALUES
('ETU001', 'Kabongo', 'Mwamba', 'Jean', 'M', '2000-05-15', 'Kinshasa', 'Avenue 1', '1.png', '0999999991', 'JeanKabongo@uaconline.edu.cd', 'pass123', 1),
('ETU002', 'Mukendi', 'Tshibanda', 'Alice', 'F', '2001-07-20', 'Lubumbashi', 'Avenue 2', '2.png', '0999999992', 'AliceMukendi@uaconline.edu.cd', 'pass456', 1),
('ETU003', 'Ndongala', 'Kabasele', 'Patrick', 'M', '1999-11-30', 'Matadi', 'Avenue 3', '3.png', '0999999993', 'PatrickNdongala@uaconline.edu.cd', 'pass789', 1),
('ETU004', 'Kasongo', 'Ilunga', 'Esther', 'F', '2002-03-25', 'Goma', 'Avenue 4', '4.png', '0999999994', 'EstherKasongo@uaconline.edu.cd', 'pass321', 1),
('ETU005', 'Mbuyi', 'Kalala', 'David', 'M', '2000-09-10', 'Kananga', 'Avenue 5', '5.png', '0999999995', 'DavidMbuyi@uaconline.edu.cd', 'pass654', 1);

INSERT INTO annee (description, status) VALUES
('2021-2022', 1),
('2022-2023', 1),
('2023-2024', 1),
('2024-2025', 1),
('2025-2026', 1);

INSERT INTO promotion (description, departement, status) VALUES
('L1', 1, 1),
('L2', 1, 1),
('L3', 1, 1),
('M1', 1, 1),
('M2', 1, 1);


INSERT INTO inscription (dates, description, etudiant, promotion, annee, status) VALUES
('2025-03-01', 'Reinscription en troisième année', 1, 1, 1, 1),
('2025-03-02', 'Reinscription en troisième année', 2, 2, 1, 1),
('2025-03-03', 'Reinscription en troisième année', 3, 3, 1, 1),
('2025-03-04', 'Reinscription en troisième année', 4, 4, 1, 1),
('2025-03-05', 'Reinscription en troisième année', 5, 5, 1, 1);

INSERT INTO encadreur (nom, postnom, prenom, genre, date_naissance, lieu_naissance, adresse, image, telephone, email, mot_de_passe, status) VALUES
('Katanga', 'Mulumba', 'Jean', 'M', '1980-04-10', 'Kinshasa', 'Avenue 10', '1.png', '0998888881', 'JeanKatanga@uaconline.edu.cd', 'encapass123', 1),
('Tshibangu', 'Lutumba', 'Marie', 'F', '1985-06-15', 'Lubumbashi', 'Avenue 11', '2.png', '0998888882', 'MarieTshibangu@uaconline.edu.cd', 'encapass456', 1),
('Kabwe', 'Mwanza', 'Patrick', 'M', '1978-02-20', 'Matadi', 'Avenue 12', '3.png', '0998888883', 'PatrickKabwe@uaconline.edu.cd', 'encapass789', 1),
('Mubenga', 'Kasongo', 'Esther', 'F', '1982-09-05', 'Goma', 'Avenue 13', '4.png', '0998888884', 'EstherMubenga@uaconline.edu.cd', 'encapass321', 1),
('Ngalula', 'Kalombo', 'David', 'M', '1975-12-30', 'Kananga', 'Avenue 14', '5.png', '0998888885', 'DavidNgalula@uaconline.edu.cd', 'encapass654', 1);