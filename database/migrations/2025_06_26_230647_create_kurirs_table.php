<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKurirsTable extends Migration
{
    public function up()
    {
        Schema::create('kurirs', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama kurir
            $table->string('email')->unique(); // Email kurir
            $table->string('phone'); // Nomor telepon
            $table->string('ktp_number')->unique(); // No KTP
            $table->string('plate_number'); // Plat nomor kendaraan
            $table->string('address'); // Alamat lengkap
            $table->string('vehicle_type'); // Jenis kendaraan
            $table->string('photo_path')->nullable(); // Foto kurir (path file)
            $table->string('driver_license_number')->nullable()->unique(); // No SIM / driver license
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kurirs');
    }
}
