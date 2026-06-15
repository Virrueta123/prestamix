<ul class="scrollable__content py-2">
    <li>
        <a href="{{ route('home') }}" class="menu">
            <div class="menu__icon"> <i data-lucide="home"></i> </div>
            <div class="menu__title"> Panel </div>
        </a>
    </li>

    <li>
        <a href="{{ route('simulacion') }}" class="menu">
            <div class="menu__icon"> <i class="fa-solid fa-calculator"></i> </div>
            <div class="menu__title"> Simulacion prestamo </div>
        </a>
    </li>

    <li>
        <a href="javascript:;" class="menu">
            <div class="menu__icon"> <i data-lucide="folder-open"></i> </div>
            <div class="menu__title">
                Solicitud
                <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>

        <ul>
            <li>
                <a href="{{ route('solicitud.create') }}" class="menu">
                    <div class="menu__icon"> <i data-lucide="plus"></i> </div>
                    <div class="menu__title"> Crear Solicitud </div>
                </a>
            </li>

            <li>
                <a href="{{ route('solicitud.cambiar_contrato') }}" class="menu">
                    <div class="menu__icon"> <i class="fa-solid fa-envelope-open-text"></i> </div>
                    <div class="menu__title"> Cambiar contrato </div>
                </a>
            </li>

            <li>
                <a href="{{ route('solicitud.tabla_solicitud_procesado') }}" class="menu">
                    <div class="menu__icon"> <i data-lucide="grid"></i> </div>
                    <div class="menu__title"> Tabla de Solicitudes Procesado </div>
                </a>
            </li>

            <li>
                <a href="{{ route('solicitud.tabla_solicitud_pendiente') }}" class="menu">
                    <div class="menu__icon"> <i data-lucide="grid"></i> </div>
                    <div class="menu__title"> Tabla de Solicitudes Pendientes </div>
                </a>
            </li>

            <li>
                <a href="{{ route('solicitud.tabla_solicitud_aprobado') }}" class="menu">
                    <div class="menu__icon"> <i data-lucide="grid"></i> </div>
                    <div class="menu__title"> Tabla de Solicitudes aprobadas </div>
                </a>
            </li>

            <li>
                <a href="{{ route('solicitud.tabla_solicitud_rechazado') }}" class="menu">
                    <div class="menu__icon"> <i data-lucide="grid"></i> </div>
                    <div class="menu__title"> Tabla de Solicitudes rechazadas </div>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="menu">
            <div class="menu__icon"> <i class="fa-solid fa-users"></i> </div>
            <div class="menu__title">
                Cliente
                <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>

        <ul>
            <li>
                <a href="{{ route('cliente.index') }}" class="menu">
                    <div class="menu__icon"> <i class="fa-solid fa-table-list"></i> </div>
                    <div class="menu__title"> Tabla cliente </div>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="menu">
            <div class="menu__icon"> <i class="fa-solid fa-cash-register"></i> </div>
            <div class="menu__title">
                Caja
                <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>

        <ul>
            <li>
                <a href="{{ route('caja.index') }}" class="menu">
                    <div class="menu__icon"> <i class="fa fa-table"></i> </div>
                    <div class="menu__title"> Toda las cajas </div>
                </a>
            </li>
            @role('gerente')
            <li>
                <a href="{{ route('caja.create') }}" class="menu">
                    <div class="menu__icon"> <i class="fa fa-plus"></i> </div>
                    <div class="menu__title"> crear una caja </div>
                </a>
            </li>
            @endrole
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="menu">
            <div class="menu__icon"> <i data-lucide="credit-card"></i> </div>
            <div class="menu__title">
                Cuentas
                <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>

        <ul>
            <li>
                <a href="{{ route('cuentas.index') }}" class="menu">
                    <div class="menu__icon"> <i class="fa fa-table"></i> </div>
                    <div class="menu__title"> Toda las cuentas </div>
                </a>
            </li>
            <li>
                <a href="{{ route('cuentas.create') }}" class="menu">
                    <div class="menu__icon"> <i class="fa fa-plus"></i> </div>
                    <div class="menu__title"> crear una cuenta </div>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="menu">
            <div class="menu__icon"> <i class="fa fa-arrow-trend-down"></i> </div>
            <div class="menu__title">
                Gastos
                <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>

        <ul>
            <li>
                <a href="{{ route('gastos.index') }}" class="menu">
                    <div class="menu__icon"> <i class="fa fa-table"></i> </div>
                    <div class="menu__title"> Toda los gastos </div>
                </a>
            </li>
            <li>
                <a href="{{ route('gastos.create') }}" class="menu">
                    <div class="menu__icon"> <i class="fa fa-plus"></i> </div>
                    <div class="menu__title"> crear un gasto </div>
                </a>
            </li>

            <li>
                <a href="{{ route('tipo_gasto.index') }}" class="menu">
                    <div class="menu__icon"> <i class="fa fa-table"></i> </div>
                    <div class="menu__title"> Todo los tipos de gastos </div>
                </a>
            </li>
            <li>
                <a href="{{ route('tipo_gasto.create') }}" class="menu">
                    <div class="menu__icon"> <i class="fa fa-plus"></i> </div>
                    <div class="menu__title"> crear un tipo de gasto </div>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="menu">
            <div class="menu__icon"> <i data-lucide="coins"></i> </div>
            <div class="menu__title">
                Prestamo
                <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>

        <ul>
            <li>
                <a href="{{ route('prestamo.planilla_prestamos') }}" class="menu">
                    <div class="menu__icon"> <i data-lucide="table"></i> </div>
                    <div class="menu__title"> tabla planilla </div>
                </a>
            </li>
            <li>
                <a href="{{ route('prestamo.table_mensajes_prestamo') }}" class="menu">
                    <div class="menu__icon"> <i data-lucide="message-circle"></i> </div>
                    <div class="menu__title"> tabla mensajes </div>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="menu">
            <div class="menu__icon"> <i data-lucide="shopping-cart"></i> </div>
            <div class="menu__title">
                Compras
                <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>

        <ul>
            <li>
                <a href="{{ route('compra.index') }}" class="menu">
                    <div class="menu__icon"> <i data-lucide="table"></i> </div>
                    <div class="menu__title">Toda las compras </div>
                </a>
            </li>
            <li>
                <a href="{{ route('compra.create') }}" class="menu">
                    <div class="menu__icon"> <i data-lucide="plus"></i> </div>
                    <div class="menu__title"> Crear una compra</div>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript:;" class="menu">
            <div class="menu__icon"> <i class="fa fa-people-group"></i> </div>
            <div class="menu__title">
                Recursos Humanos
                <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
            </div>
        </a>

        <ul>
            <li>
                <a href="{{ route('tabla_solicitud') }}" class="menu">
                    <div class="menu__icon"> <i class="fa-solid fa-table-list"></i> </div>
                    <div class="menu__title"> Tabla de solicitudes trabajadores </div>
                </a>
            </li>
            <li>
                <a href="{{ route('crear_solicitud_plantilla') }}" class="menu">
                    <div class="menu__icon"> <i class="fa fa-people-robbery"></i> </div>
                    <div class="menu__title"> Crear solicitud planilla </div>
                </a>
            </li>
        </ul>
    </li>

    @role('gerente')
        <li>
            <a href="javascript:;" class="menu">
                <div class="menu__icon"> <i data-lucide="users"></i> </div>
                <div class="menu__title">
                    Usuarios
                    <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                </div>
            </a>

            <ul>
                <li>
                    <a href="{{ route('usuario.create') }}" class="menu">
                        <div class="menu__icon"> <i data-lucide="user-plus"></i> </div>
                        <div class="menu__title"> Crear Usuario </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('usuario.index') }}" class="menu">
                        <div class="menu__icon"> <i data-lucide="table"></i> </div>
                        <div class="menu__title"> Tabla de usuario </div>
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="javascript:;" class="menu">
                <div class="menu__icon"> <i class="fa-solid fa-shop"></i> </div>
                <div class="menu__title">
                    Cautiva
                    <div class="menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                </div>
            </a>

            <ul>
                <li>
                    <a href="{{ route('cautiva.pos') }}" class="menu">
                        <div class="menu__icon"> <i class="fa-solid fa-bag-shopping"></i> </div>
                        <div class="menu__title"> Pos </div>
                    </a>
                </li>
            </ul>
        </li>
    @endrole
</ul>
