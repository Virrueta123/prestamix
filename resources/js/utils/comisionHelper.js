/**
 * Helpers de comisión del cobrador y mora por cuota.
 *
 * Mora automática: (interés de la cuota / 30) * días de atraso (máx. 30).
 * La mora cobrada puede personalizarse (menor o igual a la calculada, o el monto que se decida).
 * Comisión del trabajador = % sobre (interés + mora cobrada), mostrada por separado.
 */

export function nombreCobradorDesdePrestamo(getCuota) {
    if (!getCuota) return 'Sin asignar';
    if (getCuota.cobrador_nombre) return getCuota.cobrador_nombre;
    if (typeof getCuota.analista === 'string' && getCuota.analista.trim()) return getCuota.analista.trim();
    if (getCuota.analista && typeof getCuota.analista === 'object') {
        return `${getCuota.analista.name || ''} ${getCuota.analista.lastname || ''}`.trim() || 'Sin asignar';
    }
    return 'Sin asignar';
}

/** Interés base de la cuota (sin mora). */
export function interesBaseCuota(pG) {
    if (!pG || pG.yes_interes === 'N') return 0;
    return redondear2(parseFloat(pG.interes || 0));
}

/**
 * Días de atraso (0 si no venció; tope 30 para el cálculo de mora).
 */
export function diasAtrasoCuota(pG, fechaRef = null) {
    if (!pG || !pG.fechaVencimiento) return 0;
    const venc = new Date(pG.fechaVencimiento);
    const hoy = fechaRef ? new Date(fechaRef) : new Date();
    venc.setHours(0, 0, 0, 0);
    hoy.setHours(0, 0, 0, 0);
    if (hoy <= venc) return 0;
    return Math.floor((hoy - venc) / (1000 * 60 * 60 * 24));
}

/**
 * Mora calculada automáticamente por el sistema.
 * Fórmula: (interés / 30) * min(días atraso, 30)
 * Solo aplica si yes_mora === 'Y' (o se fuerza) e interés > 0.
 */
export function moraCalculadaCuota(pG, opciones = {}) {
    if (!pG) return 0;
    const yesMora = opciones.forzarMora ? 'Y' : (pG.yes_mora || 'N');
    if (yesMora !== 'Y') return 0;

    const interes = parseFloat(pG.interes || 0);
    if (interes <= 0) return 0;

    const dias = diasAtrasoCuota(pG, opciones.fechaRef || null);
    if (dias <= 0) return 0;

    const diasCobrados = Math.min(30, dias);
    return redondear2((interes / 30) * diasCobrados);
}

/**
 * Mora que se va a cobrar en este pago.
 * Prioridad: monto_mora_cobrar (personalizado) → mora_calculada → recalcular.
 */
export function moraCobradaCuota(pG) {
    if (!pG || pG.yes_mora !== 'Y') return 0;

    if (pG.monto_mora_cobrar !== undefined && pG.monto_mora_cobrar !== null && pG.monto_mora_cobrar !== '') {
        const v = parseFloat(pG.monto_mora_cobrar);
        if (!Number.isNaN(v) && v >= 0) return redondear2(v);
    }

    if (pG.mora_calculada !== undefined && pG.mora_calculada !== null) {
        return redondear2(parseFloat(pG.mora_calculada) || 0);
    }

    return moraCalculadaCuota(pG);
}

/**
 * Inicializa campos de mora en un ítem de pago_grupal (mutación controlada).
 */
export function inicializarMoraCuota(pG) {
    if (!pG) return pG;
    const calc = moraCalculadaCuota(pG);
    pG.mora_calculada = calc;
    if (pG.monto_mora_cobrar === undefined || pG.monto_mora_cobrar === null || pG.monto_mora_cobrar === '') {
        pG.monto_mora_cobrar = calc;
    } else {
        pG.monto_mora_cobrar = redondear2(parseFloat(pG.monto_mora_cobrar) || 0);
    }
    return pG;
}

/**
 * Base para comisión: interés + mora cobrada (sin mezclar en un solo concepto).
 * @deprecated Preferir interesBase + moraCobrada por separado
 */
export function interesCuotaParaComision(pG) {
    return redondear2(interesBaseCuota(pG) + moraCobradaCuota(pG));
}

export function baseComisionCuota(pG) {
    return interesCuotaParaComision(pG);
}

export function comisionDesdeInteres(interes, porcentaje) {
    return redondear2((parseFloat(interes) || 0) * (parseFloat(porcentaje) / 100));
}

/** Comisión solo sobre el interés de la cuota. */
export function comisionDesdeInteresBase(pG, porcentaje) {
    return comisionDesdeInteres(interesBaseCuota(pG), porcentaje);
}

/** Comisión solo sobre la mora cobrada. */
export function comisionDesdeMora(pG, porcentaje) {
    return comisionDesdeInteres(moraCobradaCuota(pG), porcentaje);
}

/** Comisión total = % de (interés + mora). */
export function comisionTotalCuota(pG, porcentaje) {
    return redondear2(
        comisionDesdeInteresBase(pG, porcentaje) + comisionDesdeMora(pG, porcentaje)
    );
}

/** % que se queda la empresa: 100 - % comisión del cobrador. */
export function porcentajeEmpresa(porcentajeComision) {
    const p = parseFloat(porcentajeComision);
    if (Number.isNaN(p)) return 0;
    return redondear2(100 - p);
}

export function empresaDesdeInteres(interes, porcentajeComision) {
    const i = parseFloat(interes) || 0;
    const comision = comisionDesdeInteres(i, porcentajeComision);
    return redondear2(i - comision);
}

export function desgloseComisionCuota(pG, porcentaje) {
    const interes = interesBaseCuota(pG);
    const mora = moraCobradaCuota(pG);
    const comisionInteres = comisionDesdeInteres(interes, porcentaje);
    const comisionMora = comisionDesdeInteres(mora, porcentaje);
    return {
        interes,
        mora,
        base: redondear2(interes + mora),
        comisionInteres,
        comisionMora,
        comisionTotal: redondear2(comisionInteres + comisionMora),
        empresaInteres: redondear2(interes - comisionInteres),
        empresaMora: redondear2(mora - comisionMora),
        empresaTotal: redondear2(interes + mora - comisionInteres - comisionMora),
    };
}

function redondear2(n) {
    return Math.round((parseFloat(n) || 0) * 100) / 100;
}
