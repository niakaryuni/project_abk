<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abk_id')->constrained('abks')->onDelete('cascade');
            $table->string('mata_pelajaran');
            $table->enum('aspek', ['motorik', 'keterampilan', 'sosial']);
            $table->integer('nilai')->nullable(); // 0-100
            $table->text('catatan')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};