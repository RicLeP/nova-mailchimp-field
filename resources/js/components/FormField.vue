<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
			<template v-if="loaded">
				<template v-if="mailchimp.member.status === 'subscribed'">
					<div v-if="mailchimp.groups" :key="rerender">
						<p class="mb-3 text-80">Select your preferences</p>

						<div class="nmcf-groups border-b border-40 mb-4 pb-3">
							<div v-for="group in mailchimp.groups" class="nmcf-groups__group">
								<p class="font-bold mb-2">{{ group.title }}</p>

								<ul v-if="group.options" class="nmcf-options">
									<li v-for="option in group.options" @input="toggleCheckbox(group.id, option.id)" class="nmfc-option">
										<checkbox-with-label
												class="pb-2"
												:id="option.id"
												:name="option.name"
												:checked="option.ticked"
										>
											<p class="mr-2">{{ option.name }}</p>
										</checkbox-with-label>
									</li>
								</ul>
							</div>
						</div>
					</div>

					<button class="btn btn-default btn-primary" @click="unsubscribe" type="button">Unsubscribe from {{ mailchimp.list.name }}</button>
				</template>
				<template v-else>
					<button class="btn btn-default btn-primary" @click="subscribe" type="button">Subscribe to {{ mailchimp.list.name }}</button>
				</template>
			</template>
			<p v-else>
				Loading subscription preferences
			</p>
        </template>
    </default-field>
</template>

<script>
	import Vue from 'vue';
	import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

	data() {
    	return {
			loaded: false,
			mailchimp: null,
			rerender: 1,
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
				this.tickAlreadySelectedOptions();
			});
		},
		toggleCheckbox(groupId, optionId) {
			this.mailchimp.member.interests[optionId] = ! this.mailchimp.member.interests[optionId];
			this.tickAlreadySelectedOptions();

			axios.post('/nova-vendor/nova-mailchimp-field/update-interests', {
				list_id: this.mailchimp.list.id,
				member_id: this.mailchimp.member.id,
				interests: this.mailchimp.member.interests
			}).then(response => {
				this.$toasted.show('Newsletter preferences updated!', {type: 'success'});
			}).catch((error) => {
				this.$toasted.show('Error updating newsletter preferences!', {type: 'error'});
			});
		},
		tickAlreadySelectedOptions() {
			Object.keys(this.mailchimp.groups).forEach((group) => {
				Object.keys(this.mailchimp.groups[group].options).forEach((option) => {
					let optionId = this.mailchimp.groups[group].options[option].id;

					Vue.set(this.mailchimp.groups[group].options[option], 'ticked', this.mailchimp.member.interests[optionId]);

					this.rerender++; // hacky way to force a rerender of the checkboxes
				});
			});
		},
		subscribe() {
			axios.post('/nova-vendor/nova-mailchimp-field/subscribe', {
				email_address: this.mailchimp.member.email_address
			}).then(response => {
				this.loadMailchimp();
				this.$toasted.show('Newsletter preferences updated!', {type: 'success'});
			}).catch((error) => {
				this.$toasted.show('Error updating newsletter preferences!', {type: 'error'});
			});
		},
		unsubscribe() {
			axios.post('/nova-vendor/nova-mailchimp-field/unsubscribe', {
				email_address: this.mailchimp.member.email_address
			}).then(response => {
				this.loadMailchimp();
				this.$toasted.show('Newsletter preferences updated!', {type: 'success'});
			}).catch((error) => {
				this.$toasted.show('Error updating newsletter preferences!', {type: 'error'});
			});
		}
    },

	mounted() {
		this.loadMailchimp();
	}
}
</script>

<style lang="scss">

	.nmcf-groups {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
		grid-gap: 20px;
	}

	.nmcf-options {
		padding-left: 0;

		list-style: none;
	}
</style>