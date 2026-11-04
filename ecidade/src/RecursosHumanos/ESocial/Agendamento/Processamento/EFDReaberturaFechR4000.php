<?php

namespace ECidade\RecursosHumanos\ESocial\Agendamento\Processamento;

use BusinessException;
use CgmRepository;
use ECidade\RecursosHumanos\ESocial\Agendamento\Evento;
use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use Exception;
use stdClass;

class EFDReaberturaFechR4000 extends ProcessamentoAbstract implements ProcessamentoInterface
{

    private $cgm;
    private $indFechReab;
    private $ano;
    private $mes;

    public function __construct($cgm, $ano = null, $mes = null)
    {
        $this->cgm = $cgm;
        $this->ano = $ano;
        $this->mes = $mes;
    }

    public function processar()
    {
        $alteracaoDados = false;

        if ($this->indFechReab == "") {
            throw new \BusinessException("Deve-se informar o indicativo");
        }

        $dadosEvento = $this->preProcessar();
        $eventoFila = new Evento(Tipo::R4099, $this->cgm, $dadosEvento->referencia, $dadosEvento);

        if ($eventoFila->adicionarFila(false, false)) {
            $alteracaoDados = true;
        }

        return $alteracaoDados;
    }

    private function preProcessar()
    {
        $sql = "select * from esocial.efdreabfechdadosresp where efd10_numcgm = {$this->cgm} limit 1";
        $rs  = db_query($sql);

        if (!$rs) {
            throw new BusinessException('[Erro Técnico] Não foi possível buscar dados do responsável');
        }

        if (pg_num_rows($rs) == 0) {
            throw new BusinessException("
                Não há informação de responsavel para R-4099.\n
                Deve-se preencher em efdreinf > preenchimento > Responsável R-4099
            ");
        }

        $resp  = pg_fetch_object($rs, 0);
        $dados = new stdClass();

        $dados->perapur = "{$this->ano}-{$this->mes}";
        $dados->inscricao_contribuinte = CgmRepository::getByCodigo($this->cgm)->getCnpj();
        $dados->referencia = "{$this->ano}-{$this->mes} {$this->indFechReab} $dados->inscricao_contribuinte";

        $dados->iderespinf = new \stdClass();
        $dados->iderespinf->nmresp   = $resp->efd10_nome;
        $dados->iderespinf->cpfresp  = $resp->efd10_cpf;
        $dados->iderespinf->telefone = $resp->efd10_telefone;
        $dados->iderespinf->email    = $resp->efd10_email;
        $dados->fechret = $this->indFechReab;

        return $dados;
    }

    public function setIndFechReab($indFechReab)
    {
        $this->indFechReab = $indFechReab;
    }
}
