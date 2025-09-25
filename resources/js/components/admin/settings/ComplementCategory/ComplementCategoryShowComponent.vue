<template>
    <LoadingComponent :props="loading" />
    <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">Categorías de Complementos</h3>
        </div>
        <div class="db-card-body">
            <div class="row">
                <div class="col-12">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-2xl font-medium capitalize mb-2 text-paragraph">{{ complementCategory.name }}</h3>
                            <p class="text-sm text-gray-500">ID: {{ complementCategory.id }}</p>
                        </div>
                        
                        <div v-if="complementCategory.description">
                            <h4 class="text-lg font-medium mb-2">{{ $t('label.description') }}</h4>
                            <p class="db-light-text">{{ complementCategory.description }}</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">{{ $t('label.created_at') }}</h4>
                                <p class="text-sm text-gray-500">{{ complementCategory.created_at }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-700">{{ $t('label.updated_at') }}</h4>
                                <p class="text-sm text-gray-500">{{ complementCategory.updated_at }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import alertService from "../../../../services/alertService";

export default {
    name: "ComplementCategoryShowComponent",
    components: {
        LoadingComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },

        }
    },
    computed: {
        complementCategory: function () {
            return this.$store.getters['categoryComplement/show'] || {};
        }
    },
    mounted() {
        this.loading.isActive = true;
        const categoryId = this.$route.params.id;
        
        this.$store.dispatch('categoryComplement/show', categoryId)
            .then(() => {
                this.loading.isActive = false;
            })
            .catch((error) => {
                this.loading.isActive = false;
                alertService.error(error.response?.data?.message || 'Error al cargar la categoría');
            });
    },
    methods: {
        // Los métodos se pueden agregar aquí si son necesarios
    }
}
</script>