<?PHP
class Template {

	private static $path = 'templates/template.';
	private static $ext = '.php';
	
	public static $CLOCK_DASH_TOTAL = 47;

	public static function show($templateName, $_) {
		include(Template::$path.$templateName.Template::$ext);
	}
	
}
?>