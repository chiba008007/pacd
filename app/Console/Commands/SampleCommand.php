<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = date("w");
        $id = "root";
        $dbname = "laravel";
        $pwd = "5i6G,Z..yxG~";
        $cmd = "/usr/bin/mysqldump -u ".$id." -p".$pwd." ".$dbname." > /var/tmp/backup/mysql_".$date.".dump";

        system($cmd);
/*
        shell_exec($cmd);
        print "dump complete";
*/
        echo $cmd;
        return 0;
    }
}
