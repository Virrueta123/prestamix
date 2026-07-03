<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sistema_config', function (Blueprint $table) {
            $table->id('sistema_config_id');
            $table->string('clave', 100)->unique();
            $table->string('valor', 255);
            $table->string('descripcion', 255)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        DB::table('sistema_config')->insert([
            'clave' => 'comision_interes_porcentaje',
            'valor' => '30',
            'descripcion' => 'Porcentaje de comisión del trabajador cobrador sobre el interés pagado en cuotas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sistema_config');
    }
};