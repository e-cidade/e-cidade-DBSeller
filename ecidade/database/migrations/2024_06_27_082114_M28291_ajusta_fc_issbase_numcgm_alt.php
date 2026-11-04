<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28291AjustaFcIssbaseNumcgmAlt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

CREATE OR REPLACE FUNCTION public.fc_issbase_numcgm_alt() 
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function$

declare 

  _q02_numcgm integer;
  _tipo       integer;
  r_socios    record; 
  _teste      integer;
  r_inscr     record;
  r_numpre    record;
  _regraiptu  integer;

  lRaise boolean default false;

begin

  select coalesce(db21_regracgmiss,1)
    into _tipo
    from db_config 
   where prefeitura = true ;

   if lRaise is true then
     raise notice '_tipo: %', _tipo;
   end if;

  select coalesce(db21_regracgmiptu,0)
    into _regraiptu
    from db_config 
   where prefeitura = true ;
 
   --regraiptu: 
   -- "0" Considerar Proprietario e Promitente (iptubase/propri/promitente)
   -- "1" Considerar Somente Proprietario (iptubase/propri)
   -- "2" Considerar Somente Promitente (promitente)

   if lRaise is true then
     raise notice '_regraiptu: %', _regraiptu;
   end if;
  
  update arrecad 
     set k00_numcgm = new.q02_numcgm 
    from arreinscr
   where arrecad.k00_numpre = arreinscr.k00_numpre 
     and arreinscr.k00_inscr = new.q02_inscr;
 
-- comentado por evandro em 2008-09-16 =, pois tem que processar o for abaixo, independente do parametro...
--  if _tipo = 1 then
    -- incluir debitos para o dono da inscricao
    for r_inscr in select k00_numpre
                     from arreinscr
                    where k00_inscr = new.q02_inscr 
    loop

      if lRaise is true then
        raise notice 'processando numcgm: % - numpre: %', new.q02_numcgm, r_inscr.k00_numpre;
      end if;
        
      select k00_numcgm
        into _teste
        from arrenumcgm
       where k00_numcgm = new.q02_numcgm 
         and k00_numpre = r_inscr.k00_numpre;

      if not found then
        insert into arrenumcgm (k00_numcgm, k00_numpre)
        values (new.q02_numcgm, r_inscr.k00_numpre);
      end if;

    end loop;

--  end if;
  
  -- inclui se numpre tem no arrematric e iptubase
  for r_numpre  in select arreinscr.k00_numpre, 
                          iptubase.j01_numcgm
                     from arreinscr
                          inner join arrematric on arrematric.k00_numpre = arreinscr.k00_numpre
                          inner join iptubase   on j01_matric            = arrematric.k00_matric
                     where arreinscr.k00_inscr = new.q02_inscr 
  loop

    select k00_numcgm
      into _teste
      from arrenumcgm
     where k00_numcgm = r_numpre.j01_numcgm
       and k00_numpre = r_numpre.k00_numpre;

    if lRaise is true then
      raise notice 'numcgm: % - numpre: % -_teste: %', r_numpre.j01_numcgm, r_numpre.k00_numpre, _teste;
    end if;

    if not found and (_regraiptu = 0 or _regraiptu = 1) then
      insert into arrenumcgm (k00_numcgm, k00_numpre) 
      values (r_numpre.j01_numcgm,r_numpre.k00_numpre);
  
    end if;
 
  end loop;

  -- inclui se numpre tem no arrematric e promitente
  for r_numpre  in select arreinscr.k00_numpre, 
                          promitente.j41_matric, 
                          promitente.j41_numcgm
                     from arreinscr
                          inner join arrematric on arrematric.k00_numpre = arreinscr.k00_numpre
                          inner join promitente on j41_matric            = arrematric.k00_matric
                     where arreinscr.k00_inscr = new.q02_inscr 
  loop

    select k00_numcgm
      into _teste
      from arrenumcgm
     where k00_numcgm = r_numpre.j41_numcgm  and k00_numpre = r_numpre.k00_numpre;

    if lRaise is true then
      raise notice 'numcgm: % - numpre: % -_teste: %', r_numpre.j41_numcgm, r_numpre.k00_numpre, _teste;
    end if;

    if not found and (_regraiptu = 0 or _regraiptu = 2) then

      insert into arrenumcgm (k00_numcgm, k00_numpre) 
      values (r_numpre.j41_numcgm,r_numpre.k00_numpre);

    end if;   
 
  end loop;

  -- inclui se numpre tem no arrematric e propri
    
  for r_numpre  in select arreinscr.k00_numpre, 
                          propri.j42_matric,  
                          propri.j42_numcgm
                     from arreinscr
                          inner join arrematric on arrematric.k00_numpre = arreinscr.k00_numpre
                          inner join propri     on j42_matric = arrematric.k00_matric
                     where arreinscr.k00_inscr = new.q02_inscr 
  loop

    select k00_numcgm
      into _teste
      from arrenumcgm
     where k00_numcgm = r_numpre.j42_numcgm 
       and k00_numpre = r_numpre.k00_numpre;

    if lRaise is true then
      raise notice 'numcgm: % - numpre: % -_teste: %', r_numpre.j42_numcgm, r_numpre.k00_numpre, _teste;
    end if;

    if not found and (_regraiptu = 0 or _regraiptu = 1) then

      insert into arrenumcgm (k00_numcgm, k00_numpre) 
      values (r_numpre.j42_numcgm,r_numpre.k00_numpre);
 
    end if;
    
  end loop;

	if _tipo = 1 then 

   	for r_socios in select q95_cgmpri,
	 	                   q95_numcgm
		              from socios 
				     where q95_cgmpri = old.q02_numcgm 
                       and q95_tipo   = 1
                   
    loop
	      perform fc_socios_exclui_arrenumcgm(old.q02_numcgm,r_socios.q95_numcgm);
	      perform fc_socios_inclui_arrenumcgm(new.q02_numcgm,r_socios.q95_numcgm);
   	end loop;		 

	end if;	

	if old.q02_numcgm <> new.q02_numcgm then 
	   update socios 
          set q95_cgmpri = new.q02_numcgm
   	    where q95_cgmpri = old.q02_numcgm;
    end if;

  return new;
       
end;

\$function$

SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec(<<<SQLD

CREATE OR REPLACE FUNCTION public.fc_issbase_numcgm_alt() 
 RETURNS trigger 
 LANGUAGE plpgsql 
 AS \$function$

declare 

  _q02_numcgm integer;
  _tipo       integer;
  r_socios    record; 
  _teste      integer;
  r_inscr     record;
  r_numpre    record;

  lRaise boolean default false;

begin

  select coalesce(db21_regracgmiss,1)
    into _tipo
    from db_config 
   where prefeitura = true ;

   if lRaise is true then
     raise notice '_tipo: %', _tipo;
   end if;
  
  update arrecad 
     set k00_numcgm = new.q02_numcgm 
    from arreinscr
   where arrecad.k00_numpre = arreinscr.k00_numpre 
     and arreinscr.k00_inscr = new.q02_inscr;
 
-- comentado por evandro em 2008-09-16 =, pois tem que processar o for abaixo, independente do parametro...
--  if _tipo = 1 then
    -- incluir debitos para o dono da inscricao
    for r_inscr in select k00_numpre
                     from arreinscr
                    where k00_inscr = new.q02_inscr 
    loop

      if lRaise is true then
        raise notice 'processando numcgm: % - numpre: %', new.q02_numcgm, r_inscr.k00_numpre;
      end if;
        
      select k00_numcgm
        into _teste
        from arrenumcgm
       where k00_numcgm = new.q02_numcgm 
         and k00_numpre = r_inscr.k00_numpre;

      if not found then
        insert into arrenumcgm (k00_numcgm, k00_numpre)
        values (new.q02_numcgm, r_inscr.k00_numpre);
      end if;

    end loop;

--  end if;
  
  -- inclui se numpre tem no arrematric e iptubase
  for r_numpre  in select arreinscr.k00_numpre, 
                          iptubase.j01_numcgm
                     from arreinscr
                          inner join arrematric on arrematric.k00_numpre = arreinscr.k00_numpre
                          inner join iptubase   on j01_matric            = arrematric.k00_matric
                     where arreinscr.k00_inscr = new.q02_inscr 
  loop

    select k00_numcgm
      into _teste
      from arrenumcgm
     where k00_numcgm = r_numpre.j01_numcgm
       and k00_numpre = r_numpre.k00_numpre;

    if lRaise is true then
      raise notice 'numcgm: % - numpre: % -_teste: %', r_numpre.j01_numcgm, r_numpre.k00_numpre, _teste;
    end if;

    if not found then

      insert into arrenumcgm (k00_numcgm, k00_numpre) 
      values (r_numpre.j01_numcgm,r_numpre.k00_numpre);
  
    end if;
 
  end loop;

  -- inclui se numpre tem no arrematric e promitente
  for r_numpre  in select arreinscr.k00_numpre, 
                          promitente.j41_matric, 
                          promitente.j41_numcgm
                     from arreinscr
                          inner join arrematric on arrematric.k00_numpre = arreinscr.k00_numpre
                          inner join promitente on j41_matric            = arrematric.k00_matric
                     where arreinscr.k00_inscr = new.q02_inscr 
  loop

    select k00_numcgm
      into _teste
      from arrenumcgm
     where k00_numcgm = r_numpre.j41_numcgm  and k00_numpre = r_numpre.k00_numpre;

    if lRaise is true then
      raise notice 'numcgm: % - numpre: % -_teste: %', r_numpre.j41_numcgm, r_numpre.k00_numpre, _teste;
    end if;

    if not found then

      insert into arrenumcgm (k00_numcgm, k00_numpre) 
      values (r_numpre.j41_numcgm,r_numpre.k00_numpre);

    end if;   
 
  end loop;

  -- inclui se numpre tem no arrematric e propri
    
  for r_numpre  in select arreinscr.k00_numpre, 
                          propri.j42_matric,  
                          propri.j42_numcgm
                     from arreinscr
                          inner join arrematric on arrematric.k00_numpre = arreinscr.k00_numpre
                          inner join propri     on j42_matric = arrematric.k00_matric
                     where arreinscr.k00_inscr = new.q02_inscr 
  loop

    select k00_numcgm
      into _teste
      from arrenumcgm
     where k00_numcgm = r_numpre.j42_numcgm 
       and k00_numpre = r_numpre.k00_numpre;

    if lRaise is true then
      raise notice 'numcgm: % - numpre: % -_teste: %', r_numpre.j42_numcgm, r_numpre.k00_numpre, _teste;
    end if;

    if not found then

      insert into arrenumcgm (k00_numcgm, k00_numpre) 
      values (r_numpre.j42_numcgm,r_numpre.k00_numpre);
 
    end if;
    
  end loop;

	if _tipo = 1 then 

   	for r_socios in select q95_cgmpri,
	 	                   q95_numcgm
		              from socios 
				     where q95_cgmpri = old.q02_numcgm 
                       and q95_tipo   = 1
                   
    loop
	      perform fc_socios_exclui_arrenumcgm(old.q02_numcgm,r_socios.q95_numcgm);
	      perform fc_socios_inclui_arrenumcgm(new.q02_numcgm,r_socios.q95_numcgm);
   	end loop;		 

	end if;	

	if old.q02_numcgm <> new.q02_numcgm then 
	   update socios 
          set q95_cgmpri = new.q02_numcgm
   	    where q95_cgmpri = old.q02_numcgm;
    end if;

  return new;
       
end;

\$function$

SQLD
        );

    }
}
