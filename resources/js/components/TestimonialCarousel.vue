<script setup>
import { computed, onBeforeUnmount, ref } from 'vue'

const props = defineProps({
    items: { type: Array, default: () => [] }, // [{ body, author, location, role, rating }]
    interval: { type: Number, default: 7000 },
})

const index = ref(0)
const active = computed(() => props.items[index.value] ?? null)

function go(i) {
    index.value = (i + props.items.length) % props.items.length
}

let timer = null
if (props.items.length > 1) {
    timer = setInterval(() => go(index.value + 1), props.interval)
}
onBeforeUnmount(() => timer && clearInterval(timer))

function pause() {
    if (timer) {
        clearInterval(timer)
        timer = null
    }
}
</script>

<template>
    <div v-if="active" class="text-center" @mouseenter="pause" @focusin="pause">
        <div class="flex justify-center gap-1" aria-hidden="true">
            <svg v-for="n in active.rating" :key="n" class="h-5 w-5 text-brass-500" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.9 4.8 17.6l1-5.8L1.5 7.7l5.9-.9L10 1.5z" />
            </svg>
        </div>

        <Transition mode="out-in" enter-active-class="transition duration-300" enter-from-class="opacity-0 translate-y-2"
                    leave-active-class="transition duration-150" leave-to-class="opacity-0">
            <blockquote :key="index" class="mt-6">
                <p class="font-display text-2xl leading-snug text-ink-900 sm:text-3xl">&ldquo;{{ active.body }}&rdquo;</p>
                <footer class="mt-6 text-sm text-ink-500">
                    <span class="font-semibold text-ink-800">{{ active.author }}</span>
                    <span v-if="active.role"> &middot; {{ active.role }}</span>
                    <span v-if="active.location"> &middot; {{ active.location }}</span>
                </footer>
            </blockquote>
        </Transition>

        <div v-if="items.length > 1" class="mt-8 flex justify-center gap-2">
            <button
                v-for="(item, i) in items"
                :key="i"
                type="button"
                class="h-2 rounded-full transition-all"
                :class="i === index ? 'w-8 bg-brass-500' : 'w-2 bg-ink-200 hover:bg-ink-300'"
                :aria-label="`Show testimonial ${i + 1}`"
                :aria-current="i === index"
                @click="pause(); go(i)"
            />
        </div>
    </div>
</template>
