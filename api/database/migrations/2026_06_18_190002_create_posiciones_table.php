<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posiciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique();      // 6G, 5G, 3G, 4F, 2G, 1G...
            $table->string('descripcion');
            $table->string('norma', 10)->nullable();     // null=universal, asme, aws, api
            $table->enum('tipo', ['ranura', 'filete', 'ambas'])->default('ambas');
            $table->boolean('es_tuberia')->default(false);
            $table->jsonb('califica_ranura');            // ["1G","2G","3G","4G","5G","6G"]
            $table->jsonb('califica_filete');            // ["1F","2F","3F","4F"]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posiciones');
    }
};
