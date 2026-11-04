<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23234CorrecaoCalculoIsencaoCarazinho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::connection()->getPdo()->exec(<<<SQL
      CREATE OR REPLACE FUNCTION cadastro.fc_calculoiptu_car_2022(
	integer,
	integer,
	boolean,
	boolean,
	boolean,
	boolean,
	boolean,
	integer,
	integer)
    RETURNS character varying
    LANGUAGE 'plpgsql'
AS \$function\$

           declare

              iMatricula           alias   for $1; -- matricula
              iAnousu              alias   for $2; -- exercicio
              bGerafinanc          alias   for $3; -- gera financeiro
              bAtualizap           alias   for $4; -- atualiza parcela
              bNovonumpre          alias   for $5; -- novo numpre
              bCalculogeral        alias   for $6; -- calculo geral
              bDemo                alias   for $7; -- gera demonstrativo
              iParcelaini          alias   for $8; -- parcela inicial
              iParcelafim          alias   for $9; -- parcela final

              iIdbql               integer default 0;
              iNumcgm              integer default 0;
              iCodcli              integer default 0;
              iCodisen             integer default 0;
              iTipois              integer default 0;
              iParcelas            integer default 0;
              iNumconstr           integer default 0;
              iZona                integer default 0;
              iNumImoveis          integer default 0;
              iCodErro             integer default 0;
              iUsoComercial        integer default 0;

              dDatabaixa           date;

              nAreal               numeric default 0;
              nAreaTotalC          numeric default 0;
              nAreac               numeric default 0;
              nTotarea             numeric default 0;
              nFracao              numeric default 0;
              nFracaolote          numeric default 0;
              nAliquota            numeric default 0;
              nIsenaliq            numeric default 0;
              nArealo              numeric default 0;
              nVlrUrm              numeric default 0;
              nVvc                 numeric(15,2) default 0;
              nVvt                 numeric(15,2) default 0;
              nVv                  numeric(15,2) default 0;
              nViptu               numeric(15,2) default 0;

              tRetorno             text default '';
              tDemo                text default '';
              tCpfCnpj             text default ''; -- length 11 = CPF / length 14 = CNPJ

              bFinanceiro          boolean;
              bDadosIptu           boolean;
              bErro                boolean;
              bIsentaxas           boolean;
              bTempagamento        boolean;
              bEmpagamento         boolean;
              bTaxasCalculadas     boolean;
              bRaise               boolean default false; -- true para habilitar raise na funcao principal
              bSubRaise            boolean default false; -- true para habilitar raise nas sub-funcoes

              rCfiptu              record;

           begin

             /*

             1 - criar funcao que recebe anousu e codigo do cliente
               1.1 - esta funcao tera responsabilidade de escolher qual funcao de calculo
                     devera executar de acordo com seu codigo de cliente e exercicio de
                     calculo(pois as regras de calculo podem mudar)
                     essa funcao pode ler o codigo da funcao para calcular da cfiptu, onde pode ser
                     configurado por cliente e por exercio(com isso podemos ter todas as funcoes de calculo
                     instaladas na base do cliente, como ja acontece com as taxas bastando
                     esta configurado corretamente na cfiptu)
                     Para agilizar devemos documenta-las e usar a funcao montachamadafuncao

               1.2 - Para o

             */

             if bRaise then
               raise notice 'IDBQL - %  AREAL - %  FRACAO - %  CGM - %   DATABAIXA - %   ERRO - %  RETORNO - %',  iIdbql,  nAreal,  nFracao,  iNumcgm,  dDatabaixa, bErro, tRetorno;
             end if;

             /* VERIFICA SE OS PARAMETROS PASSADOS ESTAO CORRETOS */
             select riidbql, rnareal, rnfracao, rinumcgm, rdbaixa, rberro, rtretorno
               into iIdbql,  nAreal,  nFracao,  iNumcgm,  dDatabaixa, bErro, tRetorno
               from fc_iptu_verificaparametros(iMatricula,iAnousu,iParcelaini,iParcelafim);

             if bRaise then
               raise notice 'IDBQL - %  AREAL - %  FRACAO - %  CGM - %   DATABAIXA - %   ERRO - %  RETORNO - %',  iIdbql,  nAreal,  nFracao,  iNumcgm,  dDatabaixa, bErro, tRetorno;
             end if;


            /* VERIFICA SE O CALCULO PODE SER REALIZADO */
             select rbErro,
                    riCodErro
               into bErro,
                    iCodErro
               from fc_iptu_verificacalculo(iMatricula,iAnousu,iParcelaini,iParcelafim);
             if bErro is true and bDemo is false then
               select fc_iptu_geterro(iCodErro,'') into tRetorno;
               return tRetorno;
             end if;

             /* VERIFICA SE MATRICULA ESTA BAIXADA */
             if dDataBaixa is not null and to_char(dDataBaixa,'Y')::integer <= iAnousu then

                /* criar funcao para exclusao de calculo */
                delete from arrecad using iptunump where k00_numpre = iptunump.j20_numpre and iptunump.j20_anousu = iAnousu and iptunump.j20_matric = iMatricula;
                delete from iptunump where j20_anousu = iAnousu and j20_matric = iMatricula;
                select fc_iptu_geterro(2,'') into tRetorno;
                return tRetorno;

             end if;


             /* CRIA AS TABELAS TEMPORARIAS */
             select * into bErro from fc_iptu_criatemptable(bSubRaise);

             /* guarda o ano de calculo*/
             update tmpdadostaxa set anousu = iAnousu;

             /* GUARDA OS PARAMETROS DO CALCULO */
             select * from into rCfiptu cfiptu where j18_anousu = iAnousu;

             /* FRACIONA LOTE */
             if bRaise then
               raise notice 'PARAMETROS IPTU_FRACIONALOTE FRACAO DO LOTE : % -- % -- % -- % ',iMatricula, iAnousu, bDemo, bSubRaise;
             end if;
             select rnfracao, rtdemo, rtmsgerro, rberro
               into nFracaolote, tDemo, tRetorno, bErro
               from fc_iptu_fracionalote(iMatricula,iAnousu,bDemo,bSubRaise);
               update tmpdadosiptu set fracao = nFracaolote;
             if bRaise then
               raise notice 'RETORNO FC_IPTU_FRACIONALOTE --->>> FRACAO DO LOTE : % - DEMONS : % - MSGRETORNO : % - ERRO : % ',nFracaolote, tDemo, tRetorno, bErro;
             end if;

             /* VERIFICA PAGAMENTOS */
             if bRaise then
               raise notice 'PARAMETROS fc_iptu_verificapag VERIFICANDO PARGAMENTOS  : % -- % -- % -- % ',iMatricula, iAnousu, bDemo, bSubRaise;
             end if;
             select rbtempagamento, rbempagamento, rtmsgretorno, rberro
               into bTempagamento, bEmpagamento, tRetorno, bErro
               from fc_iptu_verificapag(iMatricula,iAnousu,bCalculogeral,bAtualizap,false,bDemo,bSubRaise);
             if bRaise then
               raise notice 'RETORNO fc_iptu_verificapag -->>> TEMPAGAMENTO : % -- EMPAGAMENTO % -- RETORNO % -- ERRO % ',bTempagamento, bEmpagamento, tRetorno, bErro;
             end if;

             /* CALCULA VALOR DO TERRENO */
             if bRaise then
               raise notice 'PARAMETROS fc_iptu_calculavvt  IDBQL : % -- FRACAO DO LOTE % -- DEMO % -- ERRO % ',iIdbql, nFracaolote, tRetorno, bErro;
             end if;
             select rnvvt, rnareatotalc,rnarea, rtdemo, rtmsgerro, rberro
               into nVvt, nAreaTotalC, nAreac, tDemo, tRetorno, bErro
               from fc_iptu_calculavvt_car_2008(iIdbql, nFracaolote, bDemo, bSubRaise);
             if bRaise then
               raise notice 'RETORNO fc_iptu_calculavvt -->>> Area Total Corrigida: % VVT : % -- AREA CORRIGIDA % --  RETORNO % -- ERRO % ',nAreaTotalC, nVvt, nAreac, tRetorno, bErro;
             end if;

             if bErro = true then
               select fc_iptu_geterro(6,'') into tRetorno;
               return tRetorno;
             end if;

             /* CALCULA VALOR DA CONSTRUCAO */
             if bRaise then
               raise notice 'PARAMETROS fc_iptu_calculavvc  MATRICULA % -- ANOUSU % -- DEMO % -- ERRO % ', iMatricula, iAnousu, bDemo, bSubRaise;
             end if;
             select rnvvc, rntotarea, rinumconstr, rtdemo, rtmsgerro, rberro
               into nVvc, nTotarea, iNumconstr, tDemo, tRetorno, bErro
               from fc_iptu_calculavvc_car_2008(iMatricula,iAnousu,bDemo,bSubRaise);
             if bRaise then
               raise notice 'RETORNO fc_iptu_calculavvc -->>> VVC : % -- AREA TOTAL : % --  NUMERO DE CONTRUCOES : % -- RETORNO : % -- ERRO : % ', nVvc, nTotarea, iNumconstr, tRetorno, bErro;
             end if;
             if nVvc is null or nVvc = 0 and iNumconstr <> 0 then
               select fc_iptu_geterro(22,'') into tRetorno;
               return tRetorno;
             end if;
             if bErro is true then
               select fc_iptu_geterro(22,'') into tRetorno;
               return tRetorno;
             end if;

             /*--------- CALCULA O VALOR VENAL -----------*/

             nVv    := nVvc + nVvt;

             if bRaise then
                raise notice 'INFLATOR -- % VALOR BASE -- % VALOR VENAL TOTAL -- % VALOR * INFLATOR -- % ',rCfiptu.j18_infla,rCfiptu.j18_vlrref,nVv,( nVv*rCfiptu.j18_vlrref );
             end if;

           /* SOLICITACAO DA TAREFA 8316, escrito por DALPOZZO

             a rotina de calculo de iptu, devera isentar do IPTU, os imoveis que tenham as seguintes caracteristicas:

             1 - o valor venal total nao pode ser superior a 10.000 URM;
             2 - o imovel tem que ser predial;
             3 - tem que ser o unico imovel do contribuinte, ele tem que ser proprietario, nao considerando as demais opcoes (promitente)
             # Regra abaixo adicionada de acordo com o redmine 13664 e modificada pelo redmine 18824
             4 - imovel deve pertencer a pessoa fisica

           */

             select i02_valor
               into nVlrUrm
               from infla
              where i02_codigo = 'URM'
                and extract(month from i02_data)::integer = 1
                and extract(year  from i02_data)::integer = iAnousu;

             select count(*) + 1
             into iNumImoveis
             from (
                   select distinct j42_matric, j42_numcgm
                     from propri inner join iptubase on j01_matric = j42_matric
                    where j42_numcgm = iNumcgm
                      and j01_baixa is null
                      and j42_matric <> iMatricula
                   union
                   select distinct j01_matric, j01_numcgm
                     from iptubase
                    where j01_numcgm = iNumcgm
                      and j01_baixa is null
                      and j01_matric <> iMatricula
                  ) as x;

            if iNumImoveis = 1 then
               perform from promitente where j41_numcgm = iNumcgm and j41_matric <> iMatricula;	       
               if found then
                  iNumImoveis = iNumImoveis + 1;
               end if;
            end if;

             select z01_cgccpf
               into tCpfCnpj
               from cgm
              where z01_numcgm = iNumcgm;

             select count(j48_caract)
             into iUsoComercial
             from carconstr
             where j48_matric = iMatricula
               and j48_caract = 112;

             if nVv <= ( 10000 * nVlrUrm )::numeric and iNumconstr > 0 and iNumImoveis = 1 and length(trim(tCpfCnpj)) = 11 and iUsoComercial < 1 then

               select fc_iptu_registraisencao(iMatricula,iAnousu,bDemo,bSubRaise) into bErro;

               if bErro is false then
                 select fc_iptu_geterro(28,'') into tRetorno;
                 return tRetorno;
               end if;

                 /* VERIFICA ISENCOES */
                 if bRaise then
                     raise notice 'PARAMETROS fc_iptu_verificaisencoes  MATRICULA % -- ANOUSU % -- DEMO % -- ERRO % ', iMatricula, iAnousu, bDemo, bSubRaise;
                 end if;

                 select ricodisen, ritipois, rnisenaliq, rbisentaxas, rnarealo
                   into iCodisen, iTipois, nIsenaliq, bIsentaxas, nArealo
                   from fc_iptu_verificaisencoes(iMatricula,iAnousu,bDemo,bSubRaise);

                 if iTipois is not null then
                     raise notice 'tipo isen -- % isentataxas -- % ',iTipois,bIsentaxas;
                    update tmpdadosiptu set tipoisen = iTipois, isentaxas = bIsentaxas;
                 end if;

                 if bRaise then
                     raise notice 'RETORNO fc_iptu_verificaisencoes -->>> CODISEN : % -- TIPOISEN : % --  ALIQ INSEN : % -- INSENTAXAS: % -- AREALO : % ',iCodisen, iTipois, nIsenaliq, bIsentaxas, nArealo;
                 end if;
             else
                 /* VERIFICA ISENCOES */
                 if bRaise then
                     raise notice 'PARAMETROS fc_iptu_verificaisencoes  MATRICULA % -- ANOUSU % -- DEMO % -- ERRO % ', iMatricula, iAnousu, bDemo, bSubRaise;
                 end if;

                 select ricodisen, ritipois, rnisenaliq, rbisentaxas, rnarealo
                   into iCodisen, iTipois, nIsenaliq, bIsentaxas, nArealo
                   from fc_iptu_verificaisencoes(iMatricula,iAnousu,bDemo,bSubRaise);

                 if iTipois is not null then
                     raise notice 'tipo isen -- % isentataxas -- % ',iTipois,bIsentaxas;
                    update tmpdadosiptu set tipoisen = iTipois, isentaxas = bIsentaxas;
                 end if;

                 if bRaise then
                     raise notice 'RETORNO fc_iptu_verificaisencoes -->>> CODISEN : % -- TIPOISEN : % --  ALIQ INSEN : % -- INSENTAXAS: % -- AREALO : % ',iCodisen, iTipois, nIsenaliq, bIsentaxas, nArealo;
                 end if;

             end if;
             /*------------------------------------------*/
             /* BUSCA A ALIQUOTA  */
             -- so executar se nao for isento

             if iNumconstr is not null and iNumconstr > 0 then
                 select fc_iptu_getaliquota(iMatricula,iIdbql,iNumcgm,true,bSubRaise) into nAliquota;
             else
                 select fc_iptu_getaliquota(iMatricula,iIdbql,iNumcgm,false,bSubRaise) into nAliquota;
             end if;

             if not found or nAliquota = 0 then
                 select fc_iptu_geterro(13,'') into tRetorno;
                 return tRetorno;
             end if;

           /*----------- APLICA ALIQUOTA ---------------*/

             nViptu := nVv * ( nAliquota / 100 );

           /*-------------------------------------------*/
             select count(*)
               into iParcelas
               from cadvencdesc
                    inner join cadvenc on q92_codigo = q82_codigo
              where q92_codigo = rCfiptu.j18_vencim ;
             if not found or iParcelas = 0 then
               select fc_iptu_geterro(14,'') into tRetorno;
               return tRetorno;
             end if;

             select j34_zona
               into iZona
               from lote
              where j34_idbql = iIdbql;

             perform predial from tmpdadosiptu where predial is true;
             if found then
               insert into tmprecval values (rCfiptu.j18_rpredi, nViptu, 1, false);
             else
               insert into tmprecval values (rCfiptu.j18_rterri, nViptu, 1, false);
             end if;

             update tmpdadosiptu set viptu = nViptu, codvenc = rCfiptu.j18_vencim , aliq = nAliquota;

             update tmpdadostaxa
                set anousu = iAnousu,
                    matric = iMatricula,
                    idbql = iIdbql,
                    valiptu = nViptu,
                    valref = rCfiptu.j18_vlrref,
                    vvt = nVvt,
                    nparc = iParcelas,
                    zona = iZona,
                    totareaconst = nTotArea ;

           /* CALCULA AS TAXAS */
             select db21_codcli
               into iCodcli
               from db_config
              where prefeitura is true;

             if bRaise then
               raise notice 'PARAMETROS fc_iptu_calculataxas  ANOUSU % -- CODCLI % ',iAnousu, iCodcli;
             end if;


             select fc_iptu_calculataxas(iMatricula,iAnousu,iCodcli,bSubRaise)
                into bTaxasCalculadas;

             if bRaise then
               raise notice 'RETORNO fc_iptu_calculataxas --->>> TAXASCALCULADAS - %',bTaxasCalculadas;
             end if;

           /* MONTA O DEMONSTRATIVO */
             select fc_iptu_demonstrativo(iMatricula,iAnousu,iIdbql,bSubRaise )
                into tDemo;

           /* GERA FINANCEIRO */
             if bDemo is false then -- Se nao for demonstrativo gera o financeiro, caso contrario retorna o demonstrativo
               select fc_iptu_geradadosiptu(iMatricula,iIdbql,iAnousu,nIsenaliq,bDemo,bSubRaise)
                 into bDadosIptu;
                 if bGerafinanc then
                   select fc_iptu_gerafinanceiro(iMatricula,iAnousu,iParcelaini,iParcelafim,bCalculogeral,bTempagamento,bNovonumpre,bDemo,bSubRaise)
                     into bFinanceiro;
                 end if;
             else
                return tDemo;
             end if;

             if bDemo is false then
                update iptucalc set j23_manual = tDemo where j23_matric = iMatricula and j23_anousu = iAnousu;
             end if;

             select fc_iptu_geterro(1,'') into tRetorno;
             return tRetorno;

           end;

\$function\$;

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

        CREATE OR REPLACE FUNCTION cadastro.fc_calculoiptu_car_2022(
            integer,
            integer,
            boolean,
            boolean,
            boolean,
            boolean,
            boolean,
            integer,
            integer)
            RETURNS character varying
            LANGUAGE 'plpgsql'
        AS \$function\$
        
                   declare
        
                      iMatricula           alias   for $1; -- matricula
                      iAnousu              alias   for $2; -- exercicio
                      bGerafinanc          alias   for $3; -- gera financeiro
                      bAtualizap           alias   for $4; -- atualiza parcela
                      bNovonumpre          alias   for $5; -- novo numpre
                      bCalculogeral        alias   for $6; -- calculo geral
                      bDemo                alias   for $7; -- gera demonstrativo
                      iParcelaini          alias   for $8; -- parcela inicial
                      iParcelafim          alias   for $9; -- parcela final
        
                      iIdbql               integer default 0;
                      iNumcgm              integer default 0;
                      iCodcli              integer default 0;
                      iCodisen             integer default 0;
                      iTipois              integer default 0;
                      iParcelas            integer default 0;
                      iNumconstr           integer default 0;
                      iZona                integer default 0;
                      iNumImoveis          integer default 0;
                      iCodErro             integer default 0;
                      iUsoComercial        integer default 0;
        
                      dDatabaixa           date;
        
                      nAreal               numeric default 0;
                      nAreaTotalC          numeric default 0;
                      nAreac               numeric default 0;
                      nTotarea             numeric default 0;
                      nFracao              numeric default 0;
                      nFracaolote          numeric default 0;
                      nAliquota            numeric default 0;
                      nIsenaliq            numeric default 0;
                      nArealo              numeric default 0;
                      nVlrUrm              numeric default 0;
                      nVvc                 numeric(15,2) default 0;
                      nVvt                 numeric(15,2) default 0;
                      nVv                  numeric(15,2) default 0;
                      nViptu               numeric(15,2) default 0;
        
                      tRetorno             text default '';
                      tDemo                text default '';
                      tCpfCnpj             text default ''; -- length 11 = CPF / length 14 = CNPJ
        
                      bFinanceiro          boolean;
                      bDadosIptu           boolean;
                      bErro                boolean;
                      bIsentaxas           boolean;
                      bTempagamento        boolean;
                      bEmpagamento         boolean;
                      bTaxasCalculadas     boolean;
                      bRaise               boolean default false; -- true para habilitar raise na funcao principal
                      bSubRaise            boolean default false; -- true para habilitar raise nas sub-funcoes
        
                      rCfiptu              record;
        
                   begin
        
                     /*
        
                     1 - criar funcao que recebe anousu e codigo do cliente
                       1.1 - esta funcao tera responsabilidade de escolher qual funcao de calculo
                             devera executar de acordo com seu codigo de cliente e exercicio de
                             calculo(pois as regras de calculo podem mudar)
                             essa funcao pode ler o codigo da funcao para calcular da cfiptu, onde pode ser
                             configurado por cliente e por exercio(com isso podemos ter todas as funcoes de calculo
                             instaladas na base do cliente, como ja acontece com as taxas bastando
                             esta configurado corretamente na cfiptu)
                             Para agilizar devemos documenta-las e usar a funcao montachamadafuncao
        
                       1.2 - Para o
        
                     */
        
                     if bRaise then
                       raise notice 'IDBQL - %  AREAL - %  FRACAO - %  CGM - %   DATABAIXA - %   ERRO - %  RETORNO - %',  iIdbql,  nAreal,  nFracao,  iNumcgm,  dDatabaixa, bErro, tRetorno;
                     end if;
        
                     /* VERIFICA SE OS PARAMETROS PASSADOS ESTAO CORRETOS */
                     select riidbql, rnareal, rnfracao, rinumcgm, rdbaixa, rberro, rtretorno
                       into iIdbql,  nAreal,  nFracao,  iNumcgm,  dDatabaixa, bErro, tRetorno
                       from fc_iptu_verificaparametros(iMatricula,iAnousu,iParcelaini,iParcelafim);
        
                     if bRaise then
                       raise notice 'IDBQL - %  AREAL - %  FRACAO - %  CGM - %   DATABAIXA - %   ERRO - %  RETORNO - %',  iIdbql,  nAreal,  nFracao,  iNumcgm,  dDatabaixa, bErro, tRetorno;
                     end if;
        
        
                    /* VERIFICA SE O CALCULO PODE SER REALIZADO */
                     select rbErro,
                            riCodErro
                       into bErro,
                            iCodErro
                       from fc_iptu_verificacalculo(iMatricula,iAnousu,iParcelaini,iParcelafim);
                     if bErro is true and bDemo is false then
                       select fc_iptu_geterro(iCodErro,'') into tRetorno;
                       return tRetorno;
                     end if;
        
                     /* VERIFICA SE MATRICULA ESTA BAIXADA */
                     if dDataBaixa is not null and to_char(dDataBaixa,'Y')::integer <= iAnousu then
        
                        /* criar funcao para exclusao de calculo */
                        delete from arrecad using iptunump where k00_numpre = iptunump.j20_numpre and iptunump.j20_anousu = iAnousu and iptunump.j20_matric = iMatricula;
                        delete from iptunump where j20_anousu = iAnousu and j20_matric = iMatricula;
                        select fc_iptu_geterro(2,'') into tRetorno;
                        return tRetorno;
        
                     end if;
        
        
                     /* CRIA AS TABELAS TEMPORARIAS */
                     select * into bErro from fc_iptu_criatemptable(bSubRaise);
        
                     /* guarda o ano de calculo*/
                     update tmpdadostaxa set anousu = iAnousu;
        
                     /* GUARDA OS PARAMETROS DO CALCULO */
                     select * from into rCfiptu cfiptu where j18_anousu = iAnousu;
        
                     /* FRACIONA LOTE */
                     if bRaise then
                       raise notice 'PARAMETROS IPTU_FRACIONALOTE FRACAO DO LOTE : % -- % -- % -- % ',iMatricula, iAnousu, bDemo, bSubRaise;
                     end if;
                     select rnfracao, rtdemo, rtmsgerro, rberro
                       into nFracaolote, tDemo, tRetorno, bErro
                       from fc_iptu_fracionalote(iMatricula,iAnousu,bDemo,bSubRaise);
                       update tmpdadosiptu set fracao = nFracaolote;
                     if bRaise then
                       raise notice 'RETORNO FC_IPTU_FRACIONALOTE --->>> FRACAO DO LOTE : % - DEMONS : % - MSGRETORNO : % - ERRO : % ',nFracaolote, tDemo, tRetorno, bErro;
                     end if;
        
                     /* VERIFICA PAGAMENTOS */
                     if bRaise then
                       raise notice 'PARAMETROS fc_iptu_verificapag VERIFICANDO PARGAMENTOS  : % -- % -- % -- % ',iMatricula, iAnousu, bDemo, bSubRaise;
                     end if;
                     select rbtempagamento, rbempagamento, rtmsgretorno, rberro
                       into bTempagamento, bEmpagamento, tRetorno, bErro
                       from fc_iptu_verificapag(iMatricula,iAnousu,bCalculogeral,bAtualizap,false,bDemo,bSubRaise);
                     if bRaise then
                       raise notice 'RETORNO fc_iptu_verificapag -->>> TEMPAGAMENTO : % -- EMPAGAMENTO % -- RETORNO % -- ERRO % ',bTempagamento, bEmpagamento, tRetorno, bErro;
                     end if;
        
                     /* CALCULA VALOR DO TERRENO */
                     if bRaise then
                       raise notice 'PARAMETROS fc_iptu_calculavvt  IDBQL : % -- FRACAO DO LOTE % -- DEMO % -- ERRO % ',iIdbql, nFracaolote, tRetorno, bErro;
                     end if;
                     select rnvvt, rnareatotalc,rnarea, rtdemo, rtmsgerro, rberro
                       into nVvt, nAreaTotalC, nAreac, tDemo, tRetorno, bErro
                       from fc_iptu_calculavvt_car_2008(iIdbql, nFracaolote, bDemo, bSubRaise);
                     if bRaise then
                       raise notice 'RETORNO fc_iptu_calculavvt -->>> Area Total Corrigida: % VVT : % -- AREA CORRIGIDA % --  RETORNO % -- ERRO % ',nAreaTotalC, nVvt, nAreac, tRetorno, bErro;
                     end if;
        
                     if bErro = true then
                       select fc_iptu_geterro(6,'') into tRetorno;
                       return tRetorno;
                     end if;
        
                     /* CALCULA VALOR DA CONSTRUCAO */
                     if bRaise then
                       raise notice 'PARAMETROS fc_iptu_calculavvc  MATRICULA % -- ANOUSU % -- DEMO % -- ERRO % ', iMatricula, iAnousu, bDemo, bSubRaise;
                     end if;
                     select rnvvc, rntotarea, rinumconstr, rtdemo, rtmsgerro, rberro
                       into nVvc, nTotarea, iNumconstr, tDemo, tRetorno, bErro
                       from fc_iptu_calculavvc_car_2008(iMatricula,iAnousu,bDemo,bSubRaise);
                     if bRaise then
                       raise notice 'RETORNO fc_iptu_calculavvc -->>> VVC : % -- AREA TOTAL : % --  NUMERO DE CONTRUCOES : % -- RETORNO : % -- ERRO : % ', nVvc, nTotarea, iNumconstr, tRetorno, bErro;
                     end if;
                     if nVvc is null or nVvc = 0 and iNumconstr <> 0 then
                       select fc_iptu_geterro(22,'') into tRetorno;
                       return tRetorno;
                     end if;
                     if bErro is true then
                       select fc_iptu_geterro(22,'') into tRetorno;
                       return tRetorno;
                     end if;
        
                     /*--------- CALCULA O VALOR VENAL -----------*/
        
                     nVv    := nVvc + nVvt;
        
                     if bRaise then
                        raise notice 'INFLATOR -- % VALOR BASE -- % VALOR VENAL TOTAL -- % VALOR * INFLATOR -- % ',rCfiptu.j18_infla,rCfiptu.j18_vlrref,nVv,( nVv*rCfiptu.j18_vlrref );
                     end if;
        
                   /* SOLICITACAO DA TAREFA 8316, escrito por DALPOZZO
        
                     a rotina de calculo de iptu, devera isentar do IPTU, os imoveis que tenham as seguintes caracteristicas:
        
                     1 - o valor venal total nao pode ser superior a 10.000 URM;
                     2 - o imovel tem que ser predial;
                     3 - tem que ser o unico imovel do contribuinte, ele tem que ser proprietario, nao considerando as demais opcoes (promitente)
                     # Regra abaixo adicionada de acordo com o redmine 13664 e modificada pelo redmine 18824
                     4 - imovel deve pertencer a pessoa fisica
        
                   */
        
                     select i02_valor
                       into nVlrUrm
                       from infla
                      where i02_codigo = 'URM'
                        and extract(month from i02_data)::integer = 1
                        and extract(year  from i02_data)::integer = iAnousu;
        
                     select count(*) + 1
                     into iNumImoveis
                     from (
                           select distinct j42_matric, j42_numcgm
                             from propri inner join iptubase on j01_matric = j42_matric
                            where j42_numcgm = iNumcgm
                              and j01_baixa is null
                              and j42_matric <> iMatricula
                           union
                           select distinct j01_matric, j01_numcgm
                             from iptubase
                            where j01_numcgm = iNumcgm
                              and j01_baixa is null
                              and j01_matric <> iMatricula
                          ) as x;
        
                    if iNumImoveis = 1 then
                       perform from promitente where j41_matric = iMatricula;
                   if found then
                          iNumImoveis = iNumImoveis + 1;
                       end if;
                    end if;
        
                     select z01_cgccpf
                       into tCpfCnpj
                       from cgm
                      where z01_numcgm = iNumcgm;
        
                     select count(j48_caract)
                     into iUsoComercial
                     from carconstr
                     where j48_matric = iMatricula
                       and j48_caract = 112;
        
                     if nVv <= ( 10000 * nVlrUrm )::numeric and iNumconstr > 0 and iNumImoveis = 1 and length(trim(tCpfCnpj)) = 11 and iUsoComercial < 1 then
        
                       select fc_iptu_registraisencao(iMatricula,iAnousu,bDemo,bSubRaise) into bErro;
        
                       if bErro is false then
                         select fc_iptu_geterro(28,'') into tRetorno;
                         return tRetorno;
                       end if;
        
                         /* VERIFICA ISENCOES */
                         if bRaise then
                             raise notice 'PARAMETROS fc_iptu_verificaisencoes  MATRICULA % -- ANOUSU % -- DEMO % -- ERRO % ', iMatricula, iAnousu, bDemo, bSubRaise;
                         end if;
        
                         select ricodisen, ritipois, rnisenaliq, rbisentaxas, rnarealo
                           into iCodisen, iTipois, nIsenaliq, bIsentaxas, nArealo
                           from fc_iptu_verificaisencoes(iMatricula,iAnousu,bDemo,bSubRaise);
        
                         if iTipois is not null then
                             raise notice 'tipo isen -- % isentataxas -- % ',iTipois,bIsentaxas;
                            update tmpdadosiptu set tipoisen = iTipois, isentaxas = bIsentaxas;
                         end if;
        
                         if bRaise then
                             raise notice 'RETORNO fc_iptu_verificaisencoes -->>> CODISEN : % -- TIPOISEN : % --  ALIQ INSEN : % -- INSENTAXAS: % -- AREALO : % ',iCodisen, iTipois, nIsenaliq, bIsentaxas, nArealo;
                         end if;
                     else
                         /* VERIFICA ISENCOES */
                         if bRaise then
                             raise notice 'PARAMETROS fc_iptu_verificaisencoes  MATRICULA % -- ANOUSU % -- DEMO % -- ERRO % ', iMatricula, iAnousu, bDemo, bSubRaise;
                         end if;
        
                         select ricodisen, ritipois, rnisenaliq, rbisentaxas, rnarealo
                           into iCodisen, iTipois, nIsenaliq, bIsentaxas, nArealo
                           from fc_iptu_verificaisencoes(iMatricula,iAnousu,bDemo,bSubRaise);
        
                         if iTipois is not null then
                             raise notice 'tipo isen -- % isentataxas -- % ',iTipois,bIsentaxas;
                            update tmpdadosiptu set tipoisen = iTipois, isentaxas = bIsentaxas;
                         end if;
        
                         if bRaise then
                             raise notice 'RETORNO fc_iptu_verificaisencoes -->>> CODISEN : % -- TIPOISEN : % --  ALIQ INSEN : % -- INSENTAXAS: % -- AREALO : % ',iCodisen, iTipois, nIsenaliq, bIsentaxas, nArealo;
                         end if;
        
                     end if;
                     /*------------------------------------------*/
                     /* BUSCA A ALIQUOTA  */
                     -- so executar se nao for isento
        
                     if iNumconstr is not null and iNumconstr > 0 then
                         select fc_iptu_getaliquota(iMatricula,iIdbql,iNumcgm,true,bSubRaise) into nAliquota;
                     else
                         select fc_iptu_getaliquota(iMatricula,iIdbql,iNumcgm,false,bSubRaise) into nAliquota;
                     end if;
        
                     if not found or nAliquota = 0 then
                         select fc_iptu_geterro(13,'') into tRetorno;
                         return tRetorno;
                     end if;
        
                   /*----------- APLICA ALIQUOTA ---------------*/
        
                     nViptu := nVv * ( nAliquota / 100 );
        
                   /*-------------------------------------------*/
                     select count(*)
                       into iParcelas
                       from cadvencdesc
                            inner join cadvenc on q92_codigo = q82_codigo
                      where q92_codigo = rCfiptu.j18_vencim ;
                     if not found or iParcelas = 0 then
                       select fc_iptu_geterro(14,'') into tRetorno;
                       return tRetorno;
                     end if;
        
                     select j34_zona
                       into iZona
                       from lote
                      where j34_idbql = iIdbql;
        
                     perform predial from tmpdadosiptu where predial is true;
                     if found then
                       insert into tmprecval values (rCfiptu.j18_rpredi, nViptu, 1, false);
                     else
                       insert into tmprecval values (rCfiptu.j18_rterri, nViptu, 1, false);
                     end if;
        
                     update tmpdadosiptu set viptu = nViptu, codvenc = rCfiptu.j18_vencim , aliq = nAliquota;
        
                     update tmpdadostaxa
                        set anousu = iAnousu,
                            matric = iMatricula,
                            idbql = iIdbql,
                            valiptu = nViptu,
                            valref = rCfiptu.j18_vlrref,
                            vvt = nVvt,
                            nparc = iParcelas,
                            zona = iZona,
                            totareaconst = nTotArea ;
        
                   /* CALCULA AS TAXAS */
                     select db21_codcli
                       into iCodcli
                       from db_config
                      where prefeitura is true;
        
                     if bRaise then
                       raise notice 'PARAMETROS fc_iptu_calculataxas  ANOUSU % -- CODCLI % ',iAnousu, iCodcli;
                     end if;
        
        
                     select fc_iptu_calculataxas(iMatricula,iAnousu,iCodcli,bSubRaise)
                        into bTaxasCalculadas;
        
                     if bRaise then
                       raise notice 'RETORNO fc_iptu_calculataxas --->>> TAXASCALCULADAS - %',bTaxasCalculadas;
                     end if;
        
                   /* MONTA O DEMONSTRATIVO */
                     select fc_iptu_demonstrativo(iMatricula,iAnousu,iIdbql,bSubRaise )
                        into tDemo;
        
                   /* GERA FINANCEIRO */
                     if bDemo is false then -- Se nao for demonstrativo gera o financeiro, caso contrario retorna o demonstrativo
                       select fc_iptu_geradadosiptu(iMatricula,iIdbql,iAnousu,nIsenaliq,bDemo,bSubRaise)
                         into bDadosIptu;
                         if bGerafinanc then
                           select fc_iptu_gerafinanceiro(iMatricula,iAnousu,iParcelaini,iParcelafim,bCalculogeral,bTempagamento,bNovonumpre,bDemo,bSubRaise)
                             into bFinanceiro;
                         end if;
                     else
                        return tDemo;
                     end if;
        
                     if bDemo is false then
                        update iptucalc set j23_manual = tDemo where j23_matric = iMatricula and j23_anousu = iAnousu;
                     end if;
        
                     select fc_iptu_geterro(1,'') into tRetorno;
                     return tRetorno;
        
                   end;
        
        \$function\$;
SQL
    );   
    }
}
