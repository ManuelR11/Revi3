<template>
    <LoadingComponent :props="loading" />

    <div id="sidebar" class="drawer">
        <div class="drawer-header">
            <h3 class="drawer-title">{{ $t("label.edit") }} {{ $t("menu.complementos") }}</h3>
            <button class="fa-solid fa-xmark close-btn" @click="closeModal"></button>
        </div>
        <div class="drawer-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="edit_name" class="db-field-title required">{{ $t("label.name") }}</label>
                        <input v-model="form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                            id="edit_name" class="db-field-control">
                        <small class="db-field-alert" v-if="errors.name">{{ errors.name }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="edit_extra_price" class="db-field-title required">{{ $t("label.extra_price") }}</label>
                        <input v-model="form.extra_price" v-bind:class="errors.extra_price ? 'invalid' : ''" 
                            type="text" id="edit_extra_price" class="db-field-control" @keypress="numberOnly">
                        <small class="db-field-alert" v-if="errors.extra_price">{{ errors.extra_price }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="edit_category" class="db-field-title required">{{ $t("label.category") }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="edit_category"
                            v-bind:class="errors.category ? 'invalid' : ''"
                            v-model="form.category" :options="complementoCategories" label-by="name"
                            value-by="name" :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.category">{{ errors.category }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title">{{ $t("label.status") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" v-model="form.status" id="edit_active"
                                        :value="enums.statusEnum.ACTIVE" class="custom-radio-field">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="edit_active" class="db-field-label">{{ $t('label.active') }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" class="custom-radio-field" v-model="form.status"
                                        id="edit_inactive" :value="enums.statusEnum.INACTIVE">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="edit_inactive" class="db-field-label">{{ $t('label.inactive') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-col-12">
                        <label for="edit_description" class="db-field-title">{{ $t("label.description") }}</label>
                        <textarea v-model="form.description" v-bind:class="errors.description ? 'invalid' : ''"
                            id="edit_description" rows="3" class="db-field-control" 
                            placeholder="Describe las características de este complemento..."></textarea>
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
import appService from "../../../services/appService";

export default {
    name: "ComplementoEditComponent",
    components: {
        LoadingComponent
    },
    props: ['complemento'],
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: { ACTIVE: 5, INACTIVE: 10 }
            },
            form: {
                id: null,
                name: '',
                extra_price: '',
                category: 'Obligatorio',
                status: 5,
                description: ''
            },
            complementoCategories: [
                { id: 1, name: 'Obligatorio' },
                { id: 2, name: 'Opcional' }
            ],
            errors: {}
        }
    },
    watch: {
        complemento: {
            immediate: true,
            handler(newComplemento) {
                if (newComplemento) {
                    this.loadComplemento(newComplemento);
                }
            }
        }
    },
    methods: {
        numberOnly(event) {
            if (!/[0-9.]/.test(event.key) && !['Backspace', 'Delete', 'Tab', 'Escape', 'Enter'].includes(event.key)) {
                event.preventDefault();
            }
        },
        loadComplemento(complemento) {
            console.log('Cargando complemento para editar:', complemento);
            this.form = {
                id: complemento.id,
                name: complemento.name || '',
                extra_price: complemento.extra_price ? complemento.extra_price.toString() : '0',
                category: complemento.category_name || complemento.category || 'Obligatorio',
                status: complemento.status === 'Activo' || complemento.status === 5 ? this.enums.statusEnum.ACTIVE : this.enums.statusEnum.INACTIVE,
                description: complemento.description || ''
            };
            this.errors = {};
            console.log('Form actualizado:', this.form);
        },
        reset() {
            this.form = {
                id: null,
                name: '',
                extra_price: '',
                category: 'Obligatorio',
                status: this.enums.statusEnum.ACTIVE,
                description: ''
            };
            this.errors = {};
        },
        closeModal() {
            this.reset();
            appService.sideDrawerHide();
        },
        save() {
            // Validación básica
            this.errors = {};
            let hasErrors = false;

            if (!this.form.name) {
                this.errors.name = 'El nombre es requerido';
                hasErrors = true;
            }
            if (!this.form.extra_price && this.form.extra_price !== 0) {
                this.errors.extra_price = 'El precio extra es requerido';
                hasErrors = true;
            }
            if (!this.form.category) {
                this.errors.category = 'La categoría es requerida';
                hasErrors = true;
            }

            if (hasErrors) {
                return;
            }

            this.loading.isActive = true;
            
            // Simulación de guardado
            setTimeout(() => {
                this.loading.isActive = false;
                
                // Emitir evento con los datos actualizados del complemento
                this.$emit('complemento-updated', {
                    id: this.form.id,
                    name: this.form.name,
                    category: this.form.category,
                    extra_price: parseFloat(this.form.extra_price) || 0,
                    status: this.form.status,
                    description: this.form.description || ''
                });
                
                this.reset();
                
                // Cerrar modal usando el appService
                appService.sideDrawerHide();
            }, 1000);
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