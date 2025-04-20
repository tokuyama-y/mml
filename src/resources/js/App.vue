<template>
    <div class="flex flex-col gap-6 p-6">

        <!-- 上段：画像アップロード + デバイススキャナ -->
        <div class="flex flex-row justify-between">
            <div class="w-[48%] pr-2">
                <image-uploader />
            </div>
            <div class="w-[48%] pl-2">
                <device-scanner />
            </div>
        </div>
        <br>
        <br>
        <br>

        <!-- 下段：Mindscape一覧（上との間に余白追加）-->
        <div class="mt-8">
            <MindscapeList :items="mindscapeItems" />
        </div>

    </div>
</template>


<script setup lang="ts">
import {onMounted, ref} from 'vue'
import ImageUploader from './Components/ImageUploader.vue'
import DeviceScanner from './Components/DeviceScanner.vue'
import MindscapeList from './Components/MindscapeList.vue'
import axios from "axios";

const mindscapeItems = ref([])
const fetchMindscapeResults = async () => {
    try {
        const response = await axios.get('/api/mindscape-results')
        mindscapeItems.value = response.data
    } catch (error) {
        console.error('❌ Failed to fetch mindscape results:', error)
    }
}

onMounted(() => {
    // 初回取得
    fetchMindscapeResults()

    // 10秒ごとにポーリング（必要に応じて変更可）
    setInterval(fetchMindscapeResults, 10000)
})
</script>

