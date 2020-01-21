ALTER TABLE tb_agency
ADD statusPayment BOOLEAN NOT NULL DEFAULT TRUE AFTER `statusRevision`;


ALTER TABLE tb_agency ADD column instancedb varchar(50) DEFAULT NULL;
UPDATE tb_agency set instancedb = 'db_asremis_5estrellas' where nameAgency = '5estrellas';
UPDATE tb_agency set instancedb = 'db_asremis_alternativa' where nameAgency = 'alternativa';
UPDATE tb_agency set instancedb = 'db_asremis_antonella' where nameAgency = 'antonella';
UPDATE tb_agency set instancedb = 'db_asremis_ArgentinaRemis' where nameAgency = 'ArgentinaRemis';
UPDATE tb_agency set instancedb = 'db_asremis_arraijan' where nameAgency = 'Arraijan';
UPDATE tb_agency set instancedb = 'db_asremis_autovip' where nameAgency = 'AutoVip';
UPDATE tb_agency set instancedb = 'db_asremis_avenidaremis' where nameAgency = 'AvenidaRemis';
UPDATE tb_agency set instancedb = 'db_asremis_casimiro' where nameAgency = 'casimiro';
UPDATE tb_agency set instancedb = 'db_asremis_clasea' where nameAgency = 'clasea';
UPDATE tb_agency set instancedb = 'db_asremis_developer' where nameAgency = 'developer';
UPDATE tb_agency set instancedb = 'db_asremis_EberRemis' where nameAgency = 'EberRemis';
UPDATE tb_agency set instancedb = 'db_asremis_go24' where nameAgency = 'go24';
UPDATE tb_agency set instancedb = 'db_asremis_hola' where nameAgency = 'HolaRemis';
UPDATE tb_agency set instancedb = 'db_asremis_intercar' where nameAgency = 'intercar';
UPDATE tb_agency set instancedb = 'db_asremis_LaPazRemises' where nameAgency = 'LaPazRemises';
UPDATE tb_agency set instancedb = 'db_asremis_lared' where nameAgency = 'lared';
UPDATE tb_agency set instancedb = 'db_asremis_luxe' where nameAgency = 'lexe';
UPDATE tb_agency set instancedb = 'db_asremis_next' where nameAgency = 'next';
UPDATE tb_agency set instancedb = 'db_asremis_era' where nameAgency = 'NuevaEra';
UPDATE tb_agency set instancedb = 'db_asremis_talcahuano2' where nameAgency = 'orgtalcahuano';
UPDATE tb_agency set instancedb = 'db_asremis_palacios' where nameAgency = 'palacios';
UPDATE tb_agency set instancedb = 'db_asremis_patagonia' where nameAgency = 'patagonia';
UPDATE tb_agency set instancedb = 'db_asremis_patagoniatras' where nameAgency = 'patagoniatras';
UPDATE tb_agency set instancedb = 'db_asremis_perugo' where nameAgency = 'perugo';
UPDATE tb_agency set instancedb = 'db_asremis_president' where nameAgency = 'President';
UPDATE tb_agency set instancedb = 'db_asremis_red_24_new' where nameAgency = 'red24';
UPDATE tb_agency set instancedb = 'db_asremis_remicar' where nameAgency = 'Remicar';
UPDATE tb_agency set instancedb = 'db_asremis_reminor' where nameAgency = 'reminor';
UPDATE tb_agency set instancedb = 'db_asremis_RemisCielo' where nameAgency = 'RemisCielo';
UPDATE tb_agency set instancedb = 'db_asremis_alvear' where nameAgency = 'RemisesAlvea';
UPDATE tb_agency set instancedb = 'db_asremis_remiscentrol' where nameAgency = 'RemisesCentro';
UPDATE tb_agency set instancedb = 'db_asremis_remisespinky' where nameAgency = 'remisespinky';
UPDATE tb_agency set instancedb = 'db_asremis_remisesya' where nameAgency = 'remisesya';
UPDATE tb_agency set instancedb = 'db_asremis_Remisur' where nameAgency = 'Remisur';
UPDATE tb_agency set instancedb = 'db_asremis_rosario' where nameAgency = 'Rosario';
UPDATE tb_agency set instancedb = 'db_asremis_SanCayetano' where nameAgency = 'SanCayetano';
UPDATE tb_agency set instancedb = 'db_asremis_set_panama' where nameAgency = 'Setpanama';
UPDATE tb_agency set instancedb = 'db_asremis_taximartinez' where nameAgency = 'TaxiMartinez';
UPDATE tb_agency set instancedb = 'db_asremis_TESTING' where nameAgency = 'TESTING';
UPDATE tb_agency set instancedb = 'db_asremis_totaltaxi' where nameAgency = 'Totaltaxi';
UPDATE tb_agency set instancedb = 'db_asremis_transtrip' where nameAgency = 'transtrip';
UPDATE tb_agency set instancedb = 'db_asremis_traslados_laplata' where nameAgency = 'TrasladosLaPlata';
UPDATE tb_agency set instancedb = 'db_asremis_tripservice' where nameAgency = 'TripService';
UPDATE tb_agency set instancedb = 'db_asremis_talcahuano' where nameAgency = 'ttalcahuano';
UPDATE tb_agency set instancedb = 'db_asremis_turismocentro' where nameAgency = 'turismocentro';
UPDATE tb_agency set instancedb = 'db_asremis_twins' where nameAgency = 'twins';
UPDATE tb_agency set instancedb = 'db_asremis_uvar' where nameAgency = 'uvar';
UPDATE tb_agency set instancedb = 'db_asremis_villadiego' where nameAgency = 'VillaDiego';
UPDATE tb_agency set instancedb = 'db_asremis_ypf' where nameAgency = 'YPF';


select (t1.drv  + t2.drv ) as suma  from (select count(idDriver) as drv from db_asremis_developer.tb_driver) as t1 join (select count(idDriver) as drv from patagoniatrasDB.tb_driver) as t2;


select (t1.drv  + t2.drv ) as suma  from (select count(a.tokenFB) as drv from db_asremis_developer.tb_user a inner join db_asremis_developer.tb_driver b on a.idUser = b.idUserDriver where a.tokenFB is not NULL  and a.tokenFB !='') as t1 join (select count(a.tokenFB) as drv from patagoniatrasDB.tb_user a inner join patagoniatrasDB.tb_driver b on a.idUser = b.idUserDriver where a.tokenFB is not NULL  and a.tokenFB !='') as t2;


-- =============

select x.suma, y.activos from (
select (t1.drv  + t2.drv ) as suma  from (select count(idDriver) as drv from db_asremis_developer.tb_driver) as t1 join (select count(idDriver) as drv from patagoniatrasDB.tb_driver) as t2 
) as x join (
select (t1.drv  + t2.drv ) as activos  from (select count(a.tokenFB) as drv from db_asremis_developer.tb_user a inner join db_asremis_developer.tb_driver b on a.idUser = b.idUserDriver where a.tokenFB is not NULL  and a.tokenFB !='') as t1 join (select count(a.tokenFB) as drv from patagoniatrasDB.tb_user a inner join patagoniatrasDB.tb_driver b on a.idUser = b.idUserDriver where a.tokenFB is not NULL  and a.tokenFB !='') as t2
) as y;


select x.suma, y.activos from (select (t1.drv  + t2.drv ) as suma  from (select count(idDriver) as drv from db_asremis_developer.tb_driver) as t1 join (select count(idDriver) as drv from patagoniatrasDB.tb_driver) as t2) as x join (select (d1.act  + d2.act ) as activos  from (select count(a1.tokenFB) as act from db_asremis_developer.tb_user a1 inner join db_asremis_developer.tb_driver b1 on a1.idUser = b1.idUserDriver where a1.tokenFB is not NULL  and a1.tokenFB !='') as d1 join (select count(a2.tokenFB) as act from patagoniatrasDB.tb_user a2 inner join patagoniatrasDB.tb_driver b2 on a2.idUser = b2.idUserDriver where a2.tokenFB is not NULL  and a2.tokenFB !='') as d2) as y;

-- ====================================

select (select count(idAgency) as num  from tb_agency) as todos ,(
        select count(idAgency) as num  from tb_agency where statuAgency = 1) as activos, (
        select count(idAgency) as num  from tb_agency where statuAgency = 0) as inactivos;
-- ========================================
-- ========================================



select count(iduser) as usr,
 sum( IF(idProfileUser = 2, 1, 0)) as cli,
 sum( IF(idProfileUser = 3, 1, 0)) as drv,
 sum( IF(idProfileUser = 4, 1, 0)) as com
 from db_asremis_developer.tb_user a1











