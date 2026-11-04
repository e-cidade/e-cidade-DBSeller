create table db_almox (
                     id SERIAL,
                     codigo INTEGER NOT NULL PRIMARY KEY,
                     cod_departamento INTEGER,
                     FOREIGN KEY(cod_departamento) REFERENCES departamentos(codigo_departamento)
                     );

create table matmater (
                     id SERIAL,
                     codigo INTEGER NOT NULL PRIMARY KEY,
                     descricao VARCHAR,
                     ativo BOOLEAN
                     );

create table far_matersaude (
                     id SERIAL,
                     codigo INTEGER NOT NULL PRIMARY KEY,
                     cod_material INTEGER,
                     nome_generico VARCHAR,
                     FOREIGN KEY(cod_material) REFERENCES matmater(codigo)
                     );

create table matestoque (
                     id SERIAL,
                     codigo INTEGER NOT NULL PRIMARY KEY,
                     cod_material INTEGER,
                     cod_departamento INTEGER,
                     quantidade NUMERIC,
                     FOREIGN KEY(cod_material) REFERENCES matmater(codigo),
                     FOREIGN KEY(cod_departamento) REFERENCES departamentos(codigo_departamento)
                     );
