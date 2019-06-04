<?php

namespace Riclep\NovaMailchimpField\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Newsletter\NewsletterFacade as Newsletter;

class NovaMailchimpFieldController extends Controller
{
	public function subscriptionStatus(Request $request)
	{
		$api = Newsletter::getApi();

		// get list
		$list = Cache::remember('mc_list_' . config('newsletter.lists.subscribers.id'), 600, function() use ($api) {
			return collect($api->get('lists/' . config('newsletter.lists.subscribers.id')))->only(['id', 'name']);
		});

		$groups = Cache::remember('mc_list_' . config('newsletter.lists.subscribers.id') . '_groups', 600, function() use ($api) {
			// get groups for this list
			return collect($api->get('lists/' . config('newsletter.lists.subscribers.id') . '/interest-categories')['categories'])->transform(function ($group) use ($api) {
				$group = collect($group)->only(['id', 'title']); // only need a few fields

				// get all the options for this group
				$group['options'] = collect($api->get('lists/' . config('newsletter.lists.subscribers.id') . '/interest-categories/' . $group['id'] . '/interests')['interests'])->transform(function ($option) {
					$option = collect($option)->only(['id', 'name']); // only need a few fields

					return $option->toArray();
				})->keyBy('id');

				return $group->toArray();
			})->keyBy('id');
		});

		$member = collect(Newsletter::getMember($request->get('email_address')))->only(['id', 'email_address', 'status', 'interests']);

		return [
			'list' => $list,
			'groups' => $groups,
			'member' => $member,
		];
	}

	public function updateInterests(Request $request)
	{
		$api = Newsletter::getApi();

		$update = $api->patch('lists/' . $request->get('list_id') . '/members/' . $request->get('member_id'), [
			'interests' => $request->get('interests'),
		]);

		// make sure it matches
		if ($update['interests'] === $request->get('interests')) {
			return response()->json();
		}

		return response()->json([], 400); // return error
	}

	public function subscribe(Request $request)
	{
		if (Newsletter::subscribeOrUpdate($request->get('email_address'))) {
			return response()->json();
		}

		return response()->json([], 400); // return error
	}

	public function unsubscribe(Request $request)
	{
		if (Newsletter::unsubscribe($request->get('email_address'))) {
			return response()->json();
		}

		return response()->json([], 400); // return error
	}

	public function status(Request $request)
	{
		if (Newsletter::unsubscribe($request->get('email_address'))) {
			return response()->json();
		}

		return response()->json([], 400); // return error
	}
}
