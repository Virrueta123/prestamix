<template>
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 2xl:col-span-9">
            <div class="grid grid-cols-12 gap-6">
                <!-- BEGIN: General Report -->

                <report-general-leyenda></report-general-leyenda>
                <!-- END: General Report -->
                <!-- BEGIN: Sales Report -->



            </div>
        </div>
        <div class="col-span-12 2xl:col-span-3">
            <div class="2xl:border-l -mb-10 pb-10">
                <div class="2xl:pl-6 grid grid-cols-12 gap-x-6 2xl:gap-x-0 gap-y-6">
                    <!-- BEGIN: Transactions -->
                    <report-limit-aprobados></report-limit-aprobados>
                    <!-- END: Transactions -->

                </div>
            </div>
        </div>
    </div>


    <div class="intro-y box p-5 mt-5">
        <calendario-planilla></calendario-planilla>
    </div>

</template>

<script>
import calendarioPlanilla from '../calendario/calendario-planilla.vue'
export default {
    components: { calendarioPlanilla },

}
</script>

<style></style>