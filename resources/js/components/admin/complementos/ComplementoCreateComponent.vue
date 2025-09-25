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
                        <label for="extra_price" class="db-field-title required">{{ $t("label.extra_price") }}</label>
                        <input v-model="props.form.extra_price" v-bind:class="errors.extra_price ? 'invalid' : ''" 
                            type="text" id="extra_price" class="db-field-control" @keypress="numberOnly">
                        <small class="db-field-alert" v-if="errors.extra_price">{{ errors.extra_price }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="category" class="db-field-title required">{{ $t("label.category") }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="category"
                            v-bind:class="errors.category ? 'invalid' : ''"
                            v-model="props.form.category" :options="complementoCategories" label-by="name"
                            value-by="name" :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.category">{{ errors.category }}</small>
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
            complementoCategories: [
                { id: 1, name: 'Obligatorio' },
                { id: 2, name: 'Opcional' }
            ],
            errors: {},
            modalId: 'modal_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9)
        }
    },
    mounted() {
        this.reset();
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
                this.props.form.extra_price = '';
                this.props.form.category = 'Obligatorio';
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
            if (!this.props.form.extra_price && this.props.form.extra_price !== 0) {
                this.errors.extra_price = this.$t('message.extra_price_field_required');
                hasErrors = true;
            }
            if (!this.props.form.category) {
                this.errors.category = this.$t('message.category_field_required');
                hasErrors = true;
            }

            if (hasErrors) {
                return;
            }

            this.loading.isActive = true;
            
            // Simulación de guardado
            setTimeout(() => {
                this.loading.isActive = false;
                
                const complementoData = {
                    name: this.props.form.name,
                    category: this.props.form.category,
                    extra_price: parseFloat(this.props.form.extra_price) || 0,
                    status: parseInt(this.props.form.status),
                    description: this.props.form.description || ''
                };
                
                console.log('Datos a guardar:', complementoData);

                // Verificar si estamos editando o creando
                if (this.editingComplemento) {
                    // Modo edición - incluir el ID del complemento que se está editando
                    complementoData.id = this.editingComplemento.id;
                    this.$emit('complemento-updated', complementoData);
                } else {
                    // Modo creación
                    this.$emit('complemento-created', complementoData);
                }
                
                this.reset();
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