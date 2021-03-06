$(function () {
	$.fn.extend({button: function(state) {
		var $button = $(this);
		if (state === 'loading') {
			$button.attr('disable', true);
			var old = $button.text();
			$button.text('loading');
			$button.data('old', old)
		}
		if (state === 'reset') {
			$button.attr('disable', true);
			$button.text($button.data('old'));
		}
	}})
	var postForm = $('form[ajax]').on('submit', function (e) {
		e.preventDefault();
		var $this = $(this);
		var alert = $this.find('.alert');
		var $btn = $(this).find('button[type=submit]');
		$btn.button('loading');
		$.post($this.attr('action'), $this.serialize(), function (ret) {
			if (ret.code === 0) {
				if (ret.data && ret.data.url) {
					location.href = ret.data.url;
					return;
				}
			}
			$btn.button('reset');
			alert.removeClass('alert-hidden').text(ret.message);
		}, 'json');
	});

	var voteParent = $('.vote-btn');
	voteParent.find('div').click(function () {
		var $this = $(this);
		var span = $this.find('span');
		var n = parseInt(span.text());
		span.text(1+n);
		$.post(voteParent.data('url'), {vote: $this.data('vote')}, function (ret) {
			console.log(ret);
		}, 'json')
	})
});
