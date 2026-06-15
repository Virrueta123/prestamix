<ul>
    <li>
        <a href="{{ route('home') }}" class="side-menu">
            <div class="side-menu__icon"> <i data-lucide="home"></i> </div>
            <div class="side-menu__title"> Panel </div>
        </a>
    </li>

    <li>
        <a href="{{ route('simulacion') }}" class="side-menu">
            <div class="side-menu__icon"> <i class="fa-solid fa-calculator"></i> </div>
            <div class="side-menu__title"> Simulacion prestamo </div>
        </a>
    </li>

    <li>
        <a href="javascript:;" class="side-menu">
            <div class="side-menu__icon"> <i data-lucide="folder-open"></i> </div>
            <div class="side-menu__title">
                Solicitud
                <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>


        <ul class="">
            <li>
                <a href="{{ route('solicitud.create') }}" class="side-menu">
                    <div class="side-menu__icon"> <i data-lucide="plus"></i> </div>
                    <div class="side-menu__title"> Crear Solicitud </div>
                </a>
            </li>

            <li>
                <a href="{{ route('solicitud.cambiar_contrato') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa-solid fa-envelope-open-text"></i> </div>
                    <div class="side-menu__title"> Cambiar contrato </div>
                </a>
            </li>

            <li>
                <a href="{{ route('solicitud.tabla_solicitud_procesado') }}" class="side-menu">
                    <div class="side-menu__icon"> <i data-lucide="grid"></i> </div>
                    <div class="side-menu__title"> Tabla de Solicitudes Procesado </div>
                </a>
            </li>
            <li>
                <a href="{{ route('solicitud.tabla_solicitud_pendiente') }}" class="side-menu">
                    <div class="side-menu__icon"> <i data-lucide="grid"></i> </div>
                    <div class="side-menu__title"> Tabla de Solicitudes Pendientes </div>
                </a>
            </li>

            <li>
                <a href="{{ route('solicitud.tabla_solicitud_aprobado') }}" class="side-menu">
                    <div class="side-menu__icon"> <i data-lucide="grid"></i> </div>
                    <div class="side-menu__title"> Tabla de Solicitudes aprobadas </div>
                </a>
            </li>

            <li>
                <a href="{{ route('solicitud.tabla_solicitud_rechazado') }}" class="side-menu ">
                    <div class="side-menu__icon"> <i data-lucide="grid"></i> </div>
                    <div class="side-menu__title "> Tabla de Solicitudes rechazadas </div>
                </a>
            </li>



        </ul>
    </li>


    <li>
        <a href="javascript:;" class="side-menu">
            <div class="side-menu__icon"> <i class="fa-solid fa-users"></i></div>
            <div class="side-menu__title">
                Cliente
                <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>


        <ul class="">
            <li>
                <a href="{{ route('cliente.index') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa-solid fa-table-list"></i></div>
                    <div class="side-menu__title"> Tabla cliente </div>
                </a>
            </li>

        </ul>
    </li>

    <li>
        <a href="javascript:;" class="side-menu">
            <div class="side-menu__icon"> <i class="fa-solid fa-cash-register"></i> </div>
            <div class="side-menu__title">
                Caja
                <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>


        <ul class="">
            <li>
                <a href="{{ route('caja.index') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa fa-table"></i> </div>
                    <div class="side-menu__title"> Toda las cajas </div>
                </a>
            </li>
            @role('gerente')
            <li>
                <a href="{{ route('caja.create') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa fa-plus"></i> </div>
                    <div class="side-menu__title"> crear una caja </div>
                </a>
            </li>
            @endrole
        </ul>
    </li>


    <li>
        <a href="javascript:;" class="side-menu">
            <div class="side-menu__icon"> <i data-lucide="credit-card"></i> </div>
            <div class="side-menu__title">
                Cuentas
                <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>


        <ul class="">
            <li>
                <a href="{{ route('cuentas.index') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa fa-table"></i> </div>
                    <div class="side-menu__title"> Toda las cuentas </div>
                </a>
            </li>
            <li>
                <a href="{{ route('cuentas.create') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa fa-plus"></i> </div>
                    <div class="side-menu__title"> crear una cuenta </div>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript:;" class="side-menu">
            <div class="side-menu__icon"> <i class="fa-solid fa-right-to-bracket"></i></div>
            <div class="side-menu__title">
                Ingresos
                <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>
        <ul class="">
            <li>
                <a href="{{ route('ingresos_varios.create') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa fa-plus"></i> </div>
                    <div class="side-menu__title"> Crear un ingreso ( varios )</div>
                </a>
            </li>
        </ul>
    </li>


    <li>
        <a href="javascript:;" class="side-menu">
            <div class="side-menu__icon"> <i class="fa fa-arrow-trend-down"></i> </div>
            <div class="side-menu__title">
                Gastos
                <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>


        <ul class="">
            <li>
                <a href="{{ route('gastos.index') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa fa-table"></i> </div>
                    <div class="side-menu__title"> Toda los gastos </div>
                </a>
            </li>
            <li>
                <a href="{{ route('gastos.create') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa fa-plus"></i> </div>
                    <div class="side-menu__title"> crear un gasto </div>
                </a>
            </li>

            <li>
                <a href="{{ route('tipo_gasto.index') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa fa-table"></i> </div>
                    <div class="side-menu__title"> Todo los tipos de gastos </div>
                </a>
            </li>
            <li>
                <a href="{{ route('tipo_gasto.create') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa fa-plus"></i> </div>
                    <div class="side-menu__title"> crear un tipo de gasto </div>
                </a>
            </li>
        </ul>
    </li>




    <li>
        <a href="javascript:;" class="side-menu">
            <div class="side-menu__icon"> <i data-lucide="coins"></i> </div>
            <div class="side-menu__title">
                Prestamo
                <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>

        <ul class="">
            <li>
                <a href="{{ route('prestamo.planilla_prestamos') }}" class="side-menu">
                    <div class="side-menu__icon"> <i data-lucide="table"></i> </div>
                    <div class="side-menu__title"> tabla planilla </div>
                </a>
            </li>
            <li>
                <a href="{{ route('prestamo.table_mensajes_prestamo') }}" class="side-menu">
                    <div class="side-menu__icon"> <i data-lucide="message-circle"></i> </div>
                    <div class="side-menu__title"> tabla mensajes </div>
                </a>
            </li>
        </ul>
    </li>


     <li>
        <a href="javascript:;" class="side-menu">
            <div class="side-menu__icon"> <i class="fa fa-people-group"></i> </div>
            <div class="side-menu__title">
                Recursos Humanos
                <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>
        <ul class="">
            <li>
                <a href="{{ route('tabla_solicitud') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa-solid fa-table-list"></i> </div>
                    <div class="side-menu__title"> Tabla de solicitudes trabajadores </div>
                </a>
            </li>
            <li>
                <a href="{{ route('crear_solicitud_plantilla') }}" class="side-menu">
                    <div class="side-menu__icon"> <i class="fa fa-people-robbery"></i> </div>
                    <div class="side-menu__title"> Crear solicitud planilla </div>
                </a>
            </li>
        </ul>
    </li>

    @role('gerente')
        <li>
            <a href="javascript:;" class="side-menu">
                <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                <div class="side-menu__title">
                    Usuarios
                    <div class="side-menu__sub-icon "> <i data-lucide="chevron-down"></i> </div>
                </div>
            </a>


            <ul class="">
                <li>
                    <a href="{{ route('usuario.create') }}" class="side-menu">
                        <div class="side-menu__icon"> <i data-lucide="user-plus"></i> </div>
                        <div class="side-menu__title"> Crear Usuario </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('usuario.index') }}" class="side-menu">
                        <div class="side-menu__icon"> <i data-lucide="table"></i> </div>
                        <div class="side-menu__title"> Tabla de usuario </div>
                    </a>
                </li>
            </ul>
        </li>
    @endrole


</ul>
