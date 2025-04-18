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
                        <div v-if="Object.entries(things).length > 0">
                            <div
                                v-for="([name, thing], index) in Object.entries(things)"
                                :key="name"
                                style="font-size: 1.1em; padding-top: 5px"
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
                                <div v-for="entry in thing.api" :key="entry.name" style="font-size: 1em; padding-left: 1em; padding-bottom: .5em; color: grey">
                                    <div>
                                        {{ entry.name }}({{ entry.args.map(arg => arg.split(':')[0]).join(', ') }})
                                    </div>
                                    <div v-for="(x, i) in entry.args" :key="i" style="padding-left: 1em; white-space: nowrap">
                                        {{ x }}
                                    </div>
                                    <div v-if="entry.return" style="padding-left: 1em">
                                        <b>returns:</b> {{ entry.return }}
                                    </div>
                                </div>
                                <hr style="color: black" />
                            </div>
                        </div>
                        <div v-else class="no-things">
                            <div>no things found...</div>
                            <div>(maybe try scanning or pairing?)</div>
                        </div>
                    </div>
<!--                    <div ref="viewRef" v-show="panelType === 'view'" style="height: 100%; width: 100%; overflow: hidden"></div>-->
                </div>
            </div>
            <SideMenu />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import SideMenu from './SideMenu.vue'
import ScanButton from './ScanButton.vue'
import { OSAP } from '../lib/osapjs/osap.ts'
import { init } from '../lib/init'
import { global_state } from '../lib/global_state'
import { setThingsState } from '../lib/setThingsState'

const panelType = ref('devices')
const viewRef = ref(null)
const things = ref(global_state.things.value)

onMounted(() => {
    if (!window.__initialized) {
        init(global_state)
        window.__initialized = true
    }
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
.root { display: flex; flex-direction: column; height: 100vh; }
.content { flex: 1; display: flex; }
.not-menu-content { flex: 1; display: flex; flex-direction: row; }
.right-panel { width: 40%; background-color: #f5f5f5; overflow: auto; }
.device-panel { padding: 1em; }
.device-title { font-weight: bold; font-size: 1.2em; margin-bottom: 1em; }
.device-buttons { display: flex; flex-wrap: wrap; gap: 1em; margin-bottom: 1em; }
.device-button-container { flex: 1; }
.device-button { background-color: #eee; padding: 0.5em 1em; border: none; border-radius: 4px; cursor: pointer; }
.console-panel { background: #fff; overflow-y: auto; }
.console-content { max-height: 300px; overflow-y: auto; }
.console-input { margin-top: 10px; }
</style>
