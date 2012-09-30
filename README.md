# Laravel Compactor

A minification bundle for Laravel, based on the [Minify Driver for CodeIgniter](https://github.com/ericbarnes/ci-minify) by [Eric Barnes](http://ericlbarnes.com/).


## Credits
* Jeroen van Meerendonk - [GitHub](http://github.com/jeroen) - [Twitter](http://twitter.com/jeroen_bz)
* Joseba Juániz - [GitHub](http://github.com/patroklo) - [Twitter](http://twitter.com/patroklo)

## Usage
	
### Compactor class
	
#### `combine_file($file)`
Selects a file to compact. You can pass a string parameter with just one file to compact or pass an array of strings with a list of files that will be checked for compactation.

	$compactor->combine_files('../file.css');
	$compactor->combine_files(array('../file1.css','../file2.css'));

#### `combine_directory($dir, (opt)$ignore)`
Selects a group of css, less or js files from a directory to compact. You can pass optionally a second parameter with an array of files to be ignored in the compact process.

	$compactor->combine_directory('../css_files');
	$compactor->combine_directory('../css_files', array('../css_files/file1.css'));


#### `save_file($file)`
Compacts all the files selected and saves the stream in the given route.

	$compactor->save_file('../css/compact.css');
	$compactor->combine_directory('../css_files')->save_file('../css/compact.css');


#### `show_contents($type, $compact = TRUE, $css_charset = 'utf-8')`
Returns in a string the compacted contents of the selected files.

	$compactor->show_contents();
	$compactor->combine_directory('../css_files')->show_contents();


### CSS class

`min($file, $compact = TRUE, $is_aggregated = NULL)`: Returns a string with the data from the file passed throught the $file parameter.

### JS class

`min($file, $compact = TRUE)`: Returns a string with the data from the file passed through the $file parameter.

### LESS class
	
`min($file, $compact = TRUE, $is_aggregated = NULL)`: Compiles the less file passed throught the $fname parameter and saves it in the file set on $outFname. If the second parameter is NULL, the method will return a string with all the data.
											

## Examples

Here's an example of how to use it in a controller.

	Bundle::start('compactor');
	$compactor = new Compactor();

	// The paths
	$less_path	= path('public').'less/';
	$css_path	= path('public').'css/';
	$js_path	= path('public').'js/';

	// Some groups
	$css_files	= array(
		$css_path.'bootstrap.css',
		$less_path.'style.less',
		$less_path.'admin.less'
	);
	
	$js_files = array(
		$js_path.'header.js',
		$js_path.'header2.js'
	);

	$compactor->combine_files($css_files)->save_file($css_path.'style.min.css');
	$compactor->combine_files($js_files)->save_file($js_path.'footer.min.js');
	
You can combine LESS and CSS files in the compactor, any other file will be ignored.

	$compactor
		->combine_files($less_files)
		->combine_directory(path('public').'css_files')
		->save_file($css_path.'style.min.css');
	
Direct access to css and js methods
	
	echo $compactor->js->min($js_path.'header.js');
	echo $compactor->css->min($css_path.'styles.css');
	echo $compactor->less->min($less_path.'syles.less');
	
## Change log

### 1.2
* Bug corrections.
* New documentation.