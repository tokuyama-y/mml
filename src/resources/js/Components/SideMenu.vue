<template>
    <div>
        <div @click="handleClick('devices')">
            Devices
        </div>
<!--        <div @click="handleClick('view')">-->
<!--            View-->
<!--        </div>-->
<!--        <div @click="handleClick('help')">-->
<!--            Help-->
<!--        </div>-->
    </div>
</template>

<script setup>
// import styles from '../styles/SideMenu.module.css'
import { global_state } from '../lib/global_state'
import { computed } from 'vue'

// function getStyle(name) {
//     return [
//         styles['menu-item'],
//         global_state.panelType.value === name
//             ? styles['selected-menu-item']
//             : ''
//     ].join(' ')
// }

function openPanel() {
    if (global_state.panelType.value === 'none') {
        document.documentElement.style.setProperty('--cm-width', '70%')
    }
}

function closePanel() {
    if (global_state.panelType.value === 'none') {
        document.documentElement.style.setProperty('--cm-width', '100%')
    }
}

function togglePanel(name) {
    global_state.panelType.value =
        global_state.panelType.value === name ? 'none' : name
}

function handleClick(name) {
    openPanel()
    togglePanel(name)
    closePanel()
}
</script>

<style scoped>
/* 任意の補足スタイル */
.prevent-select {
    user-select: none;
    cursor: pointer;
}
</style>
