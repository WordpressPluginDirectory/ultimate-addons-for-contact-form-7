
jQuery(document).ready(function ($) {
	const $chooseField = $('#uacf7-choose-field');
	const $argFieldset = $('#uacf7-dynamic-arg-fieldset');
	const $argInput = $('#uacf7-dynamic-arg');

	function isQueryPart(val) {
		return val === 'UACF7_URL part=query' || val.startsWith('UACF7_URL part=query');
	}

	function toggleArgField() {
		const selected = $chooseField.val();
		if (isQueryPart(selected)) {
			$argFieldset.show();
		} else {
			$argFieldset.hide();
			$argInput.val('');
		}
	}

	function updateValueField() {
		const arg = $argInput.val().trim();
		const base = 'UACF7_URL part=query';
		const current = $chooseField.val();

		if (isQueryPart(current)) {
			const final = arg ? `${base} key=${arg}` : base;
			$chooseField.val(final).trigger('change'); // Update tag preview
		}
	}

	// Trigger on all relevant events
	$chooseField.on('change input blur', function () {
		toggleArgField();
	});

	$argInput.on('input change', updateValueField);

	// Show datalist suggestions again on focus (force dropdown reopen)
	$chooseField.on('focus', function () {
		this.setSelectionRange(this.value.length, this.value.length); // Moves cursor to end
	});

	// Initialize state on load
	toggleArgField();
});