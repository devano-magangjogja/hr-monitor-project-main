<?php

if (! function_exists('normalize_url')) {
    /**
     * Lengkapi link yang ditulis tanpa skema (mis. "instagram.com/@itsodepth") supaya
     * menjadi URL absolut. Tanpa ini href-nya dianggap relatif dan browser membuka
     * halaman aplikasi (mis. /admin/instagram.com/@itsodepth).
     *
     * Nilai yang bukan URL (teks bebas, "-", "@username") dikembalikan apa adanya.
     */
    function normalize_url(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return $value;
        }

        // Sudah punya skema (http, https, ftp, dsb.) atau skema non-http yang sah.
        if (preg_match('#^[a-z][a-z0-9+.\-]*://#i', $value) || preg_match('#^(mailto|tel|sms|geo):#i', $value)) {
            return $value;
        }

        // Teks bebas (mengandung spasi) bukan URL tunggal → jangan dirubah.
        if (preg_match('/\s/', $value)) {
            return $value;
        }

        // Wujud host[:port]/path?query — butuh titik sebelum garis miring pertama.
        $hostLike = '~^[a-z0-9](?:[a-z0-9\-]*[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9\-]*[a-z0-9])?)+(?::\d+)?(?:[/?#][^\s]*)?$~i';
        if (preg_match($hostLike, $value)) {
            return 'https://' . $value;
        }

        return $value;
    }
}

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

if (! function_exists('sosmed_account_rows')) {
    /**
     * Pipihkan daftar akun menjadi daftar BARIS (satu baris per pengelola) lalu urutkan
     * berdasar waktu delegasi baris itu sendiri.
     *
     * Penting:urut per baris, bukan per akun. Kalau urutan memakai waktu delegasi
     * terbaru dari sebuah akun, semua pengelola lama akun tersebut ikut terangkat
     * ke atas hanya karena satu orang baru ditambahkan.
     *
     * @param  iterable<\App\Models\SosmedAccount>  $accounts
     * @return \Illuminate\Support\Collection<int, array{acc:\App\Models\SosmedAccount, stUser:?\App\Models\User}>
     */
    function sosmed_account_rows(iterable $accounts, ?string $search = null, ?string $searchType = 'all'): Illuminate\Support\Collection
    {
        $search = trim((string) $search);
        $rows = collect();

        foreach ($accounts as $acc) {
            $managers = $acc->staffUsers;
            $list = $managers;
            $needle = mb_strtolower($search);

            if ($search !== '') {
                $matchesUser = fn($u) => str_contains(mb_strtolower($u->name), $needle);
                if ($searchType === 'manager') {
                    // Hanya pengelola yang namanya cocok yang menjadi baris.
                    $list = $managers->filter($matchesUser);
                } elseif ($searchType === 'all') {
                    $accNameMatch = str_contains(mb_strtolower((string) $acc->name), $needle)
                        || str_contains(mb_strtolower((string) $acc->username), $needle);
                    if (! $accNameMatch) {
                        $filtered = $managers->filter($matchesUser);
                        $list = $filtered->isNotEmpty() ? $filtered : $managers;
                    }
                }
            }

            if ($list->isEmpty()) {
                // Akun tanpa pengelola (atau semua pengelolanya tersaring) tetap tampil 1 baris kosong.
                $rows->push(['acc' => $acc, 'stUser' => null]);
                continue;
            }

            foreach ($list as $u) {
                $rows->push(['acc' => $acc, 'stUser' => $u]);
            }
        }

        return $rows->sortByDesc(function ($row) {
            $assignedAt = $row['stUser']?->pivot->assigned_at ?? null;
            if ($assignedAt) {
                return $assignedAt instanceof \DateTimeInterface
                    ? $assignedAt->format('Y-m-d H:i:s')
                    : (string) $assignedAt;
            }
            return optional($row['acc']->created_at)->format('Y-m-d H:i:s') ?? '1970-01-01 00:00:00';
        })->values();
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