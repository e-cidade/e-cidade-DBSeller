<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23052AlteraTriggerFcCarloteIncAltExc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL
CREATE OR REPLACE FUNCTION cadastro.fc_carlote_inc_alt_exc()
  RETURNS TRIGGER AS
$$

    declare

        sLog              text default '';
        sLogAux           text default '';

        iCaract           integer;
        iLote             integer;
        iMatric           integer;
        iCodigoOcorrencia integer;

        sAnos             varchar;
        desc_new_carcter  varchar;
        desc_old_carcter  varchar;

        rLote             record;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP = 'INSERT' then
           sLog = 'NO(S) EXERCICIO(S) DE '||sAnos||' CARACTERISTICA DE LOTE INCLUÍDA: ';
           iCaract = new.j35_idbql ;
        elseif TG_OP = 'UPDATE' then
           sLog = 'NO(S) EXERCICIO(S) DE '||sAnos||' CARACTERISTICA DE LOTE ALTERADA: ';
           iCaract = new.j35_idbql ;
        elseif TG_OP = 'DELETE' then
           sLog = 'NO(S) EXERCICIO(S) DE '||sAnos||' CARACTERISTICA DE LOTE EXCLUÍDA: ';
           iCaract = old.j35_idbql ;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
            select j31_descr into desc_new_carcter from caracter where j31_codigo = new.j35_caract;
        elseif TG_OP = 'UPDATE' then
            select j31_descr into desc_new_carcter from caracter where j31_codigo = new.j35_caract;
            select j31_descr into desc_old_carcter from caracter where j31_codigo = old.j35_caract;
        elseif TG_OP = 'DELETE' then
            select j31_descr into desc_old_carcter from caracter where j31_codigo = old.j35_caract;
        end if;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'id_bql: ' || new.j35_idbql || ' - ';
           if new.j35_caract is not null then
               sLog = sLog || 'Caracteristica: ' || new.j35_caract || ' - ' || desc_new_carcter || ' - ';
           end if;
           if new.j35_dtlanc is not null then
               sLog = sLog || 'Data: ' || new.j35_dtlanc|| ' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j35_idbql <> old.j35_idbql then
               sLog = sLog || 'id_bql alterada de : ' || old.j35_idbql ||' - ' || ' para: ' || new.j35_idbql||' - ' ;
           end if;
           if new.j35_caract  <> old.j35_caract then
               sLog = sLog || ' caracteristica alterada de: ' || old.j35_caract || ' - ' || desc_old_carcter || ' - ';
               sLog = sLog || ' para: ' || new.j35_caract || ' - ' || desc_new_carcter || ' - ';
           end if;
           if new.j35_dtlanc <> old.j35_dtlanc or
              (old.j35_dtlanc is null and new.j35_dtlanc is not null) then
              if old.j35_dtlanc is null then
                 sLog = sLog || 'data alterada de: EM BRANCO para: ' || new.j35_dtlanc || ' - ';
              else
                 sLog = sLog || 'data alterada de: ' || old.j35_dtlanc || ' para: ' || new.j35_dtlanc || ' - ';
              end if;
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'id_bql excluida: ' || old.j35_idbql || ' - ';
           if old.j35_caract is not null then
               sLog = sLog || 'caracteristica exluida: ' || old.j35_caract|| ' - ' || desc_old_carcter || ' - ';
           end if;
           if old.j35_dtlanc is not null then
               sLog = sLog || 'data exluida: ' || old.j35_dtlanc || ' - ';
           end if;
        end if;

        if sLog <> sLogAux then

            SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

            insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                         ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                       578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5),
                       1, 'log de alteracoes', sLog;

            if TG_OP = 'INSERT' then
               iLote = new.j35_idbql;
            elseif TG_OP = 'UPDATE' then
               iLote = new.j35_idbql;
            elseif TG_OP = 'DELETE' then
               iLote = old.j35_idbql;
            end if;

            FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iLote
            LOOP

                insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                    select nextval('histocorrenciamatric_ar25_sequencial_seq'), rLote.j01_matric, currval('histocorrencia_ar23_sequencial_seq');
            END LOOP;

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

$$ LANGUAGE plpgsql;

-- fc_carlote_inc_alt_exc
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
CREATE OR REPLACE FUNCTION cadastro.fc_carlote_inc_alt_exc()
  RETURNS TRIGGER AS
$$

    declare

        sLog              text default '';
        sLogAux           text default '';

        iCaract           integer;
        iLote             integer;
        iMatric           integer;
        iCodigoOcorrencia integer;

        sAnos             varchar;
        desc_new_carcter  varchar;
        desc_old_carcter  varchar;

        rLote             record;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP = 'INSERT' then
           sLog = 'NO(S) EXERCICIO(S) DE '||sAnos||' CARACTERISTICA DE LOTE INCLUÍDA: ';
           iCaract = new.j35_idbql ;
        elseif TG_OP = 'UPDATE' then
           sLog = 'NO(S) EXERCICIO(S) DE '||sAnos||' CARACTERISTICA DE LOTE ALTERADA: ';
           iCaract = new.j35_idbql ;
        elseif TG_OP = 'DELETE' then
           sLog = 'NO(S) EXERCICIO(S) DE '||sAnos||' CARACTERISTICA DE LOTE EXCLUÍDA: ';
           iCaract = old.j35_idbql ;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
            select j31_descr into desc_new_carcter from caracter where j31_codigo = new.j35_caract;
        elseif TG_OP = 'UPDATE' then
            select j31_descr into desc_new_carcter from caracter where j31_codigo = new.j35_caract;
            select j31_descr into desc_old_carcter from caracter where j31_codigo = old.j35_caract;
        elseif TG_OP = 'DELETE' then
            select j31_descr into desc_old_carcter from caracter where j31_codigo = old.j35_caract;
        end if;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'id_bql: ' || new.j35_idbql || ' - ';
           if new.j35_caract is not null then
               sLog = sLog || 'Caracteristica: ' || new.j35_caract || ' - ' || desc_new_carcter || ' - ';
           end if;
           if new.j35_dtlanc is not null then
               sLog = sLog || 'Data: ' || new.j35_dtlanc|| ' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j35_idbql <> old.j35_idbql then
               sLog = sLog || 'id_bql alterada de : ' || old.j35_idbql ||' - ' || ' para: ' || new.j35_idbql||' - ' ;
           end if;
           if new.j35_caract  <> old.j35_caract then
               sLog = sLog || ' caracteristica alterada de: ' || old.j35_caract || ' - ' || desc_old_carcter ||;
               sLog = sLog || ' para: ' || new.j35_caract || ' - ' || desc_new_carcter || ' - ';
           end if;
           if new.j35_dtlanc <> old.j35_dtlanc or
              (old.j35_dtlanc is null and new.j35_dtlanc is not null) then
              if old.j35_dtlanc is null then
                 sLog = sLog || 'data alterada de: EM BRANCO para: ' || new.j35_dtlanc || ' - ';
              else
                 sLog = sLog || 'data alterada de: ' || old.j35_dtlanc || ' para: ' || new.j35_dtlanc || ' - ';
              end if;
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'id_bql excluida: ' || old.j35_idbql || ' - ';
           if old.j35_caract is not null then
               sLog = sLog || 'caracteristica exluida: ' || old.j35_caract|| ' - ' || desc_old_carcter || ' - ';
           end if;
           if old.j35_dtlanc is not null then
               sLog = sLog || 'data exluida: ' || old.j35_dtlanc || ' - ';
           end if;
        end if;

        if sLog <> sLogAux then

            SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

            insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                         ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                       578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5),
                       1, 'log de alteracoes', sLog;

            if TG_OP = 'INSERT' then
               iLote = new.j35_idbql;
            elseif TG_OP = 'UPDATE' then
               iLote = new.j35_idbql;
            elseif TG_OP = 'DELETE' then
               iLote = old.j35_idbql;
            end if;

            FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iLote
            LOOP

                insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                    select nextval('histocorrenciamatric_ar25_sequencial_seq'), rLote.j01_matric, currval('histocorrencia_ar23_sequencial_seq');
            END LOOP;

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

$$ LANGUAGE plpgsql;

SQL
        );
    }
}
