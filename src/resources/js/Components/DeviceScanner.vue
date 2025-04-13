<template>
    <div class="relative">
        <!-- Toggle Button -->
        <button
            class="absolute -left-10 top-4 z-50 bg-gray-800 text-white rounded-l-lg px-3 py-2 hover:bg-gray-700 shadow-lg"
            @click="isOpen = !isOpen"
        >
            {{ isOpen ? '→' : '←' }}
        </button>

        <!-- Sliding Panel -->
        <transition name="slide">
            <div
                v-if="isOpen"
                class="fixed right-0 top-0 h-full w-80 bg-white border-l border-gray-300 shadow-lg p-4 flex flex-col space-y-4 z-40"
            >
                <h2 class="text-lg font-bold text-gray-800">Modular-Things Panel</h2>

                <!-- Buttons -->
                <div class="flex flex-wrap gap-2">
                    <button @click="scanDevices" class="btn-primary">Scan</button>
                    <button @click="pairDevice" class="btn-green">Pair</button>
                    <button @click="disconnectAllDevices" class="btn-red">Disconnect</button>
                </div>

                <!-- Connected Devices List -->
                <div class="mt-4">
                    <h3 class="font-semibold text-gray-700">Connected Devices</h3>
                    <ul class="list-disc pl-5 text-sm text-gray-600">
                        <li v-for="(link, index) in connectedLinks" :key="index">
                            Port {{ index + 1 }} - {{ link.isOpen() ? 'Connected' : 'Closed' }}
                        </li>
                    </ul>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const connectedLinks = ref([])
const isOpen = ref(true)

function cobsEncode(buf) {
    const dest = [0]
    let code_ptr = 0
    let code = 0x01

    function finish(incllast = true) {
        dest[code_ptr] = code
        code_ptr = dest.length
        if (incllast) dest.push(0x00)
        code = 0x01
    }

    for (let i = 0; i < buf.length; i++) {
        if (buf[i] === 0) {
            finish()
        } else {
            dest.push(buf[i])
            code += 1
            if (code === 0xFF) {
                finish()
            }
        }
    }

    finish(false)
    dest.push(0x00)
    return Uint8Array.from(dest)
}

function cobsDecode(buf) {
    const dest = []
    for (let i = 0; i < buf.length;) {
        const code = buf[i++]
        for (let j = 1; j < code; j++) {
            dest.push(buf[i++])
        }
        if (code < 0xFF && i < buf.length) {
            dest.push(0)
        }
    }
    return Uint8Array.from(dest)
}

class COBSWebSerial {
    constructor() {
        this.initialized = false
        this.openLinks = []
    }

    async init() {
        if (this.initialized) return
        this.initialized = true
        await this.rescan()

        navigator.serial.addEventListener('connect', async (event) => {
            console.warn('Serial connected:', event.target)
            await this.setupPort(event.target)
        })

        navigator.serial.addEventListener('disconnect', (event) => {
            console.warn('Serial disconnected:', event.target)
            this.removePort(event.target)
        })
    }

    async setupPort(port) {
        await port.open({ baudRate: 9600 })
        let writer = null
        let reader = null

        const send = async (data) => {
            const pck = cobsEncode(data)
            writer = port.writable.getWriter()
            await writer.write(pck)
            writer.releaseLock()
            writer = null
        }

        const link = {
            underlyingPort: port,
            isOpen: () => true,
            clearToSend: () => !writer,
            send,
            onData: (data) => console.warn('Received (default):', data),
            onClose: () => console.warn('Closed (default)'),
            close: () => {
                if (writer) writer.releaseLock()
                if (reader) {
                    reader.cancel()
                    reader.releaseLock()
                }
                port.close()
                this.removePort(port)
            }
        }

        const readLoop = async () => {
            let buffer = []
            while (port.readable) {
                reader = port.readable.getReader()
                while (true) {
                    const { value, done } = await reader.read()
                    if (value) {
                        for (const byte of value) {
                            if (byte === 0) {
                                const decoded = cobsDecode(new Uint8Array(buffer))
                                link.onData(decoded)
                                buffer = []
                            } else {
                                buffer.push(byte)
                            }
                        }
                    }
                    if (done) {
                        reader.releaseLock()
                        reader = null
                        return
                    }
                }
            }
        }

        readLoop()
        this.openLinks.push(link)
        this.onNewLink(link)
        return link
    }

    async rescan() {
        const ports = await navigator.serial.getPorts()
        for (const port of ports) {
            if (this.openLinks.find(link => link.underlyingPort === port)) continue
            await this.setupPort(port)
        }
    }

    async authorizeNewPort() {
        const port = await navigator.serial.requestPort()
        return await this.setupPort(port)
    }

    disconnectAll() {
        for (const link of [...this.openLinks]) {
            link.close()
        }
        console.warn('All devices disconnected')
    }

    removePort(port) {
        const index = this.openLinks.findIndex(l => l.underlyingPort === port)
        if (index !== -1) {
            this.openLinks[index].onClose()
            this.openLinks.splice(index, 1)
        }
    }

    onNewLink(link) {
        console.warn('New device paired')
        connectedLinks.value.push(link)
    }
}

const serial = new COBSWebSerial()

onMounted(() => {
    serial.init()
})

const pairDevice = () => serial.authorizeNewPort()
const disconnectAllDevices = () => serial.disconnectAll()
const scanDevices = async () => {
    const ports = await navigator.serial.getPorts()
    alert(`${ports.length} port(s) found`)
    console.log('Available ports:', ports)
}
</script>

<style scoped>
.btn-primary {
    background-color: #3b82f6;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    transition: background 0.3s;
}
.btn-primary:hover {
    background-color: #2563eb;
}
.btn-green {
    background-color: #10b981;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    transition: background 0.3s;
}
.btn-green:hover {
    background-color: #059669;
}
.btn-red {
    background-color: #ef4444;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    transition: background 0.3s;
}
.btn-red:hover {
    background-color: #dc2626;
}
.slide-enter-from {
    transform: translateX(100%);
}
.slide-enter-to {
    transform: translateX(0%);
}
.slide-leave-from {
    transform: translateX(0%);
}
.slide-leave-to {
    transform: translateX(100%);
}
.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}
</style>
