<script setup>
import { computed, nextTick, reactive, ref, useTemplateRef } from 'vue'
import { postJson } from '../lib/api.js'
import { successPopup, toast } from '../lib/alert.js'

const props = defineProps({
    endpoint: { type: String, required: true },
    type: { type: String, default: 'contact' },      // contact | property | appraisal
    propertyId: { type: [Number, String], default: null },
    propertyTitle: { type: String, default: '' },
    heading: { type: String, default: '' },
    subheading: { type: String, default: '' },
    submitLabel: { type: String, default: 'Send enquiry' },
    compact: { type: Boolean, default: false },
    types: { type: Object, default: () => ({}) }, // { key: label } for the appraisal form
})

const blank = () => ({
    name: '',
    email: '',
    phone: '',
    message: props.type === 'property' && props.propertyTitle
        ? `Hi, I'd like more information about ${props.propertyTitle}.`
        : '',
    address: '',
    suburb: '',
    property_type: 'house',
    bedrooms: '',
    timeframe: '',
    company: '', // honeypot
})

const root = useTemplateRef('root')
const form = reactive(blank())
const errors = ref({})
const status = ref('idle') // idle | saving | done | error
const serverMessage = ref('')

const isAppraisal = computed(() => props.type === 'appraisal')
const busy = computed(() => status.value === 'saving')

async function submit() {
    status.value = 'saving'
    errors.value = {}

    const payload = {
        type: props.type,
        property_id: props.propertyId,
        name: form.name,
        email: form.email,
        phone: form.phone,
        message: form.message,
    }

    if (isAppraisal.value) {
        Object.assign(payload, {
            address: form.address,
            suburb: form.suburb,
            property_type: form.property_type,
            bedrooms: form.bedrooms === '' ? null : Number(form.bedrooms),
            timeframe: form.timeframe,
        })
    }

    if (form.company !== '') {
        payload.company = form.company
    }

    try {
        const body = await postJson(props.endpoint, payload)
        serverMessage.value = body.message
        status.value = 'done'
        await reveal()

        successPopup({
            title: props.type === 'appraisal' ? 'Appraisal requested' : 'Message sent',
            text: body.message,
        })
    } catch (error) {
        if (error.validation) {
            errors.value = error.errors
            status.value = 'idle'
            await reveal(Object.keys(error.errors)[0])
        } else {
            serverMessage.value = error.message
            status.value = 'error'
            await reveal()
            toast(error.message, 'error')
        }
    }
}

/*
 * The submit button often sits well below the fields on a phone, so neither the
 * confirmation nor the first error is on screen when the response lands. Bring
 * the form back into view and put the cursor on whatever needs fixing.
 */
async function reveal(field = null) {
    await nextTick()

    root.value?.scrollIntoView({ behavior: 'smooth', block: 'center' })

    if (field) {
        const input = root.value?.querySelector(`[id$="-${field}"], [name="${field}"]`)
        input?.focus({ preventScroll: true })
    }
}

function reset() {
    Object.assign(form, blank())
    errors.value = {}
    status.value = 'idle'
}
</script>

<template>
    <div ref="root">
        <div v-if="heading || subheading" class="mb-6">
            <h2 v-if="heading" class="text-3xl">{{ heading }}</h2>
            <p v-if="subheading" class="mt-2 text-ink-500">{{ subheading }}</p>
        </div>

        <!-- Success -->
        <div
            v-if="status === 'done'"
            class="rounded-card border border-emerald-200 bg-emerald-50 p-6 text-center"
            role="status"
            aria-live="polite"
        >
            <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-emerald-600">
                <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                </svg>
            </div>
            <p class="font-semibold text-emerald-900">Message sent</p>
            <p class="mt-1 text-sm text-emerald-800">{{ serverMessage }}</p>
            <button type="button" class="mt-4 text-sm font-semibold text-emerald-900 underline" @click="reset">
                Send another
            </button>
        </div>

        <form v-else class="space-y-4" novalidate @submit.prevent="submit">
            <p
                v-if="status === 'error'"
                class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                role="alert"
            >
                {{ serverMessage }}
            </p>

            <div :class="compact ? 'space-y-4' : 'grid gap-4 sm:grid-cols-2'">
                <div>
                    <label class="label" :for="`${type}-name`">Your name</label>
                    <input
                        :id="`${type}-name`"
                        v-model="form.name"
                        type="text"
                        autocomplete="name"
                        class="input"
                        :class="{ 'input-error': errors.name }"
                        placeholder="Ali Raza"
                    />
                    <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                </div>

                <div>
                    <label class="label" :for="`${type}-email`">Email</label>
                    <input
                        :id="`${type}-email`"
                        v-model="form.email"
                        type="email"
                        autocomplete="email"
                        class="input"
                        :class="{ 'input-error': errors.email }"
                        placeholder="ali@example.com"
                    />
                    <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                </div>
            </div>

            <div>
                <label class="label" :for="`${type}-phone`">Phone <span class="font-normal text-ink-400">(optional)</span></label>
                <input
                    :id="`${type}-phone`"
                    v-model="form.phone"
                    type="tel"
                    autocomplete="tel"
                    class="input"
                    :class="{ 'input-error': errors.phone }"
                    placeholder="0300 0000000"
                />
                <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone }}</p>
            </div>

            <!-- Appraisal-only block -->
            <template v-if="isAppraisal">
                <div>
                    <label class="label" for="appraisal-address">Property address</label>
                    <input
                        id="appraisal-address"
                        v-model="form.address"
                        type="text"
                        autocomplete="street-address"
                        class="input"
                        :class="{ 'input-error': errors.address }"
                        placeholder="House 24, Block C"
                    />
                    <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="appraisal-suburb">Suburb</label>
                        <input id="appraisal-suburb" v-model="form.suburb" type="text" class="input" placeholder="DHA Phase 6" />
                    </div>
                    <div>
                        <label class="label" for="appraisal-type">Property type</label>
                        <select id="appraisal-type" v-model="form.property_type" class="input">
                            <option v-for="(label, key) in types" :key="key" :value="key">{{ label }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label" for="appraisal-beds">Bedrooms</label>
                        <input id="appraisal-beds" v-model="form.bedrooms" type="number" min="0" max="20" class="input" placeholder="4" />
                    </div>
                    <div>
                        <label class="label" for="appraisal-timeframe">When are you looking to sell?</label>
                        <select id="appraisal-timeframe" v-model="form.timeframe" class="input">
                            <option value="">Please choose</option>
                            <option>ASAP</option>
                            <option>1-3 months</option>
                            <option>3-6 months</option>
                            <option>6-12 months</option>
                            <option>Just researching</option>
                        </select>
                    </div>
                </div>
            </template>

            <div>
                <label class="label" :for="`${type}-message`">
                    Message <span v-if="isAppraisal" class="font-normal text-ink-400">(optional)</span>
                </label>
                <textarea
                    :id="`${type}-message`"
                    v-model="form.message"
                    rows="4"
                    class="input"
                    :class="{ 'input-error': errors.message }"
                    placeholder="Tell me a little about what you need..."
                />
                <p v-if="errors.message" class="mt-1 text-sm text-red-600">{{ errors.message }}</p>
            </div>

            <!-- Honeypot: hidden from people, catnip for bots -->
            <div class="absolute h-0 w-0 overflow-hidden" aria-hidden="true">
                <label>Company<input v-model="form.company" type="text" tabindex="-1" autocomplete="off" /></label>
            </div>

            <button type="submit" class="btn-accent w-full" :disabled="busy">
                <svg v-if="busy" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z" />
                </svg>
                {{ busy ? 'Sending...' : submitLabel }}
            </button>

            <p class="text-center text-xs text-ink-400">
                Your details stay private and are never shared with third parties.
            </p>
        </form>
    </div>
</template>
