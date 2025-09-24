import axios from 'axios'
import appService from "../../services/appService";


export const offer = {
    namespaced: true,
    state: {
        lists: [],
        offerItems: [],
        page: {},
        pagination: [],
        show: {},
        temp: {
            temp_id: null,
            isEditing: false,
            type: null,         // 👈 nuevo (1=DISCOUNT, 2=COMBO)
            combo_price: null,  // 👈 nuevo
        },
    },
    getters: {
        lists: function (state) {
            return state.lists;
        },

        pagination: function (state) {
            return state.pagination
        },
        page: function(state) {
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
                let url = 'admin/offer';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url).then((res) => {
                    if(typeof payload.vuex === "undefined" || payload.vuex === true) {
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
                let url = '/admin/offer';
                const temp = context.getters.temp; // {isEditing, temp_id, type, combo_price}

                if (temp.isEditing) {
                    method = axios.post;
                    url = `/admin/offer/${temp.temp_id}`;
                }

                const fd = payload.form; // ya viene como FormData

                // Asegura que enviamos type
                if (!fd.has('type') && temp.type != null) {
                    fd.append('type', temp.type);
                }

                // Si es COMBO, asegura combo_price (y no relies solo en amount)
                // (Si tu formulario manda ambos, el backend validará según type)
                if (temp.type === 2 /* COMBO */) {
                    // si el form no lo mandó, intenta desde temp
                    if (!fd.has('combo_price') && temp.combo_price != null) {
                        fd.append('combo_price', temp.combo_price);
                    }
                }

                method(url, fd).then(res => {
                    context.dispatch('lists', payload.search).catch(() => {});
                    context.commit('reset');
                    resolve(res);
                }).catch(reject);
            });
        },
        edit(context, payload) {
            // payload: { id, type, combo_price }
            context.commit('temp', payload);
        },
        destroy: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`admin/offer/${payload.id}`).then((res) => {
                    context.dispatch('lists', payload.search).then().catch();
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        show: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`admin/offer/show/${payload}`).then((res) => {
                    context.commit('show', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeImage: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post(
                        `/admin/offer/change-image/${payload.id}`,
                        payload.form,
                        {
                            headers: {
                                "Content-Type": "multipart/form-data",
                            },
                        }
                    )
                    .then((res) => {
                        context.commit("show", res.data.data);
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
        reset: function (context) {
            context.commit('reset');
        },

        export: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = 'admin/offer/export';
                if (payload) {
                    url = url + appService.requestHandler(payload);
                }
                axios.get(url, {responseType: 'blob'}).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
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
            if(typeof payload !== "undefined" && payload !== null) {
                state.page = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total
                }
            }
        },
        temp(state, payload) {
            // payload puede ser: { id, type, combo_price }
            state.temp.temp_id = payload?.id ?? payload; // mantiene compatibilidad
            state.temp.isEditing = true;
            if (typeof payload === 'object' && payload !== null) {
                state.temp.type = payload.type ?? null;
                state.temp.combo_price = payload.combo_price ?? null;
            }
        },
        reset(state) {
            state.temp.temp_id = null;
            state.temp.isEditing = false;
            state.temp.type = null;         // 👈 limpia
            state.temp.combo_price = null;  // 👈 limpia
        },
        show(state, payload) {
            state.show = payload;
            // Cuando haces show (abrir en edición), aprovecha para llenar temp también
            state.temp.type = payload?.type ?? state.temp.type;
            state.temp.combo_price = payload?.combo_price ?? state.temp.combo_price;
        },
    },
}
