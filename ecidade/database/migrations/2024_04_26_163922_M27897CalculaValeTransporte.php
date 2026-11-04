<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M27897CalculaValeTransporte extends Migration
{
    public function up()
 {
    DB::connection()->getPdo()->exec(<<<SQL
      create or replace function fc_valetransporte_dias_afasta_vr(integer,integer,integer) returns setof tp_dias_afastamento as
$$
declare
    _registro           alias for $1;
    _ano                alias for $2;
    _mes                alias for $3;

    ano_              integer default 0;
    mes_              integer default 0;
    dias_mes_         integer default 0;
    ano_ant_          integer default 0;
    mes_ant_          integer default 0;
    dias_mes_ant_     integer default 0;
    dias_afa_         integer default 0;
    dias_fer_         integer default 0;
    retorno_          integer default 0;

    rTpDiasAfasta     tp_dias_afastamento%ROWTYPE;

begin

retorno_ = 0;

rTpDiasAfasta.riDiasMes    := 0;
rTpDiasAfasta.riDiasMesAnt := 0;
rTpDiasAfasta.riDiasAfasta := 0;
rTpDiasAfasta.riDiasFerias := 0;

---- verifica os dias de afastamento dos assentamentos referentes ao mês anterior ao do processamento
if _mes = 1 then
   mes_ant_ := 12;
   ano_ant_ := _ano - 1;
else
  mes_ant_ := _mes - 1;
  ano_ant_ := _ano;
end if;

-- raise notice ' 1 ano     %', _ano;
-- raise notice ' 2 mes     %', _mes;
-- raise notice ' 3 ano_ant %', ano_ant_;
-- raise notice ' 4 mes_ant %', mes_ant_;

select count(*) into dias_mes_ant_ 
from 
(
select distinct
       h16_regist,
       date_part('dow', dtconc+generate_series(0, dtterm - dtconc)) as dia_semana, 
       dtconc+generate_series(0, dtterm - dtconc) as data_dia
from
  (select h16_regist, 
          h16_dtconc,
          h16_dtterm,
          case when h16_dtconc < to_date(ano_ant_||'-'||mes_ant_||'-01','YYYY-mm-dd') 
               then to_date(ano_ant_||'-'||mes_ant_||'-01','YYYY-mm-dd') 
               else h16_dtconc 
          end as dtconc,
          case when h16_dtterm > to_date(ano_ant_||'-'||mes_ant_||'-'||ndias(ano_ant_,mes_ant_)::char(2),'YYYY-mm-dd') or h16_dtterm is null
               then to_date(ano_ant_||'-'||mes_ant_||'-'||ndias(ano_ant_,mes_ant_)::char(2),'YYYY-mm-dd') 
               else h16_dtterm 
          end as dtterm
        from assenta 
           inner join tipoasse on h12_codigo = h16_assent           
           where h12_assent in ('001','003','004','006','025','029','030','032','039','041','044','049','052','053','057','062','067','100','100','118','127','130','158') 
        and h16_regist = _registro
        and (h16_dtconc <= to_date(ano_ant_||'-'||mes_ant_||'-'||ndias(ano_ant_,mes_ant_)::char(2),'YYYY-mm-dd') 
        and (h16_dtterm >= to_date(ano_ant_||'-'||mes_ant_||'-01','YYYY-mm-dd') or h16_dtterm is null )) 
     ) as x
) as y 
    inner join rhpessoalmov on rh02_anousu = ano_ant_ 
                            and rh02_mesusu = mes_ant_
                            and rh02_regist = _registro
    left join rhlotacalend  on rh02_lota   = rh64_lota
    left outer join calendf on r62_calend::bigint  = rh64_calend and r62_data = data_dia 
where r62_data is null
-- and dia_semana between 1 and 5
;

retorno_ = dias_mes_ + dias_mes_ant_ + dias_afa_;

rTpDiasAfasta.riDiasMes    := dias_mes_;
rTpDiasAfasta.riDiasMesAnt := dias_mes_ant_;
rTpDiasAfasta.riDiasFerias := dias_fer_;
rTpDiasAfasta.riDiasAfasta := dias_afa_;

raise notice ' 5 retorno_      %', retorno_;
raise notice ' 6 dias_me_s     %', dias_mes_;
raise notice ' 7 dias_mes_ant_ %', dias_mes_ant_;
raise notice ' 8 dias_fer_     %', dias_fer_;
raise notice ' 9 dias_afas_    %', dias_afa_;
raise notice ' 10 ano_         %', ano_;
raise notice ' 11 mes_         %', mes_;

return next rTpDiasAfasta;

return;

-- return retorno_;

end;
$$
language 'plpgsql';         
SQL
     );
 }

 public function down()
 {
    DB::connection()->getPdo()->exec(<<<SQL
      drop function fc_valetransporte_dias_afasta_vr(integer,integer,integer);
SQL
     );

 }
}
