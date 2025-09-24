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
        save(context, payload) {
            return new Promise((resolve, reject) => {
                let method = axios.post;
                let url = '/admin/offer';

                const temp = context.getters.temp;   // SOLO para saber si es edición + id
                const fd   = payload.form;           // FormData ya construido en el componente

                if (temp.isEditing) {
                    url = `/admin/offer/${temp.temp_id}`;
                }

                // 🔒 Asegura que el tipo se envía: si el form no lo trae, falla rápido (mejor que inventarlo)
                const type = fd.get('type');
                if (type === null || type === '') {
                    return reject(new Error('Offer type is required in FormData.'));
                }

                // 🧠 Si es COMBO y no vino combo_price en el form, no inventes desde temp: deja que el backend valide
                // (Opcional) Si quieres ser más estricto del lado del front:
                // if (Number(type) === 2 && (fd.get('combo_price') === null || fd.get('combo_price') === '')) {
                //   return reject(new Error('combo_price is required for COMBO'));
                // }

                method(url, fd)
                    .then(res => {
                        context.dispatch('lists', payload.search).catch(() => {});
                        context.commit('reset');
                        resolve(res);
                    })
                    .catch(reject);
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
