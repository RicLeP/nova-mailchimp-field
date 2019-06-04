<template>
	<div class="flex border-b border-40">
		<div class="w-1/4 py-4">
			<h4 class="font-normal text-80">
				{{ field.indexName }}
			</h4>
		</div>

		<div class="w-3/4 py-4 leading-normal">
			<template v-if="loaded">
				<p v-if="mailchimp.member.status === 'subscribed'">
					You are subscribed to: {{ mailchimp.list.name }}.<br>
					Edit your profile to update your preferences.
				</p>
				<p v-else>
					You are not subscribe to: {{ mailchimp.list.name }}.<br>
					Edit your profile to update your preferences.
				</p>
			</template>
			<p v-else>
				Loading subscription preferences
			</p>
		</div>
	</div>

</template>

<script>
export default {
    props: ['resource', 'resourceName', 'resourceId', 'field'],

	data() {
		return {
			loaded: false,
			mailchimp: null,
		}
	},

	methods: {
		loadMailchimp() {
			// get all list from MailChimp
			Nova.request().post('/nova-vendor/nova-mailchimp-field/subscription-status', {
				email_address: this.field.emailAddress
			}).then(response => {
				this.mailchimp = response.data;
				this.loaded = true;
			});
		},
	},

	mounted() {
		this.loadMailchimp();
	}
}
</script>
