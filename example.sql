USE Users;

CREATE TABLE IF NOT EXISTS `users` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `name` varchar(30) NOT NULL,
                    `email` varchar(255) NOT NULL UNIQUE,
                    `password` varchar(255) NOT NULL,
                    `reset_code` int(6) DEFAULT NULL,
                    `fails_left	` int(6) DEFAULT 3,
                    `blocked` int(6) DEFAULT 'false',
                    PRIMARY KEY (`id`)
                    );

CREATE TABLE IF NOT EXISTS `admin_table` (
                                       `id` int NOT NULL AUTO_INCREMENT,
                                       `name` varchar(30) NOT NULL,
                                       `email` varchar(255) NOT NULL UNIQUE,
                                       `password` varchar(255) NOT NULL,
                                       PRIMARY KEY (`id`)
);