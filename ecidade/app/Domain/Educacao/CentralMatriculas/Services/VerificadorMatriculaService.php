<?php
/**
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

namespace App\Domain\Educacao\CentralMatriculas\Services;

use ECidade\Educacao\MatriculaOnline\Model\Candidato;

class VerificadorMatriculaService
{
    public function verificarCandidatoMatriculado($cpf = '', Candidato $candidato = null)
    {
        $aluno = $this->buscarAluno($cpf, $candidato);
        if (is_null($aluno)) {
            return false;
        }

        return $this->verificarMatriculaAtiva($aluno);
    }

    private function buscarAluno($cpf = null, Candidato $candidato = null)
    {
        $aluno = null;
        if (!empty($cpf)) {
            $aluno = \AlunoRepository::getAlunoByCpf($cpf);
        }

        if (!is_null($aluno)) {
            return $aluno;
        }

        if (!empty($candidato)) {
            $dataNascimento = new \DBDate($candidato->getDataNascimento()->format('Y-m-d'));
            $aluno = \AlunoRepository::getAlunoPorNomeDataNascimentoNomeMae(
                $candidato->getNome(),
                $candidato->getMae(),
                $dataNascimento
            );
            if (is_null($aluno)) {
                $aluno = \AlunoRepository::getAlunoPorNomeDataNascimentoNomeMae(
                    $candidato->getNome(),
                    $candidato->getPai(),
                    $dataNascimento
                );
            }
        }

        return $aluno;
    }

    private function verificarMatriculaAtiva(\Aluno $aluno)
    {
        $matriculaAtiva = \MatriculaRepository::getMatriculaAtivaPorAluno($aluno);

        if (!is_null($matriculaAtiva)) {
            return true;
        }

        $ultimaMatricula = \MatriculaRepository::getUltimaMatriculaAluno($aluno);
        if (is_null($ultimaMatricula)) {
            return false;
        }
        if ($ultimaMatricula->getSituacao() !== 'MATRICULADO') {
            return false;
        }

        $dataAtual = new \DateTime();
        $anoAtual = $dataAtual->format('Y');
        $dataLimite = new \DateTime("{$anoAtual}-03-31");
        $anosPermitidos = [$anoAtual];

        if ($dataAtual->getTimestamp() <= $dataLimite->getTimestamp()) {
            $anosPermitidos[] = $anoAtual-1;
        }
        if (in_array($ultimaMatricula->getTurma()->getCalendario()->getAnoExecucao(), $anosPermitidos)) {
            return true;
        }

        return false;
    }
}
