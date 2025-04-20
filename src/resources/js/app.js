// import {createApp, ref} from 'vue'
// import ImageUploader from './Components/ImageUploader.vue'
// import DeviceScanner from './Components/DeviceScanner.vue'
// import MindscapeList from './Components/MindscapeList.vue'
// const app = createApp({
//     setup() {
//         const mindscapeItems = ref([
//             {
//                 uploaded_image_url: 'https://example.com/uploaded.png',
//                 mindscape_image_url: 'https://example.com/mindscape.jpg',
//                 karesansui_image_url: 'https://example.com/karesansui.svg',
//                 haiku: 'Silent waves ripple\nMemories drift in the sand\nTime stands still and breathes'
//             }
//         ])
//         return { mindscapeItems }
//     }
// })
//
// app.component('ImageUploader', ImageUploader)
// app.component('DeviceScanner', DeviceScanner)
// app.component('MindscapeList', MindscapeList)
// app.mount('#app')

import '../css/app.css'; // これを忘れると Tailwind が効きません
import { createApp } from 'vue'
import App from './App.vue'

createApp(App).mount('#app')

