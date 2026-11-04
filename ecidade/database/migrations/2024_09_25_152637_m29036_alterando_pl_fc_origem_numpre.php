<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M29036AlterandoPlFcOrigemNumpre extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
create or replace function fc_origem_numpre(integer, integer, text) returns text as
$$
declare
  iNumpre  alias for $1;
  iLimite  alias for $2;
  sOutros  alias for $3;

  sOrigemCgm    text;
  sOrigemInscr  text;
  sOrigemMatric text;
  sRetorno      text;

  sVirgula text default '';
  rNumpre  record;

  iConta   integer default 0;
  lOutros  boolean default false;

begin
  -- Origens do CGM
  sVirgula   := '';
  sOrigemCgm := '';
  iConta     := 0;
  lOutros    := false;
  for rNumpre in
    select distinct
           k00_numcgm
      from arrenumcgm
     where k00_numpre = iNumpre
  loop
    iConta     := iConta + 1;

    if lOutros is false then
      sOrigemCgm := sOrigemCgm || sVirgula || rNumpre.k00_numcgm;
    end if;

    if iLimite is not null then
      if iConta >= iLimite then
        lOutros := true;
      end if;
    end if;

    sVirgula   := ', ';
  end loop;

  if found and iConta > iLimite and lOutros is true then
    sOrigemCgm := sOrigemCgm || sOutros;
  end if;

  -- Origens das Matriculas
  sVirgula      := '';
  sOrigemMatric := '';
  iConta        := 0;
  lOutros       := false;
  for rNumpre in
    select distinct
           k00_matric
      from arrematric
     where k00_numpre = iNumpre
  loop
    iConta        := iConta + 1;

    if lOutros is false then
      sOrigemMatric := sOrigemMatric || sVirgula || rNumpre.k00_matric;
    end if;

    if iLimite is not null then
      if iConta >= iLimite then
        lOutros := true;
      end if;
    end if;

    sVirgula      := ', ';
  end loop;

  if found and iConta > iLimite and lOutros is true then
    sOrigemMatric := sOrigemMatric || sOutros;
  end if;

  sVirgula     := '';
  sOrigemInscr := '';
  iConta       := 0;
  lOutros      := false;
  for rNumpre in
    select distinct
           k00_inscr
      from arreinscr
     where k00_numpre = iNumpre
  loop
    iConta       := iConta + 1;

    if lOutros is false then
      sOrigemInscr := sOrigemInscr || sVirgula || rNumpre.k00_inscr;
    end if;

    if iLimite is not null then
      if iConta >= iLimite then
        lOutros := true;
      end if;
    end if;

    sVirgula     := ', ';
  end loop;

  if found and iConta > iLimite and lOutros is true then
    sOrigemInscr := sOrigemInscr || sOutros;
  end if;


  sOrigemCgm    := nullif(sOrigemCgm, '');
  sOrigemMatric := nullif(sOrigemMatric, '');
  sOrigemInscr  := nullif(sOrigemInscr, '');


  sRetorno := coalesce('C: '||sOrigemCgm||'  ', '') ||
              coalesce('M: '||sOrigemMatric||'  ', '') ||
              coalesce('I: '||sOrigemInscr||'  ', '');

  return sRetorno;
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
        DB::unprepared(<<<SQL
create or replace function fc_origem_numpre(integer, integer, text) returns text as
$$
declare
  iNumpre  alias for $1;
  iLimite  alias for $2;
  sOutros  alias for $3;

  sOrigemCgm    text;
  sOrigemInscr  text;
  sOrigemMatric text;
  sRetorno      text;

  sVirgula text default '';
  rNumpre  record;

  iConta   integer default 0;
  lOutros  boolean default false;

begin
  -- Origens do CGM
  sVirgula   := '';
  sOrigemCgm := '';
  iConta     := 0;
  lOutros    := false;
  for rNumpre in
    select distinct
           k00_numcgm
      from arrenumcgm
     where k00_numpre = iNumpre
  loop
    iConta     := iConta + 1;

    if lOutros is false then
      sOrigemCgm := sOrigemCgm || sVirgula || rNumpre.k00_numcgm;
    end if;

    if iLimite is not null then
      if iConta >= iLimite then
        lOutros := true;
      end if;
    end if;

    sVirgula   := ', ';
  end loop;

  if found and iConta > iLimite and lOutros is true then
    sOrigemCgm := sOrigemCgm || sOutros;
  end if;

  -- Origens das Matriculas
  sVirgula      := '';
  sOrigemMatric := '';
  iConta        := 0;
  lOutros       := false;
  for rNumpre in
    select distinct
           k00_matric
      from arrematric
     where k00_numpre = iNumpre
  loop
    iConta        := iConta + 1;

    if lOutros is false then
      sOrigemMatric := sOrigemMatric || sVirgula || rNumpre.k00_matric;
    end if;

    if iLimite is not null then
      if iConta >= iLimite then
        lOutros := true;
      end if;
    end if;

    sVirgula      := ', ';
  end loop;

  if found and iConta > iLimite and lOutros is true then
    sOrigemMatric := sOrigemMatric || sOutros;
  end if;

  sVirgula     := '';
  sOrigemInscr := '';
  iConta       := 0;
  lOutros      := true;
  for rNumpre in
    select distinct
           k00_inscr
      from arreinscr
     where k00_numpre = iNumpre
  loop
    iConta       := iConta + 1;

    if lOutros is false then
      sOrigemInscr := sOrigemInscr || sVirgula || rNumpre.k00_inscr;
    end if;

    if iLimite is not null then
      if iConta >= iLimite then
        lOutros := true;
      end if;
    end if;

    sVirgula     := ', ';
  end loop;

  if found and iConta > iLimite and lOutros is true then
    sOrigemInscr := sOrigemInscr || sOutros;
  end if;


  sOrigemCgm    := nullif(sOrigemCgm, '');
  sOrigemMatric := nullif(sOrigemMatric, '');
  sOrigemInscr  := nullif(sOrigemInscr, '');


  sRetorno := coalesce('C: '||sOrigemCgm||'  ', '') ||
              coalesce('M: '||sOrigemMatric||'  ', '') ||
              coalesce('I: '||sOrigemInscr||'  ', '');

  return sRetorno;
end;
$$ language 'plpgsql';
SQL
        );
    }
}
