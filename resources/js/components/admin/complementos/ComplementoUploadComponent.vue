<template>
    <div id="sidebar" :class="sidebar">
        <div class="db-sidebar-body">
            <div class="db-sidebar-header border-b border-gray-100 px-6 py-2 flex items-center justify-between">
                <h3 class="db-sidebar-title">{{ $t('button.upload_complementos') }}</h3>
                <button class="w-7 h-7 leading-7 text-center rounded-full transition-all duration-300 hover:bg-gray-50 hover:text-primary"
                    @click.prevent="appService.sidebarModalToggle()">
                    <i class="lab lab-close-line text-xl"></i>
                </button>
            </div>
        </div>
        <form class="db-sidebar-body" @submit.prevent="save">
            <div class="form-row">
                <div class="form-col-12">
                    <label class="db-field-title after:hidden">{{ $t("label.upload_file") }} <span class="text-red-500">*</span></label>
                    <input type="file" class="db-field-control" @change="changeFile" ref="fileInput" accept=".csv,.xlsx,.xls" required>
                    <small class="db-field-alert" id="file">{{ errors.file && errors.file[0] }}</small>
                </div>
            </div>
            <div class="form-row" v-if="fileName">
                <div class="form-col-12">
                    <div class="alert alert-primary mb-4 p-3 rounded bg-blue-50 border border-blue-200">
                        <div class="flex items-center">
                            <i class="lab lab-file-text-line text-blue-600 mr-2"></i>
                            <span class="text-blue-800">{{ fileName }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-col-12">
                    <div class="flex items-center gap-4 pt-4">
                        <button type="button" class="modal-btn-outline modal-close"
                            @click.prevent="appService.sidebarModalToggle()">
                            <i class="lab lab-close-line"></i>
                            <span>{{ $t("button.close") }}</span>
                        </button>
                        <button type="submit" class="modal-btn-one" :disabled="loading.isActive">
                            <i class="lab lab-save-line"></i>
                            <span>{{ $t("button.save") }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import appService from "../../../services/appService";

export default {
    name: "ComplementoUploadComponent",
    data() {
        return {
            loading: {
                isActive: false
            },
            sidebar: 'w-96 fixed inset-y-0 left-0 z-50 bg-white shadow-xl transform -translate-x-full transition-transform duration-300 ease-in-out',
            errors: {},
            fileName: '',
            uploadFile: null
        };
    },
    mounted() {
        this.sidebarModalHandle();
    },
    methods: {
        appService,
        sidebarModalHandle() {
            document.addEventListener('click', (e) => {
                if (e.target.id === 'sidebar') {
                    this.appService.sidebarModalToggle();
                }
            });
        },
        changeFile(e) {
            this.uploadFile = e.target.files[0];
            this.fileName = this.uploadFile ? this.uploadFile.name : '';
            console.log('Archivo seleccionado:', this.fileName);
        },
        save() {
            if (!this.uploadFile) {
                this.$toast.error(this.$t('message.please_select_file'));
                return;
            }

            console.log('Procesando archivo de complementos:', this.fileName);
            this.loading.isActive = true;

            // Simulamos el proceso de carga del archivo
            setTimeout(() => {
                this.loading.isActive = false;
                this.$toast.success('Archivo de complementos procesado correctamente');
                this.$emit('file-uploaded');
                this.reset();
                this.appService.sidebarModalToggle();
            }, 2000);
        },
        reset() {
            this.uploadFile = null;
            this.fileName = '';
            this.errors = {};
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        }
    }
};
</script>

<style scoped>
.alert {
    border-radius: 0.5rem;
}
</style>