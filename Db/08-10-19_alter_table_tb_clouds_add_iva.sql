alter table tb_clouds add column iva decimal(18,2) not null default 0.00;

update tb_clouds set iva =12.00 where idCountryKf = 65;
update tb_clouds set iva =21.00 where idCountryKf = 11;
update tb_clouds set iva =21.00 where idCountryKf = 70;
