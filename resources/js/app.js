import './bootstrap';
import { createApp, defineAsyncComponent } from 'vue';
import { createPinia } from 'pinia'

const app = createApp({});
const pinia = createPinia();

app.component('LoginPage',
    defineAsyncComponent(() => import('./components/Login.vue'))
);

app.component(
    'RegisterPage',
    defineAsyncComponent(() => import('./components/Register.vue')),
);

app.component(
    'InputText',
    defineAsyncComponent(() => import('./components/inputs/InputText.vue')),
);

app.component(
    'TaskLists',
    defineAsyncComponent(() => import('./components/TaskLists.vue')),
);

app.component(
    'NavHeader',
    defineAsyncComponent(() => import('./components/NavHeader.vue')),
);

// app.component(
//     'Dialog',
//     defineAsyncComponent(() => import('./components/modals/Dialog.vue')),
// );

app.component(
    'BaseLayout',
    defineAsyncComponent(() => import('./components/layouts/BaseLayout.vue')),
);

app.component(
    'PaginationItems',
    defineAsyncComponent(() => import('./components/Pagination.vue')),
);

app.use(pinia).mount('#app');