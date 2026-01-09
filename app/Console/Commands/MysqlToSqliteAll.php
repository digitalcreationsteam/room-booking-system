<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MysqlToSqliteAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:mysql-to-sqlite';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all MySQL tables to SQLite';

    public function handle()
    {
        $mysql = DB::connection('mysql');
        $sqlite = DB::connection('sqlite');

        // Disable foreign keys for speed
        $sqlite->statement('PRAGMA foreign_keys=OFF;');

        $tables = $mysql->select('SHOW TABLES');

        $dbName = config('database.connections.mysql.database');
        $key = 'Tables_in_' . $dbName;

        foreach ($tables as $tableObj) {
            $table = $tableObj->$key;
            $this->info("Migrating table: {$table}");

            $sqlite->table($table)->truncate();

            $rows = $mysql->table($table)->get();

            foreach ($rows as $row) {
                $sqlite->table($table)->insert((array) $row);
            }
        }

        // Enable foreign keys again
        $sqlite->statement('PRAGMA foreign_keys=ON;');

        $this->info('✅ MySQL → SQLite migration completed successfully!');
    }
}
