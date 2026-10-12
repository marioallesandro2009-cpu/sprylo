<?php

namespace App\Console\Commands;

use App\Models\PageContent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CmsScan extends Command
{
    protected $signature = 'cms:scan {--prune : Hapus key yang sudah tidak dipakai di view manapun}';

    protected $description = 'Scan view Blade untuk pemanggilan cms() lalu daftarkan sebagai konten yang bisa diedit di admin';

    public function handle(): int
    {
        $regex = "/\bcms\(\s*'([\w.\-]+)'\s*,\s*'((?:[^'\\\\]|\\\\.)*)'/s";
        $found = [];

        foreach (File::allFiles(resource_path('views')) as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            if (str_starts_with(str_replace('\\', '/', $file->getRelativePathname()), 'admin/')) {
                continue;
            }

            if (preg_match_all($regex, File::get($file->getPathname()), $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $found[$m[1]] ??= str_replace(["\\'", '\\\\'], ["'", '\\'], $m[2]);
                }
            }
        }

        $order = 0;
        $created = 0;
        $updated = 0;

        foreach ($found as $key => $default) {
            $segments = explode('.', $key);
            $page = $segments[0];
            $last = end($segments);

            $label = collect(array_slice($segments, 1))
                ->map(fn ($s) => Str::headline($s))
                ->implode(' - ') ?: Str::headline($key);

            $type = match (true) {
                str_contains($last, 'image') => 'image',
                str_contains($last, 'url'), str_contains($last, 'link') => 'url',
                strlen($default) > 100, str_contains($default, "\n") => 'textarea',
                default => 'text',
            };

            $row = PageContent::firstOrNew(['key' => $key]);
            $isNew = ! $row->exists;

            $row->fill([
                'page' => $page,
                'label' => $label,
                'type' => $type,
                'default_value' => $default,
                'sort_order' => ++$order,
            ])->save();

            $isNew ? $created++ : $updated++;
        }

        $deleted = 0;

        if ($this->option('prune') && $found !== []) {
            $deleted = PageContent::whereNotIn('key', array_keys($found))->delete();
        }

        PageContent::flush();

        $this->info("Scan selesai: {$created} baru, {$updated} diperbarui, {$deleted} dihapus.");

        return self::SUCCESS;
    }
}