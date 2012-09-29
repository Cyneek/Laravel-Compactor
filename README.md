# Laravel Compactor v.1.2

A minification bundle for Laravel


Based on the Minify Driver for Codeigniter by Eric Barnes.

NOTICE OF LICENSE

Licensed under the Open Software License version 3.0

This source file is subject to the Open Software License (OSL 3.0) that is
bundled with this package in the files license.txt / license.rst.  It is
also available through the world wide web at this URL:
http://opensource.org/licenses/OSL-3.0

LICENSE: http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)

## Author list: 

		Jeroen Van Meerendonk
		Joseba Juániz
		Eric Barnes

## API LIST OF METHODS:
	
	Compactor class:
		
		- combine_file($file): 		Selects a file to compact, you can pass a string parameter
									with just one file to compact or pass an array of strings with
									a list of files that will be checked for compactation.
			
			example: $compactor->combine_files('../file.css');
			example: $compactor->combine_files(array('../file1.css','../file2.css'));
	
	
		- combine_directory($dir, (opt)$ignore): 	Selects a group of css or js files from a directory 
													to compact you can pass optionally a second parameter 
													with an array of files to be ignored in the compact 
													process.
			
			example: $compactor->combine_directory('../css_files');
			example: $compactor->combine_directory('../css_files', array('../css_files/file1.css'));
		
		- save_file($file)		Compacts all the files selected and saves the stream
								in the given route.
			
			example: $compactor->save_file('../css/compact.css');
			example: $compactor->combine_directory('../css_files')->save_file('../css/compact.css');
		
		- show_contents($type, $compact = TRUE, $css_charset = 'utf-8'):
								
								Returns in a string the compacted contents of the selected files.
			
			example: $compactor->show_contents();
			example: $compactor->combine_directory('../css_files')->show_contents();
	
	Css class:
	
		- min($file, $compact = TRUE, $is_aggregated = NULL): 	Returns a string with the data from the file
																passed throught the $file parameter.
	
	Jss class:
	
		- min($file, $compact = TRUE):		Returns a string with the data from the file
											passed through the $file parameter.
	
	Less class:
	
		(from all the list of methods, the most important)
		
		- compileFile($fname, $outFname = null):		Compiles the less file passed throught the $fname
														parameter and saves it in the file set on $outFname.
														If the second parameter is NULL, the method will 
														return a string with all the data.
																

## EXAMPLES


	Here's an example of how to use it in a controller.

	$less_path	= path('public').'less/';
	$css_path	= path('public').'css/';
	$js_path	= path('public').'js/';
	
	Bundle::start('compactor');
	$compactor = new Compactor();


	// Compiling LESS into CSS

	$compactor->less->compileFile($less_path.'styles.less', $css_path.'styles.css');
	$compactor->less->compileFile($less_path.'other_styles.less', $css_path.'other_styles.css');


	// Minifying and combining
	
	$css_files	= array(
		$css_path.'styles.css',
		$css_path.'other_styles.css'
	);

	$compactor->combine_files($css_files)->save_file($css_path.'style.min.css');
	$compactor->combine_directory(path('public').'css_files')->save_file($css_path.'style2.min.css');
	
	// Direct access to css and js methods
	
	echo $compactor->js->min($js_path.'header.js');
	echo $compactor->css->min($css_path.'styles.css');
	
	
