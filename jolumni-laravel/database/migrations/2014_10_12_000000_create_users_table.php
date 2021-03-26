<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('user_id', 10)->primary();
            $table->string('nama_depan', 100);
            $table->string('nama_belakang', 100);
            $table->text('email')->unique();
            $table->string('password');
            $table->date('tgl_lahir');
            $table->year('tahun_masuk');
            $table->year('tahun_selesai');
            $table->enum('jurusan', ['TKJ', 'AKL', 'BDP']);
            $table->text('alamat');
            $table->string('no_telp', 20);
            $table->text('pekerjaan');
            $table->text('gambar');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
