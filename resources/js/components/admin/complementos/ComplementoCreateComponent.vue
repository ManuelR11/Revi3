<template>
    <LoadingComponent :props="loading" />
    <SmSidebarModalCreateComponent :props="addButton" @click="addReset" />

    <div id="sidebar" class="drawer">
        <div class="drawer-header">
            <h3 class="drawer-title">{{ $t("menu.complementos") }}</h3>
            <button class="fa-solid fa-xmark close-btn" @click="reset"></button>
        </div>
        <div class="drawer-body">
            <form @submit.prevent="save">
                <div class="form-row">
                    <div class="form-col-12 sm:form-col-6">
                        <label for="name" class="db-field-title required">{{ $t("label.name") }}</label>
                        <input v-model="props.form.name" v-bind:class="errors.name ? 'invalid' : ''" type="text"
                            id="name" class="db-field-control">
                        <small class="db-field-alert" v-if="errors.name">{{ errors.name[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="extra_price" class="db-field-title required">{{ $t("label.extra_price") }}</label>
                        <input v-model="props.form.extra_price" v-bind:class="errors.extra_price ? 'invalid' : ''" 
                            type="text" id="extra_price" class="db-field-control" @keypress="numberOnly">
                        <small class="db-field-alert" v-if="errors.extra_price">{{ errors.extra_price[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label for="complemento_category_id" class="db-field-title required">{{ $t("label.category") }}</label>
                        <vue-select class="db-field-control f-b-custom-select" id="complemento_category_id"
                            v-bind:class="errors.complemento_category_id ? 'invalid' : ''"
                            v-model="props.form.complemento_category_id" :options="complementoCategories" label-by="name"
                            value-by="id" :closeOnSelect="true" :searchable="true" :clearOnClose="true" placeholder="--"
                            search-placeholder="--" />
                        <small class="db-field-alert" v-if="errors.complemento_category_id">{{
                            errors.complemento_category_id[0]
                            }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title">{{ $t("label.image") }}</label>
                        <input @change="changeImage" v-bind:class="errors.image ? 'invalid' : ''" id="image" type="file"
                            class="db-field-control" ref="imageProperty" accept="image/png, image/jpeg, image/jpg">
                        <small class="db-field-alert" v-if="errors.image">{{ errors.image[0] }}</small>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title" for="available">{{ $t("label.availability") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" v-model="props.form.is_available" id="available"
                                        :value="1" class="custom-radio-field">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="available" class="db-field-label">{{ $t('label.available') }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" class="custom-radio-field" v-model="props.form.is_available"
                                        id="unavailable" :value="0">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="unavailable" class="db-field-label">{{ $t('label.unavailable') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-col-12 sm:form-col-6">
                        <label class="db-field-title">{{ $t("label.status") }}</label>
                        <div class="db-field-radio-group">
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" v-model="props.form.status" id="active"
                                        :value="enums.statusEnum.ACTIVE" class="custom-radio-field">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="active" class="db-field-label">{{ $t('label.active') }}</label>
                            </div>
                            <div class="db-field-radio">
                                <div class="custom-radio">
                                    <input type="radio" class="custom-radio-field" v-model="props.form.status"
                                        id="inactive" :value="enums.statusEnum.INACTIVE">
                                    <span class="custom-radio-span"></span>
                                </div>
                                <label for="inactive" class="db-field-label">{{ $t('label.inactive') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-col-12">
                        <label for="description" class="db-field-title">{{ $t("label.description") }}</label>
                        <textarea v-model="props.form.description" v-bind:class="errors.description ? 'invalid' : ''"
                            id="description" rows="3" class="db-field-control" 
                            placeholder="Describe las características de este complemento..."></textarea>
                        <small class="db-field-alert" v-if="errors.description">{{
                            errors.description[0]
                            }}</small>
                    </div>

                    <div class="col-12">
                        <div class="flex flex-wrap gap-3 mt-4">
                            <button type="submit" class="db-btn py-2 text-white bg-primary">
                                <i class="lab lab-save"></i>
                                <span>{{ $t("label.save") }}</span>
                            </button>
                            <button type="button" class="modal-btn-outline modal-close" @click="reset">
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

export default {
    name: "ComplementoCreateComponent",
    components: {
        LoadingComponent,
        SmSidebarModalCreateComponent
    },
    props: ['props'],
    data() {
        return {
            loading: {
                isActive: false,
            },
            enums: {
                statusEnum: { ACTIVE: 5, INACTIVE: 10 }
            },
            addButton: {
                title: this.$t('label.add')
            },
            complementoCategories: [
                { id: 1, name: 'Salsas' },
                { id: 2, name: 'Quesos' },
                { id: 3, name: 'Proteínas Extra' },
                { id: 4, name: 'Vegetales' },
                { id: 5, name: 'Bebidas Extra' }
            ],
            errors: {}
        }
    },
    mounted() {
        this.reset();
    },
    methods: {
        numberOnly(event) {
            if (!/[0-9.]/.test(event.key) && !['Backspace', 'Delete', 'Tab', 'Escape', 'Enter'].includes(event.key)) {
                event.preventDefault();
            }
        },
        changeImage(event) {
            this.props.form.image = event.target.files[0];
        },
        reset() {
            this.props.form = {
                name: '',
                extra_price: '',
                complemento_category_id: null,
                image: null,
                is_available: 1,
                status: this.enums.statusEnum.ACTIVE,
                description: ''
            };
            this.errors = {};
        },
        addReset() {
            this.reset();
        },
        save() {
            this.loading.isActive = true;
            
            // Simulación de guardado
            setTimeout(() => {
                this.loading.isActive = false;
                this.$toast.success('¡Complemento creado exitosamente!');
                this.reset();
                
                // Cerrar modal
                document.getElementById('sidebar').classList.remove('drawer-open');
                
                // Recargar lista
                this.$emit('reload');
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
    display: inline-block;
}

.custom-radio-field {
    opacity: 0;
    position: absolute;
}

.custom-radio-span {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    margin-right: 8px;
    vertical-align: middle;
}

.custom-radio-field:checked + .custom-radio-span {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.custom-radio-field:checked + .custom-radio-span::after {
    content: '';
    display: block;
    width: 6px;
    height: 6px;
    background-color: white;
    border-radius: 50%;
    margin: 3px auto;
}
</style>