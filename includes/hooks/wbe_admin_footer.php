<?php
add_action('admin_footer', function() {
    ?>
    <script>
		document.addEventListener('DOMContentLoaded', function () {
		var links = document.querySelectorAll('a[href]');
		for (var i = 0; i < links.length; i++) {
			(function(link) {
				var href = link.getAttribute('href');
				if (!href || href.indexOf('.php') === -1) return;

				var newHref = href.replace(/\.php(\?[^#]*)?/g, function(match, query) {
					return query || '';
				});

				link.setAttribute('href', newHref);
			})(links[i]);
		}
	});
	
	window.wbeApiSettings = {
		'nonce': <?= json_encode(wp_create_nonce('wp_rest')) ?>
	};

    </script>
    <?php
});