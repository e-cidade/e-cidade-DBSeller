<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class M26791AdicionaAjustesFcCalcula extends Migration
{
    public function up()
    {
        $sql = <<<SQL
create or replace function fc_calcula(integer,integer,integer,date,date,integer) returns varchar as 
$$
begin
  return fc_calcula($1, $2, $3, $4, $5, $6, null);
end
$$
language 'plpgsql';

create or replace function fc_calcula(integer,integer,integer,date,date,integer,varchar) returns varchar as 
$$
declare
numpre          alias for $1;
numpar          alias for $2;
creceita        alias for $3;
dtemite         alias for $4;
dtvenc          alias for $5;
subdir          alias for $6;
nomedebitos     alias for $7; -- Nome alternativo para a tabela debitos (default = debitos)

iFormaCorrecao integer default 2;
iInstit        integer;

v_subdir        integer;
numero_erro     char(1) default '1';    
v_debitos       record;
record_numpre   record;
record_alias    record;
record_grava    record;
record_numpref  record;
v_composicao    record;

venc_unic       date default current_date;
venc_unic1      date;
venc_unic2      date;
num_par         integer;

valor_receita   float8 default 0;
correcao        float8 default 0;
juro            float8 default 0;
multa           float8 default 0;
desconto        float8 default 0;
valor_hist      float8 default 0;
receita         integer;

v_recjur        integer default 0;
v_recmul        integer default 0;

v_integr        boolean;

k00_operac      integer;
k06_operac      integer;
k09_operac      integer;
qualoperac      boolean default false;
dtoper          date;
datavenc        date;
unica           boolean default false;

sqlrecibo       char(255);

vlrestorno      float8 default 0;
vlrcorrecao     float8 default 0;
vlrjuros        float8 default 0;
vlrmulta        float8 default 0;
vlrdesconto     float8 default 0;
vlrinflator     float8 default 0;
nperccalc       float8 default 100;
qrinflator      varchar(5);
vlrtotinf       float8 default 0;
qinflator       varchar(5);
calcula         boolean;
processa        boolean default false;
issqnvariavel   boolean default false;
v_exerc           integer;
v_dtoper        date;
v_calculoinfla  float8;

totperc             float8  default 0;
nao_tem_recibo  boolean default false;

lRaise         boolean default false;

nValorHistorico float8 default 0;
nValorCorrecao  float8 default 0;
nValorJuros     float8 default 0;
nValorMulta     float8 default 0;
nValorDesconto  float8 default 0;

nAcumHistorico  float8 default 0;
nAcumCorrecao   float8 default 0;
nAcumJuros      float8 default 0;
nAcumMulta      float8 default 0;
nAcumDesconto   float8 default 0;

-- diferencas das proporcionalidades arrematric e arreinscr
nDiferencaHis   float8 default 0;
nDiferencaCor   float8 default 0;
nDiferencaJur   float8 default 0;
nDiferencaMul   float8 default 0;
nDiferencaDes   float8 default 0;

nComposCorrecao   numeric(15,2) default 0;
nComposJuros      numeric(15,2) default 0;
nComposMulta      numeric(15,2) default 0;

nCorreComposJuros numeric(15,2) default 0;
nCorreComposMulta numeric(15,2) default 0;

nPercMulta float8 default 0;

historic        integer default 0;

sNomeDebitos    varchar default 'debitos';

unicaJuroMulta  boolean default TRUE;
iExerc          integer;

begin

lRaise := ( case when fc_getsession('DB_debugon') is null then false else true end );
if lRaise is true then
if fc_getsession('db_debug') <> '' then  
perform fc_debug('<calcula> Processando calculo de correcao, juros e multa...', lRaise, false, false);
else 
perform fc_debug('<calcula> Processando calculo de correcao, juros e multa...', lRaise, true, false);
end if;
end if;

select k00_instit
into iInstit
from arreinstit
where k00_numpre = numpre;

if iInstit is null then
select codigo
into iInstit
from db_config
where prefeitura is true
limit 1;
end if;

if fc_getsession('DB_anousu') is not null then
  select cast( fc_getsession('DB_anousu') as integer )  into iExerc;
else
  SELECT date_part('year', now()) into iExerc;
end if;

select k03_encargoscarneunica  into unicaJuroMulta from caixa.numpref where k03_anousu = iExerc and k03_instit = iInstit;

if nomedebitos is not null or trim(nomedebitos) <> '' then
sNomeDebitos := nomedebitos;
end if;

v_subdir := subdir;
if v_subdir >= 10000 then
v_subdir := v_subdir - 10000;
end if;

select k03_separajurmulparc
into iFormaCorrecao
from numpref
where k03_instit = iInstit
and k03_anousu = v_subdir;
if lRaise is true then
perform fc_debug('<calcula> numpre: '||numpre||' - iFormaCorrecao: '||iFormaCorrecao, lRaise, false,false);
end if;

select k03_recjur, 
    k03_recmul 
into v_recjur, 
    v_recmul 
from numpref 
order by k03_anousu desc 
limit 1;

for record_numpre in select distinct * 
                    from ( select distinct 
                                k00_numpre,
                                k00_numpar  
                            from arrecad
                            where k00_numpre = numpre
                            
                            union all
                            
                            select distinct 
                                recibo.k00_numpre,
                                recibo.k00_numpar
                            from recibo
                                left outer join arrepaga on recibo.k00_numpre = arrepaga.k00_numpre
                            where recibo.k00_numpre = numpre 
                            and arrepaga.k00_numpre is null
                            
                            union all
                            
                            select distinct 
                                k99_numpre_n,
                                1 as k00_numpar
                            from db_reciboweb
                            where k99_numpre_n = numpre ) as x
                    order by k00_numpre,k00_numpar loop

if numpar != 0 then

if lRaise is true then
    perform fc_debug('<calcula> numpar diferente de 0',lRaise,false,false);
end if;
if record_numpre.k00_numpar != numpar then
    num_par := 0;
else
    num_par := numpar;
end if;

else

if lRaise is true then
    perform fc_debug('<calcula> numpar igual a 0',lRaise,false,false);
end if;
num_par := record_numpre.k00_numpar;
unica := true;

end if;

if lRaise is true then
perform fc_debug('<calcula> numpar: '||num_par||' - unica: '||unica, lRaise,false,false);
end if;

if num_par != 0 then

valor_receita := 0;

for record_alias in select * 
                        from (select *,
                                    'arrecad' as db_arquivo 
                                from (select k00_hist,
                                            k00_receit,
                                            k00_tipo,
                                            k00_dtoper,
                                            fc_calculavenci(k00_numpre,k00_numpar,k00_dtvenc,dtemite) as k00_dtvenc,
                                            k00_numpre,
                                            k00_numpar,
                                            k00_valor as k00_valor 
                                        from arrecad
                                    where k00_numpre = numpre 
                                        and k00_numpar = num_par ) as xx
                                        
                            union all
                            
                            select *,
                                    'recibo' as db_arquivo 
                                from (select k00_hist, 
                                            k00_receit,
                                            k00_tipo,
                                            k00_dtoper,
                                            k00_dtvenc,
                                            k00_numpre as k00_numpre,
                                            k00_numpar,
                                            k00_valor as k00_valor 
                                        from recibo
                                    where k00_numpre = numpre 
                                        and k00_numpar = num_par ) as xy
                                        
                            union all
                            
                            select *,
                                    'recuni' as db_arquivo 
                                from (select k00_hist, 
                                            k00_receit,
                                            k00_hist as k00_tipo,
                                            k00_dtoper,
                                            k00_dtvenc,
                                            k00_numnov as k00_numpre,
                                            0 as k00_numpar,
                                            k00_valor as k00_valor 
                                        from recibopaga
                                    where k00_numnov = numpre 
                                        and k00_conta = 0 ) as xz
                            ) as x
                    order by k00_numpre,k00_numpar,k00_receit,k00_hist loop

    if lRaise is true then
    perform fc_debug('<calcula> arquivo: '||record_alias.db_arquivo||' - valor: '||record_alias.k00_valor, lRaise, false, false);
    end if;
    
    if record_alias.db_arquivo = 'arrecad' then
    
    nao_tem_recibo = true;
    
    if record_alias.k00_valor = 0 then
    
        if unica = true then
        
        if lRaise is true then
            perform fc_debug('<calcula> 1 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
        end if;            
        return '6          0.0         0.00         0.00         0.00         0.00                   0.000000';
        
        else
        
        -- variavel
        if record_alias.k00_tipo != 3 then
        
            if lRaise is true then
            perform fc_debug('<calcula> 2 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
            end if;              
            return '7          0.0         0.00         0.00         0.00         0.00                   0.000000';
            
        end if;
        
        end if;
        
    end if;
    
    calcula = false;
    if ( creceita <> 0 and creceita = record_alias.k00_receit ) or creceita = 0 then 
        calcula = true;
    end if;

    if calcula = true then
        
        if lRaise is true then
        perform fc_debug('<calcula>    calcula: '||calcula,lRaise, false,false);
        end if;

        venc_unic     := dtvenc;
        receita       := record_alias.k00_receit;
        dtoper        := record_alias.k00_dtoper;
        datavenc      := record_alias.k00_dtvenc;
        valor_receita := record_alias.k00_valor;
        
        if valor_receita = 0 then
        
        select case 
                when q05_valor != 0 
                    then q05_valor 
                else q05_vlrinf 
                end  
            into valor_receita
            from issvar 
        where q05_numpre = record_alias.k00_numpre 
            and q05_numpar = record_alias.k00_numpar;
        if valor_receita is null then
            valor_receita := 0;
        else
            issqnvariavel := true;
        end if;
        end if;
        
        valor_hist := valor_hist + round(valor_receita, 2);
        qualoperac := false;
        
        -- calcula correcao 
        processa := true;

        if lRaise is true then
        perform fc_debug('<calcula>    numpre : '||record_alias.k00_numpre||' numpar : '||record_alias.k00_numpar||' receita : '||record_alias.k00_receit||' valor_receita : '||valor_receita||' k00_valor : '||record_alias.k00_valor||' iFormaCorrecao : '||iFormaCorrecao, lRaise, false,false);
        end if;


        if valor_receita <> 0 then 

        if iFormaCorrecao = 1 then

            select rnCorreComposJuros, 
                    rnCorreComposMulta, 
                    rnComposCorrecao, 
                    rnComposJuros, 
                    rnComposMulta
                into nCorreComposJuros, 
                    nCorreComposMulta, 
                    nComposCorrecao, 
                    nComposJuros, 
                    nComposMulta
                from fc_retornacomposicao(record_alias.k00_numpre, 
                                        record_alias.k00_numpar, 
                                        record_alias.k00_receit, 
                                        record_alias.k00_hist, 
                                        dtoper, 
                                        dtvenc, 
                                        v_subdir, 
                                        datavenc);

            if lRaise is true then
                perform fc_debug('<calcula> valor_receita: '||valor_receita||' - nComposCorrecao: '||nComposCorrecao,lRaise, false, false);
            end if;

            valor_receita = valor_receita + nComposCorrecao;

            if lRaise is true then
                perform fc_debug('<calcula>    valor_receita: '||valor_receita||' - nComposCorrecao: '||nComposCorrecao, lRaise, false, false);
            end if;

        end if;

        if lRaise is true then
            perform fc_debug('<calcula>    nComposCorrecao: '||nComposCorrecao||' - nCorreComposJuros: '||nCorreComposJuros||' - nCorreComposMulta: '||nCorreComposMulta||' - nComposJuros: '||nComposJuros||' - nComposMulta: '||nComposMulta, lRaise, false, false);
            perform fc_debug('<calcula> '||receita||' - '||dtoper||' - '||valor_receita||' - '||venc_unic||' - '||v_subdir||' - '||datavenc, lRaise, false, false);
        end if;

        correcao := fc_corre(receita,dtoper,valor_receita,venc_unic,v_subdir,datavenc);

        if lRaise is true then
            perform fc_debug('<calcula>   correcao (2): '||correcao, lRaise, false, false);
        end if;

        correcao := correcao + nCorreComposJuros + nCorreComposMulta;

        if lRaise is true then
            perform fc_debug('<calcula> correcao (3): '||correcao, lRaise, false, false);
        end if;

        if correcao = 0 then
        
            if lRaise is true then
            perform fc_debug('<calcula> 3 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
            end if;              
            return '9          0.0         0.00         0.00         0.00         0.00                   0.000000';
            
        end if;

        correcao := round( correcao - valor_receita , 2 );

        else
        correcao := 0;
        end if;

        vlrcorrecao := vlrcorrecao + correcao + valor_receita;
        
        if lRaise is true then
        perform fc_debug('<calcula>    vlrcorrecao: '||vlrcorrecao||' - correcao: '||correcao||' - valor_receita: '||valor_receita, lRaise, false, false);
        end if;

        if ( valor_receita + correcao ) <> 0 then

        select k02_integr
            into v_integr
            from tabrec
                inner join tabrecjm on tabrec.k02_codjm = tabrecjm.k02_codjm
        where k02_codigo = receita;

        if lRaise is true then
            perform fc_debug('<calcula> v_integr: '||v_integr||' - unica: '||unica, lRaise, false, false);
        end if;

        if v_integr is null then
            v_integr = false;
        end if;
       
        if unica and unicaJuroMulta = false then
       		v_integr = true;
        end if;

        if lRaise is true then
            perform fc_debug('<calcula> unica: '||unica||' - v_integr: '||v_integr, lRaise, false, false);
        end if;

        if unica is false or (unica is true and v_integr is not true) then
        
            if lRaise is true then
            perform fc_debug('<calcula>  receita: '||receita||' - datavenc: '||datavenc||' - dtemite: '||dtemite||' - dtoper: '||dtoper||' - v_subdir: '||v_subdir, lRaise, false, false);
            perform fc_debug('<calcula>  fc_juros('||receita||','||datavenc||','||dtemite||','||dtoper||',false,'||v_subdir||')', lRaise, false, false);
            end if;
            
            juro := round(( correcao+valor_receita) * fc_juros(receita,datavenc,dtemite,dtoper,false,v_subdir)::numeric(20,10) ,2);
            juro  = round(juro + nComposJuros,2);
            
            -- calcula multa
            nPercMulta  = fc_multa(receita,datavenc,dtemite,dtoper,v_subdir)::numeric(20,10);
            multa      := round( round( correcao+valor_receita ,2) * fc_multa(receita,datavenc,dtemite,dtoper,v_subdir)::numeric(20,10) ,2);
            multa       = round(multa + nComposMulta,2);

            if lRaise is true then
            perform fc_debug('<calcula> k03_recjur: '||v_recjur||' - k03_recmul: '||v_recmul, lRaise, false, false);
            perform fc_debug('<calcula> --- aqui: multa: '||multa||' - correcao: '||correcao||' - valor_receita: '||valor_receita||' - nComposMulta: '||nComposMulta||' - percmulta: '||nPercMulta, lRaise, false, false);
            end if;
            
            if v_recjur = 0 or v_recmul = 0 or v_recjur = v_recmul then
            
            if juro+multa <> 0 then
                vlrjuros := vlrjuros + juro;
                vlrmulta := vlrmulta + multa;
            end if;
            
            else
            
            if juro <> 0 then
                vlrjuros := vlrjuros + juro;
            end if;
            
            if multa <> 0 then
                vlrmulta := vlrmulta + multa;
            end if;
            
            end if;
            
        end if;
        --calcular desconto
        -- somente para sapiranga
        
        if correcao+valor_receita <> 0 then
            
            if lRaise is true then
            perform fc_debug('<calcula> desconto '||receita||' - '||venc_unic||'-'||valor_receita||'-'||juro||'-'||unica||'-'||datavenc||'-'||v_subdir, lRaise, false, false);
            perform fc_debug('<calcula> desconto - rec: '||receita||' - venc_unic: '||venc_unic||' - correc: '||correcao||' - vlrrec: '||valor_receita||' - juro: '||juro||' - unica: '||unica||' - dtvenc: '||datavenc||' - subdir: '||v_subdir, lRaise, false, false);
            end if;
            
            desconto := fc_desconto(receita,venc_unic,correcao+valor_receita,juro+multa,unica,datavenc,v_subdir,record_alias.k00_numpre)::numeric(20,10);
            
            if lRaise is true then
            perform fc_debug('<calcula>    desconto calculado: '||desconto, lRaise, false, false);
            end if;
            
            if desconto <> 0 then
            vlrdesconto := vlrdesconto + desconto;
            end if; 
            
            if lRaise is true then
            perform fc_debug('<calcula> desconto '||desconto, lRaise, false, false);
            end if;
            
        end if; 
        
        select k02_corr 
            into qinflator
            from tabrec
                inner join tabrecjm on tabrec.k02_codjm = tabrecjm.k02_codjm
        where k02_codigo = receita;
        if not qinflator is null then
        
            vlrinflator := correcao + valor_receita + juro + multa - desconto;
            if lRaise is true then
            perform fc_debug('<calcula> correcao: '||correcao||' - valor_receita: '||valor_receita||' - juro: '||juro||' - multa: '||multa||' - desconto: '||desconto, lRaise, false, false);
            perform fc_debug('<calcula> vlrinflator: '||vlrinflator||' - qinflator: '||qinflator||' - venc_unic: '||venc_unic, lRaise, false, false);
            end if;
            
            v_calculoinfla := fc_vlinf(qinflator,venc_unic);
            if v_calculoinfla = 0 then
            v_calculoinfla = 0;
            else
            select round(vlrinflator/v_calculoinfla,6)
            into vlrinflator;
            end if;
            
            vlrtotinf  := vlrtotinf + vlrinflator;
            qrinflator := qinflator;
            
        end if;
        
        end if;
        
    end if;
    
    else
    
    if record_alias.k00_tipo = 400 then
    
        vlrjuros := vlrjuros + record_alias.k00_valor;
        
    else
    
        if record_alias.k00_tipo = 401 then
        vlrmulta := vlrmulta + record_alias.k00_valor;
        else
        vlrcorrecao := vlrcorrecao + record_alias.k00_valor ;
        end if;
        
    end if ;
    
    processa := true;
    
    end if;
    
end loop;

end if;

end loop;

if processa = true then

if vlrcorrecao+vlrjuros+vlrmulta = 0 then

if issqnvariavel = true then

    if lRaise is true then
    perform fc_debug('<calcula> 4 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;      
    return '8          0.0         0.00         0.00         0.00         0.00                   0.000000';
    
else

    if lRaise is true then
    perform fc_debug('<calcula> 5 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;      
    return '5          0.0         0.00         0.00         0.00         0.00                   0.000000';
    
end if;

else

if vlrtotinf is null then
    vlrtotinf := 0;
end if;

if qrinflator is null then
    qrinflator := '   ';
end if;

if subdir >= 10000 then

    -- cria consulta preparada para os numpres que serao inseridos na debitos
    begin

    prepare select_debitos(integer, integer, integer) as 
        select distinct
            deb.*,
            arreinstit.k00_instit,
            arretipo.k03_tipo
        from (
            select arrecad.k00_numpre,
                    arrecad.k00_numpar,
                    arrecad.k00_receit,
                    arrecad.k00_dtvenc,
                    arrecad.k00_dtoper,
                    (select
                        rinumcgm
                    from
                        fc_busca_envolvidos('t',
                        (case
                            when db_config.db21_regracgmiptu = 0
                    then 3
                            else db_config.db21_regracgmiptu
                        end),
                        'M',
                        arrematric.k00_matric)
                    limit 1) as k00_numcgm,
                    arrematric.k00_matric,
                    arrematric.k00_perc as k00_percmatric,
                    cast(0 as integer)  as k00_inscr,
                    cast(0 as float8)   as k00_percinscr,
                    arrecad.k00_tipo
                from arrecad
                    inner join arrematric on arrematric.k00_numpre = arrecad.k00_numpre
                    inner join iptubase   on iptubase.j01_matric   = arrematric.k00_matric
                    inner join arreinstit on arrecad.k00_numpre = arreinstit.k00_numpre
                    inner join db_config on db_config.codigo = arreinstit.k00_instit
            where arrecad.k00_numpre = $1
                and arrecad.k00_numpar = $2
                and arrecad.k00_receit = $3
            union all
            select arrecad.k00_numpre,
                    arrecad.k00_numpar,
                    arrecad.k00_receit,
                    arrecad.k00_dtvenc,
                    arrecad.k00_dtoper,
                    arrecad.k00_numcgm,
                    cast(0 as integer)  as k00_matric,
                    cast(0 as float8)   as k00_percmatric,
                    arreinscr.k00_inscr as k00_inscr,
                    arreinscr.k00_perc  as k00_percinscr,
                    arrecad.k00_tipo
                from arrecad
                    inner join arreinscr  on arreinscr.k00_numpre  = arrecad.k00_numpre
            where arrecad.k00_numpre = $1
                and arrecad.k00_numpar = $2
                and arrecad.k00_receit = $3
            union all
            select arrecad.k00_numpre,
                    arrecad.k00_numpar,
                    arrecad.k00_receit,
                    arrecad.k00_dtvenc,
                    arrecad.k00_dtoper,
                    arrenumcgm.k00_numcgm,
                    cast(0 as integer)  as k00_matric,
                    cast(0 as float8)   as k00_percmatric,
                    cast(0 as integer)  as k00_inscr,
                    cast(0 as float8)   as k00_percinscr,
                    arrecad.k00_tipo
                from arrecad
                    inner join arrenumcgm on arrenumcgm.k00_numpre = arrecad.k00_numpre
                    left  join arreinscr  on arreinscr.k00_numpre  = arrecad.k00_numpre
                    left  join arrematric on arrematric.k00_numpre = arrecad.k00_numpre
            where arrecad.k00_numpre = $1
                and arrecad.k00_numpar = $2
                and arrecad.k00_receit = $3
                and arreinscr.k00_numpre is null
                and arrematric.k00_numpre is null
            ) as deb
            inner join arreinstit on arreinstit.k00_numpre = deb.k00_numpre
            inner join arretipo   on arretipo.k00_tipo     = deb.k00_tipo;

    prepare select_exerc_tipo_5(integer, integer) as
        select v01_exerc
        from divida
        where v01_numpre = $1
        and v01_numpar = $2
        limit 1;

    prepare select_exerc_tipo_18(integer, integer) as
        select case when divida.v01_exerc is null then extract (year from arrecad.k00_dtoper) else divida.v01_exerc end
        from arrecad
            inner join inicialnumpre on inicialnumpre.v59_numpre = arrecad.k00_numpre
            inner join inicialcert   on inicialcert.v51_inicial  = inicialnumpre.v59_inicial
            inner join certid        on certid.v13_certid        = inicialcert.v51_certidao
            inner join certdiv       on certdiv.v14_certid       = certid.v13_certid
            inner join divida        on certdiv.v14_coddiv       = divida.v01_coddiv
                                    and divida.v01_numpre        = arrecad.k00_numpre
                                    and divida.v01_numpar        = arrecad.k00_numpar
        where arrecad.k00_numpre = $1
        and arrecad.k00_numpar = $2
        limit 1;

    prepare select_dtoper_termo(integer) as
        select v07_dtlanc
        from termo
        where v07_numpre = $1
        limit 1;

    exception when duplicate_prepared_statement then
    end;

    for v_debitos in execute 'execute select_debitos('||numpre||', '||numpar||', '||receita||')'
    loop
    v_exerc := null; 
    
    if v_debitos.k03_tipo = 5 then
    
        execute 'execute select_exerc_tipo_5('||numpre||', '||numpar||')'
        into v_exerc;
        
    elsif v_debitos.k03_tipo = 18 then
    
        execute 'execute select_exerc_tipo_18('||numpre||', '||numpar||')'
        into v_exerc;
        
    end if;

    if v_debitos.k03_tipo in (6, 21, 28, 30) then
    
        execute 'execute select_dtoper_termo('||numpre||')'
        into v_dtoper;
        
        if v_dtoper is null then
        v_dtoper := v_debitos.k00_dtoper;
        end if;
        
    else
        v_dtoper := v_debitos.k00_dtoper;
    end if;

    if v_exerc is null then
        v_exerc := to_char(v_debitos.k00_dtoper, 'yyyy');
    end if;
    
    if v_debitos.k00_percmatric > 0 and v_debitos.k00_percmatric::float8 <> 100::float8 then
        nperccalc := v_debitos.k00_percmatric::float8;
    elsif v_debitos.k00_percinscr > 0 and v_debitos.k00_percinscr::float8 <> 100::float8 then
        nperccalc := v_debitos.k00_percinscr::float8;
    else
        nperccalc := 100::float8;
    end if;

    -- acumula valor historico para comparar com o arrecad
    nValorHistorico := round( (valor_hist  * nperccalc/100)::float8 ,2);
    nValorCorrecao  := round( (vlrcorrecao * nperccalc/100)::float8 ,2);
    nValorJuros     := round( (vlrjuros    * nperccalc/100)::float8 ,2);
    nValorMulta     := round( (vlrmulta    * nperccalc/100)::float8 ,2);
    nValorDesconto  := round( (vlrdesconto * nperccalc/100)::float8 ,2);

    nAcumHistorico  := nAcumHistorico + nValorHistorico;
    nDiferencaHis   := round(valor_hist - nAcumHistorico, 2);

    nAcumCorrecao   := nAcumCorrecao + nValorCorrecao;
    nDiferencaCor   := round(vlrcorrecao - nAcumCorrecao, 2);

    nAcumJuros      := nAcumJuros + nValorJuros;
    nDiferencaJur   := round(vlrjuros - nAcumJuros, 2);

    nAcumMulta      := nAcumMulta + nValorMulta;
    nDiferencaMul   := round(vlrmulta - nAcumMulta, 2);

    nAcumDesconto   := nAcumDesconto + nValorDesconto;
    nDiferencaDes   := round(vlrdesconto - nAcumDesconto, 2);

    -- efetuar acerto dos centavos do valor historico
    if abs(nDiferencaHis) = cast(0.01 as float8) then
        nValorHistorico := nValorHistorico + nDiferencaHis;
    end if;

    -- efetuar acerto dos centavos do valor corrigido
    if abs(nDiferencaCor) = cast(0.01 as float8) then
        nValorCorrecao := nValorCorrecao + nDiferencaCor;
    end if;

    -- efetuar acerto dos centavos do valor juros
    if abs(nDiferencaJur) = cast(0.01 as float8) then
        nValorJuros := nValorJuros + nDiferencaJur;
    end if;

    -- efetuar acerto dos centavos do valor multa
    if abs(nDiferencaMul) = cast(0.01 as float8) then
        nValorMulta := nValorMulta + nDiferencaMul;
    end if;

    -- efetuar acerto dos centavos do valor desconto
    if abs(nDiferencaDes) = cast(0.01 as float8) then
        nValorDesconto := nValorDesconto + nDiferencaDes;
    end if;

    select k00_hist 
        into historic
        from arrecad 
    where k00_numpre = v_debitos.k00_numpre 
        and k00_numpar = v_debitos.k00_numpar 
        and k00_receit = v_debitos.k00_receit 
    limit 1;

    execute '
        insert into '||sNomedebitos||'
                        (k22_data ,
                            k22_numpre,
                            k22_numpar,
                            k22_receit,
                            k22_dtvenc,
                            k22_dtoper,
                            k22_hist  ,
                            k22_numcgm,
                            k22_matric,
                            k22_inscr ,
                            k22_tipo  ,
                            k22_vlrhis,
                            k22_vlrcor,
                            k22_juros ,
                            k22_multa ,
                            k22_desconto,
                            k22_exerc,
                            k22_instit )
                values ( '||quote_literal(dtemite)||',
                            '||coalesce(v_debitos.k00_numpre, 0)||',
                            '||coalesce(v_debitos.k00_numpar, 0)||',
                            '||coalesce(v_debitos.k00_receit, 0)||',
                            '||quote_literal(v_debitos.k00_dtvenc)||',
                            '||quote_literal(v_dtoper)||',
                            '||coalesce(historic, 0)||',
                            '||coalesce(v_debitos.k00_numcgm, 0)||',
                            '||coalesce(v_debitos.k00_matric, 0)||',
                            '||coalesce(v_debitos.k00_inscr, 0)||',
                            '||coalesce(v_debitos.k00_tipo, 0)||',
                            '||coalesce(nValorHistorico, 0.0)||',
                            '||coalesce(nValorCorrecao, 0.0)||',
                            '||coalesce(nValorJuros, 0.0)||',
                            '||coalesce(nValorMulta, 0.0)||',
                            '||coalesce(nValorDesconto, 0.0)||',
                            '||coalesce(v_exerc, 0)||',
                            '||coalesce(v_debitos.k00_instit, 0)||')' ; 
    
    end loop;
    
    if lRaise is true then
    perform fc_debug('<calcula> 6 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;        
    return '0          0.0         0.00         0.00         0.00         0.00                   0.000000';
    
else

    if lRaise is true then
    perform fc_debug('<calcula> 7 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;      
    return trim(numero_erro) || to_char(valor_hist,'999999990.00')|| to_char(vlrcorrecao,'999999990.00') || to_char(vlrjuros,'999999990.00') || to_char(vlrmulta,'999999990.00') || to_char(vlrdesconto,'999999990.00') || to_char(venc_unic,'yyyy-mm-dd') || to_char(vlrtotinf,'999999990.000000')||qrinflator;
    
end if;
end if;

else

-- criar select para pegar valor do estorno ver possibilidade
-- de listar pela data de pagamento
if numpar = 0 then

select sum(k00_valor)
    into vlrcorrecao
    from arrepaga
where k00_numpre = numpre ;

else

select sum(k00_valor)
    into vlrcorrecao
    from arrepaga
where k00_numpre = numpre 
    and k00_numpar = numpar;

end if;

if vlrcorrecao is null then

select round(sum(k00_valor),2) as sum
    into vlrcorrecao
    from db_reciboweb
inner join arrepaga on db_reciboweb.k99_numpre   = arrepaga.k00_numpre 
                    and db_reciboweb.k99_numpar   = arrepaga.k00_numpar 
where db_reciboweb.k99_numpre_n = numpre;
if vlrcorrecao is null then

    if lRaise is true then
    perform fc_debug('<calcula> 8 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;      
    return '9          0.0         0.00         0.00         0.00         0.00                   0.000000';
    
else
    
    select round(sum(k00_valor),2) as sum
    into vlrestorno
    from recibopaga
    where k00_numnov = numpre;
    if vlrestorno != vlrcorrecao then
    
    if lRaise is true then
        perform fc_debug('<calcula> 9 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;        
    return '2' || to_char(vlrcorrecao,'999999990.00');
    
    else
    
    if lRaise is true then
        perform fc_debug('<calcula> 10 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;        
    return '4' || to_char(vlrcorrecao,'999999990.00');
    
    end if;
    
end if;

else

if lRaise is true then
    perform fc_debug('<calcula> 11 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
end if;    
return '4' || to_char(vlrcorrecao,'999999990.00');

end if;

end if;

if lRaise is true then
perform fc_debug('<calcula> 12 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
end if;

return '9          0.0         0.00         0.00         0.00         0.00                   0.000000';

end;
$$ language 'plpgsql';

SQL;
        $this->executeQuery($sql);
    }

    public function down()
    {
        $sql = <<<SQL
--drop function fc_calcula(integer,integer,integer,date,date,integer);
create or replace function fc_calcula(integer,integer,integer,date,date,integer) returns varchar as 
$$
begin
  return fc_calcula($1, $2, $3, $4, $5, $6, null);
end
$$
language 'plpgsql';

create or replace function fc_calcula(integer,integer,integer,date,date,integer,varchar) returns varchar as 
$$
declare
numpre          alias for $1;
numpar          alias for $2;
creceita        alias for $3;
dtemite         alias for $4;
dtvenc          alias for $5;
subdir          alias for $6;
nomedebitos     alias for $7; -- Nome alternativo para a tabela debitos (default = debitos)

iFormaCorrecao integer default 2;
iInstit        integer;

v_subdir        integer;
numero_erro     char(1) default '1';    
v_debitos       record;
record_numpre   record;
record_alias    record;
record_grava    record;
record_numpref  record;
v_composicao    record;

venc_unic       date default current_date;
venc_unic1      date;
venc_unic2      date;
num_par         integer;

valor_receita   float8 default 0;
correcao        float8 default 0;
juro            float8 default 0;
multa           float8 default 0;
desconto        float8 default 0;
valor_hist      float8 default 0;
receita         integer;

v_recjur        integer default 0;
v_recmul        integer default 0;

v_integr        boolean;

k00_operac      integer;
k06_operac      integer;
k09_operac      integer;
qualoperac      boolean default false;
dtoper          date;
datavenc        date;
unica           boolean default false;

sqlrecibo       char(255);

vlrestorno      float8 default 0;
vlrcorrecao     float8 default 0;
vlrjuros        float8 default 0;
vlrmulta        float8 default 0;
vlrdesconto     float8 default 0;
vlrinflator     float8 default 0;
nperccalc       float8 default 100;
qrinflator      varchar(5);
vlrtotinf       float8 default 0;
qinflator       varchar(5);
calcula         boolean;
processa        boolean default false;
issqnvariavel   boolean default false;
v_exerc           integer;
v_dtoper        date;
v_calculoinfla  float8;

totperc             float8  default 0;
nao_tem_recibo  boolean default false;

lRaise         boolean default false;

nValorHistorico float8 default 0;
nValorCorrecao  float8 default 0;
nValorJuros     float8 default 0;
nValorMulta     float8 default 0;
nValorDesconto  float8 default 0;

nAcumHistorico  float8 default 0;
nAcumCorrecao   float8 default 0;
nAcumJuros      float8 default 0;
nAcumMulta      float8 default 0;
nAcumDesconto   float8 default 0;

-- diferencas das proporcionalidades arrematric e arreinscr
nDiferencaHis   float8 default 0;
nDiferencaCor   float8 default 0;
nDiferencaJur   float8 default 0;
nDiferencaMul   float8 default 0;
nDiferencaDes   float8 default 0;

nComposCorrecao   numeric(15,2) default 0;
nComposJuros      numeric(15,2) default 0;
nComposMulta      numeric(15,2) default 0;

nCorreComposJuros numeric(15,2) default 0;
nCorreComposMulta numeric(15,2) default 0;

nPercMulta float8 default 0;

historic        integer default 0;

sNomeDebitos    varchar default 'debitos';

begin

lRaise := ( case when fc_getsession('DB_debugon') is null then false else true end );
if lRaise is true then
if fc_getsession('db_debug') <> '' then  
perform fc_debug('<calcula> Processando calculo de correcao, juros e multa...', lRaise, false, false);
else 
perform fc_debug('<calcula> Processando calculo de correcao, juros e multa...', lRaise, true, false);
end if;
end if;

select k00_instit
into iInstit
from arreinstit
where k00_numpre = numpre;

if iInstit is null then
select codigo
into iInstit
from db_config
where prefeitura is true
limit 1;
end if;

if nomedebitos is not null or trim(nomedebitos) <> '' then
sNomeDebitos := nomedebitos;
end if;

v_subdir := subdir;
if v_subdir >= 10000 then
v_subdir := v_subdir - 10000;
end if;

select k03_separajurmulparc
into iFormaCorrecao
from numpref
where k03_instit = iInstit
and k03_anousu = v_subdir;
if lRaise is true then
perform fc_debug('<calcula> numpre: '||numpre||' - iFormaCorrecao: '||iFormaCorrecao, lRaise, false,false);
end if;

select k03_recjur, 
    k03_recmul 
into v_recjur, 
    v_recmul 
from numpref 
order by k03_anousu desc 
limit 1;

for record_numpre in select distinct * 
                    from ( select distinct 
                                k00_numpre,
                                k00_numpar  
                            from arrecad
                            where k00_numpre = numpre
                            
                            union all
                            
                            select distinct 
                                recibo.k00_numpre,
                                recibo.k00_numpar
                            from recibo
                                left outer join arrepaga on recibo.k00_numpre = arrepaga.k00_numpre
                            where recibo.k00_numpre = numpre 
                            and arrepaga.k00_numpre is null
                            
                            union all
                            
                            select distinct 
                                k99_numpre_n,
                                1 as k00_numpar
                            from db_reciboweb
                            where k99_numpre_n = numpre ) as x
                    order by k00_numpre,k00_numpar loop

if numpar != 0 then

if lRaise is true then
    perform fc_debug('<calcula> numpar diferente de 0',lRaise,false,false);
end if;
if record_numpre.k00_numpar != numpar then
    num_par := 0;
else
    num_par := numpar;
end if;

else

if lRaise is true then
    perform fc_debug('<calcula> numpar igual a 0',lRaise,false,false);
end if;
num_par := record_numpre.k00_numpar;
unica := true;

end if;

if lRaise is true then
perform fc_debug('<calcula> numpar: '||num_par||' - unica: '||unica, lRaise,false,false);
end if;

if num_par != 0 then

valor_receita := 0;

for record_alias in select * 
                        from (select *,
                                    'arrecad' as db_arquivo 
                                from (select k00_hist,
                                            k00_receit,
                                            k00_tipo,
                                            k00_dtoper,
                                            fc_calculavenci(k00_numpre,k00_numpar,k00_dtvenc,dtemite) as k00_dtvenc,
                                            k00_numpre,
                                            k00_numpar,
                                            k00_valor as k00_valor 
                                        from arrecad
                                    where k00_numpre = numpre 
                                        and k00_numpar = num_par ) as xx
                                        
                            union all
                            
                            select *,
                                    'recibo' as db_arquivo 
                                from (select k00_hist, 
                                            k00_receit,
                                            k00_tipo,
                                            k00_dtoper,
                                            k00_dtvenc,
                                            k00_numpre as k00_numpre,
                                            k00_numpar,
                                            k00_valor as k00_valor 
                                        from recibo
                                    where k00_numpre = numpre 
                                        and k00_numpar = num_par ) as xy
                                        
                            union all
                            
                            select *,
                                    'recuni' as db_arquivo 
                                from (select k00_hist, 
                                            k00_receit,
                                            k00_hist as k00_tipo,
                                            k00_dtoper,
                                            k00_dtvenc,
                                            k00_numnov as k00_numpre,
                                            0 as k00_numpar,
                                            k00_valor as k00_valor 
                                        from recibopaga
                                    where k00_numnov = numpre 
                                        and k00_conta = 0 ) as xz
                            ) as x
                    order by k00_numpre,k00_numpar,k00_receit,k00_hist loop

    if lRaise is true then
    perform fc_debug('<calcula> arquivo: '||record_alias.db_arquivo||' - valor: '||record_alias.k00_valor, lRaise, false, false);
    end if;
    
    if record_alias.db_arquivo = 'arrecad' then
    
    nao_tem_recibo = true;
    
    if record_alias.k00_valor = 0 then
    
        if unica = true then
        
        if lRaise is true then
            perform fc_debug('<calcula> 1 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
        end if;            
        return '6          0.0         0.00         0.00         0.00         0.00                   0.000000';
        
        else
        
        -- variavel
        if record_alias.k00_tipo != 3 then
        
            if lRaise is true then
            perform fc_debug('<calcula> 2 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
            end if;              
            return '7          0.0         0.00         0.00         0.00         0.00                   0.000000';
            
        end if;
        
        end if;
        
    end if;
    
    calcula = false;
    if ( creceita <> 0 and creceita = record_alias.k00_receit ) or creceita = 0 then 
        calcula = true;
    end if;

    if calcula = true then
        
        if lRaise is true then
        perform fc_debug('<calcula>    calcula: '||calcula,lRaise, false,false);
        end if;

        venc_unic     := dtvenc;
        receita       := record_alias.k00_receit;
        dtoper        := record_alias.k00_dtoper;
        datavenc      := record_alias.k00_dtvenc;
        valor_receita := record_alias.k00_valor;
        
        if valor_receita = 0 then
        
        select case 
                when q05_valor != 0 
                    then q05_valor 
                else q05_vlrinf 
                end  
            into valor_receita
            from issvar 
        where q05_numpre = record_alias.k00_numpre 
            and q05_numpar = record_alias.k00_numpar;
        if valor_receita is null then
            valor_receita := 0;
        else
            issqnvariavel := true;
        end if;
        end if;
        
        valor_hist := valor_hist + round(valor_receita, 2);
        qualoperac := false;
        
        -- calcula correcao 
        processa := true;

        if lRaise is true then
        perform fc_debug('<calcula>    numpre : '||record_alias.k00_numpre||' numpar : '||record_alias.k00_numpar||' receita : '||record_alias.k00_receit||' valor_receita : '||valor_receita||' k00_valor : '||record_alias.k00_valor||' iFormaCorrecao : '||iFormaCorrecao, lRaise, false,false);
        end if;


        if valor_receita <> 0 then 

        if iFormaCorrecao = 1 then

            select rnCorreComposJuros, 
                    rnCorreComposMulta, 
                    rnComposCorrecao, 
                    rnComposJuros, 
                    rnComposMulta
                into nCorreComposJuros, 
                    nCorreComposMulta, 
                    nComposCorrecao, 
                    nComposJuros, 
                    nComposMulta
                from fc_retornacomposicao(record_alias.k00_numpre, 
                                        record_alias.k00_numpar, 
                                        record_alias.k00_receit, 
                                        record_alias.k00_hist, 
                                        dtoper, 
                                        dtvenc, 
                                        v_subdir, 
                                        datavenc);

            if lRaise is true then
                perform fc_debug('<calcula> valor_receita: '||valor_receita||' - nComposCorrecao: '||nComposCorrecao,lRaise, false, false);
            end if;

            valor_receita = valor_receita + nComposCorrecao;

            if lRaise is true then
                perform fc_debug('<calcula>    valor_receita: '||valor_receita||' - nComposCorrecao: '||nComposCorrecao, lRaise, false, false);
            end if;

        end if;

        if lRaise is true then
            perform fc_debug('<calcula>    nComposCorrecao: '||nComposCorrecao||' - nCorreComposJuros: '||nCorreComposJuros||' - nCorreComposMulta: '||nCorreComposMulta||' - nComposJuros: '||nComposJuros||' - nComposMulta: '||nComposMulta, lRaise, false, false);
            perform fc_debug('<calcula> '||receita||' - '||dtoper||' - '||valor_receita||' - '||venc_unic||' - '||v_subdir||' - '||datavenc, lRaise, false, false);
        end if;

        correcao := fc_corre(receita,dtoper,valor_receita,venc_unic,v_subdir,datavenc);

        if lRaise is true then
            perform fc_debug('<calcula>   correcao (2): '||correcao, lRaise, false, false);
        end if;

        correcao := correcao + nCorreComposJuros + nCorreComposMulta;

        if lRaise is true then
            perform fc_debug('<calcula> correcao (3): '||correcao, lRaise, false, false);
        end if;

        if correcao = 0 then
        
            if lRaise is true then
            perform fc_debug('<calcula> 3 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
            end if;              
            return '9          0.0         0.00         0.00         0.00         0.00                   0.000000';
            
        end if;

        correcao := round( correcao - valor_receita , 2 );

        else
        correcao := 0;
        end if;

        vlrcorrecao := vlrcorrecao + correcao + valor_receita;
        
        if lRaise is true then
        perform fc_debug('<calcula>    vlrcorrecao: '||vlrcorrecao||' - correcao: '||correcao||' - valor_receita: '||valor_receita, lRaise, false, false);
        end if;

        if ( valor_receita + correcao ) <> 0 then

        select k02_integr
            into v_integr
            from tabrec
                inner join tabrecjm on tabrec.k02_codjm = tabrecjm.k02_codjm
        where k02_codigo = receita;

        if lRaise is true then
            perform fc_debug('<calcula> v_integr: '||v_integr||' - unica: '||unica, lRaise, false, false);
        end if;

        if v_integr is null then
            v_integr = false;
        end if;

        if lRaise is true then
            perform fc_debug('<calcula> unica: '||unica||' - v_integr: '||v_integr, lRaise, false, false);
        end if;

        if unica is false or (unica is true and v_integr is not true) then
        
            if lRaise is true then
            perform fc_debug('<calcula>  receita: '||receita||' - datavenc: '||datavenc||' - dtemite: '||dtemite||' - dtoper: '||dtoper||' - v_subdir: '||v_subdir, lRaise, false, false);
            perform fc_debug('<calcula>  fc_juros('||receita||','||datavenc||','||dtemite||','||dtoper||',false,'||v_subdir||')', lRaise, false, false);
            end if;
            
            juro := round(( correcao+valor_receita) * fc_juros(receita,datavenc,dtemite,dtoper,false,v_subdir)::numeric(20,10) ,2);
            juro  = round(juro + nComposJuros,2);
            
            -- calcula multa
            nPercMulta  = fc_multa(receita,datavenc,dtemite,dtoper,v_subdir)::numeric(20,10);
            multa      := round( round( correcao+valor_receita ,2) * fc_multa(receita,datavenc,dtemite,dtoper,v_subdir)::numeric(20,10) ,2);
            multa       = round(multa + nComposMulta,2);

            if lRaise is true then
            perform fc_debug('<calcula> k03_recjur: '||v_recjur||' - k03_recmul: '||v_recmul, lRaise, false, false);
            perform fc_debug('<calcula> --- aqui: multa: '||multa||' - correcao: '||correcao||' - valor_receita: '||valor_receita||' - nComposMulta: '||nComposMulta||' - percmulta: '||nPercMulta, lRaise, false, false);
            end if;
            
            if v_recjur = 0 or v_recmul = 0 or v_recjur = v_recmul then
            
            if juro+multa <> 0 then
                vlrjuros := vlrjuros + juro;
                vlrmulta := vlrmulta + multa;
            end if;
            
            else
            
            if juro <> 0 then
                vlrjuros := vlrjuros + juro;
            end if;
            
            if multa <> 0 then
                vlrmulta := vlrmulta + multa;
            end if;
            
            end if;
            
        end if;
        --calcular desconto
        -- somente para sapiranga
        
        if correcao+valor_receita <> 0 then
            
            if lRaise is true then
            perform fc_debug('<calcula> desconto '||receita||' - '||venc_unic||'-'||valor_receita||'-'||juro||'-'||unica||'-'||datavenc||'-'||v_subdir, lRaise, false, false);
            perform fc_debug('<calcula> desconto - rec: '||receita||' - venc_unic: '||venc_unic||' - correc: '||correcao||' - vlrrec: '||valor_receita||' - juro: '||juro||' - unica: '||unica||' - dtvenc: '||datavenc||' - subdir: '||v_subdir, lRaise, false, false);
            end if;
            
            desconto := fc_desconto(receita,venc_unic,correcao+valor_receita,juro+multa,unica,datavenc,v_subdir,record_alias.k00_numpre)::numeric(20,10);
            
            if lRaise is true then
            perform fc_debug('<calcula>    desconto calculado: '||desconto, lRaise, false, false);
            end if;
            
            if desconto <> 0 then
            vlrdesconto := vlrdesconto + desconto;
            end if; 
            
            if lRaise is true then
            perform fc_debug('<calcula> desconto '||desconto, lRaise, false, false);
            end if;
            
        end if; 
        
        select k02_corr 
            into qinflator
            from tabrec
                inner join tabrecjm on tabrec.k02_codjm = tabrecjm.k02_codjm
        where k02_codigo = receita;
        if not qinflator is null then
        
            vlrinflator := correcao + valor_receita + juro + multa - desconto;
            if lRaise is true then
            perform fc_debug('<calcula> correcao: '||correcao||' - valor_receita: '||valor_receita||' - juro: '||juro||' - multa: '||multa||' - desconto: '||desconto, lRaise, false, false);
            perform fc_debug('<calcula> vlrinflator: '||vlrinflator||' - qinflator: '||qinflator||' - venc_unic: '||venc_unic, lRaise, false, false);
            end if;
            
            v_calculoinfla := fc_vlinf(qinflator,venc_unic);
            if v_calculoinfla = 0 then
            v_calculoinfla = 0;
            else
            select round(vlrinflator/v_calculoinfla,6)
            into vlrinflator;
            end if;
            
            vlrtotinf  := vlrtotinf + vlrinflator;
            qrinflator := qinflator;
            
        end if;
        
        end if;
        
    end if;
    
    else
    
    if record_alias.k00_tipo = 400 then
    
        vlrjuros := vlrjuros + record_alias.k00_valor;
        
    else
    
        if record_alias.k00_tipo = 401 then
        vlrmulta := vlrmulta + record_alias.k00_valor;
        else
        vlrcorrecao := vlrcorrecao + record_alias.k00_valor ;
        end if;
        
    end if ;
    
    processa := true;
    
    end if;
    
end loop;

end if;

end loop;

if processa = true then

if vlrcorrecao+vlrjuros+vlrmulta = 0 then

if issqnvariavel = true then

    if lRaise is true then
    perform fc_debug('<calcula> 4 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;      
    return '8          0.0         0.00         0.00         0.00         0.00                   0.000000';
    
else

    if lRaise is true then
    perform fc_debug('<calcula> 5 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;      
    return '5          0.0         0.00         0.00         0.00         0.00                   0.000000';
    
end if;

else

if vlrtotinf is null then
    vlrtotinf := 0;
end if;

if qrinflator is null then
    qrinflator := '   ';
end if;

if subdir >= 10000 then

    -- cria consulta preparada para os numpres que serao inseridos na debitos
    begin

    prepare select_debitos(integer, integer, integer) as 
        select distinct
            deb.*,
            arreinstit.k00_instit,
            arretipo.k03_tipo
        from (
            select arrecad.k00_numpre,
                    arrecad.k00_numpar,
                    arrecad.k00_receit,
                    arrecad.k00_dtvenc,
                    arrecad.k00_dtoper,
                    (select
                        rinumcgm
                    from
                        fc_busca_envolvidos('t',
                        (case
                            when db_config.db21_regracgmiptu = 0
                    then 3
                            else db_config.db21_regracgmiptu
                        end),
                        'M',
                        arrematric.k00_matric)
                    limit 1) as k00_numcgm,
                    arrematric.k00_matric,
                    arrematric.k00_perc as k00_percmatric,
                    cast(0 as integer)  as k00_inscr,
                    cast(0 as float8)   as k00_percinscr,
                    arrecad.k00_tipo
                from arrecad
                    inner join arrematric on arrematric.k00_numpre = arrecad.k00_numpre
                    inner join iptubase   on iptubase.j01_matric   = arrematric.k00_matric
                    inner join arreinstit on arrecad.k00_numpre = arreinstit.k00_numpre
                    inner join db_config on db_config.codigo = arreinstit.k00_instit
            where arrecad.k00_numpre = $1
                and arrecad.k00_numpar = $2
                and arrecad.k00_receit = $3
            union all
            select arrecad.k00_numpre,
                    arrecad.k00_numpar,
                    arrecad.k00_receit,
                    arrecad.k00_dtvenc,
                    arrecad.k00_dtoper,
                    arrecad.k00_numcgm,
                    cast(0 as integer)  as k00_matric,
                    cast(0 as float8)   as k00_percmatric,
                    arreinscr.k00_inscr as k00_inscr,
                    arreinscr.k00_perc  as k00_percinscr,
                    arrecad.k00_tipo
                from arrecad
                    inner join arreinscr  on arreinscr.k00_numpre  = arrecad.k00_numpre
            where arrecad.k00_numpre = $1
                and arrecad.k00_numpar = $2
                and arrecad.k00_receit = $3
            union all
            select arrecad.k00_numpre,
                    arrecad.k00_numpar,
                    arrecad.k00_receit,
                    arrecad.k00_dtvenc,
                    arrecad.k00_dtoper,
                    arrenumcgm.k00_numcgm,
                    cast(0 as integer)  as k00_matric,
                    cast(0 as float8)   as k00_percmatric,
                    cast(0 as integer)  as k00_inscr,
                    cast(0 as float8)   as k00_percinscr,
                    arrecad.k00_tipo
                from arrecad
                    inner join arrenumcgm on arrenumcgm.k00_numpre = arrecad.k00_numpre
                    left  join arreinscr  on arreinscr.k00_numpre  = arrecad.k00_numpre
                    left  join arrematric on arrematric.k00_numpre = arrecad.k00_numpre
            where arrecad.k00_numpre = $1
                and arrecad.k00_numpar = $2
                and arrecad.k00_receit = $3
                and arreinscr.k00_numpre is null
                and arrematric.k00_numpre is null
            ) as deb
            inner join arreinstit on arreinstit.k00_numpre = deb.k00_numpre
            inner join arretipo   on arretipo.k00_tipo     = deb.k00_tipo;

    prepare select_exerc_tipo_5(integer, integer) as
        select v01_exerc
        from divida
        where v01_numpre = $1
        and v01_numpar = $2
        limit 1;

    prepare select_exerc_tipo_18(integer, integer) as
        select case when divida.v01_exerc is null then extract (year from arrecad.k00_dtoper) else divida.v01_exerc end
        from arrecad
            inner join inicialnumpre on inicialnumpre.v59_numpre = arrecad.k00_numpre
            inner join inicialcert   on inicialcert.v51_inicial  = inicialnumpre.v59_inicial
            inner join certid        on certid.v13_certid        = inicialcert.v51_certidao
            inner join certdiv       on certdiv.v14_certid       = certid.v13_certid
            inner join divida        on certdiv.v14_coddiv       = divida.v01_coddiv
                                    and divida.v01_numpre        = arrecad.k00_numpre
                                    and divida.v01_numpar        = arrecad.k00_numpar
        where arrecad.k00_numpre = $1
        and arrecad.k00_numpar = $2
        limit 1;

    prepare select_dtoper_termo(integer) as
        select v07_dtlanc
        from termo
        where v07_numpre = $1
        limit 1;

    exception when duplicate_prepared_statement then
    end;

    for v_debitos in execute 'execute select_debitos('||numpre||', '||numpar||', '||receita||')'
    loop
    v_exerc := null; 
    
    if v_debitos.k03_tipo = 5 then
    
        execute 'execute select_exerc_tipo_5('||numpre||', '||numpar||')'
        into v_exerc;
        
    elsif v_debitos.k03_tipo = 18 then
    
        execute 'execute select_exerc_tipo_18('||numpre||', '||numpar||')'
        into v_exerc;
        
    end if;

    if v_debitos.k03_tipo in (6, 21, 28, 30) then
    
        execute 'execute select_dtoper_termo('||numpre||')'
        into v_dtoper;
        
        if v_dtoper is null then
        v_dtoper := v_debitos.k00_dtoper;
        end if;
        
    else
        v_dtoper := v_debitos.k00_dtoper;
    end if;

    if v_exerc is null then
        v_exerc := to_char(v_debitos.k00_dtoper, 'yyyy');
    end if;
    
    if v_debitos.k00_percmatric > 0 and v_debitos.k00_percmatric::float8 <> 100::float8 then
        nperccalc := v_debitos.k00_percmatric::float8;
    elsif v_debitos.k00_percinscr > 0 and v_debitos.k00_percinscr::float8 <> 100::float8 then
        nperccalc := v_debitos.k00_percinscr::float8;
    else
        nperccalc := 100::float8;
    end if;

    -- acumula valor historico para comparar com o arrecad
    nValorHistorico := round( (valor_hist  * nperccalc/100)::float8 ,2);
    nValorCorrecao  := round( (vlrcorrecao * nperccalc/100)::float8 ,2);
    nValorJuros     := round( (vlrjuros    * nperccalc/100)::float8 ,2);
    nValorMulta     := round( (vlrmulta    * nperccalc/100)::float8 ,2);
    nValorDesconto  := round( (vlrdesconto * nperccalc/100)::float8 ,2);

    nAcumHistorico  := nAcumHistorico + nValorHistorico;
    nDiferencaHis   := round(valor_hist - nAcumHistorico, 2);

    nAcumCorrecao   := nAcumCorrecao + nValorCorrecao;
    nDiferencaCor   := round(vlrcorrecao - nAcumCorrecao, 2);

    nAcumJuros      := nAcumJuros + nValorJuros;
    nDiferencaJur   := round(vlrjuros - nAcumJuros, 2);

    nAcumMulta      := nAcumMulta + nValorMulta;
    nDiferencaMul   := round(vlrmulta - nAcumMulta, 2);

    nAcumDesconto   := nAcumDesconto + nValorDesconto;
    nDiferencaDes   := round(vlrdesconto - nAcumDesconto, 2);

    -- efetuar acerto dos centavos do valor historico
    if abs(nDiferencaHis) = cast(0.01 as float8) then
        nValorHistorico := nValorHistorico + nDiferencaHis;
    end if;

    -- efetuar acerto dos centavos do valor corrigido
    if abs(nDiferencaCor) = cast(0.01 as float8) then
        nValorCorrecao := nValorCorrecao + nDiferencaCor;
    end if;

    -- efetuar acerto dos centavos do valor juros
    if abs(nDiferencaJur) = cast(0.01 as float8) then
        nValorJuros := nValorJuros + nDiferencaJur;
    end if;

    -- efetuar acerto dos centavos do valor multa
    if abs(nDiferencaMul) = cast(0.01 as float8) then
        nValorMulta := nValorMulta + nDiferencaMul;
    end if;

    -- efetuar acerto dos centavos do valor desconto
    if abs(nDiferencaDes) = cast(0.01 as float8) then
        nValorDesconto := nValorDesconto + nDiferencaDes;
    end if;

    select k00_hist 
        into historic
        from arrecad 
    where k00_numpre = v_debitos.k00_numpre 
        and k00_numpar = v_debitos.k00_numpar 
        and k00_receit = v_debitos.k00_receit 
    limit 1;

    execute '
        insert into '||sNomedebitos||'
                        (k22_data ,
                            k22_numpre,
                            k22_numpar,
                            k22_receit,
                            k22_dtvenc,
                            k22_dtoper,
                            k22_hist  ,
                            k22_numcgm,
                            k22_matric,
                            k22_inscr ,
                            k22_tipo  ,
                            k22_vlrhis,
                            k22_vlrcor,
                            k22_juros ,
                            k22_multa ,
                            k22_desconto,
                            k22_exerc,
                            k22_instit )
                values ( '||quote_literal(dtemite)||',
                            '||coalesce(v_debitos.k00_numpre, 0)||',
                            '||coalesce(v_debitos.k00_numpar, 0)||',
                            '||coalesce(v_debitos.k00_receit, 0)||',
                            '||quote_literal(v_debitos.k00_dtvenc)||',
                            '||quote_literal(v_dtoper)||',
                            '||coalesce(historic, 0)||',
                            '||coalesce(v_debitos.k00_numcgm, 0)||',
                            '||coalesce(v_debitos.k00_matric, 0)||',
                            '||coalesce(v_debitos.k00_inscr, 0)||',
                            '||coalesce(v_debitos.k00_tipo, 0)||',
                            '||coalesce(nValorHistorico, 0.0)||',
                            '||coalesce(nValorCorrecao, 0.0)||',
                            '||coalesce(nValorJuros, 0.0)||',
                            '||coalesce(nValorMulta, 0.0)||',
                            '||coalesce(nValorDesconto, 0.0)||',
                            '||coalesce(v_exerc, 0)||',
                            '||coalesce(v_debitos.k00_instit, 0)||')' ; 
    
    end loop;
    
    if lRaise is true then
    perform fc_debug('<calcula> 6 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;        
    return '0          0.0         0.00         0.00         0.00         0.00                   0.000000';
    
else

    if lRaise is true then
    perform fc_debug('<calcula> 7 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;      
    return trim(numero_erro) || to_char(valor_hist,'999999990.00')|| to_char(vlrcorrecao,'999999990.00') || to_char(vlrjuros,'999999990.00') || to_char(vlrmulta,'999999990.00') || to_char(vlrdesconto,'999999990.00') || to_char(venc_unic,'yyyy-mm-dd') || to_char(vlrtotinf,'999999990.000000')||qrinflator;
    
end if;
end if;

else

-- criar select para pegar valor do estorno ver possibilidade
-- de listar pela data de pagamento
if numpar = 0 then

select sum(k00_valor)
    into vlrcorrecao
    from arrepaga
where k00_numpre = numpre ;

else

select sum(k00_valor)
    into vlrcorrecao
    from arrepaga
where k00_numpre = numpre 
    and k00_numpar = numpar;

end if;

if vlrcorrecao is null then

select round(sum(k00_valor),2) as sum
    into vlrcorrecao
    from db_reciboweb
inner join arrepaga on db_reciboweb.k99_numpre   = arrepaga.k00_numpre 
                    and db_reciboweb.k99_numpar   = arrepaga.k00_numpar 
where db_reciboweb.k99_numpre_n = numpre;
if vlrcorrecao is null then

    if lRaise is true then
    perform fc_debug('<calcula> 8 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;      
    return '9          0.0         0.00         0.00         0.00         0.00                   0.000000';
    
else
    
    select round(sum(k00_valor),2) as sum
    into vlrestorno
    from recibopaga
    where k00_numnov = numpre;
    if vlrestorno != vlrcorrecao then
    
    if lRaise is true then
        perform fc_debug('<calcula> 9 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;        
    return '2' || to_char(vlrcorrecao,'999999990.00');
    
    else
    
    if lRaise is true then
        perform fc_debug('<calcula> 10 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
    end if;        
    return '4' || to_char(vlrcorrecao,'999999990.00');
    
    end if;
    
end if;

else

if lRaise is true then
    perform fc_debug('<calcula> 11 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
end if;    
return '4' || to_char(vlrcorrecao,'999999990.00');

end if;

end if;

if lRaise is true then
perform fc_debug('<calcula> 12 - Fim do processamento do calculo de correcao, juros e multa...', lRaise, false, true);
end if;

return '9          0.0         0.00         0.00         0.00         0.00                   0.000000';

end;
$$ language 'plpgsql';
SQL;
        $this->executeQuery($sql);
    }

    private function executeQuery($sql)
    {
        DB::connection()->getPdo()->exec($sql);
    }
}
