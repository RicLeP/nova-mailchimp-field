<?php

namespace Riclep\NovaMailchimpField;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class NovaMailchimpField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-mailchimp-field';

	/**
	 * @var bool
	 */
	public $showOnIndex = false;

	/**
	 * @param mixed $resource
	 * @param null $attribute
	 */
	public function resolveForDisplay($resource, $attribute = null)
	{
		parent::resolveForDisplay($resource, $attribute);
	}
}
