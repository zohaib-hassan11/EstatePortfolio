<script setup>
import { ref } from 'vue'

const props = defineProps({
    areas: { type: Array, default: () => [] },
    embedQuery: { type: String, default: '' },
    officeLabel: { type: String, default: '' },
    directionsUrl: { type: String, default: '' },
})

const selected = ref('')
const loaded = ref(false)

/*
 * The map iframe is only injected once someone opts in. Keeps Google off the
 * page for visitors who never scroll here, which is good for load time and
 * means no third-party cookies until there is intent.
 */
function query() {
    return selected.value ? `${selected.value}, ${props.embedQuery}` : props.embedQuery
}

function src() {
    return `https://maps.google.com/maps?q=${encodeURIComponent(query())}&output=embed&z=12`
}

function pick(area) {
    selected.value = selected.value === area ? '' : area
    if (loaded.value) return
    loaded.value = true
}
</script>

<template>
    <div class="grid gap-8 lg:grid-cols-[1fr_1.3fr] lg:items-start">
        <div>
            <p class="eyebrow">Where I work</p>
            <h2 class="mt-3 text-3xl sm:text-4xl">Service areas</h2>
            <p class="prose-page mt-4">
                I work across Lahore, mostly in these societies. Tap one to see it on the map, or ask me
                about an address outside them - I will tell you honestly if it is not my patch.
            </p>

            <div class="mt-6 flex flex-wrap gap-2">
                <button
                    v-for="area in areas"
                    :key="area"
                    type="button"
                    class="rounded-full border px-4 py-2 text-sm font-medium transition-colors"
                    :class="selected === area
                        ? 'border-ink-900 bg-ink-900 text-sand-50'
                        : 'border-ink-200 bg-white text-ink-700 hover:border-ink-900'"
                    :aria-pressed="selected === area"
                    @click="pick(area)"
                >
                    {{ area }}
                </button>
            </div>

            <a v-if="directionsUrl" :href="directionsUrl" target="_blank" rel="noopener noreferrer"
               class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-brass-600 hover:text-brass-700">
                Get directions to the office
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M9 7h8v8" />
                </svg>
            </a>
        </div>

        <div class="overflow-hidden rounded-card border border-ink-100 bg-ink-100">
            <iframe
                v-if="loaded"
                :src="src()"
                class="aspect-4/3 w-full sm:aspect-16/10"
                style="border: 0"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                :title="`Map of ${query()}`"
            />
            <button
                v-else
                type="button"
                class="flex aspect-4/3 w-full flex-col items-center justify-center gap-3 bg-ink-100 text-ink-600 transition hover:bg-ink-200 sm:aspect-16/10"
                @click="loaded = true"
            >
                <svg class="h-10 w-10 text-ink-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11Z" />
                    <circle cx="12" cy="10" r="2.5" />
                </svg>
                <span class="font-semibold">Load map</span>
                <span class="text-sm text-ink-500">{{ officeLabel }}</span>
            </button>
        </div>
    </div>
</template>
