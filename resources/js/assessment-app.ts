import {createApp} from 'vue';
import Assessment from './components/Assessment.vue';
import { plugin, defaultConfig } from '@formkit/vue'

const app = createApp({}).use(plugin, defaultConfig);


app.component('Assessment', Assessment);
app.mount('#assessment-app');
