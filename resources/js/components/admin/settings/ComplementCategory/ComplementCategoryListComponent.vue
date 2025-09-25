<template>
    <LoadingComponent :props="loading" />

    <div class="db-card db-tab-div active">
        <div class="db-card-header border-none">
            <h3 class="db-card-title">{{ $t("menu.complement_categories") }}</h3>
            <div class="db-card-filter">
                <TableLimitComponent :method="list" :search="props.search" :page="paginationPage" />
                <div class="dropdown-group">
                    <ExportComponent />
                    <div class="dropdown-list db-card-filter-dropdown-list">
                        <ExcelComponent :method="xls" />
                    </div>
                </div>
                <div class="dropdown-group">
                    <ImportComponent />
                    <div class="dropdown-list db-card-filter-dropdown-list">
                        <SampleFileComponent @click="downloadSample" />
                        <UploadFileComponent :dataModal="'complementCategoryUpload'" @click="uploadModal('#complementCategoryUpload')" />
                    </div>
                </div>
                <ComplementCategoryCreateComponent :props="props" @category-saved="handleCategorySaved" />
                <ComplementCategoryUploadComponent v-on:list="list" />
            </div>
        </div>

        <div class="db-table-responsive">
            <table class="db-table stripe">
                <thead class="db-table-head">
                    <tr class="db-table-head-tr">
                        <th class="db-table-head-th"><i class="lab lab-list"></i></th>
                        <th class="db-table-head-th">{{ $t("label.name") }}</th>
                        <th class="db-table-head-th">{{ $t("label.status") }}</th>
                        <th class="db-table-head-th">{{ $t("label.action") }}</th>
                    </tr>
                </thead>
                <draggable tag="tbody" class="db-table-body" v-if="categories.length > 0" v-model="categories"
                    @end="sortCategory" :handle="'.drag-handle'">
                    <tr class="db-table-body-tr" v-for="complementCategory in categories" :key="complementCategory">
                        <td class="db-table-body-td"><i class="lab lab-move cursor-move drag-handle"></i></td>
                        <td class="db-table-body-td">{{ complementCategory.name }}</td>
                        <td class="db-table-body-td">
                            <span :class="statusClass(complementCategory.status)">
                                {{ enums.statusEnumArray[complementCategory.status] }}
                            </span>
                        </td>
                        <td class="db-table-body-td">
                            <div class="flex justify-start items-center sm:items-start sm:justify-start gap-1.5">
                                <SmViewComponent :link="'admin.settings.complementCategory.show'" :id="complementCategory.id" />
                                <SmModalEditComponent @click="edit(complementCategory)" />
                                <SmDeleteComponent @click="destroy(complementCategory.id)" />
                            </div>
                        </td>
                    </tr>
                </draggable>
                <tbody class="db-table-body" v-else>
                    <tr class="db-table-body-tr">
                        <td class="db-table-body-td text-center" colspan="7">
                            <div class="p-4">
                                <div class="max-w-[300px] mx-auto mt-2">
                                    <img class="w-full h-full" :src="ENV.API_URL + '/images/default/not-found.png'"
                                        alt="Not Found">
                                </div>
                                <span class="d-block mt-3 text-lg">No hay datos disponibles</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-6"
            v-if="categories.length > 0">
            <PaginationSMBox :pagination="pagination" :method="list" />
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <PaginationTextComponent :props="{ page: paginationPage }" />
                <PaginationBox :pagination="pagination" :method="list" />
            </div>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../../components/LoadingComponent";
import ComplementCategoryCreateComponent from "./ComplementCategoryCreateComponent";
import alertService from "../../../../services/alertService";
import PaginationTextComponent from "../../components/pagination/PaginationTextComponent";
import PaginationBox from "../../components/pagination/PaginationBox";
import PaginationSMBox from "../../components/pagination/PaginationSMBox";
import appService from "../../../../services/appService";
import statusEnum from "../../../../enums/modules/statusEnum";
import TableLimitComponent from "../../components/TableLimitComponent";
import SmDeleteComponent from "../../components/buttons/SmDeleteComponent";
import SmModalEditComponent from "../../components/buttons/SmModalEditComponent";
import SmViewComponent from "../../components/buttons/SmViewComponent";
import { VueDraggableNext } from 'vue-draggable-next'
import ExportComponent from "../../components/buttons/export/ExportComponent";
import SampleFileComponent from "../../components/buttons/import/SampleFileComponent.vue";
import UploadFileComponent from "../../components/buttons/import/UploadFileComponent.vue";
import ImportComponent from "../../components/buttons/import/ImportComponent.vue";
import ComplementCategoryUploadComponent from './ComplementCategoryUploadComponent.vue';
import ExcelComponent from "../../components/buttons/export/ExcelComponent";
import ENV from "../../../../config/env";

export default {
    name: "ComplementCategoryListComponent",
    components: {
        TableLimitComponent,
        PaginationSMBox,
        PaginationBox,
        PaginationTextComponent,
        ComplementCategoryCreateComponent,
        LoadingComponent,
        SmDeleteComponent,
        SmModalEditComponent,
        SmViewComponent,
        draggable: VueDraggableNext,
        ExcelComponent,
        UploadFileComponent,
        ExportComponent,
        SampleFileComponent,
        ImportComponent,
        ComplementCategoryUploadComponent
    },
    data() {
        return {
            loading: {
                isActive: false
            },
            enums: {
                statusEnum: statusEnum,
                statusEnumArray: {
                    [statusEnum.ACTIVE]: this.$t("label.active"),
                    [statusEnum.INACTIVE]: this.$t("label.inactive")
                }
            },
            props: {
                form: {
                    name: "",
                    status: statusEnum.ACTIVE,
                    description: ""
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                    order_column: 'sort',
                    order_type: 'asc',
                }
            },
            categories: [],
            editingCategoryId: null,
            ENV: ENV
        }
    },
    computed: {
        complementCategories: function () {
            // Por ahora simulamos datos hasta que tengamos el store
            return this.categories;
        },
        pagination: function () {
            // Simulamos paginación
            return {
                current_page: 1,
                last_page: 1,
                per_page: 10,
                total: this.categories.length
            };
        },
        paginationPage: function () {
            return 1;
        }
    },
    mounted() {
        this.list();
    },
    methods: {
        statusClass: function (status) {
            return appService.statusClass(status);
        },
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            // Simulamos datos de categorías de complementos con persistencia local
            setTimeout(() => {
                // Cargar datos desde localStorage o usar datos por defecto
                const savedCategories = localStorage.getItem('complementCategories');
                if (savedCategories) {
                    this.categories = JSON.parse(savedCategories);
                } else {
                    this.categories = [
                        {
                            id: 1,
                            name: 'Bebidas',
                            status: statusEnum.ACTIVE,
                            description: 'Categoría de bebidas complementarias'
                        },
                        {
                            id: 2,
                            name: 'Postres',
                            status: statusEnum.ACTIVE,
                            description: 'Categoría de postres complementarios'
                        },
                        {
                            id: 3,
                            name: 'Salsas',
                            status: statusEnum.INACTIVE,
                            description: 'Categoría de salsas adicionales'
                        }
                    ];
                    // Guardar datos iniciales
                    localStorage.setItem('complementCategories', JSON.stringify(this.categories));
                }
                this.loading.isActive = false;
            }, 500);
        },
        saveToLocalStorage: function() {
            localStorage.setItem('complementCategories', JSON.stringify(this.categories));
        },
        edit: function (complementCategory) {
            appService.modalShow("#complementCategoryModal");
            this.loading.isActive = true;
            // Guardamos el ID para saber que estamos editando
            this.editingCategoryId = complementCategory.id;
            this.props.form = {
                name: complementCategory.name,
                status: complementCategory.status,
                description: complementCategory.description
            };
            this.loading.isActive = false;
        },
        handleCategorySaved: function() {
            if (this.editingCategoryId) {
                // Estamos editando
                const index = this.categories.findIndex(cat => cat.id === this.editingCategoryId);
                if (index !== -1) {
                    this.categories[index] = {
                        ...this.categories[index],
                        name: this.props.form.name,
                        status: this.props.form.status,
                        description: this.props.form.description
                    };
                }
                this.editingCategoryId = null;
            } else {
                // Estamos creando
                const newCategory = {
                    id: Math.max(...this.categories.map(c => c.id)) + 1,
                    name: this.props.form.name,
                    status: this.props.form.status,
                    description: this.props.form.description
                };
                this.categories.push(newCategory);
            }
            this.saveToLocalStorage();
            this.list();
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                try {
                    this.loading.isActive = true;
                    // Simulamos eliminación
                    setTimeout(() => {
                        this.categories = this.categories.filter(cat => cat.id !== id);
                        this.saveToLocalStorage();
                        this.loading.isActive = false;
                        alertService.successFlip(null, 'Categoría de complemento');
                    }, 500);
                } catch (err) {
                    this.loading.isActive = false;
                    alertService.error('Error al eliminar la categoría');
                }
            }).catch((err) => {
                this.loading.isActive = false;
            })
        },
        sortCategory: function () {
            console.log('Ordenando categorías de complementos');
            // Por ahora solo un log, luego implementar con API
        },
        xls: function () {
            this.loading.isActive = true;
            // Simulamos exportación
            setTimeout(() => {
                this.loading.isActive = false;
                // Crear datos CSV simulados
                const csvData = this.categories.map(cat => 
                    `${cat.name},${cat.status === statusEnum.ACTIVE ? 'Activo' : 'Inactivo'},${cat.description}`
                ).join('\n');
                
                const header = 'Nombre,Estado,Descripción\n';
                const fullCsv = header + csvData;
                
                const blob = new Blob([fullCsv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement("a");
                const url = URL.createObjectURL(blob);
                
                link.setAttribute("href", url);
                link.setAttribute("download", "complement_categories.csv");
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 1000);
        },
        uploadModal: function (id) {
            appService.modalShow(id);
        },
        downloadSample: function () {
            this.loading.isActive = true;
            setTimeout(() => {
                this.loading.isActive = false;
                // Crear archivo de muestra
                const sampleData = 'Nombre,Estado,Descripción\nBebidas Frías,1,Categoría de bebidas frías\nPostres Especiales,1,Categoría de postres especiales';
                const blob = new Blob([sampleData], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement("a");
                const url = URL.createObjectURL(blob);
                
                link.setAttribute("href", url);
                link.setAttribute("download", "complement_categories_sample.csv");
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }, 1000);
        }
    }
}
</script>