<?php
/**
 * SimpleLogger - single-file, dependency-free debug logger (PHP 5.4+ / 7 / 8).
 *
 * Usage:
 *   require_once '/path/to/SimpleLogger.php';
 *   SimpleLogger::setFile('/var/log/myapp/debug.log');   // optional
 *
 *   SimpleLogger::log('plain message');
 *   SimpleLogger::log('user loaded', $userArray);         // arrays / objects
 *   SimpleLogger::error('payment failed', ['id' => 42]);
 *   SimpleLogger::trace('how did we get here?');          // full stack trace
 *   SimpleLogger::exception($e);                          // exception + its trace
 *
 *   dlog('quick', $anything);                             // global shortcut
 */
class SimpleLogger
{
    private static $file      = null;   // null = <system temp dir>/simple-debug.log
    private static $enabled   = true;
    private static $requestId = null;

    public static function setFile($path)    { self::$file = $path; }
    public static function enable($on = true) { self::$enabled = (bool) $on; }
    public static function getFile()
    {
        return self::$file !== null ? self::$file : sys_get_temp_dir() . '/simple-debug.log';
    }

    public static function log($message, $context = null)   { self::write('DEBUG', $message, $context, false); }
    public static function debug($message, $context = null) { self::write('DEBUG', $message, $context, false); }
    public static function info($message, $context = null)  { self::write('INFO',  $message, $context, false); }
    public static function warn($message, $context = null)  { self::write('WARN',  $message, $context, false); }
    public static function error($message, $context = null) { self::write('ERROR', $message, $context, false); }
    public static function trace($message, $context = null) { self::write('TRACE', $message, $context, true); }

    public static function exception($e, $message = 'Exception')
    {
        $text = sprintf(
            "%s: %s (code %s)\n  thrown in %s:%d\n%s",
            get_class($e), $e->getMessage(), $e->getCode(),
            $e->getFile(), $e->getLine(), $e->getTraceAsString()
        );
        self::write('ERROR', $message, $text, false);
    }

    private static function write($level, $message, $context, $withTrace)
    {
        if (!self::$enabled) {
            return;
        }

        // The logger must never break the app it is debugging.
        try {
            $frames = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            // Skip frames that belong to this logger to find the real caller.
            $i = 0;
            while (isset($frames[$i]) && (!isset($frames[$i]['file']) || $frames[$i]['file'] === __FILE__)) {
                $i++;
            }
            $file = isset($frames[$i]['file']) ? $frames[$i]['file'] : 'unknown';
            $line = isset($frames[$i]['line']) ? $frames[$i]['line'] : 0;
            $func = isset($frames[$i + 1]) ? self::frameName($frames[$i + 1]) : '{main}';

            $micro = microtime(true);
            $time  = date('Y-m-d H:i:s', (int) $micro) . sprintf('.%03d', ($micro - floor($micro)) * 1000);

            $entry = sprintf(
                "[%s] [%s] [%s] [%s] %s:%d %s - %s\n",
                $time, $level, self::requestId(), self::requestInfo(),
                $file, $line, $func, self::stringify($message)
            );

            if ($context !== null) {
                $entry .= self::indent(self::stringify($context)) . "\n";
            }

            if ($withTrace) {
                $entry .= "  Stack trace:\n";
                $n = 0;
                for ($j = $i; isset($frames[$j]); $j++) {
                    $f = $frames[$j];
                    $where = isset($f['file']) ? $f['file'] . ':' . $f['line'] : '[internal]';
                    $calledIn = isset($frames[$j + 1]) ? self::frameName($frames[$j + 1]) : '{main}';
                    $entry .= sprintf("    #%d %s %s\n", $n++, $where, $calledIn);
                }
            }

            if (@file_put_contents(self::getFile(), $entry, FILE_APPEND | LOCK_EX) === false) {
                error_log('[SimpleLogger] could not write to ' . self::getFile() . ': ' . $entry);
            }
        } catch (Exception $ex) {
            error_log('[SimpleLogger] ' . $ex->getMessage());
        }
    }

    private static function frameName(array $f)
    {
        $name = isset($f['function']) ? $f['function'] : '';
        if (isset($f['class'])) {
            $name = $f['class'] . $f['type'] . $name;
        }
        return $name . '()';
    }

    private static function stringify($value)
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_bool($value) || $value === null) {
            return var_export($value, true);
        }
        if (is_scalar($value)) {
            return (string) $value;
        }
        if ($value instanceof Exception || (interface_exists('Throwable') && $value instanceof Throwable)) {
            return get_class($value) . ': ' . $value->getMessage() . ' @ ' . $value->getFile() . ':' . $value->getLine();
        }
        return print_r($value, true); // arrays, objects (handles recursion)
    }

    private static function indent($text)
    {
        return '  ' . str_replace("\n", "\n  ", rtrim($text));
    }

    // Short id shared by all log lines of the same request, so concurrent requests can be told apart.
    private static function requestId()
    {
        if (self::$requestId === null) {
            self::$requestId = substr(md5(uniqid('', true)), 0, 8);
        }
        return self::$requestId;
    }

    private static function requestInfo()
    {
        if (PHP_SAPI === 'cli') {
            return 'CLI';
        }
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '?';
        $uri    = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '?';
        return $method . ' ' . $uri;
    }
}

if (!function_exists('dlog')) {
    function dlog($message, $context = null)
    {
        SimpleLogger::log($message, $context);
    }
}
