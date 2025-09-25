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
                        <th class="db-table-head-th">{{ $t("label.description") }}</th>
                        <th class="db-table-head-th">{{ $t("label.action") }}</th>
                    </tr>
                </thead>
                <draggable tag="tbody" class="db-table-body" v-if="categories.length > 0" v-model="categories"
                    @end="sortCategory" :handle="'.drag-handle'">
                    <tr class="db-table-body-tr" v-for="complementCategory in categories" :key="complementCategory.id">
                        <td class="db-table-body-td"><i class="lab lab-move cursor-move drag-handle"></i></td>
                        <td class="db-table-body-td">{{ complementCategory.name }}</td>
                        <td class="db-table-body-td">{{ complementCategory.description || '-' }}</td>
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
                    description: ""
                },
                search: {
                    paginate: 1,
                    page: 1,
                    per_page: 10,
                }
            },

            ENV: ENV
        }
    },
    computed: {
        categories: function () {
            return this.$store.getters['categoryComplement/lists'] || [];
        },
        pagination: function () {
            return this.$store.getters['categoryComplement/pagination'];
        },
        paginationPage: function () {
            return this.$store.getters['categoryComplement/page'];
        }
    },
    mounted() {
        this.list();
    },
    methods: {
        textShortener: function (text, number = 30) {
            return appService.textShortener(text, number);
        },
        list: function (page = 1) {
            this.loading.isActive = true;
            
            // Construir parámetros válidos para PaginateRequest
            const searchParams = {
                page: page,
                per_page: this.props.search.per_page || 10
            };
            
            this.$store.dispatch('categoryComplement/lists', searchParams).then((res) => {
                this.loading.isActive = false;
            }).catch((err) => {
                this.loading.isActive = false;
                console.error('List error:', err);
                alertService.error(err.response?.data?.message || 'Error al cargar las categorías');
            });
        },

        edit: function (complementCategory) {
            appService.modalShow("#complementCategoryModal");
            this.$store.dispatch('categoryComplement/edit', complementCategory.id);
            this.props.form = {
                name: complementCategory.name,
                description: complementCategory.description
            };
        },
        handleCategorySaved: function() {
            // Recargar la lista para obtener los datos actualizados del servidor
            this.list();
            
            // Limpiar el formulario
            this.props.form = {
                name: '',
                description: ''
            };
            
            // Cerrar el modal
            appService.modalHide("#complementCategoryModal");
            
            // Mostrar notificación de éxito
            alertService.success(this.$t('complement_category_saved_successfully'));
        },
        destroy: function (id) {
            appService.destroyConfirmation().then((res) => {
                this.loading.isActive = true;
                this.$store.dispatch('categoryComplement/destroy', id)
                    .then(() => {
                        this.loading.isActive = false;
                        alertService.successFlip(null, this.$t('complement_category'));
                        this.list(); // Recargar la lista
                    })
                    .catch((error) => {
                        this.loading.isActive = false;
                        alertService.error(error.response?.data?.message || this.$t('error_deleting_complement_category'));
                    });
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
            try {
                // Crear datos CSV
                const csvData = this.categories.map(cat => 
                    `${cat.name},${cat.description || ''}`
                ).join('\n');
                
                const header = 'Nombre,Descripción\n';
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
                
                this.loading.isActive = false;
                alertService.success(this.$t('exported_successfully'));
            } catch (error) {
                this.loading.isActive = false;
                alertService.error(this.$t('export_error'));
            }
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