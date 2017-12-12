<?php

namespace reqc;

define("reqc\TYPES", [
	"CLI" => 0,
	"HTTP" => 1,
	"PAGE" => 2,
	"ASSET" => 3
]);

define("reqc\TYPE", php_sapi_name() == "cli" ? TYPES["CLI"] : TYPES["HTTP"]);

// HTTP Constants
if(TYPE == TYPES["HTTP"]) {

	$uri = strtok($_SERVER["REQUEST_URI"], "?");
	$path = (strpos($uri, ".")) ? strstr($uri, ".", true) : $uri;
	$parts = explode("/", $path);
	$directory = implode("/", array_slice($parts, 0, -1));
	$filename = array_reverse($parts)[0];
	$extension = (ltrim(strstr($uri, "."), "."));
	$extension = ($extension == "") ? null : $extension;
	$domainParts = array_reverse(explode(".", $_SERVER["HTTP_HOST"]));

	parse_str(strtok("?"), $_GET);
	$input = file_get_contents('php://input');
	$json = json_decode($input, true);

	if(parse_url("?".$input, PHP_URL_QUERY)) parse_str($input, $_REQUEST);
	if(json_last_error() == JSON_ERROR_NONE) $_REQUEST = $json;

	define("reqc\PROTOCOL", $_SERVER["SERVER_PROTOCOL"]);
	define("reqc\SSL", (bool)($_SERVER["SSL"] ?? false));
	define("reqc\IP", $_SERVER["REMOTE_ADDR"]);
	define("reqc\PORT", (int)$_SERVER['SERVER_PORT']);
	define("reqc\METHOD", strtoupper($_SERVER['REQUEST_METHOD']));
	define("reqc\BASEURL", $_SERVER["SERVER_NAME"]);
	define("reqc\FULLURL", (SSL ? "https://" : "http://").$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
	define("reqc\URI", strtok($_SERVER["REQUEST_URI"], "?"));
	define("reqc\SUBTYPE", (isset($extension) && !in_array($extension, ["html", "php"])) ? TYPES["ASSET"] : TYPES["PAGE"]);
	define("reqc\DIRECTORY", $directory);
	define("reqc\FILENAME", $filename);
	define("reqc\EXTENSION", $extension);
	define("reqc\FILE", $filename.((isset($extension)) ? ".".$extension : ""));
	define("reqc\PATH", $directory."/".$filename.((isset($extension)) ? ".".$extension : ""));
	define("reqc\H2PUSH", (bool)($_SERVER["H2PUSH"] ?? false));

	// get headers
	$headers = [];
	foreach($_SERVER as $key => $val) {
		if(substr(strtolower($key), 0, 5) == "http_") $headers[substr($key, 5)] = $val;
	}
	$headers["ACCEPT"] = explode(",", $headers["ACCEPT"]);
	define("reqc\HEADERS", $headers);

	// keep backwards compatibility with v1.0-1.3
	define("reqc\ACCEPT", HEADERS["ACCEPT"]);
	define("reqc\HOST", HEADERS["HOST"]);

	if(!filter_var(HOST, \FILTER_VALIDATE_IP)) {
		define("reqc\TLD", $domainParts[0]);
		if(count($domainParts) >= 2) define("reqc\DOMAIN", $domainParts[1]);
		if(count($domainParts) > 2) define("reqc\SUBDOMAIN", implode(".", array_slice($domainParts, 2)));
	} else define("reqc\HOSTISIP", true);

// CLI Constants
} else {

	if(isset($argv) && count($argv) > 0) {
		foreach ($argv as $arg) {
	    	$e = explode("=",$arg);

	    	if(count($e) == 2) $_REQUEST[$e[0]] = $e[1];
	    	else $_REQUEST[$e[0]] = true;
	    }
	}
}

// MIME TYPES

define("reqc\MIME_TYPES", [
	"PHP" => "text/html",
	"HTML" => "text/html",
	"XML" => "application/xml",
	"JSON" => "application/json",
	"JS" => "text/javascript",
	"CSS" => "text/css",
	"WOFF" => "application/font-woff",
	"WOFF2" => "font/woff2",
	"TTF" => "font/ttf",
	"PNG" => "image/png",
	"JPG" => "image/jpeg",
	"JPEG" => "image/jpeg",
	"GIF" => "image/gif",
	"PDF" => "application/pdf",
	"WEBP" => "image/webp",
	"OTF" => "application/font-otf",
	"ICO" => "image/x-icon",
	"TPL" => "text/html",
	"EVENT_STREAM" => "text/event-stream\n\n"
]);

define("reqc\VARS", $_REQUEST);
