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

use App\Domain\Financeiro\Planejamento\Models\Iniciativa;
use App\Domain\Financeiro\Planejamento\Models\MetasIniciativa;
use App\Domain\Financeiro\Orcamento\Models\ProjetoAtividadeUnidadeMedida;
use Exception;
use stdClass;

/**
 * Class MetasIniciativaService
 * @package App\Domain\Financeiro\Planejamento\Services
 */
class MetasIniciativaService
{

    /**
     * @param stdClass $dados
     * @return array
     * @throws Exception
     */
    public function salvarFromObject(stdClass $dados)
    {
        $metas = [];

        $configuracao = new ConfiguracaoService();
        $apenasValorAnalitico = $configuracao->get()->apenas_valor_analitico;

        $iniciativa = Iniciativa::find($dados->pl12_codigo);
        $iniciativa->metas()->delete();
        
        foreach ($dados->metas as $dadosMeta) {
            $dadosMeta = \JSON::create()->parse(str_replace('\"', '"', $dadosMeta));
            $meta = new MetasIniciativa();

            if (empty($dadosMeta->exercicio)) {
                throw new Exception("Você não pode salvar uma meta sem informar o exercício.", 403);
            }
            if (!$apenasValorAnalitico &&
                (is_null($dadosMeta->meta_financeira) || $dadosMeta->meta_financeira === '')) {
                throw new Exception("Você não pode salvar uma meta sem informar o valor da meta financeira.", 403);
            }

            /**
             * Bloco de vinculo da atividade projeto com o id da unidade de medida do Sigfis
             * especificamente para os clientes do RJ
             */
            $model = ProjetoAtividadeUnidadeMedida::where('o221_orcprojativ', $iniciativa->pl12_orcprojativ)
            ->where('o221_anousu', $dadosMeta->exercicio);
            
            $modelResult = $model->get();

            if (isRioDeJaneiro() && count($modelResult) == 0 && !empty($dadosMeta->id_unidade)) {
                $projetoAtividadeUnidadeMedida = new ProjetoAtividadeUnidadeMedida();
                
                $projetoAtividadeUnidadeMedida->o221_orcprojativ = $iniciativa->pl12_orcprojativ;
                $projetoAtividadeUnidadeMedida->o221_anousu =$dadosMeta->exercicio;
                $projetoAtividadeUnidadeMedida->o221_unidade = $dadosMeta->id_unidade;
                
                if (!$projetoAtividadeUnidadeMedida->save()) {
                    throw new Exception("Não foi possível vincular a unidade de medida a atividade projeto!");
                }
            } elseif (isRioDeJaneiro() && count($modelResult) > 0 && !empty($dadosMeta->id_unidade)) {
                if (!$model->update([
                    'o221_unidade' => $dadosMeta->id_unidade
                ])) {
                    throw new Exception("Não foi possível atualizar a unidade de medida a atividade projeto!");
                }
            } elseif (isRioDeJaneiro() && count($modelResult) > 0 && empty($dadosMeta->id_unidade)) {
                if (!$model->delete([
                    'o221_unidade' => $dadosMeta->id_unidade
                ])) {
                    throw new Exception("Não foi possível desvincular a unidade de medida da atividade projeto!");
                }
            }
                                    
            $meta->exercicio = $dadosMeta->exercicio;
            $meta->meta_financeira = $dadosMeta->meta_financeira;
            $meta->unidade = $dadosMeta->unidade;
            $meta->meta_fisica = $dadosMeta->meta_fisica?:null;
            $meta->iniciativa()->associate($iniciativa);

            $meta->save();
            $metas[] = $meta;
        }

        return $metas;
    }
}
