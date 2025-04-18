<template>
    <div class="root">
        <div class="content">
            <div class="not-menu-content">
                <div class="divider"></div>
                <div class="right-panel">
                    <div v-if="panelType === 'devices'" class="device-panel">
                        <div class="device-title">List of Things</div>
                        <div class="device-buttons">
                            <div class="device-button-container">
                                <ScanButton />
                            </div>
                            <div class="device-button-container">
                                <button class="device-button pair-button-trigger">pair new thing</button>
                            </div>
                            <div class="device-button-container">
                                <button class="device-button disconnect-button-trigger">disconnect all</button>
                            </div>
                            <div class="device-button-container">
                                <button class="device-button test-button-trigger">test</button>
                            </div>
                        </div>
<!--                        <pre>{{ global_state.things.value }}</pre>-->
<!--                        <pre>{{ things }}</pre>-->
<!--                        <pre>{{ JSON.stringify(things, null, 2) }}</pre>-->
                        <div v-if="Object.entries(things).length > 0">
                            <div
                                v-for="([name, thing], index) in Object.entries(things)"
                                :key="name"
                            >
                                <div style="display: flex; justify-content: space-between; align-items: center">
                                    <div style="font-weight: bold; font-size: 1.05em">name: {{ name }}</div>
                                    <button
                                        class="device-button"
                                        style="font-size: .9em; width: 100px"
                                        @click="renameThing(name, thing)"
                                    >rename</button>
                                </div>
                                <div>type: {{ thing.typeName }}</div>
<!--                                <div v-for="entry in thing.api" :key="entry.name" style="font-size: 1em; padding-left: 1em; padding-bottom: .5em; color: grey">-->
<!--                                    <div>-->
<!--                                        {{ entry.name }}({{ entry.args.map(arg => arg.split(':')[0]).join(', ') }})-->
<!--                                    </div>-->
<!--                                    <div v-for="(x, i) in entry.args" :key="i" style="padding-left: 1em; white-space: nowrap">-->
<!--                                        {{ x }}-->
<!--                                    </div>-->
<!--                                    <div v-if="entry.return" style="padding-left: 1em">-->
<!--                                        <b>returns:</b> {{ entry.return }}-->
<!--                                    </div>-->
<!--                                </div>-->
                                <hr style="color: black" />
                            </div>
                        </div>
                        <div v-else class="no-things">
                            <div>no things found...</div>
                            <div>(maybe try scanning or pairing?)</div>
                        </div>
                    </div>
                </div>
            </div>
<!--            <SideMenu />-->
        </div>
    </div>
</template>

<script setup>
import {ref, computed, onMounted, watch, onUnmounted} from 'vue'
// import SideMenu from './SideMenu.vue'
import ScanButton from './ScanButton.vue'
import { OSAP } from '../lib/osapjs/osap.ts'
import { init } from '../lib/init'
import { global_state } from '../lib/global_state'
import { setThingsState } from '../lib/setThingsState'

// const panelType = computed(() => global_state.panelType.value)
const panelType = ref(global_state.panelType.value)
// const viewRef = ref(null)
const things = ref(global_state.things.value)

let unsubThings = null

onMounted(() => {
    if (!window.__initialized) {
        init(global_state)
        window.__initialized = true
    }
    // 🔁 things のブリッジ
    unsubThings = global_state.things.subscribe((newThings) => {
        things.value = newThings
        console.log('[Vue Reflect] updated things:', things.value)
    })
})

onUnmounted(() => {
    if (unsubThings) unsubThings()
})

const renameThing = async (name, thing) => {
    const newName = prompt(`New name for ${name}`)
    if (!newName) return
    await OSAP.rename(name, newName)
    const updatedThings = { ...things.value }
    delete updatedThings[name]
    updatedThings[newName] = thing
    thing.updateName(newName)
    setThingsState(updatedThings)
    things.value = updatedThings
}
</script>

<style scoped>
.root {
    display: flex;
    flex-direction: column;
    /*height: 100vh;*/
}

.content {
    display: flex;
    flex: 1;
}

.not-menu-content {
    display: flex;
    flex: 1;
    flex-direction: row;
}

.right-panel {
    width: 320px; /* 固定幅で制御しやすく */
    min-width: 300px;
    background-color: #f5f5f5;
    padding: 1rem;
    box-sizing: border-box;
    overflow-y: auto;
    border-left: 1px solid #ddd;
}

.device-panel {
    width: 100%;
}

.device-title {
    font-weight: bold;
    font-size: 1.2em;
    margin-bottom: 1em;
}

.device-buttons {
    display: flex;
    flex-direction: column; /* 縦並び */
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.device-button-container {
    width: 100%;
}

.device-button {
    background-color: #eee;
    padding: 0.5em 1em;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-align: center;
    width: 100%;
}
</style>
