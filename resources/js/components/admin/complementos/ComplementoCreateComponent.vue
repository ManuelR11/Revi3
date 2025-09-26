<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset" />

    <div id="sidebar" class="drawer">
        <div class="drawer-header">
            <h3 class="drawer-title">{{ $t("menu.complementos") }}</h3>
            <button class="fa-solid fa-xmark close-btn" @click="closeModal"></button>
        </div>
        <div class="drawer-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="name" class="db-field-title required">{{ $t("label.name") }}</label>
                        <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                            id="name" class="db-field-control">
                        <small class="db-field-alert" v-if="errors.name">{{ errors.name }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="price" class="db-field-title required">{{ $t("label.price") }}</label>
                        <input v-model="props.form.price" v-bind:class="errors.price ? 'invalid' : ''" 
                            type="text" id="price" class="db-field-control" @keypress="numberOnly">
                        <small class="db-field-alert" v-if="errors.price">{{ errors.price }}</small>
                    </div>

                    <div class="form-col-12">
                        <label for="categories" class="db-field-title required">{{ $t("label.categories") }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="categories"
                            v-bind:class="errors.categories ? 'invalid' : ''"
                            v-model="props.form.categories" :options="complementoCategories" label-by="name"
                            value-by="id" :multiple="true" :closeOnSelect="false" :searchable="true" :clearOnClose="true" :placeholder="$t('label.select_complements_category')" 
                            :search-placeholder="$t('label.search_complements_category')" />
                        <small class="db-field-alert" v-if="errors.categories">{{ errors.categories }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title">{{ $t("label.status") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" v-model="props.form.status" :id="'active_' + modalId"
                                        :value="enums.statusEnum.ACTIVE" class="custom-radio-field" name="status">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label :for="'active_' + modalId" class="db-field-label">{{ $t('label.active') }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" class="custom-radio-field" v-model="props.form.status"
                                        :id="'inactive_' + modalId" :value="enums.statusEnum.INACTIVE" name="status">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label :for="'inactive_' + modalId" class="db-field-label">{{ $t('label.inactive') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-col-12">
                        <label for="description" class="db-field-title">{{ $t("label.description") }}</label>
                        <textarea v-model="props.form.description" v-bind:class="errors.description ? 'invalid' : ''"
                            id="description" rows="3" class="db-field-control" 
                            :placeholder="$t('label.complement_description')"></textarea>
                        <small class="db-field-alert" v-if="errors.description">{{ errors.description }}</small>
                    </div>

                    <div class="col-12">
                        <div class="flex flex-wrap gap-3 mt-4">
                            <button type="submit" class="db-btn py-2 text-white bg-primary">
                                <i class="lab lab-save"></i>
                                <span>{{ $t("label.save") }}</span>
                            </button>
                            <button type="button" class="modal-btn-outline modal-close" @click="closeModal">
                                <i class="lab lab-close"></i>
                                <span>{{ $t("button.close") }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import LoadingComponent from "../components/LoadingComponent";
import SmSidebarModalCreateComponent from "../components/buttons/SmSidebarModalCreateComponent";
import appService from "../../../services/appService";
import alertService from "../../../services/alertService";

export default {
    name: "ComplementoCreateComponent",
    components: {
        LoadingComponent,
        SmSidebarModalCreateComponent
    },
    props: ['props', 'editingComplemento'],
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: { ACTIVE: 5, INACTIVE: 10 }
            },
            addButton: {
                title: this.$t('label.add_complements')
            },

            errors: {},
            modalId: 'modal_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9)
        }
    },
    computed: {
        complementoCategories: function() {
            return this.$store.getters['categoryComplement/lists'] || [];
        }
    },
    mounted() {
        this.reset();
        // Cargar las categorías de complementos
        this.$store.dispatch('categoryComplement/lists', {});
    },
    watch: {
        editingComplemento: {
            handler(newVal) {
                if (newVal) {
                    console.log('Modo edición activado, forzando actualización del status:', newVal.status);
                    // Forzar la actualización del status
                    this.$nextTick(() => {
                        this.props.form.status = parseInt(newVal.status);
                    });
                } else {
                    console.log('Modo creación activado');
                }
            },
            immediate: true,
            deep: true
        },
        'props.form.status': {
            handler(newVal) {
                console.log('Status cambió a:', newVal);
            }
        }
    },
    methods: {
        numberOnly(event) {
            if (!/[0-9.]/.test(event.key) && !['Backspace', 'Delete', 'Tab', 'Escape', 'Enter'].includes(event.key)) {
                event.preventDefault();
            }
        },
        reset() {
            // Limpiar errores primero
            this.errors = {};
            
            // Resetear form con $nextTick para asegurar reactividad
            this.$nextTick(() => {
                this.props.form.name = '';
                this.props.form.price = '';
                this.props.form.categories = [];
                this.props.form.status = this.enums.statusEnum.ACTIVE;
                this.props.form.description = '';
                console.log('Form reseteado, status:', this.props.form.status);
            });
        },
        addReset() {
            this.reset();
            // Notificar al componente padre que estamos en modo creación
            this.$emit('clear-editing-state');
        },
        closeModal() {
            this.reset();
            // Cerrar modal usando el appService
            appService.sideDrawerHide();
        },
        save() {
            // Validación básica
            this.errors = {};
            let hasErrors = false;

            if (!this.props.form.name) {
                this.errors.name = this.$t('message.name_field_required');
                hasErrors = true;
            }
            if (!this.props.form.price && this.props.form.price !== 0) {
                this.errors.price = this.$t('message.price_field_required');
                hasErrors = true;
            }
            if (!this.props.form.categories || this.props.form.categories.length === 0) {
                this.errors.categories = this.$t('message.categories_field_required');
                hasErrors = true;
            }

            if (hasErrors) {
                return;
            }

            this.loading.isActive = true;
            
            const complementoData = {
                name: this.props.form.name,
                price: parseFloat(this.props.form.price) || 0,
                status: parseInt(this.props.form.status),
                description: this.props.form.description || '',
                categories: this.props.form.categories || []
            };
            
            console.log('Datos a guardar:', complementoData);

            // Verificar si estamos editando o creando
            if (this.editingComplemento) {
                // Modo edición - incluir el ID del complemento que se está editando
                complementoData.id = this.editingComplemento.id;
                
                this.$store.dispatch('complement/edit', complementoData).then((response) => {
                    this.loading.isActive = false;
                    console.log('Complemento actualizado:', response.data);
                    this.$emit('complemento-updated', complementoData);
                    this.reset();
                    appService.sideDrawerHide();
                }).catch((error) => {
                    this.loading.isActive = false;
                    console.error('Error actualizando complemento:', error);
                    if (error.response && error.response.data && error.response.data.errors) {
                        this.errors = error.response.data.errors;
                    } else {
                        alertService.error(this.$t('message.error_updating_complement'));
                    }
                });
            } else {
                // Modo creación
                this.$store.dispatch('complement/save', complementoData).then((response) => {
                    this.loading.isActive = false;
                    console.log('Complemento creado:', response.data);
                    this.$emit('complemento-created', complementoData);
                    this.reset();
                    appService.sideDrawerHide();
                }).catch((error) => {
                    this.loading.isActive = false;
                    console.error('Error creando complemento:', error);
                    if (error.response && error.response.data && error.response.data.errors) {
                        this.errors = error.response.data.errors;
                    } else {
                        alertService.error(this.$t('message.error_creating_complement'));
                    }
                });
            }
        }
    }
}
</script>

<style scoped>
.db-field-alert {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.invalid {
    border-color: #ef4444;
}

.custom-radio {
    position: relative;
    display: inline-flex;
    align-items: center;
    flex-shrink: 0;
}

.custom-radio-field {
    opacity: 0;
    position: absolute;
    width: 18px;
    height: 18px;
    margin: 0;
    cursor: pointer;
}

.custom-radio-span {
    position: relative;
    display: inline-block;
    width: 18px;
    height: 18px;
    min-width: 18px;
    min-height: 18px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    margin-right: 8px;
    transition: all 0.2s ease;
    flex-shrink: 0;
    box-sizing: border-box;
}

.custom-radio-field:checked + .custom-radio-span {
    background-color: rgb(252, 77, 37);
    border-color: rgb(252, 77, 37);
}

.custom-radio-field:checked + .custom-radio-span::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 6px;
    height: 6px;
    background-color: white;
    border-radius: 50%;
}

.custom-radio-field:hover + .custom-radio-span {
    border-color: rgb(252, 77, 37);
}
</style>