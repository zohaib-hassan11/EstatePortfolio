<script setup>
import { computed, reactive, ref } from 'vue'

const props = defineProps({
    action: { type: String, required: true },
    suburbs: { type: Array, default: () => [] },
    initial: { type: Object, default: () => ({}) },
    types: { type: Object, default: () => ({}) },      // { key: label }
    priceBands: { type: Object, default: () => ({}) }, // { value: label }
    showPrice: { type: Boolean, default: true },
    showSort: { type: Boolean, default: true },
})

const blank = {
    q: '', suburb: '', type: '', beds: '', baths: '', min: '', max: '', sort: '',
}

const filters = reactive({ ...blank, ...props.initial })
const expanded = ref(false)

const activeCount = computed(() =>
    Object.entries(filters).filter(([key, value]) => key !== 'q' && key !== 'sort' && value !== '' && value !== null).length
)

/*
 * Submitting as a real GET form keeps every result set shareable, bookmarkable
 * and crawlable - the filters live in the URL, not in component state.
 */
function clear() {
    Object.assign(filters, blank)
    window.location.href = props.action
}

const priceOptions = computed(() =>
    Object.entries(props.priceBands).map(([value, label]) => ({ value, label }))
)
</script>

<template>
    <form :action="action" method="GET" class="rounded-card border border-ink-100 bg-white p-4 sm:p-5">
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <svg
                    class="pointer-events-none absolute top-1/2 left-3.5 h-4.5 w-4.5 -translate-y-1/2 text-ink-400"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                >
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" d="m20 20-3.5-3.5" />
                </svg>
                <label class="sr-only" for="filter-q">Search by area or block</label>
                <input
                    id="filter-q"
                    v-model="filters.q"
                    name="q"
                    type="search"
                    class="input pl-10"
                    placeholder="Search area, block or property"
                />
            </div>

            <button
                type="button"
                class="btn-outline shrink-0 sm:w-auto"
                :aria-expanded="expanded"
                @click="expanded = !expanded"
            >
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" d="M4 6h16M7 12h10M10 18h4" />
                </svg>
                Filters
                <span
                    v-if="activeCount"
                    class="ml-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-brass-500 px-1.5 text-xs font-bold text-white"
                >{{ activeCount }}</span>
            </button>

            <button type="submit" class="btn-primary shrink-0 sm:w-auto">Search</button>
        </div>

        <Transition
            enter-active-class="transition-all duration-200 ease-out overflow-hidden"
            enter-from-class="opacity-0 max-h-0"
            enter-to-class="opacity-100 max-h-[40rem]"
            leave-active-class="transition-all duration-150 ease-in overflow-hidden"
            leave-from-class="opacity-100 max-h-[40rem]"
            leave-to-class="opacity-0 max-h-0"
        >
            <div v-show="expanded" class="mt-4 border-t border-ink-100 pt-4">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label class="label" for="filter-suburb">Area</label>
                        <select id="filter-suburb" v-model="filters.suburb" name="suburb" class="input">
                            <option value="">All areas</option>
                            <option v-for="suburb in suburbs" :key="suburb" :value="suburb">{{ suburb }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="label" for="filter-type">Property type</label>
                        <select id="filter-type" v-model="filters.type" name="type" class="input">
                            <option value="">Any type</option>
                            <option v-for="(label, key) in types" :key="key" :value="key">{{ label }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="label" for="filter-beds">Bedrooms (min)</label>
                        <select id="filter-beds" v-model="filters.beds" name="beds" class="input">
                            <option value="">Any</option>
                            <option v-for="n in 5" :key="n" :value="n">{{ n }}+</option>
                        </select>
                    </div>

                    <div>
                        <label class="label" for="filter-baths">Bathrooms (min)</label>
                        <select id="filter-baths" v-model="filters.baths" name="baths" class="input">
                            <option value="">Any</option>
                            <option v-for="n in 4" :key="n" :value="n">{{ n }}+</option>
                        </select>
                    </div>

                    <template v-if="showPrice">
                        <div>
                            <label class="label" for="filter-min">Price from</label>
                            <select id="filter-min" v-model="filters.min" name="min" class="input">
                                <option v-for="option in priceOptions" :key="`min-${option.value}`" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="label" for="filter-max">Price to</label>
                            <select id="filter-max" v-model="filters.max" name="max" class="input">
                                <option v-for="option in priceOptions" :key="`max-${option.value}`" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </div>
                    </template>

                    <div v-if="showSort">
                        <label class="label" for="filter-sort">Sort by</label>
                        <select id="filter-sort" v-model="filters.sort" name="sort" class="input">
                            <option value="">Newest first</option>
                            <option value="price_asc">Price: low to high</option>
                            <option value="price_desc">Price: high to low</option>
                            <option value="beds_desc">Most bedrooms</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-4">
                    <button type="submit" class="btn-primary">Apply filters</button>
                    <button type="button" class="text-sm font-semibold text-ink-500 underline hover:text-ink-900" @click="clear">
                        Clear all
                    </button>
                </div>
            </div>
        </Transition>
    </form>
</template>
