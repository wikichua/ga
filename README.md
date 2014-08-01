# ga (Google Analytic) for Laravel 4

## Setup


1. Add `wikichua/ga` to `composer.json`.
```
# composer require wikichua/ga dev-master
```

1. Add `'Wikichua\Ga\GaServiceProvider',` to `app/config/app.php` under providers array.
1. Add `'Ga'  =>  'Wikichua\Ga\Facades\Ga'` to `app/config/app.php` under aliases array.
1. Publish configuration.
```
# php artisan config:publish wikichua/ga
```
1. Create your CLIENT ID, EMAIL ADDRESS and download PUBLIC KEY FINGERPRINTS (P12 key) from [Google Developer Console](https://console.developers.google.com)
1. Fill up `app/config/wikichua/config/ga.php`

## Usage

1. Retrieve rows data in array.
```
$Ga = Ga::make('74924308')
	->from('360daysAgo')
	->to('today')
	->metrics('sessions')
	->metrics('entrances')
	->dimensions('country')
	->dimensions('region')
	->filters('country==Malaysia')
	->filters('country==Canada')
	->get();
	echo '<pre>';
	print_r($Ga);
	echo '</pre>';
```
```
$Ga = Ga::make('74924308')
	->range('2014-01-01','yesterday')
	->metrics('sessions','entrances')
	->dimensions('country','region')
	->filters('country==Malaysia','country==Canada')
	->get();
	echo '<pre>';
	print_r($Ga);
	echo '</pre>';
```
1. Retrive all in Object 
```
$Ga = Ga::make('74924308')
	->range('2014-01-01','yesterday')
	->metrics('sessions','entrances')
	->dimensions('country','region')
	->filters('country==Malaysia','country==Canada')
	->all();
	echo '<pre>';
	print_r($Ga);
	echo '</pre>';
```

## Tips
Explore this [Google Analytics Query Explorer 2](http://ga-dev-tools.appspot.com/explorer/)