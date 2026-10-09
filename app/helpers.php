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

if (! function_exists('sosmed_search_terms')) {
    /**
     * Pecah kata kunci per spasi. Semua kata harus cocok (urutan bebas), jadi
     * "fashion pria" tetap menemukan akun bernama "Fashion Pria Official".
     *
     * @return array<int, string>
     */
    function sosmed_search_terms(?string $search): array
    {
        $search = trim((string) $search);

        return $search === '' ? [] : (preg_split('/\s+/u', $search) ?: []);
    }
}

if (! function_exists('sosmed_text_matches')) {
    /** Semua kata kunci terdapat di dalam teks (case-insensitive). */
    function sosmed_text_matches(?string $text, array $terms): bool
    {
        if ($terms === []) {
            return false;
        }

        $haystack = mb_strtolower((string) $text);
        foreach ($terms as $term) {
            if (! str_contains($haystack, mb_strtolower($term))) {
                return false;
            }
        }

        return true;
    }
}

if (! function_exists('sosmed_filter_account_search')) {
    /**
     * Filter pencarian daftar akun sosmed (tab akun, Staff & Admin).
     *
     * Tipe 'wide' dipakai daftar "Akun Sosmed Saya": hanya kolom akun, tanpa fallback nama orang.
     *
     * Untuk kriteria 'all': kalau ada akun yang nama/username/platform/brand-nya cocok dengan
     * kata kunci, hanya akun-akun itu yang diambil. Nama orang (pengelola/PM/asisten/staff
     * pengawas) baru dipakai ketika kata kunci tidak cocok dengan akun mana pun — jadi search
     * "fashion" hanya menampilkan akun berisi kata itu, bukan akun yang kebetulan dikelola
     * orang bernama mirip.
     *
     * @param  \Illuminate\Contracts\Database\Eloquent\Builder  $query
     * @return \Illuminate\Contracts\Database\Eloquent\Builder
     */
    function sosmed_filter_account_search($query, ?string $search, ?string $searchType = 'all')
    {
        $terms = sosmed_search_terms($search);
        if ($terms === []) {
            return $query;
        }

        $columns = [
            'account' => ['name', 'username'],
            'wide'    => ['name', 'username', 'platform', 'brand'],
            'brand'   => ['brand'],
        ];

        $byFields = function ($q, array $cols) use ($terms) {
            foreach ($terms as $term) {
                $like = '%' . $term . '%';
                $q->where(function ($qq) use ($cols, $like) {
                    foreach ($cols as $col) {
                        $qq->orWhere($col, 'like', $like);
                    }
                });
            }
        };

        $byPeople = function ($q, array $relations) use ($terms) {
            foreach ($relations as $relation) {
                $q->orWhereHas($relation, function ($sq) use ($terms) {
                    foreach ($terms as $term) {
                        $sq->where('users.name', 'like', '%' . $term . '%');
                    }
                });
            }
        };

        $personRelations = ['staffUsers', 'pmUser', 'assistantUser', 'supervisorStaff'];

        $filter = match ($searchType) {
            'account'   => fn($q) => $byFields($q, $columns['account']),
            'wide'      => fn($q) => $byFields($q, $columns['wide']),
            'brand'     => fn($q) => $byFields($q, $columns['brand']),
            'manager'   => fn($q) => $byPeople($q, ['staffUsers']),
            'pm'        => fn($q) => $byPeople($q, ['pmUser']),
            'assistant' => fn($q) => $byPeople($q, ['assistantUser']),
            'staff'     => fn($q) => $byPeople($q, ['supervisorStaff']),
            default     => (clone $query)->where(fn($q) => $byFields($q, $columns['wide']))->exists()
                ? fn($q) => $byFields($q, $columns['wide'])
                : fn($q) => $byPeople($q, $personRelations),
        };

        return $query->where($filter);
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
        $terms = sosmed_search_terms($search);
        $rows = collect();

        foreach ($accounts as $acc) {
            $managers = $acc->staffUsers;
            $list = $managers;

            if ($terms !== []) {
                $matchesUser = fn($u) => sosmed_text_matches($u->name, $terms);
                if ($searchType === 'manager') {
                    // Hanya pengelola yang namanya cocok yang menjadi baris.
                    $list = $managers->filter($matchesUser);
                } elseif (! in_array($searchType, ['account', 'brand', 'pm', 'assistant', 'staff'], true)) {
                    // Kriteria 'all': kalau akunnya sendiri yang cocok, semua pengelolanya tampil;
                    // kalau yang cocok nama orangnya, hanya baris orang itu yang tersisa.
                    $accMatch = sosmed_text_matches($acc->name, $terms)
                        || sosmed_text_matches($acc->username, $terms)
                        || sosmed_text_matches($acc->platform, $terms)
                        || sosmed_text_matches($acc->brand, $terms);
                    if (! $accMatch) {
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