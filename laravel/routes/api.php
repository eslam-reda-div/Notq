<?php

if (! function_exists('requireRoutesRecursively')) {
    function requireRoutesRecursively($path, $prefix = '')
    {
        $files = glob($path.'/*.php');
        foreach ($files as $file) {
            if (!empty($prefix)) {
                Route::prefix($prefix)->group(function () use ($file) {
                    require $file;
                });
            } else {
                require $file;
            }
        }

        // Get all subdirectories
        $directories = glob($path.'/*', GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $dirName = basename($directory);

            // Check if this is a version directory (starts with 'v' followed by number)
            if (preg_match('/^v\d+$/', $dirName)) {
                // Use the version folder name as prefix for all nested routes
                requireRoutesRecursively($directory, $dirName);
            } else {
                // For non-version directories, keep the existing prefix
                requireRoutesRecursively($directory, $prefix);
            }
        }
    }
}

requireRoutesRecursively(base_path('routes/api'));
