import ComplementosComponent from "../../components/admin/complementos/ComplementosComponent.vue";
import ComplementosListComponent from "../../components/admin/complementos/ComplementosListComponent.vue";
import ComplementoShowComponent from "../../components/admin/complementos/ComplementoShowComponent.vue";

export default [
    {
        path: '/admin/complementos',
        component: ComplementosComponent,
        name: 'admin.complementos',
        redirect: {name: 'admin.complementos.list'},
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: 'complementos',
            breadcrumb: 'complementos'
        },
        children: [
            {
                path: '',
                component: ComplementosListComponent,
                name: 'admin.complementos.list',
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: 'complementos',
                    breadcrumb: ''
                },
            },
            {
                path: "show/:id",
                component: ComplementoShowComponent,
                name: "admin.complemento.show",
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: "complementos",
                    breadcrumb: "view",
                },
            }
        ]
    }
]