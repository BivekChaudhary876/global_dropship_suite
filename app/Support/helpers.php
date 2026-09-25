<?php

/**
 * Small, app-wide helper functions. Loaded globally via the composer.json
 * "autoload.files" entry (see composer.json), so any controller, model,
 * or Blade view can call these directly - no `use` statement needed.
 *
 * Keep this file for genuinely tiny, stateless formatting helpers only.
 * Anything with real logic (image handling, form option lists) belongs in
 * a proper class instead - see app/Support/ImageUploader.php and
 * app/Http/Controllers/Concerns/HasProductFormOptions.php.
 */

if (! function_exists('money')) {
    /**
     * Format a numeric amount as a currency string.
     * Was previously written out by hand as number_format($x, 2) in six
     * different Blade files, sometimes with a leading $ and sometimes not.
     */
    function money(float|string $amount, string $symbol = '$'): string
    {
        return $symbol.number_format((float) $amount, 2);
    }
}

if (! function_exists('initials')) {
    /**
     * "Jamie Customer" -> "JC". Used for the small avatar circles in the
     * navbar and review list instead of repeating the same substr/explode
     * logic wherever an avatar is shown.
     */
    function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        $letters = array_map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)), array_filter($parts));

        return implode('', array_slice($letters, 0, 2)) ?: '?';
    }
}