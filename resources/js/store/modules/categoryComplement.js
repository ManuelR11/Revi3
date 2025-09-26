import axios from 'axios'
import appService from "../../services/appService";

export const categoryComplement = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        pagination: [],
        show: {},
        temp: null,
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },
        pagination: function (state) {
            return state.pagination
        },
        page: function (state) {
            return state.page;
        },
        show: function (state) {
            return state.show;
        },
        temp: function (state) {
            return state.temp;
        }
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/category-complement';
                
                // Usar el formato que funciona: ?paginate=1&per_page=10
                const params = new URLSearchParams();
                params.append('paginate', '1');
                params.append('per_page', (payload && payload.per_page) ? payload.per_page : '10');
                
                if (payload && payload.page && payload.page > 1) {
                    params.append('page', payload.page);
                }
                if (payload && payload.search && payload.search.trim() !== '') {
                    params.append('search', payload.search);
                }
                
                url += '?' + params.toString();
                
                axios.get(url).then((res) => {
                    const dataToCommit = res.data.data || res.data;
                    context.commit('lists', dataToCommit);
                    
                    if (res.data.meta) {
                        context.commit('page', res.data.meta);
                        context.commit('pagination', res.data);
                    }
                    
                    resolve(res);
                }).catch((err) => {
                    // Solo rechazar errores que NO sean de autenticación
                    if (!err.response || (err.response.status !== 401 && err.response.status !== 403)) {
                        reject(err);
                    }
                });
            });
        },
        save: function (context, payload) {
            return new Promise((resolve, reject) => {
                let method = axios.post;
                let url = 'admin/category-complement';
                
                // Si tiene ID, es edición
                if (payload.id) {
                    method = axios.put;
                    url = `admin/category-complement/${payload.id}`;
                }
                
                method(url, payload).then(res => {
                    resolve(res);
                }).catch((err) => {
                    // Solo rechazar errores que NO sean de autenticación
                    if (!err.response || (err.response.status !== 401 && err.response.status !== 403)) {
                        reject(err);
                    }
                });
            });
        },
        edit: function (context, payload) {
            context.commit('temp', payload);
        },
        destroy: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/category-complement/${payload}`).then((res) => {
                    context.dispatch('lists', payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    // Solo rechazar errores que NO sean de autenticación
                    if (!err.response || (err.response.status !== 401 && err.response.status !== 403)) {
                        reject(err);
                    }
                });
            });
        },
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/category-complement/show/${payload}`).then((res) => {
                    // La respuesta viene como res.data.data según tu ejemplo
                    context.commit('show', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    // Solo rechazar errores que NO sean de autenticación
                    if (!err.response || (err.response.status !== 401 && err.response.status !== 403)) {
                        reject(err);
                    }
                });
            });
        },
        reset: function (context) {
            context.commit('reset');
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
            if (typeof payload !== "undefined" && payload !== null) {
                state.page = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total
                }
            }
        },
        show: function (state, payload) {
            state.show = payload;
        },
        temp: function (state, payload) {
            state.temp = payload;
        },
        reset: function (state) {
            state.temp = null;
        }
    },
}