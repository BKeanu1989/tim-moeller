<?php

require_once 'SimpleLogger.php';

SimpleLogger::setFile(__DIR__ . '/test.log');

SimpleLogger::info("test");
