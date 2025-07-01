<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKurirIdToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Periksa apakah kolom sudah ada, kalau belum tambahkan
            if (!Schema::hasColumn('orders', 'kurir_id')) {
                $table->unsignedBigInteger('kurir_id')->nullable()->after('status');
                // Jika ingin foreign key ke tabel user atau kurir, aktifkan baris berikut
                // $table->foreign('kurir_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'kurir_id')) {
                $table->dropColumn('kurir_id');
            }
        });
    }
}
