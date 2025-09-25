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
                            <SampleFileComponent @sample-file="sampleFile" />
                            <UploadFileComponent @upload="upload" />
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
                            <input id="price" v-on:keypress="numberOnly($event)" v-model="props.search.extra_price"
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
                                <span class="db-table-badge text-[10px] text-white bg-primary capitalize">
                                    {{ complemento.category_name }}
                                </span>
                            </td>
                            <td class="db-table-body-td">
                                <span class="font-medium text-primary">${{ parseFloat(complemento.extra_price || 0).toFixed(2) }}</span>
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
                    extra_price: '',
                    complemento_category_id: null,
                    status: null
                },
                form: {
                    name: '',
                    extra_price: '',
                    category: 'Obligatorio',
                    status: 5,
                    description: ''
                },
                errors: {}
            },
            complementos: [],
            complementoCategories: [
                { id: 1, name: 'Obligatorio', description: 'Complementos que vienen incluidos por defecto' },
                { id: 2, name: 'Opcional', description: 'Complementos que el cliente puede agregar por un costo extra' }
            ],
            pagination: {
                current_page: 1,
                per_page: 10,
                total: 0,
                last_page: 1
            },
            paginationPage: 1,
            printObj: {
                id: "print",
                popTitle: this.$t('menu.complementos')
            },
            editingComplemento: null
        }
    },
    mounted() {
        this.list();
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
            setTimeout(() => {
                this.complementos = [
                    {
                        id: 1,
                        name: 'Salsa BBQ',
                        category_name: 'Obligatorio',
                        extra_price: 0.00,
                        status: 5,
                        image: null,
                        description: 'Deliciosa salsa BBQ casera'
                    },
                    {
                        id: 2,
                        name: 'Queso Cheddar Extra',
                        category_name: 'Opcional',
                        extra_price: 3.00,
                        status: 5,
                        image: null,
                        description: 'Queso cheddar premium adicional'
                    },
                    {
                        id: 3,
                        name: 'Pollo Extra',
                        category_name: 'Opcional',
                        extra_price: 5.00,
                        status: 5,
                        image: null,
                        description: 'Porción adicional de pollo a la parrilla'
                    }
                ];
                this.pagination.total = this.complementos.length;
                this.loading.isActive = false;
            }, 500);
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
                extra_price: '',
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
            const categoryValue = complemento.category_name || complemento.category || 'Obligatorio';
            
            this.props.form = {
                name: complemento.name,
                extra_price: complemento.extra_price.toString(),
                category: categoryValue,
                status: complemento.status,
                description: complemento.description || ''
            };
            this.loading.isActive = false;
        },
        destroy(id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    
                    setTimeout(() => {
                        const index = this.complementos.findIndex(c => c.id === id);
                        if (index !== -1) {
                            const complementoName = this.complementos[index].name;
                            this.complementos.splice(index, 1);
                        } else {
                            this.$toast.error('No se pudo encontrar el complemento a eliminar');
                        }
                        this.loading.isActive = false;
                    }, 1000);

                } catch (err) {
                    this.loading.isActive = false;
                    this.$toast.error('Error al eliminar el complemento');
                }
            }).catch((err) => {
                this.loading.isActive = false;
            });
        },
        addComplementoToList(newComplemento) {
            this.complementos.unshift({
                id: Date.now(),
                name: newComplemento.name,
                category: newComplemento.category,
                category_name: newComplemento.category,
                extra_price: newComplemento.extra_price,
                status: newComplemento.status,
                description: newComplemento.description,
                created_at: new Date().toISOString()
            });

            this.editingComplemento = null;
        },
        updateComplementoInList(updatedComplemento) {
            const index = this.complementos.findIndex(c => c.id === updatedComplemento.id);

            if (index !== -1) {
                this.complementos[index] = {
                    ...this.complementos[index],
                    name: updatedComplemento.name,
                    category: updatedComplemento.category,
                    category_name: updatedComplemento.category,
                    extra_price: updatedComplemento.extra_price,
                    status: updatedComplemento.status,
                    description: updatedComplemento.description,
                    updated_at: new Date().toISOString()
                };
            } else {
                console.error('No se pudo encontrar el complemento a actualizar con ID:', updatedComplemento.id);
                console.error('IDs disponibles:', this.complementos.map(c => c.id));
            }
            
            this.editingComplemento = null;
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
                    this.$toast.success('¡Complementos exportados exitosamente!');
                } catch (err) {
                    this.loading.isActive = false;
                    this.$toast.error('Error al exportar complementos');
                }
            }, 1000);
        },
        sampleFile() {
            console.log('Descargando archivo de muestra para complementos');
            // Por ahora solo mostramos un mensaje
            this.$toast.info('Archivo de muestra para complementos en desarrollo');
        },
        upload() {
            console.log('Abriendo modal de importación de complementos');
            appService.sidebarModalToggle();
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