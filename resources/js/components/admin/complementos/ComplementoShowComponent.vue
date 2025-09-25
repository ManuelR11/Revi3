<template>
    <LoadingComponent :props="loading" />

    <div class="col-12">
        <div class="grid grid-cols-1 sm:grid-cols-1 mb-4 sm:mb-0">
            <button type="button" @click="handleTab($event, '#information', '.db-tabBtn', '.db-tabDiv', 'active')"
                class="db-tabBtn !justify-start active">
                <i class="lab lab-information lab-font-size-16"></i>
                {{ $t('label.information') }}
            </button>
        </div>
        
        <div class="db-tabDiv active" id="information">
            <div class="row py-2">
                <div class="col-12 sm:col-6 !py-1.5">
                    <div class="db-list-item p-0">
                        <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.name') }}</span>
                        <span class="db-list-item-text w-full sm:w-1/2">{{ complemento.name }}</span>
                    </div>
                </div>
                <div class="col-12 sm:col-6 !py-1.5">
                    <div class="db-list-item p-0">
                        <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.extra_price') }}</span>
                        <span class="db-list-item-text w-full sm:w-1/2">${{ parseFloat(complemento.extra_price || 0).toFixed(2) }}</span>
                    </div>
                </div>

                <div class="col-12 sm:col-6 !py-1.5">
                    <div class="db-list-item p-0">
                        <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.category') }}</span>
                        <span class="db-list-item-text w-full sm:w-1/2">{{ complemento.category_name || complemento.category }}</span>
                    </div>
                </div>

                <div class="col-12 sm:col-6 !py-1.5">
                    <div class="db-list-item p-0">
                        <span class="db-list-item-title w-full sm:w-1/2">{{ $t('label.status') }}</span>
                        <span class="db-list-item-text w-full sm:w-1/2">
                            <span :class="statusClass(complemento.status)">{{ enums.statusEnumArray[complemento.status] }}</span>
                        </span>
                    </div>
                </div>

                <div class="col-12 !py-1.5" v-if="complemento.description">
                    <div class="db-list-item p-0">
                        <span class="db-list-item-title w-full sm:w-1/6">{{ $t('label.description') }}</span>
                        <span class="db-list-item-text w-full sm:w-5/6">{{ complemento.description }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";

export default {
    name: "ComplementoShowComponent",
    components: {
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: { ACTIVE: 5, INACTIVE: 10 },
                statusEnumArray: { 5: this.$t('label.active'), 10: this.$t('label.inactive') }
            },
            complemento: {
                id: null,
                name: '',
                category: '',
                category_name: '',
                extra_price: 0,
                status: 5,
                description: ''
            }
        }
    },
    mounted() {
        this.show();
    },
    methods: {
        permissionChecker(permission) {
            return true;
        },
        statusClass(status) {
            return status === 5 ? 'text-[10px] text-green-700 bg-green-100 px-2 py-1 rounded capitalize' 
                                : 'text-[10px] text-red-700 bg-red-100 px-2 py-1 rounded capitalize';
        },
        handleTab(event, targetId, tabClass, contentClass, activeClass) {
            // Remover clase activa de todos los botones y contenidos
            document.querySelectorAll(tabClass).forEach(tab => {
                tab.classList.remove(activeClass);
            });
            document.querySelectorAll(contentClass).forEach(content => {
                content.classList.remove(activeClass);
            });

            // Agregar clase activa al botón clickeado y al contenido correspondiente
            event.target.classList.add(activeClass);
            document.querySelector(targetId).classList.add(activeClass);
        },
        show() {
            this.loading.isActive = true;
            
            // Obtener ID de la ruta
            const complementoId = this.$route.params.id;
            
            // Simular carga de datos del complemento
            setTimeout(() => {
                // Aquí simularemos buscar el complemento por ID
                // En un caso real, harías una llamada a la API
                const complementos = [
                    {
                        id: 1,
                        name: 'Salsa BBQ',
                        category: 'Obligatorio',
                        category_name: 'Obligatorio',
                        extra_price: 0.00,
                        status: 5,
                        description: 'Deliciosa salsa BBQ casera'
                    },
                    {
                        id: 2,
                        name: 'Queso Cheddar Extra',
                        category: 'Opcional',
                        category_name: 'Opcional',
                        extra_price: 3.00,
                        status: 5,
                        description: 'Queso cheddar premium adicional'
                    },
                    {
                        id: 3,
                        name: 'Pollo Extra',
                        category: 'Opcional',
                        category_name: 'Opcional',
                        extra_price: 5.00,
                        status: 5,
                        description: 'Porción adicional de pollo a la parrilla'
                    }
                ];

                const foundComplemento = complementos.find(c => c.id == complementoId);
                if (foundComplemento) {
                    this.complemento = { ...foundComplemento };
                } else {
                    // Si no se encuentra, usar datos por defecto
                    this.complemento = {
                        id: complementoId,
                        name: 'Complemento no encontrado',
                        category: 'N/A',
                        category_name: 'N/A',
                        extra_price: 0,
                        status: 10,
                        description: 'Este complemento no existe en el sistema'
                    };
                }
                
                this.loading.isActive = false;
            }, 1000);
        }
    }
}
</script>

