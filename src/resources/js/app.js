import { createApp } from 'vue'
import ImageUploader from './Components/ImageUploader.vue'
import DeviceScanner from './Components/DeviceScanner.vue'

const app = createApp({})
app.component('ImageUploader', ImageUploader)
app.component('DeviceScanner', DeviceScanner)
app.mount('#app')
