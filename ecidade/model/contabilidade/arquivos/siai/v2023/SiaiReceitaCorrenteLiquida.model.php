<?php
/*
 * E-cidade Software Publico para Gestao Municipal
 * Copyright (C) 2013 DBselller Servicos de Informatica
 * www.dbseller.com.br
 * e-cidade@dbseller.com.br
 *
 * Este programa e software livre; voce pode redistribui-lo e/ou
 * modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 * publicada pela Free Software Foundation; tanto a versao 2 da
 * Licenca como (a seu criterio) qualquer versao mais nova.
 *
 * Este programa e distribuido na expectativa de ser util, mas SEM
 * QUALQUER GARANTIA; sem mesmo a garantia implicita de
 * COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 * PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 * detalhes.
 *
 * Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 * junto com este programa; se nao, escreva para a Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307, USA.
 *
 * Copia da licenca no diretorio licenca/licenca_en.txt
 * licenca/licenca_pt.txt
 */
require_once(modification("model/contabilidade/arquivos/siai/SiaiArquivoBase.model.php"));

use App\Domain\Financeiro\Contabilidade\Factories\AnexoTresFactory;

class SiaiReceitaCorrenteLiquida extends SiaiArquivoBase
{
    
    /**
     * Busca os dados para gerar o Arquivo da Receita
     */
    public function gerarDados()
    {
        $iNumLinha = 1;
        
        $arqFinal = fopen("tmp/A03_" . $this->sBimReferencia . ".TXT", 'w+');
        $this->setNomeArquivo("A03_" . $this->sBimReferencia . ".TXT");
        
        $fitros = array();
        $filtros["DB_anousu"] = 2023;
        $filtros["DB_instit"] = 1;
        $filtros["periodo"] = 7;
        $filtros["codigo_relatorio"] = 270;
        $relatorio = AnexoTresFactory::getServiceMdf(2023, $filtros);
        $relatorio->processar();
        
        dd($relatório);
    }
}
