import { createApp } from 'vue'
import ImageUploader from './Components/ImageUploader.vue'

const app = createApp({})
app.component('ImageUploader', ImageUploader)
app.mount('#app')
