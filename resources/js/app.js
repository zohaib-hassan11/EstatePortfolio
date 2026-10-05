import { createApp } from 'vue'
import { confirmAction, toast } from './lib/alert.js'

import MobileNav from './components/MobileNav.vue'
import EnquiryForm from './components/EnquiryForm.vue'
import PropertyFilter from './components/PropertyFilter.vue'
import PropertyGallery from './components/PropertyGallery.vue'
import TestimonialCarousel from './components/TestimonialCarousel.vue'
import ServiceAreaMap from './components/ServiceAreaMap.vue'
import StackedBarChart from './components/StackedBarChart.vue'
import HBarChart from './components/HBarChart.vue'
import ReplyDrafter from './components/ReplyDrafter.vue'

/*
 * Vue islands.
 *
 * Every page is server-rendered Blade so Google gets real HTML. Vue only takes
 * over the pieces that are genuinely interactive. Drop this on any element:
 *
 *   <div data-vue="EnquiryForm" data-props='@json([...])'></div>
 */
const components = {
    MobileNav,
    EnquiryForm,
    PropertyFilter,
    PropertyGallery,
    TestimonialCarousel,
    ServiceAreaMap,
    StackedBarChart,
    HBarChart,
    ReplyDrafter,
}

document.querySelectorAll('[data-vue]').forEach((el) => {
    const name = el.dataset.vue
    const component = components[name]

    if (!component) {
        console.warn(`[islands] Unknown component "${name}"`)
        return
    }

    let props = {}
    try {
        props = el.dataset.props ? JSON.parse(el.dataset.props) : {}
    } catch (error) {
        console.error(`[islands] Bad props JSON on "${name}"`, error)
    }

    createApp(component, props).mount(el)
})


/*
 * Flash messages arrive as a data attribute on <body> and surface as a toast,
 * so a redirect after saving still tells you what happened.
 */
const flash = document.body.dataset.flash
if (flash) {
    toast(flash, document.body.dataset.flashType || 'success')
}

/*
 * Any form carrying data-confirm asks first. Progressive: without JS the form
 * still submits, which is the correct fallback for a destructive action guarded
 * by an authenticated POST.
 */
document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        if (form.dataset.confirmed === 'yes') return

        event.preventDefault()

        const ok = await confirmAction({
            title: form.dataset.confirmTitle || 'Are you sure?',
            text: form.dataset.confirm,
            confirmText: form.dataset.confirmButton || 'Yes, delete it',
            danger: form.dataset.confirmDanger !== 'false',
        })

        if (ok) {
            form.dataset.confirmed = 'yes'
            form.requestSubmit()
        }
    })
})
