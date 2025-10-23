import {createApp} from 'vue';
import Assessment from './components/Assessment.vue';

const app = createApp({});


app.component('Assessment', Assessment);
app.mount('#assessment-app');
