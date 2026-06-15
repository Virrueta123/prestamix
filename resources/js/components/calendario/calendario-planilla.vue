<template>
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Calendario
        </h2>
    </div>
    <div class="grid grid-cols-12 gap-5 mt-5">
        <!-- BEGIN: Calendar Side Menu -->
        <div class="col-span-12 xl:col-span-4 2xl:col-span-3">
            <div class="card box p-3 ">
                <div class="flex flex-col sm:flex-row sm:items-end xl:items-start">
                    <div class="xl:flex sm:mr-auto mt-2 pt-0">
                        <div class="font-bold text-base  font-xl">Fecha seleccionada
                        </div>
                    </div>
                    <div class="flex mt-5 sm:mt-0">

                        <div class="dropdown w-1/2 sm:w-auto" style="position: relative;">
                            <a :href="url_planilla_fecha"
                                class="dropdown-toggle btn btn-outline-primary w-full sm:w-auto" aria-expanded="false"
                                data-tw-toggle="dropdown">
                                <Icon icon="calendar-check" class="mr-2" /> {{ fecha_plantilla }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box p-5 intro-y mt-4">
                <div class="flex text-primary">
                    <Icon icon='list' class='mr-2 mt-2' />
                    <div class="font-medium text-base mx-auto">Leyenda</div>
                </div>
                <div class="border-t border-slate-200/60 dark:border-darkmode-400 pt-5 mt-5">
                    <div class="flex items-center">
                        <div class="w-5 h-4 bg-pending fila-roja  rounded mr-3"></div>
                        <span class="truncate">Cliente morosos</span>
                        <div class="h-px flex-1 border border-r border-dashed border-slate-200 mx-3 xl:hidden"></div>
                        <span class="font-medium xl:ml-auto">{{ leyenda.cliente_moroso }}</span>
                    </div>
                    <div class="flex items-center mt-4">
                        <div class="w-5 h-4 bg-primary fila-amarilla rounded mr-3"></div>
                        <span class="truncate">Cuotas que hoy vencen</span>
                        <div class="h-px flex-1 border border-r border-dashed border-slate-200 mx-3 xl:hidden"></div>
                        <span class="font-medium xl:ml-auto">{{ leyenda.cuota_hoy }}</span>
                    </div>
                    <div class="flex items-center mt-4">
                        <div class="w-5 h-4 bg-primary fila-pendiente rounded  mr-3"></div>
                        <span class="truncate">Cuotas pendientes</span>
                        <div class="h-px flex-1 border border-r border-dashed border-slate-200 mx-3 xl:hidden"></div>
                        <span class="font-medium xl:ml-auto">{{ leyenda.cuota_pendientes }}</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- END: Calendar Side Menu -->
        <!-- BEGIN: Calendar Content -->
        <div class="col-span-12 xl:col-span-8 2xl:col-span-9">
            <div class="box p-5">
                <FullCalendar ref="calendar" class="full-calendar" :options="calendarOptions" @datesSet="onDatesSet" />
            </div>
        </div>
        <!-- END: Calendar Content -->
    </div>

    <Sidebar v-model:visible="is_opciones_modal" header="Opciones para la tabla de solicitudes" position="bottom"
        style="height: auto">

        <opciones-cronograma v-if="get_cuota.frecuencia_pagos == 'Mensual'"
            :get_cuota="get_cuota"></opciones-cronograma>
        <opciones-cronograma-por-parte v-else :get_cuota="get_cuota"></opciones-cronograma-por-parte>
    </Sidebar>

</template>

<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import timegrid from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'; // Importar el módulo de lista

import moment from 'moment';

import esLocale from '@fullcalendar/core/locales/es';

import $ from 'jquery';

import {
    myMixin
} from "../../mixin.js";

export default {
    mixins: [myMixin],
    components: {
        FullCalendar,
    },
    data() {
        return {
            get_cuota: null,
            is_opciones_modal: false,
            url_planilla_fecha: "",
            fecha_plantilla: "",
            leyenda: [],
            // cofiguracion del calendario
            calendarOptions: {
                plugins: [dayGridPlugin, interactionPlugin, timegrid, listPlugin],
                locales: [esLocale],
                locale: 'es',
                select: this.event_select,
                eventClick: this.eventClick,
                hiddenDays: [0],
                scrollTime: '18:00:00',
                initialView: 'dayGridMonth',
                slotMinTime: "09:00:00",
                slotMaxTime: "22:00:00",
                slotDuration: "00:30:00",
                touchSupport: true,
                eventLongPressDelay: 500,
                selectLongPressDelay: 500,
                contentHeight: 'auto',
                height: "auto",
                selectable: true,
                dayCount: 3,
                dayMaxEventRows: 4,
                daysInWeek: 4,
                nowIndicator: true,
                eventColor: '#FF2000',
                locale: 'es',
                navLinks: true,
                datesSet: this.onDatesSet,
                editable: true,
                selectable: true,
                displayEventTime: true,
                droppable: true,
                eventDrop: this.eventDrop,
                eventResize: this.eventDrop,
                drop: function (info) {
                    // is the "remove after drop" checkbox checked?
                    if (document.getElementById('drop-remove').checked) {
                        // if so, remove the element from the "Draggable Events" list
                        info.draggedEl.parentNode.removeChild(info.draggedEl);
                    }
                },
                businessHours: [ // specify an array instead
                    {
                        daysOfWeek: [1, 2, 3, 4, 5, 6], // Monday, Tuesday, Wednesday
                        startTime: '09:00', // 8am
                        endTime: '13:00', // 6pm
                        color: "#0F111A"
                    },
                    {
                        daysOfWeek: [1, 2, 3, 4, 5, 6], // Thursday, Friday
                        startTime: '15:00', // 10am
                        endTime: '20:00' // 4pm
                    }
                ],
                eventTimeFormat: { // like '14:30:00'
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                },
                slotLabelFormat: {
                    hour: '2-digit',
                    hour12: true
                }, //se visualizara de esta manera 01:00 AM en la columna de horas

                headerToolbar: {
                    start: 'title', // will normally be on the left. if RTL, will be on the right
                    center: 'dayGridMonth listWeek listDay',
                    end: 'today prev next' // will normally be on the right. if RTL, will be on the left
                },
                loading: function (isLoading) {
                    if (!isLoading) {
                        // Reset custom events
                        this.getEvents().forEach(function (e) {
                            if (e.source === null) {
                                e.remove();
                            }
                            console.log(e)
                        });
                    }
                }
            }
        }
    },
    methods: {
        onDatesSet(arg) {
            console.log('Dates Set:', arg.start, arg.end);
            var fecha = moment(arg.start).format('YYYY-MM-DD');
            var fecha_format = moment(arg.start).format('DD/MM/YYYY');
            console.log(fecha, "-", fecha_format);
            this.url_planilla_fecha = "/planilla_prestamos/" + fecha;
            this.fecha_plantilla = fecha_format;
        },
        async load_calendar() {
            const data = {};
            const headers = this.headers;

            try {
                const response = await axios.post("/load_calendar", data, { headers });

                console.log(response);

                if (response.data.success) {
                    this.calendarOptions.events = response.data.data.events;
                    this.leyenda = response.data.data.leyenda;

                    return true; // Retorna la data si todo es exitoso
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response.data.message,
                        footer: "-------",
                    });
                    console.error(response.data);
                    throw new Error(response.data.message); // Lanza un error si no hay éxito
                }
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Error 500",
                    text: "Error en el servidor, vuelva a intentar",
                    footer: "-------",
                });
                console.error(error);
                throw error; // Lanza el error para que pueda ser capturado en el `then` o `catch`
            }
        },
        eventClick(arg) {
            var event = arg.event;
            var get_cuota_event = event._def.extendedProps.cronograma;

            this.get_cuota = get_cuota_event;
            console.log(this.get_cuota);

            this.get_cuota.solicitud.solicitud_nombre = this.get_cuota.solicitud.solicitud_nombre.replace(/ /g, "-");

            this.is_opciones_modal = true;
        },
        event_select: function (arg) {
            console.log(arg);
            console.log(arg.end.getYear);

            //fecha start
            var start = arg.startStr.split("T")
            var fecha_start = start[0];
            var hora_start = start[1].split("-")[0];

            //fecha end
            var end = arg.endStr.split("T")
            var fecha_end = end[0];
            var hora_end = end[1].split("-")[0];

            //var Cx_Start = fecha_start + ' ' + hora_start;
            //var Cx_End = fecha_end + ' ' + hora_end;

        }
    },
    mounted() {

        this.load_calendar()
            .then((data) => {
                if (data) {
                    // this.loading_end();
                    console.log("El calendario se cargó correctamente");
                }
            })
            .catch((error) => {
                console.error("Hubo un error al cargar el calendario:", error);
            });

        $("#preload").fadeIn();
    },
}
</script>

<style></style>