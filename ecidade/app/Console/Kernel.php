<?php

namespace App\Console;

use App\Console\Commands\ClearArquivosInfinity;
use App\Console\Commands\ReenvioLancamentosRequisicaoMaterial;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 *
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\AlteracaoSituacaoInscricaoTask::class,
        Commands\ControleParcelamentoVencidoTask::class,
        Commands\InativarVinculoProfessor::class,
        Commands\ClearPayrollData::class,
        Commands\GenerateDataPayrolls::class,
        Commands\AtualizacaoCadastroSimplesNacionalTask::class,
        Commands\RedesimRetrieveEstablishmentsTask::class,
        Commands\RedesimProcessRetrievedEstablishmentsTask::class,
        Commands\RedesimAutomaticEstablishmentProcessTask::class,
        Commands\PruneOldTokens::class,
        Commands\CacheMenu::class,
        Commands\NotificaAcordosVencimento::class,
        ReenvioLancamentosRequisicaoMaterial::class,
        Commands\ClearArquivosInfinity::class
    ];

    public function bootstrap()
    {
        // Don't forget to call parent bootstrap
        parent::bootstrap();

        // Do your own bootstrapping stuff here
    }

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('matriculaonline:alteracaoinscricao')->dailyAt('21:00');
         $schedule->command('tributario:parcelamentovencido')->hourly();
         $schedule->command('agendamento:inativarvinculoprofessor')->dailyAt('21:00');
         $schedule->command('tributario:atualizacaocadastrosimplesnacional')->hourly();
         $schedule->command("tributario:redesim:retrieveEstablishments")->cron("*/15 * * * *");
         $schedule->command("tributario:redesim:processRetrievedEstablishments")->cron("*/20 * * * *");
         $schedule->command("tributario:redesim:automaticEstablishmentProcess")->everyFiveMinutes();
         $schedule->command('dbseller:prune-tokens')->dailyAt('21:00');
         $schedule->command('dbseller:notifica-acordos-vencimento')->dailyAt('22:00');
         $schedule->command('saude:limparpastabackupinfinity')->weekly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
