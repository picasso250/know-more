$(function () {
	var Alert = $('#Alert');
	var postForm = $('form[method="post"][role=post]').on('submit', function (e) {
		e.preventDefault();
		var $this = $(this);
		var $btn = $(this).find('button[type=submit]');
		$btn.button('loading');
		$.post($this.attr('action'), $this.serialize(), function (ret) {
			if (ret.code === 0) {
				if (ret.data && ret.data.url) {
					location.href = ret.data.url;
					return;
				}
				if (ret.message) {
					window.alert(ret.message);
				};
			}
			$btn.button('reset');
			Alert.removeClass('alert-hidden').text(ret.message);
		}, 'json');
	});
});
