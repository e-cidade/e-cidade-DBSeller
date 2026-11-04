<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M18291OcorrenciasTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec(<<<SQL

CREATE OR REPLACE FUNCTION cadastro.fc_carconstr_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog              text default '';
        sLogAux           text default '';

        sAnos             varchar;
        desc_new_carcter  varchar;
        desc_old_carcter  varchar;

        iMatric           integer;
        iCodigoOcorrencia integer;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP = 'INSERT' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' CARACTERISTICA DA CONSTRUCAO INCLUÍDA: ';
           iMatric = new.j48_matric ;
        elseif TG_OP = 'UPDATE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' CARACTERISTICA DA CONSTRUCAO ALTERADA: ';
           iMatric = new.j48_matric ;
        elseif TG_OP = 'DELETE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' CARACTERISTICA DA CONSTRUCAO EXCLUÍDA: ';
           iMatric = old.j48_matric ;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
            select j31_descr into desc_new_carcter from caracter where j31_codigo = new.j48_caract;
        elseif TG_OP = 'UPDATE' then
            select j31_descr into desc_new_carcter from caracter where j31_codigo = new.j48_caract;
            select j31_descr into desc_old_carcter from caracter where j31_codigo = old.j48_caract;
        elseif TG_OP = 'DELETE' then
            select j31_descr into desc_old_carcter from caracter where j31_codigo = old.j48_caract;
        end if;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'matricula: ' || new.j48_matric || ' - ';
           if new.j48_idcons is not null then
               sLog = sLog || 'id: ' || new.j48_idcons|| ' - ';
           end if;
           if new.j48_caract is not null then
               sLog = sLog || 'caracteristica incluida: ' || new.j48_caract || ' - ' || desc_new_carcter || ' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j48_matric <> old.j48_matric then
               sLog = sLog || 'matricula alterada de : ' || old.j48_matric || ' para: ' || new.j48_matric||' - ';
           end if;
           if new.j48_idcons <> old.j48_idcons then
               sLog = sLog || 'id alterado de: ' || old.j48_idcons || ' para: ' || new.j48_idcons || ' - ';
           end if;
           if new.j48_caract  <> old.j48_caract then
               sLog = sLog || ' caracteristica alterada de: ' || old.j48_caract || ' - ' ||desc_old_carcter ||
                      ' para: ' || new.j48_caract ||' - ' || desc_new_carcter || ' - ';
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'matricula: ' || old.j48_matric || ' - ';
           if old.j48_idcons is not null then
               sLog = sLog || 'id: ' || old.j48_idcons || ' - ';
           end if;
           if old.j48_caract is not null then
               sLog = sLog || 'caracteristica exluida: ' || old.j48_caract|| ' - ' || desc_old_carcter || ' - ';
           end if;
        end if;

        if sLog <> sLogAux then
            SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;
            insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                         ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                       578, fc_getsession('DB_itemmenu_acessado')::integer, current_date,
                       substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

            insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                select nextval('histocorrenciamatric_ar25_sequencial_seq'), iMatric, iCodigoOcorrencia;

            IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia )
                    select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),
                           fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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


CREATE OR REPLACE FUNCTION cadastro.fc_carlote_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

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
\$function\$;


CREATE OR REPLACE FUNCTION cadastro.fc_constrcar_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog              text default '';
        sLogAux           text default '';

        sAnos             varchar;

        iMatric           integer;
        iCodigoOcorrencia integer;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP = 'INSERT' then
           sLog = 'ESCRITURADAS INCLUÍDA: ';
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' ESCRITURADAS INCLUÍDA: ';
           iMatric = new.j53_matric;
        elseif TG_OP = 'UPDATE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' ESCRITURADAS ALTERADA: ';
           iMatric = new.j53_matric;
        elseif TG_OP = 'DELETE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' ESCRITURADAS EXCLUÍDA: ';
           iMatric = old.j53_matric;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'Matricula: ' || new.j53_matric || ' - ';
           if new.j53_idcons  is not null then
              sLog = sLog || 'Codigo construcao : ' || new.j53_idcons ||' - ';
           end if;
           if new.j53_caract is not null then
              sLog = sLog || 'Caracteristica: ' || new.j53_caract || ' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j53_matric  <> old.j53_matric  then
              sLog = sLog || 'Matricula alterada de: '||old.j53_matric||' para: '||new.j53_matric|| ' - ';
           end if;
           if new.j53_idcons  <> old.j53_idcons  then
              sLog = sLog || 'Codigo construcao : '|| old.j53_idcons ||' para: '|| new.j53_idcons || ' - ';
           end if;
           if new.j53_caract <> old.j53_caract then
              sLog = sLog || 'Caracteristica: '||old.j53_caract ||' para: '||new.j53_caract || ' - ';
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'Matricula: ' || old.j53_matric || ' - ';
           if old.j53_idcons  is not null then
              sLog = sLog || 'Codigo construcao : ' || old.j53_idcons || ' - ';
           end if;
           if old.j53_caract is not null then
              sLog = sLog || 'Caracteristica:'|| old.j53_caract|| '-';
           end if;
        end if;

        if sLogAux <> sLog then
           SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;
           insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                        ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer,
                       fc_getsession('DB_instit')::integer, 578, fc_getsession('DB_itemmenu_acessado')::integer,
                       current_date, substr(current_time::varchar,1,5),
                       1, 'log de alteracoes', sLog;

           insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                select nextval('histocorrenciamatric_ar25_sequencial_seq'), iMatric, iCodigoOcorrencia;

           IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
              insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia )
                select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG'),iCodigoOcorrencia;
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

CREATE OR REPLACE FUNCTION cadastro.fc_constrescr_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog              text default '';
        sLogAux           text default '';

        sAnos             varchar;

        iMatric           integer;
        iCodigoOcorrencia integer;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP = 'INSERT' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' ESCRITURADA INCLUÍDA: ';
           iMatric = new.j52_matric;
        elseif TG_OP = 'UPDATE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' ESCRITURADA ALTERADA: ';
           iMatric = new.j52_matric;
        elseif TG_OP = 'DELETE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' ESCRITURADA EXCLUÍDA: ';
           iMatric = old.j52_matric;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'Matricula: ' || new.j52_matric || ' - ';
           if new.j52_idcons  is not null then
              sLog = sLog || 'Id: ' || new.j52_idcons ||' - ';
           end if;
           if new.j52_ano is not null then
              sLog = sLog || 'Ano construcao: ' || new.j52_ano || ' - ';
           end if;
           if new.j52_area is not null then
              sLog = sLog || 'Area construida: ' || new.j52_area || ' - ';
           end if;
           if new.j52_areap is not null then
              sLog = sLog || 'Area privada: ' || new.j52_areap || ' - ';
           end if;
           if new.j52_dtlan  is not null then
              sLog = sLog || 'Data Lancamento: ' || new.j52_dtlan || ' - ';
           end if;
           if new.j52_codigo is not null then
              sLog = sLog || 'Codigo: ' || new.j52_codigo || '-';
           end if;
           if new.j52_numero is not null then
              sLog = sLog || 'Numero: '|| new.j52_numero||'-';
           end if;
           if new.j52_compl is not null then
              sLog = sLog || 'Complemento: '|| new.j52_compl||'-';
           end if;
           if new.j52_dtdemo is not null then
              sLog = sLog || 'Data de Demolicao: '||new.j52_dtdemo||'-';
           end if;
           if new.j52_idaument is not null then
              sLog = sLog || 'Construcao Principal: '||new.j52_idaument||'-';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j52_matric  <> old.j52_matric  then
              sLog = sLog || 'matricula alterada de: '||old.j52_matric||' para: '||new.j52_matric|| ' - ';
           end if;
           if new.j52_idcons  <> old.j52_idcons  then
              sLog = sLog || 'id de construcao alterado de: '|| old.j52_idcons ||' para: '|| new.j52_idcons || ' - ';
           end if;
           if new.j52_ano <> old.j52_ano then
              sLog = sLog || 'ano alterada de: '||old.j52_ano ||' para: '||new.j52_ano || ' - ';
           end if;
           if new.j52_area <> old.j52_area then
              sLog = sLog ||'area alterada de: '||old.j52_area||' para: ' || new.j52_area|| ' - ';
           end if;
           if new.j52_areap <> old.j52_areap then
              sLog = sLog ||'areap alterada de: '||old.j52_areap||' para: '||new.j52_areap|| ' - ';
           end if;
           if (new.j52_dtlan <> old.j52_dtlan) or
              (new.j52_dtlan is null and old.j52_dtlan is not null) or
              (new.j52_dtlan is not null and old.j52_dtlan is null) then
              if old.j52_dtlan is null then
                 sLog = sLog || 'data de lancamento alterada de: EM BRANCO para: ' || new.j52_dtlan || ' - ';
              elseif new.j52_dtlan is null then
                 sLog = sLog || 'data de lancamento alterada de: ' || old.j52_dtlan || ' para: EM BRANCO - ';
              else
                 sLog = sLog || 'data de lancamento alterada de: ' || old.j52_dtlan || ' para: ' || new.j52_dtlan || ' - ';
              end if;
           end if;
           if new.j52_codigo <> old.j52_codigo then
              sLog = sLog || 'Codigo alterado de: ' || old.j52_codigo || 'para:' || new.j52_codigo || '-';
           end if;
           if new.j52_numero <> old.j52_numero then
              sLog = sLog || 'Numero alterado de: ' || old.j52_numero || 'para:' || new.j52_numero || '-';
           end if;
           if new.j52_compl <> old.j52_compl then
              sLog = sLog || 'Complemento alterado de: ' || old.j52_compl || 'para:' || new.j52_compl || '-';
           end if;
           if (new.j52_dtdemo <> old.j52_dtdemo) or
              (new.j52_dtdemo is null and old.j52_dtdemo is not null) or
              (new.j52_dtdemo is not null and old.j52_dtdemo is null) then
              if old.j52_dtdemo is null then
                 sLog = sLog || ' data de demolicao alterada de: EM BRANCO para: ' || new.j52_dtdemo || ' - ';
              elseif new.j52_dtdemo is null then
                 sLog = sLog || ' data de demolicao alterada de: ' || old.j52_dtdemo || ' para: EM BRANCO - ';
              else
                 sLog = sLog || ' data de demolicao alterada de: ' || old.j52_dtdemo || ' para: ' || new.j52_dtdemo || ' - ';
              end if;
           end if;
           if new.j52_idaument <> old.j52_idaument then
              sLog = sLog || 'Id aument alterado de: ' || old.j52_idaument || ' para: ' || new.j52_idaument || '-';
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'matricula: ' || old.j52_matric || ' - ';
           if new.j52_idcons  is not null then
              sLog = sLog || 'Id: ' || new.j52_idcons ||' - ';
           end if;
           if new.j52_ano is not null then
              sLog = sLog || 'Ano construcao: ' || new.j52_ano || ' - ';
           end if;
           if new.j52_area is not null then
              sLog = sLog || 'Area construida: ' || new.j52_area || ' - ';
           end if;
           if new.j52_areap is not null then
              sLog = sLog || 'Area privada: ' || new.j52_areap || ' - ';
           end if;
           if new.j52_dtlan  is not null then
              sLog = sLog || 'Data Lancamento: ' || new.j52_dtlan || ' - ';
           end if;
           if old.j52_codigo  is not null then
              sLog = sLog || 'Codigo: ' || old.j52_codigo || ' - ';
           end if;
           if old.j52_numero is not null then
              sLog = sLog || 'Numero: '|| old.j52_compl||'-';
           end if;
           if old.j52_compl is not null then
              sLog = sLog || 'Complemento: '||old.j52_compl|| '-';
           end if;
           if old.j52_dtdemo is not null then
              sLog = sLog || 'Data de Demolicao: '||old.j52_dtdemo||'-';
           end if;
           if old.j52_idaument is not null then
              sLog = sLog || 'Construcao Principal: '|| old.j52_idaument||'-';
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

CREATE OR REPLACE FUNCTION cadastro.fc_imobil_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog              text default '';
        sLogAux           text default '';

        sAnos             varchar;

        iMatric           integer;
        iIdbql            integer;
        iCodigoOcorrencia integer;

        rLote             record;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP = 'INSERT' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' IMOBILIARIA INCLUÍDA: ';
           iMatric = new.j44_matric ;
        elseif TG_OP = 'UPDATE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' IMOBILIARIA ALTERADA: ';
           iMatric = new.j44_matric ;
        elseif TG_OP = 'DELETE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' IMOBILIARIA EXCLUÍDA: ';
           iMatric = old.j44_matric ;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'Matricula: ' || new.j44_matric ||' - ';
           if new.j44_numcgm   is not null then
              sLog = sLog || 'CGM: ' || new.j44_numcgm || ' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j44_matric   <> old.j44_matric   then
              sLog = sLog || 'Matricula alterada de: '||old.j44_matric ||' para: '||new.j44_matric || ' - ';
           end if;
           if new.j44_numcgm  <> old.j44_numcgm  then
              sLog = sLog || 'CGM alterado de: '||old.j44_numcgm||' para: '||new.j44_numcgm|| ' - ';
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'Matricula: ' || old.j44_matric ||' - ';
           if old.j44_numcgm  is not null then
              sLog = sLog || 'CGM: ' || old.j44_numcgm || ' - ';
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

CREATE OR REPLACE FUNCTION cadastro.fc_iptuant_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog              text default '';
        sLogAux           text default '';

        sAnos             varchar;

        iMatric           integer;
        iCodigoOcorrencia integer;

        rLote             record;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP = 'INSERT' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' IPTU INCLUÍDO: ';
           iMatric = new.j40_matric ;
        elseif TG_OP = 'UPDATE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' IPTU ALTERADO: ';
           iMatric = new.j40_matric ;
        elseif TG_OP = 'DELETE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' IPTU EXCLUÍDO: ';
           iMatric = old.j40_matric ;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'Matricula ' || new.j40_matric || ' - ';
           if new.j40_refant  is not null then
              sLog = sLog || 'Ref. anterior ' || new.j40_refant ||' - ';
           end if;
           if new.j40_registrocartografico  is not null then
              sLog = sLog || 'Reg. cartografico ' || new.j40_registrocartografico ||' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j40_matric  <> old.j40_matric  then
              sLog = sLog || 'Matricula alterada de: '||old.j40_matric||' para: '||new.j40_matric|| ' - ';
           end if;
           if new.j40_refant  <> old.j40_refant then
              sLog = sLog || 'Ref. anterior alterada de: '|| old.j40_refant ||' para: '|| new.j40_refant || ' - ';
           end if;
           if (new.j40_registrocartografico  <> old.j40_registrocartografico) or
              (old.j40_registrocartografico is null and new.j40_registrocartografico is not null) then
              if old.j40_registrocartografico is null then
                 sLog = sLog || 'Reg. cartografico alterado de: EM BRANCO para: '|| new.j40_registrocartografico || ' - ';
              else
                 sLog = sLog || 'Reg. cartografico alterado de: '|| old.j40_registrocartografico ||' para: '|| new.j40_registrocartografico || ' - ';
              end if;
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'Matricula ' || old.j40_matric || ' - ';
           if old.j40_refant  is not null then
              sLog = sLog || 'Ref. anterior ' || old.j40_refant || ' - ';
           end if;
           if old.j40_registrocartografico  is not null then
              sLog = sLog || 'Reg. cartografico ' || old.j40_registrocartografico || ' - ';
           end if;
        end if;

        if sLogAux <> sLog then
           SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;
           insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                        ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                       578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5),
                       1, 'log de alteracoes', sLog;
           if TG_OP = 'INSERT' then
              iMatric = new.j40_matric;
           elseif TG_OP = 'UPDATE' then
              iMatric = new.j40_matric;
           elseif TG_OP = 'DELETE' then
              iMatric = old.j40_matric;
           end if;

           FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_matric = iMatric
           LOOP
                insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                    select nextval('histocorrenciamatric_ar25_sequencial_seq'), iMatric, iCodigoOcorrencia;
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
\$function\$;

CREATE OR REPLACE FUNCTION cadastro.fc_iptubasecondominio_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog              text default '';
        sLogAux           text default '';
        sCondominioNovo   text;
        sCondominioAntigo text;

        sAnos             varchar;

        iCondominio       integer;
        iMatric           integer;
        iCodigoOcorrencia integer;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP = 'INSERT' then
           sLog  = 'NO(S) EXERCICIO(S) DE '||sAnos||' REGISTRO DE CONDOMINIO INCLUÍDO: ';
           iCondominio  = new.j108_sequencial;
           iMatric = new.j108_matric;
        elseif TG_OP = 'UPDATE' then
           sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' REGISTRO DE CONDOMINIO ALTERADO: ';
           iCondominio  = new.j108_sequencial;
           iMatric = new.j108_matric;
        elseif TG_OP = 'DELETE' then
           sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' REGISTRO DE CONDOMINIO EXCLUÍDO: ';
           iCondominio  = old.j108_sequencial;
           iMatric = old.j108_matric;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'sequencial: ' || new.j108_sequencial || ' - ';
           if new.j108_matric  is not null then
              sLog = sLog || 'registro de matricula: ' || new.j108_matric || ' - ';
           end if;
           if new.j108_condominio is not null then
              select j107_nome into sCondominioNovo from condominio where j107_sequencial = new.j108_condominio;
              sLog = sLog || 'condominio: ' || new.j108_condominio ||  ' - ' || sCondominioNovo || ' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j108_sequencial  <> old.j108_sequencial  then
              sLog = sLog || 'sequencia alterada de: ' || old.j108_sequencial  || ' para: ' || new.j108_sequencial  || ' - ';
           end if;
           if new.j108_matric  <> old.j108_matric  then
              sLog = sLog || 'matricula alterada de: ' || old.j108_matric || ' para: ' || new.j108_matric ||' - ';
           end if;
           if new.j108_condominio  <> old.j108_condominio  then
              select j107_nome into sCondominioAntigo from condominio where j107_sequencial = old.j108_condominio;
              select j107_nome into sCondominioNovo from condominio where j107_sequencial = new.j108_condominio;
              sLog = sLog || 'condominio alterado de: ' || old.j108_condominio  || ' - ' || sCondominioAntigo;
              sLog = sLog || ' para: ' || new.j108_condominio  || ' - ' || sCondominioNovo || ' - ';
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'sequencial: ' || old.j108_sequencial   || ' - ';
           if old.j108_matric  is not null then
              sLog = sLog || 'registro de matricula: ' || old.j108_matric || ' - ';
           end if;
           if old.j108_condominio is not null then
              select j107_nome into sCondominioAntigo from condominio where j107_sequencial = old.j108_condominio;
              sLog = sLog || 'condominio: ' || old.j108_condominio|| ' - ' || sCondominioAntigo || ' - ';
           end if;
        end if;

        if sLog <> sLogAux then
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

CREATE OR REPLACE FUNCTION cadastro.fc_iptubasepredio_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog              text default '';
        sLogAux           text default '';

        sAnos             varchar;

        iPredio           integer;
        iMatric           integer;
        iCodigoOcorrencia integer;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP = 'INSERT' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' REGISTRO DE PREDIO INCLUÍDO: ';
           iPredio = new.j109_sequencial;
           iMatric = new.j109_matric;
        elseif TG_OP = 'UPDATE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' REGISTRO DE PREDIO ALTERADO: ';
           iPredio = new.j109_sequencial;
           iMatric = new.j109_matric;
        elseif TG_OP = 'DELETE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' REGISTRO DE PREDIO EXCLUÍDO: ';
           iPredio = old.j109_sequencial;
           iMatric = old.j109_matric;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'sequencial: ' || new.j109_sequencial        || ' - ';
           if new.j109_predio   is not null then
              sLog = sLog || 'registro de predio: ' || new.j109_predio  ||        ' - ';
           end if;
           if new.j109_matric is not null then
              sLog = sLog || 'matricula: ' || new.j109_matric || ' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j109_sequencial  <> old.j109_sequencial  then
              sLog = sLog || 'sequencia alterada de: ' || old.j109_sequencial  || ' para: ' || new.j109_sequencial  || ' - ';
           end if;
           if new.j109_predio  <> old.j109_predio  then
              sLog = sLog || 'predio alterado de: ' || old.j109_predio || 'para: ' || new.j109_predio || ' - ';
           end if;
           if new.j109_matric  <> old.j109_matric  then
              sLog = sLog || 'matricula alterada de: ' || old.j109_matric  ||  ' para: ' || new.j109_matric  ||' - ';
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'sequencial: ' || old.j109_sequencial || ' - ' ;
           if new.j109_predio   is not null then
              sLog = sLog || 'registro de predio: ' || old.j109_predio  || ' - ';
           end if;
           if new.j109_matric is not null then
              sLog = sLog || 'matricula: ' || old.j109_matric || ' - ';
           end if;
        end if;

        if sLog <> sLogAux then
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

CREATE OR REPLACE FUNCTION cadastro.fc_iptubaseregimovel_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog              text default '';
        sLogAux           text default '';

        sAnos             varchar;

        iMatric           integer;
        iCodigoOcorrencia integer;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP = 'INSERT' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' REGISTRO DE IMOVEL INCLUÍDO: ';
           iMatric = new.j04_matric;
        elseif TG_OP = 'UPDATE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' REGISTRO DE IMOVEL ALTERADO: ';
           iMatric = new.j04_matric;
        elseif TG_OP = 'DELETE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' REGISTRO DE IMOVEL EXCLUÍDO: ';
           iMatric = old.j04_matric;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'sequencial: ' || new.j04_sequencial  || ' - ';
           if new.j04_setorregimovel  is not null then
              sLog = sLog || 'registro de imovel: ' || new.j04_setorregimovel || ( select j69_sequencial from setorregimovel where j69_sequencial = new.j04_setorregimovel) || ' - ';
           end if;
           if new.j04_matric is not null then
              sLog = sLog || 'matricula: ' || new.j04_matric || ' - ';
           end if;
           if new.j04_matricregimo is not null then
              sLog = sLog || 'matricula do registro de imóveis: ' || new.j04_matricregimo || ' - ';
           end if;
           if new.j04_quadraregimo is not null then
              sLog = sLog || 'quadra do registro de imóveis: ' || new.j04_quadraregimo || ' - ';
           end if;
           if new.j04_loteregimo  is not null then
              sLog = sLog || 'lote do registro de imóveis: ' || new.j04_loteregimo || ' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
            if new.j04_sequencial  <> old.j04_sequencial  then
               sLog = sLog || 'sequencia alterada de: ' || old.j04_sequencial  || ' para: ' || new.j04_sequencial  || ' - ';
            end if;
            if new.j04_setorregimovel  <> old.j04_setorregimovel  then
               sLog = sLog || 'setor alterado de: ' || old.j04_setorregimovel   || ' para: ' || new.j01_numcgm||' - ';
            end if;
            if new.j04_matric  <> old.j04_matric  then
               sLog = sLog || 'matricula alterada de: ' || old.j04_matric  || ( select j01_matric from iptubase where j01_matric = new.j04_matric);
               sLog = sLog || '        para: ' || new.j04_matric  || ( select j01_matric from iptubase where j01_matric = new.j04_matric) || ' - ';
            end if;
            if (new.j04_matricregimo  <> old.j04_matricregimo) or
               (old.j04_matricregimo is null and new.j04_matricregimo is not null) then
               if old.j04_matricregimo is null then
                  sLog = sLog || 'matricula do registro de imóveis alterada de: EM BRANCO para: ' || new.j04_matricregimo  || ' - ';
               else
                  sLog = sLog || 'matricula do registro de imóveis alterada de: ' || old.j04_matricregimo  || ' para: ' || new.j04_matricregimo  || ' - ';
               end if;
            end if;
            if (new.j04_quadraregimo <> old.j04_quadraregimo) or
               (old.j04_quadraregimo is null and new.j04_quadraregimo is not null) then
               if old.j04_quadraregimo is null then
                  sLog = sLog || 'quadra do registro de imóveis alterada de: EM BRANCO para: ' || new.j04_quadraregimo || ' - ';
               else
                  sLog = sLog || 'quadra do registro de imóveis alterada de: ' || new.j04_quadraregimo  || ' para: ' || new.j04_quadraregimo || ' - ';
               end if;
            end if;
            if (new.j04_loteregimo  <> old.j04_loteregimo) or
               (old.j04_loteregimo is null and new.j04_loteregimo is not null) then
               if old.j04_loteregimo is null then
                  sLog = sLog || 'lote do registro de imóveis alterado de: EM BRANCO para: ' || new.j04_loteregimo  || ' - ';
               else
                  sLog = sLog || 'lote do registro de imóveis alterado de: ' || old.j04_loteregimo  || ' para: ' || new.j04_loteregimo  || ' - ';
               end if;
            end if;
        elseif TG_OP = 'DELETE' then
            sLog = sLog || 'sequencial: ' || old.j04_sequencial || ' - ';
            if old.j04_setorregimovel  is not null then
               sLog = sLog || 'registro de imovel: ' || old.j04_setorregimovel || ( select j69_sequencial from setorregimovel where j69_sequencial = old.j04_setorregimovel) || ' - ';
            end if;
            if old.j04_matric is not null then
               sLog = sLog || 'matricula: ' || old.j04_matric || ( select j01_matric from iptubase where j01_matric = old.j04_matric)|| ' - ';
            end if;
            if old.j04_matricregimo is not null then
               sLog = sLog || 'matricula do registro de imóveis: ' || old.j04_matricregimo || ' - ';
            end if;
            if old.j04_quadraregimo is not null then
               sLog = sLog || 'quadra do registro de imóveis: ' || old.j04_quadraregimo || ' - ';
            end if;
            if old.j04_loteregimo  is not null then
               sLog = sLog || 'lote do registro de imóveis: ' || old.j04_loteregimo || ' - ';
            end if;
        end if;

        if sLog <> sLogAux then
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

CREATE OR REPLACE FUNCTION cadastro.fc_iptuconstrdemo_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog              text default '';
        sLogAux           text default '';

        sAnos             varchar;

        iDemolicao        integer;
        iCodigoOcorrencia integer;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP      = 'INSERT' then
           sLog       = 'NO(S) EXERCICIO(S) DE '||sAnos||' DEMOLICAO INCLUÍDA: ';
           iDemolicao = new.j60_matric;
        elseif TG_OP  = 'UPDATE' then
           sLog       = 'NO(S) EXERCICIO(S) DE '||sAnos||' DEMOLICAO ALTERADA: ';
           iDemolicao = new.j60_matric;
        elseif TG_OP  = 'DELETE' then
           sLog       = 'NO(S) EXERCICIO(S) DE '||sAnos||' DEMOLICAO EXCLUÍDA: ';
           iDemolicao = old.j60_matric;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'matricula: ' || new.j60_matric          || ' - ';
           if new.j60_idcons is not null then
              sLog = sLog || 'idcons: ' || new.j60_idcons          || ' - ';
           end if;
           if new.j60_seq is not null then
              sLog = sLog || 'sequencia: ' || new.j60_seq          || ' - ';
           end if;
           if new.j60_codproc is not null then
              sLog = sLog || 'codproc: ' || new.j60_codproc        || ' - ';
           end if;
           if new.j60_area   is not null then
              sLog = sLog || 'area: ' || new.j60_area              || ' - ';
           end if;
           if new.j60_data is not null then
              sLog = sLog || 'data de demolicao: ' || new.j60_data || ' - ';
           end if;
           if new.j60_hora is not null then
              sLog = sLog || 'hora: ' || new.j60_hora              || ' - ';
           end if;
           if new.j60_datademo is not null then
              sLog = sLog || 'data: ' || new.j60_datademo          || ' - ';
           end if;
           if new.j60_usuario is not null then
              sLog = sLog || 'usuario: ' || new.j60_usuario        || ' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j60_matric <> old.j60_matric then
              sLog = sLog || 'matricula alterada de : ' || old.j60_matric || ' para: ' || new.j60_matric            || ' - ';
           end if;
           if new.j60_idcons <> old.j60_idcons then
              sLog = sLog || 'idcons alterada de: ' || old.j60_idcons || ' para: ' || new.j60_idcons                || ' - ';
           end if;
           if new.j60_seq <> old.j60_seq then
              sLog = sLog || 'sequencia alterada de: ' || old.j60_seq || ' para: ' || new.j60_seq                   || ' - ';
           end if;
           if new.j60_codproc <> old.j60_codproc then
              sLog = sLog || 'codproc alterada de: ' || old.j60_codproc || ' para: ' || new.j60_codproc             || ' - ';
           end if;
           if new.j60_area <> old.j60_area then
              sLog = sLog || 'area alterado de: ' || old.j60_area || ' para: ' || new.j60_area                      || ' - ';
           end if;
           if (new.j60_datademo <> old.j60_datademo) or
              (old.j60_datademo is null and new.j60_datademo is not null) then
              if old.j60_datademo is null then
                 sLog = sLog || 'data de demolicao alterado de: EM BRANCO para: ' || new.j60_datademo || ' - ';
              else
                 sLog = sLog || 'data de demolicao alterado de: ' || old.j60_datademo || ' para: ' || new.j60_datademo || ' - ';
              end if;
           end if;
           if new.j60_hora  <> old.j60_hora  then
              sLog = sLog || 'hora alterada de: ' || old.j60_hora  || ' para: ' || new.j60_hora                     || ' - ';
           end if;
           if new.j60_usuario <> old.j60_usuario then
              sLog = sLog || 'usuario alterado de: ' || old.j60_usuario || ' para: ' || new.j60_usuario             || ' - ';
           end if;
           if (new.j60_data <> old.j60_data) or
              (old.j60_data is null and new.j60_data is not null) then
              if old.j60_data is null then
                 sLog = sLog || 'data alterada de: EM BRANCO para: ' || new.j60_data || ' - ';
              else
                 sLog = sLog || 'data alterada de: ' || old.j60_data || ' para: ' || new.j60_data || ' - ';
              end if;
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'matricula: ' || old.j60_matric                                                           || ' - ';
           if old.j60_idcons is not null then
               sLog = sLog || 'idcons: ' || old.j60_idcons                                                          || ' - ';
           end if;
           if old.j60_seq is not null then
               sLog = sLog || 'sequencia: ' || old.j60_seq                                                           || ' - ';
           end if;
           if old.j60_codproc is not null then
               sLog = sLog || 'codproc: ' || old.j60_codproc                                                         || ' - ';
           end if;
           if old.j60_area   is not null then
               sLog = sLog || 'area: ' || old.j60_area                                                               || ' - ';
           end if;
           if old.j60_datademo is not null then
               sLog = sLog || 'data: ' || old.j60_datademo                                                           || ' - ';
           end if;
           if old.j60_hora is not null then
               sLog = sLog || 'hora: ' || old.j60_hora                                                               || ' - ';
           end if;
           if old.j60_usuario is not null then
               sLog = sLog || 'usuario: ' || old.j60_usuario                                                         || ' - ';
           end if;
           if old.j60_data is not null then
               sLog = sLog || 'data de demolicao: ' || old.j60_data                                                  || ' - ';
           end if;
        end if;

        if sLog <> sLogAux then
           SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;
           insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                        ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                       578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5),
                       1, 'log de alteracoes', sLog;
           insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                select nextval('histocorrenciamatric_ar25_sequencial_seq'), iDemolicao, iCodigoOcorrencia;
            IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
               insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia )
                    select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
            END IF;
        end if;

        if TG_OP      = 'INSERT' then
           return new;
        elseif TG_OP  = 'UPDATE' then
           return new;
        elseif TG_OP  = 'DELETE' then
           return old;
        end if;
    end;
\$function\$;

CREATE OR REPLACE FUNCTION cadastro.fc_iptuconstrhabite_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog              text default '';
        sLogAux           text default '';

        sAnos             varchar;

        iMatric           integer;
        iCodigoOcorrencia integer;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP = 'INSERT' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' HABITE-SE INCLUÍDO: ';
           iMatric = new.j131_matric;
        elseif TG_OP = 'UPDATE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' HABITE-SE ALTERADO: ';
           iMatric = new.j131_matric;
        elseif TG_OP = 'DELETE' then
           sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' HABITE-SE EXCLUÍDO: ';
           iMatric = old.j131_matric;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
            sLog = sLog || 'sequencia: ' || new.j131_sequencial || ' - ';
            if new.j131_idcons is not null then
               sLog = sLog || 'id: ' || new.j131_idcons || ' - ';
            end if;
            if new.j131_matric is not null then
               sLog = sLog || 'matricula: ' || new.j131_matric || (select j39_matric from iptuconstr where j39_matric = new.j131_matric) || ' - ';
            end if;
            if new.j131_usuario is not null then
               sLog = sLog || 'usuario: ' || new.j131_usuario || ' - ';
            end if;
            if new.j131_codprot is not null then
               sLog = sLog || 'codprot: ' || new.j131_codprot || ' - ';
            end if;
            if new.j131_cadhab is not null then
               sLog = sLog || 'habitacao: ' || new.j131_cadhab  || ' - ';
            end if;
            if new.j131_data is not null then
               sLog = sLog || 'data: ' || new.j131_data || ' - ';
            end if;
            if new.j131_hora is not null then
               sLog = sLog || 'hora: ' || new.j131_hora || ' - ';
            end if;
            if new.j131_obs  is not null then
               sLog = sLog || 'observacao: ' || new.j131_obs || ' - ';
            end if;
            if new.j131_dtprot is not null then
               sLog = sLog || 'data prot: ' || new.j131_dtprot || ' - ';
            end if;
            if new.j131_dthabite is not null then
               sLog = sLog || 'data habite: ' || new.j131_dthabite || ' - ';
            end if;
        elseif TG_OP = 'UPDATE' then
            if new.j131_sequencial  <> old.j131_sequencial  then
               sLog = sLog || 'sequencia alterada de: ' || old.j131_sequencial  || ' para: ' || new.j131_sequencial  || ' - ';
            end if;
            if new.j131_idcons <> old.j131_idcons then
               sLog = sLog || 'id alterada de: ' || old.j131_idcons || (select j39_idcons from iptuconstr where j39_idcons = new.j131_idcons);
               sLog = sLog || ' para: ' || new.j131_idcons || (select j39_idcons from iptuconstr where j39_idcons = new.j131_idcons) ||  ' - ';
            end if;
            if new.j131_matric <> old.j131_matric then
               sLog = sLog || 'matricula alterada de: ' || old.j131_matric || (select j39_matric from iptuconstr where j39_matric = new.j131_matric);
               sLog = sLog || ' para: ' || new.j131_matric || (select j39_matric from iptuconstr where j39_matric = new.j131_matric) || ' - ';
            end if;
            if new.j131_usuario <> old.j131_usuario then
               sLog = sLog || 'usuario alterado de: ' || old.j131_usuario || ' para: ' || new.j131_usuario || ' - ';
            end if;
            if new.j131_codprot <> old.j131_codprot then
               sLog = sLog || 'codprot: ' || new.j131_codprot || ' - ';
               sLog = sLog || 'codprot alterado de: ' || old.j39_codigo || ' para: ' || new.j131_codprot || ' - ';
            end if;
            if new.j131_cadhab <> old.j131_cadhab then
               sLog = sLog || 'cadhab alterado de: ' || old.j131_cadhab || ' para: ' || new.j131_cadhab || ' - ';
            end if;
            if new.j131_data <> old.j131_data then
               sLog = sLog || 'data alterado de: ' || old.j131_data || ' para: ' || new.j131_data || ' - ';
            end if;
            if new.j131_hora <> old.j131_hora then
               sLog = sLog || 'hora alterada de: ' || old.j131_hora || ' para: ' || new.j131_hora || ' - ';
            end if;
            if new.j131_obs <> old.j131_obs then
               sLog = sLog || 'observacao alterada de: ' || old.j131_obs || ' para: ' || new.j131_obs || ' - ';
            end if;
            if (new.j131_dtprot <> old.j131_dtprot) or
               (old.j131_dtprot is null and new.j131_dtprot is not null) then
               if old.j131_dtprot is null then
                  sLog = sLog || 'dataprot alterado de: EM BRANCO para: ' || new.j131_dtprot || ' - ';
               else
                  sLog = sLog || 'dataprot alterado de: ' || old.j131_dtprot || ' para: ' || new.j131_dtprot || ' - ';
               end if;
            end if;
            if (new.j131_dthabite <> old.j131_dthabite) or
               (old.j131_dthabite is null and new.j131_dthabite is not null) then
               if old.j131_dthabite is null then
                  sLog = sLog || 'data do habite-se alterada de: EM BRANCO para: ' || new.j131_dthabite || ' - ';
               else
                  sLog = sLog || 'data do habite-se alterada de: ' || old.j131_dthabite || ' para: ' || new.j131_dthabite || ' - ';
               end if;
            end if;
        elseif TG_OP = 'DELETE' then
            sLog = sLog || 'sequencia: ' || old.j131_sequencial || ' - ';
            if old.j131_idcons is not null then
               sLog = sLog || 'id: ' || old.j131_idcons || ' - ';
            end if;
            if old.j131_matric is not null then
               sLog = sLog || 'matricula: ' || old.j131_matric || ' - ';
            end if;
            if old.j131_usuario is not null then
               sLog = sLog || 'usuario: ' || old.j131_usuario || ' - ';
            end if;
            if old.j131_codprot is not null then
               sLog = sLog || 'codprot: ' || old.j131_codprot || ' - ';
            end if;
            if old.j131_cadhab is not null then
               sLog = sLog || 'habitacao: ' || old.j131_cadhab || ' - ';
            end if;
            if old.j131_data is not null then
               sLog = sLog || 'data: ' || old.j131_data || ' - ';
            end if;
            if old.j131_hora is not null then
               sLog = sLog || 'hora: ' || old.j131_hora || ' - ';
            end if;
            if old.j131_obs  is not null then
               sLog = sLog || 'observacao: ' || old.j131_obs || ' - ';
            end if;
            if old.j131_dtprot is not null then
               sLog = sLog || 'data prot: ' || old.j131_dtprot || ' - ';
            end if;
            if old.j131_dthabite is not null then
               sLog = sLog || 'data habite: ' || old.j131_dthabite || ' - ';
            end if;
        end if;

        if sLog <> sLogAux then
            SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;
            insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                         ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                select  iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
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

CREATE OR REPLACE FUNCTION cadastro.fc_iptuconstr_inc_alt_exc()
   RETURNS trigger
   LANGUAGE plpgsql
AS \$function\$

    declare

        sLog              text default '';
        sLogAux           text default '';
        desc_new_logr     text;
        desc_old_logr     text;

        sAnos             varchar;

        iMatric           integer;
        iCodigoOcorrencia integer;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP     = 'INSERT' then
           sLog       = 'NO(S) EXERCICIO(S) DE '||sAnos||' CONSTRUCAO INCLUÍDA: ';
           iMatric    = new.j39_matric;
        elseif TG_OP = 'UPDATE' then
           sLog       = 'NO(S) EXERCICIO(S) DE '||sAnos||' CONSTRUCAO ALTERADA: ';
           iMatric    = new.j39_matric;
        elseif TG_OP = 'DELETE' then
           sLog       = 'NO(S) EXERCICIO(S) DE '||sAnos||' CONSTRUCAO EXCLUÍDA: ';
           iMatric    = old.j39_matric;
        end if;

        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           select j88_descricao || ' - ' ||j14_nome into desc_new_logr from ruas inner join ruastipo on j88_codigo = j14_tipo  where j14_codigo = new.j39_codigo;
        elseif TG_OP = 'UPDATE' then
           select j88_descricao || ' - ' ||j14_nome into desc_new_logr from ruas inner join ruastipo on j88_codigo = j14_tipo  where j14_codigo = new.j39_codigo;
           select j88_descricao || ' - ' ||j14_nome into desc_old_logr from ruas inner join ruastipo on j88_codigo = j14_tipo  where j14_codigo = old.j39_codigo;
        elseif TG_OP = 'DELETE' then
           select j88_descricao || ' - ' ||j14_nome into desc_old_logr from ruas inner join ruastipo on j88_codigo = j14_tipo  where j14_codigo = old.j39_codigo;
        end if;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'id: ' || new.j39_idcons                                         || ' - ';
           if new.j39_ano is not null then
              sLog = sLog || 'ano: ' || new.j39_ano                                        || ' - ';
           end if;
           if new.j39_area is not null then
              sLog = sLog || 'area: ' || new.j39_area                                      || ' - ';
           end if;
           if new.j39_areap is not null then
              sLog = sLog || 'area privada: ' || new.j39_areap                             || ' - ';
           end if;
           if new.j39_dtlan is not null then
              sLog = sLog || 'data de lancamento: ' || new.j39_dtlan                       || ' - ';
           end if;
           if new.j39_codigo is not null then
              sLog = sLog || 'logradouro: ' || new.j39_codigo                              || ' - ';
           end if;
           if new.j39_numero is not null then
              sLog = sLog || 'numero do imovel: ' || new.j39_numero                        || ' - ';
           end if;
           if new.j39_compl is not null then
              sLog = sLog || 'complemento: ' || new.j39_compl                              || ' - ';
           end if;
           if new.j39_dtdemo is not null  then
              sLog = sLog || 'data de demolicao: ' || new.j39_dtdemo                       || ' - ';
           end if;
           if new.j39_idaument is not null then
              sLog = sLog || 'id da contrucao de aumento: '  || new.j39_idaument           || ' - ';
           end if;
           if new.j39_idprinc is not null then
              sLog = sLog || 'principal: ' || new.j39_idprinc                              || ' - ';
           end if;
           if new.j39_habite is not null then
              sLog = sLog || 'data do habite-se: ' || new.j39_habite                       || ' - ';
           end if;
           if new.j39_pavim is not null then
              sLog = sLog || 'pavimento: ' || new.j39_pavim                                || ' - ';
           end if;
           if new.j39_codprotdemo is not null and new.j39_codprotdemo <> '' then
              sLog = sLog || 'processo de protocolo da demolicao: ' || new.j39_codprotdemo || ' - ';
           end if;
           if new.j39_obs is not null then
              sLog = sLog || 'observacoes: ' || new.j39_obs                                || ' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j39_ano <> old.j39_ano then
              sLog = sLog || 'ano alterado de: ' || old.j39_ano || ' para: ' || new.j39_ano || ' - ';
           end if;
           if new.j39_area <> old.j39_area then
              sLog = sLog || 'area alterada de: ' || old.j39_area || ' para: ' || new.j39_area || ' - ';
           end if;
           if new.j39_areap <> old.j39_areap then
              sLog = sLog || 'area privada alterada de: ' || old.j39_areap || ' para: ' || new.j39_areap || ' - ';
           end if;
           if (new.j39_dtlan <> old.j39_dtlan) or
              (new.j39_dtlan is null and old.j39_dtlan is not null) or
              (new.j39_dtlan is not null and old.j39_dtlan is null) then
              sLog = sLog || 'data de lancamento alterada de: ' || old.j39_dtlan || ' para: ' || new.j39_dtlan || ' - ';
              if old.j39_dtlan is null then
                 sLog = sLog || 'data de lancamento alterada de:  para: ' || new.j39_dtlan || ' - ';
              elseif new.j39_dtlan is null then
                 sLog = sLog || 'data de lancamento alterada de: ' || old.j39_dtlan || ' para:  - ';
              else
                 sLog = sLog || 'data de lancamento alterada de: ' || old.j39_dtlan || ' para: ' || new.j39_dtlan || ' - ';
              end if;
           end if;
           if new.j39_codigo <> old.j39_codigo then
              sLog = sLog || 'logradouro: ' || new.j39_codigo  || ' - ' || desc_new_logr || ' -' ;
              sLog = sLog || 'logradouro alterado de: ' || old.j39_codigo  ||' - ' ||  desc_old_logr || ' para: ';
              sLog = sLog || new.j39_codigo || ' - ' || desc_new_logr || ' - ';
           end if;
           if new.j39_numero <> old.j39_numero then
              sLog = sLog || 'numero do imovel alterado de: ' || old.j39_numero || ' para: ' || new.j39_numero || ' - ';
           end if;
           if trim(new.j39_compl) <> trim(old.j39_compl) then
              sLog = sLog || 'complemento alterado de: ' || old.j39_compl || ' para: ' || new.j39_compl || ' - ';
           end if;
           if (new.j39_dtdemo <> old.j39_dtdemo) or
              (new.j39_dtdemo is null and old.j39_dtdemo is not null) or
              (new.j39_dtdemo is not null and old.j39_dtdemo is null) then
              if old.j39_dtdemo is null then
                 sLog = sLog || 'data de demolicao alterada de:  para: ' || new.j39_dtdemo || ' - ';
              elseif new.j39_dtdemo is null then
                 sLog = sLog || 'data de demolicao alterada de: ' || old.j39_dtdemo || ' para:  - ';
              else
                 sLog = sLog || 'data de demolicao alterada de: ' || old.j39_dtdemo || ' para: ' || new.j39_dtdemo || ' - ';
              end if;
           end if;
           if new.j39_idaument <> old.j39_idaument then
              sLog = sLog || 'id da contrucao de aumento alterado de: ' || old.j39_idaument || ' para: ' || new.j39_idaument || ' - ';
           end if;
           if new.j39_idprinc <> old.j39_idprinc then
              sLog = sLog || 'principal alterado de: ' || old.j39_idprinc || ' para: ' || new.j39_idprinc || ' - ';
           end if;
           if (new.j39_habite <> old.j39_habite) or
              (old.j39_habite is null and new.j39_habite is not null) then
              if old.j39_habite is null then
                 sLog = sLog || 'data do habite-se alterada de: EM BRANCO para: ' || new.j39_habite || ' - ';
              else
                 sLog = sLog || 'data do habite-se alterada de: ' || old.j39_habite || ' para: ' || new.j39_habite || ' - ';
              end if;
           end if;
           if new.j39_pavim <> old.j39_pavim then
              sLog = sLog || 'pavimento alterada de: ' || old.j39_pavim || ' para: ' || new.j39_pavim || ' - ';
           end if;
           if new.j39_codprotdemo <> old.j39_codprotdemo then
              sLog = sLog || 'processo de protocolo alterado de: ' || old.j39_codprotdemo || ' para: ' || new.j39_codprotdemo || ' - ';
           end if;
           if (new.j39_obs <> old.j39_obs) or
              (old.j39_obs is null and new.j39_obs is not null) then
              if old.j39_obs is null then
                 sLog = sLog || 'observacoes alterado para: ' || new.j39_obs || ' - ';
              else
                 sLog = sLog || 'observacoes alterado de: ' || old.j39_obs || ' para: ' || new.j39_obs || ' - ';
              end if;
           end if;
           if sLog <> sLogAux then
              sLog = 'id: ' || new.j39_idcons || ' - ' || sLog;
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'id: ' || old.j39_idcons                                         || ' - ';
           if old.j39_ano is not null then
              sLog = sLog || 'ano: ' || old.j39_ano                                        || ' - ';
           end if;
           if old.j39_area is not null then
              sLog = sLog || 'area: ' || old.j39_area                                      || ' - ';
           end if;
           if old.j39_areap is not null then
              sLog = sLog || 'area privada: ' || old.j39_areap                             || ' - ';
           end if;
           if old.j39_dtlan is not null then
              sLog = sLog || 'data de lancamento: ' || old.j39_dtlan                       || ' - ';
           end if;
           if old.j39_codigo is not null then
              sLog = sLog || 'logradouro: ' || old.j39_codigo                              || ' - ';
           end if;
           if old.j39_numero is not null then
              sLog = sLog || 'numero do imovel: ' || old.j39_numero                        || ' - ';
           end if;
           if old.j39_compl is not null then
              sLog = sLog || 'complemento: ' || old.j39_compl                              || ' - ';
           end if;
           if old.j39_dtdemo is not null then
              sLog = sLog || 'data de demolicao: ' || old.j39_dtdemo                       || ' - ';
           end if;
           if old.j39_idaument is not null then
              sLog = sLog || 'id da contrucao de aumento: ' || old.j39_idaument            || ' - ';
           end if;
           if old.j39_idprinc is not null then
              sLog = sLog || 'principal: ' || old.j39_idprinc                              || ' - ';
           end if;
           if old.j39_habite is not null then
              sLog = sLog || 'data do habite-se: ' || old.j39_habite                       || ' - ';
           end if;
           if old.j39_pavim is not null then
              sLog = sLog || 'pavimento: ' || old.j39_pavim                                || ' - ';
           end if;
           if old.j39_codprotdemo is not null then
              sLog = sLog || 'processo de protocolo da demolicao: ' || old.j39_codprotdemo || ' - ';
           end if;
           if old.j39_obs is not null then
              sLog = sLog || 'observacoes: ' || old.j39_obs                                || ' - ';
           end if;
        end if;

        if sLog <> sLogAux then
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

 CREATE OR REPLACE FUNCTION cadastro.fc_iptuender_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

         declare

         sLog    text default '';
         sLogAux text default '';
         sAnos   varchar;
         iMatric integer;

         iCodigoOcorrencia integer;
         bCampoProcesso boolean;

         begin

            select fc_getsession('DB_anoretroativo') into sAnos;

            if sAnos is null then
               select fc_getsession('DB_anousu') into sAnos;
            end if;

                     if TG_OP = 'INSERT' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' ENDEREÇO DE ENTREGA INCLUÍDO: ';
                        iMatric = new.j43_matric ;
                     elseif TG_OP = 'UPDATE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' ENDEREÇO DE ENTREGA ALTERADO: ';
                        iMatric = new.j43_matric ;
                     elseif TG_OP = 'DELETE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' ENDEREÇO DE ENTREGA EXCLUÍDO: ';
                        iMatric = old.j43_matric ;
                     end if;

                     sLogAux = sLog;

                     if TG_OP = 'INSERT' then

                     sLog = sLog || 'Matricula: ' || new.j43_matric || ' - ';

                     if new.j43_dest  is not null then
                         sLog = sLog || 'Dest: ' || new.j43_dest ||' - ';
                     end if;

                     if new.j43_ender is not null then
                         sLog = sLog || 'Endereco: ' || new.j43_ender || ' - ';
                     end if;

                     if new.j43_numimo is not null then
                         sLog = sLog || 'Numero: ' || new.j43_numimo                                || ' - ';
                     end if;

                     if new.j43_comple is not null then
                         sLog = sLog || 'complemento: ' || new.j43_comple                                || ' - ';
                     end if;

                     if new.j43_bairro is not null then
                         sLog = sLog || 'bairro: ' || new.j43_bairro                                || ' - ';
                     end if;

                     if new.j43_munic is not null then
                         sLog = sLog || 'municipio: ' || new.j43_munic                                || ' - ';
                     end if;

                     if new.j43_uf is not null then
                         sLog = sLog || 'uf: ' || new.j43_uf                                || ' - ';
                     end if;

                     if new.j43_cep is not null then
                         sLog = sLog || 'cep: ' || new.j43_cep                                || ' - ';
                     end if;

                     if new.j43_cxpost is not null then
                         sLog = sLog || 'caixa postal: ' || new.j43_cxpost                                || ' - ';
                     end if;

                     elseif TG_OP = 'UPDATE' then

                     if new.j43_matric  <> old.j43_matric  then
                         sLog = sLog || 'matricula alterada de: '||old.j43_matric||' para: '||new.j43_matric|| ' - ';
                     end if;

                     if new.j43_dest  <> old.j43_dest  then
                         sLog = sLog || 'dest alterada de: '|| old.j43_dest ||' para: '|| new.j43_dest || ' - ';
                     end if;

                     if new.j43_ender <> old.j43_ender then
                         sLog = sLog || 'endereco alterado de: '||old.j43_ender ||' para: '||new.j43_ender || ' - ';
                     end if;

                     if new.j43_numimo  <> old.j43_numimo  then
                         sLog = sLog ||'numero alterado de: '||old.j43_numimo ||' para:' || new.j43_numimo || ' - ';
                     end if;

                     if new.j43_comple  <> old.j43_comple  then
                         sLog = sLog ||'complemento alterado de: '||old.j43_comple ||' para:' || new.j43_comple || ' - ';
                     end if;

                     if new.j43_bairro  <> old.j43_bairro  then
                         sLog = sLog ||'bairro alterado de: '||old.j43_bairro ||' para:' || new.j43_bairro || ' - ';
                     end if;

                     if new.j43_munic <> old.j43_munic then
                         sLog = sLog ||'municipio alterado de: '||old.j43_munic  ||' para:' || new.j43_munic  || ' - ';
                     end if;

                     if new.j43_uf  <> old.j43_uf  then
                         sLog = sLog ||'uf alterado de: '||old.j43_uf ||' para:' || new.j43_uf || ' - ';
                     end if;

                     if new.j43_cep   <> old.j43_cep   then
                         sLog = sLog ||'cep alterado de: '||old.j43_cep  ||' para:' || new.j43_cep  || ' - ';
                     end if;

                     if new.j43_cxpost  <> old.j43_cxpost  then
                         sLog = sLog ||'caixa postal alterado de: '||old.j43_cxpost ||' para:' || new.j43_cxpost || ' - ';
                     end if;

                     elseif TG_OP = 'DELETE' then

                     sLog = sLog || 'Matricula: ' || old.j43_matric                                     || ' - ';

                     if old.j43_dest  is not null then
                         sLog = sLog || 'Dest: ' || old.j43_dest ||' - ';
                     end if;

                     if old.j43_ender is not null then
                         sLog = sLog || 'Endereco: ' || old.j43_ender || ' - ';
                     end if;

                     if old.j43_numimo is not null then
                         sLog = sLog || 'Numero: ' || old.j43_numimo                                || ' - ';
                     end if;

                     if old.j43_comple is not null then
                         sLog = sLog || 'complemento: ' || old.j43_comple                                || ' - ';
                     end if;

                     if old.j43_bairro is not null then
                         sLog = sLog || 'bairro: ' || old.j43_bairro                                || ' - ';
                     end if;

                     if old.j43_munic is not null then
                         sLog = sLog || 'municipio: ' || old.j43_munic                                || ' - ';
                     end if;

                     if old.j43_uf is not null then
                         sLog = sLog || 'uf: ' || old.j43_uf                                || ' - ';
                     end if;

                     if old.j43_cep is not null then
                         sLog = sLog || 'cep: ' || old.j43_cep                                || ' - ';
                     end if;

                     if old.j43_cxpost is not null then
                         sLog = sLog || 'caixa postal: ' || old.j43_cxpost                    || ' - ';
                     end if;

                     end if;

                     if sLog <> sLogAux then
                         SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                         insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                             select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                                     578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                         insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                             select nextval('histocorrenciamatric_ar25_sequencial_seq'), iMatric, iCodigoOcorrencia;

                             IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                                 insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_iptuisen_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

      declare

      sLog    text default '';
      sLogAux text default '';
      sAnos   varchar;
      iMatric integer;

      iCodigoOcorrencia integer;
      bCampoProcesso boolean;

      begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                     if TG_OP = 'INSERT' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' CÓDIGO DE ISENÇÃO INCLUÍDO: ';
                        iMatric = new.j46_matric  ;
                     elseif TG_OP = 'UPDATE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' CÓDIGO DE ISENÇÃO ALTERADO: ';
                        iMatric = new.j46_matric ;
                     elseif TG_OP = 'DELETE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' CÓDIGO DE ISENÇÃO EXCLUÍDO: ';
                        iMatric = old.j46_matric ;
                     end if;

                     sLogAux = sLog;

                     if TG_OP = 'INSERT' then

                     sLog = sLog || 'Codigo: ' || new.j46_codigo                                     || ' - ';

                     if new.j46_matric  is not null then
                         sLog = sLog || 'matricula: ' || new.j46_matric ||' - ';
                     end if;

                     if new.j46_tipo is not null then
                         sLog = sLog || 'tipo: ' || new.j46_tipo || ' - ';
                     end if;

                     if new.j46_dtini is not null then
                         sLog = sLog || 'data inicial: ' || new.j46_dtini                                || ' - ';
                     end if;

                     if new.j46_dtfim is not null then
                         sLog = sLog || 'data final: ' || new.j46_dtfim                                || ' - ';
                     end if;

                     if new.j46_perc is not null then
                         sLog = sLog || 'percentual: ' || new.j46_perc                                || ' - ';
                     end if;

                     if new.j46_dtinc is not null then
                         sLog = sLog || 'dtinc: ' || new.j46_dtinc                                || ' - ';
                     end if;

                     if new.j46_idusu is not null then
                         sLog = sLog || 'id usuaio: ' || new.j46_idusu                                || ' - ';
                     end if;

                     if new.j46_hist is not null then
                         sLog = sLog || 'historico: ' || new.j46_hist                                || ' - ';
                     end if;

                     if new.j46_arealo is not null then
                         sLog = sLog || 'Area isenta: ' || new.j46_arealo                                || ' - ';
                     end if;

                     elseif TG_OP = 'UPDATE' then

                     if new.j46_codigo  <> old.j46_codigo  then
                         sLog = sLog || 'Codigo alterado de: '||old.j46_codigo||' para: '||new.j46_codigo|| ' - ';
                     end if;

                     if new.j46_matric  <> old.j46_matric  then
                         sLog = sLog || 'matricula alterada de: '|| old.j46_matric ||' para: '|| new.j46_matric || ' - ';
                     end if;

                     if new.j46_tipo  <> old.j46_tipo  then
                         sLog = sLog ||' tipo  alterado de: '||old.j46_tipo  ||' para: '||new.j46_tipo  || ' - ';
                     end if;

                     if new.j46_dtini  <> old.j46_dtini  then
                         sLog = sLog ||'data inicial alterada de: '||old.j46_dtini ||' para:' || new.j46_dtini || ' - ';
                     end if;

                     if new.j46_dtfim  <> old.j46_dtfim  then
                         sLog = sLog ||'data fim alterada de: '||old.j46_dtfim ||' para:' || new.j46_dtfim || ' - ';
                     end if;

                     if new.j46_perc   <> old.j46_perc   then
                         sLog = sLog ||'percentual alterado de: '||old.j46_perc  ||' para:' || new.j46_perc  || ' - ';
                     end if;

                     if new.j46_dtinc   <> old.j46_dtinc   then
                         sLog = sLog ||'data inicial alterado de: '||old.j46_dtinc  ||' para:' || new.j46_dtinc  || ' - ';
                     end if;

                     if new.j46_idusu  <> old.j46_idusu  then
                         sLog = sLog ||'id_usuario alterado de: '||old.j46_idusu ||' para:' || new.j46_idusu || ' - ';
                     end if;

                     if new.j46_hist   <> old.j46_hist   then
                         sLog = sLog ||'historico alterado de: '||old.j46_hist  ||' para:' || new.j46_hist  || ' - ';
                     end if;

                     if new.j46_arealo  <> old.j46_arealo   then
                         sLog = sLog ||'Area isenta alterada de: '||old.j46_arealo  ||' para:' || new.j46_arealo  || ' - ';
                     end if;

                     elseif TG_OP = 'DELETE' then

                     if old.j46_matric is not null then
                         sLog = sLog || 'Codigo: ' || old.j46_codigo     || ' - ';
                     end if;

                     if old.j46_matric  is not null then
                         sLog = sLog || 'matricula: ' || old.j46_matric ||' - ';
                     end if;

                     if old.j46_tipo is not null then
                         sLog = sLog || 'tipo: ' || old.j46_tipo || ' - ';
                     end if;

                     if old.j46_dtini is not null then
                         sLog = sLog || 'data inicial: ' || old.j46_dtini                                || ' - ';
                     end if;

                     if old.j46_dtfim is not null then
                         sLog = sLog || 'data final: ' || old.j46_dtfim                                || ' - ';
                     end if;

                     if old.j46_perc is not null then
                         sLog = sLog || 'percentual: ' || old.j46_perc                                || ' - ';
                     end if;

                     if old.j46_dtinc is not null then
                         sLog = sLog || 'dtinc: ' || old.j46_dtinc                                || ' - ';
                     end if;

                     if old.j46_idusu is not null then
                         sLog = sLog || 'id usuario: ' || old.j46_idusu                                || ' - ';
                     end if;

                     if old.j46_hist is not null then
                         sLog = sLog || 'historico: ' || old.j46_hist                                || ' - ';
                     end if;

                     if old.j46_arealo is not null then
                         sLog = sLog || 'Area isenta: ' || old.j46_arealo                                || ' - ';
                     end if;

                     end if;

                     if sLogAux <> sLog then

                         SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                     insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                         select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                     insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                         select nextval('histocorrenciamatric_ar25_sequencial_seq'), iMatric, iCodigoOcorrencia;

                         IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                             insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_isenproc_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

      declare

         sLog    text default '';
         sLogAux text default '';
         sAnos   varchar;
         iMatric integer;
         iIdbql  integer;
         rLote   record;
         iCodigoOcorrencia integer;
         bCampoProcesso boolean;

      begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                 if TG_OP = 'INSERT' then
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' CÓDIGO DE PROCESSO DE ISENÇÃO INCLUÍDO: ';
                    select j46_matric
                    into iMatric
                    from iptuisen
                    where j46_codigo = new.j61_codigo ;
                 elseif TG_OP = 'UPDATE' then
                    sLog        = 'NO(S) EXERCICIO(S) DE '||sAnos||' CÓDIGO DE PROCESSO DE ISENÇÃO ALTERADO: ';
                    select j46_matric
                    into iMatric
                    from iptuisen
                    where j46_codigo = new.j61_codigo ;
                 elseif TG_OP = 'DELETE' then
                    sLog        = 'NO(S) EXERCICIO(S) DE '||sAnos||' CÓDIGO DE PROCESSO DE ISENÇÃO EXCLUÍDO: ';
                    select j46_matric
                    into iMatric
                    from iptuisen
                    where j46_codigo = old.j61_codigo ;
                 end if;

                 sLogAux = sLog;

                 if TG_OP = 'INSERT' then

                 sLog = sLog || 'Codigo: ' || new.j61_codigo ||' - ';

                 if new.j61_codproc   is not null then
                 sLog = sLog || 'Codigo do processo: ' || new.j61_codproc   || ' - ';
                 end if;

                 elseif TG_OP = 'UPDATE' then

                 if new.j61_codigo   <> old.j61_codigo   then
                 sLog = sLog || 'Codigo alterado de: '||old.j61_codigo ||' para: '||new.j61_codigo || ' - ';
                 end if;

                 if new.j61_codproc  <> old.j61_codproc  then
                 sLog = sLog || 'Codigo do processo alterado de: '||old.j61_codproc||' para: '||new.j61_codproc|| ' - ';
                 end if;

                 elseif TG_OP = 'DELETE' then

                 sLog = sLog || 'Codigo: ' || old.j61_codigo ||' - ';

                 if old.j61_codproc  is not null then
                 sLog = sLog || 'Codigo do processo: ' || old.j61_codproc                                  || ' - ';
                 end if;

                 end if;

                 if sLogAux <> sLog then

                 SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;


                 insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                 select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                 insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                 select nextval('histocorrenciamatric_ar25_sequencial_seq'), iMatric, iCodigoOcorrencia;

                 IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                     insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_isentaxa_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

      declare

         sLog    text default '';
         sLogAux text default '';
         sAnos   varchar;
         iMatric integer;

         iCodigoOcorrencia integer;
         bCampoProcesso boolean;

      begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                 if TG_OP = 'INSERT' then
                    sLog  = 'NO(S) EXERCICIO(S) DE '||sAnos||' ISENÇÃO INCLUÍDA: ';
                    select j46_matric
                    into iMatric
                    from iptuisen
                    where j46_codigo = new.j56_codigo;
                 elseif TG_OP = 'UPDATE' then
                    sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' ISENÇÃO ALTERADA: ';
                    select j46_matric
                    into iMatric
                    from iptuisen
                    where j46_codigo = new.j56_codigo;
                 elseif TG_OP = 'DELETE' then
                    sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' ISENÇÃO EXCLUÍDA: ';
                    select j46_matric
                    into iMatric
                    from iptuisen
                    where j46_codigo = old.j56_codigo;
                 end if;

                 sLogAux = sLog;

                 if TG_OP = 'INSERT' then

                 sLog = sLog || 'Codigo: ' || new.j56_codigo || ' - ';

                 if new.j56_receit  is not null then
                 sLog = sLog || 'Receita: ' || new.j56_receit ||' - ';
                 end if;

                 if new.j56_perc is not null then
                 sLog = sLog || 'Percentual: ' || new.j56_perc || ' - ';
                 end if;

                 if new.j56_iptucadtaxaexe is not null then
                 sLog = sLog || 'Cadastro da taxa: ' || new.j56_iptucadtaxaexe || ' - ';
                 end if;

                 elseif TG_OP = 'UPDATE' then

                 if new.j56_codigo  <> old.j56_codigo  then
                 sLog = sLog || 'Codigo alterada de: '||old.j56_codigo||' para: '||new.j56_codigo|| ' - ';
                 end if;

                 if new.j56_receit  <> old.j56_receit  then
                 sLog = sLog || 'Receita: '|| old.j56_receit ||' para: '|| new.j56_receit || ' - ';
                 end if;

                 if new.j56_perc <> old.j56_perc then
                 sLog = sLog || 'Percentual: '||old.j56_perc ||' para: '||new.j56_perc || ' - ';
                 end if;

                 if new.j56_iptucadtaxaexe <> old.j56_iptucadtaxaexe then
                 sLog = sLog || 'Cadastro da taxa: '||old.j56_iptucadtaxaexe ||' para: '||new.j56_iptucadtaxaexe || ' - ';
                 end if;

                 elseif TG_OP = 'DELETE' then

                 sLog = sLog || 'Codigo: ' || old.j56_codigo || ' - ';

                 if old.j56_receit  is not null then
                 sLog = sLog || 'Receita: ' || old.j56_receit || ' - ';
                 end if;

                 if old.j56_perc is not null then
                 sLog = sLog || 'Percentual:' || old.j56_perc|| '-';
                 end if;

                 if old.j56_iptucadtaxaexe is not null then
                 sLog = sLog || 'Cadastro da taxa:' || old.j56_iptucadtaxaexe|| '-';
                 end if;

                 end if;

                 if sLogAux <> sLog then

                 SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                 insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                 select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                 insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                 select nextval('histocorrenciamatric_ar25_sequencial_seq'), iMatric, iCodigoOcorrencia;

                 IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                     insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_loteam_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

      declare

         sLog    text default '';
         sLogAux text default '';
         sAnos   varchar;
         iMatric integer;
         iCodigoOcorrencia integer;
         bCampoProcesso boolean;

      begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                     if TG_OP = 'INSERT' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTEAMENTO INCLUÍDO: ';
                        iMatric = new.j34_loteam ;
                     elseif TG_OP = 'UPDATE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTEAMENTO ALTERADO: ';
                        iMatric = new.j34_loteam ;
                     elseif TG_OP = 'DELETE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTEAMENTO EXCLUÍDO: ';
                        iMatric = old.j34_loteam ;
                     end if;

                     sLogAux = sLog;

                     if TG_OP = 'INSERT' then

                     sLog = sLog || 'Loteamento: ' || new.j34_loteam           || ' - ';

                     if new.j34_descr  is not null then
                         sLog = sLog || 'Descricao: ' || new.j34_descr ||' - ';
                     end if;

                     if new.j34_areacc is not null then
                         sLog = sLog || 'areacc: ' || new.j34_areacc || ' - ';
                     end if;

                     if new.j34_areapc is not null then
                         sLog = sLog || 'areapc: ' || new.j34_areapc                                || ' - ';
                     end if;

                     if new.j34_areato is not null then
                         sLog = sLog || 'areato: ' || new.j34_areato                                || ' - ';
                     end if;

                     elseif TG_OP = 'UPDATE' then

                         if new.j34_loteam  <> old.j34_loteam  then
                         sLog = sLog || 'loteamento alterado de: '||old.j34_loteam||' para: '||new.j34_loteam|| ' - ';
                         end if;

                         if new.j34_descr  <> old.j34_descr  then
                         sLog = sLog || 'descricao alterada de: '|| old.j34_descr ||' para: '|| new.j34_descr || ' - ';
                         end if;

                         if new.j34_areacc <> old.j34_areacc then
                         sLog = sLog || 'areacc alterada de: '||old.j34_areacc ||' para: '||new.j34_areacc || ' - ';
                         end if;

                         if new.j34_areapc  <> old.j34_areapc  then
                         sLog = sLog ||'areapc alterada de: '||old.j34_areapc ||' para:' || new.j34_areapc || ' - ';
                         end if;

                         if new.j34_areato  <> old.j34_areato  then
                         sLog = sLog ||'areato alterada de: '||old.j34_areato ||' para:' || new.j34_areato || ' - ';
                         end if;

                     elseif TG_OP = 'DELETE' then

                     sLog = sLog || 'loteamento: ' || old.j34_loteam                                     || ' - ';

                     if old.j34_descr  is not null then
                         sLog = sLog || 'descricao: ' || old.j34_descr || ' - ';
                     end if;

                     if old.j34_areacc  is not null then
                         sLog = sLog || 'areacc: ' || old.j34_areacc || ' - ';
                     end if;

                     if old.j34_areapc is not null then
                         sLog = sLog || 'areapc: ' || old.j34_areapc                        || ' - ';
                     end if;

                     if old.j34_areato is not null then
                         sLog = sLog || 'areato: ' || old.j34_areato                        || ' - ';
                     end if;

                     end if;

                     if sLogAux <> sLog then

                         SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                     insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                         select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                     insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                         select nextval('histocorrenciamatric_ar25_sequencial_seq'), null, iCodigoOcorrencia;

                         IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                             insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_lotedist_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

      declare

         sLog    text default '';
         sLogAux text default '';
         sAnos   varchar;
         iMatric integer;
         iIdbql  integer;
         rLote   record;
         iCodigoOcorrencia integer;
         bCampoProcesso boolean;

      begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                 if TG_OP = 'INSERT' then
                    sLog   = 'NO(S) EXERCICIO(S) DE '||sAnos||' DISTANCIA DE LOTE INCLUÍDA: ';
                    iIdbql = new.j54_idbql ;
                 elseif TG_OP = 'UPDATE' then
                    sLog   = 'NO(S) EXERCICIO(S) DE '||sAnos||' DISTANCIA DE LOTE ALTERADA: ';
                    iIdbql = new.j54_idbql ;
                 elseif TG_OP = 'DELETE' then
                    sLog   = 'NO(S) EXERCICIO(S) DE '||sAnos||' DISTANCIA DE LOTE EXCLUÍDA: ';
                    iIdbql = old.j54_idbql ;
                 end if;

                 sLogAux = sLog;

                 if TG_OP = 'INSERT' then

                 sLog = sLog || 'Idbql: ' || new.j54_idbql ||' - ';

                 if new.j54_codigo   is not null then
                 sLog = sLog || 'Codigo: ' || new.j54_codigo   || ' - ';
                 end if;

                 if new.j54_orientacao  is not null then
                 sLog = sLog || 'Orientacao: ' || new.j54_orientacao ||' - ';
                 end if;

                 if new.j54_distan   is not null then
                 sLog = sLog || 'Distancia: ' || new.j54_distan  ||' - ';
                 end if;

                 elseif TG_OP = 'UPDATE' then

                 if new.j54_idbql   <> old.j54_idbql   then
                 sLog = sLog || 'Idbql alterado de: '||old.j54_idbql ||' para: '||new.j54_idbql || ' - ';
                 end if;

                 if new.j54_codigo  <> old.j54_codigo  then
                 sLog = sLog || 'Codigo alterado de: '||old.j54_codigo||' para: '||new.j54_codigo|| ' - ';
                 end if;

                 if new.j54_orientacao  <> old.j54_orientacao then
                 sLog = sLog || 'Orientacao alterada de: '|| old.j54_orientacao ||' para: '|| new.j54_orientacao || ' - ';
                 end if;

                 if new.j54_distan  <> old.j54_distan then
                 sLog = sLog || 'Distancia alterada de: '|| old.j54_distan ||' para: '|| new.j54_distan || ' - ';
                 end if;

                 elseif TG_OP = 'DELETE' then

                 sLog = sLog || 'Idbql: ' || old.j54_idbql ||' - ';

                 if old.j54_codigo  is not null then
                 sLog = sLog || 'Codigo: ' || old.j54_codigo                                     || ' - ';
                 end if;

                 if old.j54_orientacao  is not null then
                 sLog = sLog || 'Orientacao: ' || old.j54_orientacao ||' - ';
                 end if;

                 if old.j54_distan   is not null then
                 sLog = sLog || 'Distancia: ' || old.j54_distan  ||' - ';
                 end if;

                 end if;

                 if sLogAux <> sLog then

                 SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                 insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                 select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                 if TG_OP = 'INSERT' then
                 iIdbql = new.j54_idbql;

                 elseif TG_OP = 'UPDATE' then
                 iIdbql = new.j54_idbql;

                 elseif TG_OP = 'DELETE' then
                 iIdbql = old.j54_idbql;

                 end if;

                 FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iIdbql
                 LOOP

                 insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                 select nextval('histocorrenciamatric_ar25_sequencial_seq'), rLote.j01_matric, iCodigoOcorrencia;

                 END LOOP;

                 IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                 insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_lote_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

      declare

         sLog            text default '';
         sLogAux         text default '';
         sAnos           varchar;
         ilote           integer;
         iMatric         integer;
         iIdbql          integer;
         desc_new_bairro varchar;
         desc_old_bairro varchar;
         rLote           record;
         iCodigoOcorrencia integer;
         bCampoProcesso boolean;

         sZonaNova text;
         sZonaAntiga text;

      begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

         if TG_OP = 'INSERT' then
            sLog     = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTE INCLUÍDO: ';
            ilote = new.j34_idbql;
         elseif TG_OP = 'UPDATE' then
            sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTE ALTERADO: ';
            ilote = new.j34_idbql;
         elsif TG_OP = 'DELETE' then
            sLog     = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTE EXCLUÍDO: ';
            ilote = old.j34_idbql;
         end if;

         sLogAux = sLog;

         if TG_OP = 'INSERT' then
            Select j13_descr into desc_new_bairro from bairro where j13_codi =  new.j34_bairro;
         elseif TG_OP = 'UPDATE' then
            Select j13_descr into desc_new_bairro from bairro where j13_codi =  new.j34_bairro;
            Select j13_descr into desc_old_bairro from bairro where j13_codi =  old.j34_bairro;
         elsif TG_OP = 'DELETE' then
            Select j13_descr into desc_old_bairro from bairro where j13_codi =  old.j34_bairro;
         end if;

         if TG_OP = 'INSERT' then
            sLog = sLog  || 'idbql: ' ||  new.j34_idbql ||                                   ' - ';

            if new.j34_setor is not null then
                sLog = sLog ||  'setor: '||  new.j34_setor ||  ( select j30_codi from setor where j30_codi = new.j34_setor ) || ' - ';
            end if;

            if new.j34_quadra  is not null then
                sLog = sLog || 'quadra: '||  new.j34_quadra   ||                                    ' - ';
            end if;

            if new.j34_lote is not null then
                sLog = sLog ||  'lote: ' || new.j34_lote    ||                       ' - ';
            end if;

            if new.j34_area is not null then
                sLog = sLog ||  'area: ' || new.j34_area ||                  ' - ';
            end if;

            if new.j34_bairro is not null then
                sLog = sLog ||  'bairro: '||  new.j34_bairro  || ' - ' || desc_new_bairro || ' - ';
            end if;

            if new.j34_areal is not null then
                sLog = sLog ||  'Area medida: ' || new.j34_areal  ||                       ' - ';
            end if;

            if new.j34_zona is not null then
                select j50_descr into sZonaNova from zonas where j50_zona = new.j34_zona;
                sLog = sLog || 'zona: ' || new.j34_zona || ' - ' || sZonaNova || ' - ';
            end if;

            if new.j34_quamat is not null then
                sLog = sLog || 'quamat: '||  new.j34_quamat ||                              ' - ';
            end if;

            if new.j34_areapreservada is not null then
                sLog = sLog ||'Area preservada: ' || new.j34_areapreservada ||                       ' - ';
            end if;
         end if;

         if TG_OP = 'UPDATE' then
            if new.j34_idbql  <> old.j34_idbql  then
                sLog = sLog || 'idbql alterado de: ' || old.j34_idbql ||  ' para: ' || new.j34_idbql ||  ' - ';
            end if;

            if new.j34_setor <> old.j34_setor then
                sLog = sLog || 'setor alterado de: ' || old.j34_setor || ' para: ' || new.j34_setor || ' - ';
            end if;

            if new.j34_quadra  <> old.j34_quadra  then
                sLog = sLog || 'quadra alterada de: ' || old.j34_quadra ||  ' para: ' || new.j34_quadra ||  ' - ';
            end if;

            if new.j34_lote  <> old.j34_lote  then
                sLog = sLog || 'lote alterado de: ' || old.j34_lote ||  ' para: ' || new.j34_lote ||  ' - ';
            end if;

            if new.j34_area <> old.j34_area then
                sLog = sLog || 'area alterada de: ' || old.j34_area  || ' para: ' || new.j34_area || ' - ';
            end if;

            if new.j34_bairro <> old.j34_bairro then
                sLog = sLog || 'bairro: alterado de ' || old.j34_bairro  ||  ' ' || desc_old_bairro || ' para: ' || new.j34_bairro  || ' - ' ||  desc_new_bairro || ' - ';
            end if;

            if new.j34_areal <> old.j34_areal then
                sLog = sLog || 'Area Medida alterada de: '||  old.j34_areal || ' para: ' || new.j34_areal || ' - ';
            end if;

            if new.j34_zona <> old.j34_zona then
                select j50_descr into sZonaAntiga from zonas where j50_zona = old.j34_zona;
                select j50_descr into sZonaNova from zonas where j50_zona = new.j34_zona;
                sLog = sLog || 'zona alterada de: '||  old.j34_zona || ' - ' || sZonaAntiga ||' para: '||  new.j34_zona  || ' - ' || sZonaNova || ' - ';
            end if;

            if new.j34_quamat <> old.j34_quamat then
                sLog = sLog || 'quamat alterado de: ' || old.j34_quamat || ' para: ' || new.j34_quamat || ' - ';
            end if;

            if new.j34_areapreservada <> old.j34_areapreservada then
                sLog = sLog || 'Area preservada alterada de: ' || old.j34_areapreservada || ' para: '||  new.j34_areapreservada ||  ' - ';
            end if;
         end if;

         if TG_OP = 'DELETE' then
            sLog = sLog || 'idbql: ' || old.j34_idbql ||                    ' - ';

            if old.j34_setor is not null then
                sLog = sLog  ||'setor: ' || old.j34_setor || ' - ';
            end if;

            if old.j34_quadra  is not null then
               sLog = sLog || 'quadra: ' || old.j34_quadra ||                  ' - ';
            end if;

            if old.j34_lote is not null then
               sLog = sLog || 'lote: ' || old.j34_lote ||                          ' - ';
            end if;

            if old.j34_area is not null then
                sLog = sLog || 'area: ' || old.j34_area ||                  ' - ';
            end if;

            if old.j34_bairro is not null then
                sLog = sLog || 'bairro: ' || old.j34_bairro  ||' - ' || desc_old_bairro || ' - ';
            end if;

            if old.j34_areal is not null then
                sLog = sLog || 'Area real medida: ' || old.j34_areal  ||                       ' - ';
            end if;

            if old.j34_zona is not null then
                select j50_descr into sZonaAntiga from zonas where j50_zona = old.j34_zona;
                sLog = sLog || 'zona: '||  old.j34_zona  || ' - ' || sZonaAntiga || ' - ';
            end if;

            if old.j34_quamat is not null then
                sLog = sLog || 'quamat: '||  old.j34_quamat||                               ' - ';
            end if;

            if old.j34_areapreservada is not null then
                sLog = sLog || 'area preservada: '||  old.j34_areapreservada  ||                      ' - ';
            end if;
         end if;

         if sLogAux <> sLog then
            SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

            insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                         ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
               select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                      578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5),
                      1, 'log de alteracoes', sLog;

            if TG_OP = 'INSERT' then
                iIdbql = new.j34_idbql;
            elseif TG_OP = 'UPDATE' then
                iIdbql = new.j34_idbql;
            elseif TG_OP = 'DELETE' then
                iIdbql = old.j34_idbql;
            end if;

            FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iIdbql
            LOOP
                insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                  select nextval('histocorrenciamatric_ar25_sequencial_seq'), rLote.j01_matric, iCodigoOcorrencia;
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
\$function\$;

 CREATE OR REPLACE FUNCTION cadastro.fc_loteloc_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

      declare

         sLog    text default '';
         sLogAux text default '';
         sAnos   varchar;
         iMatric integer;
         iIdbql  integer;
         rLote   record;

         iCodigoOcorrencia integer;
         bCampoProcesso boolean;

      begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                 if TG_OP = 'INSERT' then
                    sLog   = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOCALIZACAO DE LOTE INCLUÍDA: ';
                    iIdbql = new.j06_idbql ;
                 elseif TG_OP = 'UPDATE' then
                    sLog   = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOCALIZACAO DE LOTE ALTERADA: ';
                    iIdbql = new.j06_idbql ;
                 elseif TG_OP = 'DELETE' then
                    sLog   = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOCALIZACAO DE LOTE EXCLUÍDA: ';
                    iIdbql = old.j06_idbql ;
                 end if;

                 sLogAux = sLog;

                 if TG_OP = 'INSERT' then

                 sLog = sLog || 'Idbql: ' || new.j06_idbql ||' - ';

                 if new.j06_setorloc   is not null then
                 sLog = sLog || 'Setor: ' || new.j06_setorloc   || ' - ';
                 end if;

                 if new.j06_quadraloc  is not null then
                 sLog = sLog || 'Quadra: ' || new.j06_quadraloc ||' - ';
                 end if;

                 if new.j06_lote   is not null then
                 sLog = sLog || 'Localizacao de lote: ' || new.j06_lote  ||' - ';
                 end if;

                 elseif TG_OP = 'UPDATE' then

                 if new.j06_idbql   <> old.j06_idbql   then
                 sLog = sLog || 'Idbql alterado de: '||old.j06_idbql ||' para: '||new.j06_idbql || ' - ';
                 end if;

                 if new.j06_setorloc  <> old.j06_setorloc  then
                 sLog = sLog || 'Setor alterado de: '||old.j06_setorloc||' para: '||new.j06_setorloc|| ' - ';
                 end if;

                 if new.j06_quadraloc  <> old.j06_quadraloc then
                 sLog = sLog || 'Quadra alterada de: '|| old.j06_quadraloc ||' para: '|| new.j06_quadraloc || ' - ';
                 end if;

                 if new.j06_lote   <> old.j06_lote  then
                 sLog = sLog || 'Localizacao de lote alterado de: '|| old.j06_lote  ||' para: '|| new.j06_lote  || ' - ';
                 end if;

                 elseif TG_OP = 'DELETE' then

                 sLog = sLog || 'Idbql: ' || old.j06_idbql ||' - ';

                 if old.j06_setorloc  is not null then
                 sLog = sLog || 'Setor: ' || old.j06_setorloc || ' - ';
                 end if;

                 if old.j06_quadraloc  is not null then
                 sLog = sLog || 'Quadra: ' || old.j06_quadraloc ||' - ';
                 end if;

                 if old.j06_lote   is not null then
                 sLog = sLog || 'Localizacao de lote: ' || old.j06_lote  ||' - ';
                 end if;

                 end if;

                 if sLogAux <> sLog then

                 SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                 insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                 select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                 if TG_OP = 'INSERT' then
                 iIdbql = new.j06_idbql;

                 elseif TG_OP = 'UPDATE' then
                 iIdbql = new.j06_idbql;

                 elseif TG_OP = 'DELETE' then
                 iIdbql = old.j06_idbql;

                 end if;

                 FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iIdbql
                 LOOP

                 insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                 select nextval('histocorrenciamatric_ar25_sequencial_seq'), rLote.j01_matric, iCodigoOcorrencia;

                 END LOOP;

                 IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                     insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_loteloteam_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

      declare

         sLog    text default '';
         sLogAux text default '';
         sAnos   varchar;
         iMatric integer;
         iIdbql  integer;
         rLote   record;
         iCodigoOcorrencia integer;
         bCampoProcesso boolean;

         sLoteamento text;

      begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                     if TG_OP = 'DELETE' then
                         SELECT upper(j34_loteam || ' - ' || j34_descr) AS lotemento
                       INTO sLoteamento
                       FROM loteam
                      WHERE j34_loteam = old.j34_loteam;

                     else

                     SELECT upper(j34_loteam || ' - ' || j34_descr) AS lotemento
                       INTO sLoteamento
                       FROM loteam
                      WHERE j34_loteam = new.j34_loteam;

                     end if;

                     if sLoteamento is null then
                         sLoteamento := '';
                     else
                         sLoteamento := sLoteamento||' -';
                     end if;

                     if TG_OP = 'INSERT' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTEAMENTO INCLUÍDO: '||sLoteamento||' ';
                        iMatric = new.j34_idbql ;
                     elseif TG_OP = 'UPDATE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTEAMENTO ALTERADO: '||sLoteamento||' ';
                        iMatric = new.j34_idbql ;
                     elseif TG_OP = 'DELETE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTEAMENTO EXCLUÍDO: '||sLoteamento||' ';
                        iMatric = old.j34_idbql ;
                     end if;

                     sLogAux = sLog;

                     if TG_OP = 'INSERT' then

                     sLog = sLog || 'idbql: ' || new.j34_idbql || ' - ';

                     if new.j34_loteam  is not null then
                         sLog = sLog || 'loteamento: ' || new.j34_loteam ||' - ';
                     end if;

                     elseif TG_OP = 'UPDATE' then

                         if new.j34_idbql  <> old.j34_idbql  then
                         sLog = sLog || 'idbql alterado de: '||old.j34_idbql||' para: '||new.j34_idbql|| ' - ';
                         end if;

                         if new.j34_loteam  <> old.j34_loteam then
                         sLog = sLog || 'loteamento alterada de: '|| old.j34_loteam ||' para: '|| new.j34_loteam || ' - ';
                         end if;

                     elseif TG_OP = 'DELETE' then

                     sLog = sLog || 'idbql: ' || old.j34_idbql || ' - ';

                     if old.j34_loteam  is not null then
                         sLog = sLog || 'loteamento: ' || old.j34_loteam || ' - ';
                     end if;

                     end if;

                     if sLogAux <> sLog then

                         SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                     insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                         select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                     if TG_OP = 'INSERT' then
                         iIdbql = new.j34_idbql;
                     elseif TG_OP = 'UPDATE' then
                         iIdbql = new.j34_idbql;
                     elseif TG_OP = 'DELETE' then
                         iIdbql = old.j34_idbql;
                     end if;

                     FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iIdbql
                     LOOP

                         insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia ) select nextval('histocorrenciamatric_ar25_sequencial_seq'), rLote.j01_matric, iCodigoOcorrencia;

                     END LOOP;

                     IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                         insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_lotesetorfiscal_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

                 declare

                 sLog    text default '';
                 sLogAux text default '';
                 sAnos   varchar;
                 iCodigo integer;
                 iIdbql  integer;
                 rLote   record;
                 iCodigoOcorrencia integer;
                 bCampoProcesso boolean;

                 begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                     if TG_OP = 'INSERT' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' CÓDIGO SETOR INCLUÍDO: ';
                        iCodigo = new.j91_idbql;
                     elseif TG_OP = 'UPDATE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' CÓDIGO SETOR ALTERADO: ';
                        iCodigo = new.j91_idbql;
                     elseif TG_OP = 'DELETE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' CÓDIGO SETOR EXCLUÍDO: ';
                        iCodigo = old.j91_idbql;
                     end if;

                     sLogAux = sLog;

                     if TG_OP = 'INSERT' then

                     sLog = sLog || 'idbql: ' || new.j91_idbql || ' - ';

                     if new.j91_codigo  is not null then
                         sLog = sLog || 'Codigo setor fiscal: ' || new.j91_codigo ||' - ';
                     end if;

                     elseif TG_OP = 'UPDATE' then

                     if new.j91_idbql  <> old.j91_idbql  then
                         sLog = sLog || 'idbql alterada de: '||old.j91_idbql||' para: '||new.j91_idbql|| ' - ';
                     end if;

                     if new.j91_codigo  <> old.j91_codigo  then
                         sLog = sLog || 'Codigo setor fiscal alterado de: '|| old.j91_codigo ||' para: '|| new.j91_codigo || ' - ';
                     end if;

                     elseif TG_OP = 'DELETE' then

                     sLog = sLog || 'idbql: ' || old.j91_idbql || ' - ';

                     if old.j91_codigo  is not null then
                         sLog = sLog || 'Codigo setor fiscal: ' || old.j91_codigo || ' - ';
                     end if;

                     end if;

                     if sLogAux <> sLog then

                         SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                     insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                         select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                     if TG_OP = 'INSERT' then
                         iIdbql = new.j91_idbql ;

                     elseif TG_OP = 'UPDATE' then
                         iIdbql = new.j91_idbql ;

                     elseif TG_OP = 'DELETE' then
                         iIdbql = old.j91_idbql ;

                     end if;

                     FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iIdbql
                     LOOP
                         insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                         select nextval('histocorrenciamatric_ar25_sequencial_seq'), rLote.j01_matric, iCodigoOcorrencia;

                     END LOOP;

                         IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                             insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_matricobs_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

                 declare

                 sLog    text default '';
                 sLogAux text default '';
                 sAnos   varchar;
                 iMatric integer;
                 rLote   record;

                 iCodigoOcorrencia integer;
                 bCampoProcesso boolean;

                 begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                     if TG_OP = 'INSERT' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' IPTU INCLUÍDO: ';
                        iMatric = new.j26_matric ;
                     elseif TG_OP = 'UPDATE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' IPTU ALTERADO: ';
                        iMatric = new.j26_matric ;
                     elseif TG_OP = 'DELETE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' IPTU EXCLUÍDO: ';
                        iMatric = old.j26_matric ;
                     end if;

                     sLogAux = sLog;

                     if TG_OP = 'INSERT' then

                     sLog = sLog || 'Matricula ' || new.j26_matric || ' - ';

                     if new.j26_obs  is not null then
                         sLog = sLog || 'Observacao ' || new.j26_obs ||' - ';
                     end if;

                     elseif TG_OP = 'UPDATE' then

                     if new.j26_matric  <> old.j26_matric  then
                         sLog = sLog || 'Matricula alterado de: '||old.j26_matric||' para: '||new.j26_matric|| ' - ';
                     end if;

                     if new.j26_obs  <> old.j26_obs then
                         sLog = sLog || 'Observacao alterada de: '|| old.j26_obs ||' para: '|| new.j26_obs || ' - ';
                     end if;

                     elseif TG_OP = 'DELETE' then

                     sLog = sLog || 'Matricula ' || old.j26_matric || ' - ';

                     if old.j26_obs  is not null then
                         sLog = sLog || 'Observacao ' || old.j26_obs || ' - ';
                     end if;

                     end if;

                     if sLogAux <> sLog then

                         SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                     insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                         select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                     if TG_OP = 'INSERT' then
                         iMatric = new.j26_matric;

                     elseif TG_OP = 'UPDATE' then
                         iMatric = new.j26_matric;

                     elseif TG_OP = 'DELETE' then
                         iMatric = old.j26_matric;

                     end if;

                     FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_matric = iMatric
                     LOOP

                         insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                         select nextval('histocorrenciamatric_ar25_sequencial_seq'), iMatric, iCodigoOcorrencia;

                     END LOOP;

                     IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                         insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_promitente_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

                 declare

                 sLog                 text default '';
                 sLogAux              text default '';
                 sTipoPromitenteNovo  text;
                 sTipoPromitenteVelho text;

                 sAnos                varchar;

                 iMatric              integer;
                 iCodigoOcorrencia    integer;

                 bCampoProcesso       boolean;


                 begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                 if TG_OP = 'INSERT' then
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' PROMITENTE INCLUÍDO: ';
                    iMatric = new.j41_matric;
                 elseif TG_OP = 'UPDATE' then
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' PROMITENTE ALTERADO: ';
                    iMatric = new.j41_matric;
                 elseif TG_OP = 'DELETE' then
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' PROMITENTE EXCLUÍDO: ';
                    iMatric = old.j41_matric;
                 end if;

                 sLogAux = sLog;

                 if TG_OP = 'INSERT' then

                 sLog = sLog || 'Matricula: ' || new.j41_matric                                   || ' - ';

                 if new.j41_numcgm  is not null then
                 sLog = sLog || 'CGM: ' || new.j41_numcgm ||' - ';
                 end if;

                 if new.j41_tipopro is not null then
                 sLog = sLog || 'Responsavel: ' || new.j41_tipopro || ' - ';
                 end if;

                 if new.j41_promitipo is not null then
                 sLog = sLog || 'Promitipo: ' || new.j41_promitipo                                || ' - ';
                 end if;

                 /*
                 if new.j41_tipopromitente is not null then
                 select j164_tipopromitente || ' - ' || j164_descricao into sTipoPromitenteNovo from tipopromitente where j164_tipopromitente = new.j41_tipopromitente;
                 sLog = sLog || 'Tipo do promitente: ' || sTipoPromitenteNovo || ' - ';
                 end if;
                 */

                 elseif TG_OP = 'UPDATE' then

                 if new.j41_matric  <> old.j41_matric  then
                 sLog = sLog || 'matricula alterada de: '||old.j41_matric||' para: '||new.j41_matric|| ' - ';
                 end if;

                 if new.j41_numcgm  <> old.j41_numcgm  then
                 sLog = sLog || 'CGM: '|| old.j41_numcgm ||' para: '|| new.j41_numcgm || ' - ';
                 end if;

                 if new.j41_tipopro <> old.j41_tipopro then
                 sLog = sLog || 'Responsavel: '||old.j41_tipopro ||' para: '||new.j41_tipopro || ' - ';
                 end if;

                 if new.j41_promitipo <> old.j41_promitipo then
                 sLog = sLog ||'Promitipo: '||old.j41_promitipo||' para:' || new.j41_promitipo|| ' - ';
                 end if;

                 /*
                 if new.j41_tipopromitente <> old.j41_tipopromitente then
                 select j164_tipopromitente || ' - ' || j164_descricao into sTipoPromitenteNovo from tipopromitente where j164_tipopromitente = new.j41_tipopromitente;
                 select j164_tipopromitente || ' - ' || j164_descricao into sTipoPromitenteVelho from tipopromitente where j164_tipopromitente = old.j41_tipopromitente;
                 sLog = sLog ||'Tipo do promitente: '||sTipoPromitenteVelho||' para:' || sTipoPromitenteNovo|| ' - ';
                 end if;
                 */

                 elseif TG_OP = 'DELETE' then

                 sLog = sLog || 'matricula: ' || old.j41_matric                                     || ' - ';

                 if old.j41_numcgm  is not null then
                 sLog = sLog || 'CGM: ' || old.j41_numcgm || ' - ';
                 end if;

                 if old.j41_tipopro is not null then
                 sLog = sLog || 'Responsavel:'|| old.j41_tipopro|| '-';
                 end if;

                 if old.j41_promitipo is not null then
                 sLog = sLog || 'Promitipo:'|| old.j41_promitipo|| '-';
                 end if;

                 /*
                 if old.j41_tipopromitente is not null then
                 select j164_tipopromitente || ' - ' || j164_descricao into sTipoPromitenteVelho from tipopromitente where j164_tipopromitente = old.j41_tipopromitente;
                 sLog = sLog || 'Tipo do promitente:'|| sTipoPromitenteVelho || '-';
                 end if;
                 */

                 end if;

                 if sLogAux <> sLog then

                 SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                 insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                 select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                 insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                 select nextval('histocorrenciamatric_ar25_sequencial_seq'), iMatric, iCodigoOcorrencia;

                 IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                    insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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


 CREATE OR REPLACE FUNCTION cadastro.fc_propri_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

                 declare

                 sLog                   text default '';
                 sLogAux                text default '';
                 sTipoProprietarioNovo  text;
                 sTipoProprietarioVelho text;

                 sAnos                  varchar;

                 iMatric                integer;
                 iIdbql                 integer;
                 iCodigoOcorrencia      integer;

                 rLote                  record;

                 bCampoProcesso         boolean;

                 begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                 if TG_OP = 'INSERT' then
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' PROPRIEDADE INCLUÍDA: ';
                    iMatric = new.j42_matric ;
                 elseif TG_OP = 'UPDATE' then
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' PROPRIEDADE ALTERADA: ';
                    iMatric = new.j42_matric ;
                 elseif TG_OP = 'DELETE' then
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' PROPRIEDADE EXCLUÍDA: ';
                    iMatric = old.j42_matric ;
                 end if;

                 sLogAux = sLog;

                 if TG_OP = 'INSERT' then

                 sLog = sLog || 'Matricula: ' || new.j42_matric ||' - ';
                 sLog = sLog || 'CGM: ' || new.j42_numcgm   || ' - ';

                 /*
                 if new.j42_tipoproprietario   is not null then
                 select j163_tipoproprietario || ' - ' || j163_descricao into sTipoProprietarioNovo from tipoproprietario where j163_tipoproprietario = new.j42_tipoproprietario;
                 sLog = sLog || 'Tipo de ProprietÃ¡rio: ' || sTipoProprietarioNovo || ' - ';
                 end if;
                 */

                 elseif TG_OP = 'UPDATE' then

                 if new.j42_matric   <> old.j42_matric   then
                 sLog = sLog || 'Matricula alterada de: '||old.j42_matric ||' para: '||new.j42_matric || ' - ';
                 end if;

                 if new.j42_numcgm  <> old.j42_numcgm  then
                 sLog = sLog || 'CGM alterado de: '||old.j42_numcgm||' para: '||new.j42_numcgm|| ' - ';
                 end if;

                 /*
                 if new.j42_tipoproprietario  <> old.j42_tipoproprietario  then
                 select j163_tipoproprietario || ' - ' || j163_descricao into sTipoProprietarioNovo from tipoproprietario where j163_tipoproprietario = new.j42_tipoproprietario;
                 select j163_tipoproprietario || ' - ' || j163_descricao into sTipoProprietarioVelho from tipoproprietario where j163_tipoproprietario = old.j42_tipoproprietario;
                 sLog = sLog || 'Tipo de ProprietÃ¡rio: '||sTipoProprietarioVelho||' para: '||sTipoProprietarioNovo|| ' - ';
                 end if;
                 */

                 elseif TG_OP = 'DELETE' then

                 sLog = sLog || 'Matricula: ' || old.j42_matric ||' - ';

                 if old.j42_numcgm  is not null then
                 sLog = sLog || 'CGM: ' || old.j42_numcgm                                  || ' - ';
                 end if;

                 /*
                 if old.j42_tipoproprietario  is not null then
                 select j163_tipoproprietario || ' - ' || j163_descricao into sTipoProprietarioVelho from tipoproprietario where j163_tipoproprietario = old.j42_tipoproprietario;
                 sLog = sLog || 'Tipo de ProprietÃ¡rio: ' || sTipoProprietarioVelho || ' - ';
                 end if;
                 */

                 end if;

                 if sLogAux <> sLog then

                 SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                 insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                 select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                 insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                 select nextval('histocorrenciamatric_ar25_sequencial_seq'), iMatric, iCodigoOcorrencia;

                 IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                     insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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


 CREATE OR REPLACE FUNCTION cadastro.fc_setorfiscal_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

                 declare

                 sLog    text default '';
                 sLogAux text default '';
                 sAnos   varchar;
                 iCodigo integer;
                 iCodigoOcorrencia integer;
                 bCampoProcesso boolean;

                 begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                     if TG_OP = 'INSERT' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' SETOR INCLUÍDO: ';
                        iCodigo = new.j90_codigo;
                     elseif TG_OP = 'UPDATE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' SETOR ALTERADO: ';
                        iCodigo = new.j90_codigo;
                     elseif TG_OP = 'DELETE' then
                        sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' SETOR EXCLUÍDO: ';
                        iCodigo = old.j90_codigo;
                     end if;

                     sLogAux = sLog;

                     if TG_OP = 'INSERT' then

                     sLog = sLog || 'codigo: ' || new.j90_codigo       || ' - ';

                     if new.j90_descr  is not null then
                         sLog = sLog || 'descricao: ' || new.j90_descr ||' - ';
                     end if;

                     if new.j90_valor is not null then
                         sLog = sLog || 'valor: ' || new.j90_valor || ' - ';
                     end if;

                     elseif TG_OP = 'UPDATE' then

                         if new.j90_codigo  <> old.j90_codigo  then
                         sLog = sLog || 'codigo alterado de: '||old.j90_codigo||' para: '||new.j90_codigo|| ' - ';
                         end if;

                         if new.j90_descr  <> old.j90_descr  then
                         sLog = sLog || 'descricao alterado de: '|| old.j90_descr ||' para: '|| new.j90_descr || ' - ';
                         end if;

                         if new.j90_valor <> old.j90_valor then
                         sLog = sLog || 'valor alterado de: '||old.j90_valor ||' para: '||new.j90_valor || ' - ';
                         end if;

                     elseif TG_OP = 'DELETE' then

                     sLog = sLog || 'codigo: ' || old.j90_codigo                     || ' - ';

                     if old.j01_numcgm  is not null then
                         sLog = sLog || 'descricao: ' || old.j90_descr || ' - ';
                     end if;

                     if old.j90_valor  is not null then
                         sLog = sLog || 'valor: ' || old.j90_valor || ' - ';
                     end if;

                     end if;

                     if sLogAux <> sLog then

                         SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                     insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                         select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                     insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                         select nextval('histocorrenciamatric_ar25_sequencial_seq'), null, iCodigoOcorrencia;

                         IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                             insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_tesinter_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

 declare

 sLog text default '';
 sLogAux text default '';
 sAnos   varchar;
 iMatric integer;
 iIdbql integer;
 rLote record;
 iCodigoOcorrencia integer;
 bCampoProcesso boolean;
 sOrientacaoOld varchar;
 sOrientacaoNew varchar;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

     if TG_OP = 'INSERT' then
        sLog   = 'NO(S) EXERCICIO(S) DE '||sAnos||' TESTADA INTERNA INCLUÍDA: ';
        iIdbql = new.j39_idbql ;
     elseif TG_OP = 'UPDATE' then
        sLog   = 'NO(S) EXERCICIO(S) DE '||sAnos||' TESTADA INTERNA ALTERADA: ';
        iIdbql = new.j39_idbql ;
     elseif TG_OP = 'DELETE' then
        sLog   = 'NO(S) EXERCICIO(S) DE '||sAnos||' TESTADA INTERNA EXCLUÍDA: ';
        iIdbql = old.j39_idbql ;
     end if;

     sLogAux = sLog;

   if TG_OP = 'INSERT' then

      select coalesce(j64_descricao, '')::varchar
        into sOrientacaoNew
        from orientacao
       where j64_sequencial = new.j39_orientacao;

         sLog = sLog || 'Sequencial: ' || new.j39_sequencial ||' - ';

      if new.j39_idbql  is not null then
        sLog = sLog || 'idbql: ' || new.j39_idbql || ' - ';
      end if;

      if new.j39_orientacao  is not null then
         sLog = sLog || 'Orientação: ' || new.j39_orientacao || '-' || sOrientacaoNew  || ' - ';
      end if;

       if new.j39_testad   is not null then
         sLog = sLog || 'Testada MI: ' || new.j39_testad  ||' - ';
      end if;

      if new.j39_testle  is not null then
         sLog = sLog || 'Testada Medida: ' || new.j39_testle ||' - ';
      end if;

   elseif TG_OP = 'UPDATE' then

      select coalesce(j64_descricao, '')::varchar
        into sOrientacaoOld
        from orientacao
       where j64_sequencial = old.j39_orientacao;

      select coalesce(j64_descricao, '')::varchar
        into sOrientacaoNew
        from orientacao
       where j64_sequencial = new.j39_orientacao;

      if new.j39_sequencial  <> old.j39_sequencial  then
         sLog = sLog || 'Sequencial alterado de: '||old.j39_sequencial||' para: '||new.j39_sequencial|| ' - ';
      end if;

      if new.j39_idbql  <> old.j39_idbql  then
         sLog = sLog || 'idbql alterado de: '||old.j39_idbql||' para: '||new.j39_idbql|| ' - ';
      end if;

      if new.j39_orientacao  <> old.j39_orientacao then
         sLog = sLog || 'Orientação alterada de: '|| old.j39_orientacao || '-' || sOrientacaoOld ||' para: '|| new.j39_orientacao || '-' || sOrientacaoNew || ' - ';
      end if;

      if new.j39_testad  <> old.j39_testad then
         sLog = sLog || 'Testada MI alterada de: '|| old.j39_testad ||' para: '|| new.j39_testad || ' - ';
      end if;

      if new.j39_testle  <> old.j39_testle then
         sLog = sLog || 'Testada Medida alterada de: '|| old.j39_testle ||' para: '|| new.j39_testle || ' - ';
      end if;

   elseif TG_OP = 'DELETE' then

      select coalesce(j64_descricao, '')::varchar
        into sOrientacaoOld
        from orientacao
       where j64_sequencial = old.j39_orientacao;

      sLog = sLog || 'Sequencial: ' || old.j39_sequencial ||' - ';

      if old.j39_idbql  is not null then
         sLog = sLog || 'idbql: ' || old.j39_idbql         || ' - ';
      end if;

      if old.j39_orientacao  is not null then
         sLog = sLog || 'Orientação: ' || old.j39_orientacao || '-' || sOrientacaoOld ||' - ';
      end if;

       if old.j39_testad   is not null then
         sLog = sLog || 'Testada MI: ' || old.j39_testad  ||' - ';
      end if;

      if old.j39_testle  is not null then
         sLog = sLog || 'Testada Medida: ' || old.j39_testle ||' - ';
      end if;

   end if;

       if sLogAux <> sLog then

          SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

          insert into histocorrencia ( ar23_sequencial,
                                       ar23_id_usuario,
                                       ar23_instit,
                                       ar23_modulo,
                                       ar23_id_itensmenu,
                                       ar23_data,
                                       ar23_hora,
                                       ar23_tipo,
                                       ar23_descricao,
                                       ar23_ocorrencia )
                                select iCodigoOcorrencia,
                                       fc_getsession('DB_id_usuario')::integer,
                                       fc_getsession('DB_instit')::integer,
                                       578,
                                       fc_getsession('DB_itemmenu_acessado')::integer,
                                       current_date,
                                       substr(current_time::varchar,1,5),
                                       1,
                                       'Log de alteraÃ§Ãµes',
                                       sLog;

          if TG_OP = 'INSERT' then
             iIdbql = new.j39_idbql;
          elseif TG_OP = 'UPDATE' then
             iIdbql = new.j39_idbql;
          elseif TG_OP = 'DELETE' then
             iIdbql = old.j39_idbql;
          end if;

         FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iIdbql
             LOOP
                  insert into histocorrenciamatric ( ar25_sequencial,
                                                     ar25_matric,
                                                     ar25_histocorrencia )
                              select nextval('histocorrenciamatric_ar25_sequencial_seq'),
                                     rLote.j01_matric,
                                     iCodigoOcorrencia;

            END LOOP;

            IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
               insert into cadastro.histocorrenciaprocesso
                               ( ar201_sequencial,
                                 ar201_processo,
                                 ar201_histocorrencia )
                            select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),
                                   fc_getsession('PROCESSO_LOG'),
                                   iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_tesinterlote_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

                 declare

                 sLog text default '';
                 sLogAux text default '';
                 sAnos   varchar;
                 iMatric integer;
                 rLote record;
                 iIdbql integer;
                 iCodigoOcorrencia integer;
                 bCampoProcesso boolean;

                 begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                     if TG_OP = 'INSERT' then
                        sLog   = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTES INTER INCLUÍDO: ';
                        iIdbql = new.j69_idbql ;
                     elseif TG_OP = 'UPDATE' then
                        sLog   = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTES INTER ALTERADO: ';
                        iIdbql = new.j69_idbql ;
                     elseif TG_OP = 'DELETE' then
                        sLog   = 'NO(S) EXERCICIO(S) DE '||sAnos||' LOTES INTER EXCLUÍDO: ';
                        iIdbql = old.j69_idbql ;
                     end if;

                     sLogAux = sLog;

                     if TG_OP = 'INSERT' then

                     sLog = sLog || 'Lote inter: ' || new.j69_tesinter || ' - ';

                     if new.j69_idbql  is not null then
                         sLog = sLog || 'idbql: ' || new.j69_idbql ||' - ';
                     end if;

                     elseif TG_OP = 'UPDATE' then

                     if new.j69_tesinter  <> old.j69_tesinter  then
                         sLog = sLog || 'Lote inter alterado de: '||old.j69_tesinter||' para: '||new.j69_tesinter|| ' - ';
                     end if;

                     if new.j69_idbql  <> old.j69_idbql then
                         sLog = sLog || 'idbql alterada de: '|| old.j69_idbql ||' para: '|| new.j69_idbql || ' - ';
                     end if;

                     elseif TG_OP = 'DELETE' then

                     sLog = sLog || 'Lote inter: ' || old.j69_tesinter || ' - ';

                     if old.j69_idbql  is not null then
                         sLog = sLog || 'idbql: ' || old.j69_idbql || ' - ';
                     end if;

                     end if;

                     if sLogAux <> sLog then

                         SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                     insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                         select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                     if TG_OP = 'INSERT' then
                         iIdbql = new.j69_idbql;

                     elseif TG_OP = 'UPDATE' then
                         iIdbql = new.j69_idbql;

                     elseif TG_OP = 'DELETE' then
                         iIdbql = old.j69_idbql;

                     end if;

                     FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iIdbql
                     LOOP

                         insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                             select nextval('histocorrenciamatric_ar25_sequencial_seq'), rLote.j01_matric, iCodigoOcorrencia;

                     END LOOP;

                     IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                         insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

 CREATE OR REPLACE FUNCTION cadastro.fc_tesinteroutros_inc_alt_exc()
  RETURNS trigger
  LANGUAGE plpgsql
 AS \$function\$

                 declare

                 sLog              text default '';
                 sLogAux           text default '';

                 sAnos             varchar;
                 sOutrosOld        varchar;
                 sOutrosNew        varchar;

                 iIdbql            integer;
                 iMatric           integer;
                 iCodigoOcorrencia integer;

                 bCampoProcesso    boolean;

                 rLote             record;



                 begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

                 if TG_OP = 'INSERT' then
                    select coalesce(j92_descr, '')
                      into sOutrosNew
                      from tesintertipo
                     where j92_sequencial = new.j84_tesintertipo;

                 sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' TIPO TESTADA INTERNA INCLUÍDO: ';
                 iMatric = new.j84_tesintertipo ;

                 elseif TG_OP = 'UPDATE' then

                    select coalesce(j92_descr, '')
                      into sOutrosOld
                      from tesintertipo
                     where j92_sequencial = old.j84_tesintertipo;

                    select coalesce(j92_descr, '')
                      into sOutrosNew
                      from tesintertipo
                     where j92_sequencial = new.j84_tesintertipo;


                 sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' TIPO TESTADA INTERNA ALTERADO: ';
                 iMatric = new.j84_tesintertipo ;

                 elseif TG_OP = 'DELETE' then

                    select coalesce(j92_descr, '')
                      into sOutrosOld
                      from tesintertipo
                     where j92_sequencial = old.j84_tesintertipo;

                 sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' TIPO TESTADA INTERNA EXCLUÍDO: ';
                 iMatric = old.j84_tesintertipo ;

                 end if;

                 sLogAux = sLog;

                 if TG_OP = 'INSERT' then

                    select coalesce(j92_descr, '')
                      into sOutrosNew
                      from tesintertipo
                     where j92_sequencial = new.j84_tesintertipo;


                     sLog = sLog || 'Tipo de testada interna: ' || new.j84_tesintertipo || '-' || sOutrosNew || ' - ';

                     if new.j84_tesinter  is not null then
                         sLog = sLog || 'Testada interna: ' || new.j84_tesinter ||' - ';
                     end if;

                     if new.j84_observacao is not null and trim(new.j84_observacao) != '' then
                         sLog = sLog || 'Observacao: ' || new.j84_observacao ||' - ';
                     end if;

                 elseif TG_OP = 'UPDATE' then

                    select coalesce(j92_descr, '')
                      into sOutrosOld
                      from tesintertipo
                     where j92_sequencial = old.j84_tesintertipo;

                     if new.j84_tesintertipo  <> old.j84_tesintertipo   then
                         sLog = sLog || 'Tipo de testada interna alterado de: '|| old.j84_tesintertipo || '-' || sOutrosOld  ||' para: '||new.j84_tesintertipo || '-' || sOutrosNew || ' - ';
                     end if;

                     if new.j84_tesinter  <> old.j84_tesinter then
                         sLog = sLog || 'Testada interna alterada de: '|| old.j84_tesinter ||' para: '|| new.j84_tesinter || ' - ';
                     end if;

                     if new.j84_observacao <> old.j84_observacao then
                         sLog = sLog || 'Observacao alterada de: '|| old.j84_observacao ||' para: '|| new.j84_observacao || ' - ';
                     end if;

                 elseif TG_OP = 'DELETE' then

                     select coalesce(j92_descr, '')
                      into sOutrosOld
                      from tesintertipo
                     where j92_sequencial = old.j84_tesintertipo;

                     sLog  = sLog || 'Tipo de testada interna: ' || old.j84_tesintertipo || '-' || sOutrosOld || ' - ';

                     if old.j84_tesinter  is not null then
                         sLog = sLog || 'Testada interna: ' || old.j84_tesinter ||' - ';
                     end if;

                 end if;

                 if sLogAux <> sLog then

                 SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

                 insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu, ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
                 select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                 578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5), 1, 'log de alteracoes', sLog;

                 if (TG_OP = 'INSERT' and new.j84_tesinter is not null) then

                     select j39_idbql
                       into iIdbql
                       from tesinter
                      where j39_sequencial = new.j84_tesinter;

                  elseif (TG_OP = 'UPDATE' and new.j84_tesinter is not null) then

                     select j39_idbql
                       into iIdbql
                       from tesinter
                      where j39_sequencial = new.j84_tesinter;

                  elseif TG_OP = 'DELETE' then

                     select j39_idbql
                       into iIdbql
                       from tesinter
                      where j39_sequencial = old.j84_tesinter;

                  end if;

                 IF iIdbql IS NOT NULL THEN

                     FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iIdbql
                         LOOP
                              insert into histocorrenciamatric ( ar25_sequencial,
                                                                 ar25_matric,
                                                                 ar25_histocorrencia )
                                          select nextval('histocorrenciamatric_ar25_sequencial_seq'),
                                                 rLote.j01_matric,
                                                 iCodigoOcorrencia;

                     END LOOP;

                 END IF;

                 IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
                     insert into cadastro.histocorrenciaprocesso ( ar201_sequencial, ar201_processo, ar201_histocorrencia ) select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),fc_getsession('PROCESSO_LOG') , iCodigoOcorrencia;
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

CREATE OR REPLACE FUNCTION cadastro.fc_testada_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

   declare

      sLog              text default '';
      sLogAux           text default '';
      sLogradouro       text;

      iMatric           integer;
      iLote             integer;
      iCodigoOcorrencia integer;

      bCampoProcesso    boolean;

      rLote             record;

      sAnos             varchar;
      sOrientacaoOld    varchar;
      sOrientacaoNew    varchar;

   begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

      if TG_OP = 'DELETE' then
         SELECT upper(j14_codigo || ' - ' || j14_nome) AS logradouro
           INTO sLogradouro
         FROM testada
              JOIN ruas on testada.j36_codigo = ruas.j14_codigo
         WHERE j36_idbql = old.j36_idbql
           and j36_face = old.j36_face;
      else
         SELECT upper(j14_codigo || ' - ' || j14_nome) AS logradouro
           INTO sLogradouro
         FROM testada
              JOIN ruas on testada.j36_codigo = ruas.j14_codigo
         WHERE j36_idbql = new.j36_idbql
           and j36_face = new.j36_face;
      end if;

      if sLogradouro is null then
          sLogradouro := '';
      else
          sLogradouro := 'NO LOGRADOURO ' || sLogradouro;
      end if;

      if TG_OP = 'INSERT' then
         sLog = 'NO(S) EXERCICIO(S) DE ' || sAnos || ' TESTADA INCLUÍDA ' || sLogradouro || ':';
      elseif TG_OP = 'UPDATE' then
         sLog = 'NO(S) EXERCICIO(S) DE ' || sAnos || ' TESTADA ALTERADA ' || sLogradouro || ':';
      elseif TG_OP = 'DELETE' then
         sLog = 'NO(S) EXERCICIO(S) DE ' || sAnos || ' TESTADA EXCLUÍDA ' || sLogradouro || ':';
      end if;

      sLogAux = sLog;

      if TG_OP = 'INSERT' then
         select coalesce(j64_descricao, '')::varchar
           into sOrientacaoNew
           from orientacao
          where j64_sequencial = new.j36_orientacao;

          sLog = sLog || 'idbql: ' || new.j36_idbql || ' - ' || (select j34_idbql
                                                                   from lote
                                                                  where j34_idbql = new.j36_idbql) || ' - ';
          if new.j36_face is not null then
             sLog = sLog || 'face: ' || new.j36_face || ' - ';
          end if;
          if new.j36_codigo is not null then
              sLog = sLog || 'codigo: ' || new.j36_codigo || ' - ' || (select j14_codigo
                                                                         from ruas
                                                                        where j14_codigo = new.j36_codigo) || ' - ';
          end if;
          if new.j36_testad is not null then
              sLog = sLog || 'testad: ' || new.j36_testad || ' - ';
          end if;
          if new.j36_testle is not null then
              sLog = sLog || 'testle: ' || new.j36_testle || ' - ';
          end if;

          if new.j36_orientacao is not null then
              sLog = sLog || 'orientacao: ' || new.j36_orientacao || '-' || sOrientacaoNew || ' - ';
          end if;
      elseif TG_OP = 'UPDATE' then
         select coalesce(j64_descricao, '')::varchar
           into sOrientacaoOld
         from orientacao
         where j64_sequencial = old.j36_orientacao;

         select coalesce(j64_descricao, '')::varchar
           into sOrientacaoNew
         from orientacao
         where j64_sequencial = new.j36_orientacao;

         if new.j36_idbql <> old.j36_idbql then
            sLog = sLog || 'idbql alterado de: ' || old.j36_idbql || (select j34_idbql
                                                                      from lote
                                                                      where j34_idbql = new.j36_idbql) || ' para: ' ||
                                                                            new.j36_idbql || ( select j34_idbql
                                                                                               from lote
                                                                                               where j34_idbql = new.j36_idbql) || ' - ';
         end if;
         if new.j36_face <> old.j36_face then
            sLog = sLog || 'face alterada de: ' || old.j36_face || ' para: ' || new.j36_face || ' - ';
         end if;
         if new.j36_codigo <> old.j36_codigo then
            sLog = sLog || 'codigo alterado de: ' || old.j36_codigo || (select j14_codigo
                                                                        from ruas
                                                                        where j14_codigo = new.j36_codigo) ||
                           ' para: ' || new.j36_codigo || (select j14_codigo
                                                           from ruas
                                                           where j14_codigo = new.j36_codigo) || ' - ';
         end if;
         if new.j36_testad <> old.j36_testad then
            sLog = sLog || 'testad alterado de: ' || old.j36_testad || ' para: ' || new.j36_testad || ' - ';
         end if;
         if new.j36_testle <> old.j36_testle then
            sLog = sLog || 'testle alterado de: ' || old.j36_testle || ' para: ' || new.j36_testle || ' - ';
         end if;

         if new.j36_orientacao <> old.j36_orientacao then
            sLog = sLog || 'orientacao alterado de: ' || old.j36_orientacao || '-' || sOrientacaoOld ||
                           ' para: ' || new.j36_orientacao || '-' || sOrientacaoNew || ' - ';
         end if;
      elseif TG_OP = 'DELETE' then

         select coalesce(j64_descricao, '')::varchar
           into sOrientacaoOld
           from orientacao
          where j64_sequencial = old.j36_orientacao;

         sLog = sLog || 'idbql: ' || old.j36_idbql || (select j34_idbql
                                                       from lote
                                                       where j34_idbql = old.j36_idbql) || ' - ';
         if old.j36_face is not null then
            sLog = sLog || 'face: ' || old.j36_face || ' - ';
         end if;
         if old.j36_codigo is not null then
            sLog = sLog || 'codigo: ' || old.j36_codigo || (select j14_codigo
                                                            from ruas
                                                            where j14_codigo = old.j36_codigo) || ' - ';
         end if;
         if old.j36_testad is not null then
            sLog = sLog || 'testad: ' || old.j36_testad || ' - ';
         end if;
         if old.j36_testle is not null then
            sLog = sLog || 'testle: ' || old.j36_testle || ' - ';
         end if;

         if old.j36_orientacao is not null then
             sLog = sLog || 'orientacao: ' || old.j36_orientacao || '-' || sOrientacaoOld || ' - ';
         end if;
      end if;

      if sLog <> sLogAux then
         SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;

          insert into histocorrencia (
             ar23_sequencial,
             ar23_id_usuario,
             ar23_instit,
             ar23_modulo,
             ar23_id_itensmenu,
             ar23_data,
             ar23_hora,
             ar23_tipo,
             ar23_descricao,
             ar23_ocorrencia)
             select iCodigoOcorrencia,
                    fc_getsession('DB_id_usuario')::integer,
                    fc_getsession('DB_instit')::integer,
                    578,
                    fc_getsession('DB_itemmenu_acessado')::integer,
                    current_date,
                    substr(current_time::varchar, 1, 5),
                    1,
                    'Log de alteracoes',
                    sLog;

          if TG_OP = 'INSERT' then
             iLote = new.j36_idbql;
          elseif TG_OP = 'UPDATE' then
             iLote = new.j36_idbql;
          elseif TG_OP = 'DELETE' then
             iLote = old.j36_idbql;
          end if;

          FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iLote
          LOOP
              insert into
                      histocorrenciamatric (ar25_sequencial,
                                            ar25_matric,
                                            ar25_histocorrencia)
                                     select nextval('histocorrenciamatric_ar25_sequencial_seq'),
                                            rLote.j01_matric,
                                            iCodigoOcorrencia;

          END LOOP;

          IF fc_getsession('PROCESSO_LOG') IS NOT NULL THEN
             insert into cadastro.histocorrenciaprocesso (ar201_sequencial,
                                                          ar201_processo,
                                                          ar201_histocorrencia)
                                                  select nextval('cadastro.histocorrenciaprocesso_ar201_sequencial_seq'),
                                                         fc_getsession('PROCESSO_LOG'),
                                                         iCodigoOcorrencia;
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

CREATE OR REPLACE FUNCTION cadastro.fc_testadanumero_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

   declare

      sLog              text default '';
      sLogAux           text default '';
      sLogradouro       text;

      sAnos             varchar;

      iTestadanumero    integer;
      iLote             integer;
      iMatric           integer;
      iCodigoOcorrencia integer;

      rLote             record;

      bCampoProcesso    boolean;

   begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

      if TG_OP = 'DELETE' then
         SELECT upper(j14_codigo || ' - ' || j14_nome) AS logradouro
           INTO sLogradouro
         FROM testada
              JOIN ruas on testada.j36_codigo = ruas.j14_codigo
         WHERE j36_idbql = old.j15_idbql and j36_face = old.j15_face;
      else
         SELECT upper(j14_codigo || ' - ' || j14_nome) AS logradouro
           INTO sLogradouro
         FROM testada
              JOIN ruas on testada.j36_codigo = ruas.j14_codigo
         WHERE j36_idbql = new.j15_idbql and j36_face = new.j15_face;
      end if;

      if sLogradouro is null then
          sLogradouro := '';
      else
          sLogradouro := 'NO LOGRADOURO ' || sLogradouro;
      end if;

      if TG_OP = 'INSERT' then
         sLog     = 'NO(S) EXERCICIO(S) DE '||sAnos||' TESTADA NUMERO INCLUÍDA '|| sLogradouro ||':';
         iTestadanumero = new.j15_codigo;
      elseif TG_OP = 'UPDATE' then
         sLog     = 'NO(S) EXERCICIO(S) DE '||sAnos||' TESTADA NUMERO ALTERADA '|| sLogradouro ||':';
         iTestadanumero = new.j15_codigo;
      elseif TG_OP = 'DELETE' then
         sLog     = 'NO(S) EXERCICIO(S) DE '||sAnos||' TESTADA NUMERO EXCLUÍDA '|| sLogradouro ||':';
         iTestadanumero = old.j15_codigo;
      end if;
      sLogAux = sLog;

      if TG_OP = 'INSERT' then
         sLog = sLog || 'Codigo testada numero: ' || new.j15_codigo ||  ' - ';
         if new.j15_idbql  is not null then
            sLog = sLog || 'idbql: ' || new.j15_idbql  || ' - ';
         end if;
         if new.j15_face is not null then
            sLog = sLog || 'face: ' || new.j15_face || ' - ';
         end if;
         if new.j15_numero is not null then
            sLog = sLog || 'Numero: ' || new.j15_numero || ' - ';
         end if;
         if new.j15_compl is not null then
            sLog = sLog || 'complemento: ' || new.j15_compl || ' - ';
         end if;
         if new.j15_obs is not null then
            sLog = sLog || 'observacao: ' || new.j15_obs || ' - ';
         end if;
      elseif TG_OP = 'UPDATE' then
         if new.j15_codigo <> old.j15_codigo then
            sLog = sLog || 'codigo alterado de: ' || old.j15_codigo || ' para: ' ||  new.j15_codigo || ' - ';
         end if;
         if new.j15_idbql <> old.j15_idbql then
            sLog = sLog || 'Idbql alterado de: ' || old.j15_idbql ||  ' para: ' || new.j15_idbql|| ' - ';
         end if;
         if new.j15_face <> old.j15_face then
            sLog = sLog || 'face alterada de: ' || old.j15_face || ' para: ' || new.j15_face || ' - ';
         end if;
         if new.j15_numero <> old.j15_numero then
            sLog = sLog || 'numero alterado de: ' || old.j15_numero || ' para: ' || new.j15_numero || ' - ';
         end if;
         if new.j15_compl <> old.j15_compl then
            sLog = sLog || 'complemento alterado de: ' || old.j15_compl || ' para: ' || new.j15_compl ||' - ';
         end if;
         if new.j15_obs <> old.j15_obs then
            sLog = sLog || 'Observacao alterada de: ' || old.j15_obs || ' para: ' || new.j15_obs || ' - ';
         end if;
      elseif TG_OP = 'DELETE' then
         sLog = sLog || 'Codigo testada numero: ' || old.j15_codigo ||  ' - ';
         if old.j15_idbql  is not null then
            sLog = sLog || 'idbql: ' || old.j15_idbql ||  ' - ';
         end if;
         if old.j15_face is not null then
            sLog = sLog || 'face: ' || old.j15_face ||  ' - ';
         end if;
         if old.j15_numero is not null then
            sLog = sLog || 'Numero: ' || old.j15_numero || ' - ';
         end if;
         if old.j15_compl is not null then
            sLog = sLog || 'complemento: ' || old.j15_compl || ' - ';
         end if;
         if old.j15_obs is not null then
            sLog = sLog || 'observacao: ' || old.j15_obs || ' - ';
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
            iLote = new.j15_idbql;
         elseif TG_OP = 'UPDATE' then
            iLote = new.j15_idbql;
         elseif TG_OP = 'DELETE' then
            iLote = old.j15_idbql;
         end if;

         FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iLote
            LOOP

               insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                  select nextval('histocorrenciamatric_ar25_sequencial_seq'), rLote.j01_matric, iCodigoOcorrencia;

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
\$function\$;

CREATE OR REPLACE FUNCTION cadastro.fc_testadaprincipal_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog              text default '';
        sLogAux           text default '';

        sAnos             varchar;

        iLote             integer;
        iMatric           integer;
        iCodigoOcorrencia integer;

        rLote             record;

        bCampoProcesso    boolean;

    begin

         select fc_getsession('DB_anoretroativo') into sAnos;

         if sAnos is null then
            select fc_getsession('DB_anousu') into sAnos;
         end if;

        if TG_OP = 'INSERT' then
           sLog              = 'NO(S) EXERCICIO(S) DE '||sAnos||' TESTADA PRINCIPAL INCLUÍDA: ';
        elseif TG_OP = 'UPDATE' then
           sLog              = 'NO(S) EXERCICIO(S) DE '||sAnos||' TESTADA PRINCIPAL ALTERADA: ';
        elseif TG_OP = 'DELETE' then
           sLog              = 'NO(S) EXERCICIO(S) DE '||sAnos||' TESTADA PRINCIPAL EXCLUÍDA: ';
        end if;
        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'idbql: ' || new.j49_idbql ||   ' - ';
           if new.j49_face  is not null then
              sLog = sLog || 'face: ' || new.j49_face || ' - ';
           end if;
           if new.j49_codigo is not null then
              sLog = sLog || 'codigo: ' || new.j49_codigo ||  ' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j49_idbql <> old.j49_idbql then
              sLog = sLog || 'idbql alterado de: ' || old.j49_idbql  ' para: ' ||new.j49_idbql  || ' - ';
           end if;
           if new.j49_face <> old.j49_face then
              sLog = sLog || 'face alterada de: ' || old.j49_face || ' para: ' || new.j49_face|| ' - ';
           end if;
           if new.j49_codigo <> old.j49_codigo then
              sLog = sLog || 'codigo alterado de: ' || old.j49_codigo || ' para:      ' || new.j49_codigo || ' - ';
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'idbql: ' || old.j49_idbql  ||  ' - ';
           if old.j49_face  is not null then
              sLog = sLog || 'face: ' || old.j49_face  || ' - ';
           end if;
           if old.j49_codigo is not null then
              sLog = sLog || 'codigo: ' || old.j49_codigo || ' - ';
           end if;
        end if;

        if sLogAux <> sLog then
           SELECT nextval('histocorrencia_ar23_sequencial_seq') INTO iCodigoOcorrencia;
           insert into histocorrencia ( ar23_sequencial, ar23_id_usuario, ar23_instit, ar23_modulo, ar23_id_itensmenu,
                                        ar23_data, ar23_hora, ar23_tipo, ar23_descricao, ar23_ocorrencia )
               select iCodigoOcorrencia, fc_getsession('DB_id_usuario')::integer, fc_getsession('DB_instit')::integer,
                      578, fc_getsession('DB_itemmenu_acessado')::integer, current_date, substr(current_time::varchar,1,5),
                      1, 'log de alteracoes', sLog;

           if TG_OP = 'INSERT' then
              iLote = new.j49_idbql;
           elseif TG_OP = 'UPDATE' then
              iLote = new.j49_idbql;
           elseif TG_OP = 'DELETE' then
              iLote = old.j49_idbql;
           end if;

           FOR rLote IN SELECT j01_matric FROM iptubase WHERE j01_idbql = iLote
               LOOP
               insert into histocorrenciamatric ( ar25_sequencial, ar25_matric, ar25_histocorrencia )
                   select nextval('histocorrenciamatric_ar25_sequencial_seq'), rLote.j01_matric,iCodigoOcorrencia;
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
\$function\$;

CREATE OR REPLACE FUNCTION cadastro.fc_iptudiversos_inc_alt_exc()
 RETURNS trigger
 LANGUAGE plpgsql
AS \$function\$

    declare

        sLog                    text default '';
        sLogAux                 text default '';

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
           iMatric   = new.j80_matric;
        elseif TG_OP = 'UPDATE' then
           sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' MATRICULA ALTERADA: ';
           iMatric   = new.j80_matric;
        elseif TG_OP = 'DELETE' then
           sLog      = 'NO(S) EXERCICIO(S) DE '||sAnos||' MATRICULA EXCLUÍDA: ';
           iMatric   = old.j80_matric;
        end if;
        sLogAux = sLog;

        if TG_OP = 'INSERT' then
           sLog = sLog || 'matricula: ' || new.j80_matric  || ' - ';
           if new.j80_areatrib is not null then
              sLog = sLog || 'area tributada: ' || new.j80_areatrib || ' - ';
           end if;
           if new.j80_profund is not null then
              sLog = sLog || 'profundidade: ' || new.j80_profund  || ' - ';
           end if;
        elseif TG_OP = 'UPDATE' then
           if new.j80_matric  <> old.j80_matric then
              sLog = sLog || 'matricula alterada de: ' ||old.j80_matric|| ' para: ' ||new.j80_matric|| ' - ';
           end if;
           if new.j80_areatrib <> old.j80_areatrib then
              sLog = sLog || 'area tributada alterada de: ' || old.j80_areatrib || ' para: ' ||new.j80_areatrib || ' - ';
           end if;
           if new.j80_profund <> old.j80_profund  then
              sLog = sLog || 'profundidade alterada de: ' || old.j80_profund || ' para: ' ||new.j80_profund || ' - ';
           end if;
        elseif TG_OP = 'DELETE' then
           sLog = sLog || 'matricula: ' || old.j80_matric || ' - ';
           if old.j80_areatrib is not null then
               sLog = sLog || 'area tributada: ' || old.j80_areatrib || ' - ';
           end if;
           if old.j80_profund is not null then
               sLog = sLog || 'area tributada: ' || old.j80_profund || ' - ';
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

drop   trigger if exists tg_iptubase_inc_alt_exc on iptubase;
create trigger tg_iptubase_inc_alt_exc after insert or update or delete on iptubase for each row execute procedure fc_iptubase_inc_alt_exc();
drop   trigger if exists tg_iptuconstrdemo_inc_alt_exc on iptuconstrdemo;
create trigger tg_iptuconstrdemo_inc_alt_exc after insert or update or delete on iptuconstrdemo for each row execute procedure fc_iptuconstrdemo_inc_alt_exc();
drop   trigger if exists tg_iptuconstr_inc_alt_exc on iptuconstr;
create trigger tg_iptuconstr_inc_alt_exc after insert or update or delete on iptuconstr for each row execute procedure fc_iptuconstr_inc_alt_exc();
drop   trigger if exists tg_iptubasepredio_inc_alt_exc on iptubasepredio;
create trigger tg_iptubasepredio_inc_alt_exc after insert or update or delete on iptubasepredio for each row execute procedure fc_iptubasepredio_inc_alt_exc();
drop   trigger if exists tg_iptubasecondomino_inc_alt_exc on iptubasecondominio;
create trigger tg_iptubasecondomino_inc_alt_exc after insert or update or delete on iptubasecondominio for each row execute procedure fc_iptubasecondominio_inc_alt_exc();
drop   trigger if exists tg_iptubaseregimovel_inc_alt_exc on iptubaseregimovel;
create trigger tg_iptubaseregimovel_inc_alt_exc after insert or update or delete on iptubaseregimovel for each row execute procedure fc_iptubaseregimovel_inc_alt_exc();
drop   trigger if exists tg_iptuconstrhabite_inc_alt_exc on iptuconstrhabite;
create trigger tg_iptuconstrhabite_inc_alt_exc after insert or update or delete on iptuconstrhabite for each row execute procedure fc_iptuconstrhabite_inc_alt_exc();
drop   trigger if exists tg_lote_inc_alt_exc on lote;
create trigger tg_lote_inc_alt_exc after insert or update or delete on lote for each row execute procedure fc_lote_inc_alt_exc();
drop   trigger if exists tg_testadanumero_inc_alt_exc on testadanumero;
create trigger tg_testadanumero_inc_alt_exc after insert or update or delete on testadanumero for each row execute procedure fc_testadanumero_inc_alt_exc();
drop   trigger if exists tg_testada_inc_alt_exc on testada;
create trigger tg_testada_inc_alt_exc after insert or update or delete on testada for each row execute procedure fc_testada_inc_alt_exc();
drop   trigger if exists tg_testadaprincipal_inc_alt_exc on testpri;
create trigger tg_testadaprincipal_inc_alt_exc after insert or update or delete on testpri for each row execute procedure fc_testadaprincipal_inc_alt_exc();
drop   trigger if exists tg_carlote_inc_alt_exc on carlote;
create trigger tg_carlote_inc_alt_exc after insert or update or delete on carlote for each row execute procedure fc_carlote_inc_alt_exc();
drop   trigger if exists tg_carconstr_inc_alt_exc on carconstr;
create trigger tg_carconstr_inc_alt_exc after insert or update or delete on carconstr for each row execute procedure fc_carconstr_inc_alt_exc ();
drop   trigger if exists tg_setorfiscal_inc_alt_exc on setorfiscal;
create trigger tg_setorfiscal_inc_alt_exc after insert or update or delete on setorfiscal for each row execute procedure fc_setorfiscal_inc_alt_exc();
drop   trigger if exists tg_loteam_inc_alt_exc on loteam;
create trigger tg_loteam_inc_alt_exc after insert or update or delete on loteam for each row execute procedure fc_loteam_inc_alt_exc();
drop   trigger if exists tg_iptuender_inc_alt_exc on iptuender;
create trigger tg_iptuender_inc_alt_exc after insert or update or delete on iptuender for each row execute procedure fc_iptuender_inc_alt_exc();
drop   trigger if exists tg_iptuisen_inc_alt_exc on iptuisen;
create trigger tg_iptuisen_inc_alt_exc after insert or update or delete on iptuisen for each row execute procedure fc_iptuisen_inc_alt_exc();
drop   trigger if exists tg_lotesetorfiscal_inc_alt_exc on lotesetorfiscal;
create trigger tg_lotesetorfiscal_inc_alt_exc after insert or update or delete on lotesetorfiscal for each row execute procedure fc_lotesetorfiscal_inc_alt_exc();
drop   trigger if exists tg_loteloteam_inc_alt_exc on loteloteam;
create trigger tg_loteloteam_inc_alt_exc after insert or update or delete on loteloteam for each row execute procedure fc_loteloteam_inc_alt_exc();
drop   trigger if exists tg_tesinter_inc_alt_exc on tesinter;
create trigger tg_tesinter_inc_alt_exc after insert or update or delete on tesinter for each row execute procedure fc_tesinter_inc_alt_exc();
drop   trigger if exists tg_tesinterlote_inc_alt_exc on tesinterlote;
create trigger tg_tesinterlote_inc_alt_exc after insert or update or delete on tesinterlote for each row execute procedure fc_tesinterlote_inc_alt_exc();
drop   trigger if exists tg_tesinteroutros_inc_alt_exc on tesinteroutros;
create trigger tg_tesinteroutros_inc_alt_exc after insert or update or delete on tesinteroutros for each row execute procedure fc_tesinteroutros_inc_alt_exc();
drop   trigger if exists tg_lotedist_inc_alt_exc on lotedist;
create trigger tg_lotedist_inc_alt_exc after insert or update or delete on lotedist for each row execute procedure fc_lotedist_inc_alt_exc();
drop   trigger if exists tg_loteloc_inc_alt_exc on loteloc;
create trigger tg_loteloc_inc_alt_exc after insert or update or delete on loteloc for each row execute procedure fc_loteloc_inc_alt_exc();
drop   trigger if exists tg_imobil_inc_alt_exc on imobil;
create trigger tg_imobil_inc_alt_exc after insert or update or delete on imobil for each row execute procedure fc_imobil_inc_alt_exc();
drop   trigger if exists tg_constrescr_inc_alt_exc on constrescr;
create trigger tg_constrescr_inc_alt_exc after insert or update or delete on constrescr for each row execute procedure fc_constrescr_inc_alt_exc();
drop   trigger if exists tg_promitente_inc_alt_exc on promitente;
create trigger tg_promitente_inc_alt_exc after insert or update or delete on promitente for each row execute procedure fc_promitente_inc_alt_exc();
drop   trigger if exists tg_propri_inc_alt_exc on propri;
create trigger tg_propri_inc_alt_exc after insert or update or delete on propri for each row execute procedure fc_propri_inc_alt_exc();
drop   trigger if exists tg_isenproc_inc_alt_exc on isenproc;
create trigger tg_isenproc_inc_alt_exc after insert or update or delete on isenproc for each row execute procedure fc_isenproc_inc_alt_exc();
drop   trigger if exists tg_isentaxa_inc_alt_exc on isentaxa;
create trigger tg_isentaxa_inc_alt_exc after insert or update or delete on isentaxa for each row execute procedure fc_isentaxa_inc_alt_exc();
drop   trigger if exists tg_constrcar_inc_alt_exc on constrcar;
create trigger tg_constrcar_inc_alt_exc after insert or update or delete on constrcar for each row execute procedure fc_constrcar_inc_alt_exc();
drop   trigger if exists tg_iptuant_inc_alt_exc on iptuant;
create trigger tg_iptuant_inc_alt_exc after insert or update or delete on iptuant for each row execute procedure fc_iptuant_inc_alt_exc();
drop   trigger if exists tg_matricobs_inc_alt_exc on matricobs;
create trigger tg_matricobs_inc_alt_exc after insert or update or delete on matricobs for each row execute procedure fc_matricobs_inc_alt_exc();
drop   trigger if exists tg_iptudiversos_inc_alt_exc on iptudiversos;
create trigger tg_iptudiversos_inc_alt_exc after insert or update or delete on iptudiversos for each row execute procedure fc_iptudiversos_inc_alt_exc();



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

drop trigger if exists tg_carconstr_inc_alt_exc on carconstr;
drop trigger if exists tg_carlote_inc_alt_exc on carlote;
drop trigger if exists tg_constrcar_inc_alt_exc on constrcar;
drop trigger if exists tg_constrescr_inc_alt_exc on constrescr;
drop trigger if exists tg_imobil_inc_alt_exc on imobil;
drop trigger if exists tg_iptuant_inc_alt_exc on iptuant;
drop trigger if exists tg_iptubasecondomino_inc_alt_exc on iptubasecondominio;
drop trigger if exists tg_iptubase_inc_alt_exc on iptubase;
drop trigger if exists tg_iptubasepredio_inc_alt_exc on iptubasepredio;
drop trigger if exists tg_iptubaseregimovel_inc_alt_exc on iptubaseregimovel;
drop trigger if exists tg_iptuconstrdemo_inc_alt_exc on iptuconstrdemo;
drop trigger if exists tg_iptuconstrhabite_inc_alt_exc on iptuconstrhabite;
drop trigger if exists tg_iptuconstr_inc_alt_exc on iptuconstr;
drop trigger if exists tg_iptudiversos_inc_alt_exc on iptudiversos;
drop trigger if exists tg_iptuender_inc_alt_exc on iptuender;
drop trigger if exists tg_iptuisen_inc_alt_exc on iptuisen;
drop trigger if exists tg_isenproc_inc_alt_exc on isenproc;
drop trigger if exists tg_isentaxa_inc_alt_exc on isentaxa;
drop trigger if exists tg_loteam_inc_alt_exc on loteam;
drop trigger if exists tg_lotedist_inc_alt_exc on lotedist;
drop trigger if exists tg_lote_inc_alt_exc on lote;
drop trigger if exists tg_loteloc_inc_alt_exc on loteloc;
drop trigger if exists tg_loteloteam_inc_alt_exc on loteloteam;
drop trigger if exists tg_lotesetorfiscal_inc_alt_exc on lotesetorfiscal;
drop trigger if exists tg_matricobs_inc_alt_exc on matricobs;
drop trigger if exists tg_promitente_inc_alt_exc on promitente;
drop trigger if exists tg_propri_inc_alt_exc on propri;
drop trigger if exists tg_setorfiscal_inc_alt_exc on setorfiscal;
drop trigger if exists tg_tesinter_inc_alt_exc on tesinter;
drop trigger if exists tg_tesinterlote_inc_alt_exc on tesinterlote;
drop trigger if exists tg_tesinteroutros_inc_alt_exc on tesinteroutros;
drop trigger if exists tg_testada_inc_alt_exc on testada;
drop trigger if exists tg_testadanumero_inc_alt_exc on testadanumero;
drop trigger if exists tg_testadaprincipal_inc_alt_exc on testpri;

drop function if exists fc_carconstr_inc_alt_exc ();
drop function if exists fc_carlote_inc_alt_exc();
drop function if exists fc_constrcar_inc_alt_exc();
drop function if exists fc_constrescr_inc_alt_exc();
drop function if exists fc_imobil_inc_alt_exc();
drop function if exists fc_iptuant_inc_alt_exc();
drop function if exists fc_iptubasecondominio_inc_alt_exc();
drop function if exists fc_iptubase_inc_alt_exc();
drop function if exists fc_iptubasepredio_inc_alt_exc();
drop function if exists fc_iptubaseregimovel_inc_alt_exc();
drop function if exists fc_iptuconstrdemo_inc_alt_exc();
drop function if exists fc_iptuconstrhabite_inc_alt_exc();
drop function if exists fc_iptuconstr_inc_alt_exc();
drop function if exists fc_iptudiversos_inc_alt_exc();
drop function if exists fc_iptuender_inc_alt_exc();
drop function if exists fc_iptuisen_inc_alt_exc();
drop function if exists fc_isenproc_inc_alt_exc();
drop function if exists fc_isentaxa_inc_alt_exc();
drop function if exists fc_loteam_inc_alt_exc();
drop function if exists fc_lotedist_inc_alt_exc();
drop function if exists fc_lote_inc_alt_exc();
drop function if exists fc_loteloc_inc_alt_exc();
drop function if exists fc_loteloteam_inc_alt_exc();
drop function if exists fc_lotesetorfiscal_inc_alt_exc();
drop function if exists fc_matricobs_inc_alt_exc();
drop function if exists fc_promitente_inc_alt_exc();
drop function if exists fc_propri_inc_alt_exc();
drop function if exists fc_setorfiscal_inc_alt_exc();
drop function if exists fc_tesinter_inc_alt_exc();
drop function if exists fc_tesinterlote_inc_alt_exc();
drop function if exists fc_tesinteroutros_inc_alt_exc();
drop function if exists fc_testada_inc_alt_exc();
drop function if exists fc_testadanumero_inc_alt_exc();
drop function if exists fc_testadaprincipal_inc_alt_exc();

SQL
        );
    }
}
