<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class M23197AlteracaoProcedureCalculoIptuBage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::connection()->getPdo()->exec(<<<SQL
      -- quando caracteristica 441 e tem mais de um logradouro, o sistema calcula 10% a mais
-- V_TEM_CARACT_ESQUINA = 441 quando encontrada no carlot
-- no iptucalc a area gravada e a area corrigidae nao a area real

set check_function_bodies to on;
create or replace function fc_calculoiptu_bag_2019(integer,integer,boolean,boolean,boolean,boolean,boolean,integer,integer)
RETURNS VARCHAR(100)
as $$
DECLARE

  MATRICULA 	      ALIAS FOR $1;
  ANOUSU    	      ALIAS FOR $2;
  GERAFINANC          ALIAS FOR $3;
  ATUALIZAP	      ALIAS FOR $4;
  NOVONUMPRE	      ALIAS FOR $5;
  CALCULO_GERAL       ALIAS FOR $6;
  DEMO		      ALIAS FOR $7;
  PARCELAINI          ALIAS FOR $8;
  PARCELAFIM          ALIAS FOR $9;

  V_SETOR	      CHAR(4);
  V_QUADRA	      CHAR(4);
  V_LOTE	      CHAR(4);
 
  V_IPTUFRAC	 	    INTEGER;
  V_TOTMAT                  INTEGER;
  V_QFACE  	   	    INTEGER ;
	iHistIptuIsen	    INTEGER ;
  V_IDBQL                   INTEGER DEFAULT 0;
  V_QUANT_TESTADA	    INTEGER DEFAULT 0;
  V_NUMERO_ESQUINA          INTEGER DEFAULT 0;
  V_TIPOI		    INTEGER DEFAULT 0;
 
  V_TOTCONSTR		    FLOAT8 DEFAULT 0;
  V_TOTMATRICULA            FLOAT8 DEFAULT 0;
  V_AREALO                  FLOAT8;
  V_TAXABOMB		    FLOAT8;
	nValIsenTaxa1       FLOAT8;
  V_TOTALZAO                FLOAT8 DEFAULT 0;
  V_TAXALIMP		    FLOAT8 DEFAULT 0;
  V_VALINFLATOR	            FLOAT8;
  V_VM2T    		    FLOAT8 DEFAULT 0;
  V_AREAL   		    FLOAT8 DEFAULT 0;
  V_AREAL_GLEBA             FLOAT8 DEFAULT 0;
  V_AREAL_GLEBA_DEMAIS      FLOAT8 DEFAULT 0;
  V_PROFUND_PADRAO 	    FLOAT8 DEFAULT 0;
  V_PROFUND_MEDIA 	    FLOAT8 DEFAULT 0;
  V_INDICE_CORRECAO	    FLOAT8 DEFAULT 0;
  V_INDICE_MULTI  	    FLOAT8 DEFAULT 0;
  V_TESTADA 	    	    FLOAT8 DEFAULT 0;
  V_AREA_CORRIG 	    FLOAT8 DEFAULT 0;
  V_AREA_TRIBUT 	    FLOAT8 DEFAULT 0;
  V_VVT	          	    FLOAT8 DEFAULT 0;
  V_J01_FRACAO	  	    FLOAT8 DEFAULT 0;
  AREATOTCONSTRMAT          FLOAT8 DEFAULT 0;
  V_FRACAO      	    FLOAT8 DEFAULT 100;

  V_CARAC		VARCHAR(250);
  V_BAIXA		DATE;
  V_AREATC              FLOAT8 DEFAULT 0;
  V_AREAC		FLOAT8 DEFAULT 0;
  V_VVC		        FLOAT8 DEFAULT 0;
  V_VVCP		FLOAT8 DEFAULT 0;
  V_VM2C		FLOAT8 DEFAULT 0;
  V_VM2C2		FLOAT8 DEFAULT 0;
  V_VV			FLOAT8 DEFAULT 0;
  V_AREACALC		FLOAT8 DEFAULT 0;
  V_ALIPRE		FLOAT8 DEFAULT 0;
  V_ALITER		FLOAT8 DEFAULT 0;
  V_ALIQ		FLOAT8 DEFAULT 0;
  V_ALIQANT		FLOAT8 DEFAULT 0;
  V_RECIPTU		INTEGER;
  V_IPTU		FLOAT8 DEFAULT 0;
  V_DIGITO		INTEGER;
  V_MESMONUMPRE	BOOLEAN DEFAULT FALSE;
  V_NUMPRE		INTEGER;
  V_NUMPREEXISTE INTEGER;
  V_VENCIM		INTEGER;
  V_PARCELAS		INTEGER;
  V_VALORPAR		FLOAT8 DEFAULT 0;
  V_PARINI		FLOAT8 DEFAULT 1;
  V_QUANT_IMOVEIS	INTEGER DEFAULT 0;
  V_NUMCGM		INTEGER;
  V_CGMPRI		INTEGER;
  V_CGMPRITESTE	INTEGER;
  V_DTOPER		DATE;
  V_TEMFINANC		BOOLEAN DEFAULT FALSE;
  V_SOMA		FLOAT8;
  V_DIFIPTU		FLOAT8 default 0;
  V_ISENALIQ		FLOAT8;
  V_VLRISEN		FLOAT8 DEFAULT 0;
  V_NUMBCO		VARCHAR(15);
  V_TEMPAGAMENTO	BOOLEAN DEFAULT FALSE;
  V_TIPOIS		INTEGER;
  V_ISENTAXAS		BOOLEAN DEFAULT FALSE;
  V_CODISEN 		INTEGER;
  V_CODTIPOISEN	INTEGER;
  V_TESTAISEN		INTEGER;
  V_PROJETOCURA	FLOAT8 DEFAULT 0;
  V_CONDOMINIO		INTEGER DEFAULT 0;
  V_MURO		BOOLEAN DEFAULT FALSE;
  V_CALCADA		BOOLEAN DEFAULT FALSE;
  V_PAVIMENTACAO  	INTEGER DEFAULT 0;
  V_BEIRAL		BOOLEAN DEFAULT FALSE;
  V_SANITARIA 		BOOLEAN DEFAULT TRUE;
  V_IRREGULAR		BOOLEAN DEFAULT FALSE;
  V_QLIMPEZA		FLOAT8 DEFAULT 0;
  V_ZONA		INTEGER DEFAULT 0;
  V_COMERCIO		BOOLEAN DEFAULT FALSE;
  V_DESCONTOT		FLOAT8	DEFAULT 0;
  V_DESCONTOP		FLOAT8	DEFAULT 0;
  V_ILUMINACAO		INTEGER	DEFAULT 0;
  V_ALAGADO		BOOLEAN DEFAULT FALSE;
  V_ENCRAVADO		BOOLEAN DEFAULT FALSE;
  V_GLEBA		BOOLEAN DEFAULT FALSE;
  V_ESPECIE		INTEGER DEFAULT 0;
  V_USO		INTEGER DEFAULT 0;
  V_ANOCONSTRUCAO	INTEGER DEFAULT 0;
  V_TIPOCONSTRUCAO	INTEGER DEFAULT 0;
  V_UTILIDADEPUBLICA	BOOLEAN DEFAULT FALSE;
  V_1			INTEGER DEFAULT 1;
  V_2			INTEGER DEFAULT 2;
  V_LOTEAM		FLOAT8 DEFAULT 0;
  V_TIPOIMP            CHAR(1);
  V_DIAINI		DATE;
  V_DIAFIM		DATE;

  V_TAXA1		FLOAT8 DEFAULT 0;
  V_VALORTX1		FLOAT8 DEFAULT 0;
  V_SOMATX1 		FLOAT8 DEFAULT 0;
  V_DIFTX1 		FLOAT8 DEFAULT 0;
  V_RECTX1		INTEGER;

  V_TAXA2		FLOAT8 DEFAULT 0;
  V_VALORTX2		FLOAT8 DEFAULT 0;
  V_SOMATX2 		FLOAT8 DEFAULT 0;
  V_DIFTX2 		FLOAT8 DEFAULT 0;
  V_RECTX2		INTEGER;
  V_TAXA3		FLOAT8 DEFAULT 0;
  V_VALORTX3		FLOAT8 DEFAULT 0;
  V_SOMATX3 		FLOAT8 DEFAULT 0;
  V_DIFTX3 		FLOAT8 DEFAULT 0;
  V_RECTX3		INTEGER;
  V_TAXA4		FLOAT8 DEFAULT 0;
  V_VALORTX4		FLOAT8 DEFAULT 0;
  V_SOMATX4 		FLOAT8 DEFAULT 0;
  V_DIFTX4 		FLOAT8 DEFAULT 0;
  V_RECTX4		INTEGER;
  V_TAXA5		FLOAT8 DEFAULT 0;
  V_VALORTX5		FLOAT8 DEFAULT 0;
  V_SOMATX5 		FLOAT8 DEFAULT 0;
  V_DIFTX5 		FLOAT8 DEFAULT 0;
  V_RECTX5		INTEGER;
  V_TAXA6		FLOAT8 DEFAULT 0;
  V_VALORTX6		FLOAT8 DEFAULT 0;
  V_SOMATX6 		FLOAT8 DEFAULT 0;
  V_DIFTX6 		FLOAT8 DEFAULT 0;
  V_RECTX6		INTEGER;
  V_TAXA7		FLOAT8 DEFAULT 0;
  V_VALORTX7		FLOAT8 DEFAULT 0;
  V_SOMATX7 		FLOAT8 DEFAULT 0;
  V_DIFTX7 		FLOAT8 DEFAULT 0;
  V_RECTX7		INTEGER;
  V_TAXA8		FLOAT8 DEFAULT 0;
  V_VALORTX8		FLOAT8 DEFAULT 0;
  V_SOMATX8 		FLOAT8 DEFAULT 0;
  V_DIFTX8 		FLOAT8 DEFAULT 0;
  V_RECTX8		INTEGER;
  V_TAXA9		FLOAT8 DEFAULT 0;
  V_VALORTX9		FLOAT8 DEFAULT 0;
  V_SOMATX9 		FLOAT8 DEFAULT 0;
  V_DIFTX9 		FLOAT8 DEFAULT 0;
  V_RECTX9		INTEGER;
  V_QUATX		INTEGER DEFAULT 0;
  V_J71_VALOR		FLOAT8 DEFAULT 0;
  V_J72_VALOR		FLOAT8 DEFAULT 0;
  V_CARTAXA		INTEGER;

  R_CONSTR          RECORD;
  R_IDCONSTR		    RECORD;
  R_VENCIM  		    RECORD;
  R_TAXA	    	    RECORD;
  R_CARLOTE	  	    RECORD;
  R_CARFACE	  	    RECORD;
  R_IPTUTAXA		    RECORD;
  R_FRACAO		      RECORD;
  R_PROPRIS		      RECORD;
  V_RECORD_ARRECAD	RECORD;

  V_MANUAL 	     TEXT DEFAULT ' \n';
 
  V_CAR_PASSEIO	INTEGER;
  V_CAR_CERCA_MURO	INTEGER;
  V_SIT_CONSTR		INTEGER DEFAULT 0;

  CAR_ESQ 		INTEGER DEFAULT 0;
  CAR_AMBAS 		INTEGER DEFAULT 0;
  V_CARACT_CALCULO	INTEGER;
  V_FORMA_TERRENO 	INTEGER;

  V_PONTOS FLOAT8 DEFAULT 0;
  V_FATOR	FLOAT8;
  V_FATOR_FORMA FLOAT8 DEFAULT 0;
  V_FATOR_ESQUINA FLOAT8 DEFAULT 0;
  V_FATOR_UTIL_LOTE FLOAT8 DEFAULT 1;
  V_FATOR_NIVEL_RUA FLOAT8 DEFAULT 1;
  V_FATOR_TOPOGRAFIA FLOAT8 DEFAULT 1;
  V_FATOR_PEDOLOGIA FLOAT8 DEFAULT 1;
  V_FATOR_HOMOGEINI FLOAT8 DEFAULT 1;
  V_FATOR_GLEBA FLOAT8 DEFAULT 1;
  V_INDICE_GLEBA FLOAT8 DEFAULT 0;

  V_PONTUACAO		INTEGER;
  V_TAXALIXOCAR	INTEGER;
  V_TAXALIXOVAL	FLOAT8 DEFAULT 0;
  V_BASE		FLOAT8 DEFAULT 0;
  V_ACRESCIMO		BOOLEAN DEFAULT FALSE;
  V_MOSTRAR		BOOLEAN DEFAULT TRUE;

  V_TEM_CARACT_ESQUINA INTEGER DEFAULT 0;
  V_TEM_CARACT_GLEBA INTEGER DEFAULT 0;
  V_EMPAGAMENTO	BOOLEAN DEFAULT FALSE;
  v_raise    		boolean default false;


  -- mudancas parcelas
  V_TEXT		TEXT DEFAULT '';
  V_PERC		FLOAT8;
  V_HIST		INTEGER;
  V_TIPO		INTEGER;
  V_PARCELAINI		INTEGER DEFAULT 0;
  V_PARCELACALC	FLOAT8;

  V_PASSA       	BOOLEAN DEFAULT TRUE;

  lProcessarArrecad	BOOLEAN DEFAULT TRUE;
  lAbatimento       BOOLEAN DEFAULT TRUE;

  iNumpreVerifica   INTEGER;

  rRecibosGerados record;

  BEGIN

    v_raise := ( case when fc_getsession('DB_debugon') is null then false else true end );

    IF PARCELAFIM < PARCELAINI THEN
      RETURN '21 PARCELAS INCONSISTENTES';
    END IF;

    SELECT J01_IDBQL, CASE WHEN J34_AREAL = 0 THEN J34_AREA ELSE J34_AREAL END AS J34_AREAL,
           J01_NUMCGM, J01_BAIXA, J34_SETOR, J34_QUADRA, J34_LOTE
      INTO V_IDBQL, V_AREAL, V_NUMCGM, V_BAIXA, V_SETOR, V_QUADRA, V_LOTE
    FROM IPTUBASE
                  INNER JOIN LOTE     ON J34_IDBQL = J01_IDBQL
                  LEFT  JOIN IPTUFRAC ON J25_ANOUSU = ANOUSU AND J25_MATRIC = J01_MATRIC
    WHERE J01_MATRIC = MATRICULA;

    IF V_IDBQL IS NULL THEN
      RETURN '16 MATRICULA NAO CADASTRADA';
    END IF;

    IF NOT V_BAIXA IS NULL THEN
      RETURN '02 MATRICULA BAIXADA';
    END IF;

    IF V_AREAL = 0 OR V_AREAL IS NULL THEN
      RETURN '03 AREA DO LOTE ZERADA';
    END IF;

    if v_raise is true then
      raise notice 'V_SETOR: % - V_QUADRA: % - V_LOTE: %', V_SETOR, V_QUADRA, V_LOTE;
    end if;


    SELECT COUNT(J01_IDBQL)
    INTO V_TOTMAT
    FROM IPTUBASE
    WHERE J01_BAIXA IS NULL AND J01_IDBQL = V_IDBQL;

    if v_raise is true then
      raise notice 'matricula: %',MATRICULA;
      raise notice 'total de matriculas: %',V_TOTMAT;
    end if;

    select j20_numpre
      into iNumpreVerifica
      from iptunump
     where j20_matric = MATRICULA
       and j20_anousu = ANOUSU
     limit 1;

    if found then

      -- Verifica se existe Pagamento Parcial para o debito informado
      select fc_verifica_abatimento(1, iNumpreVerifica)::boolean into lAbatimento;

      if lAbatimento then
        return '04 DEBITO COM PAGAMENTO PARCIAL!';
      end if;
    end if;

    IF V_TOTMAT > 1 THEN

       -- CALCULA FRACAO DO LOTE
      SELECT COALESCE(SUM(J39_AREA), 0)
        INTO V_TOTCONSTR
      FROM IPTUBASE
                    INNER JOIN IPTUCONSTR ON J39_MATRIC = J01_MATRIC
                    INNER JOIN LOTE       ON J34_IDBQL = J01_IDBQL
      WHERE J01_BAIXA IS NULL
        AND J34_SETOR = V_SETOR
        AND J34_QUADRA = V_QUADRA
        AND J34_LOTE = V_LOTE
        AND J39_DTDEMO IS NULL;

      if v_raise is true then
        raise notice 'total construido no lote: %',V_TOTCONSTR;
      end if;

      V_MANUAL := V_MANUAL || 'AREA TOTAL CONSTRUIDA NO LOTE: ' || V_TOTCONSTR || ' - ';

      IF V_TOTCONSTR = 0 THEN
         UPDATE IPTUBASE SET J01_FRACAO = 0 WHERE J01_IDBQL = V_IDBQL;
      ELSE

        if v_raise is true then
          raise notice 'entrando no fracao';
        end if;

        FOR R_FRACAO IN
                        SELECT J01_MATRIC, COALESCE(SUM(J39_AREA), 0) AS AREATOTCONSTRMAT
                        FROM IPTUBASE LEFT JOIN IPTUCONSTR ON J39_MATRIC = J01_MATRIC
                        WHERE J01_BAIXA IS NULL AND J39_DTDEMO IS NULL AND J01_IDBQL = V_IDBQL
                        GROUP BY J01_MATRIC LOOP

          if v_raise is true then
            raise notice 'processando fracao matricula: % - construido desta: %',R_FRACAO.J01_MATRIC,R_FRACAO.AREATOTCONSTRMAT;
          end if;

          SELECT J25_MATRIC
            INTO V_IPTUFRAC
          FROM IPTUFRAC
          WHERE J25_MATRIC = R_FRACAO.J01_MATRIC
            AND J25_ANOUSU = ANOUSU;

          IF V_IPTUFRAC IS NULL THEN
            if v_raise is true then
              raise notice '   insert no iptufrac';
            end if;

            INSERT INTO IPTUFRAC values (ANOUSU,R_FRACAO.J01_MATRIC,V_IDBQL,((R_FRACAO.AREATOTCONSTRMAT / V_TOTCONSTR) * 100));
          ELSE
            if v_raise is true then
               raise notice '   update no iptufrac';
               raise notice '   fracao iptufrac: %', V_IPTUFRAC;
            end if;

            UPDATE IPTUFRAC
              SET J25_FRACAO = ((R_FRACAO.AREATOTCONSTRMAT / V_TOTCONSTR) * 100), J25_IDBQL = V_IDBQL
              WHERE J25_MATRIC = R_FRACAO.J01_MATRIC AND J25_ANOUSU = ANOUSU
                AND J25_FRACAO <> ((R_FRACAO.AREATOTCONSTRMAT / V_TOTCONSTR) * 100);
          END IF;

        END LOOP;

        SELECT J25_FRACAO
          INTO V_FRACAO
        FROM IPTUFRAC
        WHERE J25_MATRIC = MATRICULA AND J25_ANOUSU = ANOUSU;

        IF V_FRACAO IS NULL OR V_FRACAO = 0 THEN
          V_FRACAO = 100::float8;
        END IF;

      END IF;

    END IF;

    SELECT J01_FRACAO
      INTO V_J01_FRACAO
    FROM IPTUBASE
    WHERE J01_MATRIC = MATRICULA;

    IF V_J01_FRACAO IS NOT NULL AND V_J01_FRACAO > 0 THEN
      V_FRACAO = V_J01_FRACAO;

      if v_raise is true then
        raise notice 'fracao da iptubase: %', V_FRACAO;
      end if;
    END IF;

    if v_raise is true then
      raise notice 'fracao da iptubase: %', V_FRACAO;
      raise notice 'verificando pagamentos';
      raise notice 'select iptunump inner join arrecant';
    end if;

    -- VERIFICA PAGAMENTOS
    ----raise NOTICE 'VERIFICA PAGAMENTO';
    SELECT J20_NUMPRE,MAX(K00_NUMPAR) AS K00_NUMPAR
      INTO V_NUMPRE,V_PARINI
      FROM IPTUNUMP
           INNER JOIN ARRECANT ON J20_NUMPRE = K00_NUMPRE
     WHERE J20_ANOUSU = ANOUSU
  		 AND J20_MATRIC = MATRICULA
    GROUP BY J20_NUMPRE;

    IF NOT V_NUMPRE IS NULL AND DEMO = FALSE THEN
      IF ATUALIZAP = FALSE THEN
        V_EMPAGAMENTO = TRUE;
        --RETURN '04 CARNE EM PROCESSO DE PAGAMENTO';
      END IF;
      V_TEMPAGAMENTO = TRUE;
    ELSE
      V_PARINI := 1;
    END IF;

    if v_raise is true then
      raise notice 'V_NUMPRE: % - V_PARINI: %', V_NUMPRE, V_PARINI;
    end if;

    -- REMOVE CALCULO EXISTENTE
    if v_raise is true then
      raise notice 'deletando iptucalv... iptucale... iptucalc...';
    end if;

    IF CALCULO_GERAL = FALSE AND DEMO IS FALSE THEN
      DELETE FROM IPTUCALV WHERE J21_ANOUSU = ANOUSU AND J21_MATRIC = MATRICULA;
      DELETE FROM IPTUCALE WHERE J22_ANOUSU = ANOUSU AND J22_MATRIC = MATRICULA;
      DELETE FROM IPTUCALC WHERE J23_ANOUSU = ANOUSU AND J23_MATRIC = MATRICULA;
    END IF;

    -- CALCULA VALOR DO TERRENO

    SELECT J30_ALITER, J30_ALIPRE, J34_ZONA
    INTO V_ALITER, V_ALIPRE, V_ZONA
    FROM LOTE INNER JOIN SETOR ON J34_SETOR = J30_CODI
    WHERE J34_IDBQL = V_IDBQL;

    if v_raise is true then
      raise NOTICE 'IDBQL %', V_IDBQL;
    end if;

    select j35_caract
		  into V_FORMA_TERRENO
    FROM CARLOTE inner join caracter on j31_codigo = j35_caract
    WHERE j35_idbql = V_IDBQL
      AND j31_grupo = 31;

    if V_FORMA_TERRENO is null then
      V_FORMA_TERRENO = 0;
    end if;

    V_MANUAL := V_MANUAL || 'CARACTERISTICA DO FORMATO DO IMOVEL: ' || V_FORMA_TERRENO || ' \n ';

    select coalesce(j74_fator, 1) into V_FATOR_FORMA from carfator where j74_anousu = ANOUSU and j74_caract = V_FORMA_TERRENO;

    V_MANUAL := V_MANUAL || 'FATOR DO FORMATO DO IMOVEL: ' || V_FATOR_FORMA || ' \n ';

    --
    -- TESTADA PRINCIPAL DO LOTE
    --

    SELECT J37_FACE, CASE WHEN COALESCE(J36_TESTLE, 0) = 0 THEN J36_TESTAD ELSE J36_TESTLE END AS J36_TESTLE
    INTO V_QFACE, V_TESTADA
    FROM IPTUCONSTR INNER JOIN IPTUBASE ON J01_MATRIC = J39_MATRIC
                    INNER JOIN TESTPRI ON j49_idbql = J01_IDBQL
                    INNER JOIN TESTADA ON J36_FACE = j49_face AND J36_IDBQL = J01_IDBQL
                    INNER JOIN FACE ON J37_FACE = J36_FACE
    WHERE J39_MATRIC = MATRICULA AND J39_DTDEMO IS NULL AND J01_BAIXA IS NULL LIMIT 1;

    if v_raise is true then
      raise NOTICE 'V_QFACE % ',V_QFACE;
    end if;

    select count(*) into V_QUANT_TESTADA from testada WHERE j36_idbql = V_IDBQL;

    if v_raise is true then
      raise notice 'V_QUANT_TESTADA: %', V_QUANT_TESTADA;
    end if;

    select j35_caract
      INTO  V_TEM_CARACT_ESQUINA
    from carlote
    where j35_idbql = V_IDBQL and j35_caract = 441;

    IF V_TEM_CARACT_ESQUINA IS NULL THEN
       V_TEM_CARACT_ESQUINA := 0;
    END IF;

    V_MANUAL := V_MANUAL || ' TEM ESQUINA(441) : ' || V_TEM_CARACT_ESQUINA || ' - ';

    --
    -- PEGAR ZONA
    --

    if v_raise is true then
      raise notice ' ZONA %',V_ZONA;
    end if;

    V_MANUAL := V_MANUAL || 'FRACAO: ' || V_FRACAO || ' - ';
    V_MANUAL := V_MANUAL || 'ZONA FISCAL: ' || V_ZONA || ' - ';
    V_MANUAL := V_MANUAL || 'QUANTIDADE DE TESTADA: ' || V_QUANT_TESTADA || ' \n ';

    IF V_QFACE IS NULL THEN

      SELECT J49_FACE, CASE WHEN COALESCE(J36_TESTLE, 0) = 0 THEN J36_TESTAD ELSE J36_TESTLE END AS J36_TESTLE
        INTO V_QFACE, V_TESTADA
        FROM TESTPRI INNER JOIN FACE ON J49_FACE = J37_FACE
                     INNER JOIN TESTADA ON J49_FACE = J36_FACE AND J49_IDBQL = J36_IDBQL
       WHERE J49_IDBQL = V_IDBQL;

    END IF;

    IF V_QFACE IS NULL THEN
      RETURN '06 TESTADA PRINCIPAL DO LOTE NAO CADASTRADA OU FACE NAO ENCONTRADA';
    END IF;

    select j81_valorterreno
      into V_VM2T
    from FACEVALOR
     where J81_FACE   = V_QFACE
       and J81_ANOUSU = ANOUSU;

    if not found or V_VM2T is null or V_VM2T = 0 then
       RETURN '05 VALOR DO M2 DO TERRENO NAO ENCONTRADO PARA A FACE '||V_QFACE||' !';
    end if;

    V_MANUAL := V_MANUAL || 'VALOR DO M2 DO TERRENO: ' || V_VM2T || ' - ';

    if v_raise is true then
       raise NOTICE 'V_QFACE % AREA % TESTADA %',V_QFACE,V_AREAL,V_TESTADA;
    end if;

    --VERIFICA ISENCOES
    if v_raise is true then
       raise NOTICE 'verificando isencoes';
    end if;

    SELECT J46_CODIGO, J45_TIPIS, J46_PERC,
           CASE WHEN J45_TAXAS = FALSE THEN TRUE ELSE FALSE END,
           CASE WHEN J46_AREALO IS NULL THEN 0 ELSE J46_AREALO END AS J46_AREALO
      INTO V_CODISEN, V_TIPOIS, V_ISENALIQ, V_ISENTAXAS, V_AREALO
    FROM IPTUISEN INNER JOIN ISENEXE ON J46_CODIGO = J47_CODIGO
                  INNER JOIN TIPOISEN ON J46_TIPO = J45_TIPO
    WHERE J46_MATRIC = MATRICULA AND J47_ANOUSU = ANOUSU;

    IF V_ISENALIQ IS NULL THEN
      V_ISENALIQ := 0;
    END IF;

    IF V_ISENTAXAS IS NULL THEN
      V_ISENTAXAS := TRUE;
    END IF;

    if v_raise is true then
      raise notice 'V_CODISEN: % - V_ISENTAXAS: % - V_ISENALIQ: %', V_CODISEN, V_ISENTAXAS, V_ISENALIQ;
    end if;

    -- CALCULA A PROFUNDIDADE MEDIA

    V_PROFUND_PADRAO := 30;

    V_MANUAL := V_MANUAL || 'PROFUNDIDADE PADRAO: ' || V_PROFUND_PADRAO || ' \n ';

    -- verifica caracteristica do grupo 30
    select j35_caract
		into V_CARACT_CALCULO
    FROM CARLOTE inner join caracter on j31_codigo = j35_caract
    WHERE j35_idbql = V_IDBQL
      AND j31_grupo = 30;

    if V_CARACT_CALCULO is null then
       V_CARACT_CALCULO = 0;
    end if;

    V_MANUAL := V_MANUAL || 'CARACTERISTICA DA AREA DO LOTE: ' || V_CARACT_CALCULO || ' - ';

    if v_raise is true then
      raise notice 'V_CARACT_CALCULO: %', V_CARACT_CALCULO;
    end if;

    V_AREA_TRIBUT = V_AREAL;
    V_MANUAL := V_MANUAL || 'AREA TRIBUTADA: ' || V_AREA_TRIBUT || ' \n ';

    if v_raise is true then
      raise notice 'AREA TRIBUTADA: %', V_AREA_TRIBUT;
    end if;

    if V_CARACT_CALCULO = 300 THEN
      -- calcula com a area tributada
      -- informada. os campos:
      -- iptudiversos->j80_areatrib = area tributada

      V_MANUAL := V_MANUAL || 'V_CARACT_CALCULO: ' || V_CARACT_CALCULO || ' - CALCULANDO PELA AREA TRIBUTADA ' || ' - ';

      select j80_areatrib into V_AREA_TRIBUT from iptudiversos where j80_matric = MATRICULA;

      V_MANUAL := V_MANUAL || 'AREA TRIBUTADA INFORMADA: ' || V_AREA_TRIBUT || ' \n ';

      IF V_AREA_TRIBUT IS NULL THEN
        RETURN '09 - sem dados de area tributada lancada...';
      END IF;

      if v_raise is true then
        raise notice 'AREA TRIBUTADA INFORMADA: %', V_AREA_TRIBUT;
      end if;

    end if;

    V_MANUAL := V_MANUAL || ' CALCULANDO PELA FORMULA ' || ' - ';

    V_PROFUND_MEDIA = ROUND(V_AREAL / V_TESTADA,2);
    V_MANUAL := V_MANUAL || ' PROFUNDIDADE MEDIA = AREA DO LOTE / TESTADA: ' || V_PROFUND_MEDIA || ' - ';

    if v_raise is true then
      raise notice 'PROFUNDIDADE MEDIA = AREA DO LOTE / TESTADA: %', V_PROFUND_MEDIA;
    end if;

    V_INDICE_CORRECAO = SQRT(V_PROFUND_PADRAO / V_PROFUND_MEDIA);
    V_MANUAL := V_MANUAL || ' INDICE DE CORRECAO = RAIZ QUADRADA DA (PROFUNDIDADE PADRAO / PROFUNDIDADE MEDIA): ' || V_INDICE_CORRECAO || ' \n ';

    if v_raise is true then
      raise notice 'INDICE DE CORRECAO = RAIZ QUADRADA DA (PROFUNDIDADE PADRAO / PROFUNDIDADE MEDIA): %', V_INDICE_CORRECAO;
    end if;

    if V_TEM_CARACT_ESQUINA > 0 AND V_QUANT_TESTADA > 1 then
       if V_QUANT_TESTADA = 2 then
          V_FATOR_ESQUINA := 1.1;
       elsif V_QUANT_TESTADA = 3 then
          V_FATOR_ESQUINA := 1.2;
       else
          V_FATOR_ESQUINA := 1.3;
       end if;

       V_MANUAL := V_MANUAL || ' SE QUANTIDADE DE TESTADA = ' || V_QUANT_TESTADA || ' - ';
       V_MANUAL := V_MANUAL || ' FATOR ESQUINA = ' || V_FATOR_ESQUINA || ' - ';

       if v_raise is true then
         raise notice 'QUANTIDADE DE TESTADA: %', V_QUANT_TESTADA;
         raise notice 'FATOR ESQUINA: %', V_FATOR_ESQUINA;
       end if;
    else
       V_FATOR_ESQUINA := 1;
       if V_QUANT_TESTADA > 1 then
          V_FATOR_ESQUINA := 1.1;
       end if;

       V_MANUAL := V_MANUAL || ' SE QUANTIDADE DE TESTADA = ' || V_QUANT_TESTADA || ' - ';
       V_MANUAL := V_MANUAL || ' FATOR ESQUINA = ' || V_FATOR_ESQUINA || ' - ';

       if v_raise is true then
         raise notice 'QUANTIDADE DE TESTADA: %', V_QUANT_TESTADA;
         raise notice 'FATOR ESQUINA: %', V_FATOR_ESQUINA;
       end if;
    end if;

    V_AREA_TRIBUT = ROUND(V_AREA_TRIBUT * (V_FRACAO / 100),2);
    V_MANUAL := V_MANUAL || 'AREA TRIBUTADA = AREA TRIBUTADA * FRACAO: ' || V_AREA_TRIBUT || '\n';

    if v_raise is true then
      raise notice 'AREA TRIBUTADA = AREA TRIBUTADA * FRACAO: %', V_AREA_TRIBUT;
    end if;

    -- VERIFICA SE LOTE EH GLEBA

    select j35_caract
		  into V_TEM_CARACT_GLEBA
    FROM CARLOTE inner join caracter on j31_codigo = j35_caract
    WHERE j35_idbql = V_IDBQL
      AND j31_grupo = 93;

    IF V_TEM_CARACT_GLEBA IS NULL THEN
       V_TEM_CARACT_GLEBA := 5545;
    END IF;

    V_MANUAL := V_MANUAL || ' CARACTERISTICA GLEBA: ' || V_TEM_CARACT_GLEBA || ' - ';

    if v_raise is true then
      raise notice 'CARACTERISTICA GLEBA: %', V_TEM_CARACT_GLEBA;
    end if;

    if V_TEM_CARACT_GLEBA = 5544 then

       select j71_quantfim, ((V_AREAL - j71_ini) * j71_valor), j71_valor
         into V_INDICE_GLEBA, V_AREAL_GLEBA, V_FATOR_GLEBA
       from carvalor
       where j71_anousu = ANOUSU
         and j71_caract = V_TEM_CARACT_GLEBA
         and V_AREAL between j71_ini and j71_fim ;

       if not found then
          return '37 - AREA NAO ENCONTRADA PARA A MATRICULA '|| MATRICULA;
       end if;

       if v_raise is true then
         raise notice 'V_INDICE_GLEBA: % - V_AREAL_GLEBA: % - V_FATOR_GLEBA: %', V_INDICE_GLEBA, V_AREAL_GLEBA, V_FATOR_GLEBA;
       end if;

       select coalesce(sum(j71_quantini), 0)
         into V_AREAL_GLEBA_DEMAIS
       from carvalor
       where j71_anousu = ANOUSU
         and j71_caract = V_TEM_CARACT_GLEBA
         and j71_quantfim < V_INDICE_GLEBA ;

       if found then
          V_AREAL_GLEBA := ( V_AREAL_GLEBA + V_AREAL_GLEBA_DEMAIS );

          if v_raise is true then
            raise notice 'V_AREAL_GLEBA_DEMAIS: % - NOVO V_AREAL_GLEBA: %', V_AREAL_GLEBA_DEMAIS, V_AREAL_GLEBA;
          end if;
       end if;

       V_AREA_TRIBUT := round(V_AREAL_GLEBA, 2);

       IF V_AREA_TRIBUT IS NULL OR V_AREA_TRIBUT = 0 THEN
         RETURN '36 - AREA TRIBUTADA ZERADA PARA GLEBA - MATRICULA: ' || MATRICULA;
       END IF;

    end if;

    SELECT coalesce(sum(j39_area), 0)
      INTO V_TOTMATRICULA
    FROM IPTUCONSTR
    WHERE J39_MATRIC = MATRICULA
      AND J39_DTDEMO IS NULL ;

    if v_raise is true then
       raise notice 'AREA TOTAL CONSTRUIDA DA MATRICULA: %', V_TOTMATRICULA;
    end if;

    if V_TOTCONSTR is null or V_TOTCONSTR = 0 then
       V_TOTCONSTR = 1;
    end if;

    if v_raise is true then
       raise notice 'AREA TOTAL CONSTRUIDA DO LOTE: %', V_TOTCONSTR;
    end if;

    if V_CARACT_CALCULO = 300 OR V_TEM_CARACT_GLEBA = 5544 THEN
       V_FATOR_HOMOGEINI := 1;
    elsif V_CARACT_CALCULO = 301 AND V_AREAL < 10000 AND (V_TOTMATRICULA / V_TOTCONSTR) <> 1 THEN
       V_FATOR_HOMOGEINI := ( V_INDICE_CORRECAO * V_FATOR_FORMA * V_FATOR_ESQUINA * V_FATOR_UTIL_LOTE * V_FATOR_NIVEL_RUA );
       V_FATOR_HOMOGEINI := ( V_FATOR_HOMOGEINI * V_FATOR_TOPOGRAFIA * V_FATOR_PEDOLOGIA );

       IF V_FATOR_HOMOGEINI IS NULL OR V_FATOR_HOMOGEINI = 0 THEN
         RETURN '37 - FATOR HOMOGEINIZACAO ZERADO MATRICULA: ' || MATRICULA;
       END IF;
    end if;

    V_MANUAL := V_MANUAL || ' FATOR HOMOGENEIZACAO = ' || V_FATOR_HOMOGEINI || ' - ';

    if v_raise is true then
      raise notice 'FATOR HOMOGENEIZACAO: %', V_FATOR_HOMOGEINI;
    end if;

    --====================================================================================

    V_VVT = round(V_AREA_TRIBUT * V_VM2T * V_FATOR_HOMOGEINI, 2);
    V_AREA_TRIBUT = round(V_AREA_TRIBUT * V_FATOR_HOMOGEINI, 2);

    V_MANUAL := V_MANUAL || 'TESTADA TRIBUTADA: ' || V_TESTADA || ' METROS\n';
    V_MANUAL := V_MANUAL || 'VALOR VENAL DO TERRENO: AREA TRIBUTADA * VALOR DO METRO QUADRADO DO TERRENO ' || V_VVT || '\n';

    SELECT DISTINCT I02_VALOR
      INTO V_VALINFLATOR
    FROM CFIPTU INNER JOIN INFLA ON J18_INFLA = I02_CODIGO
    WHERE CFIPTU.J18_ANOUSU = ANOUSU
    AND DATE_PART('y',I02_DATA) = ANOUSU;

    IF V_VALINFLATOR IS NULL THEN
      V_VALINFLATOR = 1;
    END IF;

    if v_raise is true then
      raise NOTICE 'V_VALINFLATOR: % ', V_VALINFLATOR;
    end if;

    if v_raise is true then
      raise NOTICE 'VALOR VENAL TERRENO % - f % ',V_VVT,v_fracao;
    end if;

    -- CALCULA EDIFICACOES
    V_TIPOIMP = 'T';

    FOR R_CONSTR IN SELECT *
                    FROM IPTUCONSTR
                    WHERE J39_MATRIC = MATRICULA
                    AND J39_DTDEMO IS NULL
    LOOP

      V_TIPOI := 1;
      V_CARAC := '';
      V_PONTOS:= 0;
      V_ANOCONSTRUCAO = ANOUSU - R_CONSTR.J39_ANO;
      --       comentado para depois acertar ANOUSU
      if v_raise is true then
        raise notice ' ';
        raise notice 'construcao %', R_CONSTR.J39_IDCONS;
      end if;
      V_TIPOIMP = 'P';

      V_MANUAL := V_MANUAL || 'EDIFICACAO ' || LPAD(R_CONSTR.j39_idcons,3,'0') || ' - LISTA DE CARACTERISTICAS PONTOS\n';

      FOR R_IDCONSTR IN SELECT J48_CARACT,J31_PONTOS::FLOAT8, J31_GRUPO, J31_DESCR, J32_DESCR
                        FROM CARCONSTR INNER JOIN CARACTER ON J48_CARACT = J31_CODIGO
                                       INNER JOIN CARGRUP  ON J31_GRUPO = J32_GRUPO
                        WHERE J48_MATRIC = R_CONSTR.J39_MATRIC AND J48_IDCONS = R_CONSTR.J39_IDCONS
      LOOP

        V_DESCONTOP = 0;
        V_CARAC := V_CARAC || TO_CHAR(R_IDCONSTR.J48_CARACT,'9999');
        V_PONTOS := V_PONTOS + R_IDCONSTR.J31_PONTOS;

        V_MANUAL := V_MANUAL || RPAD(UPPER(R_IDCONSTR.j32_descr),30,'_') || '\t' || RPAD(R_IDCONSTR.j48_caract,3) || ' ' || RPAD(RTRIM(R_IDCONSTR.j31_descr),25,'_') || '\t' || LPAD(R_IDCONSTR.j31_pontos,3) || '\n';

        IF R_IDCONSTR.J31_GRUPO = 20 THEN
          V_PONTUACAO = R_IDCONSTR.J48_CARACT;
        END IF;

        IF R_IDCONSTR.J31_GRUPO = 6 THEN
          -- Alterado lógica para manter a caracteristica com maior valor
          select case 
                 when j72_valor > V_TAXALIXOVAL
                 then 
                   j72_caract 
                 else
                   V_TAXALIXOCAR
                 end,
                 case 
                 when j72_valor > V_TAXALIXOVAL
                 then 
                   j72_valor 
                 else
                   V_TAXALIXOVAL
                 end                 
            into V_TAXALIXOCAR,
                 V_TAXALIXOVAL
            from carzonavalor 
           where j72_anousu = anousu 
             and j72_caract = R_IDCONSTR.J48_CARACT 
             and j72_zona   = v_zona 
             and j72_tipo   = 'V';

        END IF;

        IF R_IDCONSTR.J31_GRUPO = 38 THEN
          V_SIT_CONSTR = R_IDCONSTR.J48_CARACT;
        END IF;

      END LOOP;

      V_AREAC = ROUND(R_CONSTR.J39_AREA);

      select j71_valor into V_VM2C2 from carvalor where j71_anousu = anousu and j71_caract = V_PONTUACAO;

      if v_raise is true then
        raise notice 'V_TAXALIXOCAR: % - V_TAXALIXOVAL: %', V_TAXALIXOCAR, V_TAXALIXOVAL;
      end if;

      V_AREATC := V_AREATC + V_AREAC;

      V_VVCP   := ROUND( V_VM2C2 * V_AREAC,2);

      V_VVC    := ROUND(V_VVC + V_VVCP,2)::FLOAT8;

      if v_raise is true then
        raise notice 'V_VM2C2 % - AREA: % - V_VVCP: %, V_VVC: %',V_VM2C2, R_CONSTR.J39_AREA, V_VVCP, V_VVC;
      end if;

      IF V_VM2C2 IS NULL OR V_VM2C2 = 0 THEN
        RETURN '10 VALOR M2 CONSTRUCAO ZERADA. CONSTRUCAO: ' || R_CONSTR.J39_IDCONS || ' PONTOS : ' || TO_CHAR(V_PONTOS,'999');
      END IF;

      IF DEMO IS FALSE THEN

        if v_raise is true then
          raise notice 'inserindo em IPTUCALE';
        end if;

        INSERT INTO IPTUCALE VALUES ( ANOUSU,
        MATRICULA,
        R_CONSTR.J39_IDCONS,
        R_CONSTR.J39_AREA,
        V_VM2C2,
        V_PONTOS,
        V_VVCP);
      END IF;

    END LOOP;

    if v_raise is true then
      raise notice 'area total construcao %',V_AREATC;
    end if;

    V_DESCONTOT := 0;

    IF V_TIPOI = 0 THEN
      if v_raise is true then
        raise NOTICE 'TIPO TERRITORIAL ';
      end if;
    ELSE
      if v_raise is true then
        raise NOTICE 'TIPO PREDIAL ';
      end if;
    END IF;

    SELECT CASE WHEN V_TIPOI = 0 THEN J18_RTERRI ELSE J18_RPREDI END, J18_VENCIM, J18_DTOPER, j18_vlrref
      INTO V_RECIPTU, V_VENCIM, V_DTOPER, V_BASE
    FROM CFIPTU
    WHERE J18_ANOUSU = ANOUSU;

    if v_raise is true then
      raise notice 'V_RECIPTU: %', V_RECIPTU;
    end if;

    IF V_RECIPTU IS NULL THEN
      RETURN '11 RECEITA IPTU NAO CADASTRADA';
    END IF;

    IF V_VENCIM IS NULL THEN
      RETURN '12 TABELA DE VENCIMENTO NAO CADASTRADA';
    END IF;

    if v_raise is true then
      raise notice ' TIPO %',V_TIPOI;
      raise notice ' V_ALITER: %, V_ALIPRE: %', V_ALITER, V_ALIPRE;
    end if;

    -- VERIFICA CARACTERISTICAS DO LOTE

    V_MANUAL := V_MANUAL || '\nCARACTERISTICAS DO LOTE                GRUPO\n';

    FOR R_CARLOTE IN SELECT J35_CARACT, J31_GRUPO, j31_descr, j32_descr
                     FROM CARLOTE INNER JOIN CARACTER ON J31_CODIGO = J35_CARACT
                                  INNER JOIN CARGRUP  ON J32_GRUPO = J31_GRUPO
                     WHERE J35_IDBQL = V_IDBQL
    LOOP

      IF R_CARLOTE.J31_GRUPO = 36 THEN
        V_CAR_PASSEIO = R_CARLOTE.J35_CARACT;
      end if;

      IF R_CARLOTE.J31_GRUPO = 37 THEN
        V_CAR_CERCA_MURO = R_CARLOTE.J35_CARACT;
      end if;

    END LOOP;

    V_VV := V_VVT + V_VVC;
    V_MANUAL := V_MANUAL || 'VALOR VENAL TOTAL: ' || V_VV || '\n';

    if v_raise is true then
      raise notice 'V_VV: % - V_BASE: %', V_VV, V_BASE;
    end if;

    IF V_TIPOI = 0 THEN
      SELECT 	j70_aliter
       into V_ALITER
      from zonasaliq
      where j70_zona = V_ZONA;

      V_ALIQ := V_ALITER;
      V_MANUAL := V_MANUAL || 'TERRITORIAL - ALIQUOTA: ' || V_ALIQ || '\n';
    ELSE
      IF ANOUSU > 2020 THEN
         IF V_VV <= 100000 THEN
            if v_raise is true then
               raise notice 'ate 100000';
            end if;

            V_ALIQ := 0.4;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 100000.01 AND 200000 THEN
            if v_raise is true then
               raise notice 'entre 100000.01 e 200000';
            end if;

            V_ALIQ := 0.5;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 200000.01 AND 300000 THEN
            if v_raise is true then
               raise notice 'entre 200000.01 e 300000';
            end if;

            V_ALIQ := 0.7;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 300000.01 AND 400000 THEN
            if v_raise is true then
               raise notice 'entre 300000.01 e 400000';
            end if;

            V_ALIQ := 0.75;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 400000.01 AND 500000 THEN
            if v_raise is true then
               raise notice 'entre 400000.01 e 500000';
            end if;

            V_ALIQ := 0.80;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 500000.01 AND 600000 THEN
            if v_raise is true then
               raise notice 'entre 500000.01 e 600000';
            end if;

            V_ALIQ := 0.81;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 600000.01 AND 700000 THEN
            if v_raise is true then
               raise notice 'entre 600000.01 e 700000';
            end if;

            V_ALIQ := 0.82;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 700000.01 AND 800000 THEN
            if v_raise is true then
               raise notice 'entre 700000.01 e 800000';
            end if;

            V_ALIQ := 0.83;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 800000.01 AND 900000 THEN
            if v_raise is true then
               raise notice 'entre 800000.01 e 900000';
            end if;

            V_ALIQ := 0.84;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 900000.01 AND 1000000 THEN
            if v_raise is true then
               raise notice 'entre 900000.01 e 1000000';
            end if;

            V_ALIQ := 0.85;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV > 1000000 THEN
            if v_raise is true then
               raise notice 'acima de 1000000';
            end if;

            V_ALIQ := 0.9;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         END IF;
      ELSIF ANOUSU BETWEEN 2019 AND 2020 THEN
         IF V_VV <= 100000 THEN
            if v_raise is true then
               raise notice 'ate 100000';
            end if;

            V_ALIQ := 0.4;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL ENTRE VALOR BASE (' || V_BASE || ') MULTIPLICADO POR 0.01 E VALOR BASE MULTIPLICADO POR 183.339: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 100000.01 AND 700000 THEN
            if v_raise is true then
               raise notice 'entre 100000.01 e 700000';
            end if;

            V_ALIQ := 0.45;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL ENTRE VALOR BASE (' || V_BASE || ') MULTIPLICADO POR 183.34 E VALOR BASE MULTIPLICADO POR 366.6699: ' || V_ALIQ || '\n';
         ELSIF V_VV > 700000 THEN
            if v_raise is true then
               raise notice 'acima de 700000';
            end if;

            V_ALIQ := 0.5;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL ENTRE VALOR BASE (' || V_BASE || ') MULTIPLICADO POR 366.6699 E VALOR BASE MULTIPLICADO POR 9999999999.9999: ' || V_ALIQ || '\n';
         END IF;
      ELSE
         IF V_VV BETWEEN (V_BASE * 0.0001) AND (V_BASE * 183.3399) THEN
           if v_raise is true then
             raise notice 'entre 0 e 183.3399';
           end if;
           V_ALIQ := 0.8;
           V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL ENTRE VALOR BASE (' || V_BASE || ') MULTIPLICADO POR 0.01 E VALOR BASE MULTIPLICADO POR 183.339: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN (V_BASE * 183.34) AND (V_BASE * 366.6699) THEN
           if v_raise is true then
             raise notice 'entre 183.34 e 366.6699';
           end if;
           V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL ENTRE VALOR BASE (' || V_BASE || ') MULTIPLICADO POR 183.34 E VALOR BASE MULTIPLICADO POR 366.6699: ' || V_ALIQ || '\n';
           V_ALIQ := 0.9;
         ELSIF V_VV BETWEEN (V_BASE * 366.67) AND (V_BASE * 9999999999.9999) THEN
           if v_raise is true then
             raise notice 'entre 366.67 e 9999999999.9999';
           end if;
           V_ALIQ := 1;
           V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL ENTRE VALOR BASE (' || V_BASE || ') MULTIPLICADO POR 366.6699 E VALOR BASE MULTIPLICADO POR 9999999999.9999: ' || V_ALIQ || '\n';
         END IF;
      END IF;

    END IF;

    IF V_ALIQ = 0 THEN
      RETURN '13 ALIQUOTA ZERADA';
    END IF;

    if v_raise is true then
      raise notice 'aliquota: %', V_ALIQ;
    end if;

    if v_raise is true then
      raise notice 'procurando CARZONAVALOR - V_CAR_PASSEIO: %', V_CAR_PASSEIO;
    end if;

    select j72_valor into V_FATOR
		from CARZONAVALOR
    where j72_anousu = ANOUSU
      AND j72_caract = V_CAR_PASSEIO
      AND j72_zona = V_ZONA;

    if V_FATOR is not null and 1 = 2 then
      V_ALIQ = V_ALIQ * V_FATOR;
      V_ACRESCIMO = TRUE;

      if v_raise is true then
        raise notice '   achou CARZONAVALOR - V_CAR_PASSEIO: % - FATOR: %', V_CAR_PASSEIO, V_FATOR;
      end if;

    end if;


    if V_ACRESCIMO is false then
      if v_raise is true then
        raise notice 'procurando CARZONAVALOR - V_CAR_CERCA_MURO: %', V_CAR_CERCA_MURO;
      end if;

      select j72_valor into V_FATOR
      from CARZONAVALOR
      where j72_anousu = ANOUSU
        AND j72_caract = V_CAR_CERCA_MURO
        AND j72_zona = V_ZONA;

      if V_FATOR is not null and 1 = 2 then
        V_ALIQ = V_ALIQ * V_FATOR;
        V_ACRESCIMO = TRUE;
        if v_raise is true then
          raise notice '   achou CARZONAVALOR - V_CAR_CERCA_MURO: % - V_FATOR: %', V_CAR_CERCA_MURO, V_FATOR;
        end if;
      end if;
    end if;

    if v_raise is true then
      raise notice 'procurando CARZONAVALOR - V_SIT_CONSTR: %', V_SIT_CONSTR;
    end if;

    if V_ACRESCIMO is false AND (V_SIT_CONSTR IS NOT NULL OR V_SIT_CONSTR > 0) then
      select j72_valor into V_FATOR
      from CARZONAVALOR
      where j72_anousu = ANOUSU
        AND j72_caract = V_SIT_CONSTR
        AND j72_zona = V_ZONA;

      if V_FATOR is not null and 1 = 2 then
        V_ACRESCIMO = TRUE;
        V_ALIQ = V_ALIQ * V_FATOR;
        if v_raise is true then
          raise notice '   achou CARZONAVALOR - V_SIT_CONSTR: % - V_FATOR: %', V_SIT_CONSTR, V_FATOR;
        end if;
      end if;
    end if;

    V_QLIMPEZA := 1;

    V_MANUAL := V_MANUAL || '\n';

    V_MANUAL := V_MANUAL || '\n';

    if v_raise is true then
      raise notice 'IPTU BRUTO %',V_IPTU;
    end if;

    V_IPTU := V_IPTU::float8 - V_VLRISEN::float8;

    V_MANUAL := V_MANUAL || 'VALOR VENAL TOTAL: ' || V_VV || '\n';

    if v_raise is true then
      raise notice 'VVT %,VVC % ',v_vvt, v_vvc;
    end if;

    V_MANUAL := V_MANUAL || 'VALOR VENAL EDIFICACOES: ' || V_VVC || ' - ';

    if v_raise is true then
      raise notice 'ALIQUOTA: %', V_ALIQ;
    end if;

    V_IPTU := ROUND((( V_VVT::float8 + V_VVC::float8 ) * ( V_ALIQ::float8 / 100::float8))::numeric,2)::float8;

    V_MANUAL := V_MANUAL || 'VALOR DO IPTU: ' || V_IPTU || ' - ';

    V_VLRISEN := ROUND((V_IPTU::float8 * ( V_ISENALIQ::float8 / 100::float8))::numeric,2);

    -- VERIFICA ENQUADRAMENTO NA LEI
    -- PARA ISENCAO DE IPTU

    SELECT j46_codigo into V_TESTAISEN
    FROM IPTUISEN INNER JOIN ISENEXE on ISENEXE.j47_codigo = IPTUISEN.j46_codigo
    WHERE IPTUISEN.j46_matric = MATRICULA
      and IPTUISEN.j46_tipo = 1010 and ISENEXE.j47_anousu = ANOUSU;

    if V_TESTAISEN is not null then
       DELETE FROM ISENEXE
       USING IPTUISEN
       WHERE ISENEXE.j47_codigo = IPTUISEN.j46_codigo
         and IPTUISEN.j46_matric = MATRICULA
         and ISENEXE.j47_anousu = ANOUSU
         and IPTUISEN.j46_tipo = 1010;

      delete from IPTUISEN using ISENEXE
      where IPTUISEN.j46_codigo = V_TESTAISEN;

      if v_raise is true then
        raise notice 'deletando registro da IPTUISEN e ISENEXE';
      end if;
    end if;

    if V_TIPOI = 1 then

       V_INDICE_MULTI = 56.24;
   
       select j41_numcgm into V_CGMPRI from promitente where j41_matric = MATRICULA and j41_tipopro is true limit 1;
   
       if V_CGMPRI is null then -- nao tem promitente nessa matricula
         V_CGMPRI =  V_NUMCGM;
       end if;
   
       if v_raise is true then
         raise notice 'Buscando imoveis do cgm %', V_CGMPRI;
       end if;
   
       FOR R_PROPRIS IN
   
         select * from (
         select distinct
   			j01_matric,
   			j01_numcgm,
   			1 as j01_tipo
   			from iptubase
   			left join promitente on j41_matric = j01_matric
   			where j01_baixa is null     and
			      j01_numcgm = V_CGMPRI and
   			      j41_matric is null    and 
			      j01_matric < 90000
         union
         select distinct
   			j42_matric,
   			j42_numcgm,
   			2 as j01_tipo
   			from propri
			inner join iptubase  on j42_matric = j01_matric  
   			left join promitente on j41_matric = j42_matric
   			where j01_baixa is null     and
			      j42_numcgm = V_CGMPRI and
   			      j41_matric is null    and
			      j01_matric < 90000
         union
         select
   			distinct
   			j41_matric,
   			j41_numcgm,
   			3 as j01_tipo
   			from promitente
			inner join iptubase on j41_matric = j01_matric
   			where j01_baixa is null and 
			      j41_numcgm = V_CGMPRI and
		              j01_matric < 90000) as x
         order by j01_tipo desc
         LOOP
   
   			if v_raise is true then
   				raise notice 'tipo %', R_PROPRIS.j01_tipo;
   			end if;
   
         if R_PROPRIS.j01_tipo = 3 then
           if V_CGMPRI = R_PROPRIS.j01_numcgm then
             V_QUANT_IMOVEIS = V_QUANT_IMOVEIS + 1;
           end if;
         else
           if V_CGMPRI = R_PROPRIS.j01_numcgm then
             V_QUANT_IMOVEIS = V_QUANT_IMOVEIS + 1;
           end if;
         end if;
   
       END LOOP;
   
       if v_raise is true then
         raise notice 'V_QUANT_IMOVEIS: % - V_CGMPRI: %', V_QUANT_IMOVEIS, V_CGMPRI;
       end if;
   
       IF V_QUANT_IMOVEIS IS NULL THEN
         V_QUANT_IMOVEIS = 0;
       END IF;
   
       if V_VV <= (V_BASE * V_INDICE_MULTI) and V_QUANT_IMOVEIS = 1 then
          V_VLRISEN = V_IPTU;
          V_ISENALIQ = 100;
   
          SELECT J46_CODIGO into V_TESTAISEN
          FROM IPTUISEN
          INNER JOIN ISENEXE on ISENEXE.j47_codigo = IPTUISEN.j46_codigo
          WHERE IPTUISEN.j46_matric = MATRICULA
            and IPTUISEN.j46_tipo = 1010 and ISENEXE.j47_anousu = anousu;
   
          IF V_TESTAISEN IS NULL THEN
   
            select nextval('iptuisen_j46_codigo_seq') INTO V_CODISEN;
   
            V_DIAINI = TO_CHAR(ANOUSU, '9999') || '-01-01';
            V_DIAFIM = TO_CHAR(ANOUSU, '9999') || '-12-31';
   
            if v_raise is true then
              raise notice 'V_DIAINI: % - V_DIAFIM: %', V_DIAINI, V_DIAFIM;
            end if;
   
            V_CODTIPOISEN = 1010;
   
            if v_raise is true then
              raise notice 'inserindo no iptuisen j46_codigo: %', V_CODISEN;
            end if;
   
            insert into IPTUISEN (j46_codigo, j46_matric, j46_tipo, j46_dtini, j46_dtfim, j46_perc, j46_dtinc, j46_idusu, j46_hist, j46_arealo)
            values
            (V_CODISEN, MATRICULA, V_CODTIPOISEN, V_DIAINI::date, V_DIAFIM::date, 100, now(),1,'ISENTO CFE. LEI 6333 DE 23.11.2021 (CALCULO)',0);
   
            INSERT INTO ISENEXE (j47_codigo, j47_anousu) values (V_CODISEN, ANOUSU);
   
          END IF;
   
       END IF;
    END IF;

    V_MANUAL := V_MANUAL || 'VALOR DA ISENCAO: ' || V_VLRISEN || ' \n ';

    if v_raise is true then
      raise notice 'IPTU: % - ISENCAO: %',V_IPTU,V_VLRISEN;
    end if;

    if v_raise is true then
      raise NOTICE 'VALOR VT % VC % VALOR TOTAL % ',V_VVT,V_VVC,V_VV ;
    end if;

    IF DEMO IS FALSE THEN

      if v_raise is true then
        raise notice 'inserindo em IPTUCALC';
      end if;

      INSERT INTO IPTUCALC VALUES ( ANOUSU,
      MATRICULA,
      V_TESTADA,
      V_AREA_TRIBUT,
      V_FRACAO,
      V_AREATC,
      V_VM2T,
      V_VVT,
      V_ALIQ,
      ROUND(V_VLRISEN,2),
      V_TIPOIMP );

		select j18_iptuhistisen
		  into iHistIptuIsen
		  from cfiptu
		 where j18_anousu = anousu;
		if iHistIptuIsen is null then
			return '18 PARAMETRO DE CONFIGURACAO DO HISTORICO DE ISENCAO DO IPTU NAO CONFIGURADO';
		end if;

      if V_IPTU > 0 then
        -- registra valor iptu
        INSERT INTO IPTUCALV 	(
        j21_anousu,
        j21_matric,
        j21_receit,
        j21_valor,
        j21_quant,
        j21_codhis
        )
        VALUES (ANOUSU,
        MATRICULA,
        V_RECIPTU,
        ROUND(V_IPTU,2),
        V_ALIQ,
        1);
      end if;

      IF V_VLRISEN > 0 THEN
				-- registra valor isencoes
				INSERT INTO IPTUCALV 	(
				j21_anousu,
				j21_matric,
				j21_receit,
				j21_valor,
				j21_quant,
				j21_codhis
				)
				VALUES (
				ANOUSU,
				MATRICULA,
				V_RECIPTU,
				ROUND(V_VLRISEN*-1,2),
				0,
				iHistIptuIsen);
			END IF;

    END IF;

    V_IPTU := V_IPTU - V_VLRISEN;

    -- VERIFICA PARCELAS
    SELECT COUNT(*)
    INTO V_PARCELAS
    FROM CADVENCDESC
    INNER JOIN CADVENC ON Q92_CODIGO = Q82_CODIGO
    WHERE Q92_CODIGO = V_VENCIM ;

    IF V_PARCELAS IS NULL OR V_PARCELAS = 0 THEN
      RETURN '14 PARCELAS NAO CADASTRADAS ';
    END IF;

    if v_raise is true then
      raise notice 'isentaxa: %', V_ISENTAXAS;
    end if;

    IF V_ISENTAXAS = TRUE THEN

      if v_raise is true then
        raise notice 'acessou isentaxas... ';
      end if;

      FOR R_TAXA IN SELECT *
        FROM IPTUTAXA
				     inner join iptucadtaxaexe on j08_tabrec = J19_RECEIT
				                              and j08_anousu = ANOUSU
             LEFT OUTER JOIN ISENTAXA ON J56_CODIGO = V_CODISEN AND J56_RECEIT = J19_RECEIT
        WHERE J19_ANOUSU = ANOUSU
		  LOOP

        if v_raise is true then
          raise notice 'inserindo em IPTUCALC... taxas...';
        end if;

        IF R_TAXA.J19_RECEIT = 13 THEN
          -- registra valor lixo
          V_QUATX  := V_QUATX + 1;
          V_TAXA1  := V_TAXALIXOVAL * V_PARCELAS;
          V_RECTX1 := R_TAXA.J19_RECEIT;
          if v_raise is true then
            raise notice 'rec lixo %',v_taxa1;
          end if;
          IF R_TAXA.J56_PERC != 0 THEN
            if v_raise is true then
              raise notice 'rec taxa1%',v_taxa1;
            end if;
	  				nValIsenTaxa1 := V_TAXA1 - (V_TAXA1 * ( ( 100::FLOAT8-(R_TAXA.J56_PERC) )/100::FLOAT8 ));
--            V_TAXA1 := V_TAXA1 * ((100::FLOAT8-(R_TAXA.J56_PERC))/100::FLOAT8);
            IF V_TAXA1 = 0 OR V_TAXA1 < 0 THEN
              V_TAXA1 = 0;
            END IF;
            if v_raise is true then
              raise notice 'rec taxa1%',v_taxa1;
            end if;
          END IF;

          IF DEMO IS FALSE THEN

            if V_TAXA1 > 0 then
              INSERT INTO IPTUCALV	(
              j21_anousu,
              j21_matric,
              j21_receit,
              j21_valor,
              j21_quant,
              j21_codhis
              )
              VALUES (ANOUSU,
              MATRICULA,
              V_RECTX1,
              ROUND(V_TAXA1,2),
              V_QUATX,
              R_TAXA.j08_iptucalh);
            end if;

						if nValIsenTaxa1 > 0 then
							INSERT INTO IPTUCALV	(	j21_anousu,
																			j21_matric,
																			j21_receit,
																			j21_valor,
																			j21_quant,
																			j21_codhis )
              							 VALUES ( ANOUSU,
																			MATRICULA,
																			V_RECTX1,
																			ROUND((nValIsenTaxa1*-1),2),
																			V_QLIMPEZA,
																			R_TAXA.j08_histisen);
						end if;

          END IF;

        END IF;

      END LOOP;

    END IF;

    -- GERA FINANCEIRO
    IF GERAFINANC = TRUE THEN
      --daa =  current_time;
      if v_raise is true then
        raise notice 'gerando financeiro...';
      end if;

      -- VERIFICA CODIGO DE ARRECADACAO
      SELECT J20_NUMPRE
      INTO V_NUMPRE
      FROM IPTUNUMP
      WHERE J20_ANOUSU = ANOUSU AND J20_MATRIC = MATRICULA;

      IF NOT V_NUMPRE IS NULL THEN
        IF CALCULO_GERAL = FALSE AND DEMO IS FALSE THEN
          if v_raise is true then
            raise notice 'deletando arrebanco, arrematric e arrecad...';
          end if;

          if v_raise is true then
            raise notice ' ';
            raise notice ' ';
            raise notice ' ';
          end if;

          FOR V_RECORD_ARRECAD IN SELECT distinct K00_NUMPAR FROM ARRECAD WHERE K00_NUMPRE = V_NUMPRE ORDER BY k00_numpar LOOP

            if v_raise is true then
              raise notice 'processando parcela: %', V_RECORD_ARRECAD.K00_NUMPAR;
            end if;
						IF PARCELAFIM > 0 THEN
							IF V_RECORD_ARRECAD.K00_NUMPAR >= PARCELAINI AND V_RECORD_ARRECAD.K00_NUMPAR <= PARCELAFIM THEN
								if v_raise is true then
									raise notice '   deletando do arrecad parcela: %', V_RECORD_ARRECAD.K00_NUMPAR;
								end if;
								DELETE FROM ARRECAD WHERE K00_NUMPRE = V_NUMPRE AND K00_NUMPAR = V_RECORD_ARRECAD.K00_NUMPAR;
							END IF;
						ELSE
							IF V_RECORD_ARRECAD.K00_NUMPAR >= PARCELAINI THEN
								if v_raise is true then
									raise notice '   deletando do arrecad parcela: %', V_RECORD_ARRECAD.K00_NUMPAR;
								end if;
								DELETE FROM ARRECAD WHERE K00_NUMPRE = V_NUMPRE AND K00_NUMPAR = V_RECORD_ARRECAD.K00_NUMPAR;
							END IF;
            END IF;

          END LOOP;

          if v_raise is true then
            raise notice ' ';
            raise notice ' ';
            raise notice ' ';
          end if;

        END IF;

        IF NOVONUMPRE = FALSE THEN
          V_MESMONUMPRE = TRUE;
        ELSE
          IF V_TEMPAGAMENTO = FALSE THEN
            IF CALCULO_GERAL = FALSE AND DEMO IS FALSE THEN
              if v_raise is true then
                raise notice 'deletando iptunump...';
              end if;
              DELETE FROM IPTUNUMP WHERE J20_ANOUSU = ANOUSU AND J20_MATRIC = MATRICULA;
            END IF;
            IF DEMO IS FALSE THEN
              SELECT NEXTVAL('numpref_k03_numpre_seq')::INTEGER
              INTO V_NUMPRE;
            END IF;
          END IF;
        END IF;
      ELSE
        IF DEMO IS FALSE THEN
          SELECT NEXTVAL('numpref_k03_numpre_seq')::INTEGER
          INTO V_NUMPRE;
        END IF;
      END IF;
      -- se imune sai
      IF NOT V_TIPOIS IS NULL THEN
        IF V_TIPOIS = 1 THEN
          RETURN '15 MATRICULA IMUNE';
        END IF;
      END IF;
      -- verifica taxas
      V_SOMA := 0;

      if v_raise is true then
        raise notice 'antes dos vencimentos';
      end if;

      V_MANUAL := V_MANUAL || 'PARCELA INICIAL BASEADA NOS PAGAMENTOS: ' || V_PARINI || ' \n ';

      V_TEXT = 'SELECT *
      FROM CADVENCDESC
      INNER JOIN CADVENC        	ON Q92_CODIGO = Q82_CODIGO
      LEFT  JOIN CADVENCDESCBAN 	ON Q93_CODIGO = Q92_CODIGO
      LEFT  JOIN CADBAN		ON K15_CODIGO = Q93_CADBAN
      WHERE Q92_CODIGO = ' || V_VENCIM || ' ORDER BY Q82_PARC';

      if v_raise is true then
        raise notice 'V_TEXT: %', V_TEXT;
      end if;

      V_PASSA = TRUE;

      FOR R_VENCIM IN EXECUTE V_TEXT LOOP
        --daa = current_time;
        if v_raise is true then
          raise notice 'gerando parcelas % pelo cadvencdesc - venc: %',R_VENCIM.Q82_PARC, R_VENCIM.Q82_VENC;
        end if;
        -- VERIFICA SE PARCELA INICIAL ESTA CERTA
        if v_raise is true then
          raise NOTICE 'INICIA % PARCELA % ',V_PARINI,R_VENCIM.Q82_PARC;
        end if;

        lProcessarArrecad := true;

        --
        -- Se parcela ja esta paga ou cancelada passa para a proxima
        --

        if V_NUMPRE is not null then
          perform * from fc_statusdebitos(V_NUMPRE,R_VENCIM.Q82_PARC) where rtstatus = 'PAGO' or rtstatus = 'CANLELADO' limit 1;
          if found then
            lProcessarArrecad := false;
          end if;
        end if;

        IF PARCELAINI = 0 THEN
          V_PASSA = TRUE;
        ELSE
          IF R_VENCIM.Q82_PARC >= PARCELAINI AND R_VENCIM.Q82_PARC <= PARCELAFIM THEN
            V_PASSA = TRUE;
          ELSE
            V_PASSA = FALSE;
          END IF;
        END IF;

        if v_raise is true then
          raise notice 'processando parcela: % - V_PASSA: %', R_VENCIM.Q82_PARC, V_PASSA;
        end if;

        IF V_PASSA IS TRUE THEN

          if v_raise is true then
            raise notice 'V_IPTU: % - V_TAXA1: % - V_TAXA2: %', V_IPTU, V_TAXA1, V_TAXA2;
          end if;
          IF V_IPTU > 0 THEN
            V_VALORPAR := ROUND(V_IPTU * ( R_VENCIM.Q82_PERC / 100),2)::FLOAT8;
            if V_MOSTRAR IS TRUE THEN
              if v_raise is true then
                raise notice 'soma % vparc % parcela % v_parcelas %',v_soma,v_valorpar,r_vencim.q82_parc, V_PARCELAS;
              end if;
            end if;
            IF V_PARCELAS = R_VENCIM.Q82_PARC THEN
              V_VALORPAR := ROUND(V_IPTU - V_DIFIPTU - V_SOMA,2)::FLOAT8;
            END IF;
            V_SOMA := V_SOMA + V_VALORPAR;
            V_TEMFINANC = TRUE;
            V_DIGITO := FC_DIGITO(V_NUMPRE,R_VENCIM.Q82_PARC,V_PARCELAS);
            IF DEMO IS FALSE THEN
              if V_MOSTRAR IS TRUE THEN
                if v_raise is true then
                  raise notice 'CGM: % - DTOPER: % - REC: % - HIST: %',V_NUMCGM,V_DTOPER,V_RECIPTU,R_VENCIM.Q82_HIST;
                  raise notice 'VALOR: % - VENC: % - NUMPRE: % - PARC: %',V_VALORPAR,R_VENCIM.Q82_VENC,V_NUMPRE,R_VENCIM.Q82_PARC;
                  raise notice 'PARC: % - DIGITO: % - TIPO: %',V_PARCELAS,V_DIGITO,R_VENCIM.Q92_TIPO;
                end if;
              end if;

              if lProcessarArrecad then
                 INSERT INTO ARRECAD (K00_NUMCGM,K00_DTOPER,K00_RECEIT,K00_HIST,K00_VALOR,K00_DTVENC,K00_NUMPRE,K00_NUMPAR,K00_NUMTOT,K00_NUMDIG,K00_TIPO)
                 VALUES( V_NUMCGM,
                 V_DTOPER,
                 V_RECIPTU,
                 R_VENCIM.Q82_HIST,
                 V_VALORPAR,
                 R_VENCIM.Q82_VENC,
                 V_NUMPRE,
                 R_VENCIM.Q82_PARC,
                 V_PARCELAS,
                 V_DIGITO,
                 R_VENCIM.Q92_TIPO);
              end if;
            END IF;
            V_TOTALZAO = V_TOTALZAO + V_VALORPAR;
          END IF;
          IF V_TAXA1 > 0 THEN
            V_VALORTX1 := ROUND( (V_TAXA1-coalesce(nValIsenTaxa1,0) ) *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            IF V_PARCELAS = R_VENCIM.Q82_PARC THEN
              V_VALORTX1 := ROUND(V_TAXA1 - V_DIFTX1 - V_SOMATX1 - coalesce(nValIsenTaxa1,0),2)::FLOAT8;
            END IF;
            V_SOMATX1 := V_SOMATX1 + V_VALORTX1;

            V_TEMFINANC = TRUE;
            IF DEMO IS FALSE THEN
              if V_MOSTRAR IS TRUE THEN
                if v_raise is true then
                  raise notice 'CGM: % - DTOPER: % - REC: % - HIST: % - VALOR: % - VENC: % - NUMPRE: % - PARC: % - PARC: % - DIGITO: % - TIPO: %',V_NUMCGM,V_DTOPER,V_RECTX1,R_VENCIM.Q82_HIST,V_VALORTX1,R_VENCIM.Q82_VENC,V_NUMPRE,R_VENCIM.Q82_PARC,V_PARCELAS,V_DIGITO,R_VENCIM.Q92_TIPO;
                end if;
              end if;
              if lProcessarArrecad then
                INSERT INTO ARRECAD (K00_NUMCGM,K00_DTOPER,K00_RECEIT,K00_HIST,K00_VALOR,K00_DTVENC,K00_NUMPRE,K00_NUMPAR,K00_NUMTOT,K00_NUMDIG,K00_TIPO)
                VALUES(V_NUMCGM,
                 V_DTOPER,
                 V_RECTX1,
                 R_VENCIM.Q82_HIST,
                 V_VALORTX1,
                 R_VENCIM.Q82_VENC,
                 V_NUMPRE,
                 R_VENCIM.Q82_PARC,
                 V_PARCELAS,
                 V_DIGITO,
                 R_VENCIM.Q92_TIPO);
              end if;
            END IF;
            V_TOTALZAO = V_TOTALZAO + V_VALORTX1;
          END IF;
          IF V_TAXA2 > 0 THEN
            V_VALORTX2 := ROUND(V_TAXA2 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            IF V_PARCELAS = R_VENCIM.Q82_PARC THEN
              V_VALORTX2 := ROUND(V_TAXA2 - V_DIFTX2 - V_SOMATX2,2)::FLOAT8;
            END IF;
            V_SOMATX2 := V_SOMATX2 + V_VALORTX2;

            V_TEMFINANC = TRUE;
            IF DEMO IS FALSE THEN
              if V_MOSTRAR IS TRUE THEN
                if v_raise is true then
                  raise notice 'CGM: % - DTOPER: % - REC: % - HIST: % - VALOR: % - VENC: % - NUMPRE: % - PARC: % - PARC: % - DIGITO: % - TIPO: %',V_NUMCGM,V_DTOPER,V_RECTX2,R_VENCIM.Q82_HIST,V_VALORTX2,R_VENCIM.Q82_VENC,V_NUMPRE,R_VENCIM.Q82_PARC,V_PARCELAS,V_DIGITO,R_VENCIM.Q92_TIPO;
                end if;
              end if;
              if lProcessarArrecad then
                INSERT INTO ARRECAD (K00_NUMCGM,K00_DTOPER,K00_RECEIT,K00_HIST,K00_VALOR,K00_DTVENC,K00_NUMPRE,K00_NUMPAR,K00_NUMTOT,K00_NUMDIG,K00_TIPO)
                VALUES(V_NUMCGM,
                V_DTOPER,
                V_RECTX2,
                R_VENCIM.Q82_HIST,
                V_VALORTX2,
                R_VENCIM.Q82_VENC,
                V_NUMPRE,
                R_VENCIM.Q82_PARC,
                V_PARCELAS,
                V_DIGITO,
                R_VENCIM.Q92_TIPO);
              end if;
            END IF;
            V_TOTALZAO = V_TOTALZAO + V_VALORTX2;
          END IF;
          IF V_TAXA3 > 0 THEN
            V_VALORTX3 := ROUND(V_TAXA3 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            IF V_PARCELAS = R_VENCIM.Q82_PARC THEN
              V_VALORTX3 := ROUND(V_TAXA3 - V_DIFTX3 - V_SOMATX3,2)::FLOAT8;
            END IF;
            V_SOMATX3 := V_SOMATX3 + V_VALORTX3;

            V_TEMFINANC = TRUE;
            IF DEMO IS FALSE THEN
              if V_MOSTRAR IS TRUE THEN
                if v_raise is true then
                  raise notice 'CGM: % - DTOPER: % - REC: % - HIST: % - VALOR: % - VENC: % - NUMPRE: % - PARC: % - PARC: % - DIGITO: % - TIPO: %',V_NUMCGM,V_DTOPER,V_RECTX3,R_VENCIM.Q82_HIST,V_VALORTX3,R_VENCIM.Q82_VENC,V_NUMPRE,R_VENCIM.Q82_PARC,V_PARCELAS,V_DIGITO,R_VENCIM.Q92_TIPO;
                end if;
              end if;
              if lProcessarArrecad then
                 INSERT INTO ARRECAD (K00_NUMCGM,K00_DTOPER,K00_RECEIT,K00_HIST,K00_VALOR,K00_DTVENC,K00_NUMPRE,K00_NUMPAR,K00_NUMTOT,K00_NUMDIG,K00_TIPO)
                 VALUES (V_NUMCGM,
                 V_DTOPER,
                 V_RECTX3,
                 R_VENCIM.Q82_HIST,
                 V_VALORTX3,
                 R_VENCIM.Q82_VENC,
                 V_NUMPRE,
                 R_VENCIM.Q82_PARC,
                 V_PARCELAS,
                 V_DIGITO,
                 R_VENCIM.Q92_TIPO);
              end if;
            END IF;
            V_TOTALZAO = V_TOTALZAO + V_VALORTX3;
          END IF;
          IF V_TAXA4 > 0 THEN
            V_VALORTX4 := ROUND(V_TAXA4 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            IF V_PARCELAS = R_VENCIM.Q82_PARC THEN
              V_VALORTX4 := ROUND(V_TAXA4 - V_DIFTX4 - V_SOMATX4,2)::FLOAT8;
            END IF;
            V_SOMATX4 := V_SOMATX4 + V_VALORTX4;

            V_TEMFINANC = TRUE;
            IF DEMO IS FALSE THEN
              if V_MOSTRAR IS TRUE THEN
                if v_raise is true then
                  raise notice 'CGM: % - DTOPER: % - REC: % - HIST: % - VALOR: % - VENC: % - NUMPRE: % - PARC: % - PARC: % - DIGITO: % - TIPO: %',V_NUMCGM,V_DTOPER,V_RECTX4,R_VENCIM.Q82_HIST,V_VALORTX4,R_VENCIM.Q82_VENC,V_NUMPRE,R_VENCIM.Q82_PARC,V_PARCELAS,V_DIGITO,R_VENCIM.Q92_TIPO;
                end if;
              end if;
              if lProcessarArrecad then
                 INSERT INTO ARRECAD (K00_NUMCGM,K00_DTOPER,K00_RECEIT,K00_HIST,K00_VALOR,K00_DTVENC,K00_NUMPRE,K00_NUMPAR,K00_NUMTOT,K00_NUMDIG,K00_TIPO)
                 VALUES (V_NUMCGM,
                 V_DTOPER,
                 V_RECTX4,
                 R_VENCIM.Q82_HIST,
                 V_VALORTX4,
                 R_VENCIM.Q82_VENC,
                 V_NUMPRE,
                 R_VENCIM.Q82_PARC,
                 V_PARCELAS,
                 V_DIGITO,
                 R_VENCIM.Q92_TIPO);
              end if;
            END IF;
            V_TOTALZAO = V_TOTALZAO + V_VALORTX4;
          END IF;
          IF V_TAXA5 > 0 THEN
            V_VALORTX5 := ROUND(V_TAXA5 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            IF V_PARCELAS = R_VENCIM.Q82_PARC THEN
              V_VALORTX5 := ROUND(V_TAXA5 - V_DIFTX5 - V_SOMATX5,2)::FLOAT8;
            END IF;
            V_SOMATX5 := V_SOMATX5 + V_VALORTX5;
            V_TEMFINANC = TRUE;
            IF DEMO IS FALSE THEN
              if V_MOSTRAR IS TRUE THEN
                if v_raise is true then
                  raise notice 'CGM: % - DTOPER: % - REC: % - HIST: % - VALOR: % - VENC: % - NUMPRE: % - PARC: % - PARC: % - DIGITO: % - TIPO: %',V_NUMCGM,V_DTOPER,V_RECTX5,R_VENCIM.Q82_HIST,V_VALORTX5,R_VENCIM.Q82_VENC,V_NUMPRE,R_VENCIM.Q82_PARC,V_PARCELAS,V_DIGITO,R_VENCIM.Q92_TIPO;
                end if;
              end if;
              if lProcessarArrecad then
                INSERT INTO ARRECAD (K00_NUMCGM,K00_DTOPER,K00_RECEIT,K00_HIST,K00_VALOR,K00_DTVENC,K00_NUMPRE,K00_NUMPAR,K00_NUMTOT,K00_NUMDIG,K00_TIPO)
                VALUES (V_NUMCGM,
                V_DTOPER,
                V_RECTX5,
                R_VENCIM.Q82_HIST,
                V_VALORTX5,
                R_VENCIM.Q82_VENC,
                V_NUMPRE,
                R_VENCIM.Q82_PARC,
                V_PARCELAS,
                V_DIGITO,
                R_VENCIM.Q92_TIPO);
              end if;
            END IF;
            V_TOTALZAO = V_TOTALZAO + V_VALORTX5;
          END IF;

        ELSE

          IF V_IPTU > 0 THEN
            V_VALORPAR := ROUND(V_IPTU * ( R_VENCIM.Q82_PERC / 100),2)::FLOAT8;
            V_DIFIPTU := V_DIFIPTU + V_VALORPAR;
          END IF;

          IF V_TAXA1 > 0 THEN
            V_VALORTX1 := ROUND(V_TAXA1 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            V_DIFTX1 := V_DIFTX1 + V_VALORTX1;
          END IF;

          IF V_TAXA2 > 0 THEN
            V_VALORTX2 := ROUND(V_TAXA2 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            V_DIFTX2 := V_DIFTX2 + V_VALORTX2;
          END IF;

          IF V_TAXA3 > 0 THEN
            V_VALORTX3 := ROUND(V_TAXA3 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            V_DIFTX3 := V_DIFTX3 + V_VALORTX3;
          END IF;

          IF V_TAXA4 > 0 THEN
            V_VALORTX4 := ROUND(V_TAXA4 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            V_DIFTX4 := V_DIFTX4 + V_VALORTX4;
          END IF;

          IF V_TAXA5 > 0 THEN
            V_VALORTX5 := ROUND(V_TAXA5 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            V_DIFTX5 := V_DIFTX5 + V_VALORTX5;
          END IF;

        END IF;

      END LOOP;

      if v_raise is true then
        raise notice 'depois dos vencimentos';
      end if;

      IF V_TEMFINANC = TRUE THEN
        IF DEMO IS FALSE THEN
          SELECT K00_NUMPRE
          INTO V_NUMPREEXISTE
          FROM ARREMATRIC
          WHERE K00_NUMPRE = V_NUMPRE AND
          K00_MATRIC = MATRICULA;
          IF V_NUMPREEXISTE IS NULL THEN
            INSERT INTO ARREMATRIC VALUES ( V_NUMPRE,
            MATRICULA );
          END IF;
        END IF;
        IF V_MESMONUMPRE = FALSE AND DEMO IS FALSE THEN
          INSERT INTO IPTUNUMP VALUES ( ANOUSU,
          MATRICULA,
          V_NUMPRE );
        END IF;

        if DEMO is false then

          for rRecibosGerados in select distinct recibopaga.k00_numnov,
                                        recibopaga.k00_dtoper,
                                        recibopaga.k00_dtpaga
                                   from arrecad
                                        inner join recibopaga on recibopaga.k00_numpre = arrecad.k00_numpre
                                                             and recibopaga.k00_numpar = arrecad.k00_numpar
                                  where arrecad.k00_numpre = V_NUMPRE
          loop

            delete from recibopaga where k00_numnov = rRecibosGerados.k00_numnov;
            perform fc_recibo(rRecibosGerados.k00_numnov, rRecibosGerados.k00_dtoper, rRecibosGerados.k00_dtpaga, extract(year from rRecibosGerados.k00_dtpaga)::integer);
          end loop;

        end if;

      END IF;

    END IF;

    V_MANUAL := V_MANUAL || 'VALOR TOTAL A PAGAR: ' || V_TOTALZAO || '\n';

    IF DEMO IS FALSE THEN

      perform * from IPTUCALC where J23_MATRIC = MATRICULA AND J23_ANOUSU = ANOUSU;
      if found then
        UPDATE IPTUCALC SET J23_MANUAL = V_MANUAL WHERE J23_MATRIC = MATRICULA AND J23_ANOUSU = ANOUSU;
      else
        RETURN V_MANUAL;
      end if;

    END IF;

    IF DEMO IS TRUE THEN
      ----      V_MANUAL := '9 CALCULO CONCLUIDO';
      RETURN V_MANUAL;
    ELSE

      if v_raise is true then
        raise notice 'matricula: %',MATRICULA;
        raise notice 'demo: %',DEMO;
        raise notice 'Demonstrativo : %', V_MANUAL;
      end if;


      RETURN '01 CALCULO CONCLUIDO - MATRICULA '||MATRICULA;
    END IF;

    END;

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
        -- quando caracteristica 441 e tem mais de um logradouro, o sistema calcula 10% a mais
-- V_TEM_CARACT_ESQUINA = 441 quando encontrada no carlot
-- no iptucalc a area gravada e a area corrigidae nao a area real

set check_function_bodies to on;
create or replace function fc_calculoiptu_bag_2019(integer,integer,boolean,boolean,boolean,boolean,boolean,integer,integer)
RETURNS VARCHAR(100)
as $$
DECLARE

  MATRICULA 	      ALIAS FOR $1;
  ANOUSU    	      ALIAS FOR $2;
  GERAFINANC          ALIAS FOR $3;
  ATUALIZAP	      ALIAS FOR $4;
  NOVONUMPRE	      ALIAS FOR $5;
  CALCULO_GERAL       ALIAS FOR $6;
  DEMO		      ALIAS FOR $7;
  PARCELAINI          ALIAS FOR $8;
  PARCELAFIM          ALIAS FOR $9;

  V_SETOR	      CHAR(4);
  V_QUADRA	      CHAR(4);
  V_LOTE	      CHAR(4);
 
  V_IPTUFRAC	 	    INTEGER;
  V_TOTMAT                  INTEGER;
  V_QFACE  	   	    INTEGER ;
	iHistIptuIsen	    INTEGER ;
  V_IDBQL                   INTEGER DEFAULT 0;
  V_QUANT_TESTADA	    INTEGER DEFAULT 0;
  V_NUMERO_ESQUINA          INTEGER DEFAULT 0;
  V_TIPOI		    INTEGER DEFAULT 0;
 
  V_TOTCONSTR		    FLOAT8 DEFAULT 0;
  V_TOTMATRICULA            FLOAT8 DEFAULT 0;
  V_AREALO                  FLOAT8;
  V_TAXABOMB		    FLOAT8;
	nValIsenTaxa1       FLOAT8;
  V_TOTALZAO                FLOAT8 DEFAULT 0;
  V_TAXALIMP		    FLOAT8 DEFAULT 0;
  V_VALINFLATOR	            FLOAT8;
  V_VM2T    		    FLOAT8 DEFAULT 0;
  V_AREAL   		    FLOAT8 DEFAULT 0;
  V_AREAL_GLEBA             FLOAT8 DEFAULT 0;
  V_AREAL_GLEBA_DEMAIS      FLOAT8 DEFAULT 0;
  V_PROFUND_PADRAO 	    FLOAT8 DEFAULT 0;
  V_PROFUND_MEDIA 	    FLOAT8 DEFAULT 0;
  V_INDICE_CORRECAO	    FLOAT8 DEFAULT 0;
  V_INDICE_MULTI  	    FLOAT8 DEFAULT 0;
  V_TESTADA 	    	    FLOAT8 DEFAULT 0;
  V_AREA_CORRIG 	    FLOAT8 DEFAULT 0;
  V_AREA_TRIBUT 	    FLOAT8 DEFAULT 0;
  V_VVT	          	    FLOAT8 DEFAULT 0;
  V_J01_FRACAO	  	    FLOAT8 DEFAULT 0;
  AREATOTCONSTRMAT          FLOAT8 DEFAULT 0;
  V_FRACAO      	    FLOAT8 DEFAULT 100;

  V_CARAC		VARCHAR(250);
  V_BAIXA		DATE;
  V_AREATC              FLOAT8 DEFAULT 0;
  V_AREAC		FLOAT8 DEFAULT 0;
  V_VVC		        FLOAT8 DEFAULT 0;
  V_VVCP		FLOAT8 DEFAULT 0;
  V_VM2C		FLOAT8 DEFAULT 0;
  V_VM2C2		FLOAT8 DEFAULT 0;
  V_VV			FLOAT8 DEFAULT 0;
  V_AREACALC		FLOAT8 DEFAULT 0;
  V_ALIPRE		FLOAT8 DEFAULT 0;
  V_ALITER		FLOAT8 DEFAULT 0;
  V_ALIQ		FLOAT8 DEFAULT 0;
  V_ALIQANT		FLOAT8 DEFAULT 0;
  V_RECIPTU		INTEGER;
  V_IPTU		FLOAT8 DEFAULT 0;
  V_DIGITO		INTEGER;
  V_MESMONUMPRE	BOOLEAN DEFAULT FALSE;
  V_NUMPRE		INTEGER;
  V_NUMPREEXISTE INTEGER;
  V_VENCIM		INTEGER;
  V_PARCELAS		INTEGER;
  V_VALORPAR		FLOAT8 DEFAULT 0;
  V_PARINI		FLOAT8 DEFAULT 1;
  V_QUANT_IMOVEIS	INTEGER DEFAULT 0;
  V_NUMCGM		INTEGER;
  V_CGMPRI		INTEGER;
  V_CGMPRITESTE	INTEGER;
  V_DTOPER		DATE;
  V_TEMFINANC		BOOLEAN DEFAULT FALSE;
  V_SOMA		FLOAT8;
  V_DIFIPTU		FLOAT8 default 0;
  V_ISENALIQ		FLOAT8;
  V_VLRISEN		FLOAT8 DEFAULT 0;
  V_NUMBCO		VARCHAR(15);
  V_TEMPAGAMENTO	BOOLEAN DEFAULT FALSE;
  V_TIPOIS		INTEGER;
  V_ISENTAXAS		BOOLEAN DEFAULT FALSE;
  V_CODISEN 		INTEGER;
  V_CODTIPOISEN	INTEGER;
  V_TESTAISEN		INTEGER;
  V_PROJETOCURA	FLOAT8 DEFAULT 0;
  V_CONDOMINIO		INTEGER DEFAULT 0;
  V_MURO		BOOLEAN DEFAULT FALSE;
  V_CALCADA		BOOLEAN DEFAULT FALSE;
  V_PAVIMENTACAO  	INTEGER DEFAULT 0;
  V_BEIRAL		BOOLEAN DEFAULT FALSE;
  V_SANITARIA 		BOOLEAN DEFAULT TRUE;
  V_IRREGULAR		BOOLEAN DEFAULT FALSE;
  V_QLIMPEZA		FLOAT8 DEFAULT 0;
  V_ZONA		INTEGER DEFAULT 0;
  V_COMERCIO		BOOLEAN DEFAULT FALSE;
  V_DESCONTOT		FLOAT8	DEFAULT 0;
  V_DESCONTOP		FLOAT8	DEFAULT 0;
  V_ILUMINACAO		INTEGER	DEFAULT 0;
  V_ALAGADO		BOOLEAN DEFAULT FALSE;
  V_ENCRAVADO		BOOLEAN DEFAULT FALSE;
  V_GLEBA		BOOLEAN DEFAULT FALSE;
  V_ESPECIE		INTEGER DEFAULT 0;
  V_USO		INTEGER DEFAULT 0;
  V_ANOCONSTRUCAO	INTEGER DEFAULT 0;
  V_TIPOCONSTRUCAO	INTEGER DEFAULT 0;
  V_UTILIDADEPUBLICA	BOOLEAN DEFAULT FALSE;
  V_1			INTEGER DEFAULT 1;
  V_2			INTEGER DEFAULT 2;
  V_LOTEAM		FLOAT8 DEFAULT 0;
  V_TIPOIMP            CHAR(1);
  V_DIAINI		DATE;
  V_DIAFIM		DATE;

  V_TAXA1		FLOAT8 DEFAULT 0;
  V_VALORTX1		FLOAT8 DEFAULT 0;
  V_SOMATX1 		FLOAT8 DEFAULT 0;
  V_DIFTX1 		FLOAT8 DEFAULT 0;
  V_RECTX1		INTEGER;

  V_TAXA2		FLOAT8 DEFAULT 0;
  V_VALORTX2		FLOAT8 DEFAULT 0;
  V_SOMATX2 		FLOAT8 DEFAULT 0;
  V_DIFTX2 		FLOAT8 DEFAULT 0;
  V_RECTX2		INTEGER;
  V_TAXA3		FLOAT8 DEFAULT 0;
  V_VALORTX3		FLOAT8 DEFAULT 0;
  V_SOMATX3 		FLOAT8 DEFAULT 0;
  V_DIFTX3 		FLOAT8 DEFAULT 0;
  V_RECTX3		INTEGER;
  V_TAXA4		FLOAT8 DEFAULT 0;
  V_VALORTX4		FLOAT8 DEFAULT 0;
  V_SOMATX4 		FLOAT8 DEFAULT 0;
  V_DIFTX4 		FLOAT8 DEFAULT 0;
  V_RECTX4		INTEGER;
  V_TAXA5		FLOAT8 DEFAULT 0;
  V_VALORTX5		FLOAT8 DEFAULT 0;
  V_SOMATX5 		FLOAT8 DEFAULT 0;
  V_DIFTX5 		FLOAT8 DEFAULT 0;
  V_RECTX5		INTEGER;
  V_TAXA6		FLOAT8 DEFAULT 0;
  V_VALORTX6		FLOAT8 DEFAULT 0;
  V_SOMATX6 		FLOAT8 DEFAULT 0;
  V_DIFTX6 		FLOAT8 DEFAULT 0;
  V_RECTX6		INTEGER;
  V_TAXA7		FLOAT8 DEFAULT 0;
  V_VALORTX7		FLOAT8 DEFAULT 0;
  V_SOMATX7 		FLOAT8 DEFAULT 0;
  V_DIFTX7 		FLOAT8 DEFAULT 0;
  V_RECTX7		INTEGER;
  V_TAXA8		FLOAT8 DEFAULT 0;
  V_VALORTX8		FLOAT8 DEFAULT 0;
  V_SOMATX8 		FLOAT8 DEFAULT 0;
  V_DIFTX8 		FLOAT8 DEFAULT 0;
  V_RECTX8		INTEGER;
  V_TAXA9		FLOAT8 DEFAULT 0;
  V_VALORTX9		FLOAT8 DEFAULT 0;
  V_SOMATX9 		FLOAT8 DEFAULT 0;
  V_DIFTX9 		FLOAT8 DEFAULT 0;
  V_RECTX9		INTEGER;
  V_QUATX		INTEGER DEFAULT 0;
  V_J71_VALOR		FLOAT8 DEFAULT 0;
  V_J72_VALOR		FLOAT8 DEFAULT 0;
  V_CARTAXA		INTEGER;

  R_CONSTR          RECORD;
  R_IDCONSTR		    RECORD;
  R_VENCIM  		    RECORD;
  R_TAXA	    	    RECORD;
  R_CARLOTE	  	    RECORD;
  R_CARFACE	  	    RECORD;
  R_IPTUTAXA		    RECORD;
  R_FRACAO		      RECORD;
  R_PROPRIS		      RECORD;
  V_RECORD_ARRECAD	RECORD;

  V_MANUAL 	     TEXT DEFAULT ' \n';
 
  V_CAR_PASSEIO	INTEGER;
  V_CAR_CERCA_MURO	INTEGER;
  V_SIT_CONSTR		INTEGER DEFAULT 0;

  CAR_ESQ 		INTEGER DEFAULT 0;
  CAR_AMBAS 		INTEGER DEFAULT 0;
  V_CARACT_CALCULO	INTEGER;
  V_FORMA_TERRENO 	INTEGER;

  V_PONTOS FLOAT8 DEFAULT 0;
  V_FATOR	FLOAT8;
  V_FATOR_FORMA FLOAT8 DEFAULT 0;
  V_FATOR_ESQUINA FLOAT8 DEFAULT 0;
  V_FATOR_UTIL_LOTE FLOAT8 DEFAULT 1;
  V_FATOR_NIVEL_RUA FLOAT8 DEFAULT 1;
  V_FATOR_TOPOGRAFIA FLOAT8 DEFAULT 1;
  V_FATOR_PEDOLOGIA FLOAT8 DEFAULT 1;
  V_FATOR_HOMOGEINI FLOAT8 DEFAULT 1;
  V_FATOR_GLEBA FLOAT8 DEFAULT 1;
  V_INDICE_GLEBA FLOAT8 DEFAULT 0;

  V_PONTUACAO		INTEGER;
  V_TAXALIXOCAR	INTEGER;
  V_TAXALIXOVAL	FLOAT8 DEFAULT 0;
  V_BASE		FLOAT8 DEFAULT 0;
  V_ACRESCIMO		BOOLEAN DEFAULT FALSE;
  V_MOSTRAR		BOOLEAN DEFAULT TRUE;

  V_TEM_CARACT_ESQUINA INTEGER DEFAULT 0;
  V_TEM_CARACT_GLEBA INTEGER DEFAULT 0;
  V_EMPAGAMENTO	BOOLEAN DEFAULT FALSE;
  v_raise    		boolean default false;


  -- mudancas parcelas
  V_TEXT		TEXT DEFAULT '';
  V_PERC		FLOAT8;
  V_HIST		INTEGER;
  V_TIPO		INTEGER;
  V_PARCELAINI		INTEGER DEFAULT 0;
  V_PARCELACALC	FLOAT8;

  V_PASSA       	BOOLEAN DEFAULT TRUE;

  lProcessarArrecad	BOOLEAN DEFAULT TRUE;
  lAbatimento       BOOLEAN DEFAULT TRUE;

  iNumpreVerifica   INTEGER;

  rRecibosGerados record;

  BEGIN

    v_raise := ( case when fc_getsession('DB_debugon') is null then false else true end );

    IF PARCELAFIM < PARCELAINI THEN
      RETURN '21 PARCELAS INCONSISTENTES';
    END IF;

    SELECT J01_IDBQL, CASE WHEN J34_AREAL = 0 THEN J34_AREA ELSE J34_AREAL END AS J34_AREAL,
           J01_NUMCGM, J01_BAIXA, J34_SETOR, J34_QUADRA, J34_LOTE
      INTO V_IDBQL, V_AREAL, V_NUMCGM, V_BAIXA, V_SETOR, V_QUADRA, V_LOTE
    FROM IPTUBASE
                  INNER JOIN LOTE     ON J34_IDBQL = J01_IDBQL
                  LEFT  JOIN IPTUFRAC ON J25_ANOUSU = ANOUSU AND J25_MATRIC = J01_MATRIC
    WHERE J01_MATRIC = MATRICULA;

    IF V_IDBQL IS NULL THEN
      RETURN '16 MATRICULA NAO CADASTRADA';
    END IF;

    IF NOT V_BAIXA IS NULL THEN
      RETURN '02 MATRICULA BAIXADA';
    END IF;

    IF V_AREAL = 0 OR V_AREAL IS NULL THEN
      RETURN '03 AREA DO LOTE ZERADA';
    END IF;

    if v_raise is true then
      raise notice 'V_SETOR: % - V_QUADRA: % - V_LOTE: %', V_SETOR, V_QUADRA, V_LOTE;
    end if;


    SELECT COUNT(J01_IDBQL)
    INTO V_TOTMAT
    FROM IPTUBASE
    WHERE J01_BAIXA IS NULL AND J01_IDBQL = V_IDBQL;

    if v_raise is true then
      raise notice 'matricula: %',MATRICULA;
      raise notice 'total de matriculas: %',V_TOTMAT;
    end if;

    select j20_numpre
      into iNumpreVerifica
      from iptunump
     where j20_matric = MATRICULA
       and j20_anousu = ANOUSU
     limit 1;

    if found then

      -- Verifica se existe Pagamento Parcial para o debito informado
      select fc_verifica_abatimento(1, iNumpreVerifica)::boolean into lAbatimento;

      if lAbatimento then
        return '04 DEBITO COM PAGAMENTO PARCIAL!';
      end if;
    end if;

    IF V_TOTMAT > 1 THEN

       -- CALCULA FRACAO DO LOTE
      SELECT COALESCE(SUM(J39_AREA), 0)
        INTO V_TOTCONSTR
      FROM IPTUBASE
                    INNER JOIN IPTUCONSTR ON J39_MATRIC = J01_MATRIC
                    INNER JOIN LOTE       ON J34_IDBQL = J01_IDBQL
      WHERE J01_BAIXA IS NULL
        AND J34_SETOR = V_SETOR
        AND J34_QUADRA = V_QUADRA
        AND J34_LOTE = V_LOTE
        AND J39_DTDEMO IS NULL;

      if v_raise is true then
        raise notice 'total construido no lote: %',V_TOTCONSTR;
      end if;

      V_MANUAL := V_MANUAL || 'AREA TOTAL CONSTRUIDA NO LOTE: ' || V_TOTCONSTR || ' - ';

      IF V_TOTCONSTR = 0 THEN
         UPDATE IPTUBASE SET J01_FRACAO = 0 WHERE J01_IDBQL = V_IDBQL;
      ELSE

        if v_raise is true then
          raise notice 'entrando no fracao';
        end if;

        FOR R_FRACAO IN
                        SELECT J01_MATRIC, COALESCE(SUM(J39_AREA), 0) AS AREATOTCONSTRMAT
                        FROM IPTUBASE LEFT JOIN IPTUCONSTR ON J39_MATRIC = J01_MATRIC
                        WHERE J01_BAIXA IS NULL AND J39_DTDEMO IS NULL AND J01_IDBQL = V_IDBQL
                        GROUP BY J01_MATRIC LOOP

          if v_raise is true then
            raise notice 'processando fracao matricula: % - construido desta: %',R_FRACAO.J01_MATRIC,R_FRACAO.AREATOTCONSTRMAT;
          end if;

          SELECT J25_MATRIC
            INTO V_IPTUFRAC
          FROM IPTUFRAC
          WHERE J25_MATRIC = R_FRACAO.J01_MATRIC
            AND J25_ANOUSU = ANOUSU;

          IF V_IPTUFRAC IS NULL THEN
            if v_raise is true then
              raise notice '   insert no iptufrac';
            end if;

            INSERT INTO IPTUFRAC values (ANOUSU,R_FRACAO.J01_MATRIC,V_IDBQL,((R_FRACAO.AREATOTCONSTRMAT / V_TOTCONSTR) * 100));
          ELSE
            if v_raise is true then
               raise notice '   update no iptufrac';
               raise notice '   fracao iptufrac: %', V_IPTUFRAC;
            end if;

            UPDATE IPTUFRAC
              SET J25_FRACAO = ((R_FRACAO.AREATOTCONSTRMAT / V_TOTCONSTR) * 100), J25_IDBQL = V_IDBQL
              WHERE J25_MATRIC = R_FRACAO.J01_MATRIC AND J25_ANOUSU = ANOUSU
                AND J25_FRACAO <> ((R_FRACAO.AREATOTCONSTRMAT / V_TOTCONSTR) * 100);
          END IF;

        END LOOP;

        SELECT J25_FRACAO
          INTO V_FRACAO
        FROM IPTUFRAC
        WHERE J25_MATRIC = MATRICULA AND J25_ANOUSU = ANOUSU;

        IF V_FRACAO IS NULL OR V_FRACAO = 0 THEN
          V_FRACAO = 100::float8;
        END IF;

      END IF;

    END IF;

    SELECT J01_FRACAO
      INTO V_J01_FRACAO
    FROM IPTUBASE
    WHERE J01_MATRIC = MATRICULA;

    IF V_J01_FRACAO IS NOT NULL AND V_J01_FRACAO > 0 THEN
      V_FRACAO = V_J01_FRACAO;

      if v_raise is true then
        raise notice 'fracao da iptubase: %', V_FRACAO;
      end if;
    END IF;

    if v_raise is true then
      raise notice 'fracao da iptubase: %', V_FRACAO;
      raise notice 'verificando pagamentos';
      raise notice 'select iptunump inner join arrecant';
    end if;

    -- VERIFICA PAGAMENTOS
    ----raise NOTICE 'VERIFICA PAGAMENTO';
    SELECT J20_NUMPRE,MAX(K00_NUMPAR) AS K00_NUMPAR
      INTO V_NUMPRE,V_PARINI
      FROM IPTUNUMP
           INNER JOIN ARRECANT ON J20_NUMPRE = K00_NUMPRE
     WHERE J20_ANOUSU = ANOUSU
  		 AND J20_MATRIC = MATRICULA
    GROUP BY J20_NUMPRE;

    IF NOT V_NUMPRE IS NULL AND DEMO = FALSE THEN
      IF ATUALIZAP = FALSE THEN
        V_EMPAGAMENTO = TRUE;
        --RETURN '04 CARNE EM PROCESSO DE PAGAMENTO';
      END IF;
      V_TEMPAGAMENTO = TRUE;
    ELSE
      V_PARINI := 1;
    END IF;

    if v_raise is true then
      raise notice 'V_NUMPRE: % - V_PARINI: %', V_NUMPRE, V_PARINI;
    end if;

    -- REMOVE CALCULO EXISTENTE
    if v_raise is true then
      raise notice 'deletando iptucalv... iptucale... iptucalc...';
    end if;

    IF CALCULO_GERAL = FALSE AND DEMO IS FALSE THEN
      DELETE FROM IPTUCALV WHERE J21_ANOUSU = ANOUSU AND J21_MATRIC = MATRICULA;
      DELETE FROM IPTUCALE WHERE J22_ANOUSU = ANOUSU AND J22_MATRIC = MATRICULA;
      DELETE FROM IPTUCALC WHERE J23_ANOUSU = ANOUSU AND J23_MATRIC = MATRICULA;
    END IF;

    -- CALCULA VALOR DO TERRENO

    SELECT J30_ALITER, J30_ALIPRE, J34_ZONA
    INTO V_ALITER, V_ALIPRE, V_ZONA
    FROM LOTE INNER JOIN SETOR ON J34_SETOR = J30_CODI
    WHERE J34_IDBQL = V_IDBQL;

    if v_raise is true then
      raise NOTICE 'IDBQL %', V_IDBQL;
    end if;

    select j35_caract
		  into V_FORMA_TERRENO
    FROM CARLOTE inner join caracter on j31_codigo = j35_caract
    WHERE j35_idbql = V_IDBQL
      AND j31_grupo = 31;

    if V_FORMA_TERRENO is null then
      V_FORMA_TERRENO = 0;
    end if;

    V_MANUAL := V_MANUAL || 'CARACTERISTICA DO FORMATO DO IMOVEL: ' || V_FORMA_TERRENO || ' \n ';

    select coalesce(j74_fator, 1) into V_FATOR_FORMA from carfator where j74_anousu = ANOUSU and j74_caract = V_FORMA_TERRENO;

    V_MANUAL := V_MANUAL || 'FATOR DO FORMATO DO IMOVEL: ' || V_FATOR_FORMA || ' \n ';

    --
    -- TESTADA PRINCIPAL DO LOTE
    --

    SELECT J37_FACE, CASE WHEN COALESCE(J36_TESTLE, 0) = 0 THEN J36_TESTAD ELSE J36_TESTLE END AS J36_TESTLE
    INTO V_QFACE, V_TESTADA
    FROM IPTUCONSTR INNER JOIN IPTUBASE ON J01_MATRIC = J39_MATRIC
                    INNER JOIN TESTPRI ON j49_idbql = J01_IDBQL
                    INNER JOIN TESTADA ON J36_FACE = j49_face AND J36_IDBQL = J01_IDBQL
                    INNER JOIN FACE ON J37_FACE = J36_FACE
    WHERE J39_MATRIC = MATRICULA AND J39_DTDEMO IS NULL AND J01_BAIXA IS NULL LIMIT 1;

    if v_raise is true then
      raise NOTICE 'V_QFACE % ',V_QFACE;
    end if;

    select count(*) into V_QUANT_TESTADA from testada WHERE j36_idbql = V_IDBQL;

    if v_raise is true then
      raise notice 'V_QUANT_TESTADA: %', V_QUANT_TESTADA;
    end if;

    select j35_caract
      INTO  V_TEM_CARACT_ESQUINA
    from carlote
    where j35_idbql = V_IDBQL and j35_caract = 441;

    IF V_TEM_CARACT_ESQUINA IS NULL THEN
       V_TEM_CARACT_ESQUINA := 0;
    END IF;

    V_MANUAL := V_MANUAL || ' TEM ESQUINA(441) : ' || V_TEM_CARACT_ESQUINA || ' - ';

    --
    -- PEGAR ZONA
    --

    if v_raise is true then
      raise notice ' ZONA %',V_ZONA;
    end if;

    V_MANUAL := V_MANUAL || 'FRACAO: ' || V_FRACAO || ' - ';
    V_MANUAL := V_MANUAL || 'ZONA FISCAL: ' || V_ZONA || ' - ';
    V_MANUAL := V_MANUAL || 'QUANTIDADE DE TESTADA: ' || V_QUANT_TESTADA || ' \n ';

    IF V_QFACE IS NULL THEN

      SELECT J49_FACE, CASE WHEN COALESCE(J36_TESTLE, 0) = 0 THEN J36_TESTAD ELSE J36_TESTLE END AS J36_TESTLE
        INTO V_QFACE, V_TESTADA
        FROM TESTPRI INNER JOIN FACE ON J49_FACE = J37_FACE
                     INNER JOIN TESTADA ON J49_FACE = J36_FACE AND J49_IDBQL = J36_IDBQL
       WHERE J49_IDBQL = V_IDBQL;

    END IF;

    IF V_QFACE IS NULL THEN
      RETURN '06 TESTADA PRINCIPAL DO LOTE NAO CADASTRADA OU FACE NAO ENCONTRADA';
    END IF;

    select j81_valorterreno
      into V_VM2T
    from FACEVALOR
     where J81_FACE   = V_QFACE
       and J81_ANOUSU = ANOUSU;

    if not found or V_VM2T is null or V_VM2T = 0 then
       RETURN '05 VALOR DO M2 DO TERRENO NAO ENCONTRADO PARA A FACE '||V_QFACE||' !';
    end if;

    V_MANUAL := V_MANUAL || 'VALOR DO M2 DO TERRENO: ' || V_VM2T || ' - ';

    if v_raise is true then
       raise NOTICE 'V_QFACE % AREA % TESTADA %',V_QFACE,V_AREAL,V_TESTADA;
    end if;

    --VERIFICA ISENCOES
    if v_raise is true then
       raise NOTICE 'verificando isencoes';
    end if;

    SELECT J46_CODIGO, J45_TIPIS, J46_PERC,
           CASE WHEN J45_TAXAS = FALSE THEN TRUE ELSE FALSE END,
           CASE WHEN J46_AREALO IS NULL THEN 0 ELSE J46_AREALO END AS J46_AREALO
      INTO V_CODISEN, V_TIPOIS, V_ISENALIQ, V_ISENTAXAS, V_AREALO
    FROM IPTUISEN INNER JOIN ISENEXE ON J46_CODIGO = J47_CODIGO
                  INNER JOIN TIPOISEN ON J46_TIPO = J45_TIPO
    WHERE J46_MATRIC = MATRICULA AND J47_ANOUSU = ANOUSU;

    IF V_ISENALIQ IS NULL THEN
      V_ISENALIQ := 0;
    END IF;

    IF V_ISENTAXAS IS NULL THEN
      V_ISENTAXAS := TRUE;
    END IF;

    if v_raise is true then
      raise notice 'V_CODISEN: % - V_ISENTAXAS: % - V_ISENALIQ: %', V_CODISEN, V_ISENTAXAS, V_ISENALIQ;
    end if;

    -- CALCULA A PROFUNDIDADE MEDIA

    V_PROFUND_PADRAO := 30;

    V_MANUAL := V_MANUAL || 'PROFUNDIDADE PADRAO: ' || V_PROFUND_PADRAO || ' \n ';

    -- verifica caracteristica do grupo 30
    select j35_caract
		into V_CARACT_CALCULO
    FROM CARLOTE inner join caracter on j31_codigo = j35_caract
    WHERE j35_idbql = V_IDBQL
      AND j31_grupo = 30;

    if V_CARACT_CALCULO is null then
       V_CARACT_CALCULO = 0;
    end if;

    V_MANUAL := V_MANUAL || 'CARACTERISTICA DA AREA DO LOTE: ' || V_CARACT_CALCULO || ' - ';

    if v_raise is true then
      raise notice 'V_CARACT_CALCULO: %', V_CARACT_CALCULO;
    end if;

    V_AREA_TRIBUT = V_AREAL;
    V_MANUAL := V_MANUAL || 'AREA TRIBUTADA: ' || V_AREA_TRIBUT || ' \n ';

    if v_raise is true then
      raise notice 'AREA TRIBUTADA: %', V_AREA_TRIBUT;
    end if;

    if V_CARACT_CALCULO = 300 THEN
      -- calcula com a area tributada
      -- informada. os campos:
      -- iptudiversos->j80_areatrib = area tributada

      V_MANUAL := V_MANUAL || 'V_CARACT_CALCULO: ' || V_CARACT_CALCULO || ' - CALCULANDO PELA AREA TRIBUTADA ' || ' - ';

      select j80_areatrib into V_AREA_TRIBUT from iptudiversos where j80_matric = MATRICULA;

      V_MANUAL := V_MANUAL || 'AREA TRIBUTADA INFORMADA: ' || V_AREA_TRIBUT || ' \n ';

      IF V_AREA_TRIBUT IS NULL THEN
        RETURN '09 - sem dados de area tributada lancada...';
      END IF;

      if v_raise is true then
        raise notice 'AREA TRIBUTADA INFORMADA: %', V_AREA_TRIBUT;
      end if;

    end if;

    V_MANUAL := V_MANUAL || ' CALCULANDO PELA FORMULA ' || ' - ';

    V_PROFUND_MEDIA = ROUND(V_AREAL / V_TESTADA,2);
    V_MANUAL := V_MANUAL || ' PROFUNDIDADE MEDIA = AREA DO LOTE / TESTADA: ' || V_PROFUND_MEDIA || ' - ';

    if v_raise is true then
      raise notice 'PROFUNDIDADE MEDIA = AREA DO LOTE / TESTADA: %', V_PROFUND_MEDIA;
    end if;

    V_INDICE_CORRECAO = SQRT(V_PROFUND_PADRAO / V_PROFUND_MEDIA);
    V_MANUAL := V_MANUAL || ' INDICE DE CORRECAO = RAIZ QUADRADA DA (PROFUNDIDADE PADRAO / PROFUNDIDADE MEDIA): ' || V_INDICE_CORRECAO || ' \n ';

    if v_raise is true then
      raise notice 'INDICE DE CORRECAO = RAIZ QUADRADA DA (PROFUNDIDADE PADRAO / PROFUNDIDADE MEDIA): %', V_INDICE_CORRECAO;
    end if;

    if V_TEM_CARACT_ESQUINA > 0 AND V_QUANT_TESTADA > 1 then
       if V_QUANT_TESTADA = 2 then
          V_FATOR_ESQUINA := 1.1;
       elsif V_QUANT_TESTADA = 3 then
          V_FATOR_ESQUINA := 1.2;
       else
          V_FATOR_ESQUINA := 1.3;
       end if;

       V_MANUAL := V_MANUAL || ' SE QUANTIDADE DE TESTADA = ' || V_QUANT_TESTADA || ' - ';
       V_MANUAL := V_MANUAL || ' FATOR ESQUINA = ' || V_FATOR_ESQUINA || ' - ';

       if v_raise is true then
         raise notice 'QUANTIDADE DE TESTADA: %', V_QUANT_TESTADA;
         raise notice 'FATOR ESQUINA: %', V_FATOR_ESQUINA;
       end if;
    else
       V_FATOR_ESQUINA := 1;
       if V_QUANT_TESTADA > 1 then
          V_FATOR_ESQUINA := 1.1;
       end if;

       V_MANUAL := V_MANUAL || ' SE QUANTIDADE DE TESTADA = ' || V_QUANT_TESTADA || ' - ';
       V_MANUAL := V_MANUAL || ' FATOR ESQUINA = ' || V_FATOR_ESQUINA || ' - ';

       if v_raise is true then
         raise notice 'QUANTIDADE DE TESTADA: %', V_QUANT_TESTADA;
         raise notice 'FATOR ESQUINA: %', V_FATOR_ESQUINA;
       end if;
    end if;

    V_AREA_TRIBUT = ROUND(V_AREA_TRIBUT * (V_FRACAO / 100),2);
    V_MANUAL := V_MANUAL || 'AREA TRIBUTADA = AREA TRIBUTADA * FRACAO: ' || V_AREA_TRIBUT || '\n';

    if v_raise is true then
      raise notice 'AREA TRIBUTADA = AREA TRIBUTADA * FRACAO: %', V_AREA_TRIBUT;
    end if;

    -- VERIFICA SE LOTE EH GLEBA

    select j35_caract
		  into V_TEM_CARACT_GLEBA
    FROM CARLOTE inner join caracter on j31_codigo = j35_caract
    WHERE j35_idbql = V_IDBQL
      AND j31_grupo = 93;

    IF V_TEM_CARACT_GLEBA IS NULL THEN
       V_TEM_CARACT_GLEBA := 5545;
    END IF;

    V_MANUAL := V_MANUAL || ' CARACTERISTICA GLEBA: ' || V_TEM_CARACT_GLEBA || ' - ';

    if v_raise is true then
      raise notice 'CARACTERISTICA GLEBA: %', V_TEM_CARACT_GLEBA;
    end if;

    if V_TEM_CARACT_GLEBA = 5544 then

       select j71_quantfim, ((V_AREAL - j71_ini) * j71_valor), j71_valor
         into V_INDICE_GLEBA, V_AREAL_GLEBA, V_FATOR_GLEBA
       from carvalor
       where j71_anousu = ANOUSU
         and j71_caract = V_TEM_CARACT_GLEBA
         and V_AREAL between j71_ini and j71_fim ;

       if not found then
          return '37 - AREA NAO ENCONTRADA PARA A MATRICULA '|| MATRICULA;
       end if;

       if v_raise is true then
         raise notice 'V_INDICE_GLEBA: % - V_AREAL_GLEBA: % - V_FATOR_GLEBA: %', V_INDICE_GLEBA, V_AREAL_GLEBA, V_FATOR_GLEBA;
       end if;

       select coalesce(sum(j71_quantini), 0)
         into V_AREAL_GLEBA_DEMAIS
       from carvalor
       where j71_anousu = ANOUSU
         and j71_caract = V_TEM_CARACT_GLEBA
         and j71_quantfim < V_INDICE_GLEBA ;

       if found then
          V_AREAL_GLEBA := ( V_AREAL_GLEBA + V_AREAL_GLEBA_DEMAIS );

          if v_raise is true then
            raise notice 'V_AREAL_GLEBA_DEMAIS: % - NOVO V_AREAL_GLEBA: %', V_AREAL_GLEBA_DEMAIS, V_AREAL_GLEBA;
          end if;
       end if;

       V_AREA_TRIBUT := round(V_AREAL_GLEBA, 2);

       IF V_AREA_TRIBUT IS NULL OR V_AREA_TRIBUT = 0 THEN
         RETURN '36 - AREA TRIBUTADA ZERADA PARA GLEBA - MATRICULA: ' || MATRICULA;
       END IF;

    end if;

    SELECT coalesce(sum(j39_area), 0)
      INTO V_TOTMATRICULA
    FROM IPTUCONSTR
    WHERE J39_MATRIC = MATRICULA
      AND J39_DTDEMO IS NULL ;

    if v_raise is true then
       raise notice 'AREA TOTAL CONSTRUIDA DA MATRICULA: %', V_TOTMATRICULA;
    end if;

    if V_TOTCONSTR is null or V_TOTCONSTR = 0 then
       V_TOTCONSTR = 1;
    end if;

    if v_raise is true then
       raise notice 'AREA TOTAL CONSTRUIDA DO LOTE: %', V_TOTCONSTR;
    end if;

    if V_CARACT_CALCULO = 300 OR V_TEM_CARACT_GLEBA = 5544 THEN
       V_FATOR_HOMOGEINI := 1;
    elsif V_CARACT_CALCULO = 301 AND V_AREAL < 10000 AND (V_TOTMATRICULA / V_TOTCONSTR) <> 1 THEN
       V_FATOR_HOMOGEINI := ( V_INDICE_CORRECAO * V_FATOR_FORMA * V_FATOR_ESQUINA * V_FATOR_UTIL_LOTE * V_FATOR_NIVEL_RUA );
       V_FATOR_HOMOGEINI := ( V_FATOR_HOMOGEINI * V_FATOR_TOPOGRAFIA * V_FATOR_PEDOLOGIA );

       IF V_FATOR_HOMOGEINI IS NULL OR V_FATOR_HOMOGEINI = 0 THEN
         RETURN '37 - FATOR HOMOGEINIZACAO ZERADO MATRICULA: ' || MATRICULA;
       END IF;
    end if;

    V_MANUAL := V_MANUAL || ' FATOR HOMOGENEIZACAO = ' || V_FATOR_HOMOGEINI || ' - ';

    if v_raise is true then
      raise notice 'FATOR HOMOGENEIZACAO: %', V_FATOR_HOMOGEINI;
    end if;

    --====================================================================================

    V_VVT = round(V_AREA_TRIBUT * V_VM2T * V_FATOR_HOMOGEINI, 2);
    V_AREA_TRIBUT = round(V_AREA_TRIBUT * V_FATOR_HOMOGEINI, 2);

    V_MANUAL := V_MANUAL || 'TESTADA TRIBUTADA: ' || V_TESTADA || ' METROS\n';
    V_MANUAL := V_MANUAL || 'VALOR VENAL DO TERRENO: AREA TRIBUTADA * VALOR DO METRO QUADRADO DO TERRENO ' || V_VVT || '\n';

    SELECT DISTINCT I02_VALOR
      INTO V_VALINFLATOR
    FROM CFIPTU INNER JOIN INFLA ON J18_INFLA = I02_CODIGO
    WHERE CFIPTU.J18_ANOUSU = ANOUSU
    AND DATE_PART('y',I02_DATA) = ANOUSU;

    IF V_VALINFLATOR IS NULL THEN
      V_VALINFLATOR = 1;
    END IF;

    if v_raise is true then
      raise NOTICE 'V_VALINFLATOR: % ', V_VALINFLATOR;
    end if;

    if v_raise is true then
      raise NOTICE 'VALOR VENAL TERRENO % - f % ',V_VVT,v_fracao;
    end if;

    -- CALCULA EDIFICACOES
    V_TIPOIMP = 'T';

    FOR R_CONSTR IN SELECT *
                    FROM IPTUCONSTR
                    WHERE J39_MATRIC = MATRICULA
                    AND J39_DTDEMO IS NULL
    LOOP

      V_TIPOI := 1;
      V_CARAC := '';
      V_PONTOS:= 0;
      V_ANOCONSTRUCAO = ANOUSU - R_CONSTR.J39_ANO;
      --       comentado para depois acertar ANOUSU
      if v_raise is true then
        raise notice ' ';
        raise notice 'construcao %', R_CONSTR.J39_IDCONS;
      end if;
      V_TIPOIMP = 'P';

      V_MANUAL := V_MANUAL || 'EDIFICACAO ' || LPAD(R_CONSTR.j39_idcons,3,'0') || ' - LISTA DE CARACTERISTICAS PONTOS\n';

      FOR R_IDCONSTR IN SELECT J48_CARACT,J31_PONTOS::FLOAT8, J31_GRUPO, J31_DESCR, J32_DESCR
                        FROM CARCONSTR INNER JOIN CARACTER ON J48_CARACT = J31_CODIGO
                                       INNER JOIN CARGRUP  ON J31_GRUPO = J32_GRUPO
                        WHERE J48_MATRIC = R_CONSTR.J39_MATRIC AND J48_IDCONS = R_CONSTR.J39_IDCONS
      LOOP

        V_DESCONTOP = 0;
        V_CARAC := V_CARAC || TO_CHAR(R_IDCONSTR.J48_CARACT,'9999');
        V_PONTOS := V_PONTOS + R_IDCONSTR.J31_PONTOS;

        V_MANUAL := V_MANUAL || RPAD(UPPER(R_IDCONSTR.j32_descr),30,'_') || '\t' || RPAD(R_IDCONSTR.j48_caract,3) || ' ' || RPAD(RTRIM(R_IDCONSTR.j31_descr),25,'_') || '\t' || LPAD(R_IDCONSTR.j31_pontos,3) || '\n';

        IF R_IDCONSTR.J31_GRUPO = 20 THEN
          V_PONTUACAO = R_IDCONSTR.J48_CARACT;
        END IF;

        IF R_IDCONSTR.J31_GRUPO = 6 THEN
          V_TAXALIXOCAR = R_IDCONSTR.J48_CARACT;
        END IF;

        IF R_IDCONSTR.J31_GRUPO = 38 THEN
          V_SIT_CONSTR = R_IDCONSTR.J48_CARACT;
        END IF;

      END LOOP;

      V_AREAC = ROUND(R_CONSTR.J39_AREA);

      select j71_valor into V_VM2C2 from carvalor where j71_anousu = anousu and j71_caract = V_PONTUACAO;

      select j72_valor into V_TAXALIXOVAL from carzonavalor where j72_anousu = anousu and j72_caract = V_TAXALIXOCAR and j72_zona = v_zona and j72_tipo = 'V';

      if v_raise is true then
        raise notice 'V_TAXALIXOCAR: % - V_TAXALIXOVAL: %', V_TAXALIXOCAR, V_TAXALIXOVAL;
      end if;

      V_AREATC := V_AREATC + V_AREAC;

      V_VVCP   := ROUND( V_VM2C2 * V_AREAC,2);

      V_VVC    := ROUND(V_VVC + V_VVCP,2)::FLOAT8;

      if v_raise is true then
        raise notice 'V_VM2C2 % - AREA: % - V_VVCP: %, V_VVC: %',V_VM2C2, R_CONSTR.J39_AREA, V_VVCP, V_VVC;
      end if;

      IF V_VM2C2 IS NULL OR V_VM2C2 = 0 THEN
        RETURN '10 VALOR M2 CONSTRUCAO ZERADA. CONSTRUCAO: ' || R_CONSTR.J39_IDCONS || ' PONTOS : ' || TO_CHAR(V_PONTOS,'999');
      END IF;

      IF DEMO IS FALSE THEN

        if v_raise is true then
          raise notice 'inserindo em IPTUCALE';
        end if;

        INSERT INTO IPTUCALE VALUES ( ANOUSU,
        MATRICULA,
        R_CONSTR.J39_IDCONS,
        R_CONSTR.J39_AREA,
        V_VM2C2,
        V_PONTOS,
        V_VVCP);
      END IF;

    END LOOP;

    if v_raise is true then
      raise notice 'area total construcao %',V_AREATC;
    end if;

    V_DESCONTOT := 0;

    IF V_TIPOI = 0 THEN
      if v_raise is true then
        raise NOTICE 'TIPO TERRITORIAL ';
      end if;
    ELSE
      if v_raise is true then
        raise NOTICE 'TIPO PREDIAL ';
      end if;
    END IF;

    SELECT CASE WHEN V_TIPOI = 0 THEN J18_RTERRI ELSE J18_RPREDI END, J18_VENCIM, J18_DTOPER, j18_vlrref
      INTO V_RECIPTU, V_VENCIM, V_DTOPER, V_BASE
    FROM CFIPTU
    WHERE J18_ANOUSU = ANOUSU;

    if v_raise is true then
      raise notice 'V_RECIPTU: %', V_RECIPTU;
    end if;

    IF V_RECIPTU IS NULL THEN
      RETURN '11 RECEITA IPTU NAO CADASTRADA';
    END IF;

    IF V_VENCIM IS NULL THEN
      RETURN '12 TABELA DE VENCIMENTO NAO CADASTRADA';
    END IF;

    if v_raise is true then
      raise notice ' TIPO %',V_TIPOI;
      raise notice ' V_ALITER: %, V_ALIPRE: %', V_ALITER, V_ALIPRE;
    end if;

    -- VERIFICA CARACTERISTICAS DO LOTE

    V_MANUAL := V_MANUAL || '\nCARACTERISTICAS DO LOTE                GRUPO\n';

    FOR R_CARLOTE IN SELECT J35_CARACT, J31_GRUPO, j31_descr, j32_descr
                     FROM CARLOTE INNER JOIN CARACTER ON J31_CODIGO = J35_CARACT
                                  INNER JOIN CARGRUP  ON J32_GRUPO = J31_GRUPO
                     WHERE J35_IDBQL = V_IDBQL
    LOOP

      IF R_CARLOTE.J31_GRUPO = 36 THEN
        V_CAR_PASSEIO = R_CARLOTE.J35_CARACT;
      end if;

      IF R_CARLOTE.J31_GRUPO = 37 THEN
        V_CAR_CERCA_MURO = R_CARLOTE.J35_CARACT;
      end if;

    END LOOP;

    V_VV := V_VVT + V_VVC;
    V_MANUAL := V_MANUAL || 'VALOR VENAL TOTAL: ' || V_VV || '\n';

    if v_raise is true then
      raise notice 'V_VV: % - V_BASE: %', V_VV, V_BASE;
    end if;

    IF V_TIPOI = 0 THEN
      SELECT 	j70_aliter
       into V_ALITER
      from zonasaliq
      where j70_zona = V_ZONA;

      V_ALIQ := V_ALITER;
      V_MANUAL := V_MANUAL || 'TERRITORIAL - ALIQUOTA: ' || V_ALIQ || '\n';
    ELSE
      IF ANOUSU > 2020 THEN
         IF V_VV <= 100000 THEN
            if v_raise is true then
               raise notice 'ate 100000';
            end if;

            V_ALIQ := 0.4;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 100000.01 AND 200000 THEN
            if v_raise is true then
               raise notice 'entre 100000.01 e 200000';
            end if;

            V_ALIQ := 0.5;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 200000.01 AND 300000 THEN
            if v_raise is true then
               raise notice 'entre 200000.01 e 300000';
            end if;

            V_ALIQ := 0.7;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 300000.01 AND 400000 THEN
            if v_raise is true then
               raise notice 'entre 300000.01 e 400000';
            end if;

            V_ALIQ := 0.75;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 400000.01 AND 500000 THEN
            if v_raise is true then
               raise notice 'entre 400000.01 e 500000';
            end if;

            V_ALIQ := 0.80;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 500000.01 AND 600000 THEN
            if v_raise is true then
               raise notice 'entre 500000.01 e 600000';
            end if;

            V_ALIQ := 0.81;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 600000.01 AND 700000 THEN
            if v_raise is true then
               raise notice 'entre 600000.01 e 700000';
            end if;

            V_ALIQ := 0.82;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 700000.01 AND 800000 THEN
            if v_raise is true then
               raise notice 'entre 700000.01 e 800000';
            end if;

            V_ALIQ := 0.83;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 800000.01 AND 900000 THEN
            if v_raise is true then
               raise notice 'entre 800000.01 e 900000';
            end if;

            V_ALIQ := 0.84;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 900000.01 AND 1000000 THEN
            if v_raise is true then
               raise notice 'entre 900000.01 e 1000000';
            end if;

            V_ALIQ := 0.85;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         ELSIF V_VV > 1000000 THEN
            if v_raise is true then
               raise notice 'acima de 1000000';
            end if;

            V_ALIQ := 0.9;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL (' || V_BASE || ') MULTIPLICADO POR: ' || V_ALIQ || '\n';
         END IF;
      ELSIF ANOUSU BETWEEN 2019 AND 2020 THEN
         IF V_VV <= 100000 THEN
            if v_raise is true then
               raise notice 'ate 100000';
            end if;

            V_ALIQ := 0.4;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL ENTRE VALOR BASE (' || V_BASE || ') MULTIPLICADO POR 0.01 E VALOR BASE MULTIPLICADO POR 183.339: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN 100000.01 AND 700000 THEN
            if v_raise is true then
               raise notice 'entre 100000.01 e 700000';
            end if;

            V_ALIQ := 0.45;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL ENTRE VALOR BASE (' || V_BASE || ') MULTIPLICADO POR 183.34 E VALOR BASE MULTIPLICADO POR 366.6699: ' || V_ALIQ || '\n';
         ELSIF V_VV > 700000 THEN
            if v_raise is true then
               raise notice 'acima de 700000';
            end if;

            V_ALIQ := 0.5;
            V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL ENTRE VALOR BASE (' || V_BASE || ') MULTIPLICADO POR 366.6699 E VALOR BASE MULTIPLICADO POR 9999999999.9999: ' || V_ALIQ || '\n';
         END IF;
      ELSE
         IF V_VV BETWEEN (V_BASE * 0.0001) AND (V_BASE * 183.3399) THEN
           if v_raise is true then
             raise notice 'entre 0 e 183.3399';
           end if;
           V_ALIQ := 0.8;
           V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL ENTRE VALOR BASE (' || V_BASE || ') MULTIPLICADO POR 0.01 E VALOR BASE MULTIPLICADO POR 183.339: ' || V_ALIQ || '\n';
         ELSIF V_VV BETWEEN (V_BASE * 183.34) AND (V_BASE * 366.6699) THEN
           if v_raise is true then
             raise notice 'entre 183.34 e 366.6699';
           end if;
           V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL ENTRE VALOR BASE (' || V_BASE || ') MULTIPLICADO POR 183.34 E VALOR BASE MULTIPLICADO POR 366.6699: ' || V_ALIQ || '\n';
           V_ALIQ := 0.9;
         ELSIF V_VV BETWEEN (V_BASE * 366.67) AND (V_BASE * 9999999999.9999) THEN
           if v_raise is true then
             raise notice 'entre 366.67 e 9999999999.9999';
           end if;
           V_ALIQ := 1;
           V_MANUAL := V_MANUAL || 'PREDIAL - VALOR VENAL TOTAL ENTRE VALOR BASE (' || V_BASE || ') MULTIPLICADO POR 366.6699 E VALOR BASE MULTIPLICADO POR 9999999999.9999: ' || V_ALIQ || '\n';
         END IF;
      END IF;

    END IF;

    IF V_ALIQ = 0 THEN
      RETURN '13 ALIQUOTA ZERADA';
    END IF;

    if v_raise is true then
      raise notice 'aliquota: %', V_ALIQ;
    end if;

    if v_raise is true then
      raise notice 'procurando CARZONAVALOR - V_CAR_PASSEIO: %', V_CAR_PASSEIO;
    end if;

    select j72_valor into V_FATOR
		from CARZONAVALOR
    where j72_anousu = ANOUSU
      AND j72_caract = V_CAR_PASSEIO
      AND j72_zona = V_ZONA;

    if V_FATOR is not null and 1 = 2 then
      V_ALIQ = V_ALIQ * V_FATOR;
      V_ACRESCIMO = TRUE;

      if v_raise is true then
        raise notice '   achou CARZONAVALOR - V_CAR_PASSEIO: % - FATOR: %', V_CAR_PASSEIO, V_FATOR;
      end if;

    end if;


    if V_ACRESCIMO is false then
      if v_raise is true then
        raise notice 'procurando CARZONAVALOR - V_CAR_CERCA_MURO: %', V_CAR_CERCA_MURO;
      end if;

      select j72_valor into V_FATOR
      from CARZONAVALOR
      where j72_anousu = ANOUSU
        AND j72_caract = V_CAR_CERCA_MURO
        AND j72_zona = V_ZONA;

      if V_FATOR is not null and 1 = 2 then
        V_ALIQ = V_ALIQ * V_FATOR;
        V_ACRESCIMO = TRUE;
        if v_raise is true then
          raise notice '   achou CARZONAVALOR - V_CAR_CERCA_MURO: % - V_FATOR: %', V_CAR_CERCA_MURO, V_FATOR;
        end if;
      end if;
    end if;

    if v_raise is true then
      raise notice 'procurando CARZONAVALOR - V_SIT_CONSTR: %', V_SIT_CONSTR;
    end if;

    if V_ACRESCIMO is false AND (V_SIT_CONSTR IS NOT NULL OR V_SIT_CONSTR > 0) then
      select j72_valor into V_FATOR
      from CARZONAVALOR
      where j72_anousu = ANOUSU
        AND j72_caract = V_SIT_CONSTR
        AND j72_zona = V_ZONA;

      if V_FATOR is not null and 1 = 2 then
        V_ACRESCIMO = TRUE;
        V_ALIQ = V_ALIQ * V_FATOR;
        if v_raise is true then
          raise notice '   achou CARZONAVALOR - V_SIT_CONSTR: % - V_FATOR: %', V_SIT_CONSTR, V_FATOR;
        end if;
      end if;
    end if;

    V_QLIMPEZA := 1;

    V_MANUAL := V_MANUAL || '\n';

    V_MANUAL := V_MANUAL || '\n';

    if v_raise is true then
      raise notice 'IPTU BRUTO %',V_IPTU;
    end if;

    V_IPTU := V_IPTU::float8 - V_VLRISEN::float8;

    V_MANUAL := V_MANUAL || 'VALOR VENAL TOTAL: ' || V_VV || '\n';

    if v_raise is true then
      raise notice 'VVT %,VVC % ',v_vvt, v_vvc;
    end if;

    V_MANUAL := V_MANUAL || 'VALOR VENAL EDIFICACOES: ' || V_VVC || ' - ';

    if v_raise is true then
      raise notice 'ALIQUOTA: %', V_ALIQ;
    end if;

    V_IPTU := ROUND((( V_VVT::float8 + V_VVC::float8 ) * ( V_ALIQ::float8 / 100::float8))::numeric,2)::float8;

    V_MANUAL := V_MANUAL || 'VALOR DO IPTU: ' || V_IPTU || ' - ';

    V_VLRISEN := ROUND((V_IPTU::float8 * ( V_ISENALIQ::float8 / 100::float8))::numeric,2);

    -- VERIFICA ENQUADRAMENTO NA LEI
    -- PARA ISENCAO DE IPTU

    SELECT j46_codigo into V_TESTAISEN
    FROM IPTUISEN INNER JOIN ISENEXE on ISENEXE.j47_codigo = IPTUISEN.j46_codigo
    WHERE IPTUISEN.j46_matric = MATRICULA
      and IPTUISEN.j46_tipo = 1010 and ISENEXE.j47_anousu = ANOUSU;

    if V_TESTAISEN is not null then
       DELETE FROM ISENEXE
       USING IPTUISEN
       WHERE ISENEXE.j47_codigo = IPTUISEN.j46_codigo
         and IPTUISEN.j46_matric = MATRICULA
         and ISENEXE.j47_anousu = ANOUSU
         and IPTUISEN.j46_tipo = 1010;

      delete from IPTUISEN using ISENEXE
      where IPTUISEN.j46_codigo = V_TESTAISEN;

      if v_raise is true then
        raise notice 'deletando registro da IPTUISEN e ISENEXE';
      end if;
    end if;

    if V_TIPOI = 1 then

       V_INDICE_MULTI = 56.24;
   
       select j41_numcgm into V_CGMPRI from promitente where j41_matric = MATRICULA and j41_tipopro is true limit 1;
   
       if V_CGMPRI is null then -- nao tem promitente nessa matricula
         V_CGMPRI =  V_NUMCGM;
       end if;
   
       if v_raise is true then
         raise notice 'Buscando imoveis do cgm %', V_CGMPRI;
       end if;
   
       FOR R_PROPRIS IN
   
         select * from (
         select distinct
   			j01_matric,
   			j01_numcgm,
   			1 as j01_tipo
   			from iptubase
   			left join promitente on j41_matric = j01_matric
   			where j01_baixa is null     and
			      j01_numcgm = V_CGMPRI and
   			      j41_matric is null    and 
			      j01_matric < 90000
         union
         select distinct
   			j42_matric,
   			j42_numcgm,
   			2 as j01_tipo
   			from propri
			inner join iptubase  on j42_matric = j01_matric  
   			left join promitente on j41_matric = j42_matric
   			where j01_baixa is null     and
			      j42_numcgm = V_CGMPRI and
   			      j41_matric is null    and
			      j01_matric < 90000
         union
         select
   			distinct
   			j41_matric,
   			j41_numcgm,
   			3 as j01_tipo
   			from promitente
			inner join iptubase on j41_matric = j01_matric
   			where j01_baixa is null and 
			      j41_numcgm = V_CGMPRI and
		              j01_matric < 90000) as x
         order by j01_tipo desc
         LOOP
   
   			if v_raise is true then
   				raise notice 'tipo %', R_PROPRIS.j01_tipo;
   			end if;
   
         if R_PROPRIS.j01_tipo = 3 then
           if V_CGMPRI = R_PROPRIS.j01_numcgm then
             V_QUANT_IMOVEIS = V_QUANT_IMOVEIS + 1;
           end if;
         else
           if V_CGMPRI = R_PROPRIS.j01_numcgm then
             V_QUANT_IMOVEIS = V_QUANT_IMOVEIS + 1;
           end if;
         end if;
   
       END LOOP;
   
       if v_raise is true then
         raise notice 'V_QUANT_IMOVEIS: % - V_CGMPRI: %', V_QUANT_IMOVEIS, V_CGMPRI;
       end if;
   
       IF V_QUANT_IMOVEIS IS NULL THEN
         V_QUANT_IMOVEIS = 0;
       END IF;
   
       if V_VV <= (V_BASE * V_INDICE_MULTI) and V_QUANT_IMOVEIS = 1 then
          V_VLRISEN = V_IPTU;
          V_ISENALIQ = 100;
   
          SELECT J46_CODIGO into V_TESTAISEN
          FROM IPTUISEN
          INNER JOIN ISENEXE on ISENEXE.j47_codigo = IPTUISEN.j46_codigo
          WHERE IPTUISEN.j46_matric = MATRICULA
            and IPTUISEN.j46_tipo = 1010 and ISENEXE.j47_anousu = anousu;
   
          IF V_TESTAISEN IS NULL THEN
   
            select nextval('iptuisen_j46_codigo_seq') INTO V_CODISEN;
   
            V_DIAINI = TO_CHAR(ANOUSU, '9999') || '-01-01';
            V_DIAFIM = TO_CHAR(ANOUSU, '9999') || '-12-31';
   
            if v_raise is true then
              raise notice 'V_DIAINI: % - V_DIAFIM: %', V_DIAINI, V_DIAFIM;
            end if;
   
            V_CODTIPOISEN = 1010;
   
            if v_raise is true then
              raise notice 'inserindo no iptuisen j46_codigo: %', V_CODISEN;
            end if;
   
            insert into IPTUISEN (j46_codigo, j46_matric, j46_tipo, j46_dtini, j46_dtfim, j46_perc, j46_dtinc, j46_idusu, j46_hist, j46_arealo)
            values
            (V_CODISEN, MATRICULA, V_CODTIPOISEN, V_DIAINI::date, V_DIAFIM::date, 100, now(),1,'ISENTO CFE. LEI 6333 DE 23.11.2021 (CALCULO)',0);
   
            INSERT INTO ISENEXE (j47_codigo, j47_anousu) values (V_CODISEN, ANOUSU);
   
          END IF;
   
       END IF;
    END IF;

    V_MANUAL := V_MANUAL || 'VALOR DA ISENCAO: ' || V_VLRISEN || ' \n ';

    if v_raise is true then
      raise notice 'IPTU: % - ISENCAO: %',V_IPTU,V_VLRISEN;
    end if;

    if v_raise is true then
      raise NOTICE 'VALOR VT % VC % VALOR TOTAL % ',V_VVT,V_VVC,V_VV ;
    end if;

    IF DEMO IS FALSE THEN

      if v_raise is true then
        raise notice 'inserindo em IPTUCALC';
      end if;

      INSERT INTO IPTUCALC VALUES ( ANOUSU,
      MATRICULA,
      V_TESTADA,
      V_AREA_TRIBUT,
      V_FRACAO,
      V_AREATC,
      V_VM2T,
      V_VVT,
      V_ALIQ,
      ROUND(V_VLRISEN,2),
      V_TIPOIMP );

		select j18_iptuhistisen
		  into iHistIptuIsen
		  from cfiptu
		 where j18_anousu = anousu;
		if iHistIptuIsen is null then
			return '18 PARAMETRO DE CONFIGURACAO DO HISTORICO DE ISENCAO DO IPTU NAO CONFIGURADO';
		end if;

      if V_IPTU > 0 then
        -- registra valor iptu
        INSERT INTO IPTUCALV 	(
        j21_anousu,
        j21_matric,
        j21_receit,
        j21_valor,
        j21_quant,
        j21_codhis
        )
        VALUES (ANOUSU,
        MATRICULA,
        V_RECIPTU,
        ROUND(V_IPTU,2),
        V_ALIQ,
        1);
      end if;

      IF V_VLRISEN > 0 THEN
				-- registra valor isencoes
				INSERT INTO IPTUCALV 	(
				j21_anousu,
				j21_matric,
				j21_receit,
				j21_valor,
				j21_quant,
				j21_codhis
				)
				VALUES (
				ANOUSU,
				MATRICULA,
				V_RECIPTU,
				ROUND(V_VLRISEN*-1,2),
				0,
				iHistIptuIsen);
			END IF;

    END IF;

    V_IPTU := V_IPTU - V_VLRISEN;

    -- VERIFICA PARCELAS
    SELECT COUNT(*)
    INTO V_PARCELAS
    FROM CADVENCDESC
    INNER JOIN CADVENC ON Q92_CODIGO = Q82_CODIGO
    WHERE Q92_CODIGO = V_VENCIM ;

    IF V_PARCELAS IS NULL OR V_PARCELAS = 0 THEN
      RETURN '14 PARCELAS NAO CADASTRADAS ';
    END IF;

    if v_raise is true then
      raise notice 'isentaxa: %', V_ISENTAXAS;
    end if;

    IF V_ISENTAXAS = TRUE THEN

      if v_raise is true then
        raise notice 'acessou isentaxas... ';
      end if;

      FOR R_TAXA IN SELECT *
        FROM IPTUTAXA
				     inner join iptucadtaxaexe on j08_tabrec = J19_RECEIT
				                              and j08_anousu = ANOUSU
             LEFT OUTER JOIN ISENTAXA ON J56_CODIGO = V_CODISEN AND J56_RECEIT = J19_RECEIT
        WHERE J19_ANOUSU = ANOUSU
		  LOOP

        if v_raise is true then
          raise notice 'inserindo em IPTUCALC... taxas...';
        end if;

        IF R_TAXA.J19_RECEIT = 13 THEN
          -- registra valor lixo
          V_QUATX  := V_QUATX + 1;
          V_TAXA1  := V_TAXALIXOVAL * V_PARCELAS;
          V_RECTX1 := R_TAXA.J19_RECEIT;
          if v_raise is true then
            raise notice 'rec lixo %',v_taxa1;
          end if;
          IF R_TAXA.J56_PERC != 0 THEN
            if v_raise is true then
              raise notice 'rec taxa1%',v_taxa1;
            end if;
	  				nValIsenTaxa1 := V_TAXA1 - (V_TAXA1 * ( ( 100::FLOAT8-(R_TAXA.J56_PERC) )/100::FLOAT8 ));
--            V_TAXA1 := V_TAXA1 * ((100::FLOAT8-(R_TAXA.J56_PERC))/100::FLOAT8);
            IF V_TAXA1 = 0 OR V_TAXA1 < 0 THEN
              V_TAXA1 = 0;
            END IF;
            if v_raise is true then
              raise notice 'rec taxa1%',v_taxa1;
            end if;
          END IF;

          IF DEMO IS FALSE THEN

            if V_TAXA1 > 0 then
              INSERT INTO IPTUCALV	(
              j21_anousu,
              j21_matric,
              j21_receit,
              j21_valor,
              j21_quant,
              j21_codhis
              )
              VALUES (ANOUSU,
              MATRICULA,
              V_RECTX1,
              ROUND(V_TAXA1,2),
              V_QUATX,
              R_TAXA.j08_iptucalh);
            end if;

						if nValIsenTaxa1 > 0 then
							INSERT INTO IPTUCALV	(	j21_anousu,
																			j21_matric,
																			j21_receit,
																			j21_valor,
																			j21_quant,
																			j21_codhis )
              							 VALUES ( ANOUSU,
																			MATRICULA,
																			V_RECTX1,
																			ROUND((nValIsenTaxa1*-1),2),
																			V_QLIMPEZA,
																			R_TAXA.j08_histisen);
						end if;

          END IF;

        END IF;

      END LOOP;

    END IF;

    -- GERA FINANCEIRO
    IF GERAFINANC = TRUE THEN
      --daa =  current_time;
      if v_raise is true then
        raise notice 'gerando financeiro...';
      end if;

      -- VERIFICA CODIGO DE ARRECADACAO
      SELECT J20_NUMPRE
      INTO V_NUMPRE
      FROM IPTUNUMP
      WHERE J20_ANOUSU = ANOUSU AND J20_MATRIC = MATRICULA;

      IF NOT V_NUMPRE IS NULL THEN
        IF CALCULO_GERAL = FALSE AND DEMO IS FALSE THEN
          if v_raise is true then
            raise notice 'deletando arrebanco, arrematric e arrecad...';
          end if;

          if v_raise is true then
            raise notice ' ';
            raise notice ' ';
            raise notice ' ';
          end if;

          FOR V_RECORD_ARRECAD IN SELECT distinct K00_NUMPAR FROM ARRECAD WHERE K00_NUMPRE = V_NUMPRE ORDER BY k00_numpar LOOP

            if v_raise is true then
              raise notice 'processando parcela: %', V_RECORD_ARRECAD.K00_NUMPAR;
            end if;
						IF PARCELAFIM > 0 THEN
							IF V_RECORD_ARRECAD.K00_NUMPAR >= PARCELAINI AND V_RECORD_ARRECAD.K00_NUMPAR <= PARCELAFIM THEN
								if v_raise is true then
									raise notice '   deletando do arrecad parcela: %', V_RECORD_ARRECAD.K00_NUMPAR;
								end if;
								DELETE FROM ARRECAD WHERE K00_NUMPRE = V_NUMPRE AND K00_NUMPAR = V_RECORD_ARRECAD.K00_NUMPAR;
							END IF;
						ELSE
							IF V_RECORD_ARRECAD.K00_NUMPAR >= PARCELAINI THEN
								if v_raise is true then
									raise notice '   deletando do arrecad parcela: %', V_RECORD_ARRECAD.K00_NUMPAR;
								end if;
								DELETE FROM ARRECAD WHERE K00_NUMPRE = V_NUMPRE AND K00_NUMPAR = V_RECORD_ARRECAD.K00_NUMPAR;
							END IF;
            END IF;

          END LOOP;

          if v_raise is true then
            raise notice ' ';
            raise notice ' ';
            raise notice ' ';
          end if;

        END IF;

        IF NOVONUMPRE = FALSE THEN
          V_MESMONUMPRE = TRUE;
        ELSE
          IF V_TEMPAGAMENTO = FALSE THEN
            IF CALCULO_GERAL = FALSE AND DEMO IS FALSE THEN
              if v_raise is true then
                raise notice 'deletando iptunump...';
              end if;
              DELETE FROM IPTUNUMP WHERE J20_ANOUSU = ANOUSU AND J20_MATRIC = MATRICULA;
            END IF;
            IF DEMO IS FALSE THEN
              SELECT NEXTVAL('numpref_k03_numpre_seq')::INTEGER
              INTO V_NUMPRE;
            END IF;
          END IF;
        END IF;
      ELSE
        IF DEMO IS FALSE THEN
          SELECT NEXTVAL('numpref_k03_numpre_seq')::INTEGER
          INTO V_NUMPRE;
        END IF;
      END IF;
      -- se imune sai
      IF NOT V_TIPOIS IS NULL THEN
        IF V_TIPOIS = 1 THEN
          RETURN '15 MATRICULA IMUNE';
        END IF;
      END IF;
      -- verifica taxas
      V_SOMA := 0;

      if v_raise is true then
        raise notice 'antes dos vencimentos';
      end if;

      V_MANUAL := V_MANUAL || 'PARCELA INICIAL BASEADA NOS PAGAMENTOS: ' || V_PARINI || ' \n ';

      V_TEXT = 'SELECT *
      FROM CADVENCDESC
      INNER JOIN CADVENC        	ON Q92_CODIGO = Q82_CODIGO
      LEFT  JOIN CADVENCDESCBAN 	ON Q93_CODIGO = Q92_CODIGO
      LEFT  JOIN CADBAN		ON K15_CODIGO = Q93_CADBAN
      WHERE Q92_CODIGO = ' || V_VENCIM || ' ORDER BY Q82_PARC';

      if v_raise is true then
        raise notice 'V_TEXT: %', V_TEXT;
      end if;

      V_PASSA = TRUE;

      FOR R_VENCIM IN EXECUTE V_TEXT LOOP
        --daa = current_time;
        if v_raise is true then
          raise notice 'gerando parcelas % pelo cadvencdesc - venc: %',R_VENCIM.Q82_PARC, R_VENCIM.Q82_VENC;
        end if;
        -- VERIFICA SE PARCELA INICIAL ESTA CERTA
        if v_raise is true then
          raise NOTICE 'INICIA % PARCELA % ',V_PARINI,R_VENCIM.Q82_PARC;
        end if;

        lProcessarArrecad := true;

        --
        -- Se parcela ja esta paga ou cancelada passa para a proxima
        --

        if V_NUMPRE is not null then
          perform * from fc_statusdebitos(V_NUMPRE,R_VENCIM.Q82_PARC) where rtstatus = 'PAGO' or rtstatus = 'CANLELADO' limit 1;
          if found then
            lProcessarArrecad := false;
          end if;
        end if;

        IF PARCELAINI = 0 THEN
          V_PASSA = TRUE;
        ELSE
          IF R_VENCIM.Q82_PARC >= PARCELAINI AND R_VENCIM.Q82_PARC <= PARCELAFIM THEN
            V_PASSA = TRUE;
          ELSE
            V_PASSA = FALSE;
          END IF;
        END IF;

        if v_raise is true then
          raise notice 'processando parcela: % - V_PASSA: %', R_VENCIM.Q82_PARC, V_PASSA;
        end if;

        IF V_PASSA IS TRUE THEN

          if v_raise is true then
            raise notice 'V_IPTU: % - V_TAXA1: % - V_TAXA2: %', V_IPTU, V_TAXA1, V_TAXA2;
          end if;
          IF V_IPTU > 0 THEN
            V_VALORPAR := ROUND(V_IPTU * ( R_VENCIM.Q82_PERC / 100),2)::FLOAT8;
            if V_MOSTRAR IS TRUE THEN
              if v_raise is true then
                raise notice 'soma % vparc % parcela % v_parcelas %',v_soma,v_valorpar,r_vencim.q82_parc, V_PARCELAS;
              end if;
            end if;
            IF V_PARCELAS = R_VENCIM.Q82_PARC THEN
              V_VALORPAR := ROUND(V_IPTU - V_DIFIPTU - V_SOMA,2)::FLOAT8;
            END IF;
            V_SOMA := V_SOMA + V_VALORPAR;
            V_TEMFINANC = TRUE;
            V_DIGITO := FC_DIGITO(V_NUMPRE,R_VENCIM.Q82_PARC,V_PARCELAS);
            IF DEMO IS FALSE THEN
              if V_MOSTRAR IS TRUE THEN
                if v_raise is true then
                  raise notice 'CGM: % - DTOPER: % - REC: % - HIST: %',V_NUMCGM,V_DTOPER,V_RECIPTU,R_VENCIM.Q82_HIST;
                  raise notice 'VALOR: % - VENC: % - NUMPRE: % - PARC: %',V_VALORPAR,R_VENCIM.Q82_VENC,V_NUMPRE,R_VENCIM.Q82_PARC;
                  raise notice 'PARC: % - DIGITO: % - TIPO: %',V_PARCELAS,V_DIGITO,R_VENCIM.Q92_TIPO;
                end if;
              end if;

              if lProcessarArrecad then
                 INSERT INTO ARRECAD (K00_NUMCGM,K00_DTOPER,K00_RECEIT,K00_HIST,K00_VALOR,K00_DTVENC,K00_NUMPRE,K00_NUMPAR,K00_NUMTOT,K00_NUMDIG,K00_TIPO)
                 VALUES( V_NUMCGM,
                 V_DTOPER,
                 V_RECIPTU,
                 R_VENCIM.Q82_HIST,
                 V_VALORPAR,
                 R_VENCIM.Q82_VENC,
                 V_NUMPRE,
                 R_VENCIM.Q82_PARC,
                 V_PARCELAS,
                 V_DIGITO,
                 R_VENCIM.Q92_TIPO);
              end if;
            END IF;
            V_TOTALZAO = V_TOTALZAO + V_VALORPAR;
          END IF;
          IF V_TAXA1 > 0 THEN
            V_VALORTX1 := ROUND( (V_TAXA1-coalesce(nValIsenTaxa1,0) ) *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            IF V_PARCELAS = R_VENCIM.Q82_PARC THEN
              V_VALORTX1 := ROUND(V_TAXA1 - V_DIFTX1 - V_SOMATX1 - coalesce(nValIsenTaxa1,0),2)::FLOAT8;
            END IF;
            V_SOMATX1 := V_SOMATX1 + V_VALORTX1;

            V_TEMFINANC = TRUE;
            IF DEMO IS FALSE THEN
              if V_MOSTRAR IS TRUE THEN
                if v_raise is true then
                  raise notice 'CGM: % - DTOPER: % - REC: % - HIST: % - VALOR: % - VENC: % - NUMPRE: % - PARC: % - PARC: % - DIGITO: % - TIPO: %',V_NUMCGM,V_DTOPER,V_RECTX1,R_VENCIM.Q82_HIST,V_VALORTX1,R_VENCIM.Q82_VENC,V_NUMPRE,R_VENCIM.Q82_PARC,V_PARCELAS,V_DIGITO,R_VENCIM.Q92_TIPO;
                end if;
              end if;
              if lProcessarArrecad then
                INSERT INTO ARRECAD (K00_NUMCGM,K00_DTOPER,K00_RECEIT,K00_HIST,K00_VALOR,K00_DTVENC,K00_NUMPRE,K00_NUMPAR,K00_NUMTOT,K00_NUMDIG,K00_TIPO)
                VALUES(V_NUMCGM,
                 V_DTOPER,
                 V_RECTX1,
                 R_VENCIM.Q82_HIST,
                 V_VALORTX1,
                 R_VENCIM.Q82_VENC,
                 V_NUMPRE,
                 R_VENCIM.Q82_PARC,
                 V_PARCELAS,
                 V_DIGITO,
                 R_VENCIM.Q92_TIPO);
              end if;
            END IF;
            V_TOTALZAO = V_TOTALZAO + V_VALORTX1;
          END IF;
          IF V_TAXA2 > 0 THEN
            V_VALORTX2 := ROUND(V_TAXA2 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            IF V_PARCELAS = R_VENCIM.Q82_PARC THEN
              V_VALORTX2 := ROUND(V_TAXA2 - V_DIFTX2 - V_SOMATX2,2)::FLOAT8;
            END IF;
            V_SOMATX2 := V_SOMATX2 + V_VALORTX2;

            V_TEMFINANC = TRUE;
            IF DEMO IS FALSE THEN
              if V_MOSTRAR IS TRUE THEN
                if v_raise is true then
                  raise notice 'CGM: % - DTOPER: % - REC: % - HIST: % - VALOR: % - VENC: % - NUMPRE: % - PARC: % - PARC: % - DIGITO: % - TIPO: %',V_NUMCGM,V_DTOPER,V_RECTX2,R_VENCIM.Q82_HIST,V_VALORTX2,R_VENCIM.Q82_VENC,V_NUMPRE,R_VENCIM.Q82_PARC,V_PARCELAS,V_DIGITO,R_VENCIM.Q92_TIPO;
                end if;
              end if;
              if lProcessarArrecad then
                INSERT INTO ARRECAD (K00_NUMCGM,K00_DTOPER,K00_RECEIT,K00_HIST,K00_VALOR,K00_DTVENC,K00_NUMPRE,K00_NUMPAR,K00_NUMTOT,K00_NUMDIG,K00_TIPO)
                VALUES(V_NUMCGM,
                V_DTOPER,
                V_RECTX2,
                R_VENCIM.Q82_HIST,
                V_VALORTX2,
                R_VENCIM.Q82_VENC,
                V_NUMPRE,
                R_VENCIM.Q82_PARC,
                V_PARCELAS,
                V_DIGITO,
                R_VENCIM.Q92_TIPO);
              end if;
            END IF;
            V_TOTALZAO = V_TOTALZAO + V_VALORTX2;
          END IF;
          IF V_TAXA3 > 0 THEN
            V_VALORTX3 := ROUND(V_TAXA3 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            IF V_PARCELAS = R_VENCIM.Q82_PARC THEN
              V_VALORTX3 := ROUND(V_TAXA3 - V_DIFTX3 - V_SOMATX3,2)::FLOAT8;
            END IF;
            V_SOMATX3 := V_SOMATX3 + V_VALORTX3;

            V_TEMFINANC = TRUE;
            IF DEMO IS FALSE THEN
              if V_MOSTRAR IS TRUE THEN
                if v_raise is true then
                  raise notice 'CGM: % - DTOPER: % - REC: % - HIST: % - VALOR: % - VENC: % - NUMPRE: % - PARC: % - PARC: % - DIGITO: % - TIPO: %',V_NUMCGM,V_DTOPER,V_RECTX3,R_VENCIM.Q82_HIST,V_VALORTX3,R_VENCIM.Q82_VENC,V_NUMPRE,R_VENCIM.Q82_PARC,V_PARCELAS,V_DIGITO,R_VENCIM.Q92_TIPO;
                end if;
              end if;
              if lProcessarArrecad then
                 INSERT INTO ARRECAD (K00_NUMCGM,K00_DTOPER,K00_RECEIT,K00_HIST,K00_VALOR,K00_DTVENC,K00_NUMPRE,K00_NUMPAR,K00_NUMTOT,K00_NUMDIG,K00_TIPO)
                 VALUES (V_NUMCGM,
                 V_DTOPER,
                 V_RECTX3,
                 R_VENCIM.Q82_HIST,
                 V_VALORTX3,
                 R_VENCIM.Q82_VENC,
                 V_NUMPRE,
                 R_VENCIM.Q82_PARC,
                 V_PARCELAS,
                 V_DIGITO,
                 R_VENCIM.Q92_TIPO);
              end if;
            END IF;
            V_TOTALZAO = V_TOTALZAO + V_VALORTX3;
          END IF;
          IF V_TAXA4 > 0 THEN
            V_VALORTX4 := ROUND(V_TAXA4 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            IF V_PARCELAS = R_VENCIM.Q82_PARC THEN
              V_VALORTX4 := ROUND(V_TAXA4 - V_DIFTX4 - V_SOMATX4,2)::FLOAT8;
            END IF;
            V_SOMATX4 := V_SOMATX4 + V_VALORTX4;

            V_TEMFINANC = TRUE;
            IF DEMO IS FALSE THEN
              if V_MOSTRAR IS TRUE THEN
                if v_raise is true then
                  raise notice 'CGM: % - DTOPER: % - REC: % - HIST: % - VALOR: % - VENC: % - NUMPRE: % - PARC: % - PARC: % - DIGITO: % - TIPO: %',V_NUMCGM,V_DTOPER,V_RECTX4,R_VENCIM.Q82_HIST,V_VALORTX4,R_VENCIM.Q82_VENC,V_NUMPRE,R_VENCIM.Q82_PARC,V_PARCELAS,V_DIGITO,R_VENCIM.Q92_TIPO;
                end if;
              end if;
              if lProcessarArrecad then
                 INSERT INTO ARRECAD (K00_NUMCGM,K00_DTOPER,K00_RECEIT,K00_HIST,K00_VALOR,K00_DTVENC,K00_NUMPRE,K00_NUMPAR,K00_NUMTOT,K00_NUMDIG,K00_TIPO)
                 VALUES (V_NUMCGM,
                 V_DTOPER,
                 V_RECTX4,
                 R_VENCIM.Q82_HIST,
                 V_VALORTX4,
                 R_VENCIM.Q82_VENC,
                 V_NUMPRE,
                 R_VENCIM.Q82_PARC,
                 V_PARCELAS,
                 V_DIGITO,
                 R_VENCIM.Q92_TIPO);
              end if;
            END IF;
            V_TOTALZAO = V_TOTALZAO + V_VALORTX4;
          END IF;
          IF V_TAXA5 > 0 THEN
            V_VALORTX5 := ROUND(V_TAXA5 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            IF V_PARCELAS = R_VENCIM.Q82_PARC THEN
              V_VALORTX5 := ROUND(V_TAXA5 - V_DIFTX5 - V_SOMATX5,2)::FLOAT8;
            END IF;
            V_SOMATX5 := V_SOMATX5 + V_VALORTX5;
            V_TEMFINANC = TRUE;
            IF DEMO IS FALSE THEN
              if V_MOSTRAR IS TRUE THEN
                if v_raise is true then
                  raise notice 'CGM: % - DTOPER: % - REC: % - HIST: % - VALOR: % - VENC: % - NUMPRE: % - PARC: % - PARC: % - DIGITO: % - TIPO: %',V_NUMCGM,V_DTOPER,V_RECTX5,R_VENCIM.Q82_HIST,V_VALORTX5,R_VENCIM.Q82_VENC,V_NUMPRE,R_VENCIM.Q82_PARC,V_PARCELAS,V_DIGITO,R_VENCIM.Q92_TIPO;
                end if;
              end if;
              if lProcessarArrecad then
                INSERT INTO ARRECAD (K00_NUMCGM,K00_DTOPER,K00_RECEIT,K00_HIST,K00_VALOR,K00_DTVENC,K00_NUMPRE,K00_NUMPAR,K00_NUMTOT,K00_NUMDIG,K00_TIPO)
                VALUES (V_NUMCGM,
                V_DTOPER,
                V_RECTX5,
                R_VENCIM.Q82_HIST,
                V_VALORTX5,
                R_VENCIM.Q82_VENC,
                V_NUMPRE,
                R_VENCIM.Q82_PARC,
                V_PARCELAS,
                V_DIGITO,
                R_VENCIM.Q92_TIPO);
              end if;
            END IF;
            V_TOTALZAO = V_TOTALZAO + V_VALORTX5;
          END IF;

        ELSE

          IF V_IPTU > 0 THEN
            V_VALORPAR := ROUND(V_IPTU * ( R_VENCIM.Q82_PERC / 100),2)::FLOAT8;
            V_DIFIPTU := V_DIFIPTU + V_VALORPAR;
          END IF;

          IF V_TAXA1 > 0 THEN
            V_VALORTX1 := ROUND(V_TAXA1 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            V_DIFTX1 := V_DIFTX1 + V_VALORTX1;
          END IF;

          IF V_TAXA2 > 0 THEN
            V_VALORTX2 := ROUND(V_TAXA2 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            V_DIFTX2 := V_DIFTX2 + V_VALORTX2;
          END IF;

          IF V_TAXA3 > 0 THEN
            V_VALORTX3 := ROUND(V_TAXA3 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            V_DIFTX3 := V_DIFTX3 + V_VALORTX3;
          END IF;

          IF V_TAXA4 > 0 THEN
            V_VALORTX4 := ROUND(V_TAXA4 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            V_DIFTX4 := V_DIFTX4 + V_VALORTX4;
          END IF;

          IF V_TAXA5 > 0 THEN
            V_VALORTX5 := ROUND(V_TAXA5 *( R_VENCIM.Q82_PERC / 100 ),2)::FLOAT8;
            V_DIFTX5 := V_DIFTX5 + V_VALORTX5;
          END IF;

        END IF;

      END LOOP;

      if v_raise is true then
        raise notice 'depois dos vencimentos';
      end if;

      IF V_TEMFINANC = TRUE THEN
        IF DEMO IS FALSE THEN
          SELECT K00_NUMPRE
          INTO V_NUMPREEXISTE
          FROM ARREMATRIC
          WHERE K00_NUMPRE = V_NUMPRE AND
          K00_MATRIC = MATRICULA;
          IF V_NUMPREEXISTE IS NULL THEN
            INSERT INTO ARREMATRIC VALUES ( V_NUMPRE,
            MATRICULA );
          END IF;
        END IF;
        IF V_MESMONUMPRE = FALSE AND DEMO IS FALSE THEN
          INSERT INTO IPTUNUMP VALUES ( ANOUSU,
          MATRICULA,
          V_NUMPRE );
        END IF;

        if DEMO is false then

          for rRecibosGerados in select distinct recibopaga.k00_numnov,
                                        recibopaga.k00_dtoper,
                                        recibopaga.k00_dtpaga
                                   from arrecad
                                        inner join recibopaga on recibopaga.k00_numpre = arrecad.k00_numpre
                                                             and recibopaga.k00_numpar = arrecad.k00_numpar
                                  where arrecad.k00_numpre = V_NUMPRE
          loop

            delete from recibopaga where k00_numnov = rRecibosGerados.k00_numnov;
            perform fc_recibo(rRecibosGerados.k00_numnov, rRecibosGerados.k00_dtoper, rRecibosGerados.k00_dtpaga, extract(year from rRecibosGerados.k00_dtpaga)::integer);
          end loop;

        end if;

      END IF;

    END IF;

    V_MANUAL := V_MANUAL || 'VALOR TOTAL A PAGAR: ' || V_TOTALZAO || '\n';

    IF DEMO IS FALSE THEN

      perform * from IPTUCALC where J23_MATRIC = MATRICULA AND J23_ANOUSU = ANOUSU;
      if found then
        UPDATE IPTUCALC SET J23_MANUAL = V_MANUAL WHERE J23_MATRIC = MATRICULA AND J23_ANOUSU = ANOUSU;
      else
        RETURN V_MANUAL;
      end if;

    END IF;

    IF DEMO IS TRUE THEN
      ----      V_MANUAL := '9 CALCULO CONCLUIDO';
      RETURN V_MANUAL;
    ELSE

      if v_raise is true then
        raise notice 'matricula: %',MATRICULA;
        raise notice 'demo: %',DEMO;
        raise notice 'Demonstrativo : %', V_MANUAL;
      end if;


      RETURN '01 CALCULO CONCLUIDO - MATRICULA '||MATRICULA;
    END IF;

    END;

$$ language 'plpgsql';

SQL
    );
    }
}
