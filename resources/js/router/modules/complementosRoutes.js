import ComplementosComponent from "../../components/admin/complementos/ComplementosComponent.vue";
import ComplementosListComponent from "../../components/admin/complementos/ComplementosListComponent.vue";
import ComplementosShowComponent from "../../components/admin/complementos/ComplementosShowComponent.vue";

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
                component: ComplementosShowComponent,
                name: "admin.complementos.show",
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