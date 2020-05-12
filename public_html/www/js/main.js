$(function(){
	$('[name=rate]').on('click', function(e){
		$article = $(this).data('article-id');
		$rate = $(this).data('rate');
		$element = $(this);
		
		if ($article && $rate) {
			$.getJSON( '/?articleId=' + $article + '&rating=' + $rate + '&do=rate', function() {
				// Wait for response
			})
			.done(function(data) {
				if (data.result === 'error') {
					alert('Chyba: ' + data.message);
				} else {
					alert('Článek byl úspěšně ohodnocen.');
					$element.closest('.article').find('.total span').text(data.total.toString());
					$element.closest('.rating').html('Vaše hodnocení: ' + $rate);
				}
			})
			.fail(function() {
				alert('Něco je špatně, zkuste to prosím znovu.');
			});
		} else {
			alert('Něco je špatně, zkuste to prosím znovu.');
		}
		
		e.preventDefault();
	});
});
