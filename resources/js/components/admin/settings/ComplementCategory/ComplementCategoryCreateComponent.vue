<template>
    <LoadingComponent :props="loading" />
    <SmModalCreateComponent :props="addButton" />

    <div id="complementCategoryModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">{{ $t("label.complement_category") }}</h3>
                <button class="modal-close fa-solid fa-xmark text-xl text-slate-400 hover:text-red-500"
                    @click="reset"></button>
            </div>
            <div class="modal-body">
                <form @submit.prevent="save">
                    <div class="form-row">
                        <div class="form-col-12">
                            <label for="name" class="db-field-title required">{{ $t("label.name") }}</label>
                            <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                                id="name" class="db-field-control">
                            <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label for="image" class="db-field-title">{{ $t("label.image") }} (74px,48px)</label>
                            <input @change="changeImage" v-bind:class="errors.image ? 'invalid' : ''" id="image"
                                type="file" class="db-field-control" ref="imageProperty"
                                accept="image/png, image/jpeg, image/jpg">
                            <small class="db-field-alert" v-if="errors.image">{{ errors.image[0] }}</small>
                        </div>

                        <div class="form-col-12 sm:form-col-6">
                            <label class="db-field-title required" for="active_complement">{{ $t("label.status") }}</label>
                            <div class="db-field-radio-group">
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.ACTIVE" v-model="props.form.status" id="active_complement"
                                            type="radio" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="active_complement" class="db-field-label">{{ $t("label.active") }}</label>
                                </div>
                                <div class="db-field-radio">
                                    <div class="custom-radio">
                                        <input :value="enums.statusEnum.INACTIVE" v-model="props.form.status"
                                            type="radio" id="inactive_complement" class="custom-radio-field">
                                        <span class="custom-radio-span"></span>
                                    </div>
                                    <label for="inactive_complement" class="db-field-label">{{ $t("label.inactive") }}</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-col-12">
                            <label for="description" class="db-field-title">{{ $t("label.description") }}</label>
                            <textarea v-model="props.form.description"
                                v-bind:class="errors.description ? 'invalid' : ''" id="description"
                                class="db-field-control"></textarea>
                            <small class="db-field-alert" v-if="errors.description">{{ errors.description[0] }}</small>
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
import SmModalCreateComponent from "../../components/buttons/SmModalCreateComponent";
import LoadingComponent from "../../components/LoadingComponent";
import statusEnum from "../../../../enums/modules/statusEnum";
import alertService from "../../../../services/alertService";
import appService from "../../../../services/appService";

export default {
    name: "ComplementCategoryCreateComponent",
    components: { SmModalCreateComponent, LoadingComponent },
    props: ['props'],
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
            image: "",
            errors: {},
        }
    },
    computed: {
        addButton: function () {
            return { title: this.$t('button.add_complement_category') };
        },
    },
    methods: {
        changeImage: function (e) {
            this.image = e.target.files[0];
        },
        reset: function () {
            appService.modalHide();
            this.errors = {};
            this.$props.props.form = {
                name: "",
                description: "",
                status: statusEnum.ACTIVE
            }
            if (this.image) {
                this.image = "";
                if (this.$refs.imageProperty) {
                    this.$refs.imageProperty.value = null;
                }
            }
            // Limpiar el ID de edición en el componente padre
            this.$parent.editingCategoryId = null;
        },

        save: function () {
            try {
                if (!this.props.form.name) {
                    this.errors = { name: [this.$t('message.name_field_required')] };
                    return;
                }
                
                this.loading.isActive = true;
                
                // Simulamos el guardado
                setTimeout(() => {
                    this.loading.isActive = false;
                    appService.modalHide();
                    alertService.successFlip(0, this.$t('label.complement_category'));
                    
                    // Emitimos evento para que el componente padre actualice la lista
                    this.$emit('category-saved');
                    
                    this.props.form = {
                        name: "",
                        description: "",
                        status: statusEnum.ACTIVE,
                    }
                    this.image = "";
                    this.errors = {};
                    if (this.$refs.imageProperty) {
                        this.$refs.imageProperty.value = null;
                    }
                }, 1000);

            } catch (err) {
                this.loading.isActive = false;
                alertService.error(err)
            }
        }
    }
}
</script>