import axios from "axios";

export const complement = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        pagination: [],
        show: {},
        temp: {
            name: "",
            description: "",
            price: "",
            status: "",
            categories: []
        },
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        pagination: function (state) {
            return state.pagination;
        },
        page: function (state) {
            return state.page;
        },
        show: function (state) {
            return state.show;
        },
        temp: function (state) {
            return state.temp;
        },
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/complement';
                if (payload && payload.page) {
                    context.commit('page', payload);
                    url = url + `?page=${payload.page}`;
                    if (payload.name) {
                        url += `&name=${payload.name}`;
                    }
                    if (payload.status !== null && payload.status !== undefined) {
                        url += `&status=${payload.status}`;
                    }
                    if (payload.category_id) {
                        url += `&category_id=${payload.category_id}`;
                    }
                } else {
                    context.commit('page', {});
                }
                
                axios.get(url).then((response) => {
                    context.commit('lists', response.data.data);
                    context.commit('pagination', response.data);
                    resolve(response);
                }).catch((error) => {
                    console.error('Error fetching complements:', error);
                    reject(error);
                });
            });
        },
        save: function (context, payload) {
            return new Promise((resolve, reject) => {
                console.log('Guardando complement con datos:', payload);
                
                const data = {
                    name: payload.name,
                    description: payload.description || '',
                    price: parseFloat(payload.price) || 0,
                    status: parseInt(payload.status),
                    categories: payload.categories || []
                };
                
                console.log('Datos preparados para API:', data);
                
                axios.post('admin/complement', data).then((response) => {
                    console.log('Respuesta del servidor:', response.data);
                    context.commit('temp', {
                        name: "",
                        description: "",
                        price: "",
                        status: "",
                        categories: []
                    });
                    resolve(response);
                }).catch((error) => {
                    console.error('Error guardando complement:', error);
                    if (error.response) {
                        console.error('Datos de respuesta del error:', error.response.data);
                        console.error('Status del error:', error.response.status);
                    }
                    reject(error);
                });
            });
        },
        edit: function (context, payload) {
            return new Promise((resolve, reject) => {
                console.log('Editando complement con datos:', payload);
                
                const data = {
                    name: payload.name,
                    description: payload.description || '',
                    price: parseFloat(payload.price) || 0,
                    status: parseInt(payload.status)
                };
                
                // Solo incluir categories si se proporcionan
                if (payload.categories && payload.categories.length > 0) {
                    data.categories = payload.categories;
                }
                
                console.log('Datos preparados para edición:', data);
                
                axios.put(`admin/complement/${payload.id}`, data).then((response) => {
                    console.log('Respuesta del servidor (edición):', response.data);
                    context.commit('temp', {
                        name: "",
                        description: "",
                        price: "",
                        status: "",
                        categories: []
                    });
                    resolve(response);
                }).catch((error) => {
                    console.error('Error editando complement:', error);
                    if (error.response) {
                        console.error('Datos de respuesta del error:', error.response.data);
                        console.error('Status del error:', error.response.status);
                    }
                    reject(error);
                });
            });
        },
        destroy: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/complement/${payload}`).then((response) => {
                    resolve(response);
                }).catch((error) => {
                    console.error('Error eliminando complement:', error);
                    reject(error);
                });
            });
        },
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/complement/show/${payload}`).then((response) => {
                    context.commit('show', response.data.data);
                    resolve(response);
                }).catch((error) => {
                    console.error('Error obteniendo complement:', error);
                    reject(error);
                });
            });
        },
        temp: function (context, payload) {
            context.commit('temp', payload);
        },
    },
    mutations: {
        lists: function (state, payload) {
            state.lists = payload;
        },
        pagination: function (state, payload) {
            state.pagination = payload;
        },
        page: function (state, payload) {
            state.page = payload;
        },
        show: function (state, payload) {
            state.show = payload;
        },
        temp: function (state, payload) {
            state.temp = payload;
        },
    },
};