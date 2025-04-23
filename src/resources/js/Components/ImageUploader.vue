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
import { global_state } from '../lib/global_state'
import createSynchronizer from "../lib/synchronizer";
import Time from "../lib/osapjs/utils/time";
import axios from 'axios'

const scale = 5 /8;
const fileInput = ref(null)
const isDragging = ref(false)
const previewUrl = ref(null)
// const apiResponse = ref<any>(null)

let machine;
let motorA;
let motorB;
let motorC;
let coordinates;
// const resetCoordinates = [
//     [0, 0], [226, 0],
//     [226, 20], [0, 20],
//     [0, 40], [226, 40],
//     [226, 60], [0, 60],
//     [0, 80], [226, 80],
//     [226, 100], [0, 100],
//     [0, 120], [226, 120],
//     [226, 140], [0, 140],
//     [0, 160], [226, 160],
//     [226, 180], [0, 180],
//     [0, 200], [226, 200],
//     [226, 220], [0, 220],
//     [0, 240], [226, 240]
// ];

const resetCoordinates = [
    [0, 0], [216, 0], [216, 230], [10, 230],
    [10, 10], [206, 10], [206, 220], [20, 220],
    [20, 20], [196, 20], [196, 210], [30, 210],
    [30, 30], [186, 30], [186, 200], [40, 200],
    [40, 40], [176, 40], [176, 190], [50, 190],
    [50, 50], [166, 50], [166, 180], [60, 180],
    [60, 60], [156, 60], [156, 170], [70, 170],
    [70, 70], [146, 70], [146, 160], [80, 160],
    [80, 80], [136, 80], [136, 150], [90, 150],
    [90, 90], [126, 90], [126, 140], [100, 140],
    [100, 100], [116, 100], [116, 130], [110, 130],
    [110, 110]
];

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

const handleFile = async (file) => {
    if (!file || !file.type.startsWith('image/')) {
        alert('Please select an image file.')
        return
    }

    // プレビュー表示用
    const reader = new FileReader()
    reader.onload = () => {
        previewUrl.value = reader.result
    }
    reader.readAsDataURL(file)

    // 画像アップロード処理
    const formData = new FormData()
    formData.append('image', file)

    try {
        const response = await axios.post('/api/abstract-image', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })

        console.log('API Response:', response.data)
        coordinates = response.data['coordinates']
    } catch (error) {
        console.error('❌ API Error:', error)
        return
    }
    console.log('coordinates:', coordinates)
    await runKaresansui(coordinates)
}

const initMachine = async () => {
    console.log(`initMachine`);
    motorA = global_state.things.value['motorA']
    motorB = global_state.things.value['motorB']
    motorC = global_state.things.value['motorC']
    machine = createSynchronizer([motorA, motorB]);

    await motorA.setCurrent(1);
    motorA.setStepsPerUnit(5);
    motorA.setAccel(20);

    await motorB.setCurrent(1);
    motorB.setStepsPerUnit(5);
    motorB.setAccel(20);

    // set present position as (X0,Y0)
    await machine.setPosition([0, 0]);

    await motorC.setCurrent(0.8);
    motorC.setStepsPerUnit(5);
}

const goTo = async (x, y) => {
    console.log(`Moving to (${x}, ${y})`);
    await machine.absolute([scale * (x + y), scale * (x - y)]);
}

const goToHome = async () => {
    console.log(`goToHome`);
    while(await motorB.getLimitState()){ // Limit switch at X- as Normally-Open
        motorA.velocity(-10);//move motorA CW -> CCW
        motorB.velocity(-10); //move motorB CW -> CCW
    }
    while(await motorA.getLimitState()){ //  Limit switch at Y- as Normally-Open
        motorA.velocity(-10); //positive value means CW -> CCW
        motorB.velocity(10); //negative value means CCW -> CW
    }
    motorA.velocity(0);
    motorB.velocity(0);
    machine.setPosition([0, 0]);
    await Time.delay(1000);
    await goTo(10, 10);
    await Time.delay(1000);
}

const runKaresansui = async (coordinates) => {
    console.log(`runKaresansui`);
    await initMachine()
    await goToHome()
    motorC.relative(-15);
    for (let i = 1; i < coordinates.length; i++){
        await goTo(coordinates[i][0], coordinates[i][1]);
        await Time.delay(200);
    }
    // await runReset()
    motorC.relative(15);
    await goToHome()
}

const runReset = async () => {
    console.log(`runReset`);
    motorA.velocity(1)
    motorB.velocity(1)
    for (let i = 1; i < resetCoordinates.length; i++){
        await goTo(resetCoordinates[i][0], resetCoordinates[i][1]);
        await Time.delay(200);
    }
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

