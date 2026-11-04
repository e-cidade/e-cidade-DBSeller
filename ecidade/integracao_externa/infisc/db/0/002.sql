
--drop language if exists plpgsql;
--create language plpgsql;

-- Funcao para GRANT ou REVOKE
create or replace function fc_grant_revoke(text, text, text, text, text) returns integer as 
$$
declare
  sUsuario    alias for $2;
  sPrivilegio alias for $3; -- [ all | select,insert,update,delete ]
  sEsquema    alias for $4;
  sObjeto     alias for $5; -- [ table | view | sequence ]

  sOpcao      text;
  rObjeto     record;
  iQtd        integer;

  sPredicado  text;
begin
  iQtd := 0;

  sOpcao := upper($1); -- [ grant | revoke ]

  -- Verifica predicado para Grant nos Schemas
  if position('DELETE' in sOpcao) > 0 or
     position('UPDATE' in sOpcao) > 0 or
     position('INSERT' in sOpcao) > 0 or
     sOpcao = 'ALL' then
    sPredicado := ' all ';
  else
    sPredicado := ' usage ';
  end if;

  -- Grant/Revoke nos Schemas
  for rObjeto in
    select nspname
      from pg_namespace
     where nspname not in ('pg_catalog', 'information_schema', 'pg_toast')
       and nspname !~ '^pg_temp'
       and nspname like sEsquema
  loop
    if upper(sOpcao) = 'GRANT' then
      execute 'grant ' || sPredicado || ' on schema ' || rObjeto.nspname || ' to ' || sUsuario;
    else
      execute 'revoke ' || sPredicado || ' on schema ' || rObjeto.nspname || ' from ' || sUsuario;
    end if;
  end loop;

  -- Grant/Revoke nas Relacoes
  for rObjeto in 
    select relname,
           nspname
      from pg_class c
           join pg_namespace ns on (c.relnamespace = ns.oid) 
     where relkind in ('r','v','s') -- Relacao, View, Sequence
       --and nspname not in ('pg_catalog', 'information_schema', 'pg_toast')
       --and nspname !~ '^pg_temp'
       and nspname like sEsquema 
       and relname like sObjeto
  loop
    if upper(sOpcao) = 'GRANT' then
      --raise info 'grant % on % to %;', sPrivilegio, rObjeto.relname, sUsuario;
      execute 'grant ' || sPrivilegio || ' on ' || rObjeto.nspname || '.' || rObjeto.relname || ' to ' || sUsuario;
    else
      --raise info 'revoke % on % from %;', sPrivilegio, rObjeto.relname, sUsuario;
      execute 'revoke ' || sPrivilegio || ' on ' || rObjeto.nspname || '.' || rObjeto.relname || ' from ' || sUsuario;
    end if;
    iQtd := iQtd + 1;
  end loop;
  return iQtd;
end;
$$ 
language plpgsql;


create or replace function fc_grant(text, text, text, text) returns integer as 
$$
  select fc_grant_revoke('grant', $1, $2, $3, $4);
$$
language sql;

create or replace function fc_revoke(text, text, text, text) returns integer as 
$$
  select fc_grant_revoke('revoke', $1, $2, $3, $4);
$$
language sql;

create or replace function fc_valida_hora(char(5)) returns boolean as 
$$
  select ((cast(substr($1,1,2) as integer) between 0 and 23) and (cast(substr($1,4,2) as integer) between 0 and 59));
$$
language sql;


-- Cria Role
DROP ROLE IF EXISTS integrainfisc;
CREATE ROLE integrainfisc WITH LOGIN PASSWORD 'IntegraInfisc@2023';

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM integrainfisc;

-- Define Permissoes Objetos
SELECT fc_grant('integrainfisc', 'select,update', 'public', 'integra%');

