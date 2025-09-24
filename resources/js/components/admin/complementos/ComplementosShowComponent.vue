<template>
    <div class="db-card">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">🔍 {{ $t('menu.complementos') }} - Detalle</h3>
            <div class="db-card-action">
                <router-link 
                    :to="{ name: 'admin.complementos.list' }"
                    class="db-btn h-[37px] text-white bg-gray-600 hover:bg-gray-700"
                >
                    <i class="lab lab-back mr-2"></i>
                    <span>{{ $t('button.back') }}</span>
                </router-link>
            </div>
        </div>
        
        <div class="db-card-content">
            <!-- Mensaje de verificación -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="lab lab-information text-blue-500 text-xl mr-3"></i>
                    <div>
                        <h4 class="text-blue-800 font-medium">Vista de Detalle Funcionando</h4>
                        <p class="text-blue-700 text-sm mt-1">
                            ID del complemento: <strong>{{ $route.params.id }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 text-center">
                <div class="mb-4">
                    <i class="lab lab-addons text-5xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                    Complemento #{{ $route.params.id }}
                </h3>
                <p class="text-gray-600 mb-4">
                    Aquí se mostrará la información detallada del complemento seleccionado.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900">Estado</h4>
                        <p class="text-green-600 mt-1">✅ Activo</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900">Tipo</h4>
                        <p class="text-gray-600 mt-1">Complemento de ejemplo</p>
                    </div>
                </div>

                <button 
                    type="button" 
                    class="db-btn mt-6 text-white bg-primary hover:bg-primary/80"
                    @click="testFunction"
                >
                    <i class="lab lab-test mr-2"></i>
                    Probar Funcionalidad
                </button>
            </div>

            <!-- Debug info -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-700 mb-2">🔧 Información de Debug:</h4>
                <div class="text-xs text-gray-600 space-y-1">
                    <p><strong>Ruta actual:</strong> {{ $route.fullPath }}</p>
                    <p><strong>Parámetros:</strong> {{ JSON.stringify($route.params) }}</p>
                    <p><strong>Componente:</strong> ComplementosShowComponent.vue</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ComplementosShowComponent",
    data() {
        return {
            complemento: {
                id: this.$route.params.id,
                nombre: `Complemento ${this.$route.params.id}`,
                estado: 'activo'
            }
        }
    },
    mounted() {
        console.log('✅ ComplementosShowComponent montado');
        console.log('📍 ID del complemento:', this.$route.params.id);
        
        this.$toast.success(`Vista de detalle cargada para complemento #${this.$route.params.id}`);
    },
    methods: {
        testFunction() {
            this.$toast.success('¡Función de prueba ejecutada correctamente! 🎯');
        }
    }
}
</script>

<style scoped>
.db-card {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.db-btn {
    transition: all 0.2s ease-in-out;
}

.db-btn:hover {
    transform: translateY(-1px);
}
</style>