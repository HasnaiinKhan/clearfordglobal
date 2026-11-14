<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateClearfordDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature   = 'db:create-clearford';

    /**
     * The console Create the clearford MySQL database and run all migrations.
     *
     * @var string
     */
    protected $description = 'Create the clearford MySQL database and run all migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
