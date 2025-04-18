<template>
    <div class="max-w-md w-full mx-auto mt-8">
        <!-- Upload Area -->
        <div
            class="w-[320px] h-[320px] border-2 border-dashed border-gray-300 rounded-xl p-6 text-center bg-white shadow-sm hover:shadow-md cursor-pointer transition"
            :class="{ 'bg-blue-50 border-blue-400': isDragging }"
            @dragover.prevent="onDragOver"
            @dragleave.prevent="onDragLeave"
            @drop.prevent="onDrop"
            @click="triggerFileInput"
        >
            <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="onFileChange"
            />

            <!-- Icons are wrapped separately -->
            <div class="flex justify-center mb-2">
                <ArrowUpTrayIcon class="w-10 h-10 text-gray-400" />
            </div>

            <p  v-if="!previewUrl" class="text-gray-600 text-sm">
                Drag and drop<br />or click to select an image
            </p>
        </div>
    </div>

    <!-- Preview -->
    <transition name="fade">
        <div v-if="previewUrl" class="mt-6 flex justify-center">
            <div class="w-[320px] h-[200px] overflow-hidden rounded shadow">
                <img
                    :src="previewUrl"
                    class="w-full h-full object-contain"
                    alt="Preview"
                />
            </div>
        </div>
    </transition>
</template>

<script setup>
import { ref } from 'vue'
import { ArrowUpTrayIcon } from '@heroicons/vue/24/outline'

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

const onDrop = (e) => {
    isDragging.value = false
    handleFile(e.dataTransfer.files[0])
}

const onFileChange = (e) => {
    handleFile(e.target.files[0])
}

const handleFile = (file) => {
    if (!file || !file.type.startsWith('image/')) {
        alert('Please select an image file.')
        return
    }

    const reader = new FileReader()
    reader.onload = () => {
        previewUrl.value = reader.result
    }
    reader.readAsDataURL(file)
}

async function scanDevices() {
    if (!("serial" in navigator)) {
        alert("Web Serial API not supported");
        return;
    }
    const ports = await navigator.serial.getPorts();
    console.log("Connected ports:", ports);
}

async function pairDevice() {
    const port = await navigator.serial.requestPort();
    await port.open({ baudRate: 115200 });

    // ポートをOSAPに接続（モジュール内に適切な初期化が必要）
    const osap = new OSAPClient(port);
    await osap.init();
}

async function disconnectAll(devices) {
    for (const device of devices) {
        if (device.port && device.port.readable) {
            await device.port.close();
        }
    }
    devices.length = 0; // 全デバイス削除
}
</script>
<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>

