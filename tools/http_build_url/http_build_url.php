<?php

/**
 * URL constants as defined in the PHP Manual under "Constants usable with
 * http_build_url()".
 *
 * @see http://us2.php.net/manual/en/http.constants.php#http.constants.url
 * @see https://github.com/jakeasmith/http_build_url
 */
if (!defined('HTTP_URL_REPLACE')) {
	define('HTTP_URL_REPLACE', 1);
}
if (!defined('HTTP_URL_JOIN_PATH')) {
	define('HTTP_URL_JOIN_PATH', 2);
}
if (!defined('HTTP_URL_JOIN_QUERY')) {
	define('HTTP_URL_JOIN_QUERY', 4);
}
if (!defined('HTTP_URL_STRIP_USER')) {
	define('HTTP_URL_STRIP_USER', 8);
}
if (!defined('HTTP_URL_STRIP_PASS')) {
	define('HTTP_URL_STRIP_PASS', 16);
}
if (!defined('HTTP_URL_STRIP_AUTH')) {
	define('HTTP_URL_STRIP_AUTH', 32);
}
if (!defined('HTTP_URL_STRIP_PORT')) {
	define('HTTP_URL_STRIP_PORT', 64);
}
if (!defined('HTTP_URL_STRIP_PATH')) {
	define('HTTP_URL_STRIP_PATH', 128);
}
if (!defined('HTTP_URL_STRIP_QUERY')) {
	define('HTTP_URL_STRIP_QUERY', 256);
}
if (!defined('HTTP_URL_STRIP_FRAGMENT')) {
	define('HTTP_URL_STRIP_FRAGMENT', 512);
}
if (!defined('HTTP_URL_STRIP_ALL')) {
	define('HTTP_URL_STRIP_ALL', 1024);
}

if (!function_exists('http_build_url')) {

	/**
	 * Build a URL.
	 *
	 * The parts of the second URL will be merged into the first according to
	 * the flags argument.
	 *
	 * @param mixed $url     (part(s) of) an URL in form of a string or
	 *                       associative array like parse_url() returns
	 * @param mixed $parts   same as the first argument
	 * @param int   $flags   a bitmask of binary or'ed HTTP_URL constants;
	 *                       HTTP_URL_REPLACE is the default
	 * @param array $new_url if set, it will be filled with the parts of the
	 *                       composed url like parse_url() would return
	 * @return string
	 */
	function http_build_url($url, $parts = array(), $flags = HTTP_URL_REPLACE, &$new_url = array())
	{
		is_array($url) || $url = parse_url($url);
		is_array($parts) || $parts = parse_url($parts);

		isset($url['query']) && is_string($url['query']) || $url['query'] = null;
		isset($parts['query']) && is_string($parts['query']) || $parts['query'] = null;

		$keys = array('user', 'pass', 'port', 'path', 'query', 'fragment');

		// HTTP_URL_STRIP_ALL and HTTP_URL_STRIP_AUTH cover several other flags.
		if ($flags & HTTP_URL_STRIP_ALL) {
			$flags |= HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS
				| HTTP_URL_STRIP_PORT | HTTP_URL_STRIP_PATH
				| HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT;
		} elseif ($flags & HTTP_URL_STRIP_AUTH) {
			$flags |= HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS;
		}

		// Schema and host are alwasy replaced
		foreach (array('scheme', 'host') as $part) {
			if (isset($parts[$part])) {
				$url[$part] = $parts[$part];
			}
		}

		if ($flags & HTTP_URL_REPLACE) {
			foreach ($keys as $key) {
				if (isset($parts[$key])) {
					$url[$key] = $parts[$key];
				}
			}
		} else {
			if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH)) {
				if (isset($url['path']) && substr($parts['path'], 0, 1) !== '/') {
					$url['path'] = rtrim(
							str_replace(basename($url['path']), '', $url['path']),
							'/'
						) . '/' . ltrim($parts['path'], '/');
				} else {
					$url['path'] = $parts['path'];
				}
			}

			if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY)) {
				if (isset($url['query'])) {
					parse_str($url['query'], $url_query);
					parse_str($parts['query'], $parts_query);

					$url['query'] = http_build_query(
						Tools::arrayReplaceRecursive(
							$url_query,
							$parts_query
						)
					);
				} else {
					$url['query'] = $parts['query'];
				}
			}
		}

		foreach ($keys as $key) {
			$strip = 'HTTP_URL_STRIP_' . strtoupper($key);
			if ($flags & constant($strip)) {
				unset($url[$key]);
			}
		}

		$parsed_string = '';

		if (isset($url['scheme'])) {
			$parsed_string .= $url['scheme'] . '://';
		}

		if (isset($url['user'])) {
			$parsed_string .= $url['user'];

			if (isset($url['pass'])) {
				$parsed_string .= ':' . $url['pass'];
			}

			$parsed_string .= '@';
		}

		if (isset($url['host'])) {
			$parsed_string .= $url['host'];
		}

		if (isset($url['port'])) {
			$parsed_string .= ':' . $url['port'];
		}

		if (!empty($url['path'])) {
			$parsed_string .= $url['path'];
		} else {
			$parsed_string .= '/';
		}

		if (isset($url['query'])) {
			$parsed_string .= '?' . $url['query'];
		}

		if (isset($url['fragment'])) {
			$parsed_string .= '#' . $url['fragment'];
		}

		$new_url = $url;

		return $parsed_string;
	}
}
