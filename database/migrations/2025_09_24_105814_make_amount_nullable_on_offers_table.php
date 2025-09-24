<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE offers MODIFY amount DECIMAL(19,6) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE offers ALTER COLUMN amount DROP NOT NULL');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE offers ALTER COLUMN amount DECIMAL(19,6) NULL');
        } else {
            throw new RuntimeException("Driver no soportado: {$driver}");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE offers MODIFY amount DECIMAL(19,6) NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE offers ALTER COLUMN amount SET NOT NULL');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE offers ALTER COLUMN amount DECIMAL(19,6) NOT NULL');
        } else {
            throw new RuntimeException("Driver no soportado: {$driver}");
        }
    }
};