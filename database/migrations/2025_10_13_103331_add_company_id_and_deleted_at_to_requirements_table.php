<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Ensure column exists (nullable)
        if (!Schema::hasColumn('requirements', 'company_id')) {
            Schema::table('requirements', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('requirement_id');
            });
        }

        // 2) Ensure there is at least one company
        $defaultCompanyId = DB::table('companies')->value('company_id');
        if (!$defaultCompanyId) {
            $defaultCompanyId = DB::table('companies')->insertGetId([
                'company_name'    => 'Default Company',
                'company_address' => null,
                'company_email'   => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }

        // 3) Sanitize invalid company_id values
        //    - Set to NULL if not present in companies
        DB::statement("
            UPDATE requirements r
            LEFT JOIN companies c ON c.company_id = r.company_id
            SET r.company_id = NULL
            WHERE r.company_id IS NOT NULL AND c.company_id IS NULL
        ");

        // 4) Backfill NULLs with a valid company id
        DB::table('requirements')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);

        // 5) Add soft deletes + index if missing
        if (!Schema::hasColumn('requirements', 'deleted_at')) {
            Schema::table('requirements', function (Blueprint $table) {
                $table->softDeletes()->after('updated_at');
            });
        }
        $hasDeletedAtIndex = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'requirements')
            ->where('INDEX_NAME', 'requirements_deleted_at_index')
            ->exists();
        if (!$hasDeletedAtIndex) {
            Schema::table('requirements', function (Blueprint $table) {
                $table->index('deleted_at', 'requirements_deleted_at_index');
            });
        }

        // 6) Finally add FK if missing
        $fkExists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'requirements')
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->where('CONSTRAINT_NAME', 'requirements_company_id_foreign')
            ->exists();

        if (!$fkExists) {
            Schema::table('requirements', function (Blueprint $table) {
                $table->foreign('company_id', 'requirements_company_id_foreign')
                      ->references('company_id')->on('companies')
                      ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        // Drop FK if exists
        $fkExists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'requirements')
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->where('CONSTRAINT_NAME', 'requirements_company_id_foreign')
            ->exists();

        Schema::table('requirements', function (Blueprint $table) use ($fkExists) {
            if ($fkExists) {
                $table->dropForeign('requirements_company_id_foreign');
            }
        });

        // Drop index if exists
        $hasDeletedAtIndex = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', 'requirements')
            ->where('INDEX_NAME', 'requirements_deleted_at_index')
            ->exists();
        Schema::table('requirements', function (Blueprint $table) use ($hasDeletedAtIndex) {
            if ($hasDeletedAtIndex) {
                $table->dropIndex('requirements_deleted_at_index');
            }
        });

        // Drop columns if exist
        Schema::table('requirements', function (Blueprint $table) {
            if (Schema::hasColumn('requirements', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
            if (Schema::hasColumn('requirements', 'company_id')) {
                $table->dropColumn('company_id');
            }
        });
    }
};
