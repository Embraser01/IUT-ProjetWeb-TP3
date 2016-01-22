# :movie_camera: Application Zend - Statistiques de films #
(on a refait un peu trakt.tv)  
(on = Marc-Antoine FERNANDES & Nicolas POURPRIX)

## Description de l'app ##
Cette application permet aux utilisateurs de référencer les films qu'il a vu.  
En se connectant sur le site, l'utilisateur arrive sur la page de connexion. L'utilisateur renseigne son login et son mot de passe pour accéder à l'application.  
L'application se compose de 3 pages (en plus de la page de connexion) :  
* La page affichant les films vus par l'utilisateur  
* La page où l'on peut ajouter un film  
* La page affichant tous les films de la base  

## Connexion ##

Pour se connecter à l'application, connectez-vous avec les identifiants suivants :  
```
login : test
password : azerty 
```

## Ajouter un film ##
L'utilisateur doit rentrer le **nom du film**, le **genre du film** ainsi que sa **date de sortie**.  

## Installation ##
Après avoir cloné le repo, faire un ```composer install```

Puis dans **/config/autoload** renomer le fichier ```local.php.dist``` en ```local.php``` et éditer le nom d'utilisateur et le mot de passe de la base de données.

Puis modifier le fichier **/config/autoload/global.php** en changeant la base utilisée.

Importer la base de données en lançant le script suivant :
```MySQL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `z2_Film` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `release_year` date DEFAULT NULL,
  `kind` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `z2_Film` (`id`, `name`, `release_year`, `kind`) VALUES
(12, 'Ant-Man', '2015-05-01', 'SF'),
(13, 'BLBLBLBLBL', '2019-08-01', 'BLBLBLBLBL');



CREATE TABLE `z2_FilmUser` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `film_id` int(11) NOT NULL DEFAULT '0',
  `show_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `z2_FilmUser` (`user_id`, `film_id`, `show_date`) VALUES
(1, 12, '2016-01-22'),
(1, 13, '2016-01-22');



CREATE TABLE `z2_User` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `z2_User` (`id`, `username`, `password`) VALUES
(1, 'test', 'a03985856fa9b1651976a47a4ce46684bc28273164bcb5249fddf9b34e998433');



ALTER TABLE `z2_Film`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `z2_FilmUser`
  ADD PRIMARY KEY (`user_id`,`film_id`),
  ADD KEY `z2_FilmUser_z2_Films_id_fk` (`film_id`);

ALTER TABLE `z2_User`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `z2_Film`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `z2_User`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `z2_FilmUser`
  ADD CONSTRAINT `z2_FilmUser_z2_User_id_fk` FOREIGN KEY (`user_id`) REFERENCES `z2_User` (`id`),
  ADD CONSTRAINT `z2_FilmUser_z2_Films_id_fk` FOREIGN KEY (`film_id`) REFERENCES `z2_Film` (`id`);

```