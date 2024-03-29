
CREATE TABLE IF NOT EXISTS `gevu_diagext` (
  `id_diagext` int(11) NOT NULL AUTO_INCREMENT,
  `id_lieu` int(11) NOT NULL,
  `id_instant` int(11) NOT NULL,
  `id_entreprise` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `source` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auditif` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `cognitif` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `moteur` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `visuel` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_diagext`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE  `gevu_logements` ADD  `data` LONGTEXT NOT NULL;

ALTER TABLE  `gevu_lieux` ADD  `lock_diag` VARCHAR( 10 ) NOT NULL ,
ADD INDEX (  `lock_diag` );

ALTER TABLE  `gevu_geos` 
ADD  `heading` DECIMAL( 12, 8 ) NOT NULL ,
ADD  `pitch` DECIMAL( 12, 8 ) NOT NULL ,
ADD  `zoom_cell` DECIMAL( 2, 2 ) NOT NULL,
ADD  `data` longtext COLLATE utf8_unicode_ci NOT NULL,
ADD  `latlng` point NOT NULL,
ADD  `sw` point NOT NULL,
ADD  `ne` point NOT NULL;

-- mise � jour des type de contr�le
ALTER TABLE  gevu_lieux 
ADD  id_type_controle INTEGER NOT NULL;


//
update gevu_lieux
set id_type_controle = 44
where id_lieu in (select id_lieu FROM gevu_etablissements);
update gevu_lieux
set id_type_controle = 45
where id_lieu in (select id_lieu FROM gevu_batiments);
update gevu_lieux
set id_type_controle = 46
where id_lieu in (select id_lieu FROM gevu_niveaux);
update gevu_lieux
set id_type_controle = 50
where id_lieu in (select id_lieu FROM gevu_espacesxinterieurs);
update gevu_lieux
set id_type_controle = 58
where id_lieu in (select id_lieu FROM gevu_objetsxinterieurs);
update gevu_lieux
set id_type_controle = 51
where id_lieu in (select id_lieu FROM gevu_parcelles);
update gevu_lieux
set id_type_controle = 49
where id_lieu in (select id_lieu FROM gevu_espacesxexterieurs);
update gevu_lieux
set id_type_controle = 59
where id_lieu in (select id_lieu FROM gevu_objetsxvoiries);
update gevu_lieux
set id_type_controle = 47
where id_lieu in (select id_lieu FROM gevu_objetsxexterieurs);


-- mise � jour des r�ponses
update gevu_etablissements set reponse_4 = 1 where reponse_4 = "select_1_1";
update gevu_etablissements set reponse_4 = 2 where reponse_4 = "select_1_2";
update gevu_etablissements set reponse_5 = 1 where reponse_5 = "select_2_1";
update gevu_etablissements set reponse_5 = 2 where reponse_5 = "select_2_2";

-- mise � jour des contacts
update gevu_batiments set contact_proprietaire = null, contact_delegataire=null, contact_gardien =null;
ALTER TABLE  `gevu_etablissements` CHANGE  `contact_proprietaire`  `contact_proprietaire` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,
CHANGE  `contact_delegataire`  `contact_delegataire` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;
update gevu_etablissements set contact_proprietaire = null, contact_delegataire=null;
//

TRUNCATE gevu_diagnosticsxvoirie;