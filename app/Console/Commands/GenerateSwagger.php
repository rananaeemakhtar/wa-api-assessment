<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenApi\Generator;

class GenerateSwagger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Swagger documentation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating Swagger documentation...');

        $outputFile = public_path('swagger.json');

        $openapi = Generator::scan(['app']);
        file_put_contents($outputFile, $openapi->toJson());

        $this->info('Swagger documentation generated successfully at ' . $outputFile);
    }
}
