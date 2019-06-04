Nova.booting((Vue, router, store) => {
    Vue.component('detail-nova-mailchimp-field', require('./components/DetailField'))
    Vue.component('form-nova-mailchimp-field', require('./components/FormField'))
})
