/**
 * Branding Horizon Finance para PDFs (mismo logo y colores del menú del sistema).
 */
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import moment from 'moment';

export const BRAND = {
    primary: [30, 64, 175],
    primaryDark: [30, 58, 138],
    horizonGreen: [5, 190, 80],
    slate900: [15, 23, 42],
    slate600: [71, 85, 105],
    slate400: [148, 163, 184],
    slate200: [226, 232, 240],
    slate100: [241, 245, 249],
    slate50: [248, 250, 252],
    white: [255, 255, 255],
    success: [5, 150, 105],
};

export const LOGO_DASHBOARD = '/images/logo_horizon.png';

export function money(value) {
    const n = parseFloat(value);
    if (Number.isNaN(n)) return '0.00';
    return n.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

export async function loadImageBase64(url = LOGO_DASHBOARD) {
    try {
        const res = await fetch(url, { cache: 'force-cache' });
        if (!res.ok) throw new Error('Imagen no encontrada: ' + url);
        const blob = await res.blob();
        const dataUrl = await new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        });

        const dims = await new Promise((resolve) => {
            const img = new Image();
            img.onload = () => resolve({ width: img.naturalWidth || 1, height: img.naturalHeight || 1 });
            img.onerror = () => resolve({ width: 100, height: 100 });
            img.src = dataUrl;
        });

        return { dataUrl, ...dims };
    } catch (e) {
        console.warn('No se pudo cargar logo', url, e);
        return null;
    }
}

/**
 * Cabecera Horizon: azul primary + logo + títulos.
 * @returns {number} y siguiente
 */
export async function drawHorizonHeader(doc, { title, subtitle, marginX = 14 } = {}) {
    const pageW = doc.internal.pageSize.getWidth();
    const headerH = 32;
    const logo = await loadImageBase64(LOGO_DASHBOARD);

    doc.setFillColor(...BRAND.primary);
    doc.rect(0, 0, pageW, headerH, 'F');
    doc.setFillColor(...BRAND.horizonGreen);
    doc.rect(0, headerH, pageW, 1.8, 'F');

    if (logo && logo.dataUrl) {
        try {
            const maxH = 16;
            const maxW = 68;
            const ratio = logo.width / logo.height;
            let logoH = maxH;
            let logoW = logoH * ratio;
            if (logoW > maxW) {
                logoW = maxW;
                logoH = logoW / ratio;
            }
            const logoY = (headerH - logoH) / 2;
            doc.addImage(logo.dataUrl, 'PNG', marginX, logoY, logoW, logoH);
        } catch (e) {
            doc.setTextColor(...BRAND.white);
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(15);
            doc.text('HORIZON', marginX, 19);
        }
    } else {
        doc.setTextColor(...BRAND.white);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(15);
        doc.text('HORIZON', marginX, 19);
    }

    doc.setTextColor(...BRAND.white);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(12);
    doc.text(title || 'HORIZON', pageW - marginX, 13, { align: 'right' });
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(9);
    doc.setTextColor(191, 219, 254);
    if (subtitle) {
        doc.text(subtitle, pageW - marginX, 19, { align: 'right' });
    }
    doc.setFontSize(8);
    doc.setTextColor(147, 197, 253);
    doc.text(moment().format('DD/MM/YYYY HH:mm'), pageW - marginX, 25, { align: 'right' });

    return headerH + 8;
}

export function drawHorizonFooter(doc, marginX = 14, note = 'Documento generado por HORIZON') {
    const pageW = doc.internal.pageSize.getWidth();
    const pageH = doc.internal.pageSize.getHeight();
    const footerY = pageH - 12;

    doc.setDrawColor(...BRAND.primary);
    doc.setLineWidth(0.4);
    doc.line(marginX, footerY - 4, pageW - marginX, footerY - 4);
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(7.5);
    doc.setTextColor(...BRAND.slate400);
    doc.text(note, marginX, footerY);
    doc.text(`Página ${doc.internal.getNumberOfPages()}`, pageW - marginX, footerY, { align: 'right' });
}

/**
 * HTML de cabecera para impresión DataTables.
 * Mismo fondo azul primary + franja verde que el PDF de simulación.
 * @param {string|null} logoDataUrl - logo en base64 (recomendado para que salga al imprimir)
 */
export function horizonPrintHeaderHtml({
    title,
    subtitle,
    clienteNombre,
    clienteDni,
    prestamoCode,
    extraLines = [],
    logoDataUrl = null,
} = {}) {
    const lines = extraLines.map((l) => `
        <div style="display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid #e2e8f0;font-size:11px;">
            <span style="color:#64748b;">${l.label}</span>
            <strong style="color:#0f172a;">${l.value}</strong>
        </div>
    `).join('');

    const logoSrc = logoDataUrl || LOGO_DASHBOARD;

    return `
    <style>
        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
        }
    </style>
    <div style="font-family:Helvetica,Arial,sans-serif;margin:0 0 16px 0;">
        <!-- Cabecera full-width: mismo azul del menú / simulación (#1e40af) -->
        <div style="
            background-color:#1e40af !important;
            background:#1e40af !important;
            color:#ffffff !important;
            padding:16px 18px;
            margin:0 -8px 0 -8px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            -webkit-print-color-adjust:exact;
            print-color-adjust:exact;
        ">
            <div style="display:flex;align-items:center;gap:10px;">
                <img src="${logoSrc}" alt="HORIZON" style="height:38px;max-width:170px;object-fit:contain;" />
            </div>
            <div style="text-align:right;color:#ffffff !important;">
                <div style="font-size:14px;font-weight:700;color:#ffffff !important;">${title || 'HORIZON'}</div>
                <div style="font-size:11px;color:#bfdbfe !important;">${subtitle || ''}</div>
                <div style="font-size:10px;color:#93c5fd !important;margin-top:2px;">${moment().format('DD/MM/YYYY HH:mm')}</div>
            </div>
        </div>
        <!-- Franja verde Horizon (#05be50) -->
        <div style="
            height:4px;
            background-color:#05be50 !important;
            background:#05be50 !important;
            margin:0 -8px 0 -8px;
            -webkit-print-color-adjust:exact;
            print-color-adjust:exact;
        "></div>
        <div style="
            border:1px solid #e2e8f0;
            border-top:0;
            border-radius:0 0 8px 8px;
            padding:12px 14px;
            background-color:#f8fafc !important;
            background:#f8fafc !important;
            -webkit-print-color-adjust:exact;
            print-color-adjust:exact;
        ">
            <div style="font-size:12px;font-weight:700;color:#1e40af;margin-bottom:8px;">Información del cliente / préstamo</div>
            ${clienteNombre ? `
            <div style="display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid #e2e8f0;font-size:11px;">
                <span style="color:#64748b;">Cliente</span>
                <strong>${clienteNombre}</strong>
            </div>` : ''}
            ${clienteDni ? `
            <div style="display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid #e2e8f0;font-size:11px;">
                <span style="color:#64748b;">DNI</span>
                <strong>${clienteDni}</strong>
            </div>` : ''}
            ${prestamoCode ? `
            <div style="display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid #e2e8f0;font-size:11px;">
                <span style="color:#64748b;">N° préstamo / solicitud</span>
                <strong>${prestamoCode}</strong>
            </div>` : ''}
            ${lines}
        </div>
    </div>
    `;
}

export { jsPDF };
