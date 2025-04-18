// src/lib/loadThing.js
import { OSAP } from '../osapjs/osap.ts'

export async function loadThing(link) {
    const osap = new OSAP()
    // await osap.linkGateway.attachSerial(link)
    await osap.port.attach(link)
    const map = await osap.updateMap()

    const motorNodeName = Object.keys(map).find(name => name === 'motorA')
    const rpc = (fn, args = []) => osap.send(motorNodeName, fn, args)

    return {
        name: motorNodeName,
        setCurrent: (val) => rpc('setCurrent', [val]),
        setStepsPerUnit: (val) => rpc('setStepsPerUnit', [val]),
        setAccel: (val) => rpc('setAccel', [val]),
        setPosition: (coords) => rpc('setPosition', coords),
        getPosition: () => rpc('getPosition'),
        getVelocity: () => rpc('getVelocity'),
        getMaxVelocity: () => rpc('getMaxVelocity'),
        getMaxAccel: () => rpc('getMaxAccel'),
        target: () => rpc('target'),
        absolute: () => rpc('absolute'),
        awaitMotionEnd: () => rpc('awaitMotionEnd'),
        stop: () => rpc('stop'),
        velocity: () => rpc('velocity')
    }
}
