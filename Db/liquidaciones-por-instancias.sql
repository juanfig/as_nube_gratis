-- Server version: 5.6.41 MySQL

alter table tb_agency drop column IF EXISTS agencyPort;
alter table tb_agency drop column IF EXISTS liquidationPercent;
alter table tb_agency drop column IF EXISTS liquidationFixed;


alter table tb_agency add column agencyPort   int default NULL;
alter table tb_agency add column liquidationPercent float not null default 0;
alter table tb_agency add column liquidationFixed   decimal(18,2) not null default 0;


truncate table tb_credit;
insert into tb_credit(nameCredit,valueCredit,periodCredit,descriptionCredit,idAgencyFk)values
("Por Porcentaje",0,1,"Porcentaje de viajes realizados",NULL),
("Por Chofer",0,1,"Monto fijo por chofer",NULL);

update tb_agency set liquidationFixed = 4, idCreditFk=2;

drop TABLE if exists tb_liquidation_agency;

CREATE TABLE tb_liquidation_agency (
  idLiquidation int(11) NOT NULL AUTO_INCREMENT primary key,
  idAgencyFk int(11) NOT NULL,
  status tinyint NOT NULL DEFAULT 0,
  liquidationDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  monthClosure date NOT NULL,
  paymentdate date DEFAULT NULL,
  userName varchar(50) DEFAULT NULL,

  liquidationPercent float not null default 0,
	liquidationFixed   decimal(18,2) not null default 0,
	numberDrivers int(11) not null default 0,
	amountTravels   decimal(18,2) not null default 0,

  amount decimal(18,2) NOT NULL,

  unique(idAgencyFk,monthClosure)

) ENGINE=MyISAM DEFAULT CHARSET=UTF8 COMMENT='Tabla donde se almacenan las liquidaciones mensuales de cada angencia.';
