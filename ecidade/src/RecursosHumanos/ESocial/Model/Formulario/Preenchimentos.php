<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */
namespace ECidade\RecursosHumanos\ESocial\Model\Formulario;

use BusinessException;
use cl_avaliacaogruporesposta;
use cl_avaliacaogruporespostaafastamentoesocial;
use cl_avaliacaogruporespostarhpessoal;
use db_utils;
use DBException;
use ECidade\RecursosHumanos\ESocial\Configuracao\S2230;
use Exception;
use Instituicao;
use Servidor;
use stdClass;

/**
 * Classe responsável por buscar os dados de preenchimento dos formulários
 * @package ECidade\RecursosHumanos\ESocial\Model\Formulario
 */
class Preenchimentos
{
    /**
     * Responsável pelo preenchimento do formulário
     *
     * @var mixed
     */
    private $responsavelPreenchimento;
    private $codigoFormulario;

    /**
     * @var Instituicao
     */
    private $instituicao;

    /**
     * @var Servidor[]
     */
    private $servidores = array();

    /**
     * @var Date
     */
    private $dataInicio;

    /**
     * @var Date
     */
    private $dataFim;

    /**
     * Informa o responsável pelo preenchimento. Se não indormado, busca de todos
     *
     * @param mixed $responsavel
     */
    public function setReponsavelPeloPreenchimento($responsavel)
    {
        $this->responsavelPreenchimento = $responsavel;
    }

    public function buscarUltimoPreenchimentoAdmissaoPreliminar($codigoFormulario)
    {
        $where = array(
            " db101_sequencial = {$codigoFormulario} "
        );

        if (!empty($this->responsavelPreenchimento)) {
            $where[] = "eso18_cgm = {$this->responsavelPreenchimento}";
        }

        // Substitui obrigatoridade da matricula, pela data do preenchimento.
        if (!empty($this->dataInicio) && !empty($this->dataFim)) {
            $where[] = " db107_datalancamento BETWEEN '{$this->dataInicio}' AND '{$this->dataFim}' ";
            $where = implode(' and ', $where);
        } else {
            $where[] = $this->montarWhereServidores('eso18_regist');
            $where = implode(' and ', $where);
        }

        $campos = 'distinct on (eso18_regist) ';
        $campos .= 'eso18_cpf as identificador, eso18_cgm as cgm, db107_sequencial as preenchimento, ';
        $campos .= '(select z01_cgccpf from cgm where z01_numcgm = eso18_cgm) as inscricao_empregador, ';
        $campos .= 'eso18_regist as matricula ';
        $dao = new \cl_avaliacaogruporespostaadmissaopreliminar();
        $sql = $dao->sql_avaliacao_preenchida(null, $campos, null, $where);
        $rs = \db_query($sql);

        if (!$rs) {
            $mensagem = "Ocorreu um erro ao tentar buscar os preenchimentos do formulário de admissão preliminar";
            $mensagem .= "\nContate o suporte.";
            throw new Exception($mensagem);
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Nenhum preenchimento do formulário de admissão preliminar foi encontrado.");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * Busca os preenchimentos dos empregadores
     *
     * @param integer $codigoFormulario
     * @return stdClass[]
     */
    public function buscarUltimoPreenchimentoEmpregador($codigoFormulario)
    {
        $where = array(" db101_sequencial = {$codigoFormulario} ");
        if (!empty($this->responsavelPreenchimento)) {
            $where[] = "eso03_cgm = {$this->responsavelPreenchimento}";
        }

        $where = implode(' and ', $where);

        $group = " group by eso03_cgm";
        $campos = 'eso03_cgm as cgm, max(db107_sequencial) as preenchimento, ';
        $campos .= '(select z01_cgccpf from cgm where z01_numcgm = eso03_cgm) as inscricao_empregador ';
        $dao = new \cl_avaliacaogruporespostacgm;
        $sql = $dao->sql_avaliacao_preenchida(null, $campos, null, $where . $group);
        $rs = \db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar os preenchimentos dos formulários dos empregadores.");
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Nenhum preenchimento foi encontrado para o empregador selecionado.");
        }
        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * Busca os preenchimentos de estabelecimentos e obras
     *
     * @param integer $codigoFormulario
     * @return stdClass[]
     */
    public function buscarUltimoPreenchimentoObras($codigoFormulario)
    {
        $where = array(" db101_sequencial = {$codigoFormulario} ");
        if (!empty($this->responsavelPreenchimento)) {
            $where[] = "eso35_empregador = {$this->responsavelPreenchimento}";
        }

        $group = " group by eso35_empregador, eso35_cnpj";
        $campos = ['eso35_empregador as cgm', 'max(db107_sequencial) as preenchimento', 'eso35_cnpj as cnpj_obras'];
        $campos[] = '(select z01_cgccpf from cgm where z01_numcgm = eso35_empregador) as inscricao_empregador ';
        $dao = new \cl_avaliacaogruporespostaobras();
        $sql = $dao->avaliacaoPreenchida($campos, $where, $group);

        $rs = \db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar os preenchimentos dos formulários de estabelecimentos e obras.");
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Nenhum preenchimento foi encontrado para o empregador selecionado.");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * Busca os preenchimentos dos servidores
     * @param $codigoFormulario
     * @param $cgmEmpregador
     * @return stdClass[]
     * @throws Exception
     */
    public function buscarUltimoPreenchimentoServidor($codigoFormulario, $cgmEmpregador)
    {
        $campos = array(
            'eso02_rhpessoal AS matricula',
            'max(eso02_avaliacaogruporesposta) AS preenchimento',
            "(SELECT z01_cgccpf FROM cgm WHERE z01_numcgm = {$cgmEmpregador}) AS inscricao_empregador"
        );

        $where = array(
            "eso02_avaliacao = {$codigoFormulario}",
            "eso02_empregador = {$cgmEmpregador}"
        );

        if ($this->filtraServidores()) {
            $where[] = $this->montarWhereServidores('eso02_rhpessoal');
        }

        $dao = new cl_avaliacaogruporespostarhpessoal();
        $sql = $dao->sql_avaliacao_preenchida($campos, $where, array('eso02_rhpessoal'));
        $rs = db_query($sql);

        $titulo = Tipo::getTitulos(Tipo::SERVIDOR);

        if (!$rs) {
            throw new Exception("Não foi possível buscar o último preenchimento do formulário {$titulo}.");
        }

        if (pg_num_rows($rs) === 0) {
            throw new Exception("Não há nenhum preenchimento do formulário {$titulo}.");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * Busca o preenchimento dos formulários genéricos.
     * Aqueles que possuem uma carga de dados e um campo pk (Uma chave única )
     *
     * @param integer $codigoFormulario
     * @return stdClass[]
     * @throws Exception
     */
    public function buscarUltimoPreenchimento($codigoFormulario)
    {
        $where = " db101_sequencial = {$codigoFormulario} ";
        $campos = 'distinct db107_sequencial as preenchimento, ';
        $campos .= '(select db106_resposta';
        $campos .= '   from avaliacaoresposta as ar ';
        $campos .= '   join avaliacaogrupoperguntaresposta as preenchimento';
        $campos .= '       on preenchimento.db108_avaliacaoresposta = ar.db106_sequencial ';
        $campos .= '   join avaliacaoperguntaopcao as apo on apo.db104_sequencial = ar.db106_avaliacaoperguntaopcao ';
        $campos .= '   join avaliacaopergunta as ap on ap.db103_sequencial = apo.db104_avaliacaopergunta ';
        $campos .= '  where ap.db103_perguntaidentificadora is true ';
        $campos .= '    and preenchimento.db108_avaliacaogruporesposta = db107_sequencial ';
        $campos .= ') as pk ';
        $dao = new cl_avaliacaogruporesposta;
        $sql = $dao->sql_avaliacao_preenchida(null, $campos, null, $where);

        $rs = \db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar os preenchimentos dos formulários das rubricas.");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * Busca o preenchimento dos formulários genéricos.
     * Aqueles que possuem uma carga de dados e um campo pk (Uma chave única )
     *
     * @param integer $codigoFormulario
     * @param bool $instituicao
     * @param $cgmEmpregador
     * @return stdClass[]
     * @throws Exception
     */
    public function buscarUltimoPreenchimentoPorInstituicao(
        $codigoFormulario,
        $instituicao = false,
        $cgmEmpregador = null,
        $identificadores = null
    ) {
        $dao = new cl_avaliacaogruporesposta;
        $sql = $dao->sql_avaliacao_preenchida_por_instituicao(
            $codigoFormulario,
            $instituicao,
            $this->responsavelPreenchimento,
            $identificadores
        );

        $rs = \db_query($sql);

        if (empty($cgmEmpregador)) {
            throw new Exception("Empregador não informado.");
        }
        if (!$rs) {
            throw new Exception("Erro ao buscar os preenchimentos do formulário.");
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Nenhum item cadastrado!");
        }

        $dadosDoPreenchimento = db_utils::getCollectionByRecord($rs);

        $campos = ' distinct z01_numcgm as cgm, z01_cgccpf as documento, z01_nome as nome, r70_instit as instituicao';
        $dao = new \cl_rhlota();

        $where = "r70_instit = {$instituicao} ";
        $where .= "and z01_numcgm = {$cgmEmpregador}";

        $sql = $dao->sql_query_lota_cgm(null, $campos, 'z01_numcgm', $where);
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar os empregadores.");
        }
        $empregadores = db_utils::getCollectionByRecord($rs);

        $dadosDoPreenchimentoPorEmpregador = array();

        foreach ($empregadores as $empregador) {
            foreach ($dadosDoPreenchimento as $item) {
                $aux = clone $item;
                $aux->inscricao_empregador = $empregador->documento;
                $dadosDoPreenchimentoPorEmpregador[] = $aux;
            }
        }
        return $dadosDoPreenchimentoPorEmpregador;
    }

    /**
     * Busca os preenchimentos dos empregadores
     *
     * @param integer $codigoFormulario
     * @return stdClass[]
     * @throws DBException
     */
    public function buscarUltimoPreenchimentoLotacao($codigoFormulario)
    {
        $where = [" db101_sequencial = {$codigoFormulario} ","db104_sequencial = 3003555"];
        if (!empty($this->responsavelPreenchimento)) {
            $where[] = "eso04_cgm = {$this->responsavelPreenchimento}";
        }

        $group = " ";
        $campos = [
            'distinct  on (db106_resposta) eso04_cgm as cgm',
            'max(db107_sequencial) over (partition by eso04_cgm, db106_resposta) as preenchimento',
            '(select z01_cgccpf from cgm where z01_numcgm = eso04_cgm) as inscricao_empregador'
        ];

        $dao = new \cl_avaliacaogruporespostalotacao;
        $sql = $dao->avaliacaoPreenchida($campos, $where, $group);
        $rs = \db_query($sql);


        if (!$rs) {
            throw new DBException("Erro ao buscar o preenchimento da Lotação Tributária.");
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Lotação Tributária não cadastrada!");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    public function buscarUltimoPreenchimentoProcesso($codigoFormulario)
    {
        $where = array(" db101_sequencial = {$codigoFormulario} ");
        if (!empty($this->responsavelPreenchimento)) {
            $where[] = "eso05_avaliacaogruporesposta = {$this->responsavelPreenchimento}";
        }

        $group = " group by eso05_cgm";
        $campos = array(
            'eso05_cgm as cgm',
            'max(db107_sequencial) as preenchimento',
            '(select z01_cgccpf from cgm where z01_numcgm = eso05_cgm) as inscricao_empregador'
        );

        $dao = new \cl_avaliacaogruporespostaprocesso;
        $sql = $dao->avaliacaoPreenchida($campos, $where, $group);
        $rs = \db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar os preenchimentos dos formulários dos empregadores.");
        }
        return db_utils::getCollectionByRecord($rs);
    }

    public function buscarUltimoPreenchimentoCargo($codigoFormulario, $instituicao = false, $cgmEmpregador = null)
    {
        $dao = new \cl_cargo();
        $sql = $dao->avaliacaoPreenchida($codigoFormulario, $instituicao, $this->responsavelPreenchimento);
        $rs = \db_query($sql);

        if (empty($cgmEmpregador)) {
            throw new Exception("Empregador não informado.");
        }

        if (!$rs) {
            throw new Exception("Erro ao buscar os preenchimentos dos formulários dos empregadores.");
        }

        $dadosDoPreenchimento = db_utils::getCollectionByRecord($rs);

        $campos = ' distinct z01_numcgm as cgm, z01_cgccpf as documento, z01_nome as nome, r70_instit as instituicao';
        $dao = new \cl_rhlota();

        $where = "r70_instit = {$instituicao} ";
        $where .= "and z01_numcgm = {$cgmEmpregador}";

        $sql = $dao->sql_query_lota_cgm(null, $campos, 'z01_numcgm', $where);
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar os empregadores.");
        }
        $empregadores = db_utils::getCollectionByRecord($rs);

        $dadosDoPreenchimentoPorEmpregador = array();

        foreach ($empregadores as $empregador) {
            foreach ($dadosDoPreenchimento as $item) {
                $aux = clone $item;
                $aux->inscricao_empregador = $empregador->documento;
                $dadosDoPreenchimentoPorEmpregador[] = $aux;
            }
        }

        return $dadosDoPreenchimentoPorEmpregador;
    }

    /**
     * Buscas as respostas de um preenchimento
     *
     * @param integer $preenchimentoId
     * @return DadosResposta[]
     */
    public static function buscaRespostas($preenchimentoId)
    {
        $dao = new cl_avaliacaogruporesposta;
        $campos = array(
            "db102_identificadorcampo as grupo",
            "db103_identificadorcampo as pergunta",
            "db103_sequencial as idpergunta",
            "db104_valorresposta as valorresposta",
            "db106_resposta as resposta",
            "db103_avaliacaotiporesposta as tipopergunta",
            "db103_obrigatoria as obrigatoria"
        );

        $campos = implode(', ', $campos);
        $sql = $dao->busca_resposta_preenchimento($preenchimentoId, $campos);
        $rs = \db_query($sql);

        return db_utils::makeCollectionFromRecord($rs, function ($dado) {

            $dadoResposta = new DadosResposta();
            $dadoResposta->grupo = $dado->grupo;
            $dadoResposta->pergunta = $dado->pergunta;
            $dadoResposta->idPergunta = $dado->idpergunta;
            $dadoResposta->valorResposta = $dado->valorresposta;
            $dadoResposta->resposta = $dado->resposta;
            $dadoResposta->tipoPergunta = $dado->tipopergunta;
            $dadoResposta->obrigatoria = $dado->obrigatoria == 't';

            return $dadoResposta;
        });
    }

    public function setCodigoFormulario($codigoFormulario)
    {
        $this->codigoFormulario = $codigoFormulario;
    }

    /**
     * @return mixed
     */
    public function getCodigoFormulario()
    {
        return $this->codigoFormulario;
    }

    /**
     * @return mixed
     */
    public function getResponsavelPreenchimento()
    {
        return $this->responsavelPreenchimento;
    }

    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;
    }

    /**
     * @return Instituicao
     */
    public function getInstituicao()
    {
        return $this->instituicao;
    }


    public function buscarUltimoAvisoPrevio($cgm)
    {
        $daoAvaliacaoAvisoPrevio = new \cl_avaliacaogruporespostaavisoprevio();

        $campos = array(
            'distinct db107_sequencial as preenchimento',
            'z01_cgccpf as inscricao_empregador',
            'eso07_regist as matricula'
        );

        $where = array(
            "db101_sequencial = $this->codigoFormulario",
            "eso07_empregador = $cgm"
        );

        $sql = $daoAvaliacaoAvisoPrevio->avaliacaoPreenchida($campos, $where);

        $recordSet = \db_query($sql);

        if (empty($recordSet)) {
            throw new DBException("Não foi possível buscar os preenchimentos deste formulário. Contate o suporte.");
        }

        if (pg_num_rows($recordSet) == 0) {
            throw new BusinessException("Não há preenchimentos para este formulário.");
        }

        return db_utils::getCollectionByRecord($recordSet);
    }

    /**
     * @param $codigoFormulario
     * @param $instituicao
     * @param $cgmEmpregador
     * @return stdClass[]
     * @throws DBException
     */
    public function buscarUltimoPreenchimentoAfastamentoTemporario(
        $codigoFormulario,
        $instituicao,
        $ano,
        $mes,
        $cgmEmpregador
    ) {
        $configuracaoAfastamento = new S2230();
        $dataConfiguracao = $configuracaoAfastamento->getPropriedade('data_envio');

        $where = array(
            "eso13_avaliacao = {$codigoFormulario}",
            "(h16_dtterm >= '{$dataConfiguracao}' or h16_dtterm is null) ",
            "rh01_instit = {$instituicao}",
            "avaliacaopergunta.db103_identificadorcampo = 'codMotAfast'"
        );

        if ($this->filtraServidores()) {
            $where[] = $this->montarWhereServidores('eso12_rhpessoal');
        }

        $dao = new cl_avaliacaogruporespostaafastamentoesocial();
        $sql = $dao->sql_avaliacao_afastamentos($instituicao, $where, $ano, $mes, $cgmEmpregador);
        $rs = db_query($sql);
        if (!$rs) {
            $mensagem = 'Não foi possível buscar o último preenchimento do formulario S-2230 - Afastamento Temporário.';
            throw new Exception($mensagem);
        }

        if (pg_num_rows($rs) === 0) {
            throw new DBException('Não há nenhum preenchimento do formulario S-2230 - Afastamento Temporário.');
        }

        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * @param integer $cgmEmpregador
     * @return stdClass[]
     * @throws Exception
     */
    public function buscarUltimoPreenchimentoDesligamentoServidor($cgmEmpregador)
    {
        $where = array(
            "eso15_avaliacao = {$this->getCodigoFormulario()}",
            "eso15_cgmempregador = {$cgmEmpregador}"
        );

        if ($this->filtraServidores()) {
            $where[] = $this->montarWhereServidores('eso15_regist');
        }

        $group = array("eso15_regist", "z01_cgccpf", "eso15_codigorescisao");

        $campos = array(
            "max(eso15_avaliacaogruporesposta) as preenchimento",
            "eso15_regist as matricula",
            "z01_cgccpf as inscricao_empregador",
            'eso15_codigorescisao as referencia',
        );

        $dao = new \cl_avaliacaogruporespostarhpesrescisao();
        $consultaPreenchimento = $dao->sql_query_formulario($campos, $where, $group);
        $consultaPreenchimento = db_query($consultaPreenchimento);

        if (!$consultaPreenchimento) {
            throw new DBException("Ocorreu um erro ao consultar os dados de preenchimento do formulário S-2299.");
        }

        if (pg_num_rows($consultaPreenchimento) === 0) {
            throw new BusinessException("Não foram encontrados registros de desligamento.");
        }

        $retornoDesligamentos = db_utils::getCollectionByRecord($consultaPreenchimento);
        return $retornoDesligamentos;
    }

    /**
     * Buscas as respostas do preenchimento de exclusão de eventos
     * @return stdClass[]
     * @throws Exception
     */
    public function buscarUltimoPreenchimentoExclusaoEventos(
        $cgmEmpregador,
        $numeroRecibo = null,
        $dataInicio = null,
        $dataFim = null
    ) {
        $daoExclusaoEventos = new \cl_avaliacaogruporespostaexclusaoeventos();

        $campos = array(
            "db107_sequencial AS preenchimento",
            "eso14_protocolo AS identificador",
            "z01_cgccpf AS inscricao_empregador",
            "db107_datalancamento as data_preenchimento",
        );

        $where = array(
            "db101_sequencial = {$this->codigoFormulario}",
            "eso14_cgm = '{$cgmEmpregador}'"
        );

        if (!empty($numeroRecibo)) {
            $where[] = "eso14_protocolo = '{$numeroRecibo}'";
        }

        if (!empty($dataInicio) && !empty($dataFim)) {
            $where[] = "db107_datalancamento between '{$dataInicio}' and '{$dataFim}'";
        }

        $sqlExclusaoEventos = $daoExclusaoEventos->buscarRespostasPreenchimento($campos, $where);
        $rsExclusaoEventos = \db_query($sqlExclusaoEventos);

        if (!$rsExclusaoEventos) {
            throw new DBException("Não foi possível buscar o preenchimento do formulário de exclusão de eventos.");
        }

        if (pg_num_rows($rsExclusaoEventos) == 0) {
            throw new DBException("Nenhum preenchimento de exclusão de eventos encontrado.");
        }
        return db_utils::getCollectionByRecord($rsExclusaoEventos);
    }

    /**
     * Buscamos o último preenchimento do TSVE Inicial
     * @param $codigoFormulario
     * @param $cgmEmpregador
     * @return stdClass[]
     * @throws DBException
     */
    public function buscarUltimoPreenchimentoTSVEInicial($codigoFormulario, $cgmEmpregador)
    {
        $where = array(
            "eso16_avaliacao = {$codigoFormulario}",
            "eso16_empregador = {$cgmEmpregador}"
        );

        if ($this->filtraServidores()) {
            $where[] = $this->montarWhereServidores('eso16_rhpessoal');
        }

        $group = array("eso16_rhpessoal, z01_cgccpf");

        $campos = array(
            "eso16_rhpessoal as matricula",
            "max(eso16_avaliacaogruporesposta) as preenchimento",
            "z01_cgccpf as inscricao_empregador"
        );

        $dao = new \cl_avaliacaogruporespostatsveinicial;
        $sql = $dao->sql_avaliacao_preenchida($campos, array(), $where, $group);
        $rs = \db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar os preenchimentos dos formulários dos trabalhadores sem vínculo.");
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Não há nenhum trabalhador sem vínculo cadastrado!");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * Buscamos o último preenchimento do TSVE Inicial
     * @param $codigoFormulario
     * @param $cgmEmpregador
     * @return stdClass[]
     * @throws DBException
     */
    public function buscarUltimoPreenchimentoTSVETermino($codigoFormulario, $cgmEmpregador)
    {
        $where = array(
            "eso24_avaliacao = {$codigoFormulario}",
            "eso24_cgmempregador = {$cgmEmpregador} "
        );

        if ($this->filtraServidores()) {
            $where[] = $this->montarWhereServidores('eso24_rhpessoal');
        }

        $campos = array(
            "max(eso24_avaliacaogruporesposta) as preenchimento",
            "eso24_rhpessoal as matricula ",
            "eso24_codigorescisao as referencia ",
            "z01_cgccpf as inscricao_empregador "
        );

        $group = " group by eso24_rhpessoal, z01_cgccpf, eso24_codigorescisao";
        $dao = new \cl_avaliacaogruporespostatertrabasemvinc();
        $sql = $dao->sql_query_avaliacao_servidor_sem_vinculo($campos, $where, $group);
        $rs = \db_query($sql);

        if (!$rs) {
            $mensagem = "Erro ao buscar os preenchimentos dos formulários dos trabalhadores sem vínculo termino.";
            throw new Exception($mensagem);
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Não há nenhum trabalhador sem vínculo cadastrado!");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * Buscas as respostas do preenchimento de trabalho intermitente
     * @return stdClass[]
     * @throws Exception
     */
    public function buscarUltimoPreenchimentoTrabalhoIntermitente($cgmEmpregador)
    {
        $daoTrabalhoIntermitente = new \cl_avaliacaogruporespostatrabintermitente();

        $campos = array(
            "db107_sequencial AS preenchimento",
            "eso19_codigoconvocacao AS identificador",
            "z01_cgccpf AS inscricao_empregador",
        );

        $where = array(
            "db101_sequencial = {$this->codigoFormulario}",
            "eso19_cgm = '{$cgmEmpregador}'"
        );

        $sqlTrabalhoIntermitente = $daoTrabalhoIntermitente->buscarRespostasPreenchimento($campos, $where);
        $rsTrabalhoIntermitente = \db_query($sqlTrabalhoIntermitente);

        if (!$rsTrabalhoIntermitente) {
            throw new DBException("Não foi possível buscar o preenchimento do formulário de trabalho intermitente.");
        }

        if (pg_num_rows($rsTrabalhoIntermitente) == 0) {
            throw new DBException("Nenhum preenchimento de trabalho intermitente encontrado.");
        }

        return db_utils::getCollectionByRecord($rsTrabalhoIntermitente);
    }

    /**
     * @param $codigoInstituicao
     * @param $codigoCgmEmpregador
     * @return stdClass[]
     * @throws Exception
     */
    public function buscarUltimoPreenchimentoAlteracaoDadosServidor($codigoInstituicao, $codigoCgmEmpregador)
    {

        $where = array(
            "avaliacao.db101_sequencial = {$this->getCodigoFormulario()}",
            "rhlota.r70_numcgm = {$codigoCgmEmpregador}",
            "rhlota.r70_instit = {$codigoInstituicao}",
        );

        if ($this->filtraServidores()) {
            $where[] = $this->montarWhereServidores('eso17_rhpessoal');
        }

        $campos = array(
            'eso17_rhpessoal as matricula',
            'max(db107_sequencial) as preenchimento',
            "(select z01_cgccpf from cgm where z01_numcgm = {$codigoCgmEmpregador}) as inscricao_empregador "
        );

        $dao = new \cl_avaliacaogruporespostarhpessoalalteracao();
        $sSql = " group by eso17_rhpessoal";

        $buscaPreenchimento = $dao->sql_query_avaliacao_alteracao_servidor($campos, $where, $sSql);

        $buscaPreenchimento = db_query($buscaPreenchimento);
        if (!$buscaPreenchimento) {
            throw new DBException("Ocorreu um erro ao consultar os dados do preenchimento para o arquivo S-2205.");
        }

        if (pg_num_rows($buscaPreenchimento) == 0) {
            throw new BusinessException("Não foi encontrado nenhum preenchimento para o arquivo S-2205.");
        }

        return db_utils::getCollectionByRecord($buscaPreenchimento);
    }

    /**
     * Buscas as respostas do preenchimento de reintegracao
     * @return stdClass[]
     * @throws Exception
     */
    public function buscarUltimoPreenchimentoReintegracao($cgmEmpregador)
    {
        $daoReintegracao = new \cl_avaliacaogruporespostareintegracao();

        $campos = array(
            "db107_sequencial AS preenchimento",
            "eso21_matricula AS identificador",
            "z01_cgccpf AS inscricao_empregador",
        );

        $where = array(
            "db101_sequencial = {$this->codigoFormulario}",
            "eso21_cgm = '{$cgmEmpregador}'"
        );

        $sqlReintegracao = $daoReintegracao->buscarRespostasPreenchimento($campos, $where);
        $rsReintegracao = \db_query($sqlReintegracao);

        if (!$rsReintegracao) {
            throw new DBException("Não foi possível buscar o preenchimento do formulário de reintegração.");
        }

        if (pg_num_rows($rsReintegracao) == 0) {
            throw new DBException("Nenhum preenchimento de reintegração encontrado.");
        }

        return db_utils::getCollectionByRecord($rsReintegracao);
    }

    public function buscarUltimoPreenchimentoAlteracaoContratual($cgmEmpregador)
    {
        $daoAlteracaoContratual = new \cl_avaliacaogruporespostaaltercontratual();

        $campos = array(
            "db107_sequencial AS preenchimento",
            "eso20_rhpessoal AS identificador",
            "z01_cgccpf AS inscricao_empregador",
        );

        $where = array(
            "db101_sequencial = {$this->codigoFormulario}",
            "eso20_cgm = '{$cgmEmpregador}'"
        );

        if ($this->filtraServidores()) {
            $where[] = $this->montarWhereServidores('eso20_rhpessoal');
        }

        $sqlAlteracaoContratual = $daoAlteracaoContratual->buscarRespostasPreenchimento($campos, $where);

        $rsAlteracaoContratual = \db_query($sqlAlteracaoContratual);

        if (!$rsAlteracaoContratual) {
            throw new DBException("Não foi possível buscar o preenchimento do formulário de trabalho intermitente.");
        }

        if (pg_num_rows($rsAlteracaoContratual) == 0) {
            throw new DBException("Nenhum preenchimento de alteração cadastral encontrado.");
        }

        return db_utils::getCollectionByRecord($rsAlteracaoContratual);
    }

    /**
     * Buscamos o último preenchimento do TSVE Inicial
     * @param $codigoFormulario
     * @param $instituicao
     * @param $cgmEmpregador
     * @return stdClass[]
     * @throws DBException
     */
    public function buscarUltimoPreenchimentoTSVEAlteracao($codigoFormulario, $instituicao, $cgmEmpregador)
    {
        $where = " db101_sequencial = {$codigoFormulario}";
        $where .= " and rh02_anousu = fc_anofolha({$instituicao}) ";
        $where .= " and rh02_mesusu = fc_mesfolha({$instituicao}) ";
        $where .= " and rh02_instit = {$instituicao} ";
        $where .= " and r70_numcgm = {$cgmEmpregador} ";

        $group = " group by eso23_rhpessoal";
        $campos = " eso23_rhpessoal as matricula, max(db107_sequencial) as preenchimento, ";
        $campos .= " (select z01_cgccpf from cgm where z01_numcgm = {$cgmEmpregador}) as inscricao_empregador ";

        $dao = new \cl_avaliacaogruporespostatsvealteracao;
        $sql = $dao->sql_avaliacao_preenchida(null, $campos, null, $where . $group);
        $rs = \db_query($sql);

        if (!$rs) {
            throw new Exception("Erro ao buscar os preenchimentos dos formulários dos trabalhadores sem vínculo.");
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Não há nenhum trabalhador sem vínculo cadastrado!");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    public function buscarUltimoPreenchimentoContribuinte()
    {
        $daoAlteracaoCadastral = new \cl_avaliacaogruporespostacontribuinte();

        $campos = array(
            "db107_sequencial AS preenchimento",
            "eso27_cgm AS identificador",
            "z01_cgccpf AS inscricao_contribuinte",
        );

        $where = array(
            "db101_sequencial = {$this->codigoFormulario}",
            "eso27_cgm = '{$this->responsavelPreenchimento}'"
        );

        $sqlAlteracaoCadastral = $daoAlteracaoCadastral->buscarRespostasPreenchimento($campos, $where);
        $rsAlteracaoCadastral = \db_query($sqlAlteracaoCadastral);

        if (!$rsAlteracaoCadastral) {
            throw new DBException("Não foi possível buscar o preenchimento do formulário de trabalho intermitente.");
        }

        if (pg_num_rows($rsAlteracaoCadastral) == 0) {
            throw new DBException("Nenhum preenchimento de contribuinte encontrado.");
        }

        return db_utils::getCollectionByRecord($rsAlteracaoCadastral);
    }

    public function buscarUltimoPreenchimentoRemuneracaoRGPS($cgmEmpregador)
    {
        $daoRemuneracaoRGPS = new \cl_avaliacaogruporespostaremuneracaorgps();

        $campos = array(
            "eso28_avaliacaogruporesposta as identificador",
            "max(db107_sequencial) as preenchimento",
            "(select z01_cgccpf from cgm where z01_numcgm = {$cgmEmpregador}) as inscricao_empregador"
        );

        $agrupar = "eso28_avaliacaogruporesposta";
        $sqlRemuneracaoRGPS = $daoRemuneracaoRGPS->buscarRespostasPorPergunta(
            null,
            null,
            implode(', ', $campos),
            null,
            $agrupar
        );
        $rsRemuneracaoRGPS = db_query($sqlRemuneracaoRGPS);

        if (!$rsRemuneracaoRGPS) {
            throw new DBException('Não foi possível buscar o preenchimento do formulário de Remuneração RGPS.');
        }

        if (pg_num_rows($rsRemuneracaoRGPS) == 0) {
            throw new BusinessException('Nenhum preenchimento encontrado para o formulário de Remuneração RGPS.');
        }

        return db_utils::getCollectionByRecord($rsRemuneracaoRGPS);
    }

    public function buscarUltimoPreenchimentoEFDProcessos()
    {
        $where = array(
            "efd02_cgm = {$this->responsavelPreenchimento}",
            "efd02_avaliacao = {$this->codigoFormulario}"
        );

        $campos = array(
            'efd02_avaliacaogruporesposta as preenchimento',
            'efd02_processo AS identificador',
            'z01_cgccpf AS inscricao_contribuinte'
        );
        $dao = new \cl_avaliacaogruporespostaefdprocesso();
        $sql = $dao->sql_query(null, implode(', ', $campos), null, implode(' and ', $where));

        $rs = \db_query($sql);

        if (!$rs) {
            throw new DBException("Não foi possível buscar o preenchimento do formulário de processos.");
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Nenhum preenchimento de processo encontrado.");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    public function buscarUltimoPreenchimentoEFDServicosTomados($ano = null, $mes = null)
    {
        $daoRetServicosTomados = new \cl_avaliacaogruporespostaretservicostomados();

        $campos = array(
            "db107_sequencial AS preenchimento",
            "efd04_cgmcontribuinte AS identificador",
            "cgmcontribuinte.z01_cgccpf AS inscricao_contribuinte",
        );


        $where = array(
            "db101_sequencial = {$this->codigoFormulario}",
            "efd04_cgmprestador = '{$this->responsavelPreenchimento}'"
        );


        if (isset($ano)) {
            $where[] = "efd04_ano = {$ano}";
        }
        if (isset($mes)) {
            $where[] = "efd04_mes = {$mes}";
        }

        $sqlRetServicosTomados = $daoRetServicosTomados->buscarRespostasPreenchimento($campos, $where);
        $rsRetServicosTomados = \db_query($sqlRetServicosTomados);

        if (!$rsRetServicosTomados) {
            $mensagem = "Não foi possível buscar o preenchimento do formulário de retenção de serviços tomados.";
            throw new DBException($mensagem);
        }

        if (pg_num_rows($rsRetServicosTomados) == 0) {
            //throw new \DBException("Nenhum preenchimento de retenção de serviços tomados encontrado.");
            return array();
        }

        return db_utils::getCollectionByRecord($rsRetServicosTomados);
    }

    public function buscarUltimoPreenchimentoEFDExclusaoEventos()
    {
        $dao = new \cl_avaliacaogruporespostaexclusaoeventosefd();

        $campos = array(
            "db107_sequencial AS preenchimento",
            "eso29_protocolo AS identificador",
            "eso29_data as data",
            "z01_cgccpf AS inscricao_contribuinte",
        );

        $where = array(
            "db101_sequencial = {$this->codigoFormulario}",
            "eso29_cgm = '{$this->responsavelPreenchimento}'"
        );

        $sql = $dao->buscarRespostasPreenchimento($campos, $where);
        $rs = \db_query($sql);

        if (!$rs) {
            throw new DBException("Não foi possível buscar o preenchimento do formulário de trabalho intermitente.");
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Nenhum preenchimento de exclusão de eventos encontrado.");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    public function buscarUltimoPreenchimentoEFDServicosPrestados()
    {
        $where = array(
            "efd05_cgm = {$this->responsavelPreenchimento}",
            "efd05_avaliacao = {$this->codigoFormulario}"
        );

        $campos = array(
            'efd05_avaliacaogruporesposta as preenchimento',
            'efd05_competencia AS identificador',
            'z01_cgccpf AS inscricao_contribuinte'
        );
        $dao = new \cl_avaliacaogruporespostaefdr2020();
        $sql = $dao->sql_query(null, implode(', ', $campos), null, implode(' and ', $where));

        $rs = \db_query($sql);

        if (!$rs) {
            $mensagem = "Não foi possível buscar o preenchimento do formulário de Retenção Contribuição Previdenciária";
            $mensagem .= " - Serviços Prestados.";
            throw new DBException($mensagem);
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Nenhum preenchimento de Retenção Contribuição Previdenciária - Serviços Prestados.");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * @throws Exception
     */
    public function buscarUltimoPreenchimentoTotalizacaoPagamentosContingencia()
    {
        $dao = new \cl_avaliacaogruporespostatotalizacaopagamentocontingencia();
        $campos = array(
            "eso34_sequencial as identificador",
            "eso34_avaliacaogruporesposta as preenchimento",
            "z01_cgccpf AS inscricao_empregador",
            "eso34_indicativoapuracao as indicativo_apuracao",
            "eso34_periodo as periodo_apuracao"

        );
        $where = array(
            "eso34_empregador = $this->responsavelPreenchimento"
        );

        $ordem = array("eso34_indicativoapuracao", "eso34_periodo");
        $sql = $dao->preenchimentos($campos, $ordem, $where);
        $rs = db_query($sql);

        if (!$rs) {
            $mensagem = "Não foi possível buscar o preenchimento do formulário de Fechamento dos Eventos Periódicos";
            throw new DBException($mensagem);
        }

        if (pg_num_rows($rs) === 0) {
            throw new DBException("Nenhum preenchimento de Fechamento dos Eventos Periódicos");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * @throws Exception
     */
    public function buscarUltimoPreenchimentoFechamentoEventosPeriodicos($indicativoPeriodoApuracao, $ano, $mes = null)
    {
        $dao = new \cl_avaliacaogruporespostaesocials1299();
        $campos = array(
            "eso33_sequencial as identificador",
            "eso33_avaliacaogruporesposta as preenchimento",
            "z01_cgccpf AS inscricao_empregador",
            "eso33_indicativoapuracao as indicativo_apuracao",
            "eso33_periodo as periodo_apuracao"

        );
        $periodo = $ano;
        if ($indicativoPeriodoApuracao == 1) {
            $periodo = "{$ano}-" . str_pad($mes, 2, 0, STR_PAD_LEFT);
        }
        $where = array(
            "eso33_empregador = $this->responsavelPreenchimento",
            "eso33_avaliacao = $this->codigoFormulario",
            "eso33_periodo = '$periodo'"
        );

        $ordem = array("eso33_indicativoapuracao", "eso33_periodo");
        $sql = $dao->preenchimentos($campos, $ordem, $where);
        $rs = db_query($sql);

        if (!$rs) {
            $mensagem = "Não foi possível buscar o preenchimento do formulário de Fechamento dos Eventos Periódicos";
            throw new DBException($mensagem);
        }

        if (pg_num_rows($rs) === 0) {
            throw new DBException("Nenhum preenchimento de Fechamento dos Eventos Periódicos");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    public function buscarUltimoPreenchimentoEFDFechamentoPeriodicos($ano, $mes)
    {
        $where = [
            "eso32_cgmcontribuinte = {$this->responsavelPreenchimento}",
            "eso32_ano = {$ano}",
            "eso32_mes = {$mes}",
        ];

        $campos = array(
            "eso32_avaliacaogruporesposta as preenchimento",
            "concat(eso32_ano, '-', lpad(eso32_mes::text, 2, '0')) as identificador",
            "z01_cgccpf AS inscricao_contribuinte"
        );
        $dao = new \cl_avaliacaogruporespostafechamentoefd();
        $sql = $dao->sql_query(null, implode(', ', $campos), null, implode(' and ', $where));

        $rs = \db_query($sql);

        if (!$rs) {
            throw new DBException("Não foi possível buscar o preenchimento do formulário de Fechamento de Períodicos");
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Nenhum preenchimento de Fechamento de Períodicos.");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * @param Servidor[] $servidores
     */
    public function setServidores(array $servidores)
    {
        $this->servidores = $servidores;
    }

    /**
     * @return bool
     */
    public function filtraServidores()
    {
        return count($this->servidores) > 0;
    }

    /**
     * @param $coluna
     * @return string
     */
    private function montarWhereServidores($coluna)
    {
        $matriculas = implode(', ', array_map(function (Servidor $servidor) {
            return $servidor->getMatricula();
        }, $this->servidores));

        return "{$coluna} IN ({$matriculas})";
    }

    /**
     * Busca os preenchimentos de Comunicado de Acidente de Trabalho
     *
     * @param integer $codigoFormulario
     * @return stdClass[]
     */
    public function buscarUltimoPreenchimentoCat($codigoFormulario, $cgmEmpregador, $dataInicio, $dataFim)
    {
        $where = array(" db101_sequencial = {$codigoFormulario} ");
        if (!empty($cgmEmpregador)) {
            $where[] = "eso36_empregador = {$cgmEmpregador}";
        }
        if (!empty($dataInicio) && !empty($dataFim)) {
            $where[] = "eso36_data between '{$dataInicio}' and '{$dataFim}' ";
        }

        $group = " group by eso36_empregador, eso36_cpf, eso36_data, z01_cgccpf";
        $campos = [
            'eso36_empregador as cgm',
            "z01_cgccpf AS inscricao_empregador",
            'max(db107_sequencial) as preenchimento',
            'eso36_cpf as cpf_cat',
            'eso36_data as data_cat'
        ];
        $dao = new \cl_esoacidentetrabalho();
        $sql = $dao->avaliacaoPreenchida($campos, $where, $group);

        $rs = \db_query($sql);

        if (!$rs) {
            $msg = "Erro ao buscar os preenchimentos dos formulários de Comunicação de Acidente de Trabalho.";
            throw new Exception($msg);
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Nenhum preenchimento foi encontrado para o empregador selecionado.");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    /**
     * Busca os preenchimentos de Monitoramento da Saúde do Trabalhador
     *
     * @param integer $codigoFormulario
     * @param integer $cgmEmpregador
     * @param integer $cpfServidor
     * @return stdClass[]
     */
    public function buscarUltimoPreenchimentoMonitoriamentoSaude(
        $codigoFormulario,
        $cgmEmpregador,
        $dataInicio,
        $dataFim
    ) {
        $where = array(" db101_sequencial = {$codigoFormulario} ");
        if (!empty($dataInicio) && !empty($dataFim)) {
            $where[] = "eso37_dataatestado between '{$dataInicio}' and '{$dataFim}' ";
        }


        $group = " group by eso37_empregador, eso37_cpf, z01_cgccpf";
        $campos = [
            'eso37_empregador as cgm',
            "z01_cgccpf AS inscricao_empregador",
            'max(db107_sequencial) as preenchimento',
            'eso37_cpf as cpf'
        ];
        $dao = new \cl_avaliacaogruporespostamonitoramentosaude();
        $sql = $dao->avaliacaoPreenchida($campos, $where, $group);

        $rs = \db_query($sql);

        if (!$rs) {
            $msg = "Erro ao buscar os preenchimentos dos formulários de Monitoramento da Saúde do Trabalhador.";
            throw new Exception($msg);
        }

        if (pg_num_rows($rs) == 0) {
            throw new DBException("Nenhum preenchimento foi encontrado para o empregador selecionado.");
        }

        return db_utils::getCollectionByRecord($rs);
    }

    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    public function setDataInicio($dataInicio)
    {
        $this->dataInicio = $dataInicio;
    }

    public function getDataFim()
    {
        return $this->dataFim;
    }

    public function setDataFim($dataFim)
    {
        $this->dataFim = $dataFim;
    }
}
