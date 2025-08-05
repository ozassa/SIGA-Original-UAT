CREATE TABLE insured (
       id   serial,
       nome text
);

--INSERT INTO insured (nome) VALUES('Alex de Moraes Exportadora Grugel Ltda');



create table importer (
       id       serial,
       nome     text,
       endereco text,
       id_pais  int8,
       id_insured int8 not null,
       foreign key (id_insured) references insured(id) on delete restrict
);

INSERT INTO importer VALUES (1, 'Importador I', 1);
INSERT INTO importer VALUES (2, 'Importador II', 1);



--
-- credit
--
CREATE TABLE credit (
       id		serial,
       id_importer	int8 not null,
       credit_date	date, 
       credit_req	float8,
       credit_assigned	float8,
       foreign key (id_importer) references importer(id) on delete restrict
);
INSERT INTO credit (id_importer, credit_date, credit_req, credit_assigned) VALUES(1, '2002/04/01', 1000, 500);

--
-- notification
--
CREATE TABLE notification (
       id		  serial,
       description        text,
       born_date          date,
       state              int4,
       link               text 
);


--
-- change_credit
--

create table change_credit (
       id		   serial,
       id_notification	   int8 not null,
       id_importer	   int8 not null,
       credit		   float8,
       state		   int4,
       foreign key (id_notification) references notification(id) on delete restrict,
       foreign key (id_importer) references importer(id) on delete restrict

);     















