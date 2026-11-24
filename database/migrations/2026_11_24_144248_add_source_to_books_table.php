<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds "source" (who added the book: the library admins or a user), kept apart from
 * category_id (what the book is about). Existing books are backfilled from user_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('source', 10)->default('admin')->after('status')->index();
        });

        DB::table('books')->whereNotNull('user_id')->update(['source' => 'user']);
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropColumn('source');
        });
    }
};
