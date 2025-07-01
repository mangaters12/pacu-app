<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleAndTokoIdToUsers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom role
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user');
            }

            // Menambahkan kolom toko_id tanpa foreign key
            if (!Schema::hasColumn('users', 'toko_id')) {
                $table->unsignedBigInteger('toko_id')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'toko_id')) {
                $table->dropColumn('toko_id');
            }
        });
    }
}
