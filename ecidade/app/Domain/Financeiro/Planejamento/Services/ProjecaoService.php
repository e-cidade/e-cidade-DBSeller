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

namespace App\Domain\Financeiro\Planejamento\Services;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Planejamento\Models\Planejamento;
use App\Domain\Financeiro\Planejamento\Requests\Procedimentos\Projecao\CalculaProjecaoRequest;
use Illuminate\Support\Facades\DB;

/**
 * Class ProjecaoService
 * @package App\Domain\Financeiro\Planejamento\Services
 */
abstract class ProjecaoService
{
    /**
     * @var Planejamento
     */
    protected $planejamento;
    /**
     * @var integer
     */
    protected $idInstituicao;
    /**
     * @var DBConfig
     */
    protected $instituicao;
    /**
     * @var integer
     */
    protected $idUsuario;
    /**
     * @var integer
     */
    protected $anoSessao;
    /**
     * data de acesso do usuário
     * @var string
     */
    protected $dataUsuario;
    /**
     * Se configurado para trabalhar com decimáis
     * @var int
     */
    protected $precisaoRound;

    /**
     * @param CalculaProjecaoRequest $request
     * @return $this
     */
    public function porRequest(CalculaProjecaoRequest $request)
    {
        $this->planejamento = Planejamento::find($request->get('planejamento'));
        $this->instituicao = DBConfig::find($request->get('DB_instit'));
        $this->idUsuario = $request->get('DB_id_usuario');
        $this->anoSessao = $request->get('DB_anousu');
        $this->dataUsuario = date('Y-m-d', $request->get('DB_datausu'));
        $this->precisaoRound = $this->getPrecisao();

        return $this;
    }

    /**
     * Verifica se o parâmetro para controlar digitação de decimais esta ativo no exercício.
     * Se sim retornar a precisão de duas casas decimais, se não retornar 0
     * @return int
     */
    protected function getPrecisao()
    {
        $parametro = DB::table('orcparametro')
            ->where('o50_anousu', '=', $this->anoSessao)
            ->select('o50_liberadecimalppa')
            ->get()
            ->shift();

        return $parametro->o50_liberadecimalppa ? 2 : 0;
    }

    protected function buidValor($ano, $valor)
    {
        return (object)[
            'ano' => $ano,
            'valor' => $valor
        ];
    }

    /**
     * Apaga todo calculo e refaz a projeção
     * Retorna um array com a estimativa
     * @return array
     */
    abstract public function recalcular();

    /**
     * Cria o calculo se não houver.
     * Depois de calculado retorna a estimativa
     * @return array
     */
    abstract public function get();

    /**
     * Deve realizar a projeção e salvar a estimativa/projeção
     */
    abstract protected function calcular();
}
