<template>
    <LoadingComponent :props="loading" />
    <div id="complementCategoryUpload" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">Categorías de Complementos</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                    @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label class="db-field-title required">Subir archivo (XLSX)</label>
                            <input @change="changeFile" v-bind:class="errors.file ? 'invalid' : ''" id="file"
                                type="file" class="db-field-control" ref="fileProperty" accept=".xlsx, .xls" />
                            <small class="db-field-alert" v-if="errors.file">{{ errors.file[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-close"></i>
                                    <span>Cerrar</span>
                                </button>

                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-save"></i>
                                    <span>Guardar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script>

import LoadingComponent from "../../components/LoadingComponent.vue";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";

export default {
    name: "ComplementCategoryUploadComponent",
    components: { LoadingComponent },
    emits:['list'],
    data() {
        return {
            loading: {
                isActive: false,
            },
            file: "",
            search: {
                paginate: 1,
                page: 1,
                per_page: 10,
                order_column: "id",
                order_type: "desc",
            },
            errors: {},
        };
    },
    methods: {
        reset: function () {
            appService.modalHide();
            this.file = "";
            this.errors = {};
            this.$refs.fileProperty.value = null;
        },
        changeFile: function (e) {
            this.file = e.target.files[0];
        },
        save: function () {
            try {
                this.loading.isActive = true;
                
                // Simulamos la importación
                setTimeout(() => {
                    this.loading.isActive = false;
                    appService.modalHide();
                    alertService.successFlip(0, this.$t("label.complement_category"));
                    
                    this.file = "";
                    this.errors = {};
                    this.$refs.fileProperty.value = null;
                    this.$emit('list');
                }, 1500);
                
            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
            }
        },
    },
};
</script>