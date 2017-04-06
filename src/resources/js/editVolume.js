var $rackspaceUsernameInput = $('.rackspace-username'),
	$rackspaceApiKeyInput = $('.racskspace-api-key'),
	$rackspaceRegionSelect = $('.rackspace-region-select > select'),
	$rackspaceContainerSelect = $('.rackspace-container-select > select'),
	$rackspaceRefreshContainersBtn = $('.rackspace-refresh-containers'),
	$rackspaceRefreshContainersSpinner = $rackspaceRefreshContainersBtn.parent().next().children(),
	refreshingRackspaceContainers = false;

$rackspaceRefreshContainersBtn.click(function () {
	if ($rackspaceRefreshContainersBtn.hasClass('disabled')) {
		return;
	}

	$rackspaceRefreshContainersBtn.addClass('disabled');
	$rackspaceRefreshContainersSpinner.removeClass('hidden');

	var data = {
		username: $rackspaceUsernameInput.val(),
		apiKey: $rackspaceApiKeyInput.val(),
		region: $rackspaceRegionSelect.val()
	};

	/** global: Craft */
	Craft.postActionRequest('rackspace', data, function (response, textStatus) {
		$rackspaceRefreshContainersBtn.removeClass('disabled');
		$rackspaceRefreshContainersSpinner.addClass('hidden');

		if (textStatus == 'success') {
			if (response.error) {
				alert(response.error);
			}
			else if (response.length > 0) {
				var currentContainer = $rackspaceContainerSelect.val(),
					currentContainerStillExists = false;

				refreshingRackspaceContainers = true;

				$rackspaceContainerSelect.prop('readonly', false).empty();

				for (var i = 0; i < response.length; i++) {
					if (response[i].container == currentContainer) {
						currentContainerStillExists = true;
					}

					$rackspaceContainerSelect.append('<option value="' + response[i].container + '" data-urlprefix="' + response[i].urlPrefix + '">' + response[i].container + '</option>');
				}

				if (currentContainerStillExists) {
					$rackspaceContainerSelect.val(currentContainer);
				}

				refreshingRackspaceContainers = false;

				if (!currentContainerStillExists) {
					$rackspaceContainerSelect.trigger('change');
				}
			}
		}
	});
});

$rackspaceContainerSelect.change(function () {
	if (refreshingRackspaceContainers) {
		return;
	}

	var $selectedOption = $rackspaceContainerSelect.children('option:selected');

	$('.volume-url').val($selectedOption.data('urlprefix'));
});
