<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23826AjusteTriggerFcArrecadAlt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

--drop function fc_arrecad_alt();
create or replace function fc_arrecad_alt() returns trigger as 
$$
begin

-- Busca OLD
--  perform *
--     from arrenumcgm
--    where k00_numpre = old.k00_numpre
--      and k00_numcgm = old.k00_numcgm ;
--
--  if found then
--		delete 
--		  from arrenumcgm
--	   where k00_numpre = old.k00_numpre
--       and k00_numcgm = old.k00_numcgm ;
--	end if;
	
--  raise notice 'k00_numpre: % - k00_numcgm: %', new.k00_numpre, new.k00_numcgm;
	
  -- Busca NEW
--  perform distinct arrenumcgm.k00_numpre
--     from arrenumcgm
--	left join arrematric on arrematric.k00_numpre = arrenumcgm.k00_numpre
--	left join arreinscr  on arreinscr.k00_numpre  = arrenumcgm.k00_numpre
--    where arrenumcgm.k00_numpre = new.k00_numpre 
--      and arrenumcgm.k00_numcgm = new.k00_numcgm ;
--					and
--					(arrematric.k00_numpre is null or arreinscr.k00_numpre is null);
--  if not found then
--	  raise notice 'inserindo arrenumcgm - numpre: % - numcgm: %', new.k00_numpre, new.k00_numcgm;
--    insert 
--		into   arrenumcgm (k00_numpre, k00_numcgm)
--	  values (new.k00_numpre, new.k00_numcgm);
--	end if;
  update arrematric set k00_numpre = k00_numpre where k00_numpre = new.k00_numpre;
  update arreinscr set k00_numpre  = k00_numpre where k00_numpre = new.k00_numpre;

  
--  insert into debitos_cache values (NEW.k00_numpre, NEW.k00_numpar, 'arrecad', NEW.xmin, now(), TG_OP); 
  
  
  return new;
       
end;
$$ language 'plpgsql';

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
        DB::connection()->getPdo()->exec(<<<SQL

--drop function fc_arrecad_alt();
create or replace function fc_arrecad_alt() returns trigger as 
$$
begin

  -- Busca OLD
  perform *
     from arrenumcgm
    where k00_numpre = old.k00_numpre
      and k00_numcgm = old.k00_numcgm ;

  if found then
		delete 
		  from arrenumcgm
	   where k00_numpre = old.k00_numpre
       and k00_numcgm = old.k00_numcgm ;
	end if;
	
--  raise notice 'k00_numpre: % - k00_numcgm: %', new.k00_numpre, new.k00_numcgm;
	
  -- Busca NEW
  perform distinct arrenumcgm.k00_numpre
     from arrenumcgm
--	left join arrematric on arrematric.k00_numpre = arrenumcgm.k00_numpre
--	left join arreinscr  on arreinscr.k00_numpre  = arrenumcgm.k00_numpre
    where arrenumcgm.k00_numpre = new.k00_numpre 
      and arrenumcgm.k00_numcgm = new.k00_numcgm ;
--					and
--					(arrematric.k00_numpre is null or arreinscr.k00_numpre is null);
  if not found then
--	  raise notice 'inserindo arrenumcgm - numpre: % - numcgm: %', new.k00_numpre, new.k00_numcgm;
    insert 
		into   arrenumcgm (k00_numpre, k00_numcgm)
	  values (new.k00_numpre, new.k00_numcgm);
	end if;
  update arrematric set k00_numpre = k00_numpre where k00_numpre = new.k00_numpre;
  update arreinscr set k00_numpre  = k00_numpre where k00_numpre = new.k00_numpre;

  
--  insert into debitos_cache values (NEW.k00_numpre, NEW.k00_numpar, 'arrecad', NEW.xmin, now(), TG_OP); 
  
  
  return new;
       
end;
$$ language 'plpgsql';

SQL
        );
    }
}
