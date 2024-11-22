import './bootstrap';
import { createApp, defineAsyncComponent } from 'vue';
import { createPinia } from 'pinia'

const app = createApp({});
const pinia = createPinia();

app.component('LoginPage',
    defineAsyncComponent(() => import('./components/Login.vue'))
);

app.use(pinia).mount('#app');