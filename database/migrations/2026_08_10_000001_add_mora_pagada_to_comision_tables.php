<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comision_detalle', function (Blueprint $table) {
            if (!Schema::hasColumn('comision_detalle', 'mora_pagada')) {
                $table->decimal('mora_pagada', 12, 2)->default(0)->after('interes_pagado');
            }
        });

        Schema::table('comision_periodo', function (Blueprint $table) {
            if (!Schema::hasColumn('comision_periodo', 'monto_mora_pagada')) {
                $table->decimal('monto_mora_pagada', 12, 2)->default(0)->after('monto_interes_pagado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comision_detalle', function (Blueprint $table) {
            if (Schema::hasColumn('comision_detalle', 'mora_pagada')) {
                $table->dropColumn('mora_pagada');
            }
        });

        Schema::table('comision_periodo', function (Blueprint $table) {
            if (Schema::hasColumn('comision_periodo', 'monto_mora_pagada')) {
                $table->dropColumn('monto_mora_pagada');
            }
        });
    }
};
