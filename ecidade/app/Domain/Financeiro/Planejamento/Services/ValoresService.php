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

use App\Domain\Financeiro\Planejamento\Models\Valor;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class ValoresService
 * @package App\Domain\Financeiro\Planejamento\Services
 */
class ValoresService
{
    /**
     * Salva a partir do array recebido na request
     *
     * @example
     *  $jsonValores deve ser o array de valores recebido na request
     *    [{"ano":2022,"valor":123},{"ano":2023,"valor":123},{"ano":2024,"valor":123},{"ano":2025,"valor":123}]
     * @param string $jsonValores
     * @param string $origem referencia de quem é o dono dos dados
     * @param integer $chave id da tabela
     * @return Collection
     * @throws Exception
     */
    public static function saveFromJson($jsonValores, $origem, $chave)
    {
        $valores = str_replace('\"', '"', $jsonValores);
        $valores = \JSON::create()->parse($valores);
        $service = new self();
        return $service->salvarColecao($valores, $origem, $chave);
    }

    /**
     * @param Collection $valores
     */
    public function remover(Collection $valores)
    {
        $valores->map(function (Valor $valor) {
            $valor->delete();
        });
    }

    /**
     * Ao optar por salvar uma coleção de valores, primeiro é removido todos valores salvos para a chave e origem
     * @param array $valores
     * @param $origem
     * @param $chave
     * @return Collection
     * @throws Exception
     */
    public function salvarColecao(array $valores, $origem, $chave)
    {
        $this->delete($chave, $origem);

        $valoresSalvos = collect([]);
        foreach ($valores as $dadoValor) {
            $valoresSalvos->push($this->salvar($origem, $chave, $dadoValor->valor, $dadoValor->ano));
        }

        return $valoresSalvos;
    }

    /**
     * @param $origem
     * @param $chave
     * @param $valor
     * @param $ano
     * @param false $find
     * @return Valor
     */
    public function salvar($origem, $chave, $valor, $ano, $find = false)
    {
        $modal = new Valor();
        if ($find) {
            $modal = Valor::where('pl10_chave', '=', $chave)
                ->where('pl10_origem', '=', $origem)
                ->where('pl10_ano', '=', $ano)
                ->first();
            if (is_null($modal)) {
                $modal = new Valor();
            }
        }

        $modal->pl10_ano = $ano;
        $modal->pl10_valor = $valor;
        $modal->pl10_origem = $origem;
        $modal->pl10_chave = $chave;
        $modal->save();
        return $modal;
    }

    /**
     * @param $chave
     * @param $origem
     * @throws Exception
     */
    public function delete($chave, $origem)
    {
        Valor::where('pl10_chave', '=', $chave)
            ->where('pl10_origem', '=', $origem)
            ->delete();
    }
}
