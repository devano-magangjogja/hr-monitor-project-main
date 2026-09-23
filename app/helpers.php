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

if (! function_exists('store_image_as_webp')) {
    /**
     * Simpan gambar upload sebagai WebP ke disk public agar ukuran file hemat.
     * Mengembalikan path relatif (mis. "task-proofs/xxx.webp").
     * Jika konversi gagal (GD tidak tersedia / format tidak didukung),
     * file disimpan apa adanya.
     */
    function store_image_as_webp(Illuminate\Http\UploadedFile $file, string $directory, int $quality = 82): string
    {
        $disk = Illuminate\Support\Facades\Storage::disk('public');

        // WebP tidak perlu dikonversi ulang
        if ($file->getMimeType() === 'image/webp') {
            return $file->store($directory, 'public');
        }

        if (! function_exists('imagewebp')) {
            return $file->store($directory, 'public');
        }

        $image = match ($file->getMimeType()) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($file->getPathname()),
            'image/png' => @imagecreatefrompng($file->getPathname()),
            'image/gif' => @imagecreatefromgif($file->getPathname()),
            'image/bmp' => @imagecreatefrombmp($file->getPathname()),
            default => false,
        };

        if ($image === false) {
            return $file->store($directory, 'public');
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        if (! $disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $path = $directory . '/' . Illuminate\Support\Str::random(40) . '.webp';
        $saved = imagewebp($image, $disk->path($path), $quality);

        if (! $saved) {
            return $file->store($directory, 'public');
        }

        return $path;
    }
}