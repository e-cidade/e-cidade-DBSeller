<?php

use App\Domain\Patrimonial\Ouvidoria\Model\TipoProcessoFormaReclamacao;
use App\Domain\Patrimonial\Ouvidoria\Model\FormaReclamacao;
use App\Domain\Patrimonial\Protocolo\Model\Processo\TipoProc;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M23845RedesimIframe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issqn.establishment_data_batches', function (Blueprint $table) {
            $table->increments('q189_sequencial');
            $table->boolean('q189_processed');
            $table->text('q189_data');
            $table->timestamp('q189_created_at');
            $table->timestamp('q189_updated_at');
        });

        Schema::create('issqn.processed_establishments', function (Blueprint $table) {
            $table->increments('q190_sequencial');
            $table->string('q190_external_id');
            $table->integer('q190_establishment_data_batch');
            $table->integer('q190_process_id')->nullable();
            $table->boolean('q190_processed');
            $table->boolean('q190_replied')->default(false);
            $table->timestamp('q190_created_at');
            $table->timestamp('q190_updated_at');

            $table->foreign("q190_establishment_data_batch")->references("q189_sequencial")->on("establishment_data_batches");
        });

        Schema::create('issqn.redesim_events', function (Blueprint $table) {
            $table->increments('q191_sequencial');
            $table->string('q191_external_id');
            $table->string('q191_description');
            $table->string('q191_event_type');
            $table->timestamp('q191_created_at');
            $table->timestamp('q191_updated_at');
        });

        Schema::create('issqn.processed_establishment_events', function (Blueprint $table) {
            $table->increments('q192_sequencial');
            $table->integer('q192_redesim_event');
            $table->integer('q192_processed_establishment');
            $table->timestamp('q192_created_at');
            $table->timestamp('q192_updated_at');

            $table->foreign("q192_processed_establishment")->references("q190_sequencial")->on("processed_establishments");
            $table->foreign("q192_redesim_event")->references("q191_sequencial")->on("redesim_events");
        });

        Schema::create('issqn.processed_establishment_datas', function (Blueprint $table) {
            $table->increments('q193_sequencial');
            $table->text('q193_data');
            $table->integer('q193_processed_establishment');
            $table->timestamp('q193_created_at');
            $table->timestamp('q193_updated_at');

            $table->foreign("q193_processed_establishment")->references("q190_sequencial")->on("processed_establishments");
        });

        Schema::create('issqn.automatic_establishment_process', function (Blueprint $table) {
            $table->increments('q194_sequencial');
            $table->integer('q194_processed_establishment');
            $table->boolean('q194_processed');
            $table->boolean('q194_error');
            $table->timestamp('q194_created_at');
            $table->timestamp('q194_updated_at');

            $table->foreign("q194_processed_establishment")->references("q190_sequencial")->on("processed_establishments");
        });

        Schema::create('issqn.establishment_unprocessed_events', function (Blueprint $table) {
            $table->increments('q195_sequencial');
            $table->integer('q195_processed_establishment');
            $table->integer('q195_redesim_event');
            $table->timestamp('q195_created_at');
            $table->timestamp('q195_updated_at');

            $table->foreign("q195_processed_establishment")->references("q190_sequencial")->on("processed_establishments");
            $table->foreign("q195_redesim_event")->references("q191_sequencial")->on("redesim_events");
        });

        DB::connection()->getPdo()->exec(<<<SQL
            ALTER TABLE issalvara ALTER COLUMN q123_sequencial SET DEFAULT nextval('issalvara_q123_sequencial_seq'::regclass);
            ALTER TABLE issmovalvara ALTER COLUMN q120_sequencial SET DEFAULT nextval('issmovalvara_q120_sequencial_seq'::regclass);
            ALTER TABLE issmovalvaraprocesso ALTER COLUMN q124_sequencial SET DEFAULT nextval('issmovalvaraprocesso_q124_sequencial_seq'::regclass);
            ALTER TABLE isscadsimples ALTER COLUMN q38_sequencial SET DEFAULT nextval('isscadsimples_q38_sequencial_seq'::regclass);
            ALTER TABLE isscadsimplesbaixa ALTER COLUMN q39_sequencial SET DEFAULT nextval('isscadsimplebaixa_q39_sequencial_seq'::regclass);
SQL
        );

        $this->upEvents();

        $this->upFormaReclamacao();
        $this->upTipoProcesso("INCLUSÃO DE INSCRIÇÃO");
        $this->upTipoProcesso("ATUALIZAÇÃO DE INSCRIÇÃO");
        $this->upTipoProcesso("BAIXA DE INSCRIÇÃO");

        $this->upCgmAutoIncrement();

        $this->upAudit();
        $this->upDictionary();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('issqn.establishment_unprocessed_events');
        Schema::dropIfExists('issqn.automatic_establishment_process');
        Schema::dropIfExists('issqn.processed_establishment_datas');
        Schema::dropIfExists('issqn.processed_establishment_events');
        Schema::dropIfExists('issqn.redesim_events');
        Schema::dropIfExists('issqn.processed_establishments');
        Schema::dropIfExists('issqn.establishment_data_batches');

        $this->downProcessos();
        $this->downCgmAutoIncrement();
        $this->downAudit();
        $this->downDictionary();
    }

    private function upEvents()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            INSERT INTO redesim_events
                (
                    q191_external_id,
                    q191_description,
                    q191_event_type,
                    q191_created_at,
                    q191_updated_at
                )
            VALUES
                ('001', 'ENTRADA DE SÓCIO/ADMINISTRADOR', 'UPDATE', now(), now()),
                ('002', 'ALTERAÇÃO DA DATA DE INCLUSÃO', 'UPDATE', now(), now()),
                ('003', 'ALTERAÇÃO DE DADOS DO SÓCIO/ADMINISTRADOR', 'UPDATE', now(), now()),
                ('005', 'SAÍDA DE SÓCIO/ADMINISTRADOR', 'UPDATE', now(), now()),
                ('006', 'ALTERAÇÃO DA DATA DE EXCLUSÃO', 'UPDATE', now(), now()),
                ('007', 'ALTERAÇÃO DA DATA DE INCLUSÃO DE SÓCIO/ADMINISTRADOR EXCLUÍDO', 'UPDATE', now(), now()),
                ('008', 'EXCLUSÃO DE REGISTRO DE SÓCIO/ADMINISTRADOR', 'UPDATE', now(), now()),
                ('010', 'INFORMAÇÃO DE SÓCIO PARA CONFERÊNCIA COM A BASE CNPJ', 'UPDATE', now(), now()),
                ('011', 'ENTRADA DO BENEFICIÁRIO FINAL', 'UPDATE', now(), now()),
                ('012', 'ALTERAÇÃO DE DADOS DO BENEFICIÁRIO FINAL', 'UPDATE', now(), now()),
                ('013', 'SAÍDA DO BENEFICIÁRIO FINAL', 'UPDATE', now(), now()),
                ('052', 'REATIVAÇÃO - ARTIGO 60 LEI 8.934/94', 'UPDATE', now(), now()),
                ('101', 'INSCRIÇÃO DE PRIMEIRO ESTABELECIMENTO', 'CREATE', now(), now()),
                ('102', 'INSCRIÇÃO DOS DEMAIS ESTABELECIMENTOS', 'UPDATE', now(), now()),
                ('103', 'INSCRIÇÃO DE ESTABELECIMENTO FILIAL DE EMPRESA BRASILEIRA NO EXTERIOR', 'UPDATE', now(), now()),
                ('105', 'INSCRIÇÃO DE EMBAIXADA/CONSULADO/REPRESENTAÇÕES DO GOVERNO NO EXTERIOR', 'UPDATE', now(), now()),
                ('106', 'INSCRIÇÃO DE MISSÕES DIPL./REPART. CONSUL./REPRES. DE ÓRGÃOS INTERNACIONAIS', 'UPDATE', now(), now()),
                ('107', 'INSCRIÇÃO DE PESSOA JURÍDICA DOMICILIADA NO EXTERIOR', 'UPDATE', now(), now()),
                ('109', 'INSCRIÇÃO DE INCORPORAÇÃO IMOBILIÁRIA (PATRIMÔNIO DE AFETAÇÃO)', 'UPDATE', now(), now()),
                ('110', 'INSCRIÇÃO DE PRODUTOR RURAL (PRIMEIRO ESTABELECIMENTO)', 'UPDATE', now(), now()),
                ('111', 'INSCRIÇÃO DE PRODUTOR RURAL (DEMAIS ESTABELECIMENTOS)', 'UPDATE', now(), now()),
                ('150', 'PROTEÇÃO DE NOME EMPRESARIAL', 'UPDATE', now(), now()),
                ('202', 'ALTERAÇÃO DA PESSOA FÍSICA RESPONSÁVEL PERANTE O CNPJ', 'UPDATE', now(), now()),
                ('203', 'EXCLUSÃO DO TÍTULO DO ESTABELECIMENTO (NOME DE FANTASIA)', 'UPDATE', now(), now()),
                ('204', 'CISÃO PARCIAL (ESPECÍFICO PARA A SUCEDIDA)', 'UPDATE', now(), now()),
                ('206', 'DESCLASSIFICAÇÃO COMO ESTABELECIMENTO UNIFICADOR', 'UPDATE', now(), now()),
                ('209', 'ALTERAÇÃO DE ENDEREÇO ENTRE MUNICÍPIOS DENTRO DO MESMO ESTADO', 'UPDATE', now(), now()),
                ('210', 'ALTERAÇÃO DE ENDEREÇO ENTRE ESTADOS', 'UPDATE', now(), now()),
                ('211', 'ALTERAÇÃO DE ENDEREÇO DENTRO DO MESMO MUNICÍPIO', 'UPDATE', now(), now()),
                ('214', 'ALTERAÇÃO DE TELEFONE (DDD/TELEFONE)', 'UPDATE', now(), now()),
                ('215', 'EXCLUSÃO DE TELEFONE (DDD/TELEFONE)', 'UPDATE', now(), now()),
                ('216', 'ALTERAÇÃO DE FAX (DDD/FAX)', 'UPDATE', now(), now()),
                ('217', 'EXCLUSÃO DE FAX (DDD/FAX)', 'UPDATE', now(), now()),
                ('218', 'ALTERAÇÃO DE CORREIO ELETRÔNICO', 'UPDATE', now(), now()),
                ('219', 'EXCLUSÃO DE CORREIO ELETRÔNICO', 'UPDATE', now(), now()),
                ('220', 'ALTERAÇÃO DO NOME EMPRESARIAL  (FIRMA OU DENOMINAÇÃO)', 'UPDATE', now(), now()),
                ('221', 'ALTERAÇÃO DO TÍTULO DO ESTABELECIMENTO (NOME DE FANTASIA)', 'UPDATE', now(), now()),
                ('222', 'ENQUADRAMENTO / REENQUADRAMENTO / DESENQUADRAMENTO DE ME/EPP', 'UPDATE', now(), now()),
                ('224', 'ALTERAÇÃO DO CONTABILISTA RESPONSÁVEL PELA ORGANIZAÇÃO CONTÁBIL PERANTE O CRC', 'UPDATE', now(), now()),
                ('225', 'ALTERAÇÃO DA NATUREZA JURÍDICA', 'UPDATE', now(), now()),
                ('230', 'ALTERAÇÃO DA QUALIFICAÇÃO DA PESSOA FÍSICA RESPONSÁVEL PERANTE O CNPJ', 'UPDATE', now(), now()),
                ('232', 'ALTERAÇÃO DO CONTABILISTA OU DA EMPRESA DE CONTABILIDADE', 'UPDATE', now(), now()),
                ('233', 'EXCLUSÃO DO CONTABILISTA OU DA EMPRESA DE CONTABILIDADE', 'UPDATE', now(), now()),
                ('235', 'ALTERAÇÃO DO ADMINISTRADOR DE EMPRESAS (FUNDOS/CLUBES E EQUIPARADAS)', 'UPDATE', now(), now()),
                ('237', 'INDICAÇÃO DE PREPOSTO', 'UPDATE', now(), now()),
                ('238', 'SUBSTITUIÇÃO DE PREPOSTO', 'UPDATE', now(), now()),
                ('239', 'EXCLUSÃO DO PREPOSTO', 'UPDATE', now(), now()),
                ('240', 'RENÚNCIA DO PREPOSTO', 'UPDATE', now(), now()),
                ('241', 'EQUIPARAÇÃO, POR OPÇÃO, A ESTABELECIMENTO INDUSTRIAL', 'UPDATE', now(), now()),
                ('242', 'DESISTÊNCIA DA EQUIPARAÇÃO, POR OPÇÃO, A ESTABELECIMENTO INDUSTRIAL', 'UPDATE', now(), now()),
                ('243', 'ALTERAÇÃO DE ENDEREÇO DE PESSOA JURÍDICA DOMICILIADA NO EXTERIOR', 'UPDATE', now(), now()),
                ('244', 'ALTERAÇÃO DE ATIVIDADES ECONOMICAS (PRINCIPAL E SECUNDÁRIAS)', 'UPDATE', now(), now()),
                ('246', 'INDICAÇÃO DE ESTABELECIMENTO MATRIZ', 'UPDATE', now(), now()),
                ('247', 'ALTERAÇÃO DE CAPITAL SOCIAL', 'UPDATE', now(), now()),
                ('248', 'ALTERAÇÃO DO TIPO DE UNIDADE', 'UPDATE', now(), now()),
                ('249', 'ALTERAÇÃO DA FORMA DE ATUAÇÃO', 'UPDATE', now(), now()),
                ('250', 'ALTERAÇÃO DO VÍNCULO COM O IMÓVEL', 'UPDATE', now(), now()),
                ('251', 'ALTERAÇÃO DA DATA DE VALIDADE DA INSCRIÇÃO', 'UPDATE', now(), now()),
                ('252', 'ALTERAÇÃO DO NIRF', 'UPDATE', now(), now()),
                ('253', 'ALTERAÇÃO DO PROPRIETÁRIO', 'UPDATE', now(), now()),
                ('254', 'ALTERAÇÃO DO NOME EMPRESARIAL (ESPECÍFICO PARA PRODUTOR RURAL)', 'UPDATE', now(), now()),
                ('255', 'EXCLUSÃO DE NIRE', 'UPDATE', now(), now()),
                ('256', 'ALTERAÇÃO DA INSCRIÇÃO ESTADUAL ANTERIOR', 'UPDATE', now(), now()),
                ('257', 'ALTERAÇÃO DO NÚMERO DE REGISTRO NO ÓRGÃO COMPETENTE', 'UPDATE', now(), now()),
                ('260', 'ALTERAÇÃO/INCLUSÃO DE ENTE FEDERATIVO RESPONSÁVEL', 'UPDATE', now(), now()),
                ('261', 'EXCLUSÃO DO NÚMERO DE REGISTRO NO ÓRGÃO COMPETENTE', 'UPDATE', now(), now()),
                ('262', 'ALTERAÇÃO DA DEPENDÊNCIA ORÇAMENTÁRIA', 'UPDATE', now(), now()),
                ('263', 'ALTERAÇÃO DO RESPONSÁVEL - PRODUTOR RURAL', 'UPDATE', now(), now()),
                ('264', 'ALTERAÇÃO DE TIPO DE PRODUTOR RURAL (INDIVIDUAL OU SOCIEDADE)', 'UPDATE', now(), now()),
                ('265', 'RENÚNCIA/ EXCLUSÃO DO REPRESENTANTE', 'UPDATE', now(), now()),
                ('266', 'ATUALIZAÇÃO DE CEP DA PESSOA JURÍDICA', 'UPDATE', now(), now()),
                ('267', 'INFORMAÇÕES DE BENEFICIÁRIO FINAL', 'UPDATE', now(), now()),
                ('268', 'ALTERAÇÃO DO ENDEREÇO DE CORRESPONDÊNCIA', 'UPDATE', now(), now()),
                ('303', 'EXCLUSÃO SIMPLES FEDERAL POR DÉBITO P/ COM FAZENDA NACIONAL OU PREVIDÊNCIA  SOCIAL', 'UPDATE', now(), now()),
                ('304', 'EXCLUSÃO DO SIMPLES FEDERAL POR  ULTRAPASSAR OS LIMITES DE RECEITA BRUTA', 'UPDATE', now(), now()),
                ('305', 'EXCLUSÃO DO SIMPLES FEDERAL POR TRANSFORMAÇÃO PARA A FORMA DE SOCIEDADE POR AÇÕES', 'UPDATE', now(), now()),
                ('306', 'EXCLUSÃO DO SIMPLES FEDERAL POR EXERCÍCIO DE ATIVIDADE ECONÔMICA VEDADA', 'UPDATE', now(), now()),
                ('307', 'EXCLUSÃO DO SIMPLES FEDERAL POR INGRESSO DE SÓCIO ESTRANGEIRO RESIDENTE NO EXTERIOR', 'UPDATE', now(), now()),
                ('308', 'EXCLUSÃO SIMPLES FEDERAL POR TRANSF. FILIAL,SUC.,AG. OU REPRES. DE PJ COM SEDE NO EXTERIOR', 'UPDATE', now(), now()),
                ('309', 'EXCLUSÃO DO SIMPLES FEDERAL POR PARTICIPAÇÃO NO CAPITAL DE OUTRA PESSOA JURÍDICA', 'UPDATE', now(), now()),
                ('310', 'EXCLUSÃO SIMPLES FEDERAL POR EXIST. TITULAR/SÓCIO REALIZE GASTOS INCOMP. COM  RENDIMENTOS DECLARADOS', 'UPDATE', now(), now()),
                ('311', 'EXCLUSÃO SIMPLES FEDERAL POR PARTICIPAÇÃO DO TITULAR OU SÓCIO NO CAPITAL DE OUTRA EMPRESA', 'UPDATE', now(), now()),
                ('312', 'EXCLUSÃO SIMPLES FEDERAL POR PARTICIPAÇÃO DE OUTRA PESSOA JURÍDICA NO CAPITAL DA EMPRESA', 'UPDATE', now(), now()),
                ('313', 'EXCLUSÃO DO SIMPLES FEDERAL POR RECEITA DE VENDA DE BENS IMPORTADOS SUPERIOR AO LIMITE', 'UPDATE', now(), now()),
                ('314', 'EXCLUSÃO DO SIMPLES FEDERAL POR PRÁTICA DE EMBARAÇO OU RESISTÊNCIA À FISCALIZAÇÃO', 'UPDATE', now(), now()),
                ('315', 'EXCLUSÃO DO SIMPLES FEDERAL RETROATIVA À DATA DA OPÇÃO / ABERTURA', 'UPDATE', now(), now()),
                ('316', 'ALTERAÇÃO DE TRIBUTOS DO SIMPLES FEDERAL', 'UPDATE', now(), now()),
                ('317', 'DESFAZ EXCLUSÃO INDEVIDA', 'UPDATE', now(), now()),
                ('318', 'DESFAZ INCLUSÃO INDEVIDA', 'UPDATE', now(), now()),
                ('319', 'INCLUSÃO NO SIMPLES FEDERAL POR DECISÃO ADMINISTRATIVA', 'UPDATE', now(), now()),
                ('320', 'INCLUSÃO NO SIMPLES FEDERAL POR MANDADO JUDICIAL', 'UPDATE', now(), now()),
                ('321', 'EXCLUSÃO DO SIMPLES FEDERAL POR DECISÃO ADMINISTRATIVA', 'UPDATE', now(), now()),
                ('322', 'EXCLUSÃO SIMPLES FEDERAL EMPRESA RESULTANTE CISÃO OU QUALQUER FORMA DE DESMEMBRAMENTO', 'UPDATE', now(), now()),
                ('325', 'EXCLUSÃO DO SIMPLES FEDERAL POR INDUSTRIALIZAR BEBIDAS OU CIGARROS', 'UPDATE', now(), now()),
                ('327', 'EVENTO DE ALTERAÇÃO DE PERÍODO DO SIMPLES NACIONAL', 'UPDATE', now(), now()),
                ('405', 'DECRETAÇÃO DE FALÊNCIA', 'UPDATE', now(), now()),
                ('406', 'REABILITAÇÃO DE FALÊNCIA', 'UPDATE', now(), now()),
                ('407', 'ESPÓLIO DE EMPRESÁRIO, EMPRESA INDIVIDUAL IMOBILIÁRIA, EIRELI OU TITULAR DE EMPRESA UNIPESSOAL DE ADVOCACIA', 'UPDATE', now(), now()),
                ('408', 'TÉRMINO DE LIQUIDAÇÃO', 'UPDATE', now(), now()),
                ('410', 'INÍCIO DA INTERVENÇÃO', 'UPDATE', now(), now()),
                ('411', 'ENCERRAMENTO DA INTERVENÇÃO', 'UPDATE', now(), now()),
                ('412', 'INTERRUPÇÃO TEMPORÁRIA DE ATIVIDADES', 'UPDATE', now(), now()),
                ('413', 'REINÍCIO DAS ATIVIDADES INTERROMPIDAS TEMPORARIAMENTE', 'UPDATE', now(), now()),
                ('414', 'RESTABELECIMENTO DE INSCRIÇÃO DA ENTIDADE', 'UPDATE', now(), now()),
                ('415', 'RESTABELECIMENTO DE INSCRIÇÃO DE FILIAL', 'UPDATE', now(), now()),
                ('416', 'INÍCIO DE LIQUIDAÇÃO JUDICIAL', 'UPDATE', now(), now()),
                ('417', 'INÍCIO DE LIQUIDAÇÃO EXTRAJUDICIAL', 'UPDATE', now(), now()),
                ('418', 'RECUPERAÇÃO JUDICIAL', 'UPDATE', now(), now()),
                ('419', 'ENCERRAMENTO DE RECUPERAÇÃO JUDICIAL', 'UPDATE', now(), now()),
                ('510', 'EXTINÇÃO POR DETERMINAÇÃO JUDICIAL', 'UPDATE', now(), now()),
                ('514', 'ANULAÇÃO DE INSCRIÇÃO INDEVIDA', 'UPDATE', now(), now()),
                ('516', 'ANULAÇÃO POR VÍCIO', 'UPDATE', now(), now()),
                ('517', 'PEDIDO DE BAIXA', 'LOW', now(), now()),
                ('518', 'BAIXA - OMISSÃO CONTUMAZ', 'LOW', now(), now()),
                ('519', 'BAIXA - INEXISTÊNCIA DE FATO', 'LOW', now(), now()),
                ('522', 'BAIXA - INAPTIDÃO', 'LOW', now(), now()),
                ('523', 'BAIXA - REGISTRO CANCELADO', 'LOW', now(), now()),
                ('601', 'INSCRIÇÃO NO ESTADO', 'UPDATE', now(), now()),
                ('602', 'INSCRIÇÃO DE SUBSTITUTO TRIBUTÁRIO NO ESTADO', 'UPDATE', now(), now()),
                ('603', 'REATIVAÇÃO DA INSCRIÇÃO NO ESTADO', 'UPDATE', now(), now()),
                ('604', 'PEDIDO DE BAIXA EXCLUSIVAMENTE NO ESTADO', 'UPDATE', now(), now()),
                ('605', 'ALTERAÇÃO DO ENDEREÇO DE CORRESPONDÊNCIA', 'UPDATE', now(), now()),
                ('606', 'INSCRIÇÃO NO ESTADO PARA ESTABELECIMENTO QUE ESTÁ LOCALIZADO EM OUTRO ESTADO, EXCETO SUBST. TRIB.', 'UPDATE', now(), now()),
                ('607', 'PEDIDO DE BAIXA DE SUBSTITUTO TRIBUTÁRIO', 'LOW', now(), now()),
                ('608', 'REATIVAÇÃO DE SUBSTITUTO TRIBUTÁRIO NO ESTADO', 'UPDATE', now(), now()),
                ('611', 'ALTERAÇÃO DO REGIME DE APURAÇÃO NO ESTADO', 'UPDATE', now(), now()),
                ('612', 'ALTERAÇÃO DE DADOS DA LICENÇA AMBIENTAL', 'UPDATE', now(), now()),
                ('613', 'ALTERAÇÃO DA CONDIÇÃO DE SUBSTITUTO TRIBUTÁRIO', 'UPDATE', now(), now()),
                ('621', 'ALTERAÇÃO DE CONDIÇÃO / REGIME DE APURAÇÃO', 'UPDATE', now(), now()),
                ('624', 'ALTERAÇÃO DA LOCALIZAÇÃO', 'UPDATE', now(), now()),
                ('625', 'OPÇÃO OU EXCLUSÃO DA INSCRIÇÃO ÚNICA', 'UPDATE', now(), now()),
                ('626', 'INCLUSÃO DE ESTABELECIMENTO DA INSCRIÇÃO ÚNICA', 'UPDATE', now(), now()),
                ('627', 'EXCLUSÃO DE ESTABELECIMENTO DA INSCRIÇÃO ÚNICA', 'UPDATE', now(), now()),
                ('628', 'ALTERAÇÃO DO TIPO DE CONTRIBUINTE', 'UPDATE', now(), now()),
                ('629', 'ALTERAÇÃO DA OPÇÃO POR LIVROS/DOCUMENTOS ELETRÔNICOS', 'UPDATE', now(), now()),
                ('630', 'ALTERAÇÃO DA PERMANÊNCIA DE LIVROS FISCAIS', 'UPDATE', now(), now()),
                ('632', 'ALTERAÇÃO DO TIPO DE CONVÊNIO / PROTOCOLO DE SUBSTITUIÇÃO TRIBUTÁRIA', 'UPDATE', now(), now()),
                ('633', 'ALTERAÇÃO DO PROCURADOR NO ESTADO', 'UPDATE', now(), now()),
                ('701', 'ALTERAÇÃO DO ENDEREÇO RESIDENCIAL (EVENTO EXCLUSIVO DO MEI)', 'UPDATE', now(), now()),
                ('702', 'ALTERAÇÃO DO REGISTRO DE IDENTIDADE (EVENTO EXCLUSIVO DO MEI)', 'UPDATE', now(), now()),
                ('703', 'ALTERAÇÃO DO CÓDIGO DE OCUPAÇÃO (PRINCIPAL E SECUNDÁRIO) (EVENTO EXCLUSIVO DO MEI)', 'UPDATE', now(), now()),
                ('801', 'INSCRIÇÃO NO MUNICÍPIO', 'UPDATE', now(), now()),
                ('802', 'INSCRIÇÃO MUNICIPAL VINCULADA A CNPJ JÁ CADASTRADO PARA OUTRO ESTABELECIMENTO', 'UPDATE', now(), now()),
                ('803', 'INSCRIÇÃO PARA ESTABELECIMENTO SEDIADO EM OUTRO MUNICÍPIO', 'UPDATE', now(), now()),
                ('804', 'PEDIDO DE BAIXA EXCLUSIVAMENTE NO MUNICÍPIO', 'LOW', now(), now()),
                ('805', 'CORREÇÃO DO NÚMERO DE INSCRIÇÃO IMOBILIÁRIA', 'UPDATE', now(), now()),
                ('806', 'ALTERAÇÃO DE ÁREA', 'UPDATE', now(), now()),
                ('808', 'RENOVAÇÃO DO TVL (ALVARÁ)', 'UPDATE', now(), now()),
                ('809', 'INFORMAÇÃO DE CÓDIGO DE ANÚNCIO', 'UPDATE', now(), now()),
                ('810', 'INSCRIÇÃO NO MUNICÍPIO DE SÃO PAULO PARA ESTABELECIMENTO SEDIADO EM OUTRO MUNICÍPIO', 'UPDATE', now(), now()),
                ('811', 'INFORMAÇÃO DO NÚMERO CCM CENTRALIZADOR', 'UPDATE', now(), now()),
                ('812', 'ALTERAÇÃO DO ENDEREÇO DO ESTABELECIMENTO VINCULADO', 'UPDATE', now(), now()),
                ('813', 'ALTERAÇÃO DO TELEFONE DO ESTABELECIMENTO VINCULADO', 'UPDATE', now(), now()),
                ('814', 'ALTERAÇÃO DO REGIME DE TRIBUTAÇÃO', 'UPDATE', now(), now()),
                ('850', 'ALVARÁ MUNICIPAL', 'UPDATE', now(), now()),
                ('851', 'ALVARÁ SANITÁRIO MUNICIPAL', 'UPDATE', now(), now()),
                ('852', 'ALVARÁ DO MEIO AMBIENTE MUNICIPAL', 'UPDATE', now(), now()),
                ('853', 'LICENCIAMENTO DE TRANSPORTES', 'UPDATE', now(), now()),
                ('854', 'ALVARÁ AGRÍCULA MUNICIPAL', 'UPDATE', now(), now()),
                ('855', 'ALVARÁ DE EDUCAÇÃO MUNICIPAL', 'UPDATE', now(), now()),
                ('901', 'INCLUSÃO DE CNPJ - MATRIZ OU FILIAL', 'UPDATE', now(), now()),
                ('902', 'INSCRIÇÃO DE ESTABELECIMENTO POR DETERMINAÇÃO JUDICIAL', 'UPDATE', now(), now()),
                ('906', 'ANULAÇÃO POR MULTIPLICIDADE DE INSCRIÇÃO', 'UPDATE', now(), now()),
                ('909', 'RESTAURAÇÃO DA SITUAÇÃO CADASTRAL ANTERIOR', 'UPDATE', now(), now()),
                ('910', 'ALTERAÇÃO DE DATA DE ABERTURA', 'UPDATE', now(), now()),
                ('912', 'SUSPENSÃO - BAIXA RECEPCIONADA (EM ANÁLISE)', 'LOW', now(), now()),
                ('913', 'SUSPENSÃO - BAIXA INDEFERIDA', 'LOW', now(), now()),
                ('915', 'SUSPENSÃO - INEXISTÊNCIA DE FATO', 'UPDATE', now(), now()),
                ('916', 'INAPTIDÃO - INEXISTÊNCIA DE FATO', 'UPDATE', now(), now()),
                ('922', 'DESFAZ INAPTIDÃO POR DETERMINAÇÃO JUDICIAL', 'UPDATE', now(), now()),
                ('923', 'ALTERAÇÃO DE DATA DE PUBLICAÇÃO / DATA DE EFEITO', 'UPDATE', now(), now()),
                ('924', 'CORREÇÃO DA DATA DE BAIXA', 'UPDATE', now(), now()),
                ('926', 'DESFAZ CISÃO PARCIAL', 'UPDATE', now(), now()),
                ('927', 'SUSPENSÃO - INDÍCIO DE INTERPOSIÇÃO FRAUDULENTA', 'UPDATE', now(), now()),
                ('928', 'SUSPENSÃO - FALTA DE PLURALIDADE DE SÓCIOS', 'UPDATE', now(), now()),
                ('929', 'CORREÇÃO DA DATA DE RESPONSABILIDADE', 'UPDATE', now(), now()),
                ('938', 'SUSPENSÃO - DETERMINAÇÃO JUDICIAL', 'UPDATE', now(), now()),
                ('939', 'INAPTIDÃO - OMISSÃO DE DECLARAÇÕES', 'UPDATE', now(), now()),
                ('940', 'INAPTIDÃO - LOCALIZAÇÃO DESCONHECIDA', 'UPDATE', now(), now()),
                ('941', 'SUBSTITUIÇÃO/ELIMINAÇÃO DO REGISTRO DE RESPONSABILIDADE', 'UPDATE', now(), now()),
                ('942', 'SUSPENSÃO ? INCONSISTÊNCIA CADASTRAL', 'UPDATE', now(), now()),
                ('990', 'CORREÇÃO DE DADOS CADASTRAIS NO ÓRGÃO DE REGISTRO', 'UPDATE', now(), now()),
                ('9001', '9001 - ALTERAÇÃO DO NOME EMPRESARIAL', 'UPDATE', now(), now()),
                ('9002', '9002 - ALTERAÇÃO DA NATUREZA JURÍDICA', 'UPDATE', now(), now()),
                ('9003', '9003 - ALTERAÇÃO DO NOME FANTASIA', 'UPDATE', now(), now()),
                ('9004', '9004 - ALTERAÇÃO DO CAPITAL SOCIAL', 'UPDATE', now(), now()),
                ('9005', '9005 - ALTERAÇÃO DO CAPITAL INTEGRALIZADO', 'UPDATE', now(), now()),
                ('9006', '9006 - ALTERAÇÃO DO OBJETO SOCIAL', 'UPDATE', now(), now()),
                ('9007', '9007 - ALTERAÇÃO DOS DADOS DO CONTATO', 'UPDATE', now(), now()),
                ('9008', '9008 - ALTERAÇÃO DE EMAIL', 'UPDATE', now(), now()),
                ('9009', '9009 - ALTERAÇÃO DA SITUAÇÃO DO ESTABELECIMENTO', 'UPDATE', now(), now()),
                ('9010', '9010 - ALTERAÇÃO DE ENDEREÇO', 'UPDATE', now(), now()),
                ('9011', '9011 - ALTERAÇÃO DE ATIVIDADES ECONÔMICAS', 'UPDATE', now(), now()),
                ('9012', '9012 - ALTERAÇÃO DE DADOS DO SÓCIO/REPRESENTANTE', 'UPDATE', now(), now()),
                ('9013', '9013 - ALTERAÇÃO DE PORTE EMPRESARIAL', 'UPDATE', now(), now()),
                ('995', 'INSCRIÇÃO SUFRAMA', 'UPDATE', now(), now()),
                ('996', 'INSCRIÇÃO MEIO AMBIENTE', 'UPDATE', now(), now()),
                ('997', 'INSCRIÇÃO BOMBEIROS', 'UPDATE', now(), now()),
                ('998', 'INSCRIÇÃO VIGILÂNCIA SANITÁRIA', 'UPDATE', now(), now()),
                ('999', 'LICENCIAMENTO DE ESTABELECIMENTO ANTERIORMENTE REGISTRADO (LEGADO)', 'UPDATE', now(), now());
SQL
        );
    }

    private function upFormaReclamacao()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            INSERT INTO formareclamacao
                (
                    p42_sequencial,
                    p42_descricao
                )
                VALUES
                (
                    nextval('formareclamacao_p42_sequencial_seq'),
                    'REDESIM IFRAME'
                );
SQL
        );
    }

    private function upTipoProcesso($description)
    {
        DB::connection()->getPdo()->exec(<<<SQL
            INSERT INTO tipoproc
                (
                    p51_codigo,
                    p51_descr,
                    p51_dtlimite,
                    p51_instit,
                    p51_tipoprocgrupo,
                    p51_identificado,
                    p51_prottipodocumentoprocesso,
                    p51_linksaibamais,
                    p51_itemmenu,
                    p51_mensagem
                )
            VALUES
                (
                    nextval('tipoproc_p51_codigo_seq'),
                    '$description',
                    null,
                    1,
                    1,
                    't',
                    1,
                    '',
                    '',
                    ''
                );


            INSERT INTO tipoprocformareclamacao
                (
                    p43_sequencial,
                    p43_formareclamacao,
                    p43_tipoproc
                )
            VALUES
                (
                    NEXTVAL('tipoprocformareclamacao_p43_sequencial_seq'),
                    CURRVAL('formareclamacao_p42_sequencial_seq'),
                    CURRVAL('tipoproc_p51_codigo_seq')
                );
SQL
        );
    }

    private function downProcessos()
    {
        $formaReclamacao = FormaReclamacao::where("p42_descricao", 'REDESIM IFRAME')->first();
        $tipoProcessoFormasReclamacao = TipoProcessoFormaReclamacao::where(
            "p43_formareclamacao",
            $formaReclamacao->p42_sequencial
        )->get();

        $codigoProcessos = $tipoProcessoFormasReclamacao->map(function ($tipoProcessoFormaReclamacao) {
            return $tipoProcessoFormaReclamacao->p43_tipoproc;
        });

        $tipoProcs = TipoProc::whereIn("p51_codigo", $codigoProcessos->toArray())->get();

        $tipoProcessoFormasReclamacao->each(function ($tipoProcessoFormaReclamacao) {
            $tipoProcessoFormaReclamacao->delete();
        });

        $tipoProcs->each(function ($tipoProc) {
            $tipoProc->delete();
        });

        $formaReclamacao->delete();
    }

    private function upCgmAutoIncrement()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER TABLE protocolo.cgm ALTER COLUMN z01_numcgm SET DEFAULT NEXTVAL('cgm_z01_numcgm_seq');
SQL
        );
    }

    private function downCgmAutoIncrement()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            ALTER TABLE protocolo.cgm ALTER COLUMN z01_numcgm DROP DEFAULT;
SQL
        );
    }

    private function upAudit()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.establishment_data_batches');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.processed_establishments');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.redesim_events');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.processed_establishment_events');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.processed_establishment_datas');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.automatic_establishment_process');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.establishment_unprocessed_events');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.inscricaoredesim');

            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.ativprinc');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.certbaixanumero');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.issalvara');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.issbairro');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.issbase');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.issbaseporte');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.issmovalvara');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.issmovalvaraprocesso');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.issmovalvarabaixa');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.issprocesso');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.issquant');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.issruas');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.isszona');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.isscadsimples');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.isscadsimplesbaixa');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.socios');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.tabativ');
            SELECT configuracoes.fc_auditoria_cria_funcao('issqn.tabativbaixa');

            SELECT configuracoes.fc_auditoria_cria_funcao('protocolo.cgm');

            SELECT configuracoes.fc_auditoria_cria_funcao('protocolo.protprocesso');
SQL
        );
    }

    private function downAudit()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.establishment_data_batches');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.processed_establishments');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.redesim_events');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.processed_establishment_events');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.processed_establishment_datas');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.automatic_establishment_process');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.establishment_unprocessed_events');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.inscricaoredesim');

            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.ativprinc');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.certbaixanumero');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.issalvara');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.issbairro');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.issbase');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.issbaseporte');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.issmovalvara');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.issmovalvaraprocesso');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.issmovalvarabaixa');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.issprocesso');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.issquant');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.issruas');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.isszona');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.isscadsimples');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.isscadsimplesbaixa');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.socios');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.tabativ');
            SELECT configuracoes.fc_auditoria_remove_funcao('issqn.tabativbaixa');

            SELECT configuracoes.fc_auditoria_remove_funcao('protocolo.cgm');

            SELECT configuracoes.fc_auditoria_remove_funcao('protocolo.protprocesso');
SQL
        );
    }

    private function upDictionary()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            insert into db_sysarquivo values (1011117, 'establishment_data_batches', 'Tabela que salva o JSON com o dados vindos da REDESIM para ser processado posteriormente.', 'q189', '2023-07-15', 'Dados estabelecimentos REDESIM', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (3,1011117);
            insert into db_syscampo values(1015246,'q189_sequencial','int4','Sequencial da tabela establishment_data_batches','0', 'Sequencial da tabela establishment_data_batches',11,'f','f','f',1,'text','Sequencial');
            insert into db_syscampo values(1015247,'q189_processed','text','Seta se os dados da já foram processados','', 'Processado',11,'f','f','f',0,'text','Processado');
            insert into db_syscampo values(1015248,'q189_data','text','Campo que salva o JSON com os dados vindos da REDESIM','', 'Dados',5000,'f','f','f',0,'text','Dados');
            insert into db_syscampo values(1015249,'q189_created_at','text','Data em que os dados foram cadastrados','', 'Data do cadastro',20,'f','f','f',0,'text','Data do cadastro');
            insert into db_syscampo values(1015250,'q189_updated_at','text','Data em que os dados foram atualizados','', 'Data de atualização',20,'f','f','f',0,'text','Data de atualização');
            delete from db_sysarqcamp where codarq = 1011117;
            insert into db_sysarqcamp values(1011117,1015246,1,0);
            insert into db_sysarqcamp values(1011117,1015249,2,0);
            insert into db_sysarqcamp values(1011117,1015250,3,0);
            insert into db_sysarqcamp values(1011117,1015247,4,0);
            insert into db_sysarqcamp values(1011117,1015248,5,0);
            delete from db_sysprikey where codarq = 1011117;
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011117,1015246,1,1015246);
            insert into db_syssequencia values(1001143, 'establishment_data_batches_q189_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
            update db_sysarqcamp set codsequencia = 1001143 where codarq = 1011117 and codcam = 1015246;


            insert into db_sysarquivo values (1011118, 'processed_establishments', 'Tabela que salva individualmente os estabelecimentos que retornou da consulta com a REDESIM.', 'q190', '2023-07-15', 'Estabelecimentos processados', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (3,1011118);
            insert into db_syscampo values(1015251,'q190_sequencial','float8','Sequencial da tabela processed_establishments','0', 'Sequencial',11,'f','f','f',4,'text','Sequencial');
            insert into db_syscampo values(1015252,'q190_external_id','text','ID do estabelecimento na base da REDESIM','', 'ID Externo',20,'f','t','f',0,'text','ID Externo');
            insert into db_syscampo values(1015253,'q190_establishment_data_batch','float8','Sequencial do lote que o estabelecimento veio','0', 'Sequencial do lote',11,'f','f','f',4,'text','Sequencial do lote');
            insert into db_syscampo values(1015254,'q190_process_id','float8','Código do processo','0', 'Código do processo',11,'t','f','f',4,'text','Código do processo');
            insert into db_syscampo values(1015255,'q190_processed','text','Campo que seta se os eventos para o estabelecimento já foram processados','', 'Processado',11,'f','f','f',0,'text','Processado');
            insert into db_syscampo values(1015256,'q190_replied','text','Campo que seta de já foi enviado resposta para a REDESIM','', 'Respondido',11,'f','f','f',0,'text','Respondido');
            insert into db_syscampo values(1015257,'q190_created_at','text','Data do cadastro','', 'Data do cadastro',20,'f','t','f',0,'text','Data do cadastro');
            insert into db_syscampo values(1015258,'q190_updated_at','text','Data de atualização','', 'Data de atualização',20,'f','f','f',0,'text','Data de atualização');
            delete from db_sysarqcamp where codarq = 1011118;
            insert into db_sysarqcamp values(1011118,1015251,1,0);
            insert into db_sysarqcamp values(1011118,1015252,2,0);
            insert into db_sysarqcamp values(1011118,1015253,3,0);
            insert into db_sysarqcamp values(1011118,1015254,4,0);
            insert into db_sysarqcamp values(1011118,1015257,5,0);
            insert into db_sysarqcamp values(1011118,1015255,6,0);
            insert into db_sysarqcamp values(1011118,1015256,7,0);
            insert into db_sysarqcamp values(1011118,1015258,8,0);
            delete from db_sysprikey where codarq = 1011118;
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011118,1015251,1,1015251);
            insert into db_syssequencia values(1001144, 'processed_establishments_q190_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
            update db_sysarqcamp set codsequencia = 1001144 where codarq = 1011118 and codcam = 1015251;

            insert into db_sysarquivo values (1011119, 'redesim_events', 'Tabela que salva os eventos que a REDESIM envia', 'q191', '2023-07-15', 'Eventos da REDESIM', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (3,1011119);
            insert into db_syscampo values(1015259,'q191_sequencial','float8','Sequencial da tabela redesim_events','0', 'Sequencial',11,'f','f','f',4,'text','Sequencial');
            insert into db_syscampo values(1015260,'q191_external_id','text','ID do evento na base da REDESIM','', 'ID Externo',11,'f','f','f',0,'text','ID Externo');
            insert into db_syscampo values(1015261,'q191_description','text','Descrição do evento','', 'Descrição',500,'f','f','f',0,'text','Descrição');
            insert into db_syscampo values(1015262,'q191_event_type','text','Tipo do evento','', 'Tipo do Evento',50,'f','t','f',0,'text','Tipo do Evento');
            insert into db_syscampo values(1015263,'q191_created_at','text','Data do cadastro','', 'Data do cadastro',20,'f','f','f',0,'text','Data do cadastro');
            insert into db_syscampo values(1015264,'q191_updated_at','text','Data de atualização','', 'Data de atualização',20,'f','f','f',0,'text','Data de atualização');
            delete from db_sysarqcamp where codarq = 1011119;
            insert into db_sysarqcamp values(1011119,1015259,1,0);
            insert into db_sysarqcamp values(1011119,1015260,2,0);
            insert into db_sysarqcamp values(1011119,1015261,3,0);
            insert into db_sysarqcamp values(1011119,1015262,4,0);
            insert into db_sysarqcamp values(1011119,1015263,5,0);
            insert into db_sysarqcamp values(1011119,1015264,6,0);
            delete from db_sysprikey where codarq = 1011119;
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011119,1015259,1,1015259);
            insert into db_syssequencia values(1001145, 'redesim_events_q191_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
            update db_sysarqcamp set codsequencia = 1001145 where codarq = 1011119 and codcam = 1015259;

            insert into db_sysarquivo values (1011120, 'processed_establishment_events', 'Tabela que salva os eventos que devem ser processados para o estabelecimento', 'q192', '2023-07-15', 'Eventos do estabelecimento', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (3,1011120);
            insert into db_syscampo values(1015265,'q192_sequencial','int8','Sequencial da tabela processed_establishment_events','0', 'Sequencial',11,'f','f','f',1,'text','Sequencial');
            insert into db_syscampo values(1015266,'q192_redesim_event','int8','ID da tabela redesim_events','0', 'ID do Evento',11,'f','f','f',1,'text','ID do Evento');
            insert into db_syscampo values(1015267,'q192_processed_establishment','int8','ID da tabela processed_establishments','0', 'ID do estabelecimento',11,'f','f','f',1,'text','ID do estabelecimento');
            insert into db_syscampo values(1015268,'q192_created_at','text','Data do cadastro','', 'Data do cadastro',20,'f','t','f',0,'text','Data do cadastro');
            insert into db_syscampo values(1015269,'q192_updated_at','text','Data de atualização','', 'Data de atualização',20,'f','f','f',0,'text','Data de atualização');
            delete from db_sysarqcamp where codarq = 1011120;
            insert into db_sysarqcamp values(1011120,1015265,1,0);
            insert into db_sysarqcamp values(1011120,1015266,2,0);
            insert into db_sysarqcamp values(1011120,1015267,3,0);
            insert into db_sysarqcamp values(1011120,1015268,4,0);
            insert into db_sysarqcamp values(1011120,1015269,5,0);
            delete from db_sysprikey where codarq = 1011120;
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011120,1015265,1,1015265);
            insert into db_syssequencia values(1001146, 'processed_establishment_events_q192_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
            update db_sysarqcamp set codsequencia = 1001146 where codarq = 1011120 and codcam = 1015265;

            insert into db_sysarquivo values (1011121, 'processed_establishment_datas', 'Tabela que salva os dados do estabelecimento individualmente', 'q193', '2023-07-15', 'Dados do estabelecimento', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (3,1011121);
            insert into db_syscampo values(1015270,'q193_sequencial','int8','Sequencial da tabela processed_establishment_datas','0', 'Sequencial',11,'f','f','f',1,'text','Sequencial');
            insert into db_syscampo values(1015271,'q193_data','text','Dados do estabelecimento','', 'Dados do estabelecimento',5000,'f','f','f',0,'text','Dados do estabelecimento');
            insert into db_syscampo values(1015272,'q193_processed_establishment','int8','ID do estabelecimento','0', 'ID do estabelecimento',11,'f','f','f',1,'text','ID do estabelecimento');
            insert into db_syscampo values(1015273,'q193_created_at','text','Data do cadastro','', 'Data do cadastro',20,'f','f','f',0,'text','Data do cadastro');
            insert into db_syscampo values(1015274,'q193_updated_at','text','Data de atualização','', 'Data de atualização',20,'f','t','f',0,'text','Data de atualização');
            delete from db_sysarqcamp where codarq = 1011121;
            insert into db_sysarqcamp values(1011121,1015270,1,0);
            insert into db_sysarqcamp values(1011121,1015271,2,0);
            insert into db_sysarqcamp values(1011121,1015272,3,0);
            insert into db_sysarqcamp values(1011121,1015273,4,0);
            insert into db_sysarqcamp values(1011121,1015274,5,0);
            delete from db_sysprikey where codarq = 1011121;
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011121,1015270,1,1015270);
            insert into db_syssequencia values(1001147, 'processed_establishment_datas_q193_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
            update db_sysarqcamp set codsequencia = 1001147 where codarq = 1011121 and codcam = 1015270;

            insert into db_sysarquivo values (1011122, 'automatic_establishment_process', 'Tabela que salva os estabelecimentos que podem ser processados de forma automática', 'q194', '2023-07-15', 'Processamento Automatico de Estabelecimentos', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (3,1011122);
            insert into db_syscampo values(1015275,'q194_sequencial','int8','Sequencial da tabela automatic_establishment_process','0', 'Sequencial',11,'f','f','f',1,'text','Sequencial');
            insert into db_syscampo values(1015276,'q194_processed_establishment','int8','ID do estabelecimento','0', 'ID do estabelecimento',11,'f','f','f',1,'text','ID do estabelecimento');
            insert into db_syscampo values(1015277,'q194_processed','text','Seta se já foi processado','', 'Processado',11,'f','t','f',0,'text','Processado');
            insert into db_syscampo values(1015278,'q194_error','text','Seta se deu algum erro na hora de processar','', 'Erro',11,'f','f','f',0,'text','Erro');
            insert into db_syscampo values(1015279,'q194_created_at','text','Data do cadastro','', 'Data do cadastro',20,'f','f','f',0,'text','Data do cadastro');
            insert into db_syscampo values(1015280,'q194_updated_at','text','Data de atualização','', 'Data de atualização',20,'f','f','f',0,'text','Data de atualização');
            delete from db_sysarqcamp where codarq = 1011122;
            insert into db_sysarqcamp values(1011122,1015275,1,0);
            insert into db_sysarqcamp values(1011122,1015276,2,0);
            insert into db_sysarqcamp values(1011122,1015277,3,0);
            insert into db_sysarqcamp values(1011122,1015278,4,0);
            insert into db_sysarqcamp values(1011122,1015279,5,0);
            insert into db_sysarqcamp values(1011122,1015280,6,0);
            delete from db_sysprikey where codarq = 1011122;
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011122,1015275,1,1015275);
            insert into db_syssequencia values(1001148, 'automatic_establishment_process_q194_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
            update db_sysarqcamp set codsequencia = 1001148 where codarq = 1011122 and codcam = 1015275;

            insert into db_sysarquivo values (1011123, 'establishment_unprocessed_events', 'Tabela que salva os eventos que não foram processados por não terem sido implementados', 'q195', '2023-07-15', 'Eventos não processados', 0, 'f', 'f', 'f', 'f' );
            insert into db_sysarqmod values (3,1011123);
            insert into db_syscampo values(1015281,'q195_sequencial','int8','Sequencial da tabela establishment_unprocessed_events','0', 'Sequencial',11,'f','f','f',1,'text','Sequencial');
            insert into db_syscampo values(1015282,'q195_processed_establishment','int8','ID do estabelecimento','0', 'ID do estabelecimento',11,'f','f','f',1,'text','ID do estabelecimento');
            insert into db_syscampo values(1015283,'q195_redesim_event','int8','ID do Evento','0', 'ID do Evento',11,'f','f','f',1,'text','ID do Evento');
            insert into db_syscampo values(1015284,'q195_created_at','text','Data do cadastro','', 'Data do cadastro',20,'f','f','f',0,'text','Data do cadastro');
            insert into db_syscampo values(1015285,'q195_updated_at','text','Data de atualização','', 'Data de atualização',20,'f','f','f',0,'text','Data de atualização');
            delete from db_sysarqcamp where codarq = 1011123;
            insert into db_sysarqcamp values(1011123,1015281,1,0);
            insert into db_sysarqcamp values(1011123,1015282,2,0);
            insert into db_sysarqcamp values(1011123,1015283,3,0);
            insert into db_sysarqcamp values(1011123,1015284,4,0);
            insert into db_sysarqcamp values(1011123,1015285,5,0);
            delete from db_sysprikey where codarq = 1011123;
            insert into db_sysprikey (codarq,codcam,sequen,camiden) values(1011123,1015281,1,1015281);
            insert into db_syssequencia values(1001149, 'establishment_unprocessed_events_q195_sequencial_seq', 1, 1, 9223372036854775807, 1, 1);
            update db_sysarqcamp set codsequencia = 1001149 where codarq = 1011123 and codcam = 1015281;
SQL
        );
    }

    private function downDictionary()
    {
        DB::connection()->getPdo()->exec(<<<SQL
            delete from db_sysforkey where codarq in (
                /* establishment_data_batches */
                1011117,
                /* processed_establishments */
                1011118,
                /* redesim_events */
                1011119,
                /* processed_establishment_events */
                1011120,
                /* processed_establishment_datas */
                1011121,
                /* automatic_establishment_process */
                1011122,
                /* establishment_unprocessed_events */
                1011123
            );

            delete from db_sysprikey where codarq in (
                /* establishment_data_batches */
                1011117,
                /* processed_establishments */
                1011118,
                /* redesim_events */
                1011119,
                /* processed_establishment_events */
                1011120,
                /* processed_establishment_datas */
                1011121,
                /* automatic_establishment_process */
                1011122,
                /* establishment_unprocessed_events */
                1011123
            );

            delete from db_sysarqcamp where codarq in (
                /* establishment_data_batches */
                1011117,
                /* processed_establishments */
                1011118,
                /* redesim_events */
                1011119,
                /* processed_establishment_events */
                1011120,
                /* processed_establishment_datas */
                1011121,
                /* automatic_establishment_process */
                1011122,
                /* establishment_unprocessed_events */
                1011123
            );

            delete from db_syssequencia where codsequencia in (
                /* establishment_data_batches */
                1001143,
                /* processed_establishments */
                1001144,
                /* redesim_events */
                1001145,
                /* processed_establishment_events */
                1001146,
                /* processed_establishment_datas */
                1001147,
                /* automatic_establishment_process */
                1001148,
                /* establishment_unprocessed_events */
                1001149
            );

            delete from db_syscampo where codcam in (
                /* establishment_data_batches */
                1015246,
                1015249,
                1015250,
                1015247,
                1015248,
                /* processed_establishments */
                1015251,
                1015252,
                1015253,
                1015254,
                1015255,
                1015256,
                1015257,
                1015258,
                /* redesim_events */
                1015259,
                1015260,
                1015261,
                1015262,
                1015263,
                1015264,
                /* processed_establishment_events */
                1015265,
                1015266,
                1015267,
                1015268,
                1015269,
                /* processed_establishment_datas */
                1015270,
                1015271,
                1015272,
                1015273,
                1015274,
                /* automatic_establishment_process */
                1015275,
                1015276,
                1015277,
                1015278,
                1015279,
                1015280,
                /* establishment_unprocessed_events */
                1015281,
                1015282,
                1015283,
                1015284,
                1015285
            );

            delete from db_sysarqmod where codarq in (
                /* establishment_data_batches */
                1011117,
                /* processed_establishments */
                1011118,
                /* redesim_events */
                1011119,
                /* processed_establishment_events */
                1011120,
                /* processed_establishment_datas */
                1011121,
                /* automatic_establishment_process */
                1011122,
                /* establishment_unprocessed_events */
                1011123
            );

            delete from db_sysarquivo where codarq in (
                /* establishment_data_batches */
                1011117,
                /* processed_establishments */
                1011118,
                /* redesim_events */
                1011119,
                /* processed_establishment_events */
                1011120,
                /* processed_establishment_datas */
                1011121,
                /* automatic_establishment_process */
                1011122,
                /* establishment_unprocessed_events */
                1011123
            );
SQL
        );
    }
}
