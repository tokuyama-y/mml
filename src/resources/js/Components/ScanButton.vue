<template>
    <button
        class="device-button"
        :disabled="scanState === 'loading'"
        @click="scan"
    >
        scan
        <template v-if="scanState === 'loading'">…</template>
        <template v-if="scanState === 'error'">
            <span :style="errorStyle">!</span>
        </template>
    </button>
</template>

<script setup>
import { ref } from 'vue'
import { rescan } from '../lib/modularThingClient'
import { global_state } from '../lib/global_state'

const scanState = ref('idle')

const errorStyle = {
    color: 'red',
    marginLeft: '0.25rem'
}

const scan = async () => {
    scanState.value = 'loading'
    try {
        await rescan()
        scanState.value = 'idle'
    } catch (e) {
        scanState.value = 'error'
        global_state.things.value = {}
        console.error(e)
    }
}
</script>

<style scoped>
/* 任意: ボタンのスタイル */
.device-button {
    padding: 0.5rem 1rem;
    font-weight: bold;
    border: 1px solid #ccc;
    background-color: #f8f8f8;
    cursor: pointer;
}
</style>
