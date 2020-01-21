-- Server version: 5.6.41 MySQL
truncate table tb_profile;             --    1            2         3           4
insert into tb_profile (profileName)values('Admin'),('usuario'),('partner'),('operador');

insert into tb_sys_module_profile(idModuleKf, idProfileKf)values(1,3),(2,3),(3,3),(14,3),(15,3),(17,3),(18,3),(19,3),(21,3);

alter table tb_users add column parentUserKf int default null;
-- alter table tb_clouds add column partnerCloudKf int default null;


