<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23511TabelaNaturezarendimento extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->upTabela();
        $this->upNaturezasRend();
    }

    /**
     * Tabela - Auditoria - Dic. de dados.
     *
     * @return void
     */
    public function upTabela()
    {
        $sql = <<<SQL

            -- trigger dicionario
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl ENABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop ENABLE;

            -- cria tabela
            CREATE TABLE empenho.naturezarendimento (
                e167_sequencial SERIAL PRIMARY KEY,
                e167_codigo VARCHAR(5) UNIQUE NOT NULL,
                e167_descricao TEXT NOT NULL,
                e167_tributo VARCHAR(100),
                e167_declarante VARCHAR(10) CHECK (e167_declarante in ('PF', 'PJ', 'PF/PJ'))
            );

            -- comentario da tabela
            COMMENT ON TABLE empenho.naturezarendimento IS
                '{
                    "descricao": "Tabela de Natureza de Rendimentos",
                    "sigla": "e167",
                    "dataincl": "2023-09-06",
                    "rotulo": "Tabela",
                    "tipotabela": 0,
                    "naolibclass": false,
                    "naolibfunc": false,
                    "naolibprog": false,
                    "naolibform": false
                }';

            -- comentario dos campos
            COMMENT ON COLUMN empenho.naturezarendimento.e167_sequencial IS
                '{
                    "descricao": "Chave primária da tabela",
                    "rotulo": "Código",
                    "rotulorel": "Código",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 1,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

            COMMENT ON COLUMN empenho.naturezarendimento.e167_codigo IS
                '{
                    "descricao": "Codigo da Natureza de rendimento",
                    "rotulo": "Codigo da Natureza de rendimento",
                    "rotulorel": "Codigo da Natureza de rendimento",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 3,
                    "tamanho": 5,
                    "tipoobj": "text"
                }';

            COMMENT ON COLUMN empenho.naturezarendimento.e167_descricao IS
                '{
                    "descricao": "Descricao",
                    "rotulo": "Descricao",
                    "rotulorel": "Descricao",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 3,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

            COMMENT ON COLUMN empenho.naturezarendimento.e167_tributo IS
                '{
                    "descricao": "Tipo de Retencao Aceita",
                    "rotulo": "Tributo",
                    "rotulorel": "Tributo",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 3,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

            COMMENT ON COLUMN empenho.naturezarendimento.e167_declarante IS
                '{
                    "descricao": "Tipo de Declarante",
                    "rotulo": "Declarante",
                    "rotulorel": "Declarante",
                    "maiusculo": false,
                    "autocompl": false,
                    "aceitatipo": 3,
                    "tamanho": 10,
                    "tipoobj": "text"
                }';

            -- auditoria / dicionario
            SELECT configuracoes.fc_auditoria_cria_funcao('empenho.naturezarendimento');
            SELECT fc_gera_dicionario_apartir_tabela('empenho', 'naturezarendimento');

            -- desabilita trigger
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl DISABLE;
            ALTER EVENT TRIGGER evtg_dicionario_gatilho_ddl_drop DISABLE;
SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Insert Naturezas
     *
     * @return void
     */
    public function upNaturezasRend()
    {
        $sql = <<<SQL

        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (10001, 'Rendimento decorrente do trabalho com vínculo empregatício', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (10002, 'Rendimento decorrente do trabalho sem vínculo empregatício', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (10003, 'Rendimento decorrente do trabalho pago a trabalhador avulso', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (10004, 'Participação nos lucros ou resultados (PLR)', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (10005, 'Benefício de Regime Próprio de Previdência Social', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (10006, 'Benefício do Regime Geral de Previdência Social', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (10009, 'Auxílio moradia', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (10010, 'Bolsa ao médico residente', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (11001, 'Decorrente de Decisão da Justiça do Trabalho', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (11002, 'Decorrente de Decisão da Justiça Federal', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (11003, 'Decorrente de Decisão da Justiça dos Estados/Distrito Federal', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (11004, 'Decisão Judicial - Responsabilidade Civil - juros e indenizações por lucros cessantes, inclusive astreinte', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (11005, 'Decisão Judicial - Importâncias pagas a título de indenizações por danos morais, decorrentes de sentença judicial.', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12001, 'Lucro e Dividendo', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12002, 'Resgate de Previdência Complementar - Modalidade Contribuição Definida/Variável - Não Optante pela Tributação Exclusiva', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12003, 'Resgate de Fundo de Aposentadoria Programada Individual (Fapi)- Não Optante pela Tributação Exclusiva', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12004, 'Resgate de Previdência Complementar - Modalidade Benefício Definido - Não Optante pela Tributação Exclusiva', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12005, 'Resgate de Previdência Complementar - Modalidade Contribuição Definida/Variável - Optante pela Tributação Exclusiva', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12006, 'Resgate de Fundo de Aposentadoria Programada Individual (Fapi)- Optante pela Tributação Exclusiva', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12007, 'Resgate de Planos de Seguro de Vida com Cláusula de Cobertura por Sobrevivência- Optante pela Tributação Exclusiva', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12008, 'Resgate de Planos de Seguro de Vida com Cláusula de Cobertura por Sobrevivência - Não Optante pela Tributação Exclusiva', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12009, 'Benefício de Previdência Complementar - Modalidade Contribuição Definida/Variável - Não Optante pela Tributação Exclusiva', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12010, 'Benefício de Fundo de Aposentadoria Programada Individual (Fapi)- Não Optante pela Tributação Exclusiva', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12011, 'Benefício de Previdência Complementar - Modalidade Benefício Definido - Não Optante pela Tributação Exclusiva', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12012, 'Benefício de Previdência Complementar - Modalidade Contribuição Definida/Variável - Optante pela Tributação Exclusiva', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12013, 'Benefício de Fundo de Aposentadoria Programada Individual (Fapi)- Optante pela Tributação Exclusiva', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12014, 'Benefício de Planos de Seguro de Vida com Cláusula de Cobertura por Sobrevivência- Optante pela Tributação Exclusiva', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12015, 'Benefício de Planos de Seguro de Vida com Cláusula de Cobertura por Sobrevivência - Não Optante pela Tributação Exclusiva', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12016, 'Juros sobre o Capital Próprio', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12017, 'Rendimento de Aplicações Financeiras de Renda Fixa, decorrentes de alienação, liquidação (total ou parcial), resgate, cessão ou repactuação do título ou aplicação', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12018, 'Rendimentos auferidos pela entrega de recursos à pessoa jurídica, sob qualquer forma e a qualquer título, independentemente de ser ou não a fonte pagadora instituição autorizada a funcionar pelo Banco Central', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12019, 'Rendimentos predeterminados obtidos em operações conjugadas realizadas: nos mercados de opções de compra e venda em bolsas de valores, de mercadorias e de futuros (box); no mercado a termo nas bolsas de valores, de mercadorias e de futuros, em operações de venda coberta e sem ajustes diários, e no mercado de balcão.', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12020, 'Rendimentos obtidos nas operações de transferência de dívidas realizadas com instituição financeira e outras instituições autorizadas a funcionar pelo Banco Central do Brasil', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12021, 'Rendimentos periódicos produzidos por título ou aplicação, bem como qualquer remuneração adicional aos rendimentos prefixados', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12022, 'Rendimentos auferidos nas operações de mútuo de recursos financeiros entre pessoa física e pessoa jurídica e entre pessoas jurídicas, inclusive controladoras, controladas, coligadas e interligadas', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12023, 'Rendimentos auferidos em operações de adiantamento sobre contratos de câmbio de exportação, não sacado (trava de câmbio), bem como operações com export notes, com debêntures, com depósitos voluntários para garantia de instância e com depósitos judiciais ou administrativos, quando seu levantamento se der em favor do depositante', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12024, 'Rendimentos obtidos nas operações de mútuo e de compra vinculada à revenda tendo por objeto ouro, ativo financeiro', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12025, 'Rendimentos auferidos em contas de depósitos de poupança', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12026, 'Rendimentos auferidos sobre juros produzidos por letras hipotecárias', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12027, 'Rendimentos ou ganhos decorrentes da negociação de títulos ou valores mobiliários de renda fixa em bolsas de valores, de mercadorias, de futuros e assemelhadas', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12028, 'Rendimentos auferidos em outras aplicações financeiras de renda fixa ou de renda variável', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12029, 'Rendimentos auferidos em Fundo de Investimento', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12030, 'Rendimentos auferidos em Fundos de investimento em quotas de fundos de investimento', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12031, 'Rendimentos produzidos por aplicações em fundos de investimento em ações', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12032, 'Rendimentos produzidos por aplicações em fundos de investimento em quotas de fundos de investimento em ações', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12033, 'Rendimentos produzidos por aplicações em Fundos Mútuos de Privatização com recursos do Fundo de Garantia por Tempo de Serviço (FGTS)', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12034, 'Rendimentos auferidos pela carteira dos Fundos de Investimento Imobiliário', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12035, 'Rendimentos distribuídos pelo Fundo de Investimento Imobiliário aos seus cotistas', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12036, 'Rendimento auferido pelo cotista no resgate de cotas na liquidação do Fundo de Investimento Imobiliário', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12037, 'Rendimentos auferidos pela carteira dos Fundos de Investimento Imobiliário - Distribuição semestral', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12038, 'Rendimentos distribuídos pelo Fundo de Investimento Imobiliário aos seus cotistas - - Distribuição semestral', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12039, 'Rendimento auferido pelo cotista no resgate de cotas na liquidação do Fundo de Investimento Imobiliário - - Distribuição semestral', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12040, 'Rendimentos e ganhos de capital distribuídos pelo Fundo de Investimento Cultural e Artístico (Ficart)', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12041, 'Rendimentos e ganhos de capital distribuídos pelo Fundo de Financiamento da Indústria Cinematográfica Nacional (Funcines)', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12042, 'Rendimentos auferidos no resgate de quotas de fundos de investimento mantidos com recursos provenientes de conversão de débitos externos brasileiros, e de que participem, exclusivamente, residentes ou domiciliados no exterior', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12043, 'Ganho de capital decorrente da integralização de cotas de fundos ou clubes de investimento por meio da entrega de ativos financeiros', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12044, 'Distribuição de Juros sobre o Capital Próprio pela companhia emissora de ações objeto de empréstimo', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12045, 'Rendimentos de Partes Beneficiárias ou de Fundador', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12046, 'Rendimentos auferidos em operações de swap', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12047, 'Rendimentos auferidos em operações day trade realizadas em bolsa de valores, de mercadorias, de futuros e assemelhadas', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12048, 'Rendimento decorrente de Operação realizada em bolsas de valores, de mercadorias, de futuros, e assemelhadas, exceto day trade', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12049, 'Rendimento decorrente de Operação realizada no mercado de balcão, com intermediação, tendo por objeto ações, ouro ativo financeiro e outros valores mobiliários negociados no mercado à vista', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12050, 'Rendimento decorrente de Operação realizada em mercados de liquidação futura fora de bolsa', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12051, 'Rendimentos de debêntures emitidas por sociedade de propósito específico conforme previsto no art. 2º da Lei nº 12.431 de 2011', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (12099, 'Demais rendimentos de Capital', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13001, 'Rendimentos de Aforamento', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13002, 'Rendimentos de Locação ou Sublocação', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13003, 'Rendimentos de Arrendamento ou Subarrendamento', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13004, 'Importâncias pagas por terceiros por conta do locador do bem (juros, comissões etc.)', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13005, 'Importâncias pagas ao locador pelo contrato celebrado (luvas, prêmios etc.)', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13006, 'Benfeitorias e quaisquer melhoramentos realizados no bem locado', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13007, 'Juros decorrente da alienação a prazo de bens', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13008, 'Rendimentos de Direito de Uso ou Passagem de Terrenos e de aproveitamento de águas', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13009, 'Rendimentos de Direito de colher ou extrair recursos vegetais, pesquisar e extrair recursos minerais', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13010, 'Rendimentos de Direito Autoral', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13011, 'Rendimentos de Direito Autoral (quando não percebidos pelo autor ou criador da obra)', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13012, 'Rendimentos de Direito de Imagem', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13013, 'Rendimentos de Direito de exploração de películas cinematográficas, Obras Audiovisuais, e Videofônicas', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13014, 'Rendimento de Direito relativo a radiodifusão de sons e imagens e serviço de comunicação eletrônica de massa por assinatura', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13015, 'Rendimentos de Direito de Conjuntos Industriais e Invenções', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13016, 'Rendimento de Direito de marcas de indústria e comércio, patentes de invenção e processo ou fórmulas de fabricação', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13017, 'Importâncias pagas por terceiros por conta do cedente dos direitos (juros, comissões etc.)', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13018, 'Importâncias pagas ao cedente do direito, pelo contrato celebrado (luvas, prêmios etc.)', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13019, 'Despesas para conservação dos direitos cedidos (quando compensadas pelo uso do bem ou direito)', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13020, 'Juros de mora e quaisquer outras compensações pelo atraso no pagamento de royalties - decorrente de prestação de serviço', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13021, 'Juros de mora e quaisquer outras compensações pelo atraso no pagamento de royalties - decorrente de aquisição de bens', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13022, 'Juros decorrente da alienação a prazo de direitos - decorrente de prestação de serviço', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13023, 'Juros decorrente da alienação a prazo de direitos - decorrente de aquisição de bens', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13024, 'Alienação de bens e direitos do ativo não circulante localizados no Brasil', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13025, 'Rendimento de Direito decorrente da transferência atleta profissional', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13026, 'Juros e comissões correspondentes à parcela dos créditos de que trata o inciso XI do art. 1º da Lei nº 9.481, de 1997, não aplicada no financiamento de exportações', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13098, 'Demais rendimentos de Royalties', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (13099, 'Demais rendimentos de Direito', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (14001, 'Prêmios distribuídos, sob a forma de bens e serviços, mediante loterias, concursos e sorteios, exceto a distribuição realizada por meio de vale-brinde', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (14002, 'Prêmios distribuídos, sob a forma de dinheiro, mediante loterias, concursos e sorteios, exceto os de antecipação nos títulos de capitalização e os de amortização e resgate das ações das sociedades anônimas', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (14003, 'Prêmios de Proprietários e Criadores de Cavalos de Corrida', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (14004, 'Benefícios líquidos mediante sorteio de títulos de capitalização, sem amortização antecipada', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (14005, 'Benefícios líquidos resultantes da amortização antecipada, mediante sorteio, dos titulos de capitalização e benefícios atribuídos aos portadores de títulos de capitalização nos lucros da empresa emitente', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (14006, 'Prêmios distribuídos, sob a forma de bens e serviços, mediante sorteios de jogos de bingo permanente ou eventual', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (14007, 'Prêmios distribuídos, em dinheiro, obtido mediante sorteios de jogos de bingo permanente ou eventual', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (14008, 'Importâncias correspondentes a multas e qualquer outra vantagem, ainda que a título de indenização, em virtude de rescisão de contrato', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (14099, 'Demais Benefícios Líquidos decorrentes de título de capitalização', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15001, 'Importâncias pagas ou creditadas a cooperativas de trabalho relativas a serviços pessoais que lhes forem prestados por associados destas ou colocados à disposição', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15002, 'Importâncias pagas ou creditadas a associações de profissionais ou assemelhadas, relativas a serviços pessoais que lhes forem prestados por associados destas ou colocados à disposição', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15003, 'Remuneração de Serviços de administração de bens ou negócios em geral, exceto consórcios ou fundos mútuos para aquisição de bens', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15004, 'Remuneração de Serviços de advocacia', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15005, 'Remuneração de Serviços de análise clínica laboratorial', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15006, 'Remuneração de Serviços de análises técnicas', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15007, 'Remuneração de Serviços de arquitetura', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15008, 'Remuneração de Serviços de assessoria e consultoria técnica, exceto serviço de assistência técnica prestado a terceiros e concernente a ramo de indústria ou comércio explorado pelo prestador do serviço', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15009, 'Remuneração de Serviços de assistência social', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15010, 'Remuneração de Serviços de auditoria', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15011, 'Remuneração de Serviços de avaliação e perícia', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15012, 'Remuneração de Serviços de  biologia e biomedicina', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15013, 'Remuneração de Serviços de cálculo em geral', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15014, 'Remuneração de Serviços de consultoria', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15015, 'Remuneração de Serviços de  contabilidade', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15016, 'Remuneração de Serviços de desenho técnico', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15017, 'Remuneração de Serviços de economia', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15018, 'Remuneração de Serviços de elaboração de projetos', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15019, 'Remuneração de Serviços de engenharia, exceto construção de estradas, pontes, prédios e obras assemelhadas', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15020, 'Remuneração de Serviços de  ensino e treinamento', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15021, 'Remuneração de Serviços de estatística', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15022, 'Remuneração de Serviços de fisioterapia', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15023, 'Remuneração de Serviços de fonoaudiologia', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15024, 'Remuneração de Serviços de geologia', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15025, 'Remuneração de Serviços de leilão', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15026, 'Remuneração de Serviços de medicina, exceto aquela prestada por ambulatório, banco de sangue, casa de saúde, casa de recuperação ou repouso sob orientação médica, hospital e pronto-socorro', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15027, 'Remuneração de Serviços de nutricionismo e dietética', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15028, 'Remuneração de Serviços de odontologia', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15029, 'Remuneração de Serviços de organização de feiras de amostras, congressos, seminários, simpósios e congêneres', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15030, 'Remuneração de Serviços de pesquisa em geral', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15031, 'Remuneração de Serviços de planejamento', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15032, 'Remuneração de Serviços de programação', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15033, 'Remuneração de Serviços de  prótese', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15034, 'Remuneração de Serviços de  psicologia e psicanálise', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15035, 'Remuneração de Serviços de química', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15036, 'Remuneração de Serviços de radiologia e radioterapia', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15037, 'Remuneração de Serviços de relações públicas', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15038, 'Remuneração de Serviços de  serviço de despachante', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15039, 'Remuneração de Serviços de  terapêutica ocupacional', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15040, 'Remuneração de Serviços de  tradução ou interpretação comercial', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15041, 'Remuneração de Serviços de urbanismo', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15042, 'Remuneração de Serviços de  veterinária.', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15043, 'Remuneração de Serviços de Limpeza', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15044, 'Remuneração de Serviços de Conservação/ Manutenção, exceto reformas e obras assemelhadas', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15045, 'Remuneração de Serviços de Segurança/Vigilância/Transporte de valores', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15046, 'Remuneração de Serviços Locação de Mão de obra', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15047, 'Remuneração de Serviços de Assessoria Creditícia, Mercadológica, Gestão de Crédito, Seleção e Riscos e Administração de Contas a Pagar e a Receber', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15048, 'Pagamentos Referentes à Aquisição de Autopeças', 'PJ', 'COFINS' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15049, 'Pagamentos a entidades imunes ou isentas - IN RFB 1.234/2012', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15050, 'Pagamento a título de transporte internacional de valores efetuado por empresas nacionais estaleiros navais brasileiros nas atividades de conservação, modernização, conversão e reparo de embarcações pré-registradas ou registradas no Registro Especial Brasileiro (REB)', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15051, 'Pagamento efetuado a empresas estrangeiras de transporte de valores', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (15099, 'Demais Rendimentos de serviços técnicos, de assistência técnica, de assistência administrativa e semelhantes', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (16001, 'Demais Rendimentos de serviços técnicos, de assistência técnica, de assistência administrativa e semelhantes', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (16002, 'Demais Rendimentos de juros e comissões', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (16003, 'Rendimento pago a companhia de navegação aérea e marítima', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (16004, 'Rendimento de Direito relativo a exploração de obras audiovisuais estrangeiras, radiodifusão de sons e imagens e serviço de comunicação eletrônica de massa por assinatura', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (16005, 'Demais Rendimentos de qualquer natureza', 'PF/PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (16006, 'Demais rendimentos sujeitos à Alíquota ZERO', 'PF/PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17001, 'Alimentação', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17002, 'Energia elétrica', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17003, 'Serviços prestados com emprego de materiais', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17004, 'Construção Civil por empreitada com emprego de materiais', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17005, 'Serviços hospitalares de que trata o art. 30 da Instrução Normativa RFB nº 1.234, de 11 de janeiro de 2012', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17006, 'Transporte de cargas , exceto os relacionados na natureza de rendimento "17017"', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17007, 'Serviços de auxílio diagnóstico e terapia, patologia clínica, imagenologia, anatomia patológica e citopatológica, medicina nuclear e análises e patologias clínicas, exames por métodos gráficos, procedimentos endoscópicos, radioterapia, quimioterapia, diálise e oxigenoterapia hiperbárica de que trata o art. 31 e parágrafo único da Instrução Normativa RFB nº 1.234, de 2012', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17008, 'Produtos farmacêuticos, de perfumaria, de toucador ou de higiene pessoal adquiridos de produtor, importador, distribuidor ou varejista, exceto os relacionados nas naturezas de rendimentos de "17019" a "17022"', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17009, 'Mercadorias e bens em geral', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17010, 'Gasolina, inclusive de aviação, óleo diesel, gás liquefeito de petróleo (GLP), combustíveis derivados de petróleo ou de gás natural, querosene de aviação (QAV), e demais produtos derivados de petróleo, adquiridos de refinarias de petróleo, de demais produtores, de importadores, de distribuidor ou varejista', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17011, 'Álcool etílico hidratado, inclusive para fins carburantes, adquirido diretamente de produtor, importador ou do distribuidor', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17012, 'Biodiesel adquirido de produtor ou importador', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17013, 'Gasolina, exceto gasolina de aviação, óleo diesel e gás liquefeito de petróleo (GLP), derivados de petróleo ou de gás natural e querosene de aviação adquiridos de distribuidores e comerciantes varejistas', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17014, 'Álcool etílico hidratado nacional, inclusive para fins carburantes adquirido de comerciante varejista', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17015, 'Biodiesel adquirido de distribuidores e comerciantes varejistas', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17016, 'Biodiesel adquirido de produtor detentor regular do selo "Combustível Social", fabricado a partir de mamona ou fruto, caroço ou amêndoa de palma produzidos nas regiões norte e nordeste e no semiárido, por agricultor familiar enquadrado no Programa Nacional de Fortalecimento da Agricultura Familiar (Pronaf)', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17017, 'Transporte internacional de cargas efetuado por empresas nacionais', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17018, 'Estaleiros navais brasileiros nas atividades de Construção, conservação, modernização, conversão e reparo de embarcações pré-registradas ou registradas no REB', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17019, 'Produtos de perfumaria, de toucador e de higiene pessoal a que se refere o § 1º do art. 22 da Instrução Normativa RFB nº 1.234, de 2012, adquiridos de distribuidores e de comerciantes varejistas', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17020, 'Produtos a que se refere o § 2º do art. 22 da Instrução Normativa RFB nº 1.234, de 2012', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17021, 'Produtos de que tratam as alíneas "c" a "k" do inciso I do art. 5º da Instrução Normativa RFB nº 1.234, de 2012', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17022, 'Outros produtos ou serviços beneficiados com isenção, não incidência ou Alíquotas zero da Cofins e da Contribuição para o PIS/Pasep, observado o disposto no § 5º do art. 2º da Instrução Normativa RFB nº 1.234, de 2012', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17023, 'Passagens aéreas, rodoviárias e demais serviços de transporte de passageiros, inclusive, tarifa de embarque, exceto transporte internacional de passageiros, efetuado por empresas nacionais', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17024, 'Transporte internacional de passageiros efetuado por empresas nacionais', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17025, 'Serviços prestados por associações profissionais ou assemelhadas e cooperativas', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17026, 'Serviços prestados por bancos comerciais, bancos de investimento, bancos de desenvolvimento, caixas econômicas, sociedades de crédito, financiamento e investimento, sociedades de crédito imobiliário, e câmbio, distribuidoras de títulos e valores mobiliários, empresas de arrendamento mercantil, cooperativas de crédito, empresas de seguros privados e de capitalização e entidades abertas de previdência complementar.', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17027, 'Seguro Saúde', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17028, 'Serviços de abastecimento de água', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17029, 'Telefone', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17030, 'Correio e telégrafos', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17031, 'Vigilância', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17032, 'Limpeza', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17033, 'Locação de mão de obra', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17034, 'Intermediação de negócios', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17035, 'Administração, locação ou cessão de bens imóveis, móveis e direitos de qualquer natureza', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17036, 'Factoring', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17037, 'Plano de saúde humano, veterinário ou odontológico com valores fixos por servidor, por empregado ou por animal', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17038, 'Pagamento efetuado a sociedade cooperativa pelo fornecimento de bens, conforme art. 24, da IN 1234/12.', 'PJ', 'CSLL' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17039, 'Pagamento a Cooperativa de produção, em relação aos atos decorrentes da comercialização ou da industrialização de produtos de seus associados, excetuado o previsto no §§ 1º e 2º do art. 25 da IN 1.234/12', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17040, 'Serviços prestados por associações profissionais ou assemelhadas e cooperativas que envolver parcela de serviços fornecidos por terceiros não cooperados ou não associados, contratados ou conveniados, para cumprimento de contratos - Serviços prestados com emprego de materiais, inclusive o de que trata a alínea "C" do Inciso II do art. 27 da IN 1.1234.', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17041, 'Serviços prestados por associações profissionais ou assemelhadas e cooperativas que envolver parcela de serviços fornecidos por terceiros não cooperados ou não associados, contratados ou conveniados, para cumprimento de contratos - Demais serviços', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17042, 'Pagamentos efetuados às associações e às cooperativas de médicos e de odontólogos, relativamente às importâncias recebidas a título de comissão, taxa de administração ou de adesão ao plano', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17043, 'Pagamento efetuado a sociedade cooperativa de produção, em relação aos atos decorrentes da comercialização ou de industrialização, pelas cooperativas agropecuárias e de pesca, de produtos adquiridos de não associados, agricultores, pecuaristas ou pescadores, para completar lotes destinados aoa cumprimento de contratos ou para suprir capacidade ociosa de suas instalações industriais, conforme § 1º do art. 25, da IN 1234/12.', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17044, 'Pagamento referente a aluguel de imóvel quando efetuado à entidade aberta de previdência complementar sem fins lucrativos, de que trata o art 34, § 2º da IN 1.234/2012.', 'PJ', 'CSLL' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17045, 'Serviços prestados por cooperativas de radiotaxi, bem como àquelas cujos cooperados se dediquem a serviços relacionados a atividades culturais e demais cooperativas de serviços, conforme art. 5º-A, da IN RFB 1.234/2012.', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17046, 'Pagamento efetuado na aquisição de bem imóvel, quando o vendedor for pessoa jurídica que exerce a atividade de compra e venda de imóveis, ou quando se tratar de imóveis adquiridos de entidades abertas de previdência complementar com fins lucrativos, conforme art. 23, inc I, da IN RFB 1234/2012.', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17047, 'Pagamento efetuado na aquisição de bem imóvel adquirido pertencente ao ativo não circulante da empresa vendedora, conforme art. 23, inc II da IN RFB 1234/2012.', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17048, 'Pagamento efetuado na aquisição de bem imóvel adquirido de entidade aberta de previdência complementar sem fins lucrativos, conforme art. 23, inc III, da IN RFB 1234/2012.', 'PJ', 'CSLL' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17049, 'Propaganda e Publicidade, em desconformidade ao art 16 da IN RFB 1234/2012, referente ao § 4º do citado artigo.', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17050, 'Propaganda e Publicidade, em conformidade ao art 16 da IN RFB 1234/2012, referente ao § 4º do citado artigo.', 'PJ', 'CSLL' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (17099, 'Demais serviços', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (18001, 'Fornecimento de bens, nos termos do art. 33 da Lei nº 10.833, de 2003', 'PJ', 'CSLL' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (18002, 'Prestação de serviços em geral, nos termos do art. 33 da Lei nº 10.833, de 2003', 'PJ', 'CSLL' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (18003, 'Transporte internacional de cargas ou de passageiros efetuados por empresas nacionais, aos estaleiros navais brasileiros e na aquisição de produtos isentos ou com Alíquota zero da Cofins e Ips/Pasep, conforme art. 4º, da IN SRF nº 475 de 2004.', 'PJ', 'CSLL' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (18004, 'Pagamentos efetuados às cooperativas, em relação aos atos cooperativos, conforme art. 5º, da IN SRF nº 475 de 2004.', 'PJ', 'COFINS' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (18005, 'Aquisição de imóvel pertencente a ativo permanente da empresa vendedora, conforme art. 19, II, da IN SRF nº 475 de 2004.', 'PJ', 'CSLL' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (18006, 'Pagamentos efetuados às sociedades cooperativas, pelo fornecimento de bens ou serviços, conforme art. 24, II, da IN SRF nº 475 de 2004.', 'PJ', 'COFINS' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (18007, 'Pagamentos efetuados à sociedade cooperativa de produção, em relação aos atos decorrentes da comercialização ou industrialização de produtos de seus associados, conforme art. 25, da IN SRF nº 475 de 2004.', 'PJ', '' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (18008, 'Pagamentos efetuados às cooperativas de trabalho, pela prestação de serviços pessoais prestados pelos cooperados, nos termos do art. 26, da IN SRF nº 475 de 2004.', 'PJ', 'CSLL' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (19001, 'Pagamento de remuneração indireta a Beneficiário não identificado', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (19009, 'Pagamento a Beneficiário não identificado', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (20001, 'Rendimento de Serviços de propaganda e publicidade', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (20002, 'Importâncias a título de comissões e corretagens relativas a colocação ou negociação de títulos de renda fixa', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (20003, 'Importâncias a título de comissões e corretagens relativas a operações realizadas em Bolsas de Valores e em Bolsas de Mercadorias', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (20004, 'Importâncias a título de comissões e corretagens relativas a distribuição de emissão de valores mobiliários, quando a pessoa jurídica atuar como agente da companhia emissora', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (20005, 'Importâncias a título de comissões e corretagens relativas a operações de câmbio', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (20006, 'Importâncias a título de comissões e corretagens relativas a vendas de passagens, excursões ou viagens', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (20007, 'Importâncias a título de comissões e corretagens relativas a administração de cartões de crédito', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (20008, 'Importâncias a título de comissões e corretagens relativas a prestação de serviços de distribuição de refeições pelo sistema de refeições-convênio', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (20009, 'Importâncias a título de comissões e corretagens relativas a prestação de serviço de administração de convênios', 'PJ', 'IR' );
        INSERT INTO naturezarendimento (e167_codigo, e167_descricao, e167_declarante, e167_tributo) VALUES (20010, 'Demais Importâncias a título de comissões, corretagens, ou qualquer outra importância paga/creditada pela representação comercial ou pela mediação na realização de negócios civis e comerciais', 'PJ', 'IR' );

SQL;
        DB::connection()->getPdo()->exec($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP TABLE empenho.naturezarendimento");
    }
}
