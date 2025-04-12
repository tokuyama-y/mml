<template>
    <div
        class="border-4 border-dashed border-gray-400 p-10 w-96 h-64 flex flex-col items-center justify-center text-gray-500 cursor-pointer transition hover:bg-gray-200"
        @click="triggerFileInput"
        @dragover.prevent="onDragOver"
        @dragleave.prevent="onDragLeave"
        @drop.prevent="onDrop"
        :class="{ 'bg-gray-200': isDragging }"
    >
        <p class="text-center">
            ここに画像をドラッグ＆ドロップ<br />
            またはクリックして選択
        </p>
        <input type="file" ref="fileInput" accept="image/*" class="hidden" @change="onFileChange" />
    </div>

    <div v-if="previewUrl" class="mt-4">
        <img :src="previewUrl" class="max-w-sm rounded shadow-lg" />
    </div>
</template>

<script setup>
import { ref } from 'vue'

const fileInput = ref(null)
const isDragging = ref(false)
const previewUrl = ref(null)

const triggerFileInput = () => {
    fileInput.value.click()
}

const onDragOver = () => {
    isDragging.value = true
}

const onDragLeave = () => {
    isDragging.value = false
}

const onDrop = (event) => {
    isDragging.value = false
    const files = event.dataTransfer.files
    handleFile(files[0])
}

const onFileChange = (event) => {
    const file = event.target.files[0]
    handleFile(file)
}

const handleFile = (file) => {
    if (!file || !file.type.startsWith('image/')) {
        alert('画像ファイルを選択してください')
        return
    }

    const reader = new FileReader()
    reader.onload = () => {
        previewUrl.value = reader.result
    }
    reader.readAsDataURL(file)

    // 🔽 Laravelへアップロードする場合（コメントを外して使えます）
    /*
    const formData = new FormData()
    formData.append('image', file)

    fetch('/api/upload', {
      method: 'POST',
      body: formData
    }).then(res => res.json()).then(data => {
      console.log('Uploaded:', data)
    })
    */
}
</script>
