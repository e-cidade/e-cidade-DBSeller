<?php

namespace App\Domain\Tributario\ISSQN\Repository\SimplesNacional;

use DateTime;

class AtualizaCadastroOptantesSimplesNacionalRepository
{
    /**
     * Metodo para buscar um registro no banco de dados
     * com base no cnpj fornecido
     *
     * @param String $cnpj
     */
    public function consultaCadastroCnpj($cnpj)
    {
        $dados = \DB::table('issbase')
            ->selectRaw('issbase.q02_inscr as inscricao')
            ->selectRaw('q38_categoria as porte')
            ->selectRaw('q38_sequencial as sequencialisscadsimples')
            ->selectRaw(
                '(
                    (
                        select
                            count(*)
                        from
                            isscadsimplesbaixa
                        where
                            q39_isscadsimples = q38_sequencial
                    ) > 0
                 ) as comDataDeBaixa'
            )->selectRaw(
                '(
                    (
                        select
                            count(*)
                        from
                            isscadsimples
                        where
                            isscadsimples.q38_inscr = issbase.q02_inscr
                    ) > 0
                 ) as cadastradoComoSimples'
            )->selectRaw('q39_dtbaixa as datadebaixaisscadsimples')
            ->selectRaw('q38_dtinicial as datainclusaoisscadsimples')
            ->join(
                'cgm',
                'cgm.z01_numcgm',
                '=',
                'issbase.q02_numcgm'
            )
            ->leftJoin(
                'isscadsimples',
                'issbase.q02_inscr',
                '=',
                'isscadsimples.q38_inscr'
            )
            ->leftJoin(
                'isscadsimplesbaixa',
                'isscadsimplesbaixa.q39_isscadsimples',
                '=',
                'isscadsimples.q38_sequencial'
            )
            ->where(
                'cgm.z01_cgccpf',
                '=',
                $cnpj
            )
            ->orderBy('q39_dtbaixa', 'desc')
            ->orderBy('q38_dtinicial', 'asc')
            ->get();

        return $dados;
    }

    /**
     * @param Int $inscricao
     * @param String $dataInicial
     * @param Int $categoriaPorte
     * @return Int
     */
    public function incluiRegistroIssCadSimples(
        $inscricao,
        $dataInicial,
        $categoriaPorte
    ) {
        $nextval = \DB::select("select nextval('isscadsimples_q38_sequencial_seq')")[0]
            ->nextval;

        $sequencialIssCadSimples = \DB::select(\DB::raw(
            "insert into
                isscadsimples (
                    q38_sequencial,
                    q38_inscr,
                    q38_dtinicial,
                    q38_categoria,
                    q38_observacao
                ) values (
                    {$nextval},
                    {$inscricao},
                    '{$dataInicial}',
                    {$categoriaPorte},
                    'Inserido pela integração com Api da Receita Federal.'
                ) returning q38_sequencial"
        ))[0]->q38_sequencial;

        $sequencialIssCadAtualizacoes = $this->incluiLogIssCadSimplesAtualizacoes();
        $this->incluiLogIssCadSimplesAtualizacoesInclusao(
            $sequencialIssCadSimples,
            $sequencialIssCadAtualizacoes
        );

        return $sequencialIssCadSimples;
    }

    /**
     * @param Int $sequencialIssCadSimples
     * @param String $dataBaixaSimplesNacional
     * @return Int
     */
    public function incluiRegistroBaixaIssCadSimplesBaixa(
        $sequencialIssCadSimples,
        $dataBaixaSimplesNacional
    ) {
        $nextval = \DB::select("select nextval('isscadsimplebaixa_q39_sequencial_seq')")[0]
            ->nextval;

        $sequencialIssCadSimplesBaixa = \DB::select(\DB::raw(
            "insert into
                 isscadsimplesbaixa (
                     q39_sequencial,
                     q39_isscadsimples,
                     q39_dtbaixa,
                     q39_issmotivobaixa,
                     q39_obs
                 )
             values
                 (
                     {$nextval},
                     {$sequencialIssCadSimples},
                     '{$dataBaixaSimplesNacional}',
                     3,
                     'Baixa realizada pela integração com Api da Receita Federal.'
                 ) returning q39_sequencial"
        ))[0]->q39_sequencial;

        $sequencialIssCadAtualizacoes = $this->incluiLogIssCadSimplesAtualizacoes();
        $this->incluiLogIssCadSimplesAtualizacoesBaixa(
            $sequencialIssCadSimplesBaixa,
            $sequencialIssCadAtualizacoes
        );

        return $sequencialIssCadSimplesBaixa;
    }

    /**
     * Metodo para retornar todos os cnpjs cadastrados na
     * tabela issbase
     */
    public function cnpjsCadastradosIssBase()
    {
        $cnpjsCadastrados = \DB::table('issbase')
            ->selectRaw('distinct z01_cgccpf')
            ->join(
                'cgm',
                'cgm.z01_numcgm',
                '=',
                'issbase.q02_numcgm'
            )
            ->whereRaw("
                q02_dtbaix is null
                and length(z01_cgccpf) = 14 
                and z01_cgccpf not in (
                        '00000000000000',
                        '11111111111111',
                        '22222222222222',
                        '33333333333333',
                        '44444444444444',
                        '55555555555555',
                        '66666666666666',
                        '77777777777777',
                        '88888888888888',
                        '99999999999999'
                    ) 
            ")
            ->get()->toArray();

        $cnpjsCadastrados = array_map(function ($item) {
            return $item->z01_cgccpf;
        }, $cnpjsCadastrados);

        return $cnpjsCadastrados;
    }

    /**
     * Metodo para incluir log de registros na tabela
     * isscadsimplesatualizacoes para controle daquilo
     * que foi alterado nos cadastros
     */
    private function incluiLogIssCadSimplesAtualizacoes()
    {
        $dataHoje = (new DateTime('now'))->format('Y/m/d');

        return \DB::select(\DB::raw(
            "insert into isscadsimplesatualizacoes
                  (
                    q186_data
                  )
             values
                 (
                     '{$dataHoje}'
                 ) returning q186_sequencial"
        ))[0]->q186_sequencial;
    }

    /**
     * Metodo para incluir log de registros na tabela
     * isscadsimplesatualizacoesinclusao para controle daquilo
     * que foi alterado nos cadastros
     */
    private function incluiLogIssCadSimplesAtualizacoesInclusao(
        $sequencialIssCadSimples,
        $sequencialIssCadSimplesAtualizacoes
    ) {
        return \DB::select(\DB::raw(
            "insert into isscadsimplesatualizacoesinclusao
                  (
                    q187_isscadsimplesatualizacoes,
                    q187_isscadsimples
                  )
             values
                 (
                     {$sequencialIssCadSimplesAtualizacoes},
                     {$sequencialIssCadSimples}
                 ) returning q187_sequencial"
        ))[0]->q187_sequencial;
    }

    /**
     * Metodo para incluir log de registros na tabela
     * isscadsimplesatualizacoesbaixa para controle daquilo
     * que foi alterado nos cadastros
     */
    private function incluiLogIssCadSimplesAtualizacoesBaixa(
        $sequencialIssCadSimplesBaixa,
        $sequencialIssCadSimplesAtualizacoes
    ) {
        return \DB::select(\DB::raw(
            "insert into isscadsimplesatualizacoesbaixa
                  (
                    q188_isscadsimplesatualizacoes,
                    q188_isscadsimplesbaixa
                  )
             values
                 (
                     {$sequencialIssCadSimplesAtualizacoes},
                     {$sequencialIssCadSimplesBaixa}
                 ) returning q188_sequencial"
        ))[0]->q188_sequencial;
    }
}
