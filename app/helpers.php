<?php

if (! function_exists('linkify')) {
    /**
     * Konversi URL dalam teks menjadi tag <a> yang bisa diklik.
     * Teks harus sudah di-escape (e()) sebelum dimasukkan ke sini.
     */
    function linkify(string $text): string
    {
        $pattern = '/(https?:\/\/[^\s<>"\']+)/i';
        $replacement = '<a href="$1" target="_blank" rel="noopener noreferrer" '
            . 'class="text-primary-600 underline hover:text-primary-800 break-all">$1</a>';

        return preg_replace($pattern, $replacement, $text);
    }
}