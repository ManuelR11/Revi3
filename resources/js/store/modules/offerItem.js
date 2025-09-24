import axios from 'axios'
import appService from "../../services/appService";


export const offerItem = {
    namespaced: true,
    state: {
        lists: [],
        page: {},
        pagination: [],
        show: {},
        temp: {
            temp_id: null,
            isEditing: false,
        },
        form: {
            item_id: null,
            quantity: 1,
        },
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
        },
        form: (state) => state.form, // 👈 nuevo
    },
    actions: {
        lists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/offer/item/${payload.id}`;
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit('lists', res.data.data);
                        context.commit('page', res.data.meta);
                        context.commit('pagination', res.data);
                    }

                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function (context, payload) {
            return new Promise((resolve, reject) => {
                let method = axios.post;
                let url = `/admin/offer/item/${payload.id}`;
                if (context.state.temp.isEditing) {
                    method = axios.put;
                    url = `/admin/offer/item/${payload.id}/${context.state.temp.temp_id}`;
                }

                let fd;
                if (payload.form instanceof FormData) {
                    fd = payload.form;
                } else {
                    fd = new FormData();
                    if (payload.form && typeof payload.form === 'object') {
                        if (payload.form.item_id != null) fd.append('item_id', payload.form.item_id);
                        if (payload.form.quantity != null) fd.append('quantity', payload.form.quantity);
                    } else {
                        // fallback: toma del store.form
                        if (context.state.form.item_id != null) fd.append('item_id', context.state.form.item_id);
                        if (context.state.form.quantity != null) fd.append('quantity', context.state.form.quantity);
                    }
                }

                // Por si viniera incompleto, asegura que ambos estén
                if (!fd.has('item_id') && context.state.form.item_id != null) {
                    fd.append('item_id', context.state.form.item_id);
                }
                if (!fd.has('quantity') && context.state.form.quantity != null) {
                    fd.append('quantity', context.state.form.quantity);
                }

                method(url, fd)
                    .then(res => {
                    context.dispatch('lists', payload.search).catch(() => {});
                    context.commit('reset');
                    resolve(res);
                    })
                    .catch(reject);
            });
        },
        edit: function (context, payload) {
            context.commit('temp', payload);
        },
        destroy: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/offer/item/${payload.offer}/${payload.id}`).then((res) => {
                    context.dispatch('lists', payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        reset: function (context) {
            context.commit('reset');
        },
    },
    mutations: {
        lists: function (state, payload) {
            state.lists = payload
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
            state.temp.temp_id = payload;
            state.temp.isEditing = true;
        },
        setForm(state, payload) {        // 👈 nuevo
            // payload puede ser { item_id, quantity }
            state.form.item_id = payload?.item_id ?? state.form.item_id;
            state.form.quantity = payload?.quantity ?? state.form.quantity;
        },
        reset(state) {
            state.temp.temp_id = null;
            state.temp.isEditing = false;
            // limpia también el form
            state.form.item_id = null;     // 👈 nuevo
            state.form.quantity = 1;       // 👈 nuevo
        },
    },
}
