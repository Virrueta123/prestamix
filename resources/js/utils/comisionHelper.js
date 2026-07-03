export function nombreCobradorDesdePrestamo(getCuota) {
    if (!getCuota) return 'Sin asignar';
    if (getCuota.cobrador_nombre) return getCuota.cobrador_nombre;
    if (typeof getCuota.analista === 'string' && getCuota.analista.trim()) return getCuota.analista.trim();
    if (getCuota.analista && typeof getCuota.analista === 'object') {
        return `${getCuota.analista.name || ''} ${getCuota.analista.lastname || ''}`.trim() || 'Sin asignar';
    }
    return 'Sin asignar';
}

export function interesCuotaParaComision(pG) {
    if (!pG || pG.yes_interes === 'N') return 0;
    let interes = parseFloat(pG.interes || 0);
    if (pG.yes_mora === 'Y' && interes > 0 && pG.fechaVencimiento) {
        const venc = new Date(pG.fechaVencimiento);
        const hoy = new Date();
        venc.setHours(0, 0, 0, 0);
        hoy.setHours(0, 0, 0, 0);
        if (hoy > venc) {
            const dias = Math.min(30, Math.floor((hoy - venc) / (1000 * 60 * 60 * 24)));
            interes += (parseFloat(pG.interes) / 30) * dias;
        }
    }
    return Math.round(interes * 100) / 100;
}

export function comisionDesdeInteres(interes, porcentaje) {
    return Math.round(interes * (parseFloat(porcentaje) / 100) * 100) / 100;
}