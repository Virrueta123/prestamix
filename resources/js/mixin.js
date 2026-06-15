import {
    message
} from "laravel-mix/src/Log";
import $ from 'jquery';
import "jquery-validation";
import "jquery-validation/dist/localization/messages_es"
import Axios from "axios";
import moment from 'moment';
import numeral from 'numeral';
import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';

export const myMixin = {
    data() {
        return {
            user: null,
            analistas: [],
            headers: {
                "Content-Type": "application/json",
            },
            spanish_datatable: {
                "sProcessing": "",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "searchPlaceholder": "Escribe aquí para buscar..",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "cargando",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            }
        }
    },
    created() {
        this.cargar_analistas();

        this.cargar_usuario().then((result) => {
            this.user = result;
        }).catch((err) => {

        });
    },
    methods: {
        // esta funcion srive para ver si el fecha de hoy dia
        is_now(date){
            return moment(date).isSame(moment(), 'day');
        },
        redondear(numero) {
            var redondeado = Math.round(numero * 10) / 10; // Redondea al número más cercano con un decimal

            if (redondeado % 1 === 0) {
                return redondeado.toFixed(2); // Si el número redondeado es un entero, devolverlo con dos decimales
            } else {
                return redondeado; // Si el número redondeado tiene decimales, devolverlo tal cual
            }
        },
        redondearNumero(monto) {
            // Dividimos la cadena por las comas y unimos las partes para eliminarlas
            monto = monto.split(',').join('');

            // Convertimos el monto a un número con dos decimales
            monto = parseFloat(monto).toFixed(2);

            // Separamos los decimales del entero
            let partes = monto.split('.');

            // Verificamos si el primer decimal es diferente de 0
            if (partes[1].charAt(0) !== '0') {
                // Redondeamos al entero más cercano
                return Math.round(parseFloat(monto));
            }

            // Si el primer decimal es 0, devolvemos el monto con un decimal
            return parseFloat(monto).toFixed(1);
        },
        // tom select simple
        select_tom_select(id) {
            new TomSelect("#" + id, {
                options: this.analistas,
                maxItems: 3,
                valueField: 'urlapi',
                labelField: 'name',
                searchField: 'name',
            });
        },
        // formatear dinero
        formatear_dinero_soles(monto) {
            return `${numeral(monto).format('0,0.00')}`;
        },
        fecha_actual() {
            return moment();
        },
        formatear_dinero(monto) {
            return numeral(monto).format('0,0.00');
        },
        formatear_dinero_sin_coma(monto) {
            return numeral(monto).format('00.00');
        },
        // nomenclatura frecuencia de pago
        nomenclatura_frecuencia_pago(frecuencia_pagos) {
            switch (frecuencia_pagos) {
                case 'Diario':
                    return "Dias";
                    break;

                case 'Mensual':
                    return "Meses";
                    break;

                case 'Semanal':
                    return "Semanas";
                    break;
                case 'Quincenal':
                    return "Quincenas";
                    break;

                case 'Anual':
                    return "Años";
                    break;

                case 'Trimestral':
                    return "Trimestres";
                    break;
            }
        },
        // generara cronograma de pagos
        // cronograma franses mensual
        calcularAmortizacionFrancesMensual(montoPrestamo, plazoMeses, tasaInteresMensual, fecha = "hoy") {

            this.tasa_diaria = (tasaInteresMensual / 30).toFixed(2);
            tasaInteresMensual = (tasaInteresMensual / 100);
            // var cuota = ((montoPrestamo * (tasaInteresMensual / 100)) * plazoMeses + montoPrestamo) / plazoMeses
            var cuota = (montoPrestamo * tasaInteresMensual * Math.pow(1 + tasaInteresMensual, plazoMeses)) /
                (Math.pow(1 + tasaInteresMensual, plazoMeses) - 1)

            if(fecha == "hoy"){
                this.fecha_desembolso = moment().format("DD/M/YYYY");
            }else{
                this.fecha_desembolso = moment().format("DD/M/YYYY");
            }

            this.cuotas = this.redondear(cuota);

            var interes_inicial = montoPrestamo * tasaInteresMensual;

            var amortizacionFrances = [];
            var amortizacionPeriodo = 0;
            let saldo_capital = montoPrestamo; 
            for (let periodo = 1; periodo <= plazoMeses; periodo++) {

                if (periodo == 1) {
                    amortizacionPeriodo = cuota - interes_inicial;
                } else {

                    saldo_capital -= amortizacionPeriodo;
                    interes_inicial = saldo_capital * tasaInteresMensual;
                    amortizacionPeriodo = cuota - interes_inicial;
                }

                // const fechaVencimiento = new Date(moment().year(), moment().month() + periodo, moment().date());

                var fechaVencimiento;

                if (fecha != "hoy") {
                    fechaVencimiento = new Date(moment(fecha).year(), moment(fecha).month() + periodo, moment(fecha).date());
                } else {
                    fechaVencimiento = new Date(moment().year(), moment().month() + periodo, moment().date());
                }

                if (fechaVencimiento.getDay() === 0) {
                    fechaVencimiento.setDate(fechaVencimiento.getDate() + 1);
                }

                const pago = {
                    periodo: periodo,
                    fechaVencimiento: fechaVencimiento.toLocaleDateString(),
                    saldoCapital: saldo_capital,
                    amortizacion: amortizacionPeriodo.toFixed(2),
                    interes: interes_inicial.toFixed(2),
                    cuota: cuota
                };

                amortizacionFrances.push(pago);
            }
            return amortizacionFrances;
        },
        calcularAmortizacionFrancesMensualFechaDesembolsoCambiada(montoPrestamo, plazoMeses, tasaInteresMensual, fecha_desembolso ,fecha = "hoy") {

            this.tasa_diaria = (tasaInteresMensual / 30).toFixed(2);
            tasaInteresMensual = (tasaInteresMensual / 100);
            // var cuota = ((montoPrestamo * (tasaInteresMensual / 100)) * plazoMeses + montoPrestamo) / plazoMeses
            var cuota = (montoPrestamo * tasaInteresMensual * Math.pow(1 + tasaInteresMensual, plazoMeses)) /
                (Math.pow(1 + tasaInteresMensual, plazoMeses) - 1)

            if(fecha == "hoy"){
                console.log("hoy");
                
                this.fecha_desembolso = moment(fecha_desembolso).format("DD/M/YYYY");
            }else{
                console.log("no es hoy");
                this.fecha_desembolso = moment(fecha_desembolso).format("DD/M/YYYY");
            }

            this.cuotas = this.redondear(cuota);

            var interes_inicial = montoPrestamo * tasaInteresMensual;

            var amortizacionFrances = [];
            var amortizacionPeriodo = 0;
            let saldo_capital = montoPrestamo; 
            for (let periodo = 1; periodo <= plazoMeses; periodo++) {

                if (periodo == 1) {
                    amortizacionPeriodo = cuota - interes_inicial;
                } else {

                    saldo_capital -= amortizacionPeriodo;
                    interes_inicial = saldo_capital * tasaInteresMensual;
                    amortizacionPeriodo = cuota - interes_inicial;
                }

                // const fechaVencimiento = new Date(moment().year(), moment().month() + periodo, moment().date());

                var fechaVencimiento;

                if (fecha != "hoy") {
                    fechaVencimiento = new Date(moment(fecha).year(), moment(fecha).month() + periodo, moment(fecha).date());
                } else {
                    fechaVencimiento = new Date(moment(fecha_desembolso).year(), moment(fecha_desembolso).month() + periodo, moment(fecha_desembolso).date());
                }

                if (fechaVencimiento.getDay() === 0) {
                    fechaVencimiento.setDate(fechaVencimiento.getDate() + 1);
                }

                const pago = {
                    periodo: periodo,
                    fechaVencimiento: fechaVencimiento.toLocaleDateString(),
                    saldoCapital: saldo_capital,
                    amortizacion: amortizacionPeriodo.toFixed(2),
                    interes: interes_inicial.toFixed(2),
                    cuota: cuota
                };

                amortizacionFrances.push(pago);
            }
            return amortizacionFrances;
        },
        // calcular cronograma semanal
        calcularAmortizacionFrancesSemanal(montoPrestamo, plazoSemanas, tasaInteresSemanal,fecha = "hoy") { 
            const cuotaSemanal = ((montoPrestamo * parseFloat(tasaInteresSemanal / 100)) + montoPrestamo) / plazoSemanas;
            const fechaDesembolso =  fecha == "hoy" ? moment().format("DD/M/YYYY") : moment(fecha).format("DD/M/YYYY");
            if(fecha == "hoy"){
                this.fecha_desembolso = moment().format("DD/M/YYYY");
            }
            const interesSemanal = (montoPrestamo * (tasaInteresSemanal / 100)) / plazoSemanas;
            const amortizacionSemanal = cuotaSemanal - interesSemanal;

            const amortizacionFrances = [];
            var saldoPendiente = montoPrestamo;


            this.cuotas = this.redondear(cuotaSemanal);
            this.tasa_diaria = (tasaInteresSemanal / 30).toFixed(2);

            for (let semana = 1; semana <= plazoSemanas; semana++) {

                saldoPendiente = saldoPendiente - amortizacionSemanal;
 
                var fechaVencimiento;

                if (fecha != "hoy") {  
                    fechaVencimiento = moment(fecha, "YYYY-MM-DD").add(semana, 'weeks');
                
                } else {
                    fechaVencimiento = moment(fechaDesembolso, "DD/M/YYYY").add(semana, 'weeks');
                }

                
                if (fechaVencimiento.day() === 0) {
                    fechaVencimiento.add(1, 'days');
                }

                const pago = {
                    periodo: semana,
                    fechaVencimiento: fechaVencimiento.format("DD/M/YYYY"),
                    saldoCapital: saldoPendiente.toFixed(2),
                    amortizacion: amortizacionSemanal.toFixed(2),
                    interes: interesSemanal.toFixed(2),
                    cuota: cuotaSemanal.toFixed(2)
                };

                amortizacionFrances.push(pago);
            }

            return amortizacionFrances;
        },
        // calcular cronograma diario
        calcularAmortizacionFrancesdiario(montoPrestamo, plazoDias, tasaInteresDiaria,fecha = "hoy") {

             const cuotaDiaria = ((montoPrestamo * (tasaInteresDiaria / 100)) + montoPrestamo) / plazoDias;
            if(fecha == "hoy"){
                this.fecha_desembolso = moment().format("DD/M/YYYY");
            }
            this.cuotas = this.redondear(cuotaDiaria);
            this.tasa_diaria = (tasaInteresDiaria / 30).toFixed(2);


            const amortizacionFrances = [];
            var fechaD = 1;
            let saldoPendiente = montoPrestamo;

            for (let dia = 1; dia <= plazoDias; dia++) {
                const interesDia = (montoPrestamo * (tasaInteresDiaria / 100)) / plazoDias;
                const amortizacionDia = cuotaDiaria - interesDia;
                saldoPendiente -= amortizacionDia;

                var fechaVencimiento;
                if (fecha != "hoy") {
                    fechaVencimiento = new Date(moment(fecha).year(), moment(fecha).month() , moment(fecha).date() + fechaD);
                } else {
                    fechaVencimiento = new Date(moment().year(), moment().month(), moment().date() + fechaD);
                }
                 
                fechaD++; // Asumiendo fecha de desembolso el 26 de abril de 2023
                if (fechaVencimiento.getDay() === 0) {
                    fechaVencimiento.setDate(fechaVencimiento.getDate() + 1);
                    fechaD++;
                }
                const pago = {
                    periodo: dia,
                    fechaVencimiento: fechaVencimiento.toLocaleDateString(),
                    saldoCapital: saldoPendiente.toFixed(2),
                    amortizacion: amortizacionDia.toFixed(2),
                    interes: interesDia.toFixed(2),
                    cuota: this.redondear(cuotaDiaria)
                };

                amortizacionFrances.push(pago);
            }

            return amortizacionFrances;
        }, 
        // programacion de las fechas a partir de una fecha personalizada 
        calcularAmortizacionFrancesMensualByDate(montoPrestamo, plazoMeses, tasaInteresMensual, fecha) {

            this.tasa_diaria = (tasaInteresMensual / 30).toFixed(2);
            tasaInteresMensual = (tasaInteresMensual / 100);
            // var cuota = ((montoPrestamo * (tasaInteresMensual / 100)) * plazoMeses + montoPrestamo) / plazoMeses
            var cuota = (montoPrestamo * tasaInteresMensual * Math.pow(1 + tasaInteresMensual, plazoMeses)) /
                (Math.pow(1 + tasaInteresMensual, plazoMeses) - 1)

            this.fecha_desembolso = moment(fecha).format("DD/M/YYYY");



            this.cuotas = this.redondear(cuota);

            var interes_inicial = montoPrestamo * tasaInteresMensual;

            var amortizacionFrances = [];
            var amortizacionPeriodo = 0;
            let saldo_capital = montoPrestamo; 
            for (let periodo = 1; periodo <= plazoMeses; periodo++) {

                if (periodo == 1) {
                    amortizacionPeriodo = cuota - interes_inicial;
                } else {

                    saldo_capital -= amortizacionPeriodo;
                    interes_inicial = saldo_capital * tasaInteresMensual;
                    amortizacionPeriodo = cuota - interes_inicial;
                }

                // const fechaVencimiento = new Date(moment().year(), moment().month() + periodo, moment().date());

                var fechaVencimiento;


                fechaVencimiento = new Date(moment(fecha).year(), moment(fecha).month() + periodo, moment(fecha).date());

                if (fechaVencimiento.getDay() === 0) {
                    fechaVencimiento.setDate(fechaVencimiento.getDate() + 1);
                }

                const pago = {
                    periodo: periodo,
                    fechaVencimiento: fechaVencimiento.toLocaleDateString(),
                    saldoCapital: saldo_capital,
                    amortizacion: amortizacionPeriodo.toFixed(2),
                    interes: interes_inicial.toFixed(2),
                    cuota: cuota
                };

                amortizacionFrances.push(pago);
            }
            return amortizacionFrances;
        },
        // calcular cronograma semanal
        calcularAmortizacionFrancesSemanalByDate(montoPrestamo, plazoSemanas, tasaInteresSemanal, fecha) {
        
            const cuotaSemanal = ((montoPrestamo * parseFloat(tasaInteresSemanal / 100)) + montoPrestamo) / plazoSemanas;
            const fechaDesembolso = moment(fecha).format("DD/M/YYYY");

            const interesSemanal = (montoPrestamo * (tasaInteresSemanal / 100)) / plazoSemanas;
            const amortizacionSemanal = cuotaSemanal - interesSemanal;

            const amortizacionFrances = [];
            var saldoPendiente = montoPrestamo;


            this.cuotas = this.redondear(cuotaSemanal);
            this.tasa_diaria = (tasaInteresSemanal / 30).toFixed(2);

            for (let semana = 1; semana <= plazoSemanas; semana++) {

                saldoPendiente = saldoPendiente - amortizacionSemanal;

                const fechaVencimiento = moment(fechaDesembolso, "DD/M/YYYY").add(semana, 'weeks');
                if (fechaVencimiento.day() === 0) {
                    fechaVencimiento.add(1, 'days');
                }

                const pago = {
                    periodo: semana,
                    fechaVencimiento: fechaVencimiento.format("DD/M/YYYY"),
                    saldoCapital: saldoPendiente.toFixed(2),
                    amortizacion: amortizacionSemanal.toFixed(2),
                    interes: interesSemanal.toFixed(2),
                    cuota: cuotaSemanal.toFixed(2)
                };

                amortizacionFrances.push(pago);
            }

            return amortizacionFrances;
        },
        // calcular cronograma diario
        calcularAmortizacionFrancesdiarioByDate(montoPrestamo, plazoDias, tasaInteresDiaria, fecha) {

            const cuotaDiaria = ((montoPrestamo * (tasaInteresDiaria / 100)) + montoPrestamo) / plazoDias;
            this.fecha_desembolso = moment(fecha).format("DD/M/YYYY");
            this.cuotas = this.redondear(cuotaDiaria);
            this.tasa_diaria = (tasaInteresDiaria / 30).toFixed(2);


            const amortizacionFrances = [];
            var fecha = 1;
            let saldoPendiente = montoPrestamo;

            for (let dia = 1; dia <= plazoDias; dia++) {
                const interesDia = (montoPrestamo * (tasaInteresDiaria / 100)) / plazoDias;
                const amortizacionDia = cuotaDiaria - interesDia;
                saldoPendiente -= amortizacionDia;
                const fechaVencimiento = new Date(moment(fecha).year(), moment(fecha).month(), moment(fecha).date() + fecha);
                fecha++; // Asumiendo fecha de desembolso el 26 de abril de 2023
                if (fechaVencimiento.getDay() === 0) {
                    fechaVencimiento.setDate(fechaVencimiento.getDate() + 1);
                    fecha++;
                }
                const pago = {
                    periodo: dia,
                    fechaVencimiento: fechaVencimiento.toLocaleDateString(),
                    saldoCapital: saldoPendiente.toFixed(2),
                    amortizacion: amortizacionDia.toFixed(2),
                    interes: interesDia.toFixed(2),
                    cuota: this.redondear(cuotaDiaria)
                };

                amortizacionFrances.push(pago);
            }

            return amortizacionFrances;
        },
        // calcular la edad de una persona
        calcularEdad(fechaNacimiento) {
            const hoy = moment();
            const nacimiento = moment(fechaNacimiento, 'YYYY-MM-DD');
            const edad = hoy.diff(nacimiento, 'years');
            return `${edad} años`;
        },
        // formatear fecha a una fecha mas legible
        formatear_fecha(fecha) {
            return moment(fecha, 'YYYY-MM-DD').format('MMMM Do YYYY');
        },
        formatear_fecha_normal(fecha) {
            return moment(fecha).format('DD/MM/YYYY');
        },
        formatear_fecha_select(fecha) {
            return moment(fecha).format('MMMM Do YYYY');
        },
        formatear_mes(fecha){
            return moment(fecha,'YYYY-MM-DD').format('MMMM');
        },
        // funcion para formatear los montos de dinero
        formatear_monto(monto) {
            return monto.toLocaleString('es-PE', {
                minimumFractionDigits: 2
            })
        },
        // formaterar cualquier monto de dinero a soles
        //aplicar el formato
        formatNumber(n) {
            // format number 1000000 to 1,234,567
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        },
        formatCurrency(input, blur) { 
            // appends $ to value, validates decimal side
            // and puts cursor back in right position.

            // get input value
            var input_val = input.val();

            // don't validate empty input
            if (input_val === "") {
                return;
            }

            // original length
            var original_len = input_val.length;

            // initial caret position 
            var caret_pos = input.prop("selectionStart");

            // check for decimal
            if (input_val.indexOf(".") >= 0) {

                // get position of first decimal
                // this prevents multiple decimals from
                // being entered
                var decimal_pos = input_val.indexOf(".");

                // split number by decimal point
                var left_side = input_val.substring(0, decimal_pos);
                var right_side = input_val.substring(decimal_pos);

                // add commas to left side of number
                left_side = this.formatNumber(left_side);

                // validate right side
                right_side = this.formatNumber(right_side);

                // On blur make sure 2 numbers after decimal
                if (blur === "blur") {
                    right_side += "00";
                }

                // Limit decimal to only 2 digits
                right_side = right_side.substring(0, 2);

                // join number by .
                input_val = left_side + "." + right_side;

            } else {
                // no decimal entered
                // add commas to number
                // remove all non-digits
                input_val = this.formatNumber(input_val);
                input_val = input_val;

                // final formatting
                if (blur === "blur") {
                    input_val += ".00";
                }
            }

            // send updated string to input
            input.val(input_val);

            // put caret back in the right position
            var updated_len = input_val.length;
            caret_pos = updated_len - original_len + caret_pos;
            input[0].setSelectionRange(caret_pos, caret_pos);
        },
        //destinar los eventos
        currency(name) {
          
            var self = this;
            $("input[data-type='" + name + "']").on({
                keyup: function () {
                    self.formatCurrency($(this));
                },
                blur: function () {
                    self.formatCurrency($(this), "blur");
                }
            });
        },
        ///////--------
        //cargar los analistas de esta sucursal
        async cargar_analistas() {
            const data = {};

            const headers = this.headers;

            Axios
                .post("/load_analistas", data, {
                    headers,
                })
                .then((response) => {
                    if (response.data.success) {
                        this.analistas = response.data.data;
                    }
                })
                .catch((error) => {
                    this.alert_error_modal("Error en el servidor, no se cargo el usuario");
                    console.error(error);
                });
        },
        // carga los datos del usuario que logeo al sistema
        async cargar_usuario() {
            const data = {};

            const headers = this.headers;

            return Axios
                .post("/load_usuario", data, {
                    headers,
                })
                .then((response) => { 
                    if (response.data.success) {

                        return response.data.data;
                    }
                })
                .catch((error) => {
                    this.alert_error_modal("Error en el servidor, no se cargo el usuario");
           
                });

        },
        async load_message_by_user_before() {
            const data = {};

            const headers = this.headers;

            return Axios
                .post("/load_message_by_user_before", data, headers)
                .then((response) => {
                
                    if (response.data.success) {
                        return response.data.data;
                    }
                })
                .catch((error) => {
                    this.alert_error_modal("Error en el servidor, no se cargo el usuario");
                    console.error(error);
                }); 
        },
        async do_no_show_message_before(id) {
            const params = {
                id: id
            };
          

     
            return Axios
                .get("/do_no_show_message_before", {
                    params
                })
                .then((response) => { 
                    if (response.data.success) {
                        return response.data;
                    }
                })
                .catch((error) => {
                    this.alert_error_modal("Error en el servidor, no se cargo el usuario");
                    console.error(error);
                });
        },

        async do_no_show_message_after(id) {
            const params = {
                id: id
            };
             

            return Axios
                .get("/do_no_show_message_after", {
                    params
                })
                .then((response) => { 
                    if (response.data.success) {
                        return response.data;
                    }
                })
                .catch((error) => {
                    this.alert_error_modal("Error en el servidor, no se cargo el usuario");
                    
                });
        }, 
        async load_message_by_user_after(id) {
            const data = {};

            const headers = this.headers;

            return Axios
                .post("/load_message_by_user_after", data, headers)
                .then((response) => {
                 
                    if (response.data.success) {
                        return response.data.data;
                    }
                })
                .catch((error) => {
                    this.alert_error_modal("Error en el servidor, no se cargo el usuario");
              
                });
        }, 
        async load_message_by_user_now(id) {
            const data = {
                id: id
            };

            const headers = this.headers;

            return Axios
                .post("/load_message_by_user", data, {
                    headers,
                })
                .then((response) => {
               
                    if (response.data.success) {
                        return response.data.data;
                    }
                })
                .catch((error) => {
                    this.alert_error_modal("Error en el servidor, no se cargo el usuario");
                    console.error(error);
                });

        },
        loading_start() {
            $("#preload").fadeIn()
        },
        loading_end() {
            $("#preload").fadeOut()
        }, 
        // mensajes de alerta
        // -----------
        alert_img_rechazada(message) {
            this.$swal.fire({
                imageUrl: "../../../../dist/images/Draw/rechazado.svg",
                imageHeight: 150,
                imageAlt: "--",
                text: message,
                icon: "success",
            });
        },
        alert_success(message) {

            const Toast = this.$swal.mixin({
                toast: true,
                position: "top-start",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = this.$swal.stopTimer;
                    toast.onmouseleave = this.$swal.resumeTimer;
                },
                customClass: {
                    container: 'swal-overlay'
                }
            });
            Toast.fire({
                icon: "success",
                title: message
            });

        },
        alert_warning(message) {

            const Toast = this.$swal.mixin({
                toast: true,
                position: "top-start",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = this.$swal.stopTimer;
                    toast.onmouseleave = this.$swal.resumeTimer;
                },
                customClass: {
                    container: 'swal-overlay'
                }
            });
            Toast.fire({
                icon: "warning",
                title: message
            });
        },
        alert_error_modal(message) {
            this.$swal.fire({
                icon: "error",
                title: "Error ",
                text: message,
                footer: "-------",
            });
        },
        // funciones para tabulator table 
        redondear_valor(values, data, calcParams) {
            var calc = 0;


            const importe = values.reduce((acumulador, res) => {
                return acumulador + parseFloat(res);
            }, 0);

            return Math.round(importe).toFixed(2);
        }
    },
    async mounted() {
        // this.cargar_usuario().then((result) => {
        //     this.user = result;

        //     // ejecutar los mensajes de alertar por usuario
        this.load_message_by_user_before().then(async (result) => { 
            for (let i = 0; i < result.length; i++) {
                await this.$swal.fire({
                    imageUrl: "../../../dist/images/Draw/message.svg",
                    imageWidth: 400,
                    imageHeight: 200,
                    html: `<ul>
                        <li>Cliente ${ result[i].prestamo.solicitud.cliente.cli_nombre } | ${ result[i].prestamo.solicitud.cliente.cli_apellido }</li>
                        <li></li>
                        <li><h2>${result[i].msj_descripcion}</h2></li>
                        <li></li>
                        <li>Fecha para que fue programada => ${moment(result[i].fecha_programada).format("DD/MM/YYYY")}</li>
                    </ul>`,
                    title: `Mensaje temporal de la solicitud N° ${result[i].prestamo.solicitud.serie}`,
                    showDenyButton: true,
                    denyButtonText: `Seguir mostrardo este mensaje`,
                    confirmButtonText: 'Ya vi este mensaje'
                }).then((resultado) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (resultado.isConfirmed) {
                        this.do_no_show_message_before(result[i].urlapi).then(async (result2) => {
                            
                            if (result2.success) {
                                this.alert_success(result2.message)
                            } else {
                                this.alert_warning(result2.message)
                            }
                        })
                    } else if (result.isDenied) {
                        console.log("User is denied");
                    }
                });
            }
        })

        this.load_message_by_user_after().then(async (result) => { 
            for (let i = 0; i < result.length; i++) {
                await this.$swal.fire({
                    imageUrl: "../../../dist/images/Draw/message.svg",
                    imageWidth: 400,
                    imageHeight: 200,
                    html: `<ul>
                        <li>Cliente ${ result[i].prestamo.solicitud.cliente.cli_nombre } | ${ result[i].prestamo.solicitud.cliente.cli_apellido }</li>
                        <li></li>
                        <li><h2>${result[i].msj_descripcion}</h2></li>
                        <li></li>
                        <li>Fecha para que fue programada => ${moment(result[i].fecha_programada).format("DD/MM/YYYY")}</li>
                    </ul>`,
                    title: `Mensaje de la solicitud N° ${result[i].prestamo.solicitud.serie}`,
                    showDenyButton: true,
                    denyButtonText: `Seguir mostrardo este mensaje`,
                    confirmButtonText: 'Ya vi este mensaje'
                }).then((resultado) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (resultado.isConfirmed) {
                        this.do_no_show_message_after(result[i].urlapi).then(async (result2) => {
                     
                            if (result2.success) {
                                this.alert_success(result2.message)
                            } else {
                                this.alert_warning(result2.message)
                            }
                        })
                    } else if (result.isDenied) {
                        console.log("User is denied");
                    }
                });
            }
        })




        $.validator.addMethod("noSpecial", function (value, element) {
            return !/[~`!#$%\^&*+=\-\[\]\\';,/{}|\\":<>\?]/g.test(value);
        }, "No se permiten caracteres especiales.");

        $.validator.setDefaults({
            highlight: function (element) {
                $(element)
                    .closest(".form-control")
                    .addClass("is-invalid")
            },
            unhighlight: function (element) {
                $(element)
                    .closest(".form-control")
                    .removeClass("is-invalid")
                    .addClass("is-valid")
            }
        })
    },
};
