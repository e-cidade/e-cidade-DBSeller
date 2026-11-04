<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23250EventoS1010 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
            update habitacao.avaliacaoperguntaopcao set db104_descricao = '48: Deduções IRRF - Previdência complementar - Férias' where db104_sequencial = 4001341;
            update habitacao.avaliacaoperguntaopcao set db104_descricao = '9046: Exigibilidade suspensa - Dedução da base de cálculo do IRRF: Previdência complementar - Salário mensal' where db104_sequencial = 4001358;
            update habitacao.avaliacaoperguntaopcao set db104_descricao = '9047: Exigibilidade suspensa-Dedução da base de cálculo do IRRF-Previdência complementar-13º salário' where db104_sequencial = 4001359;
            insert into habitacao.avaliacaoperguntaopcao( db104_sequencial ,db104_avaliacaopergunta ,db104_descricao ,db104_identificador ,db104_aceitatexto ,db104_peso ,db104_valorresposta ,db104_identificadorcampo ) values ((SELECT setval('"avaliacaoperguntaopcao_db104_sequencial_seq"', (SELECT MAX(db104_sequencial) FROM habitacao.avaliacaoperguntaopcao)+1)) ,4000297 ,'92 - Suspensão de incidência em decorrência de decisão judicial - 13º salário' ,'suspensao_incidencia_decisao_judicial_13salario' ,'false' ,0 ,'92' ,'codIncCPRP_92' );
SQL;

        $opcoes = [3003845, 3000948, 3003846 ,3003809, 3003808, 3003807, 3003811, 3003824, 3003831, 3003835, 3003840];
        foreach ($opcoes as $opcao) {
            $this->deletaOpcaoPergunta($opcao);
        }
    DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
            ------------------------------------------------------------------------------------------------------------
            insert into habitacao.avaliacaoperguntaopcao values (3000948, 3000259, 'Analfabeto, inclusive o que, embora tenha recebido instrução, não se alfabetizou', false, 'analfabeto', 0, 01, 'grauInstr_01');
            insert into habitacao.avaliacaoperguntaopcao values (3003807, 3000948, 'Deduções IRRF - Compensação judicial de anos anteriores', false, 'deducoes-irrf-compensacao-judicial-de-anos-anterio', 83, 83, 'codIncIRRF_83');
            insert into habitacao.avaliacaoperguntaopcao values (3003808, 3000948, 'Deduções IRRF - Compensação judicial do ano calendário', false, 'deducoes-irrf-compensacao-judicial-do-ano-calendar', 82, 82, 'codIncIRRF_82');
            insert into habitacao.avaliacaoperguntaopcao values (3003809, 3000948, 'Deduções IRRF - Depósito judicial', false, 'deducoes-irrf-deposito-judicial', 81, 81, 'codIncIRRF_81');
            insert into habitacao.avaliacaoperguntaopcao values (3003811, 3000948, 'Deduções IRRF - Valores pagos a titular ou sócio de microempresa ou empresa de pequeno porte, exceto pró-labore e alugueis', false, 'deducoes-irrf-valores-pagos-a-titular-ou-socio-de-', 78, 78, 'codIncIRRF_78');
            insert into habitacao.avaliacaoperguntaopcao values (3003824, 3000948, 'Deduções IRRF - Pensão Alimentícia - RRA', false, 'deducoes-irrf-pensao-alimenticia-rra', 55, 55, 'codIncIRRF_55');
            insert into habitacao.avaliacaoperguntaopcao values (3003831, 3000948, 'Deduções IRRF - PSO - RRA', false, 'deducoes-irrf-pso-rra', 44, 44, 'codIncIRRF_44');
            insert into habitacao.avaliacaoperguntaopcao values (3003835, 3000948, 'Retenções do IRRF efetuadas sobre - RRA', false, 'retencoes-do-irrf-efetuadas-sobre-rra', 35, 35, 'codIncIRRF_35');
            insert into habitacao.avaliacaoperguntaopcao values (3003840, 3000948, 'Rendimentos tributáveis base de IRRF - RRA', false, 'rendimentos-tributaveis-base-de-irrf-rra', 15, 15, 'codIncIRRF_15');
            insert into habitacao.avaliacaoperguntaopcao values (3003846, 3000948, 'Rendimento não tributável', false, 'rendimento-nao-tributavel', 0, 00, 'codIncIRRF_00');
            ------------------------------------------------------------------------------------------------------------
            update habitacao.avaliacaoperguntaopcao set db104_descricao = '48: Deduções IRRF - Previdência privada - Férias' where db104_sequencial = 4001341;
            update habitacao.avaliacaoperguntaopcao set db104_descricao = '9046: Exigibilidade suspensa - Dedução da base de cálculo do IRRF: Previdência privada - Salário mensal' where db104_sequencial = 4001358;
            update habitacao.avaliacaoperguntaopcao set db104_descricao = '9047: Exigibilidade suspensa-Dedução da base de cálculo do IRRF-Previdência privada-13º salário' where db104_sequencial = 4001359;
            update esocial.rubricasubgrupotce set rh263_descricao = 'Contribuição Sindical Compulsória' where rh263_grupo = '9230';
            ------------------------------------------------------------------------------------------------------------  
SQL;
            $this->deletaOpcaoPergunta(4001498);
            DB::connection()->getPdo()->exec($sql);
    }   

    private function deletaOpcaoPergunta($db104_sequencial) {
            $sql = <<<SQL
                delete from esocial.esocialrubricas where eso26_avaliacaoperguntaopcaocodincirrf in ({$db104_sequencial});
                delete from habitacao.avaliacaogrupoperguntaresposta where db108_avaliacaoresposta in (select db106_sequencial from avaliacaoresposta where db106_avaliacaoperguntaopcao in({$db104_sequencial}));
                delete from habitacao.avaliacaoresposta where db106_avaliacaoperguntaopcao in({$db104_sequencial});
                delete from habitacao.avaliacaoperguntaopcao where db104_sequencial in ({$db104_sequencial});
SQL;
            DB::connection()->getPdo()->exec($sql);

    }   
}