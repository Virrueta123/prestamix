<template>
    <ScrollPanel style="width: 100%; height: 400px">

        <div class="px-2 pt-2">
            <cobrador-comision-banner :get-cuota="get_cuota" :mostrar-resumen="false"></cobrador-comision-banner>
        </div>

        <div class="p-2">
            <div class="preview">
                <div class=" ">
                    <table class="table table-sm table-bordered" id="table_cronograma" style="font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">Periodo</th>
                                <th class="whitespace-nowrap">Fecha Vencimiento</th>
                                <th class="whitespace-nowrap">Saldo capital</th>
                                <th class="whitespace-nowrap">Amortizacion</th>
                                <th class="whitespace-nowrap">Interes</th>
                                <th class="whitespace-nowrap">Cuota</th>
                                <th class="whitespace-nowrap">Monto cancelado</th>
                                <th class="whitespace-nowrap">Saldo</th>
                                <th class="whitespace-nowrap">Mora</th>
                                <th class="whitespace-nowrap">Estado</th>
                                <th class="whitespace-nowrap">Cobrar mora</th>
                                <th class="whitespace-nowrap no_mostrar_print">Cobrar interes</th>
                                <th class="whitespace-nowrap">Pagar</th>
                                <th class="whitespace-nowrap">-</th>
                            </tr>
                        </thead>

                    </table>
                </div>
            </div>
        </div>
        <div class="text-primary font-semibold text-3xl">Ingresos</div>


        <table class="table table-sm table-bordered" style="font-size: 0.8rem;">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">Descripcion</th>
                    <th class="whitespace-nowrap">Monto</th>
                    <th class="whitespace-nowrap">cuenta</th>
                    <th class="whitespace-nowrap">Codigo</th>
                    <th class="whitespace-nowrap">fecha</th>
                    <th class="whitespace-nowrap">Oficina</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(g_i, index_g_i) in get_ingresos" :key="index_g_i">
                    <td>{{ g_i.descripcion }}</td>
                    <td>{{ g_i.monto }}</td>
                    <td><button type="button" name="" id="" class="btn btn-primary btn-xs btn-block">
                            <Icon icon='eye' class='' />
                        </button></td>
                    <td>{{ g_i.codigo }}</td>
                    <td>{{ formatear_fecha(g_i.created_at) }}</td>
                    <td v-if="g_i.yes_office == 'Y'">Si</td>
                    <td v-else>No</td>
                </tr>
                <tr v-if="get_ingresos.length == 0">
                    <td colspan="7" class="text-center">
                        <Icon icon='eye' class='mr-2' /> no hay datos que mostrar
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Cabecera de impresión se genera en JS con branding Horizon -->
        <div id="cronogram_print" style="display: none;"></div>

    </ScrollPanel>


    <Sidebar v-model:visible="is_opciones_modal_cronograma" header="Opciones para la cuota" position="bottom"
        style="height: auto">

        <div id="link-button" class="p-5">
            <button @click="is_opciones_modal_cronograma_pago = true;" class="btn btn-success text-white mr-1 p-4 mb-2">
                <Icon icon='coins' class='mr-2' /> Pagar cuota
            </button>

        </div>
    </Sidebar>

    <Sidebar v-model:visible="is_opciones_modal_cronograma_pago" :header="'Pagar cuota — Cobrador: ' + (get_cuota?.cobrador_nombre || get_cuota?.analista || 'Sin asignar')"
        position="bottom" style="height: auto">
        <pagar-cuota :get_cuota="get_cuota" :select_cronograma="select_cronograma"></pagar-cuota>
    </Sidebar>

    <Sidebar v-model:visible="is_opciones_modal_cronograma_pago_grupal" :header="'Pagar cuotas — Cobrador: ' + (get_cuota?.cobrador_nombre || get_cuota?.analista || 'Sin asignar')"
        position="top" style="height: auto">
        <pagar-cuota-grupal :get_cuota="get_cuota" :pago_grupal="pago_grupal"></pagar-cuota-grupal>
    </Sidebar>

    <button v-if="pago_grupal.length != 0" type="button" class="btn btn-primary btn-flotante" label="Image"
        @click="is_opciones_modal_cronograma_pago_grupal = true;">
        <Icon icon='edit' class='mr-2' /> Pagar ( {{ pago_grupal.length }} ) cuotas
    </button>
</template>

<script>
// mixin
import {
    myMixin
} from "../../mixin.js";
import TomSelect from 'tom-select';
import Axios from 'axios';

import moment from "moment";
import {
    BRAND,
    money,
    jsPDF,
    drawHorizonHeader,
    drawHorizonFooter,
    horizonPrintHeaderHtml,
    loadImageBase64,
} from '../../utils/horizonPdf.js';

export default {
    mixins: [myMixin],
    data() {
        return {
            get_cuota: this.$attrs.get_cuota,
            cronogramas: null,
            pago_grupal: [],
            contador: "disabled",
            tabla_cronograma: null,
            reprogramacion: false,
            select_cronograma: null,
            get_ingresos: [],
            is_opciones_modal_cronograma: false,
            is_opciones_modal_cronograma_pago: false,
            is_opciones_modal_cronograma_pago_grupal: false,
            exportandoPdf: false,
            logoDataUrl: null,
        };
    },
    computed: {
        clienteNombre() {
            const c = this.get_cuota?.solicitud?.cliente;
            if (!c) return this.get_cuota?.solicitud?.solicitud_nombre || '—';
            return `${c.cli_nombre || ''} ${c.cli_apellido || ''}`.trim() || '—';
        },
        clienteDni() {
            return this.get_cuota?.solicitud?.cliente?.cli_dni
                || this.get_cuota?.solicitud?.solicitud_documento
                || '—';
        },
        prestamoCode() {
            return this.get_cuota?.solicitud?.code
                || this.get_cuota?.code
                || '—';
        },
    },
    methods: {
        buildPrintHeaderHtml() {
            const p = this.get_cuota || {};
            return horizonPrintHeaderHtml({
                title: 'Estado del cronograma',
                subtitle: 'Cronograma de pagos con interés',
                clienteNombre: this.clienteNombre,
                clienteDni: this.clienteDni,
                prestamoCode: this.prestamoCode,
                logoDataUrl: this.logoDataUrl,
                extraLines: [
                    { label: 'Monto crédito', value: 'S/ ' + money(p.moto_credito) },
                    { label: 'Frecuencia', value: p.frecuencia_pagos || '—' },
                    { label: 'Tasa de interés', value: (p.interes != null ? p.interes + ' %' : '—') },
                    { label: 'Plazo', value: (p.intervalo != null ? p.intervalo + ' ' + (p.frecuencia_pagos || '') : '—') },
                ],
            });
        },

        estadoCuotaLabel(row) {
            if (row.yes_pago === 'Y' || row.status === 'A') return 'Pagado';
            const fechaDada = moment(row.fechaVencimiento);
            const hoy = moment();
            if (fechaDada.isBefore(hoy, 'day')) return 'Vencido';
            if (fechaDada.isSame(hoy, 'day')) return 'Pendiente / Hoy';
            return 'Pendiente';
        },

        interesMostrado(row) {
            if (row.yes_interes === 'N') return 0;
            return parseFloat(row.interes) || 0;
        },

        cuotaMostrada(row) {
            if (row.yes_interes === 'Y') return parseFloat(row.cuota) || 0;
            return parseFloat(row.amortizacion) || 0;
        },

        /**
         * PDF estado del cronograma (misma plantilla Horizon que simulación).
         */
        async exportarPDFCronograma() {
            const rows = Array.isArray(this.cronogramas) ? this.cronogramas : [];
            if (!rows.length) {
                this.alert_warning('No hay cuotas del cronograma para exportar.');
                return;
            }

            this.exportandoPdf = true;
            try {
                const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
                const pageW = doc.internal.pageSize.getWidth();
                const marginX = 12;
                const p = this.get_cuota || {};

                // Cabecera idéntica a simulación (azul primary full-width + franja verde)
                let y = await drawHorizonHeader(doc, {
                    title: 'Estado del cronograma',
                    subtitle: 'Cronograma de pagos con interés',
                    marginX,
                });

                // Cajas de resumen (mismo estilo que simulación)
                doc.setTextColor(...BRAND.slate900);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(12);
                doc.text('Resumen del préstamo', marginX, y);
                y += 4;

                const boxW = (pageW - marginX * 2 - 6) / 4;
                const boxH = 18;
                const resumenItems = [
                    { label: 'Monto crédito', value: 'S/ ' + money(p.moto_credito) },
                    { label: 'Plazo', value: `${p.intervalo ?? '—'} ${p.frecuencia_pagos || ''}`.trim() },
                    { label: 'Tasa de interés', value: p.interes != null ? `${p.interes} %` : '—' },
                    { label: 'N° préstamo', value: String(this.prestamoCode) },
                ];
                resumenItems.forEach((item, i) => {
                    const x = marginX + i * (boxW + 2);
                    doc.setFillColor(...BRAND.slate50);
                    doc.setDrawColor(...BRAND.slate100);
                    doc.roundedRect(x, y, boxW, boxH, 1.5, 1.5, 'FD');
                    doc.setTextColor(...BRAND.slate600);
                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(7);
                    doc.text(String(item.label).toUpperCase(), x + 3, y + 6);
                    doc.setTextColor(...BRAND.primary);
                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(9);
                    const val = String(item.value);
                    doc.text(val.length > 18 ? val.slice(0, 17) + '…' : val, x + 3, y + 13);
                });
                y += boxH + 5;

                // Banda datos cliente (fondo slate-50 como simulación)
                doc.setFillColor(...BRAND.slate50);
                doc.roundedRect(marginX, y, pageW - marginX * 2, 14, 1.5, 1.5, 'F');
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(8);
                doc.setTextColor(...BRAND.slate600);
                const metaY = y + 9;
                doc.text(`Cliente: ${this.clienteNombre}`, marginX + 4, metaY);
                doc.text(`DNI: ${this.clienteDni}`, marginX + 95, metaY);
                doc.setTextColor(...BRAND.primary);
                doc.setFont('helvetica', 'bold');
                doc.text(`Cobrador: ${p.cobrador_nombre || p.analista || '—'}`, pageW - marginX - 4, metaY, { align: 'right' });
                y += 18;

                // Totales
                let totalInteres = 0;
                let totalCuota = 0;
                let totalPagado = 0;
                rows.forEach((r) => {
                    totalInteres += this.interesMostrado(r);
                    totalCuota += this.cuotaMostrada(r);
                    totalPagado += parseFloat(r.pagado) || 0;
                });

                const head = [[
                    '#', 'Vencimiento', 'Saldo cap.', 'Amortiz.', 'Interés', 'Cuota', 'Pagado', 'Estado'
                ]];
                const body = rows.map((r) => [
                    String(r.periodo ?? ''),
                    String(r.fechaVencimiento ?? ''),
                    'S/ ' + money(r.saldoCapital),
                    'S/ ' + money(r.amortizacion),
                    'S/ ' + money(this.interesMostrado(r)),
                    'S/ ' + money(this.cuotaMostrada(r)),
                    'S/ ' + money(r.pagado),
                    this.estadoCuotaLabel(r),
                ]);

                body.push([
                    { content: 'TOTALES', colSpan: 4, styles: { halign: 'right', fontStyle: 'bold' } },
                    { content: 'S/ ' + money(totalInteres), styles: { fontStyle: 'bold' } },
                    { content: 'S/ ' + money(totalCuota), styles: { fontStyle: 'bold', textColor: BRAND.primary } },
                    { content: 'S/ ' + money(totalPagado), styles: { fontStyle: 'bold' } },
                    '',
                ]);

                doc.autoTable({
                    startY: y,
                    head,
                    body,
                    theme: 'grid',
                    margin: { left: marginX, right: marginX, bottom: 22 },
                    styles: {
                        font: 'helvetica',
                        fontSize: 7.5,
                        cellPadding: 1.8,
                        valign: 'middle',
                        textColor: BRAND.slate900,
                        lineColor: [226, 232, 240],
                        lineWidth: 0.2,
                    },
                    headStyles: {
                        fillColor: BRAND.primary,
                        textColor: BRAND.white,
                        fontStyle: 'bold',
                        fontSize: 7,
                        halign: 'center',
                    },
                    alternateRowStyles: { fillColor: BRAND.slate50 },
                    columnStyles: {
                        0: { halign: 'center', cellWidth: 10, fontStyle: 'bold' },
                        1: { halign: 'center', cellWidth: 24 },
                        2: { halign: 'right' },
                        3: { halign: 'right' },
                        4: { halign: 'right' },
                        5: { halign: 'right', fontStyle: 'bold', textColor: BRAND.primary },
                        6: { halign: 'right' },
                        7: { halign: 'center', cellWidth: 22 },
                    },
                    didParseCell: (data) => {
                        if (data.section === 'body' && data.row.index === body.length - 1) {
                            data.cell.styles.fillColor = [219, 234, 254];
                        }
                        // Colorear estado
                        if (data.section === 'body' && data.column.index === 7 && data.row.index < body.length - 1) {
                            const txt = String(data.cell.raw || '');
                            if (txt === 'Pagado') data.cell.styles.textColor = [5, 150, 105];
                            else if (txt === 'Vencido') data.cell.styles.textColor = [220, 38, 38];
                            else if (txt.includes('Hoy')) data.cell.styles.textColor = [217, 119, 6];
                        }
                    },
                    didDrawPage: () => {
                        drawHorizonFooter(
                            doc,
                            marginX,
                            'Estado del cronograma · HORIZON · Documento informativo'
                        );
                    },
                });

                const code = String(this.prestamoCode).replace(/[^\w-]/g, '');
                doc.save(`Cronograma-Estado-${code}-${moment().format('DD-MM-YYYY')}.pdf`);
                this.alert_success?.('PDF del estado del cronograma generado correctamente');
            } catch (err) {
                console.error(err);
                this.alert_error_modal?.('No se pudo generar el PDF del cronograma');
            } finally {
                this.exportandoPdf = false;
            }
        },

        // cargar todo los ingresos de este prestamo
        load_ingresos() {
            if (!this.get_cuota?.solicitud?.urlapi) {
                return Promise.resolve([]);
            }

            const data = {
                urlapi: this.get_cuota.solicitud.urlapi
            }

            const headers = this.headers;

            return Axios
                .post("/load_ingresos_prestamo", data, {
                    headers,
                })
                .then((response) => {
                    console.log("ingresos",response);
                    
                    if (response.data.success) {
                        // this.alert_success(response.data.message);
                        return response.data.data;
                        
                    } else {
                        this.alert_warning(response.data.message);
                    }
                })
                .catch((error) => {
                    this.alert_error_modal("Error en el servidor, recargue la pagina");
                    console.error(error);
                });
        },
        pagar_cuota() {

        },
        async check_interes(yes_interes, urlapi) {
            const data = {
                urlapi: urlapi,
                yes_interes: yes_interes
            };

            const headers = this.headers;

            this.loading_start();

            return Axios
                .put("/updated_yes_interes", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {
                        this.alert_success(response.data.message);
                        this.loading_end();
                        return response.data.data;
                    } else {
                        this.alert_warning(response.data.message);
                    }

                })
                .catch((error) => {

                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        },
        async check_mora(yes_mora, urlapi) {
            const data = {
                urlapi: urlapi,
                yes_mora: yes_mora
            };

            const headers = this.headers;

            this.loading_start();

            return Axios
                .put("/updated_yes_mora", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {

                        this.loading_end();
                        return response.data.data;
                    } else {
                        this.alert_warning(response.data.message);
                    }

                })
                .catch((error) => {

                    this.alert_error_modal("Error en el servidor");
                    console.error(error);
                });
        },
        load_cronogramas() {

            const data = {
                urlapi: this.get_cuota.urlapi
            }

            const headers = this.headers;

            return Axios
                .post("/get_cuotas", data, {
                    headers,
                })
                .then((response) => {

                    if (response.data.success) {
                        // this.alert_success(response.data.message);
                        return response.data.data;

                    } else {
                        this.alert_warning(response.data.message);
                    }

                })
                .catch((error) => {

                    this.alert_error_modal("Error en el servidor, recargue la pagina");
                    console.error(error);
                });
        },
        // esta funcion se encarga para mostrar los cronogramas en una tabla y lo cancelara siempre y cuando aun no este pagado
        cancelar_cronograma(prestamo_id, ncuota, periodo, cronograma_id) {
            const data = {
                prestamo_id: prestamo_id,
                ncuota: ncuota,
                periodo: periodo,
                cronograma_id: cronograma_id
            }

            const headers = this.headers;

            this.loading_start();

            return Axios
                .post("/cancelar_cronograma", data, {
                    headers,
                })
                .then((response) => {

                    if (response.data.success) {

                        window.location.reload();

                    } else {
                        this.alert_warning(response.data.message);
                    }
                    this.loading_end();
                })
                .catch((error) => {
                    this.loading_end();
                    this.alert_error_modal("Error en el servidor, recargue la pagina");
                });
        },
        load_cronogramas_datatable(result) {

            this.$nextTick(() => {
                if ($.fn.DataTable.isDataTable("#table_cronograma")) {
                    $("#table_cronograma").DataTable().destroy();
                }

                var self = this;
                this.tabla_cronograma = $('#table_cronograma').DataTable({
                    "columnDefs": [
                        {
                            "targets": 11,  // Índice de la columna donde quieres agregar la clase
                            "className": "no_mostrar_print"
                        }
                    ],
                    paging: false,
                    searching: false,
                    scrollCollapse: true,
                    scrollY: '280px',
                    processing: true,
                    "infoFiltered": "",
                    "processing": "<img src='~/Content/images/loadingNew.gif' />",
                    dom: 'Bfrtip',
                    autoWidth: true,
                    responsive: true,
                    data: result,
                    "ordering": true,
                    "language": this.spanish_datatable,
                    "buttons": [{
                        text: '<i class="fa fa-bars"></i> columnas visibles',
                        extend: 'colvis',
                    },
                    {
                        text: self.exportandoPdf
                            ? '<i class="fa fa-spinner fa-spin"></i> PDF...'
                            : '<i class="fa fa-file-pdf"></i> PDF cronograma',
                        action: function () {
                            self.exportarPDFCronograma();
                        },
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Imprimir',
                        title: '',
                        customize: function (win) {
                            // Fondo azul forzado (igual simulación) + logo en base64
                            const headerHtml = self.buildPrintHeaderHtml();
                            const $body = $(win.document.body);
                            $body
                                .css({
                                    'font-size': '10pt',
                                    'font-family': 'Helvetica, Arial, sans-serif',
                                    color: '#0f172a',
                                    margin: '0',
                                    padding: '8px',
                                })
                                .prepend(headerHtml);

                            // Estilos de impresión: conservar fondos de color
                            const style = win.document.createElement('style');
                            style.textContent = `
                                @media print {
                                    * {
                                        -webkit-print-color-adjust: exact !important;
                                        print-color-adjust: exact !important;
                                        color-adjust: exact !important;
                                    }
                                    thead th {
                                        background-color: #1e40af !important;
                                        color: #fff !important;
                                    }
                                }
                                thead th {
                                    background-color: #1e40af !important;
                                    color: #fff !important;
                                    -webkit-print-color-adjust: exact !important;
                                    print-color-adjust: exact !important;
                                }
                            `;
                            win.document.head.appendChild(style);

                            // Quitar columnas de acciones / checks (no imprimibles)
                            for (let i = 19; i >= 11; i--) {
                                $body.find('table thead th:nth-child(' + i + ')').remove();
                            }
                            $body.find('.no_mostrar_print').remove();
                            $body.find('table tbody tr').each(function () {
                                for (let i = 19; i >= 11; i--) {
                                    $(this).find('td:nth-child(' + i + ')').remove();
                                }
                            });

                            $body.find('table')
                                .addClass('compact')
                                .css({
                                    'font-size': '9pt',
                                    'width': '100%',
                                    'border-collapse': 'collapse',
                                });
                            $body.find('table thead th').css({
                                'background-color': '#1e40af',
                                'background': '#1e40af',
                                'color': '#fff',
                                'padding': '6px 4px',
                                'font-size': '8pt',
                                '-webkit-print-color-adjust': 'exact',
                                'print-color-adjust': 'exact',
                            });
                            $body.find('table tbody td').css({
                                'padding': '4px',
                                'border': '1px solid #e2e8f0',
                            });
                        },
                    },
                    {
                        text: '<i class="fa fa-calendar-days"></i> Reprogramacion', // Texto del botón extra
                        action: function (e, dt, node, config) {

                            if (self.user.rol == "gerente") {
                                window.location.href = `/reprogramacion/${self.get_cuota.solicitud.urlapi}`;
                            } else {
                                self.alert_warning("esta opcion solo es para los gerentes")
                            }
                        }
                    },
                    {
                        text: '<i class="fa fa-calendar-minus"></i> Reprogramacion Cuota', // Texto del botón extra
                        action: function (e, dt, node, config) {
                            if (self.user.rol == "gerente") {
                                window.location.href = `/reprogramacion_cuota/${self.get_cuota.solicitud.urlapi}`;
                            } else {
                                self.alert_warning("esta opcion solo es para los gerentes")
                            }
                        }
                    },
                    {
                        text: '<i class="fa-solid fa-eye"></i> Ver solicitud', // Texto del botón extra
                        action: function (e, dt, node, config) {

                            window.location.href = `/solicitud/${self.get_cuota.solicitud.urlapi}`;

                        }
                    },
                    {
                        text: '<i class="fa-solid fa-arrow-up-right-dots"></i> Ampliacion', // Texto del botón extra
                        action: function (e, dt, node, config) {
                            if (self.user.rol == "gerente") {
                                window.location.href = `/ampliacion/${self.get_cuota.solicitud.urlapi}`;
                            } else {
                                self.alert_warning("esta opcion solo es para los gerentes")
                            }
                        }
                    },
                    {
                        text: '<i class="fa-solid fa-xmarks-lines"></i> Paralelo', // Texto del botón extra
                        action: function (e, dt, node, config) { 
                            window.location.href = `/solicitud/create/${self.get_cuota.solicitud.urlapi}`; 
                        }
                    },
                    {
                        text: '<i class="fa-solid fa-xmarks-lines"></i> Cancelar prestamo', // Texto del botón extra
                        action: function (e, dt, node, config) { 
                            window.location.href = `/cancelar_prestamo/${self.get_cuota.solicitud.urlapi}`; 
                        }
                    },
                    {
                        text: '<i class="fa-solid fa-trash"></i> ', // Texto del botón extra
                        action: function (e, dt, node, config) {

                            if (self.user.rol == "gerente") {
                                window.location.href = `/desaparecer_prestamo/${self.get_cuota.solicitud.urlapi}`;
                            } else {
                                self.alert_warning("esta opcion solo es para los gerentes")
                            }
                        }
                    }

                    ],
                    columns: [
                        { "data": "periodo" },
                        {
                            "data": "fechaVencimiento",
                            render: function (data, type, row) {
                                if (row.yes_pago == "N") {
                                    const fechaDada = moment(data);
                                    const fechaActual = moment();

                                    if (fechaDada.isBefore(fechaActual, 'day')) {
                                        return `<div class="alert fila-roja  p-0 text-center text-white show "> ${data} </div>`;
                                    } else if (fechaDada.isSame(fechaActual, 'day')) {
                                        return `<div class="alert  fila-amarilla  p-0 text-center text-white show "> ${data} </div>`;

                                    } else {
                                        return `<div class="alert fila-pendiente  p-0 text-center text-white show "> ${data} </div>`;

                                    }

                                } else {
                                    return `<div class="alert fila-pagada  p-0 text-center text-white show "> ${data} </div>`;

                                }
                            }
                        },
                        { "data": "saldoCapital" },
                        { "data": "amortizacion" },
                        {
                            "data": "interes",
                            render: function (data, type, row) {

                                if (row.yes_interes == "N") {
                                    row.cuota = row.amortizacion;
                                    return `<div "> 0 </div>`;
                                } else {
                                    return `<div "> ${data} </div>`;
                                }

                            }
                        },
                        {
                            "data": "cuota",
                            render: function (data, type, row) {
                                if (row.yes_interes == "Y") {
                                    return `${data}`;
                                } else {
                                    return `${row.amortizacion}`;
                                }
                            }
                        },
                        {
                            "data": "cuota",
                            render: function (data, type, row) {
                                return row.pagado;
                            }
                        },
                        {
                            "data": "cuota",
                            render: function (data, type, row) {

                                let mora = 0;
                                if (row.yes_pago == "N") {

                                    switch (row.yes_mora) {
                                        case "N":
                                            const fechaDada = moment(row.fechaVencimiento);
                                            const fechaActual = moment();
                                            // Comparar la fecha actual con la fecha dada 
                                            if (fechaDada.isBefore(fechaActual, 'day')) {
                                                var diferencia = fechaActual.diff(fechaDada, 'days');

                                                self.check_mora("Y", row.urlapi);
                                                row.yes_mora = "Y";
                                                row.monto_mora = row.interes;

                                                var interes_by_days = row.interes / 30;

                                                var interes_cuota_actual = 0;

                                                if (diferencia >= 30) {
                                                    interes_cuota_actual = interes_by_days * 30;
                                                } else {
                                                    interes_cuota_actual = interes_by_days * diferencia;
                                                }

                                                mora = self.redondear(interes_cuota_actual);

                                            } else if (fechaDada.isSame(fechaActual, 'day')) {
                                                mora = 0;
                                            } else {
                                                mora = 0;
                                            }
                                            break;

                                        case "Y":
                                            const fecha_vencimiento = moment(row.fechaVencimiento);
                                            const fecha_actual = moment();

                                            var diferencia = fecha_actual.diff(fecha_vencimiento, 'days');

                                            var interes_by_days = row.interes / 30;

                                            var interes_cuota_actual = 0;

                                            if (diferencia >= 30) {
                                                interes_cuota_actual = interes_by_days * 30;
                                            } else {
                                                interes_cuota_actual = interes_by_days * diferencia;
                                            }

                                            mora = self.redondear(interes_cuota_actual);
                                            break;

                                        case "S":
                                        mora = 0;
                                            break;
                                    }
                                } else {
                                    return `<div "> Pagado </div>`;
                                }


                                let cuota = Number(row.cuota) || 0; // Si row.cuota es null o undefined, asignar 0
                                let montoMora = Number(mora) || 0;
                                let pagado = Number(row.pagado) || 0;

                                if (row.yes_mora == "Y") {

                                    return self.redondear((cuota + montoMora) - pagado);

                                } else {
                                    let monto = cuota - pagado;
                                    if (monto < 0) {
                                        return 0;
                                    } else {
                                        return self.redondear(monto);
                                    }

                                }

                            }
                        },
                        {
                            "data": "monto_mora",
                            render: function (data, type, row) {

                                if (row.yes_pago == "N") {

                                    switch (row.yes_mora) {
                                        case "N":
                                            const fechaDada = moment(row.fechaVencimiento);
                                            const fechaActual = moment();
                                            // Comparar la fecha actual con la fecha dada 
                                            if (fechaDada.isBefore(fechaActual, 'day')) {
                                                var diferencia = fechaActual.diff(fechaDada, 'days');

                                                self.check_mora("Y", row.urlapi);
                                                row.yes_mora = "Y";
                                                row.monto_mora = row.interes;

                                                var interes_by_days = row.interes / 30;

                                                var interes_cuota_actual = 0;

                                                if (diferencia >= 30) {
                                                    interes_cuota_actual = interes_by_days * 30;
                                                } else {
                                                    interes_cuota_actual = interes_by_days * diferencia;
                                                }

                                                return `<div "> ${self.redondear(interes_cuota_actual)} </div>`;

                                            } else if (fechaDada.isSame(fechaActual, 'day')) {
                                                return `<div "> NO </div>`;
                                            } else {
                                                return `<div "> NO </div>`;
                                            }
                                            break;

                                        case "Y":
                                            const fecha_vencimiento = moment(row.fechaVencimiento);
                                            const fecha_actual = moment();

                                            var diferencia = fecha_actual.diff(fecha_vencimiento, 'days');

                                            var interes_by_days = row.interes / 30;

                                            var interes_cuota_actual = 0;

                                            if (diferencia >= 30) {
                                                interes_cuota_actual = interes_by_days * 30;
                                            } else {
                                                interes_cuota_actual = interes_by_days * diferencia;
                                            }

                                            return `<div "> ${self.redondear(interes_cuota_actual)} </div>`;
                                            break;

                                        case "S":
                                            return `<div "> 0 </div>`;
                                            break;
                                    }
                                } else {
                                    return `<div "> Pagado </div>`;
                                }
                            }
                        },
                        {
                            "data": "status",
                            render: function (data, type, row) {

                                if (row.yes_pago == "Y") {
                                    return "Pagado";
                                }
                                const fechaDada = moment(row.fechaVencimiento);
                                const fechaActual = moment();
                                // Comparar la fecha actual con la fecha dada 
                                switch (data) {
                                    case "A":
                                        return `<div "> Pagado </div>`;
                                        break;
                                    case "P":
                                        if (fechaDada.isBefore(fechaActual, 'day')) {
                                            return `<div ">  Vencido </div>`;
                                        } else if (fechaDada.isSame(fechaActual, 'day')) {

                                            return `<div "> Pendiente / Hoy </div>`;
                                        } else {
                                            return `<div "> Pendiente </div>`;
                                        }

                                        break;
                                    case "N":
                                        if (fechaDada.isBefore(fechaActual, 'day')) {
                                            return `<div ">  Vencido </div>`;
                                        } else if (fechaDada.isSame(fechaActual, 'day')) {

                                            return `<div "> Pendiente / Hoy </div>`;
                                        } else {
                                            return `<div "> Pendiente </div>`;
                                        }
                                        break;
                                }
                            }
                        },

                        {
                            data: 'yes_mora',
                            render: function (data, type, row, meta) {
                                // Crea una casilla de verificación con el ID de la fila
                                if (row.yes_pago == "N") {

                                    switch (row.yes_mora) {
                                        case "N":
                                            const fechaDada = moment(row.fechaVencimiento);
                                            const fechaActual = moment();
                                            // Comparar la fecha actual con la fecha dada 
                                            if (fechaDada.isBefore(fechaActual, 'day')) {

                                                return `<div class="form-check form-switch">

                                                        <input  class="form-check-input chk-mora" checked index="${meta.row}" type="checkbox">

                                                     </div> `;

                                            } else if (fechaDada.isSame(fechaActual, 'day')) {
                                                return `<div "> NO </div>`;
                                            } else {
                                                return `<div "> NO </div>`;
                                            }

                                            break;

                                        case "Y":
                                            return `<div class="form-check form-switch">

                                                        <input  class="form-check-input chk-mora" checked index="${meta.row}" type="checkbox">

                                                        </div> `;
                                            break;

                                        case "S":
                                            return ` <div class="form-check form-switch">

                                                    <input  class="form-check-input chk-mora" index="${meta.row}" type="checkbox">

                                                    </div> `;
                                            break;
                                    }


                                } else {
                                    return "Pagado"
                                }

                            }
                        },
                        {
                            data: 'yes_interes',
                            render: function (data, type, row, meta) {
                                // Crea una casilla de verificación con el ID de la fila
                                if (row.yes_pago == "N") {
                                    var yes_interes = row.yes_interes == "Y" ? "checked" : "";

                                    return ` <div class="form-check form-switch no_mostrar_print">

                                                <input  ${yes_interes} class="form-check-input chk-select" index="${meta.row}" type="checkbox">

                                            </div> `;
                                } else {
                                    return "<div class='no_mostrar_print'> Pagado </div>"
                                }

                            }
                        },

                        {
                            data: 'yes_pago',
                            render: function (data, type, row, meta) {

                                if (row.urlapi == self.get_cuota.cuota_actual.urlapi) {
                                    return `<div class="form-check form-switch"> <input class="form-check-input chk-pago " index="${meta.row}" type="checkbox"> </div> `;
                                } else if (row.yes_pago == "N") {
                                    return "<div class=''>Pendiente</div>"; // or return some default value
                                } else {
                                    return "<div class=''>Pagado</div>";
                                }
                            }
                        },
                        {
                            data: 'yes_pago',
                            render: function (data, type, row, meta) {

                                if (data == "Y") {
                                   if(row.yes_reprogramacion == "Y"){
                                        return `<div class="text-success">Pagado (Reprogramación) </div>`;
                                    } else {
                                        return `<div class="text-success">Pagado</div>`;
                                    }
                                   
                                } else {
                                    return `<button class="btn btn-primary btn-sm click-cancelar" index="${meta.row}">Cancelar</button>`;
                                }
                            }
                        }

                    ],
                })

                //funciones para pagar varias cuotas
                $('#table_cronograma').on('change', '.chk-pago', function () {
                    const index = $(this).attr('index'); // Obtén el ID de la fila
                    const isChecked = $(this).prop('checked'); // Verifica si está marcado o no

                    var data = self.tabla_cronograma.row(index).data();

                    if (isChecked) {
                        self.pago_grupal.push(data)
                    } else {

                        let index = self.pago_grupal.findIndex(item => item.urlapi === data.urlapi);

                        // Si el elemento existe, elimínalo
                        if (index !== -1) {
                            self.pago_grupal.splice(index, 1);
                        }
                    }
                });

                //evento para check select  mora
                $('#table_cronograma').on('change', '.chk-mora', function () {
                    const index = $(this).attr('index'); // Obtén el ID de la fila
                    const isChecked = $(this).prop('checked'); // Verifica si está marcado o no 
                    var data = self.tabla_cronograma.row(index).data();

                    if (isChecked) {
                        self.check_mora("Y", data.urlapi).then((response) => {
                            var rowData = self.tabla_cronograma.row(index).data(); // Datos de la fila seleccionada 

                            rowData.monto_mora = response.interes;
                            rowData.yes_mora = response.yes_mora;
                            self.tabla_cronograma.row(index).data(rowData)
                            self.loading_end();
                        }).catch((err) => {
                            console.log(err);
                        });
                    } else {
                        self.check_mora("S", data.urlapi).then((response) => {

                            var rowData = self.tabla_cronograma.row(index).data(); // Datos de la fila seleccionada

                            rowData.monto_mora = 0;
                            rowData.yes_mora = response.yes_mora;

                            self.tabla_cronograma.row(index).data(rowData)
                            self.loading_end(); // Cambia el valor de la columna "Nombre" 
                        }).catch((err) => {
                            console.log(err);
                        });
                    }
                });

                // evento click para cancelar un cronograma
                $('#table_cronograma').on('click', '.click-cancelar', async function () {
                    const index = $(this).attr('index'); // Obtén el ID de la fila 
                    var data = self.tabla_cronograma.row(index).data();
  
                    await self.$swal.fire({
                        title: "Estas seguro que desea cancelar este cronograma?",
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: "Si, estoy seguro",
                        denyButtonText: `Cancelar`
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            self.cancelar_cronograma(
                                self.get_cuota.urlapi,
                                self.get_cuota.ncuotas,
                                data.periodo,
                                data.urlapi
                            );
                        }
                    });

                });

                //evento para check select no interes
                $('#table_cronograma').on('change', '.chk-select', function () {
                    const index = $(this).attr('index'); // Obtén el ID de la fila
                    const isChecked = $(this).prop('checked'); // Verifica si está marcado o no 
                    var data = self.tabla_cronograma.row(index).data();

                    if (isChecked) {
                        self.check_interes("Y", data.urlapi).then((response) => {
                            var rowData = self.tabla_cronograma.row(index).data(); // Datos de la fila seleccionada 
                            rowData.yes_interes = 'Y';
                            rowData.cuota = response.cuota;
                            rowData.interes = response.interes;
                            self.tabla_cronograma.row(index).data(rowData)
                            self.loading_end();
                        }).catch((err) => {
                            console.log(err);
                        });
                    } else {
                        self.check_interes("N", data.urlapi).then((response) => {

                            var rowData = self.tabla_cronograma.row(index).data(); // Datos de la fila seleccionada
                            rowData.cuota = response.amortizacion;
                            rowData.interes = 0;
                            rowData.yes_interes = 'N';

                            self.tabla_cronograma.row(index).data(rowData)
                            self.loading_end(); // Cambia el valor de la columna "Nombre" 
                        }).catch((err) => {
                            console.log(err);
                        });
                    }
                });

                // $('#table_cronograma tbody').on('dblclick', 'tr', function () {
                //     var data = self.tabla_cronograma.row(this).data();
                //     self.select_cronograma = data;
                //     self.is_opciones_modal_cronograma = true;
                // });

            });

        }
    },
    mounted() {
        // Precargar logo Horizon para impresión (fondo azul + logo blanco)
        loadImageBase64().then((logo) => {
            if (logo?.dataUrl) this.logoDataUrl = logo.dataUrl;
        }).catch(() => {});

        this.loading_start('Cargando cronograma...');

        Promise.all([
            this.load_ingresos(),
            this.load_cronogramas(),
        ]).then(([ingresos, cronogramas]) => {
            this.get_ingresos = ingresos || [];
            const filas = Array.isArray(cronogramas) ? cronogramas : [];
            this.cronogramas = filas;
            this.load_cronogramas_datatable(filas);

            const ultimoPeriodo = filas[filas.length - 1];
            if (ultimoPeriodo?.fechaVencimiento) {
                const fechaDada = moment(ultimoPeriodo.fechaVencimiento);
                const fechaActual = moment();
                if (fechaDada.isBefore(fechaActual, 'day')) {
                    this.reprogramacion = true;
                }
            }
        }).catch((err) => {
            console.error(err);
            this.alert_error_modal('No se pudo cargar el cronograma del préstamo.');
        }).finally(() => {
            this.loading_end(true);
        });
    }
}
</script>

<style></style>