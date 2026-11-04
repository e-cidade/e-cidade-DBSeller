<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M20190AjusteTriggerOcorrenciasTabelaIptubase extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE OR REPLACE FUNCTION cadastro.fc_iptubase_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog                    text default '';
        sLogAux                 text default '';
        sCgmNovo                text;
        sCgmAntigo              text;
        sTipoProprietarioNovo   text;
        sTipoProprietarioAntigo text;

        sAnos                   varchar;

        iMatric                 integer;
        iCodigoOcorrencia       integer;

        bCampoProcesso          boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP     = 'INSERT' then
           sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' MATRICULA INCLUÍDA: ';
           iMatric   = new.j01_matric;
        elseif TG_OP = 'UPDATE' then
           sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' MATRICULA ALTERADA: ';
           iMatric   = new.j01_matric;
        elseif TG_OP = 'DELETE' then
           sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' MATRICULA EXCLUÍDA: ';
           iMatric   = old.j01_matric;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'matricula: ' || new.j01_matric  || ' - ';
           if new.j01_numcgm  is not null then
              select z01_nome into sCgmNovo from cgm where z01_numcgm = new.j01_numcgm;
              sLog = sLog || 'cgm: ' || new.j01_numcgm || ' - ' || sCgmNovo || ' - ';
           end if;
           if new.j01_idbql is not null then
              sLog = sLog || 'idbql: ' || new.j01_idbql    || ' - ';
           end if;
           if new.j01_baixa is not null then
              sLog = sLog || 'baixa: ' || new.j01_baixa    || ' - ';
           end if;
           if new.j01_codave is not null then
              sLog = sLog || 'codave: ' || new.j01_codave  || ' - ';
           end if;
           if new.j01_fracao is not null then
              sLog = sLog || 'fracao: ' || new.j01_fracao  || ' - ';
           end if;
           if new.j01_tipoimovel is not null then
              sLog = sLog || 'tipo imovel: ' || new.j01_tipoimovel  || ' - ';
           end if;
           if new.j01_distrito is not null then
              sLog = sLog || 'distrito: ' || new.j01_distrito  || ' - ';
           end if;
           if new.j01_hectare is not null then
              sLog = sLog || 'hectare: ' || new.j01_hectare  || ' - ';
           end if;
           if new.j01_situcad is not null then
              sLog = sLog || 'situacao cadastral: ' || new.j01_situcad  || ' - ';
           end if;
           if new.j01_datacad is not null then
              sLog = sLog || 'data de cadastro: ' || new.j01_datacad  || ' - ';
           end if;
           if new.j01_processo is not null then
              sLog = sLog || 'processo: ' || new.j01_processo  || ' - ';
           end if;
           if new.j01_incra is not null then
              sLog = sLog || 'incra: ' || new.j01_incra  || ' - ';
           end if;
           if new.j01_descrlocal is not null then
              sLog = sLog || 'descricao local: ' || new.j01_descrlocal  || ' - ';
           end if;
           if new.j01_unidade is not null then
              sLog = sLog || 'unidade: ' || new.j01_unidade  || ' - ';
           end if;
           if new.j01_tipoproprietario  is not null then
              select j163_descricao into sTipoProprietarioNovo from tipoproprietario where j163_tipoproprietario = new.j01_tipoproprietario;
              sLog = sLog || 'tipo de proprietario: ' || new.j01_tipoproprietario  || ' - ' || sTipoProprietarioNovo || ' - ';
           end if;

        elseif TG_OP = 'UPDATE' then
           if new.j01_matric  <> old.j01_matric  then
              sLog = sLog || 'matricula alterada de: ' ||old.j01_matric|| ' para: ' ||new.j01_matric|| ' - ';
           end if;
           if new.j01_numcgm  <> old.j01_numcgm  then
              select z01_nome into sCgmNovo from cgm where z01_numcgm = new.j01_numcgm;
              select z01_nome into sCgmAntigo from cgm where z01_numcgm = old.j01_numcgm;
              sLog = sLog || 'cgm alterado de: ' || old.j01_numcgm || ' - ' || sCgmAntigo || ' para: ' || new.j01_numcgm || ' - ' || sCgmNovo || ' - ';
           end if;
           if new.j01_idbql <> old.j01_idbql then
              sLog = sLog || 'idbql alterado de: ' || old.j01_idbql || ' para: ' || new.j01_idbql         || ' - ';
           end if;
           if (new.j01_baixa <> old.j01_baixa) or
              (old.j01_baixa is null and new.j01_baixa is not null) then
              if old.j01_baixa is null then
                 sLog = sLog || 'data de baixa alterada de: EM BRANCO para: ' || new.j01_baixa || ' - ';
              else
                 sLog = sLog || 'data de baixa alterada de: ' || old.j01_baixa || ' para: ' || new.j01_baixa || ' - ';
              end if;
           end if;
           if new.j01_codave <> old.j01_codave then
              sLog = sLog || 'codave alterada de: ' || old.j01_codave || ' para: ' || new.j01_codave      || ' - ';
           end if;
           if new.j01_fracao <> old.j01_fracao  then
              sLog = sLog || 'fracao alterada de: ' || old.j01_fracao || ' para: ' ||new.j01_fracao       || ' - ';
           end if;
           if new.j01_tipoimovel <> old.j01_tipoimovel then
              sLog = sLog || 'tipo imovel alterado de: ' || old.j01_tipoimovel || ' para: ' || new.j01_tipoimovel  || ' - ';
           end if;
           if new.j01_distrito <> old.j01_distrito then
              sLog = sLog || 'distrito alterado de: ' || old.j01_distrito || ' para: ' || new.j01_distrito  || ' - ';
           end if;
           if new.j01_hectare <> old.j01_hectare then
              sLog = sLog || 'hectare alterado de: ' || old.j01_hectare || ' para: ' || new.j01_hectare  || ' - ';
           end if;
           if new.j01_situcad <> old.j01_situcad then
              sLog = sLog || 'situacao cadastral alterada de: ' || old.j01_situcad || ' para: ' || new.j01_situcad  || ' - ';
           end if;
           if new.j01_datacad <> old.j01_datacad or 
              (old.j01_datacad is null and new.j01_datacad is not null) then
              sLog = sLog || 'data de cadastro alterada de: ';
              if old.j01_datacad is null then
                 sLog = sLog || 'EM BRANCO para: ' || new.j01_datacad  || ' - ';
              else
                 sLog = sLog || 'data de cadastro alterada de: ' || old.j01_datacad || ' para: ' || new.j01_datacad  || ' - ';
              end if;
           end if;
           if new.j01_processo <> old.j01_processo then
              sLog = sLog || 'processo alterado de: ' || old.j01_processo || ' para: ' || new.j01_processo  || ' - ';
           end if;
           if new.j01_incra <> old.j01_incra then
              sLog = sLog || 'incra alterado de: ' || old.j01_incra || ' para: ' || new.j01_incra  || ' - ';
           end if;
           if (new.j01_descrlocal <> old.j01_descrlocal) or
              (old.j01_descrlocal is null and new.j01_descrlocal is not null) then
              if old.j01_descrlocal is null then
                 sLog = sLog || 'descricao local alterada de: EM BRANCO para: ' || new.j01_descrlocal  || ' - ';
              else
                 sLog = sLog || 'descricao local alterada de: ' || old.j01_descrlocal || ' para: ' || new.j01_descrlocal  || ' - ';
              end if;
           end if;
           if new.j01_unidade <> old.j01_unidade then
              sLog = sLog || 'unidade alterada de: ' || old.j01_unidade || ' para: ' || new.j01_unidade  || ' - ';
           end if;
           if new.j01_tipoproprietario  <> old.j01_tipoproprietario  then
               select j163_descricao into sTipoProprietarioNovo from tipoproprietario where j163_tipoproprietario = new.j01_tipoproprietario;
               select j163_descricao into sTipoProprietarioAntigo from tipoproprietario where j163_tipoproprietario = old.j01_tipoproprietario;
               sLog = sLog || 'tipo de proprietário alterado de: ' || old.j01_tipoproprietario || ' - ' || sTipoProprietarioAntigo || ' para: ' ||new.j01_tipoproprietario || ' - ' || sTipoProprietarioNovo || ' - ';
           end if;          
           if new.j01_fracaoproprietario <> old.j01_fracaoproprietario then
              sLog = sLog || 'fração por proprietário alterada de: ' || old.j01_fracaoproprietario || ' para: ' || new.j01_fracaoproprietario  || ' - ';
           end if;

        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'matricula: ' || old.j01_matric      || ' - ';
           if old.j01_numcgm  is not null then
           select z01_nome into sCgmAntigo from cgm where z01_numcgm = old.j01_numcgm;
               sLog = sLog || 'cgm: ' || old.j01_numcgm || ' - ' || sCgmAntigo || ' - ';
           end if;
           if old.j01_idbql  is not null then
               sLog = sLog || 'idbql: ' || old.j01_idbql        || ' - ';
           end if;
           if old.j01_baixa is not null then
               sLog = sLog || 'area privada: ' || old.j01_baixa || ' - ';
           end if;
           if old.j01_codave is not null then
               sLog = sLog || 'codave: ' || old.j01_codave      || ' - ';
           end if;
           if old.j01_fracao  is not null then
               sLog = sLog || 'fracao: ' || old.j01_fracao      || ' - ';
           end if;
           if old.j01_tipoimovel is not null then
              sLog = sLog || 'tipo imovel: ' || old.j01_tipoimovel  || ' - ';
           end if;
           if old.j01_distrito is not null then
              sLog = sLog || 'distrito: ' || old.j01_distrito  || ' - ';
           end if;
           if old.j01_hectare is not null then
              sLog = sLog || 'hectare: ' || old.j01_hectare  || ' - ';
           end if;
           if old.j01_situcad is not null then
              sLog = sLog || 'situacao cadastral: ' || old.j01_situcad  || ' - ';
           end if;
           if old.j01_datacad is not null then
              sLog = sLog || 'data de cadastro: ' || old.j01_datacad  || ' - ';
           end if;
           if old.j01_processo is not null then
              sLog = sLog || 'processo: ' || old.j01_processo  || ' - ';
           end if;
           if old.j01_incra is not null then
              sLog = sLog || 'incra: ' || old.j01_incra  || ' - ';
           end if;
           if old.j01_descrlocal is not null then
              sLog = sLog || 'descricao local: ' || old.j01_descrlocal  || ' - ';
           end if;
           if old.j01_unidade is not null then
              sLog = sLog || 'unidade: ' || old.j01_unidade  || ' - ';
           end if;
           if old.j01_tipoproprietario  is not null then
               select j163_descricao into sTipoProprietarioAntigo from tipoproprietario where j163_tipoproprietario = old.j01_tipoproprietario;
               sLog = sLog || 'tipo de proprietario: ' || old.j01_tipoproprietario || ' - ' || sTipoProprietarioAntigo || ' - ';
           end if;
        end if;

        if sLogAux <> sLog then
            SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;
            insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                         ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                       578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5),
                       1, 'log de alteracoes', sLog;
            insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                select nextval('histocorrenciamatric_ar25_sequencial_seq'), iMatric, iCodigoOcorrencia;
            IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia )
                    select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
            END IF;
        end if;

        if TG_OP = 'INSERT' then
           return new;
        elseif TG_OP = 'UPDATE' then
           return new;
        elseif TG_OP = 'DELETE' then
           return old;
        end if;
    end;
\$function\$;
SQL;

        DB::connection()->getPdo()->exec($sql);
    }

    public function down()
    {
        $sql = <<<SQL
CREATE OR REPLACE FUNCTION cadastro.fc_iptubase_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog                    text default '';
        sLogAux                 text default '';
        sCgmNovo                text;
        sCgmAntigo              text;
        sTipoProprietarioNovo   text;
        sTipoProprietarioAntigo text;

        sAnos                   varchar;

        iMatric                 integer;
        iCodigoOcorrencia       integer;

        bCampoProcesso          boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP     = 'INSERT' then
           sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' MATRICULA INCLUÍDA: ';
           iMatric   = new.j01_matric;
        elseif TG_OP = 'UPDATE' then
           sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' MATRICULA ALTERADA: ';
           iMatric   = new.j01_matric;
        elseif TG_OP = 'DELETE' then
           sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' MATRICULA EXCLUÍDA: ';
           iMatric   = old.j01_matric;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'matricula: ' || new.j01_matric  || ' - ';
           if new.j01_numcgm  is not null then
              select z01_nome into sCgmNovo from cgm where z01_numcgm = new.j01_numcgm;
              sLog = sLog || 'cgm: ' || new.j01_numcgm || ' - ' || sCgmNovo || ' - ';
           end if;
           if new.j01_idbql is not null then
              sLog = sLog || 'idbql: ' || new.j01_idbql    || ' - ';
           end if;
           if new.j01_baixa is not null then
              sLog = sLog || 'baixa: ' || new.j01_baixa    || ' - ';
           end if;
           if new.j01_codave is not null then
              sLog = sLog || 'codave: ' || new.j01_codave  || ' - ';
           end if;
           if new.j01_fracao is not null then
              sLog = sLog || 'fracao: ' || new.j01_fracao  || ' - ';
           end if;
           if new.j01_tipoimovel is not null then
              sLog = sLog || 'tipo imovel: ' || new.j01_tipoimovel  || ' - ';
           end if;
           if new.j01_distrito is not null then
              sLog = sLog || 'distrito: ' || new.j01_distrito  || ' - ';
           end if;
           if new.j01_hectare is not null then
              sLog = sLog || 'hectare: ' || new.j01_hectare  || ' - ';
           end if;
           if new.j01_situcad is not null then
              sLog = sLog || 'situacao cadastral: ' || new.j01_situcad  || ' - ';
           end if;
           if new.j01_datacad is not null then
              sLog = sLog || 'data de cadastro: ' || new.j01_datacad  || ' - ';
           end if;
           if new.j01_processo is not null then
              sLog = sLog || 'processo: ' || new.j01_processo  || ' - ';
           end if;
           if new.j01_incra is not null then
              sLog = sLog || 'incra: ' || new.j01_incra  || ' - ';
           end if;
           if new.j01_descrlocal is not null then
              sLog = sLog || 'descricao local: ' || new.j01_descrlocal  || ' - ';
           end if;
           if new.j01_unidade is not null then
              sLog = sLog || 'unidade: ' || new.j01_unidade  || ' - ';
           end if;
           /*
           if new.j01_tipoproprietario  is not null then
              select j163_descricao into sTipoProprietarioNovo from tipoproprietario where j163_tipoproprietario = new.j01_tipoproprietario;
              sLog = sLog || 'tipo de proprietario: ' || new.j01_tipoproprietario  || ' - ' || sTipoProprietarioNovo || ' - ';
           end if;
           */
        elseif TG_OP = 'UPDATE' then
           if new.j01_matric  <> old.j01_matric  then
              sLog = sLog || 'matricula alterada de: ' ||old.j01_matric|| ' para: ' ||new.j01_matric|| ' - ';
           end if;
           if new.j01_numcgm  <> old.j01_numcgm  then
              select z01_nome into sCgmNovo from cgm where z01_numcgm = new.j01_numcgm;
              select z01_nome into sCgmAntigo from cgm where z01_numcgm = old.j01_numcgm;
              sLog = sLog || 'cgm alterado de: ' || old.j01_numcgm || ' - ' || sCgmAntigo || ' para: ' || new.j01_numcgm || ' - ' || sCgmNovo || ' - ';
           end if;
           if new.j01_idbql <> old.j01_idbql then
              sLog = sLog || 'idbql alterado de: ' || old.j01_idbql || ' para: ' || new.j01_idbql         || ' - ';
           end if;
           if (new.j01_baixa <> old.j01_baixa) or
              (old.j01_baixa is null and new.j01_baixa is not null) then
              if old.j01_baixa is null then
                 sLog = sLog || 'data de baixa alterada de: EM BRANCO para: ' || new.j01_baixa || ' - ';
              else
                 sLog = sLog || 'data de baixa alterada de: ' || old.j01_baixa || ' para: ' || new.j01_baixa || ' - ';
              end if;
           end if;
           if new.j01_codave <> old.j01_codave then
              sLog = sLog || 'codave alterada de: ' || old.j01_codave || ' para: ' || new.j01_codave      || ' - ';
           end if;
           if new.j01_fracao <> old.j01_fracao  then
              sLog = sLog || 'fracao alterada de: ' || old.j01_fracao || ' para: ' ||new.j01_fracao       || ' - ';
           end if;
           if new.j01_tipoimovel <> old.j01_tipoimovel then
              sLog = sLog || 'tipo imovel alterado de: ' || old.j01_tipoimovel || ' para: ' || new.j01_tipoimovel  || ' - ';
           end if;
           if new.j01_distrito <> old.j01_distrito then
              sLog = sLog || 'distrito alterado de: ' || old.j01_distrito || ' para: ' || new.j01_distrito  || ' - ';
           end if;
           if new.j01_hectare <> old.j01_hectare then
              sLog = sLog || 'hectare alterado de: ' || old.j01_hectare || ' para: ' || new.j01_hectare  || ' - ';
           end if;
           if new.j01_situcad <> old.j01_situcad then
              sLog = sLog || 'situacao cadastral alterada de: ' || old.j01_situcad || ' para: ' || new.j01_situcad  || ' - ';
           end if;
           if new.j01_datacad <> old.j01_datacad or 
              (old.j01_datacad is null and new.j01_datacad is not null) then
              sLog = sLog || 'data de cadastro alterada de: ';
              if old.j01_datacad is null then
                 sLog = sLog || 'EM BRANCO para: ' || new.j01_datacad  || ' - ';
              else
                 sLog = sLog || 'data de cadastro alterada de: ' || old.j01_datacad || ' para: ' || new.j01_datacad  || ' - ';
              end if;
           end if;
           if new.j01_processo <> old.j01_processo then
              sLog = sLog || 'processo alterado de: ' || old.j01_processo || ' para: ' || new.j01_processo  || ' - ';
           end if;
           if new.j01_incra <> old.j01_incra then
              sLog = sLog || 'incra alterado de: ' || old.j01_incra || ' para: ' || new.j01_incra  || ' - ';
           end if;
           if (new.j01_descrlocal <> old.j01_descrlocal) or
              (old.j01_descrlocal is null and new.j01_descrlocal is not null) then
              if old.j01_descrlocal is null then
                 sLog = sLog || 'descricao local alterada de: EM BRANCO para: ' || new.j01_descrlocal  || ' - ';
              else
                 sLog = sLog || 'descricao local alterada de: ' || old.j01_descrlocal || ' para: ' || new.j01_descrlocal  || ' - ';
              end if;
           end if;
           if new.j01_unidade <> old.j01_unidade then
              sLog = sLog || 'unidade alterada de: ' || old.j01_unidade || ' para: ' || new.j01_unidade  || ' - ';
           end if;
           /*
           if new.j01_tipoproprietario  <> old.j01_tipoproprietario  then
               select j163_descricao into sTipoProprietarioNovo from tipoproprietario where j163_tipoproprietario = new.j01_tipoproprietario;
               select j163_descricao into sTipoProprietarioAntigo from tipoproprietario where j163_tipoproprietario = old.j01_tipoproprietario;
               sLog = sLog || 'tipo de proprietario alterado de: ' || old.j01_tipoproprietario || ' - ' || sTipoProprietarioAntigo || ' para: ' ||new.j01_tipoproprietario || ' - ' || sTipoProprietarioNovo || ' - ';
           end if;
           */
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'matricula: ' || old.j01_matric      || ' - ';
           if old.j01_numcgm  is not null then
           select z01_nome into sCgmAntigo from cgm where z01_numcgm = old.j01_numcgm;
               sLog = sLog || 'cgm: ' || old.j01_numcgm || ' - ' || sCgmAntigo || ' - ';
           end if;
           if old.j01_idbql  is not null then
               sLog = sLog || 'idbql: ' || old.j01_idbql        || ' - ';
           end if;
           if old.j01_baixa is not null then
               sLog = sLog || 'area privada: ' || old.j01_baixa || ' - ';
           end if;
           if old.j01_codave is not null then
               sLog = sLog || 'codave: ' || old.j01_codave      || ' - ';
           end if;
           if old.j01_fracao  is not null then
               sLog = sLog || 'fracao: ' || old.j01_fracao      || ' - ';
           end if;
           if old.j01_tipoimovel is not null then
              sLog = sLog || 'tipo imovel: ' || old.j01_tipoimovel  || ' - ';
           end if;
           if old.j01_distrito is not null then
              sLog = sLog || 'distrito: ' || old.j01_distrito  || ' - ';
           end if;
           if old.j01_hectare is not null then
              sLog = sLog || 'hectare: ' || old.j01_hectare  || ' - ';
           end if;
           if old.j01_situcad is not null then
              sLog = sLog || 'situacao cadastral: ' || old.j01_situcad  || ' - ';
           end if;
           if old.j01_datacad is not null then
              sLog = sLog || 'data de cadastro: ' || old.j01_datacad  || ' - ';
           end if;
           if old.j01_processo is not null then
              sLog = sLog || 'processo: ' || old.j01_processo  || ' - ';
           end if;
           if old.j01_incra is not null then
              sLog = sLog || 'incra: ' || old.j01_incra  || ' - ';
           end if;
           if old.j01_descrlocal is not null then
              sLog = sLog || 'descricao local: ' || old.j01_descrlocal  || ' - ';
           end if;
           if old.j01_unidade is not null then
              sLog = sLog || 'unidade: ' || old.j01_unidade  || ' - ';
           end if;
           /*
           if old.j01_tipoproprietario  is not null then
               select j163_descricao into sTipoProprietarioAntigo from tipoproprietario where j163_tipoproprietario = old.j01_tipoproprietario;
               sLog = sLog || 'tipo de proprietario: ' || old.j01_tipoproprietario || ' - ' || sTipoProprietarioAntigo || ' - ';
           end if;
           */
        end if;

        if sLogAux <> sLog then
            SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;
            insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                         ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                       578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5),
                       1, 'log de alteracoes', sLog;
            insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                select nextval('histocorrenciamatric_ar25_sequencial_seq'), iMatric, iCodigoOcorrencia;
            IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia )
                    select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
            END IF;
        end if;

        if TG_OP = 'INSERT' then
           return new;
        elseif TG_OP = 'UPDATE' then
           return new;
        elseif TG_OP = 'DELETE' then
           return old;
        end if;
    end;
\$function\$;
SQL;

        DB::connection()->getPdo()->exec($sql);
    }
}
