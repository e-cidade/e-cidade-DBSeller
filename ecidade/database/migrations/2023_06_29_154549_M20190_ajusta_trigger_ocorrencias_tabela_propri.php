<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M20190AjustaTriggerOcorrenciasTabelaPropri extends Migration
{
    public function up()
    {
        $sql = <<<SQL
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
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' PROPRIEDADE INCLUÕDA: ';
                    iMatric = new.j42_matric ;
                 elseif TG_OP = 'UPDATE' then
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' PROPRIEDADE ALTERADA: ';
                    iMatric = new.j42_matric ;
                 elseif TG_OP = 'DELETE' then
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' PROPRIEDADE EXCLUÕDA: ';
                    iMatric = old.j42_matric ;
                 end if;

                 sLogAux = sLog;

                 if TG_OP = 'INSERT' then

                 sLog = sLog || 'Matricula: ' || new.j42_matric ||' - ';
                 sLog = sLog || 'CGM: ' || new.j42_numcgm   || ' - ';

                 elseif TG_OP = 'UPDATE' then

                 if new.j42_matric   <> old.j42_matric   then
                 sLog = sLog || 'Matricula alterada de: '||old.j42_matric ||' para: '||new.j42_matric || ' - ';
                 end if;

                 if new.j42_numcgm  <> old.j42_numcgm  then
                    sLog = sLog || 'CGM alterado de: '||old.j42_numcgm||' para: '||new.j42_numcgm|| ' - ';
                 end if;

                 if new.j42_fracaoproprietario  <> old.j42_fracaoproprietario  then
                    sLog = sLog || 'Fracao alterada de: '||old.j42_fracaoproprietario||' para: '||new.j42_fracaoproprietario|| ' - ';
                 end if;

                 if new.j42_arealoteproprietario  <> old.j42_arealoteproprietario then
                    sLog = sLog || '¡rea alterada de: '||old.j42_arealoteproprietario||' para: '||new.j42_arealoteproprietario|| ' - ';
                 end if;

                 if new.j42_tipoproprietario  <> old.j42_tipoproprietario  then
                    select j163_tipoproprietario || ' - ' || j163_descricao into sTipoProprietarioNovo from tipoproprietario where j163_tipoproprietario = new.j42_tipoproprietario;
                    select j163_tipoproprietario || ' - ' || j163_descricao into sTipoProprietarioVelho from tipoproprietario where j163_tipoproprietario = old.j42_tipoproprietario;
                    sLog = sLog || 'Tipo de propriet·rio alterado de: '||sTipoProprietarioVelho||' para: '||sTipoProprietarioNovo|| ' - ';
                 end if;

                 sLog = sLog || 'Propriet·rio: CGM ' || new.j42_numcgm;

                 elseif TG_OP = 'DELETE' then

                 sLog = sLog || 'Matricula: ' || old.j42_matric ||' - ';

                 if old.j42_numcgm  is not null then
                    sLog = sLog || 'CGM: ' || old.j42_numcgm || ' - ';
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
SQL;

        DB::connection()->getPdo()->exec($sql);
    }

    public function down()
    {   
        $sql = <<<SQL
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
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' PROPRIEDADE INCLUÕDA: ';
                    iMatric = new.j42_matric ;
                 elseif TG_OP = 'UPDATE' then
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' PROPRIEDADE ALTERADA: ';
                    iMatric = new.j42_matric ;
                 elseif TG_OP = 'DELETE' then
                    sLog    = 'NO(S) EXERCICIO(S) DE '||sAnos||' PROPRIEDADE EXCLUÕDA: ';
                    iMatric = old.j42_matric ;
                 end if;

                 sLogAux = sLog;

                 if TG_OP = 'INSERT' then

                 sLog = sLog || 'Matricula: ' || new.j42_matric ||' - ';
                 sLog = sLog || 'CGM: ' || new.j42_numcgm   || ' - ';

                 /*
                 if new.j42_tipoproprietario   is not null then
                 select j163_tipoproprietario || ' - ' || j163_descricao into sTipoProprietarioNovo from tipoproprietario where j163_tipoproprietario = new.j42_tipoproprietario;
                 sLog = sLog || 'Tipo de Propriet√°rio: ' || sTipoProprietarioNovo || ' - ';
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
                 sLog = sLog || 'Tipo de Propriet√°rio: '||sTipoProprietarioVelho||' para: '||sTipoProprietarioNovo|| ' - ';
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
                 sLog = sLog || 'Tipo de Propriet√°rio: ' || sTipoProprietarioVelho || ' - ';
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
SQL;
        
        DB::connection()->getPdo()->exec($sql);
    }
}
