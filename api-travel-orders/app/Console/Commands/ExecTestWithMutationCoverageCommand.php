<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExecTestWithMutationCoverageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mutation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa o teste de mutação com Infection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Executando os testes com cobertura em XML para mutação...');
        
        $exitCode = null;
        passthru('php artisan test --coverage --coverage-xml=tests/result/mutation/coverage-xml', $exitCode);
        if ($exitCode !== 0) {
            $this->error('Erro ao rodar os testes com cobertura. Execução interrompida.');
            return;
        }

        $this->info('Gerando test-results.junit...');
        passthru('php artisan test --log-junit=tests/result/mutation/coverage-xml/coverage-xmltest-results.junit.xml', $exitCode);
        if ($exitCode !== 0) {
            $this->error('Erro ao gerar o arquivo JUnit. Execução interrompida.');
            return;
        }

        $this->info('Executando os testes de mutação...');
        $command = 'vendor/bin/infection --coverage=tests/result/mutation/coverage-xml --threads=4';
        passthru($command, $exitCode);
        if ($exitCode !== 0) {
            $this->error('Erro ao rodar os testes de mutação. Execução interrompida.');
            return;
        }
    }
    
}
