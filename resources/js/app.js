// importaciones vue
import './bootstrap';
import {
    createApp
} from 'vue';

 
import {
    library
} from '@fortawesome/fontawesome-svg-core';

import {
    FontAwesomeIcon
} from '@fortawesome/vue-fontawesome';

import {
    fas
} from '@fortawesome/free-solid-svg-icons';

import {
    fab
} from '@fortawesome/free-brands-svg-icons';

import VueNumerals from 'vue-numerals';

import moment from 'moment';

import VueSweetalert2 from 'vue-sweetalert2';

// datepicker
import Datepicker from 'vuejs3-datepicker';

// If you don't need the styles, do not connect
import 'sweetalert2/dist/sweetalert2.min.css';
import "primevue/resources/themes/lara-light-green/theme.css";
import "primevue/resources/primevue.min.css";
import OverlayPanel from 'primevue/overlaypanel';
import ProgressSpinner from 'primevue/progressspinner';

// importaciones primevue
import PrimeVue from 'primevue/config';
import Dialog from 'primevue/dialog';
import InputMask from 'primevue/inputmask';
import ToggleButton from 'primevue/togglebutton';
import Button from 'primevue/button';
import Sidebar from 'primevue/sidebar';
import Calendar from 'primevue/calendar';
import ScrollPanel from 'primevue/scrollpanel';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import InputSwitch from 'primevue/inputswitch';

import {
    AgChartsVue
} from 'ag-charts-vue3';

//importaciones tom select js
import 'tom-select/dist/css/tom-select.css';
import TomSelect from 'tom-select';

// Agregar los iconos que desees utilizar al library
library.add(fas, fab);

// inicializando la vue
const app = createApp({});

app.use(VueNumerals); // default locale is 'en'

// with options
app.use(VueNumerals, {
    locale: 'pe'
});

// Configure PrimeVue 
app.use(PrimeVue, {
    ripple: true
});

//importaciones
app.component('Icon', FontAwesomeIcon);
app.component('TomSelect', TomSelect);
app.component('Datepicker', Datepicker);

app.use(VueSweetalert2, {
    confirmButtonColor: '#0039A6',
});

// importaciones primevue 
app.use(Dialog);
app.component('Dialog', Dialog);
app.component('InputMask', InputMask);
app.component('ToggleButton', ToggleButton);
app.component('OverlayPanel', OverlayPanel);
app.component('Button', Button);
app.component('ProgressSpinner', ProgressSpinner);
app.component('Sidebar', Sidebar);
app.component('Calendar', Calendar);
app.component('ScrollPanel', ScrollPanel);
app.component('InputNumber', InputNumber);
app.component('Textarea', Textarea);
app.component('InputSwitch', InputSwitch);
app.component('ag-charts-vue', AgChartsVue);

//calendario
app.component('calendario-planilla', require('./components/calendario/calendario-planilla.vue').default);

//home
app.component('home-principal', require('./components/home/home-principal.vue').default);
//reportes
app.component('report-general-leyenda', require('./components/reportes/report-general-leyenda.vue').default);
app.component('report-limit-aprobados', require('./components/reportes/report-limit-aprobados.vue').default);
app.component('reporte-general-montos', require('./components/reportes/reporte-general-montos.vue').default);
app.component('reporte-general-balance', require('./components/reportes/reporte-general-balance.vue').default);

app.component('by_year', require('./components/reportes/reporte_balance/by_year.vue').default);
app.component('by_months', require('./components/reportes/reporte_balance/by_months.vue').default);

// componentes componentes usuarios
app.component('table-users', require('./components/usuarios/table-user.vue').default);
app.component('report-analista-caja', require('./components/usuarios/report-analista-caja.vue').default);
app.component('create-usuario', require('./components/usuarios/create-usuario.vue').default);
app.component('edit-usuario', require('./components/usuarios/edit-usuario.vue').default);
app.component('crear-adelanto', require('./components/usuarios/crear-adelanto.vue').default);
app.component('select-trabajadores', require('./components/usuarios/select-trabajadores.vue').default);
app.component('recepcionista', require('./components/usuarios/recepcionista.vue').default);

// componentes componentes ampliacion
app.component('ampliacion-prestamo', require('./components/prestamo/ampliacion-prestamo.vue').default);

// componentes componentes sucursales
app.component('select-sucursal', require('./components/sucursal/select-sucursal.vue').default);

// componentes prestamo
app.component('table-planilla', require('./components/prestamo/table-planilla.vue').default);
app.component('simulacion', require('./components/prestamo/simulacion.vue').default);
app.component('select-estado', require('./components/prestamo/select-estado.vue').default);

//prestamo--// ---------------- sub
app.component('opciones-cronograma', require('./components/prestamo/opciones-cronograma.vue').default);
app.component('pagar-cuota', require('./components/prestamo/pagar-cuota.vue').default);
app.component('pagar-cuota-grupal', require('./components/prestamo/pagar-cuota-grupal.vue').default);
app.component('filtrar-by-frecuencia-pago', require('./components/prestamo/filtrar-by-frecuencia-pago.vue').default);
app.component('table-planilla-por-cliente', require('./components/prestamo/table-planilla-por-cliente.vue').default);

app.component('opciones-cronograma-por-parte', require('./components/prestamo/opciones-cronograma-por-parte.vue').default);
app.component('pagar-por-parte', require('./components/prestamo/pagar-por-parte.vue').default);

// componentes analista
app.component('analistas', require('./components/usuarios/analistas.vue').default);
app.component('analista-multiple', require('./components/usuarios/analista_multiple.vue').default);

//reprogramacion
app.component('reprogramacion-prestamo', require('./components/prestamo/reprogramacion-prestamo.vue').default);

//reprogramacion cuota
app.component('reprogramacion-prestamo-cuota', require('./components/prestamo/reprogramacion-prestamo-cuota.vue').default);

//refin
app.component('refinanciamiento-prestamo', require('./components/prestamo/refinanciamiento-prestamo.vue').default);

// componentes caja
app.component('table-caja', require('./components/caja/table-caja.vue').default);
app.component('create-caja', require('./components/caja/create-caja.vue').default);
app.component('edit-caja', require('./components/cuentas/edit-caja.vue').default);
app.component('show-caja', require('./components/caja/show-caja.vue').default);

// TODO: componentes ingresos
app.component('table-ingreso', require('./components/ingreso/table-ingreso.vue').default);
app.component('ingresos-varios', require('./components/ingreso/ingresos-varios.vue').default);
app.component('select-ingreso', require('./components/ingreso/select-ingreso.vue').default);
app.component('edit-pago-ingreso', require('./components/ingreso/edit-pago-ingreso.vue').default);

// componentes gastos
app.component('table-gastos', require('./components/gastos/table-gastos.vue').default);
app.component('edit-gasto', require('./components/gastos/edit-gasto.vue').default);
app.component('create-gastos', require('./components/gastos/create-gastos.vue').default);
app.component('edit-pago-gasto', require('./components/gastos/edit-pago-gasto.vue').default);

// componentes tipo gasto
app.component('table-tipo-gasto', require('./components/tipo-gasto/table-tipo-gasto.vue').default);
app.component('create-tipo-gasto', require('./components/tipo-gasto/create-tipo-gasto.vue').default);
app.component('edit-tipo-gasto', require('./components/tipo-gasto/edit-tipo-gasto.vue').default);
app.component('select-tipo-gasto', require('./components/tipo-gasto/select-tipo-gasto.vue').default);

// componentes clientes
app.component('tipo-cliente-multiple', require('./components/cliente/tipo-cliente-multiple.vue').default);
app.component('table-cliente', require('./components/cliente/table-cliente.vue').default);
// app.component('create-cliente', require('./components/cliente/create-cliente.vue').default);
app.component('edit-cliente', require('./components/cliente/edit-cliente.vue').default);
app.component('show-cliente', require('./components/cliente/show-cliente.vue').default);

// componentes ingresos varios 
app.component('table-ingresos-varios', require('./components/ingresos-varios/table-ingresos-varios.vue').default);
app.component('create-ingresos-varios', require('./components/ingresos-varios/create-ingresos-varios.vue').default);

//componentes mensajes prestamos 
app.component('create-msj-prestamo', require('./components/mensajes-prestamos/create-msj-prestamo.vue').default);
app.component('show-msj-prestamo', require('./components/mensajes-prestamos/show-msj-prestamo.vue').default);

// componentes cuentas
app.component('table-cuentas', require('./components/cuentas/table-cuentas.vue').default);
app.component('create-cuenta', require('./components/cuentas/create-cuenta.vue').default);
app.component('edit-cuentas', require('./components/cuentas/edit-cuentas.vue').default);
app.component('select-cuenta', require('./components/cuentas/select-cuenta.vue').default);

// mensajes para los prestamos
app.component('table-msj-prestamos', require('./components/mensajes-prestamos/table-msj-prestamos.vue').default);
app.component('cancelar_prestamo', require('./components/mensajes-prestamos/cancelar_prestamo.vue').default);



// componentes solicitud
app.component('create-solicitud', require('./components/solicitudes/create-solicitud.vue').default);

app.component('agregar-tarjeta', require('./components/solicitudes/agregar-tarjeta.vue').default);

app.component('edit-solicitud', require('./components/solicitudes/edit-solicitud.vue').default);

app.component('cambiar_contrato', require('./components/solicitudes/cambiar_contrato.vue').default);

app.component('show-solicitud', require('./components/solicitudes/show-solicitud.vue').default);

app.component('table-solicitudes', require('./components/solicitudes/table-solicitudes.vue').default);

app.component('table-solicitudes-pendiente', require('./components/solicitudes/table-solicitudes-pendiente.vue').default);

app.component('table-solicitudes-rechazado', require('./components/solicitudes/table-solicitudes-rechazado.vue').default);

app.component('table-solicitudes-aprobado', require('./components/solicitudes/table-solicitudes-aprobado.vue').default);

app.component('table-solicitudes-procesado', require('./components/solicitudes/table-solicitudes-procesado.vue').default);

app.component('guardar_archivos', require('./components/solicitudes/guardar_archivos.vue').default);

app.component('editar_archivos', require('./components/solicitudes/editar_archivos.vue').default);

app.component('time-line-request', require('./components/solicitudes/time-line-request.vue').default);

app.component('load-desemboloso', require('./components/solicitudes/load-desemboloso.vue').default);

// componentes compra
app.component('table-compra', require('./components/compra/table-compra.vue').default);
app.component('create-compra', require('./components/compra/create-compra.vue').default);
app.component('edit-compra', require('./components/compra/edit-compra.vue').default);
app.component('show-compra', require('./components/compra/show-compra.vue').default);

// componentes salario   
app.component('create-salario', require('./components/salario/create-salario.vue').default);
app.component('select-salario', require('./components/salario/select-salario.vue').default);
app.component('solicitud-planilla', require('./components/salario/solicitud-planilla.vue').default);
app.component('table-solicitud', require('./components/salario/table-solicitud.vue').default);
app.component('show-solicitud-trabajador', require('./components/salario/show-solicitud-trabajador.vue').default);
app.component('table-solicitudes-procesados', require('./components/salario/table-solicitudes-procesados.vue').default);
app.component('table-sueldo', require('./components/salario/table-sueldo.vue').default);
app.component('show-salario', require('./components/salario/show-salario.vue').default);
app.component('mensajes_alert', require('./components/mensajes/mensajes_alert.vue').default);

// componentes auxiliares
app.component('btn-loading', require('./components/auxiliares/btn-loading.vue').default); 
 

// panel post para la venta de ropas cautivas

app.component('panel-pos', require('./components/cautiva/pos/panel-pos.vue').default);

app.mount('#app');

 