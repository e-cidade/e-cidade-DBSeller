<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class M24716AjustandoTabelaCursoformacao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('cursoformacao')->get()->map(function ($registro) {
            if (str_contains($registro->ed94_c_codigocenso, '+')) {
                if (strlen($registro->ed94_c_codigocenso) === 8) {
                    $parte1 = '0' . $registro->ed94_c_codigocenso[0];
                    $parte2 = substr($registro->ed94_c_codigocenso, 2, 3);
                    $parte3 = substr($registro->ed94_c_codigocenso, 6, 2);
                    $parte3 = '0' . strval(intval($parte3) - 2);
                }
                if (strlen($registro->ed94_c_codigocenso) === 7) {
                    $parte1 = '0' . $registro->ed94_c_codigocenso[0];
                    $parte2 = substr($registro->ed94_c_codigocenso, 2, 2);
                    $parte2 = $parte2[0] . '0' . $parte2[1];
                    $parte3 = substr($registro->ed94_c_codigocenso, 5, 2);
                    $parte3 = '0' . strval(intval($parte3) - 2);
                }

                if (strlen($registro->ed94_c_codigocenso) === 9) {
                    $parte1 = $registro->ed94_c_codigocenso[0];
                    $parte2 = substr($registro->ed94_c_codigocenso, 2, 4);
                    $parte3 = substr($registro->ed94_c_codigocenso, 7, 2);
                    $parte3 = '0' . strval(intval($parte3) - 3);
                }

                $novoCodigo = $parte1.$parte2.$parte3;
                DB::table('cursoformacao')->where('ed94_i_codigo', $registro->ed94_i_codigo)->update(
                    ['ed94_c_codigocenso' => $novoCodigo]
                );
            }
        });

        $dados = [
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0111C012',
                'ed94_c_descr' => 'Ciência da educação - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0111C014',
                'ed94_c_descr' => 'Ciência da educação - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0111P013',
                'ed94_c_descr' => 'Processos escolares - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0111P022',
                'ed94_c_descr' => 'Psicopedagogia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0111P024',
                'ed94_c_descr' => 'Psicopedagogia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0112E011',
                'ed94_c_descr' => 'Educação infantil formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0113E011',
                'ed94_c_descr' => 'Educação do campo formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0113E021',
                'ed94_c_descr' => 'Educação especial formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0113E031',
                'ed94_c_descr' => 'Educação indígena formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0113F011',
                'ed94_c_descr' => 'Formação pedagógica de professor para a educação básica - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0113F014',
                'ed94_c_descr' => 'Formação pedagógica de professor para a educação básica - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0113P011',
                'ed94_c_descr' => 'Pedagogia - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0113P012',
                'ed94_c_descr' => 'Pedagogia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0113P014',
                'ed94_c_descr' => 'Pedagogia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114A011',
                'ed94_c_descr' => 'Artes formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114A014',
                'ed94_c_descr' => 'Artes formação de professor - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114A021',
                'ed94_c_descr' => 'Artes visuais formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114B011',
                'ed94_c_descr' => 'Biologia formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114C011',
                'ed94_c_descr' => 'Ciências agrárias formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114C021',
                'ed94_c_descr' => 'Ciências naturais formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114C031',
                'ed94_c_descr' => 'Ciências sociais formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114C041',
                'ed94_c_descr' => 'Cinema e audiovisual formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114C051',
                'ed94_c_descr' => 'Computação formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114D011',
                'ed94_c_descr' => 'Dança formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114E011',
                'ed94_c_descr' => 'Economia doméstica formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114E021',
                'ed94_c_descr' => 'Educação do campo em áreas de conhecimento da educação básica formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114E031',
                'ed94_c_descr' => 'Educação física formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114E041',
                'ed94_c_descr' => 'Educação indígena em áreas de conhecimento da educação básica formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114E051',
                'ed94_c_descr' => 'Enfermagem formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114E061',
                'ed94_c_descr' => 'Ensino profissionalizante em área específica formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114E071',
                'ed94_c_descr' => 'Ensino religioso formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114E081',
                'ed94_c_descr' => 'Estatística formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114F011',
                'ed94_c_descr' => 'Filosofia formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114F021',
                'ed94_c_descr' => 'Física formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114G011',
                'ed94_c_descr' => 'Geografia formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114H011',
                'ed94_c_descr' => 'História formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114M011',
                'ed94_c_descr' => 'Matemática formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114M021',
                'ed94_c_descr' => 'Música formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114P011',
                'ed94_c_descr' => 'Psicologia formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114Q011',
                'ed94_c_descr' => 'Química formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0114T011',
                'ed94_c_descr' => 'Teatro formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L011',
                'ed94_c_descr' => 'Letras alemão formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L021',
                'ed94_c_descr' => 'Letras espanhol formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L031',
                'ed94_c_descr' => 'Letras francês formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L041',
                'ed94_c_descr' => 'Letras inglês formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L051',
                'ed94_c_descr' => 'Letras italiano formação de professor  - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L061',
                'ed94_c_descr' => 'Letras japonês formação de professor  - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L071',
                'ed94_c_descr' => 'Letras língua brasileira de sinais formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L081',
                'ed94_c_descr' => 'Letras línguas estrangeiras clássicas formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L091',
                'ed94_c_descr' => 'Letras linguística formação de professor  - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L101',
                'ed94_c_descr' => 'Letras outras línguas estrangeiras modernas formação de professor  - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L111',
                'ed94_c_descr' => 'Letras português alemão formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L121',
                'ed94_c_descr' => 'Letras português espanhol formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L131',
                'ed94_c_descr' => 'Letras português formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L141',
                'ed94_c_descr' => 'Letras português francês formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L151',
                'ed94_c_descr' => 'Letras português inglês formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L161',
                'ed94_c_descr' => 'Letras português italiano formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L171',
                'ed94_c_descr' => 'Letras português japonês formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L181',
                'ed94_c_descr' => 'Letras português língua brasileira de sinais formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L191',
                'ed94_c_descr' => 'Letras português línguas estrangeiras clássicas formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L201',
                'ed94_c_descr' => 'Letras português outras línguas estrangeiras modernas formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0115L211',
                'ed94_c_descr' => 'Letras tradutor e intérprete formação de professor - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0188P011',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo educação - Licenciatura',
                'ed94_i_grauacademico' => 3
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0188P012',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo educação - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 1,
                'ed94_c_descrclasse' => 'Educação',
                'ed94_c_codigocenso' => '0188P013',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo educação - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211A012',
                'ed94_c_descr' => 'Animação - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211A013',
                'ed94_c_descr' => 'Animação - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211A014',
                'ed94_c_descr' => 'Animação - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211C012',
                'ed94_c_descr' => 'Cinema e audiovisual - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211C013',
                'ed94_c_descr' => 'Cinema e audiovisual - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211C014',
                'ed94_c_descr' => 'Cinema e audiovisual - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211C023',
                'ed94_c_descr' => 'Comunicação assistiva - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211D012',
                'ed94_c_descr' => 'Design gráfico - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211D013',
                'ed94_c_descr' => 'Design gráfico - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211D014',
                'ed94_c_descr' => 'Design gráfico - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211F012',
                'ed94_c_descr' => 'Fotografia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211F013',
                'ed94_c_descr' => 'Fotografia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211F014',
                'ed94_c_descr' => 'Fotografia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211P012',
                'ed94_c_descr' => 'Produção audiovisual - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211P013',
                'ed94_c_descr' => 'Produção audiovisual - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211P014',
                'ed94_c_descr' => 'Produção audiovisual - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211P023',
                'ed94_c_descr' => 'Produção cênica - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211P032',
                'ed94_c_descr' => 'Produção cultural - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211P033',
                'ed94_c_descr' => 'Produção cultural - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211P034',
                'ed94_c_descr' => 'Produção cultural - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211P043',
                'ed94_c_descr' => 'Produção fonográfica - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211P052',
                'ed94_c_descr' => 'Produção multimídia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211P053',
                'ed94_c_descr' => 'Produção multimídia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0211P054',
                'ed94_c_descr' => 'Produção multimídia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0212D012',
                'ed94_c_descr' => 'Desenho industrial - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0212D022',
                'ed94_c_descr' => 'Design - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0212D023',
                'ed94_c_descr' => 'Design - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0212D032',
                'ed94_c_descr' => 'Design de interiores - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0212D033',
                'ed94_c_descr' => 'Design de interiores - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0212D034',
                'ed94_c_descr' => 'Design de interiores - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0212D042',
                'ed94_c_descr' => 'Design de produto - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0212D043',
                'ed94_c_descr' => 'Design de produto - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0212D044',
                'ed94_c_descr' => 'Design de produto - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0212M012',
                'ed94_c_descr' => 'Moda - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0212M013',
                'ed94_c_descr' => 'Moda - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0212M014',
                'ed94_c_descr' => 'Moda - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0213A012',
                'ed94_c_descr' => 'Artes - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0213A013',
                'ed94_c_descr' => 'Artes - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0213A014',
                'ed94_c_descr' => 'Artes - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0213A022',
                'ed94_c_descr' => 'Artes plásticas - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0213A023',
                'ed94_c_descr' => 'Artes plásticas - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0213A024',
                'ed94_c_descr' => 'Artes plásticas - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0213A032',
                'ed94_c_descr' => 'Artes visuais - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0213A033',
                'ed94_c_descr' => 'Artes visuais - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0213H012',
                'ed94_c_descr' => 'História da arte - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0214F013',
                'ed94_c_descr' => 'Fabricação de instrumentos musicais não industrial - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0215A012',
                'ed94_c_descr' => 'Artes cênicas - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0215A013',
                'ed94_c_descr' => 'Artes cênicas - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0215D012',
                'ed94_c_descr' => 'Dança - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0215D013',
                'ed94_c_descr' => 'Dança - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0215M012',
                'ed94_c_descr' => 'Música - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0215M013',
                'ed94_c_descr' => 'Música  - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0215M014',
                'ed94_c_descr' => 'Música - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0215T012',
                'ed94_c_descr' => 'Teatro - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0215T013',
                'ed94_c_descr' => 'Teatro - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0221C012',
                'ed94_c_descr' => 'Ciências da religião - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0221T012',
                'ed94_c_descr' => 'Teologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0221T014',
                'ed94_c_descr' => 'Teologia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0222A012',
                'ed94_c_descr' => 'Arqueologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0222A013',
                'ed94_c_descr' => 'Arqueologia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0222C012',
                'ed94_c_descr' => 'Conservação e restauro - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0222C013',
                'ed94_c_descr' => 'Conservação e restauro - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0222H012',
                'ed94_c_descr' => 'História - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0222H014',
                'ed94_c_descr' => 'História - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0223F012',
                'ed94_c_descr' => 'Filosofia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0223F014',
                'ed94_c_descr' => 'Filosofia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L012',
                'ed94_c_descr' => 'Letras alemão - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L023',
                'ed94_c_descr' => 'Letras escrita criativa - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L024',
                'ed94_c_descr' => 'Letras escrita criativa - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L032',
                'ed94_c_descr' => 'Letras espanhol - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L034',
                'ed94_c_descr' => 'Letras espanhol - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L042',
                'ed94_c_descr' => 'Letras francês - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L052',
                'ed94_c_descr' => 'Letras inglês - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L054',
                'ed94_c_descr' => 'Letras inglês - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L062',
                'ed94_c_descr' => 'Letras italiano - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L072',
                'ed94_c_descr' => 'Letras japonês - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L082',
                'ed94_c_descr' => 'Letras língua brasileira de sinais - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L083',
                'ed94_c_descr' => 'Letras língua brasileira de sinais - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L084',
                'ed94_c_descr' => 'Letras língua brasileira de sinais - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L092',
                'ed94_c_descr' => 'Letras línguas estrangeiras clássicas  - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L094',
                'ed94_c_descr' => 'Letras línguas estrangeiras clássicas - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L102',
                'ed94_c_descr' => 'Letras linguística - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L112',
                'ed94_c_descr' => 'Letras outras línguas estrangeiras modernas  - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L122',
                'ed94_c_descr' => 'Letras português - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L124',
                'ed94_c_descr' => 'Letras português - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L132',
                'ed94_c_descr' => 'Letras português alemão  - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L142',
                'ed94_c_descr' => 'Letras português espanhol - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L152',
                'ed94_c_descr' => 'Letras português francês - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L162',
                'ed94_c_descr' => 'Letras português inglês - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L172',
                'ed94_c_descr' => 'Letras português italiano - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L182',
                'ed94_c_descr' => 'Letras português japonês  - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L192',
                'ed94_c_descr' => 'Letras português língua brasileira de sinais - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L202',
                'ed94_c_descr' => 'Letras português línguas estrangeiras clássicas  - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L212',
                'ed94_c_descr' => 'Letras português outras línguas estrangeiras modernas - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L222',
                'ed94_c_descr' => 'Letras tradutor e intérprete - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L223',
                'ed94_c_descr' => 'Letras tradutor e intérprete - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0231L224',
                'ed94_c_descr' => 'Letras tradutor e intérprete - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0288P012',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo artes e humanidades - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 2,
                'ed94_c_descrclasse' => 'Artes e humanidades',
                'ed94_c_codigocenso' => '0288P014',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo artes e humanidades - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0311E012',
                'ed94_c_descr' => 'Economia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0311E014',
                'ed94_c_descr' => 'Economia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0312A012',
                'ed94_c_descr' => 'Antropologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0312A014',
                'ed94_c_descr' => 'Antropologia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0312C012',
                'ed94_c_descr' => 'Ciência política - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0312C014',
                'ed94_c_descr' => 'Ciência política  - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0312C022',
                'ed94_c_descr' => 'Ciências sociais - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0312C024',
                'ed94_c_descr' => 'Ciências sociais - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0312G012',
                'ed94_c_descr' => 'Geografia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0312R012',
                'ed94_c_descr' => 'Relações internacionais - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0312R014',
                'ed94_c_descr' => 'Relações internacionais - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0312S012',
                'ed94_c_descr' => 'Sociologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0312S014',
                'ed94_c_descr' => 'Sociologia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0313P012',
                'ed94_c_descr' => 'Psicologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0313P014',
                'ed94_c_descr' => 'Psicologia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0321C012',
                'ed94_c_descr' => 'Comunicação social - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0321J012',
                'ed94_c_descr' => 'Jornalismo - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0321J014',
                'ed94_c_descr' => 'Jornalismo - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0321P012',
                'ed94_c_descr' => 'Produção editorial - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0321P013',
                'ed94_c_descr' => 'Produção editorial - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0321P014',
                'ed94_c_descr' => 'Produção editorial - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0321R012',
                'ed94_c_descr' => 'Rádio, TV e internet - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0321R013',
                'ed94_c_descr' => 'Rádio, TV e internet - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0321R014',
                'ed94_c_descr' => 'Rádio, TV e internet - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0322A012',
                'ed94_c_descr' => 'Arquivologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0322B012',
                'ed94_c_descr' => 'Biblioteconomia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0322G012',
                'ed94_c_descr' => 'Gestão da informação - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0322G014',
                'ed94_c_descr' => 'Gestão da informação - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0322M012',
                'ed94_c_descr' => 'Museologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0322M013',
                'ed94_c_descr' => 'Museologia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0322M014',
                'ed94_c_descr' => 'Museologia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0388P012',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo ciências sociais, jornalismo e informação - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 3,
                'ed94_c_descrclasse' => 'Ciências sociais, comunicação e informação',
                'ed94_c_codigocenso' => '0388P014',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo ciências sociais, jornalismo e informação - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0411C012',
                'ed94_c_descr' => 'Contabilidade - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0411C013',
                'ed94_c_descr' => 'Contabilidade  - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0411C014',
                'ed94_c_descr' => 'Contabilidade - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0411G013',
                'ed94_c_descr' => 'Gestão fiscal e tributária - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0412G012',
                'ed94_c_descr' => 'Gestão financeira - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0412G013',
                'ed94_c_descr' => 'Gestão financeira  - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0412G014',
                'ed94_c_descr' => 'Gestão financeira - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0412S013',
                'ed94_c_descr' => 'Seguros - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0412S014',
                'ed94_c_descr' => 'Seguros - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413A012',
                'ed94_c_descr' => 'Administração  - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413A014',
                'ed94_c_descr' => 'Administração - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413A022',
                'ed94_c_descr' => 'Administração pública - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413A023',
                'ed94_c_descr' => 'Administração pública - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413A024',
                'ed94_c_descr' => 'Administração pública - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413C012',
                'ed94_c_descr' => 'Comércio exterior - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413C013',
                'ed94_c_descr' => 'Comércio exterior - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413C014',
                'ed94_c_descr' => 'Comércio exterior - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413E012',
                'ed94_c_descr' => 'Empreendedorismo - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413E013',
                'ed94_c_descr' => 'Empreendedorismo - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413E014',
                'ed94_c_descr' => 'Empreendedorismo - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G013',
                'ed94_c_descr' => 'Gestão da produção - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G014',
                'ed94_c_descr' => 'Gestão da produção - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G023',
                'ed94_c_descr' => 'Gestão da qualidade - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G024',
                'ed94_c_descr' => 'Gestão da qualidade - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G032',
                'ed94_c_descr' => 'Gestão da saúde - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G033',
                'ed94_c_descr' => 'Gestão da saúde - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G034',
                'ed94_c_descr' => 'Gestão da saúde - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G042',
                'ed94_c_descr' => 'Gestão de cooperativas - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G043',
                'ed94_c_descr' => 'Gestão de cooperativas - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G044',
                'ed94_c_descr' => 'Gestão de cooperativas - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G052',
                'ed94_c_descr' => 'Gestão de negócios - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G053',
                'ed94_c_descr' => 'Gestão de negócios - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G054',
                'ed94_c_descr' => 'Gestão de negócios - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G062',
                'ed94_c_descr' => 'Gestão de negócios internacionais - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G064',
                'ed94_c_descr' => 'Gestão de negócios internacionais - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G073',
                'ed94_c_descr' => 'Gestão de pessoas - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G074',
                'ed94_c_descr' => 'Gestão de pessoas - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G083',
                'ed94_c_descr' => 'Gestão de serviços - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G084',
                'ed94_c_descr' => 'Gestão de serviços - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G092',
                'ed94_c_descr' => 'Gestão do agronegócio - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G093',
                'ed94_c_descr' => 'Gestão do agronegócio - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G094',
                'ed94_c_descr' => 'Gestão do agronegócio - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G103',
                'ed94_c_descr' => 'Gestão estratégica - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G104',
                'ed94_c_descr' => 'Gestão estratégica - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G113',
                'ed94_c_descr' => 'Gestão hospitalar - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G114',
                'ed94_c_descr' => 'Gestão hospitalar - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G122',
                'ed94_c_descr' => 'Gestão pública - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G123',
                'ed94_c_descr' => 'Gestão pública - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413G124',
                'ed94_c_descr' => 'Gestão pública - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413L012',
                'ed94_c_descr' => 'Logística - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413L013',
                'ed94_c_descr' => 'Logística - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0413L014',
                'ed94_c_descr' => 'Logística - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0414M012',
                'ed94_c_descr' => 'Marketing - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0414M013',
                'ed94_c_descr' => 'Marketing - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0414M014',
                'ed94_c_descr' => 'Marketing - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0414P012',
                'ed94_c_descr' => 'Publicidade e propaganda - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0414P013',
                'ed94_c_descr' => 'Publicidade e propaganda - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0414P014',
                'ed94_c_descr' => 'Publicidade e propaganda - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0414R012',
                'ed94_c_descr' => 'Relações públicas - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0414R013',
                'ed94_c_descr' => 'Relações públicas - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0414R014',
                'ed94_c_descr' => 'Relações públicas - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0415S012',
                'ed94_c_descr' => 'Secretariado - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0415S013',
                'ed94_c_descr' => 'Secretariado - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0415S014',
                'ed94_c_descr' => 'Secretariado - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0416G013',
                'ed94_c_descr' => 'Gestão comercial - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0416G014',
                'ed94_c_descr' => 'Gestão comercial - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0416N012',
                'ed94_c_descr' => 'Negócios imobiliários - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0416N013',
                'ed94_c_descr' => 'Negócios imobiliários - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0416N014',
                'ed94_c_descr' => 'Negócios imobiliários - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0421D012',
                'ed94_c_descr' => 'Direito - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0421D013',
                'ed94_c_descr' => 'Direito - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0421D014',
                'ed94_c_descr' => 'Direito - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0421S013',
                'ed94_c_descr' => 'Serviços jurídicos e cartoriais - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0421S014',
                'ed94_c_descr' => 'Serviços jurídicos e cartoriais - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0488P012',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo negócios, administração e direito - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 4,
                'ed94_c_descrclasse' => 'Negócios, administração e direito',
                'ed94_c_codigocenso' => '0488P013',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo negócios, administração e direito - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0511B012',
                'ed94_c_descr' => 'Biologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0511B014',
                'ed94_c_descr' => 'Biologia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0512B012',
                'ed94_c_descr' => 'Bioquímica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0512B022',
                'ed94_c_descr' => 'Biotecnologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0512B023',
                'ed94_c_descr' => 'Biotecnologia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0512B024',
                'ed94_c_descr' => 'Biotecnologia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0512T013',
                'ed94_c_descr' => 'Toxicologia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0521C012',
                'ed94_c_descr' => 'Ciências ambientais - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0521C014',
                'ed94_c_descr' => 'Ciências ambientais - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0521E012',
                'ed94_c_descr' => 'Ecologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0531Q012',
                'ed94_c_descr' => 'Química - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0531Q013',
                'ed94_c_descr' => 'Química - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0531Q022',
                'ed94_c_descr' => 'Química industrial e tecnológica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0531Q023',
                'ed94_c_descr' => 'Química industrial e tecnológica - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0531Q024',
                'ed94_c_descr' => 'Química industrial e tecnológica - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0532G012',
                'ed94_c_descr' => 'Geofísica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0532G022',
                'ed94_c_descr' => 'Geologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0532G033',
                'ed94_c_descr' => 'Geoprocessamento - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0532M012',
                'ed94_c_descr' => 'Meteorologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0532O012',
                'ed94_c_descr' => 'Oceanografia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0533A012',
                'ed94_c_descr' => 'Astronomia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0533F012',
                'ed94_c_descr' => 'Física - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0533F022',
                'ed94_c_descr' => 'Física aplicada - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0533F024',
                'ed94_c_descr' => 'Física aplicada - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0533F032',
                'ed94_c_descr' => 'Física médica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0541M012',
                'ed94_c_descr' => 'Matemática - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0541M014',
                'ed94_c_descr' => 'Matemática - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0541M022',
                'ed94_c_descr' => 'Matemática aplicada e computacional - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0542C012',
                'ed94_c_descr' => 'Ciências atuariais - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0542E012',
                'ed94_c_descr' => 'Estatística - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0542E014',
                'ed94_c_descr' => 'Estatística - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0588P012',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo ciências naturais, matemática e estatística - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0588P013',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo ciências naturais, matemática e estatística  - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 5,
                'ed94_c_descrclasse' => 'Ciências naturais, matemática e estatística',
                'ed94_c_codigocenso' => '0588P014',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo ciências naturais, matemática e estatística  - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0612B013',
                'ed94_c_descr' => 'Banco de dados - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0612B014',
                'ed94_c_descr' => 'Banco de dados - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0612D013',
                'ed94_c_descr' => 'Defesa cibernética - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0612G013',
                'ed94_c_descr' => 'Gestão da tecnologia da informação - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0612G014',
                'ed94_c_descr' => 'Gestão da tecnologia da informação  - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0612R012',
                'ed94_c_descr' => 'Redes de computadores - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0612R013',
                'ed94_c_descr' => 'Redes de computadores - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0612R014',
                'ed94_c_descr' => 'Redes de computadores - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0613E012',
                'ed94_c_descr' => 'Engenharia de software - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0613E013',
                'ed94_c_descr' => 'Engenharia de software - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0613E014',
                'ed94_c_descr' => 'Engenharia de software - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0613J012',
                'ed94_c_descr' => 'Jogos digitais - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0613J013',
                'ed94_c_descr' => 'Jogos digitais - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0613J014',
                'ed94_c_descr' => 'Jogos digitais - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0614C012',
                'ed94_c_descr' => 'Ciência da computação - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0614C013',
                'ed94_c_descr' => 'Ciência da computação - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0614C014',
                'ed94_c_descr' => 'Ciência da computação - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0614I012',
                'ed94_c_descr' => 'Inteligência artificial - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0614I013',
                'ed94_c_descr' => 'Inteligência artificial - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0615S013',
                'ed94_c_descr' => 'Segurança da informação - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0615S014',
                'ed94_c_descr' => 'Segurança da informação - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0615S022',
                'ed94_c_descr' => 'Sistemas de informação - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0615S023',
                'ed94_c_descr' => 'Sistemas de informação - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0615S024',
                'ed94_c_descr' => 'Sistemas de informação - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0615S032',
                'ed94_c_descr' => 'Sistemas para internet - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0615S033',
                'ed94_c_descr' => 'Sistemas para internet - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0615S034',
                'ed94_c_descr' => 'Sistemas para internet - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0616E012',
                'ed94_c_descr' => 'Engenharia de computação (DCN Computação) - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0616I013',
                'ed94_c_descr' => 'Internet das coisas - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0616S013',
                'ed94_c_descr' => 'Sistemas embarcados - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0617A013',
                'ed94_c_descr' => 'Agrocomputação - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0617C012',
                'ed94_c_descr' => 'Ciência de dados - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0617C013',
                'ed94_c_descr' => 'Ciência de dados - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0617C022',
                'ed94_c_descr' => 'Computação e Tecnologias da Informação e Comunicação (TIC) em biociências e saúde - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0617C023',
                'ed94_c_descr' => 'Computação e Tecnologias da Informação e Comunicação (TIC) em biociências e saúde - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0617C032',
                'ed94_c_descr' => 'Criação digital - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0617C033',
                'ed94_c_descr' => 'Criação digital - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0688P012',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo computação e Tecnologias da Informação e Comunicação (TIC) - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 6,
                'ed94_c_descrclasse' => 'Computação e Tecnologias da Informação e Comunicação (TIC)',
                'ed94_c_codigocenso' => '0688P013',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo computação e Tecnologias da Informação e Comunicação (TIC) - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0710E012',
                'ed94_c_descr' => 'Engenharia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0711B013',
                'ed94_c_descr' => 'Biocombustíveis - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0711E012',
                'ed94_c_descr' => 'Engenharia bioquímica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0711E022',
                'ed94_c_descr' => 'Engenharia de bioprocessos - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0711E032',
                'ed94_c_descr' => 'Engenharia de biotecnologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0711E042',
                'ed94_c_descr' => 'Engenharia de nanotecnologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0711E052',
                'ed94_c_descr' => 'Engenharia química - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0712E012',
                'ed94_c_descr' => 'Engenharia ambiental - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0712E022',
                'ed94_c_descr' => 'Engenharia ambiental e sanitária - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0712G012',
                'ed94_c_descr' => 'Gestão ambiental - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0712G013',
                'ed94_c_descr' => 'Gestão ambiental - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0712G014',
                'ed94_c_descr' => 'Gestão ambiental - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0712G023',
                'ed94_c_descr' => 'Gestão de resíduos  - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0712G024',
                'ed94_c_descr' => 'Gestão de resíduos - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0712S013',
                'ed94_c_descr' => 'Saneamento ambiental  - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0713E013',
                'ed94_c_descr' => 'Eletrotécnica industrial  - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0713E023',
                'ed94_c_descr' => 'Energias renováveis  - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0713E032',
                'ed94_c_descr' => 'Engenharia bioenergética - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0713E042',
                'ed94_c_descr' => 'Engenharia de energia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0713E052',
                'ed94_c_descr' => 'Engenharia elétrica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0713E062',
                'ed94_c_descr' => 'Engenharia nuclear - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0713R013',
                'ed94_c_descr' => 'Refrigeração e climatização - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0713S012',
                'ed94_c_descr' => 'Sistemas elétricos - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0713S013',
                'ed94_c_descr' => 'Sistemas elétricos - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0713S014',
                'ed94_c_descr' => 'Sistemas elétricos - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714A013',
                'ed94_c_descr' => 'Automação industrial - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714A014',
                'ed94_c_descr' => 'Automação industrial - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714E013',
                'ed94_c_descr' => 'Eletrônica industrial - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714E022',
                'ed94_c_descr' => 'Engenharia acústica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714E032',
                'ed94_c_descr' => 'Engenharia biomédica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714E042',
                'ed94_c_descr' => 'Engenharia de computação (DCN Engenharia) - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714E052',
                'ed94_c_descr' => 'Engenharia de controle e automação - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714E062',
                'ed94_c_descr' => 'Engenharia de informação - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714E072',
                'ed94_c_descr' => 'Engenharia de telecomunicações - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714E082',
                'ed94_c_descr' => 'Engenharia eletrônica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714E092',
                'ed94_c_descr' => 'Engenharia mecatrônica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714G013',
                'ed94_c_descr' => 'Gestão de telecomunicações - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714G014',
                'ed94_c_descr' => 'Gestão de telecomunicações - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714M013',
                'ed94_c_descr' => 'Mecatrônica industrial - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714M014',
                'ed94_c_descr' => 'Mecatrônica industrial - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714R013',
                'ed94_c_descr' => 'Redes de telecomunicações - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714S013',
                'ed94_c_descr' => 'Sistemas biomédicos - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714S023',
                'ed94_c_descr' => 'Sistemas de telecomunicações - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714S024',
                'ed94_c_descr' => 'Sistemas de telecomunicações - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714T013',
                'ed94_c_descr' => 'Telemática - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0714T014',
                'ed94_c_descr' => 'Telemática - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0715E012',
                'ed94_c_descr' => 'Engenharia física - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0715E022',
                'ed94_c_descr' => 'Engenharia mecânica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0715E032',
                'ed94_c_descr' => 'Engenharia metalúrgica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0715F013',
                'ed94_c_descr' => 'Fabricação mecânica - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0715M013',
                'ed94_c_descr' => 'Manutenção industrial - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0715M014',
                'ed94_c_descr' => 'Manutenção industrial - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0715M023',
                'ed94_c_descr' => 'Mecânica de precisão - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0715P013',
                'ed94_c_descr' => 'Processos metalúrgicos - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0715P014',
                'ed94_c_descr' => 'Processos metalúrgicos - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0715S013',
                'ed94_c_descr' => 'Soldagem - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0715S014',
                'ed94_c_descr' => 'Soldagem - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0716A013',
                'ed94_c_descr' => 'Aeroespacial - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0716C013',
                'ed94_c_descr' => 'Construção naval - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0716E012',
                'ed94_c_descr' => 'Engenharia aeroespacial - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0716E022',
                'ed94_c_descr' => 'Engenharia aeronáutica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0716E023',
                'ed94_c_descr' => 'Engenharia aeronáutica - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0716E032',
                'ed94_c_descr' => 'Engenharia automotiva - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0716E042',
                'ed94_c_descr' => 'Engenharia ferroviária e metroviária - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0716E052',
                'ed94_c_descr' => 'Engenharia naval - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0716M013',
                'ed94_c_descr' => 'Manutenção de aeronaves - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0716M014',
                'ed94_c_descr' => 'Manutenção de aeronaves - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0716S013',
                'ed94_c_descr' => 'Sistemas automotivos - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0716S023',
                'ed94_c_descr' => 'Sistemas de navegação fluvial - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0721A012',
                'ed94_c_descr' => 'Alimentos - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0721A013',
                'ed94_c_descr' => 'Alimentos - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0721A014',
                'ed94_c_descr' => 'Alimentos - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0721E012',
                'ed94_c_descr' => 'Engenharia de alimentos - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0721L012',
                'ed94_c_descr' => 'Laticínios - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0721L013',
                'ed94_c_descr' => 'Laticínios - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0721P013',
                'ed94_c_descr' => 'Processamento de carnes - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0721P023',
                'ed94_c_descr' => 'Produção de cachaça - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0721P033',
                'ed94_c_descr' => 'Produção sucroalcooleira - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0721P034',
                'ed94_c_descr' => 'Produção sucroalcooleira - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0721P043',
                'ed94_c_descr' => 'Produção de cerveja - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0722C013',
                'ed94_c_descr' => 'Cerâmica - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0722C023',
                'ed94_c_descr' => 'Ciências dos materiais - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0722C024',
                'ed94_c_descr' => 'Ciências dos materiais - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0722E012',
                'ed94_c_descr' => 'Engenharia de materiais - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0722P013',
                'ed94_c_descr' => 'Papel e celulose - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0722P022',
                'ed94_c_descr' => 'Polímeros - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0722P023',
                'ed94_c_descr' => 'Polímeros - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0722P033',
                'ed94_c_descr' => 'Produção joalheira - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0722P043',
                'ed94_c_descr' => 'Produção moveleira - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0723E012',
                'ed94_c_descr' => 'Engenharia têxtil - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0723P013',
                'ed94_c_descr' => 'Produção de vestuário - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0723P022',
                'ed94_c_descr' => 'Produção têxtil - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0723P023',
                'ed94_c_descr' => 'Produção têxtil - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0724E012',
                'ed94_c_descr' => 'Engenharia de minas - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0724E022',
                'ed94_c_descr' => 'Engenharia de petróleo - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0724E032',
                'ed94_c_descr' => 'Engenharia geológica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0724M013',
                'ed94_c_descr' => 'Mineração - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0724P013',
                'ed94_c_descr' => 'Petróleo e gás - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0724R013',
                'ed94_c_descr' => 'Rochas ornamentais - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0725E012',
                'ed94_c_descr' => 'Engenharia de manufatura - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0725E013',
                'ed94_c_descr' => 'Engenharia de manufatura - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0725E022',
                'ed94_c_descr' => 'Engenharia de produção - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0725E032',
                'ed94_c_descr' => 'Engenharia industrial - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0725P013',
                'ed94_c_descr' => 'Produção gráfica - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0725P023',
                'ed94_c_descr' => 'Produção industrial - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0731A013',
                'ed94_c_descr' => 'Agrimensura - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0731A022',
                'ed94_c_descr' => 'Arquitetura e urbanismo - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0731A023',
                'ed94_c_descr' => 'Arquitetura e urbanismo - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0731A024',
                'ed94_c_descr' => 'Arquitetura e urbanismo - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0731E012',
                'ed94_c_descr' => 'Engenharia cartográfica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0731E022',
                'ed94_c_descr' => 'Engenharia de agrimensura - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0731E024',
                'ed94_c_descr' => 'Engenharia de agrimensura - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0731E032',
                'ed94_c_descr' => 'Engenharia de agrimensura e cartográfica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0732C013',
                'ed94_c_descr' => 'Construção de edifícios - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0732C023',
                'ed94_c_descr' => 'Controle de obras - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0732C024',
                'ed94_c_descr' => 'Controle de obras - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0732E012',
                'ed94_c_descr' => 'Engenharia civil - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0732E022',
                'ed94_c_descr' => 'Engenharia de recursos hídricos - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0732E032',
                'ed94_c_descr' => 'Engenharia de transportes - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0732E042',
                'ed94_c_descr' => 'Engenharia portuária - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0732E053',
                'ed94_c_descr' => 'Estradas - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0732G013',
                'ed94_c_descr' => 'Gestão de recursos hídricos - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0732M013',
                'ed94_c_descr' => 'Material de construção - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 7,
                'ed94_c_descrclasse' => 'Engenharia, produção e construção',
                'ed94_c_codigocenso' => '0788P012',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo engenharia, produção e construção - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811A012',
                'ed94_c_descr' => 'Agroecologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811A013',
                'ed94_c_descr' => 'Agroecologia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811A022',
                'ed94_c_descr' => 'Agroindústria - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811A023',
                'ed94_c_descr' => 'Agroindústria - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811A024',
                'ed94_c_descr' => 'Agroindústria - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811A032',
                'ed94_c_descr' => 'Agronegócio - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811A033',
                'ed94_c_descr' => 'Agronegócio - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811A042',
                'ed94_c_descr' => 'Agronomia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811A043',
                'ed94_c_descr' => 'Agronomia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811A053',
                'ed94_c_descr' => 'Agropecuária - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811C013',
                'ed94_c_descr' => 'Cafeicultura - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811E012',
                'ed94_c_descr' => 'Engenharia agrícola - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811E013',
                'ed94_c_descr' => 'Engenharia agrícola - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811E022',
                'ed94_c_descr' => 'Engenharia de biossistemas - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811F013',
                'ed94_c_descr' => 'Fruticultura - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811F014',
                'ed94_c_descr' => 'Fruticultura - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811I013',
                'ed94_c_descr' => 'Irrigação e drenagem - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811M013',
                'ed94_c_descr' => 'Manejo da produção agrícola - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811V012',
                'ed94_c_descr' => 'Viticultura e enologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811V013',
                'ed94_c_descr' => 'Viticultura e enologia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811Z012',
                'ed94_c_descr' => 'Zootecnia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811Z013',
                'ed94_c_descr' => 'Zootecnia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0811Z014',
                'ed94_c_descr' => 'Zootecnia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0812H013',
                'ed94_c_descr' => 'Horticultura - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0821E012',
                'ed94_c_descr' => 'Engenharia florestal - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0821E013',
                'ed94_c_descr' => 'Engenharia florestal - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0821S013',
                'ed94_c_descr' => 'Silvicultura - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0831A012',
                'ed94_c_descr' => 'Aquicultura - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0831A013',
                'ed94_c_descr' => 'Aquicultura - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0831A014',
                'ed94_c_descr' => 'Aquicultura - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0831E012',
                'ed94_c_descr' => 'Engenharia de pesca - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0831P013',
                'ed94_c_descr' => 'Produção pesqueira - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0841M012',
                'ed94_c_descr' => 'Medicina veterinária - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0841M013',
                'ed94_c_descr' => 'Medicina veterinária - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0841M014',
                'ed94_c_descr' => 'Medicina veterinária - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 8,
                'ed94_c_descrclasse' => 'Agricultura, silvicultura, pesca e veterinária',
                'ed94_c_codigocenso' => '0888P012',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo agricultura, silvicultura, pesca e veterinária - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0911O012',
                'ed94_c_descr' => 'Odontologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0911O014',
                'ed94_c_descr' => 'Odontologia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0912M012',
                'ed94_c_descr' => 'Medicina - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0912M013',
                'ed94_c_descr' => 'Medicina - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0912M014',
                'ed94_c_descr' => 'Medicina - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0913E012',
                'ed94_c_descr' => 'Enfermagem - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0913E014',
                'ed94_c_descr' => 'Enfermagem - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0914A013',
                'ed94_c_descr' => 'Análises clínicas e toxicológicas - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0914A014',
                'ed94_c_descr' => 'Análises clínicas e toxicológicas - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0914B012',
                'ed94_c_descr' => 'Biomedicina - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0914B014',
                'ed94_c_descr' => 'Biomedicina - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0914O013',
                'ed94_c_descr' => 'Oftálmica - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0914O022',
                'ed94_c_descr' => 'Optometria - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0914O023',
                'ed94_c_descr' => 'Optometria - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0914P013',
                'ed94_c_descr' => 'Prótese e órtese - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0914P014',
                'ed94_c_descr' => 'Prótese e órtese - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0914R013',
                'ed94_c_descr' => 'Radiologia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0914R014',
                'ed94_c_descr' => 'Radiologia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0915E012',
                'ed94_c_descr' => 'Educação física - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0915E014',
                'ed94_c_descr' => 'Educação física  - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0915F012',
                'ed94_c_descr' => 'Fisioterapia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0915F022',
                'ed94_c_descr' => 'Fonoaudiologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0915N012',
                'ed94_c_descr' => 'Nutrição - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0915N013',
                'ed94_c_descr' => 'Nutrição - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0915N014',
                'ed94_c_descr' => 'Nutrição - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0915P012',
                'ed94_c_descr' => 'Podologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0915P013',
                'ed94_c_descr' => 'Podologia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0915T012',
                'ed94_c_descr' => 'Terapia ocupacional - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0916F012',
                'ed94_c_descr' => 'Farmácia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0916F013',
                'ed94_c_descr' => 'Farmácia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0916F014',
                'ed94_c_descr' => 'Farmácia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0917M012',
                'ed94_c_descr' => 'Musicoterapia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0917P012',
                'ed94_c_descr' => 'Práticas integrativas - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0917P013',
                'ed94_c_descr' => 'Práticas integrativas - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0917P014',
                'ed94_c_descr' => 'Práticas integrativas - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0918S012',
                'ed94_c_descr' => 'Saúde coletiva - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0918S013',
                'ed94_c_descr' => 'Saúde coletiva - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0918S014',
                'ed94_c_descr' => 'Saúde coletiva - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0918S022',
                'ed94_c_descr' => 'Saúde pública - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0918S023',
                'ed94_c_descr' => 'Saúde pública - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0918S024',
                'ed94_c_descr' => 'Saúde pública - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0921A014',
                'ed94_c_descr' => 'Assistência a idosos e a deficientes - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0921G012',
                'ed94_c_descr' => 'Gerontologia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0921G013',
                'ed94_c_descr' => 'Gerontologia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0921G014',
                'ed94_c_descr' => 'Gerontologia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0923S012',
                'ed94_c_descr' => 'Serviço social - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0923S013',
                'ed94_c_descr' => 'Serviço social - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0923S014',
                'ed94_c_descr' => 'Serviço social - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0988P012',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo saúde e bem-estar - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0988P013',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo saúde e bem-estar - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 9,
                'ed94_c_descrclasse' => 'Saúde e bem-estar',
                'ed94_c_codigocenso' => '0988P014',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo saúde e bem-estar - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1011E012',
                'ed94_c_descr' => 'Economia doméstica - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1012E012',
                'ed94_c_descr' => 'Estética e cosmética - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1012E013',
                'ed94_c_descr' => 'Estética e cosmética - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1012E014',
                'ed94_c_descr' => 'Estética e cosmética - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1013G012',
                'ed94_c_descr' => 'Gastronomia - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1013G013',
                'ed94_c_descr' => 'Gastronomia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1013G014',
                'ed94_c_descr' => 'Gastronomia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1014F013',
                'ed94_c_descr' => 'Formação de técnicos e treinadores esportivos - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1014F014',
                'ed94_c_descr' => 'Formação de técnicos e treinadores esportivos - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1014G012',
                'ed94_c_descr' => 'Gestão desportiva e de lazer - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1014G013',
                'ed94_c_descr' => 'Gestão desportiva e de lazer - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1014G014',
                'ed94_c_descr' => 'Gestão desportiva e de lazer - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1015E013',
                'ed94_c_descr' => 'Eventos - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1015E014',
                'ed94_c_descr' => 'Eventos - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1015H012',
                'ed94_c_descr' => 'Hotelaria - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1015H013',
                'ed94_c_descr' => 'Hotelaria - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1015H014',
                'ed94_c_descr' => 'Hotelaria - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1015T012',
                'ed94_c_descr' => 'Turismo - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1015T013',
                'ed94_c_descr' => 'Turismo - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1015T014',
                'ed94_c_descr' => 'Turismo - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1022S012',
                'ed94_c_descr' => 'Segurança no trabalho - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1022S013',
                'ed94_c_descr' => 'Segurança no trabalho - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1022S014',
                'ed94_c_descr' => 'Segurança no trabalho - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1031C012',
                'ed94_c_descr' => 'Ciências militares - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1032I013',
                'ed94_c_descr' => 'Investigação e perícia - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1032I014',
                'ed94_c_descr' => 'Investigação e perícia - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1032S013',
                'ed94_c_descr' => 'Segurança no trânsito - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1032S014',
                'ed94_c_descr' => 'Segurança no trânsito - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1032S023',
                'ed94_c_descr' => 'Segurança privada - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1032S024',
                'ed94_c_descr' => 'Segurança privada - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1032S032',
                'ed94_c_descr' => 'Segurança pública - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1032S033',
                'ed94_c_descr' => 'Segurança pública - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1032S034',
                'ed94_c_descr' => 'Segurança pública - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1032S043',
                'ed94_c_descr' => 'Serviços penais - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1032S044',
                'ed94_c_descr' => 'Serviços penais - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1041C012',
                'ed94_c_descr' => 'Ciências aeronáuticas - Bacharelado',
                'ed94_i_grauacademico' => 2
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1041C013',
                'ed94_c_descr' => 'Ciências aeronáuticas - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1041C014',
                'ed94_c_descr' => 'Ciências aeronáuticas - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1041G013',
                'ed94_c_descr' => 'Gestão portuária - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1041G014',
                'ed94_c_descr' => 'Gestão portuária - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1041T013',
                'ed94_c_descr' => 'Transporte aéreo - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1041T023',
                'ed94_c_descr' => 'Transporte terrestre - Tecnológico',
                'ed94_i_grauacademico' => 1
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1041T024',
                'ed94_c_descr' => 'Transporte terrestre  - Sequencial',
                'ed94_i_grauacademico' => 4
            ],
            [
                'ed94_i_codclasse' => 10,
                'ed94_c_descrclasse' => 'Serviços',
                'ed94_c_codigocenso' => '1088P013',
                'ed94_c_descr' => 'Programas interdisciplinares abrangendo serviços',
                'ed94_i_grauacademico' => 1
            ]
        ];
        $codigos = [];
        foreach ($dados as $dado) {
            $codigos[] = $dado['ed94_c_codigocenso'];
            $existe = DB::table('cursoformacao')->where('ed94_c_codigocenso', $dado['ed94_c_codigocenso'])->get();
            if (count($existe) > 0) {
                DB::table('cursoformacao')->where('ed94_c_codigocenso', $dado['ed94_c_codigocenso'])->update($dado);
            } else {
                $dado['ed94_ativo'] = true;
                DB::table('cursoformacao')->insert($dado);
            }
        }

        $todosRegistro = DB::table('cursoformacao')->get();

        foreach ($todosRegistro as $registro) {
            if (!in_array($registro->ed94_c_codigocenso, $codigos)) {
                DB::table('cursoformacao')->where('ed94_i_codigo', $registro->ed94_i_codigo)->update([
                    'ed94_ativo' => false
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
