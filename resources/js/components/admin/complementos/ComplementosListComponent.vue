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
                    <button class="db-btn h-[37px] text-white bg-primary" @click="showCreateModal">
                        <i class="lab lab-add"></i>
                        <span>{{ $t('label.add') }} {{ $t('menu.complementos') }}</span>
                    </button>
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
                                <span class="font-medium text-primary">{{ complemento.currency_extra_price }}</span>
                            </td>
                            <td class="db-table-body-td">
                                <span :class="statusClass(complemento.status)">{{ enums.statusEnumArray[complemento.status] }}</span>
                            </td>
                            <td class="db-table-body-td hidden-print">
                                <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
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
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import TableLimitComponent from "../components/TableLimitComponent";
import FilterComponent from "../components/buttons/collapse/FilterComponent";
import ExportComponent from "../components/buttons/export/ExportComponent";
import PrintComponent from "../components/buttons/export/PrintComponent";
import ExcelComponent from "../components/buttons/export/ExcelComponent";
import SmIconSidebarModalEditComponent from "../components/buttons/SmIconSidebarModalEditComponent";
import SmIconDeleteComponent from "../components/buttons/SmIconDeleteComponent";
import PaginationSMBox from "../components/pagination/PaginationSMBox";
import PaginationTextComponent from "../components/pagination/PaginationTextComponent";
import PaginationBox from "../components/pagination/PaginationBox";

export default {
    name: "ComplementosListComponent",
    components: {
        LoadingComponent,
        TableLimitComponent,
        FilterComponent,
        ExportComponent,
        PrintComponent,
        ExcelComponent,
        SmIconSidebarModalEditComponent,
        SmIconDeleteComponent,
        PaginationSMBox,
        PaginationTextComponent,
        PaginationBox
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
                }
            },
            complementos: [],
            complementoCategories: [
                { id: 1, name: 'Salsas' },
                { id: 2, name: 'Quesos' },
                { id: 3, name: 'Proteínas Extra' },
                { id: 4, name: 'Vegetales' },
                { id: 5, name: 'Bebidas Extra' }
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
            }
        }
    },
    mounted() {
        this.list();
    },
    methods: {
        permissionChecker(permission) {
            return true; // Temporal - implementar permisos después
        },
        statusClass(status) {
            return status === 5 ? 'text-[10px] text-white bg-success px-2 py-1 rounded-2xl capitalize' 
                                : 'text-[10px] text-white bg-danger px-2 py-1 rounded-2xl capitalize';
        },
        list() {
            this.loading.isActive = true;
            // Datos de ejemplo
            setTimeout(() => {
                this.complementos = [
                    {
                        id: 1,
                        name: 'Salsa BBQ',
                        category_name: 'Salsas',
                        extra_price: 2.50,
                        currency_extra_price: '$2.50',
                        status: 5,
                        image: null
                    },
                    {
                        id: 2,
                        name: 'Queso Cheddar Extra',
                        category_name: 'Quesos',
                        extra_price: 3.00,
                        currency_extra_price: '$3.00',
                        status: 5,
                        image: null
                    },
                    {
                        id: 3,
                        name: 'Pollo Extra',
                        category_name: 'Proteínas Extra',
                        extra_price: 5.00,
                        currency_extra_price: '$5.00',
                        status: 5,
                        image: null
                    }
                ];
                this.pagination.total = this.complementos.length;
                this.loading.isActive = false;
            }, 500);
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
            console.log('Editando complemento:', complemento);
        },
        destroy(id) {
            if (confirm('¿Estás seguro de eliminar este complemento?')) {
                console.log('Eliminando complemento:', id);
            }
        },
        showCreateModal() {
            alert('Modal de crear complemento (próximamente)');
        },
        numberOnly(event) {
            if (!/[0-9.]/.test(event.key) && !['Backspace', 'Delete', 'Tab', 'Escape', 'Enter'].includes(event.key)) {
                event.preventDefault();
            }
        },
        xls() {
            console.log('Exportando a Excel');
        }
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