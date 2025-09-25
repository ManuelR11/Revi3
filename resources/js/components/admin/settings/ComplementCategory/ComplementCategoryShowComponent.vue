<template>
    <LoadingComponent :props="loading" />
    <div class="db-card">
        <div class="db-card-header">
            <h3 class="db-card-title">Categorías de Complementos</h3>
        </div>
        <div class="db-card-body">
            <div class="row">
                <div class="col-12 sm:col-5">
                    <img class="db-image" alt="category" :src="complementCategory.cover || '/images/default/category-placeholder.png'">
                </div>
                <div class="col-12 sm:col-7 md:pl-8">
                    <h3 class="text-lg font-medium capitalize mb-2 text-paragraph">{{ complementCategory.name }}</h3>
                    <label class="db-badge mb-3" :class="statusClass(complementCategory.status)">
                        {{ enums.statusEnumArray[complementCategory.status] }}
                    </label>
                    <p class="db-light-text">
                        {{ complementCategory.description }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import statusEnum from "../../../../enums/modules/statusEnum";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";

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
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: "Activo",
                    [statusEnum.INACTIVE]: "Inactivo"
                }
            },
            complementCategory: {}
        }
    },
    mounted() {
        this.loading.isActive = true;
        // Simulamos la carga de datos
        setTimeout(() => {
            const categoryId = this.$route.params.id;
            // Datos simulados basados en el ID
            this.complementCategory = {
                id: categoryId,
                name: 'Bebidas',
                status: statusEnum.ACTIVE,
                description: 'Categoría de bebidas complementarias para acompañar las comidas principales.',
                cover: '/images/default/category-placeholder.png'
            };
            this.loading.isActive = false;
        }, 500);
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        }
    }
}
</script>