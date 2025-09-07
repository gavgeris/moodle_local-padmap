<?php

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_padmap';
$plugin->version = 2025090700;       // YYYYMMDDXX
$plugin->release = '1.0.0';
$plugin->maturity = MATURITY_STABLE;

// Θέτουμε relatively-low requires ώστε να παίζει και σε 4.5+.
$plugin->requires = 2022041900;       // Moodle 4.0 (4.5 είναι νεότερο, οπότε οκ)
