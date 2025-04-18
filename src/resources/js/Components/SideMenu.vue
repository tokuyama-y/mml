<template>
    <div class="side-menu">
        <div
            :class="['menu-item', panelType === 'devices' ? 'active' : '']"
            @click="handleClick('devices')"
        >
            Devices
        </div>
    </div>
</template>

<script setup>
import { global_state } from '../lib/global_state'
import { computed } from 'vue'

const panelType = computed(() => global_state.panelType.value)

function handleClick(name) {
    global_state.panelType.value =
        global_state.panelType.value === name ? 'none' : name
}
</script>

<style scoped>
.side-menu {
    width: 100px;
    background: #fafafa;
    border-right: 1px solid #ddd;
    padding: 1rem;
}
.menu-item {
    cursor: pointer;
    padding: 0.5rem 0;
}
.active {
    font-weight: bold;
    color: #007acc;
}
</style>


