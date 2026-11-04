<?php

namespace App\Domain\Saude\Ambulatorial\Resources;

class UnificacaoCgsResource
{
    public static function toArray($dados)
    {

        $totalRegistros = $dados['total'];
        $dadosFormatado = $dados['data'];

        $dadosGerais = [];
        foreach ($dadosFormatado as $dadoFormatado) {
            $dadosGerais[] = (object)[
                'codigo' => $dadoFormatado['codigo'],
                'cgs' => $dadoFormatado['cgs'],
                'nome' => $dadoFormatado['nome'],
                'data_processamento' => $dadoFormatado['data_processamento'],
                'hora_processamento' => $dadoFormatado['hora_processamento'],
                'login_processamento' => $dadoFormatado['login_processamento'],
                'processado' => $dadoFormatado['processado'],
                'instituicao' => $dadoFormatado['instituicao']
            ];
        }

        return (object)[
            'totalRegistros' => $totalRegistros,
            'data' => $dadosGerais
        ];
    }
}
