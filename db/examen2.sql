DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios (
    id         bigserial    PRIMARY KEY
  , nombre     varchar(255) NOT NULL UNIQUE
  , password   varchar(255) NOT NULL
);

INSERT
  INTO usuarios (nombre, password)
VALUES ('pepe', crypt('pepe', gen_salt('bf', 11)))
     , ('juan', crypt('juan', gen_salt('bf', 11)));

DROP TABLE IF EXISTS aeropuertos CASCADE;

CREATE TABLE aeropuertos (
    id           bigserial    PRIMARY KEY
  , codigo       varchar(3)   UNIQUE
  , denominacion varchar(255) NOT NULL
);

INSERT
  INTO aeropuertos (codigo, denominacion)
VALUES ('XRY', 'Jerez')
     , ('SVQ', 'Sevilla')
     , ('STD', 'Londres Stansted');

DROP TABLE IF EXISTS companias CASCADE;

CREATE TABLE companias (
    id           bigserial    PRIMARY KEY
  , denominacion varchar(255) NOT NULL
);

INSERT
  INTO companias (denominacion)
VALUES ('Iberia')
     , ('Ryanair');

DROP TABLE IF EXISTS vuelos CASCADE;

CREATE TABLE vuelos (
    id          bigserial    PRIMARY KEY
  , codigo      varchar(6)   UNIQUE CONSTRAINT ck_codigo_valido
                             CHECK (codigo ~ '^[A-Z]{2}\d{4}$')
  , origen_id   bigint       NOT NULL REFERENCES aeropuertos (id)
                             ON DELETE NO ACTION ON UPDATE CASCADE
  , destino_id  bigint       NOT NULL REFERENCES aeropuertos (id)
                             ON DELETE NO ACTION ON UPDATE CASCADE
  , compania_id bigint       NOT NULL REFERENCES companias (id)
                             ON DELETE NO ACTION ON UPDATE CASCADE
  , salida      timestamp    NOT NULL
  , llegada     timestamp    NOT NULL
  , plazas      numeric(3)   NOT NULL
  , precio      numeric(8,2) NOT NULL
);

INSERT
  INTO vuelos (codigo, origen_id, destino_id, compania_id, salida, llegada, plazas, precio)
VALUES ('IB4341', 1, 2, 1, '2018-04-23 16:35:00', '2018-04-23 19:00:00', 300, 50);

DROP TABLE IF EXISTS reservas CASCADE;

CREATE TABLE reservas (
    id         bigserial  PRIMARY KEY
  , usuario_id bigint     NOT NULL REFERENCES usuarios (id)
                          ON DELETE NO ACTION ON UPDATE CASCADE
  , vuelo_id   bigint     NOT NULL REFERENCES vuelos (id)
                          ON DELETE NO ACTION ON UPDATE CASCADE
  , asiento    numeric(3) NOT NULL
  , created_at timestamp  NOT NULL DEFAULT localtimestamp
  , UNIQUE (vuelo_id, asiento)
);

CREATE VIEW v_vuelos AS
  SELECT v.*, c.denominacion AS compania_denominacion, plazas - COUNT(r.id) AS plazas_libres
    FROM vuelos v LEFT JOIN reservas r ON r.vuelo_id = v.id
    LEFT JOIN companias c ON v.compania_id = c.id
GROUP BY v.id, c.denominacion;
