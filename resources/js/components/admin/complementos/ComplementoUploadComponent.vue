<template>
    <LoadingComponent :props="loading" />
    <div id="complementoUpload" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t('menu.complementos') }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                    @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label class="db-field-title required">{{ $t("label.upload_file") }} (XLSX)</label>
                            <input @change="changeFile" v-bind:class="errors.file ? 'invalid' : ''" id="file"
                                type="file" class="db-field-control" ref="fileProperty" accept=".xlsx, .xls" />
                            <small class="db-field-alert" v-if="errors.file">{{ errors.file[0] }}</small>
                        </div>

                        <div class="form-col-12">
                            <div class="modal-btns">
                                <button type="button" class="modal-btn-outline modal-close" @click="reset">
                                    <i class="lab lab-close"></i>
                                    <span>{{ $t("button.close") }}</span>
                                </button>

                                <button type="submit" class="db-btn py-2 text-white bg-primary">
                                    <i class="lab lab-save"></i>
                                    <span>{{ $t("button.save") }}</span>
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
import LoadingComponent from "../components/LoadingComponent.vue";
import alertService from "../../../services/alertService";
import appService from "../../../services/appService";

export default {
    name: "ComplementoUploadComponent",
    components: { LoadingComponent },
    emits: ['file-uploaded'],
    data() {
        return {
            loading: {
                isActive: false
            },
            errors: {},
            file: ""
        };
    },
    methods: {
        changeFile: function (e) {
            this.file = e.target.files[0];
        },
        reset: function () {
            appService.modalHide();
            this.errors = {};
            this.file = "";
            if (this.$refs.fileProperty) {
                this.$refs.fileProperty.value = null;
            }
        },
        save: function () {
            try {
                if (!this.file) {
                    alertService.error(this.$t('message.error_file_required'));
                    return;
                }

                this.loading.isActive = true;
                
                // Simulamos el proceso de carga del archivo
                setTimeout(() => {
                    this.loading.isActive = false;
                    appService.modalHide();
                    alertService.successFlip(0, this.$t('menu.complementos'));
                    this.$emit('file-uploaded');
                    this.file = "";
                    this.errors = {};
                    if (this.$refs.fileProperty) {
                        this.$refs.fileProperty.value = null;
                    }
                }, 2000);

            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err);
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