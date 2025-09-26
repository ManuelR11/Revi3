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
            errors: {},
        }
    },
    computed: {
        addButton: function () {
            return { title: this.$t('button.add_complement_category') };
        },
        isEditing: function () {
            const tempId = this.$store.getters['categoryComplement/temp'];
            return tempId && tempId !== null;
        }
    },
    methods: {
        reset: function () {
            appService.modalHide();
            this.errors = {};
            this.$props.props.form = {
                name: "",
                description: ""
            };
            // Limpiar los datos temporales del store
            this.$store.commit('categoryComplement/reset');
        },

        save: function () {
            this.loading.isActive = true;
            this.errors = {};

            const data = {
                name: this.props.form.name,
                description: this.props.form.description
            };

            // Si estamos editando, incluir el ID
            if (this.isEditing) {
                data.id = this.$store.getters['categoryComplement/temp'];
            }

            this.$store.dispatch('categoryComplement/save', data)
                .then(() => {
                    this.loading.isActive = false;
                    alertService.successFlip(
                        this.isEditing ? 1 : 0, 
                        this.$t('label.complement_category')
                    );
                    
                    // Emitimos evento para que el componente padre actualice la lista
                    this.$emit('category-saved');
                    
                    // Limpiar formulario
                    this.props.form = {
                        name: "",
                        description: ""
                    };
                })
                .catch((error) => {
                    this.loading.isActive = false;
                    
                    // Solo manejar errores que NO sean de autenticación
                    if (!error.response || (error.response.status !== 401 && error.response.status !== 403)) {
                        if (error.response && error.response.data && error.response.data.errors) {
                            this.errors = error.response.data.errors;
                        } else {
                            alertService.error(error.response?.data?.message || this.$t('message.error_saving_complement_category'));
                        }
                    }
                });
        }
    }
}
</script>