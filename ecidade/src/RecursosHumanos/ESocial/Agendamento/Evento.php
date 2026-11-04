<?php

namespace ECidade\RecursosHumanos\ESocial\Agendamento;

use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;

class Evento
{

    /**
     * Código do Evento do eSocial
     *
     * @var integer
     */
    private $tipoEvento;

    /**
     * Código do empregador
     *
     * @var integer
     */
    private $empregador;

    /**
     * Código do responsavel pelo evento
     * @var mixed
     */
    private $responsavelPreenchimento;

    /**
     * Dados do Evento
     *
     * @var \stdClass
     */
    private $dado;

    /**
     * md5 do objeto salvo
     *
     * @var string
     */
    private $md5;

    /**
     * @var integer $iContador
     */
    public $iContador;

    /**
     * Undocumented function
     *
     * @param integer $tipoEvento
     * @param integer $empregador
     * @param string $responsavelPreenchimento
     * @param \stdClass $dados
     */
    public function __construct($tipoEvento, $empregador, $responsavelPreenchimento, $dado)
    {
        $this->tipoEvento = $tipoEvento;
        $this->empregador = $empregador;
        $this->responsavelPreenchimento = $responsavelPreenchimento;
        $this->dado = $dado;
        $this->iContador = 1;

        $dado = json_encode(\DBString::utf8_encode_all($this->dado));
        if (is_null($dado)) {
            throw new \Exception("Erro ao codificar dados para envio.");
        }
        $this->md5 = md5($dado);
    }

    public function adicionarFila($adicionarTarefa = false, $validaMd5 = true)
    {
        $where = array(
            "rh213_evento = '{$this->tipoEvento}'",
            "rh213_empregador = {$this->empregador}",
            "rh213_responsavelpreenchimento = '{$this->responsavelPreenchimento}'"
        );

        $where = implode(" and ", $where);
        $dao = new \cl_esocialenvio();
        $sql = $dao->sql_query_file(null, "*", null, $where);
        $rs = db_query($sql);

        if (!$rs) {
            throw new \Exception("Erro ao buscar registros.");
        }

        if (pg_num_rows($rs) == 1) {
            $md5Evento = \db_utils::fieldsMemory($rs, 0)->rh213_md5;
            if ($validaMd5) {
                if ($md5Evento == $this->md5) {
                    return false;
                }
            }
        }
        $codigoFila = pg_num_rows($rs) == 0 ? null : \db_utils::fieldsMemory($rs, 0)->rh213_sequencial;
        $this->adicionarEvento($codigoFila, $adicionarTarefa);

        return true;
    }

    /**
     * @param integer $codigo
     */
    private function adicionarEvento($codigo = null, $adicionarTarefa = null)
    {
        $daoFilaEsocial = new \cl_esocialenvio();
        $daoFilaEsocial->rh213_sequencial = $codigo;
        $daoFilaEsocial->rh213_evento = $this->tipoEvento;
        $daoFilaEsocial->rh213_empregador = $this->empregador;
        $daoFilaEsocial->rh213_responsavelpreenchimento = $this->responsavelPreenchimento;
        $daoFilaEsocial->rh213_dados = pg_escape_string(json_encode(\DBString::utf8_encode_all($this->dado)));
        $daoFilaEsocial->rh213_md5 = $this->md5;
        $daoFilaEsocial->rh213_situacao = 1;
        $daoFilaEsocial->rh213_data = date('Y-m-d H:i:s');


        if (empty($codigo)) {
            $daoFilaEsocial->incluir(null);
        } else {
            $daoFilaEsocial->alterar($codigo);
        }

        if ($daoFilaEsocial->erro_status == 0) {
            throw new \Exception("Não foi possível adicionar na fila.");
        }

        if ($adicionarTarefa) {
            $this->adicionarTarefa($daoFilaEsocial->rh213_sequencial);
        }

        $this->adicionarEventoStatus($daoFilaEsocial->rh213_sequencial);
    }

    private function adicionarEventoStatus($codigo, $mensagem = '')
    {
        $oDaoEsocialEnvioStatus = new \cl_esocialenviostatus();
        $oDaoEsocialEnvioStatus->excluir(null, "rh214_esocialenvio = {$codigo}");

        if ($oDaoEsocialEnvioStatus->erro_status == 0) {
            throw new \Exception("Não foi possível atualizar o status do evento.");
        }

        if (empty($mensagem) && empty($this->mensagem)) {
            $integracao = 'eSocial';
            if (strpos($this->tipoEvento, 'R') === 0) {
                $integracao = 'EFD-reinf';
            }
            $mensagem  = "Aguardando envio na rotina ";
            $mensagem .= "{$integracao} > Procedimentos > Envio de eventos para o {$integracao}.";
        } else {
            if (!empty($this->mensagem)) {
                $mensagem = $this->mensagem;
            }
        }

        $oDaoEsocialEnvioStatus->rh214_esocialenvio = $codigo;
        $oDaoEsocialEnvioStatus->rh214_descricao = $mensagem;
        $oDaoEsocialEnvioStatus->rh214_situacao = 'false';

        $oDaoEsocialEnvioStatus->incluir(null);

        if ($oDaoEsocialEnvioStatus->erro_status == 0) {
            throw new \Exception("Não foi possível incluir o status do evento.");
        }
    }

    /**
     * Cria o job
     *
     * @param integer $idFila
     */
    private function adicionarTarefa($idFila)
    {
        $job = new \Job();
        $job->setNome("eSocial_Evento_" . $this->tipoEvento . "_$idFila");
        $job->setCodigoUsuario(1);
        $time = new \DateTime();
        $time->modify('+ 1 minute');
        $time->modify("+ ". ($this->iContador + 1)." second");
        $job->setMomentoCricao($time->getTimestamp());
        $job->setDescricao('Evento eSocial ' . $this->tipoEvento);
        $job->setNomeClasse('FilaESocialTask');
        $job->setTipoPeriodicidade(\Agenda::PERIODICIDADE_UNICA);
        $job->adicionarParametro("id_fila", $idFila);
        $job->setCaminhoPrograma('model/esocial/FilaESocialTask.model.php');
        $job->salvar();
    }
}
