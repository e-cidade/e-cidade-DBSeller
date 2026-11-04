<?php
/**
 * Caso essa Migration dê algum erro em uma base de Nova Friburgo
 * visando que ela foi criada para uma migração de uma rotina
 * vinda deles, recomendo comentar o três primeiros métodos
 * da "up()", sendo que a rotina que eles não tem na base deles
 * só será chamado pelo método "adicionarParametrosEscola()".
 * 
 */

use App\Domain\Educacao\Escola\Models\ParametrosBloqueioNota;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M28977CriacaoEstruturaNotasParciais extends Migration
{
    var $existTabelasNotasParciais = false;
    var $existProcedimentoParamentros = false;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->checkSchema();

        if ($this->existTabelasNotasParciais == false) {
          $this->criarNovasTabelas();
          $this->criarDicionario();
        }

        if ($this->existProcedimentoParamentros == false) {
          $this->adicionarRotinaParametros();
        }

        $this->adicionarParametrosEscola();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('escola.resultadoparcial');
        Schema::drop('escola.procresultadoparcial');
        Schema::drop('escola.avaliacaoparcial');
        Schema::drop('escola.procavaliacaoparcial');

        $this->downRotinaParametros();
        $this->droparParametrosEscola();
    }

    public function checkSchema()
    {
      //Checar esquema Notas parciais
      $schemaNotas = DB::select("
            select table_name
            from information_schema.tables
            where	table_schema = 'escola'
              and table_name = 'resultadoparcial'
      ");

      if (count($schemaNotas) > 0) {
        $this->existTabelasNotasParciais = true;
      } else {
        $this->existTabelasNotasParciais = false;
      }

      //Checar esquemas parametros de bloqueio
      $schemaParamentro = DB::select("
            select table_name
            from information_schema.tables
            where table_schema = 'escola'
              and table_name = 'parametrosbloqueionota'
      ");

      if(count($schemaParamentro) > 0) {
        $this->existProcedimentoParamentros = true;
      } else {
        $this->existProcedimentoParamentros = false;
      }
    }

    public function criarNovasTabelas()
    {
        Schema::create('escola.procavaliacaoparcial', function (Blueprint $table) {
            $table->bigIncrements('ed340_codigo');
            $table->bigInteger('ed340_regencia');
            $table->bigInteger('ed340_procavaliacao');
            $table->integer('ed340_ordem');

            $table->foreign('ed340_regencia')
                ->references('ed59_i_codigo')
                ->on('escola.regencia');

            $table->foreign('ed340_procavaliacao')
                ->references('ed41_i_codigo')
                ->on('escola.procavaliacao');
        });

        Schema::create('escola.avaliacaoparcial', function (Blueprint $table) {
            $table->bigIncrements('ed341_codigo');
            $table->bigInteger('ed341_diario');
            $table->bigInteger('ed341_procavaliacaoparcial');
            $table->float('ed341_valornota')->nullable()->default(null);
            $table->string('ed341_valornivel');

            $table->foreign('ed341_diario')
                ->references('ed95_i_codigo')
                ->on('escola.diario');

            $table->foreign('ed341_procavaliacaoparcial')
                ->references('ed340_codigo')
                ->on('escola.procavaliacaoparcial')
                ->onDelete('cascade');

            $table->unique(['ed341_diario', 'ed341_procavaliacaoparcial']);
        });

        Schema::create('escola.procresultadoparcial', function (Blueprint $table) {
            $table->bigIncrements('ed342_codigo');
            $table->bigInteger('ed342_regencia');
            $table->bigInteger('ed342_procavaliacao');
            $table->string('ed342_formaobtencao', 2);
            $table->string('ed342_formaobtencaofinal', 2)->default('MN');

            $table->foreign('ed342_regencia')
                ->references('ed59_i_codigo')
                ->on('escola.regencia');

            $table->foreign('ed342_procavaliacao')
                ->references('ed41_i_codigo')
                ->on('escola.procavaliacao');

            $table->unique(['ed342_regencia', 'ed342_procavaliacao']);
        });

        Schema::create('escola.resultadoparcial', function (Blueprint $table) {
            $table->bigIncrements('ed343_codigo');
            $table->bigInteger('ed343_diario');
            $table->bigInteger('ed343_procresultadoparcial');
            $table->float('ed343_valornota')->nullable()->default(null);
            $table->string('ed343_valornivel');
            $table->string('ed343_observacao')->nullable();
            $table->float('ed343_recuperacaonota')->nullable()->default(null);
            $table->string('ed343_recuperacaonivel')->default('');
            $table->boolean('ed343_encerrado')->default(false);

            $table->foreign('ed343_diario')
                ->references('ed95_i_codigo')
                ->on('escola.diario');

            $table->foreign('ed343_procresultadoparcial')
                ->references('ed342_codigo')
                ->on('escola.procresultadoparcial');

            $table->unique(['ed343_diario', 'ed343_procresultadoparcial']);
        });
    }

    public function criarDicionario()
    {
        // Habilitando triggers
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
        DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

        $this->dicionarioTabelaProcavalicacaoparcial();
        $this->dicionarioTabelaAvaliacaoparcial();
        $this->dicionarioTabelaProcresultadoparcial();
        $this->dicionarioTabelaResultadoparcial();

        $this->selectDicionario();
    }

    public function dicionarioTabelaProcavalicacaoparcial()
    {
        //Tabela
        DB::statement("COMMENT ON TABLE escola.procavaliacaoparcial IS '{
            \"descricao\": \"Guarda os procedimentos de avalicao parcial\",
            \"sigla\": \"ed340\",
            \"dataincl\": \"2024-10-04\",
            \"rotulo\": \"procavaliacaoparcial\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
          }'"
        );

        //Colunas
        DB::statement("COMMENT ON COLUMN escola.procavaliacaoparcial.ed340_codigo IS '{
            \"descricao\": \"Serial da tabela\",
            \"rotulo\": \"ed340_codigo\",
            \"rotulorel\": \"ed340_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.procavaliacaoparcial.ed340_regencia IS '{
            \"descricao\": \"Numero da regencia\",
            \"rotulo\": \"ed340_regencia\",
            \"rotulorel\": \"ed340_regencia\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 0,
            \"tamanho\": 25,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.procavaliacaoparcial.ed340_procavaliacao IS '{
            \"descricao\": \"Numero do procedimento de avaliacao\",
            \"rotulo\": \"ed340_procavaliacao\",
            \"rotulorel\": \"ed340_procavaliacao\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 0,
            \"tamanho\": 25,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.procavaliacaoparcial.ed340_ordem IS '{
            \"descricao\": \"Ordem do procedimento\",
            \"rotulo\": \"ed340_ordem\",
            \"rotulorel\": \"ed340_ordem\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 0,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
          }'"
        );
    }

    public function dicionarioTabelaAvaliacaoparcial() 
    {
        //Tabela
        DB::statement("COMMENT ON TABLE escola.avaliacaoparcial IS '{
            \"descricao\": \"Vinculo com a rotina de notas parciais e suas notas\",
            \"sigla\": \"ed341\",
            \"dataincl\": \"2024-10-04\",
            \"rotulo\": \"avaliacaoparcial\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
          }'"
        );

        //Colunas
        DB::statement("COMMENT ON COLUMN escola.avaliacaoparcial.ed341_codigo IS '{
            \"descricao\": \"Serial da tabela\",
            \"rotulo\": \"ed341_codigo\",
            \"rotulorel\": \"ed341_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.avaliacaoparcial.ed341_diario IS '{
            \"descricao\": \"Referencia ao diario\",
            \"rotulo\": \"ed341_diario\",
            \"rotulorel\": \"ed341_diario\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 20,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.avaliacaoparcial.ed341_procavaliacaoparcial IS '{
            \"descricao\": \"Referencia com a tabela procavaliacaoparcial\",
            \"rotulo\": \"ed341_procavaliacaoparcial\",
            \"rotulorel\": \"ed341_procavaliacaoparcial\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 20,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.avaliacaoparcial.ed341_valornota IS '{
            \"descricao\": \"Valor da nota\",
            \"rotulo\": \"ed341_valornota\",
            \"rotulorel\": \"ed341_valornota\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 0,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.avaliacaoparcial.ed341_valornivel IS '{
            \"descricao\": \"Valor do nivel\",
            \"rotulo\": \"ed341_valornivel\",
            \"rotulorel\": \"ed341_valornivel\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 0,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
          }'"
        );
    }

    public function dicionarioTabelaProcresultadoparcial()
    {
        //Tabela
        DB::statement("COMMENT ON TABLE escola.procresultadoparcial IS '{
            \"descricao\": \"Informacoes do procedimentos do resultado parcial\",
            \"sigla\": \"ed342\",
            \"dataincl\": \"2024-10-04\",
            \"rotulo\": \"procresultadoparcial\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
          }'"
        );

        //Colunas
        DB::statement("COMMENT ON COLUMN escola.procresultadoparcial.ed342_codigo IS '{
            \"descricao\": \"Serial da tabela\",
            \"rotulo\": \"ed342_codigo\",
            \"rotulorel\": \"ed342_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.procresultadoparcial.ed342_regencia IS '{
            \"descricao\": \"Referencia a regencia\",
            \"rotulo\": \"ed342_regencia\",
            \"rotulorel\": \"ed342_regencia\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 20,
            \"tipoobj\": \"text\"
          }'"
        );
        
        DB::statement("COMMENT ON COLUMN escola.procresultadoparcial.ed342_procavaliacao IS '{
            \"descricao\": \"Referencia ao procedimento de avaliacao\",
            \"rotulo\": \"ed342_procavaliacao\",
            \"rotulorel\": \"ed342_procavaliacao\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 20,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.procresultadoparcial.ed342_formaobtencao IS '{
            \"descricao\": \"Descricao da forma de obtencao\",
            \"rotulo\": \"ed342_formaobtencao\",
            \"rotulorel\": \"ed342_formaobtencao\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 0,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.procresultadoparcial.ed342_formaobtencaofinal IS '{
            \"descricao\": \"Descricao da forma de obtencao da nota final\",
            \"rotulo\": \"ed342_formaobtencaofinal\",
            \"rotulorel\": \"ed342_formaobtencaofinal\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 0,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
          }'"
        );
    }

    public function dicionarioTabelaResultadoparcial()
    {
        //Tabela
        DB::statement("COMMENT ON TABLE escola.resultadoparcial IS '{
            \"descricao\": \"Registros dos resultados parciais\",
            \"sigla\": \"ed343\",
            \"dataincl\": \"2024-10-04\",
            \"rotulo\": \"resultadoparcial\",
            \"tipotabela\": 0,
            \"naolibclass\": false,
            \"naolibfunc\": false,
            \"naolibprog\": false,
            \"naolibform\": false
          }'"
        );

        //Colunas
        DB::statement("COMMENT ON COLUMN escola.resultadoparcial.ed343_codigo IS '{
            \"descricao\": \"Serial da tabela\",
            \"rotulo\": \"ed343_codigo\",
            \"rotulorel\": \"ed343_codigo\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 10,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.resultadoparcial.ed343_diario IS '{
            \"descricao\": \"Referencia ao diario\",
            \"rotulo\": \"ed343_diario\",
            \"rotulorel\": \"ed343_diario\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 15,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.resultadoparcial.ed343_procresultadoparcial IS '{
            \"descricao\": \"Referencia ao procedimento resultado parcial\",
            \"rotulo\": \"ed343_procresultadoparcial\",
            \"rotulorel\": \"ed343_procresultadoparcial\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 1,
            \"tamanho\": 15,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.resultadoparcial.ed343_valornota IS '{
            \"descricao\": \"Valor da nota\",
            \"rotulo\": \"ed343_valornota\",
            \"rotulorel\": \"ed343_valornota\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 0,
            \"tamanho\": 15,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.resultadoparcial.ed343_valornivel IS '{
            \"descricao\": \"Valor do nivel\",
            \"rotulo\": \"ed343_valornivel\",
            \"rotulorel\": \"ed343_valornivel\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 0,
            \"tamanho\": 255,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.resultadoparcial.ed343_observacao IS '{
            \"descricao\": \"Observacao\",
            \"rotulo\": \"ed343_observacao\",
            \"rotulorel\": \"ed343_observacao\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 0,
            \"tamanho\": 255,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.resultadoparcial.ed343_recuperacaonota IS '{
            \"descricao\": \"Nota de recuperacao\",
            \"rotulo\": \"ed343_recuperacaonota\",
            \"rotulorel\": \"ed343_recuperacaonota\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 0,
            \"tamanho\": 15,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.resultadoparcial.ed343_recuperacaonivel IS '{
            \"descricao\": \"Nivel da recuperacao\",
            \"rotulo\": \"ed343_recuperacaonivel\",
            \"rotulorel\": \"ed343_recuperacaonivel\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 0,
            \"tamanho\": 255,
            \"tipoobj\": \"text\"
          }'"
        );

        DB::statement("COMMENT ON COLUMN escola.resultadoparcial.ed343_encerrado IS '{
            \"descricao\": \"Nivel da recuperacao\",
            \"rotulo\": \"ed343_encerrado\",
            \"rotulorel\": \"ed343_encerrado\",
            \"maiusculo\": false,
            \"autocompl\": false,
            \"aceitatipo\": 5,
            \"tamanho\": 10,
            \"tipoobj\": \"checkbox\"
          }'"
        );
    }

    public function selectDicionario()
    {
DB::unprepared(<<<SQL
SELECT fc_gera_dicionario_apartir_tabela('escola', 'procavaliacaoparcial');
SELECT fc_gera_dicionario_apartir_tabela('escola', 'avaliacaoparcial');
SELECT fc_gera_dicionario_apartir_tabela('escola', 'procresultadoparcial');
SELECT fc_gera_dicionario_apartir_tabela('escola', 'resultadoparcial');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
      );
    }

    public function adicionarRotinaParametros()
    {
      $this->criarItensMenu();
      $this->criarNovasTabelasParametros();
      $this->criarDicionarioParametros();
    }

    public function criarNovasTabelasParametros()
    {
      Schema::create('escola.parametrosbloqueionota', function (Blueprint $table) {
        $table->bigIncrements('ed362_codigo');
        $table->integer('ed362_tipobloqueio');
        $table->integer('ed362_prazodias')->nullable();
        $table->timestamps();
      });

      Schema::create('escola.excecoesbloqueionota', function (Blueprint $table) {
          $table->bigIncrements('ed363_codigo');
          $table->integer('ed363_turma');
          $table->integer('ed363_regencia')->nullable();
          $table->integer('ed363_periodoavaliacao');
          $table->date('ed363_datalimite');
          $table->boolean('ed363_ativo')->default(true);
          $table->integer('ed363_usuario');
          $table->timestamps();

          $table->foreign('ed363_turma')->references('ed57_i_codigo')->on('escola.turma');
          $table->foreign('ed363_regencia')->references('ed59_i_codigo')->on('escola.regencia');
          $table->foreign('ed363_periodoavaliacao')->references('ed09_i_codigo')->on('escola.periodoavaliacao');
          $table->foreign('ed363_usuario')->references('id_usuario')->on('configuracoes.db_usuarios');
      });

      $parametrosBloqueioNota = new ParametrosBloqueioNota();
      $parametrosBloqueioNota->ed362_tipobloqueio = ParametrosBloqueioNota::SEM_BLOQUEIO;
      $parametrosBloqueioNota->ed362_prazodias = 0;
      $parametrosBloqueioNota->save();
    }

    public function criarDicionarioParametros()
    {
      // Habilitando triggers
      DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
      DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

      $this->dicionarioTabelaParametrosbloqueionota();
      $this->dicionarioTabeloaExcecoesbloqueionota();

      $this->selectDicionarioParametros();
    }

    public function selectDicionarioParametros()
    {
      DB::unprepared(<<<SQL
SELECT fc_gera_dicionario_apartir_tabela('escola', 'parametrosbloqueionota');
SELECT fc_gera_dicionario_apartir_tabela('escola', 'excecoesbloqueionota');

ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL
      );
    }

    public function dicionarioTabelaParametrosbloqueionota()
    {
      //Tabela
      DB::statement("COMMENT ON TABLE escola.parametrosbloqueionota IS '{
          \"descricao\": \"Parametros para o bloqueios da rotina de notas parciais\",
          \"sigla\": \"ed362\",
          \"dataincl\": \"2024-10-08\",
          \"rotulo\": \"parametrosbloqueionota\",
          \"tipotabela\": 1,
          \"naolibclass\": false,
          \"naolibfunc\": false,
          \"naolibprog\": false,
          \"naolibform\": false
        }'"
      );

      //Colunas
      DB::statement("COMMENT ON COLUMN escola.parametrosbloqueionota.ed362_codigo IS '{
        \"descricao\": \"Serial da tabela\",
        \"rotulo\": \"ed362_codigo\",
        \"rotulorel\": \"ed362_codigo\",
        \"maiusculo\": false,
        \"autocompl\": false,
        \"aceitatipo\": 1,
        \"tamanho\": 10,
        \"tipoobj\": \"text\"
      }'"
      );
      
      DB::statement("COMMENT ON COLUMN escola.parametrosbloqueionota.ed362_tipobloqueio IS '{
        \"descricao\": \"Tipo de bloqueio definido no parametro\",
        \"rotulo\": \"ed362_tipobloqueio\",
        \"rotulorel\": \"ed362_tipobloqueio\",
        \"maiusculo\": false,
        \"autocompl\": false,
        \"aceitatipo\": 1,
        \"tamanho\": 10,
        \"tipoobj\": \"text\",
        \"syscampodef\": [
          {
            \"defcampo\": \"1\",
            \"defdescr\": \"SEM_BLOQUEIO\"
          },
          {
            \"defcampo\": \"2\",
            \"defdescr\": \"BLOQUEIO_NOTAS_PARCIAIS\"
          },
          {
            \"defcampo\": \"3\",
            \"defdescr\": \"BLOQUEIO_NOTAS_DIARIO\"
          },
          {
            \"defcampo\": \"4\",
            \"defdescr\": \"BLOQUEIO_TUDO\"
          }
        ]
      }'"
      );

      DB::statement("COMMENT ON COLUMN escola.parametrosbloqueionota.ed362_prazodias IS '{
          \"descricao\": \"Quantidade de dias após fim do período de lançamento para bloqueio de notas\",
          \"rotulo\": \"ed362_prazodias\",
          \"rotulorel\": \"ed362_prazodias\",
          \"maiusculo\": false,
          \"autocompl\": false,
          \"aceitatipo\": 0,
          \"tamanho\": 10,
          \"tipoobj\": \"text\"
        }'"
      );
    }

    public function dicionarioTabeloaExcecoesbloqueionota()
    {
      //Colunas
      DB::statement("COMMENT ON TABLE escola.excecoesbloqueionota IS '{
          \"descricao\": \"Guarda lista de escolas que ainda pode lançar notas com data limite maior que a do fim do período\",
          \"sigla\": \"ed363\",
          \"dataincl\": \"2024-10-08\",
          \"rotulo\": \"excecoesbloqueionota\",
          \"tipotabela\": 0,
          \"naolibclass\": false,
          \"naolibfunc\": false,
          \"naolibprog\": false,
          \"naolibform\": false
        }'"
      );

      //Tabelas
      DB::statement("COMMENT ON COLUMN escola.excecoesbloqueionota.ed363_codigo IS '{
          \"descricao\": \"Serial da tabela\",
          \"rotulo\": \"ed363_codigo\",
          \"rotulorel\": \"ed363_codigo\",
          \"maiusculo\": false,
          \"autocompl\": false,
          \"aceitatipo\": 1,
          \"tamanho\": 10,
          \"tipoobj\": \"text\"
        }'"
      );

      DB::statement("COMMENT ON COLUMN escola.excecoesbloqueionota.ed363_turma IS '{
          \"descricao\": \"Vinculo com a tabela turma\",
          \"rotulo\": \"ed363_turma\",
          \"rotulorel\": \"ed363_turma\",
          \"maiusculo\": false,
          \"autocompl\": false,
          \"aceitatipo\": 0,
          \"tamanho\": 15,
          \"tipoobj\": \"text\"
        }'"
      );

      DB::statement("COMMENT ON COLUMN escola.excecoesbloqueionota.ed363_regencia IS '{
          \"descricao\": \"Vinculo com a regencia\",
          \"rotulo\": \"ed363_regencia\",
          \"rotulorel\": \"ed363_regencia\",
          \"maiusculo\": false,
          \"autocompl\": false,
          \"aceitatipo\": 0,
          \"tamanho\": 15,
          \"tipoobj\": \"text\"
        }'"
      );

      DB::statement("COMMENT ON COLUMN escola.excecoesbloqueionota.ed363_periodoavaliacao IS '{
          \"descricao\": \"Vinculo com periodoavaliacao\",
          \"rotulo\": \"ed363_periodoavaliacao\",
          \"rotulorel\": \"ed363_periodoavaliacao\",
          \"maiusculo\": false,
          \"autocompl\": false,
          \"aceitatipo\": 0,
          \"tamanho\": 15,
          \"tipoobj\": \"text\"
        }'"
      );

      DB::statement("COMMENT ON COLUMN escola.excecoesbloqueionota.ed363_datalimite IS '{
          \"descricao\": \"Data limite para lançamento de notas\",
          \"rotulo\": \"ed363_datalimite\",
          \"rotulorel\": \"ed363_datalimite\",
          \"maiusculo\": false,
          \"autocompl\": false,
          \"aceitatipo\": 0,
          \"tamanho\": 15,
          \"tipoobj\": \"text\"
        }'"
      );

      DB::statement("COMMENT ON COLUMN escola.excecoesbloqueionota.ed363_ativo IS '{
          \"descricao\": \"Exceçao ativa ou nao\",
          \"rotulo\": \"ed363_ativo\",
          \"rotulorel\": \"ed363_ativo\",
          \"maiusculo\": false,
          \"autocompl\": false,
          \"aceitatipo\": 5,
          \"tamanho\": 1,
          \"tipoobj\": \"checkbox\"
        }'"
      );

      DB::statement("COMMENT ON COLUMN escola.excecoesbloqueionota.ed363_usuario IS '{
          \"descricao\": \"Usuário que fez a inclusao\",
          \"rotulo\": \"ed363_usuario\",
          \"rotulorel\": \"ed363_usuario\",
          \"maiusculo\": false,
          \"autocompl\": false,
          \"aceitatipo\": 0,
          \"tamanho\": 15,
          \"tipoobj\": \"text\"
        }'"
      );
    }

    public function criarItensMenu()
    {
      DB::connection()->getPdo()->exec(<<<SQL
insert into db_itensmenu(id_item, descricao, help, funcao, itemativo, manutencao, desctec, libcliente)
    values (3000316, 'Bloqueio Lançamento de Notas', '#', '#', '1', '1', 'Menu para Bloqueio de Lançamento de Notas', 'true'),
           (3000317, 'Parametros de Bloqueio', '#', 'edu4_parametrosbloqueionota.php', '1', '1', 'Parametros de Bloqueio de notas', 'true'),
           (3000318, 'Exceções de Bloqueio', '#', 'edu4_excecoesbloqueionota.php', '1', '1', 'Turmas com exceções para de Bloqueio de notas', 'true');
insert into db_menu(id_item, id_item_filho, menusequencia, modulo)
    values (3444, 3000316, 27, 7159),
           (3000316, 3000317, 1, 7159),
           (3000316, 3000318, 2, 7159);
SQL
        );
    }

    public function downRotinaParametros()
    {
      $this->itensMenuDown();

      Schema::dropIfExists('escola.parametrosbloqueionota');
      Schema::dropIfExists('escola.excecoesbloqueionota');
    }

    public function itensMenuDown()
    {
      DB::connection()->getPdo()->exec(<<<SQL
delete from db_menu where id_item_filho in (3000316, 3000317, 3000318);
delete from db_itensmenu where id_item in (3000316, 3000317, 3000318);
SQL
        );
    }

    public function adicionarParametrosEscola()
    {
      $this->adicionarNovasColunasParametrosEscola();
      $this->dicionarioNovasColunasParamentrosEscola();
    }

    public function adicionarNovasColunasParametrosEscola()
    {
      Schema::table('escola.edu_parametros', function (Blueprint $table) {
        $table->boolean('ed233_alterarprocedimentonotaparcial')->default('false');
        $table->boolean('ed233_habilitarrecuperacaonotaparcial')->default('true');
      });
    }

    public function dicionarioNovasColunasParamentrosEscola()
    {
      DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;");
      DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;");

      DB::statement("COMMENT ON COLUMN escola.edu_parametros.ed233_alterarprocedimentonotaparcial IS '{
        \"descricao\": \"Permitir Alteração do Procedimento das Notas Parciais\",
        \"rotulo\": \"ed233_alterarprocedimentonotaparcial\",
        \"rotulorel\": \"ed233_alterarprocedimentonotaparcial\",
        \"maiusculo\": false,
        \"autocompl\": false,
        \"aceitatipo\": 5,
        \"tamanho\": 1,
        \"tipoobj\": \"text\"
      }'"
      );

      DB::statement("COMMENT ON COLUMN escola.edu_parametros.ed233_habilitarrecuperacaonotaparcial IS '{
        \"descricao\": \"Habilitar Recuperação das Notas Parciais\",
        \"rotulo\": \"ed233_habilitarrecuperacaonotaparcial\",
        \"rotulorel\": \"ed233_habilitarrecuperacaonotaparcial\",
        \"maiusculo\": false,
        \"autocompl\": false,
        \"aceitatipo\": 5,
        \"tamanho\": 1,
        \"tipoobj\": \"text\"
      }'"
      );

      DB::select("SELECT fc_gera_dicionario_apartir_tabela(?, ?)", ['escola', 'edu_parametros']);

      DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;");
      DB::statement("ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;");
    }

    public function droparParametrosEscola()
    {
      Schema::table('escola.edu_parametros', function (Blueprint $table) {
        $table->dropColumn('ed233_alterarprocedimentonotaparcial');
        $table->dropColumn('ed233_habilitarrecuperacaonotaparcial');
      });
    }
}
