# Compactor for Laravel

Doc soon.

Here's an example of how to use it in a controller.

	$less_path	= path('public').'less/';
	$css_path	= path('public').'css/';
	
	Bundle::start('compactor');
	$compactor = new Compactor();


	// Compiling LESS into CSS

	$compactor->compile_less_file($less_path.'styles.less', $css_path.'styles.css');
	$compactor->compile_less_file($less_path.'other_styles.less', $css_path.'other_styles.css');


	// Minifying and combining
	
	$css_files	= array(
		$css_path.'styles.css',
		$css_path.'other_styles.css'
	);

	$compactor->combine_files($css_files)->save_file($css_path.'style.min.css');