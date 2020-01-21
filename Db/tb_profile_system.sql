-- Server version: 5.6.41 MySQL
truncate table tb_profile;             --    1            2         3           4            5
insert into tb_profile (profileName)values('user'),('consultor'),('chofer'),('particular'),('Pasajero');

-- tabla de profiles de sistema
CREATE TABLE `tb_profile_system` (
  `idProfile` int(11) NOT NULL AUTO_INCREMENT,
  `profileName` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `idStatusProfile` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idProfile`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
                                     --    1         2           3           4
insert into tb_profile_system (profileName)values('Admin'),('usuario'),('partner'),('operador');
