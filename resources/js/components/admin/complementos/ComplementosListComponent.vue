<template>
    <LoadingComponent :props="loading" />
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header border-none">
                <h3 class="db-card-title">{{ $t('menu.complementos') }}</h3>
                <div class="db-card-filter">
                    <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                    <FilterComponent @click.prevent="handleSlide('complemento-filter')" />
                    <div class="dropdown-group">
                        <ExportComponent />
                        <div
                            class="dropdown-list db-card-filter-dropdown-list transition-all duration-300 scale-y-0 origin-top">
                            <PrintComponent :props="printObj" />
                            <ExcelComponent :method="xls" />
                        </div>
                    </div>
                    <div class="dropdown-group">
                        <ImportComponent />
                        <div
                            class="dropdown-list db-card-filter-dropdown-list transition-all duration-300 scale-y-0 origin-top">
                            <SampleFileComponent @click="sampleFile" />
                            <UploadFileComponent :dataModal="'complementoUpload'" @click="uploadModal('#complementoUpload')" />
                        </div>
                    </div>
                    <ComplementoCreateComponent :props="props" :editingComplemento="editingComplemento" @complemento-created="addComplementoToList" @complemento-updated="updateComplementoInList" @clear-editing-state="clearEditingState" />
                </div>
            </div>

            <div class="table-filter-div" id="complemento-filter">
                <form class="p-4 sm:p-5 mb-5" @submit.prevent="search">
                    <div class="row">
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="name" class="db-field-title after:hidden">{{
                                $t("label.name")
                            }}</label>
                            <input id="name" v-model="props.search.name" type="text" class="db-field-control" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="price" class="db-field-title after:hidden">{{
                                $t("label.extra_price")
                            }}</label>
                            <input id="price" v-on:keypress="numberOnly($event)" v-model="props.search.price"
                                type="text" class="db-field-control" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="complemento_category_id" class="db-field-title">{{
                                $t("label.category")
                            }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="complemento_category_id"
                                v-model="props.search.complemento_category_id" :options="complementoCategories" label-by="name"
                                value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true"
                                placeholder="--" search-placeholder="--" />
                        </div>
                        <div class="col-12 sm:col-6 md:col-4 xl:col-3">
                            <label for="status" class="db-field-title">{{ $t("label.status") }}</label>
                            <vue-select class="db-field-control f-b-custom-select" id="status"
                                v-model="props.search.status" :options="[
                                    { id: enums.statusEnum.ACTIVE, name: $t('label.active') },
                                    { id: enums.statusEnum.INACTIVE, name: $t('label.inactive') }
                                ]" label-by="name" value-by="id" :closeOnSelect="true" :searchable="true"
                                :clearOnClose="true" placeholder="--" search-placeholder="--" />
                        </div>
                        <div class="col-12">
                            <div class="flex flex-wrap gap-3 mt-3 justify-start">
                                <button class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-line-search"></i>
                                    <span>{{ $t("button.search") }}</span>
                                </button>
                                <button class="db-btn py-2 text-white bg-gray-600" type="button" @click="clear">
                                    <i class="lab lab-clear"></i>
                                    <span>{{ $t("button.clear") }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="db-table-responsive">
                <table class="db-table stripe" id="print">
                    <thead class="db-table-head">
                        <tr class="db-table-head-tr">
                            <th class="db-table-head-th">{{ $t('label.name') }}</th>
                            <th class="db-table-head-th">{{ $t('label.category') }}</th>
                            <th class="db-table-head-th">{{ $t('label.extra_price') }}</th>
                            <th class="db-table-head-th">{{ $t('label.status') }}</th>
                            <th class="db-table-head-th hidden-print">{{ $t('label.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="db-table-body" v-if="complementos.length > 0">
                        <tr class="db-table-body-tr" v-for="complemento in complementos" :key="complemento">
                            <td class="db-table-body-td">
                                <div class="flex items-center gap-2">
                                    <img class="w-[34px] h-[34px] rounded-lg flex-shrink-0 object-cover"
                                         :src="complemento.image" alt="complemento" v-if="complemento.image">
                                    <div class="w-[34px] h-[34px] rounded-lg flex-shrink-0 bg-gray-200 flex items-center justify-center" v-else>
                                        <i class="lab lab-addons text-gray-500"></i>
                                    </div>
                                    <span class="line-clamp-1">{{ complemento.name }}</span>
                                </div>
                            </td>
                            <td class="db-table-body-td">
                                <span v-if="complemento.categories && complemento.categories.length > 0">
                                    <span v-for="(category, index) in complemento.categories" :key="category.id" 
                                          class="db-table-badge text-[10px] text-white bg-primary capitalize mr-1">
                                        {{ category.name }}
                                    </span>
                                </span>
                                <span v-else class="db-table-badge text-[10px] text-gray-500 bg-gray-100 capitalize">
                                    Sin categoría
                                </span>
                            </td>
                            <td class="db-table-body-td">
                                <span class="font-medium text-primary">${{ parseFloat(complemento.price || 0).toFixed(2) }}</span>
                            </td>
                            <td class="db-table-body-td">
                                <span :class="statusClass(complemento.status)">{{ enums.statusEnumArray[complemento.status] }}</span>
                            </td>
                            <td class="db-table-body-td hidden-print">
                                <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                    <SmIconViewComponent :link="'admin.complemento.show'" :id="complemento.id" />
                                    <SmIconSidebarModalEditComponent @click="edit(complemento)" />
                                    <SmIconDeleteComponent @click="destroy(complemento.id)" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6">
                <PaginationSMBox :pagination="pagination" :method="list" />
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <PaginationTextComponent :props="{ page: paginationPage, limit: pagination.per_page, total: pagination.total }" />
                    <PaginationBox :pagination="pagination" :method="list" />
                </div>
            </div>
        </div>
    </div>

    <ComplementoUploadComponent @file-uploaded="list" />
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import TableLimitComponent from "../components/TableLimitComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import PrintComponent from "../components/buttons/export/PrintComponent";
import ExcelComponent from "../components/buttons/export/ExcelComponent";
import ComplementoCreateComponent from "./ComplementoCreateComponent.vue";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent";
import SmIconViewComponent from "../components/buttons/SmIconViewComponent";
import appService from "../../../services/appService";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";
import SampleFileComponent from "../components/buttons/import/SampleFileComponent";
import UploadFileComponent from "../components/buttons/import/UploadFileComponent";
import ImportComponent from "../components/buttons/import/ImportComponent";
import ComplementoUploadComponent from "./ComplementoUploadComponent.vue";
import alertService from "../../../services/alertService";

export default {
    name: "ComplementosListComponent",
    components: {
        LoadingComponent,
        TableLimitComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent,
        ComplementoCreateComponent,
        SmIconViewComponent,
        SmIconSidebarModalEditComponent,
        SmIconDeleteComponent,
        PaginationSMBox,
        PaginationTextComponent,
        PaginationBox,
        SampleFileComponent,
        UploadFileComponent,
        ImportComponent,
        ComplementoUploadComponent
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
            props: {
                search: {
                    paginate: 10,
                    page: 1,
                    per_page: 10,
                    order_column: 'id',
                    order_type: 'desc',
                    name: '',
                    price: '',
                    complemento_category_id: null,
                    status: null
                },
                form: {
                    name: '',
                    price: '',
                    categories: [],
                    status: 5,
                    description: ''
                },
                errors: {}
            },

            paginationPage: 1,
            printObj: {
                id: "print",
                popTitle: this.$t('menu.complementos')
            },
            editingComplemento: null
        }
    },
    computed: {
        complementoCategories: function() {
            return this.$store.getters['categoryComplement/lists'] || [];
        },
        complementos() {
            return this.$store.getters['complement/lists'];
        },
        pagination() {
            return this.$store.getters['complement/pagination'];
        }
    },
    mounted() {
        this.list();
        // Cargar las categorías de complementos
        this.$store.dispatch('categoryComplement/lists', {});
    },
    methods: {
        permissionChecker(permission) {
            return true;
        },
        statusClass(status) {
            return status === 5 ? 'text-[10px] text-green-700 bg-green-100 px-2 py-1 rounded capitalize' 
                                : 'text-[10px] text-red-700 bg-red-100 px-2 py-1 rounded capitalize';
        },

        list() {
            this.loading.isActive = true;

            const searchData = {
                page: this.paginationPage,
                name: this.props.search.name,
                status: this.props.search.status,
                category_id: this.props.search.complemento_category_id
            };

            // Agregar price a la búsqueda si está presente
            if (this.props.search.price) {
                searchData.price = this.props.search.price;
            }

            this.$store.dispatch('complement/lists', searchData).then((response) => {
                this.loading.isActive = false;
            }).catch((error) => {
                this.loading.isActive = false;
                
                if (!error.response || (error.response.status !== 401 && error.response.status !== 403)) {
                    console.error('Error cargando complementos:', error);
                    alertService.error(this.$t('message.error_loading_complements'));
                }
            });
        },
        handleSlide: function (id) {
            return appService.handleSlide(id);
        },
        search() {
            this.list();
        },
        clear() {
            this.props.search = {
                paginate: 10,
                page: 1,
                per_page: 10,
                order_column: 'id',
                order_type: 'desc',
                name: '',
                price: '',
                complemento_category_id: null,
                status: null
            };
            this.list();
        },
        edit(complemento) {
            this.editingComplemento = complemento;            
            appService.sideDrawerShow();
            this.loading.isActive = true;
            this.props.errors = {};            
            
            // Extraer IDs de categorías del complemento
            const categoryIds = complemento.categories ? complemento.categories.map(cat => cat.id) : [];
            
            this.props.form = {
                name: complemento.name,
                price: complemento.price.toString(),
                categories: categoryIds,
                status: complemento.status,
                description: complemento.description || ''
            };
            this.loading.isActive = false;
        },
        destroy(id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    
                    this.$store.dispatch('complement/destroy', id).then((response) => {
                        this.loading.isActive = false;
                        alertService.successFlip(2, this.$t('menu.complementos'));
                        this.list(); // Recargar la lista
                    }).catch((error) => {
                        this.loading.isActive = false;
                        console.error('Error eliminando complemento:', error);
                        alertService.error(this.$t('message.error_deleting_complement'));
                    });

                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(this.$t('message.error_deleting_complement'));
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        addComplementoToList(newComplemento) {
            alertService.successFlip(0, this.$t('menu.complementos'));
            this.editingComplemento = null;
            this.list(); // Recargar la lista desde la API
        },
        updateComplementoInList(updatedComplemento) {
            alertService.successFlip(1, this.$t('menu.complementos'));
            this.editingComplemento = null;
            this.list(); // Recargar la lista desde la API
        },
        clearEditingState() {
            this.editingComplemento = null;
        },
        numberOnly(event) {
            if (!/[0-9.]/.test(event.key) && !['Backspace', 'Delete', 'Tab', 'Escape', 'Enter'].includes(event.key)) {
                event.preventDefault();
            }
        },
        xls: function () {
            this.loading.isActive = true;
            
            setTimeout(() => {
                try {
                    const headers = ['ID', 'Name', 'Category', 'Extra Price', 'Status', 'Description'];
                    const csvContent = [
                        headers.join(','),
                        ...this.complementos.map(complemento => [
                            complemento.id,
                            `"${complemento.name}"`,
                            `"${complemento.category_name || complemento.category}"`,
                            complemento.extra_price,
                            complemento.status === 5 ? 'Active' : 'Inactive',
                            `"${complemento.description || ''}"`
                        ].join(','))
                    ].join('\n');

                    const blob = new Blob([csvContent], {
                        type: "text/csv;charset=utf-8;",
                    });
                    const link = document.createElement("a");
                    link.href = URL.createObjectURL(blob);
                    link.download = this.$t("menu.complementos") + ".csv";
                    link.click();
                    URL.revokeObjectURL(link.href);

                    this.loading.isActive = false;
                    alertService.success(this.$t('message.success_exporting_complements'));
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error(this.$t('message.error_exporting_complements'));
                }
            }, 1000);
        },
        sampleFile() {
            console.log('Descargando archivo de muestra para complementos');
            try {
                // Crear un archivo de muestra CSV
                const headers = ['name', 'category', 'extra_price', 'status', 'description'];
                const sampleData = [
                    ['Salsa BBQ', 'Opcional', '2.50', '5', 'Deliciosa salsa BBQ casera'],
                    ['Queso Extra', 'Opcional', '3.00', '5', 'Queso cheddar premium adicional'],
                    ['Pollo Extra', 'Obligatorio', '5.00', '5', 'Porción adicional de pollo a la parrilla']
                ];
                
                const csvContent = [
                    headers.join(','),
                    ...sampleData.map(row => row.map(cell => `"${cell}"`).join(','))
                ].join('\n');

                const blob = new Blob([csvContent], {
                    type: "text/csv;charset=utf-8;",
                });
                const link = document.createElement("a");
                link.href = URL.createObjectURL(blob);
                link.download = "complementos_sample.csv";
                link.click();
                URL.revokeObjectURL(link.href);

                alertService.success(this.$t('message.success_downloading_sample'));
            } catch (err) {
                alertService.error(this.$t('message.error_downloading_sample'));
            }
        },
        upload() {
            console.log('Abriendo modal de importación de complementos');
            this.uploadModal('#complementoUpload');
        },
        uploadModal: function (id) {
            return appService.modalShow(id);
        },
    }
}
</script>

<style scoped>
.alert {
    border-radius: 0.5rem;
}

.card {
    transition: transform 0.2s;
}

.btn {
    border-radius: 0.375rem;
}
</style>