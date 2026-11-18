<?php

namespace App\Models\Concerns;

use App\Support\Audit;
use Illuminate\Support\Str;

/**
 * Records who created, changed or deleted a row, but only when an admin did it from the admin
 * panel. Customer actions (checkout, registration) and console work are never logged.
 *
 * Models can set `$auditIgnore` (fields that don't count as a change, e.g. read flags) and
 * override auditLabel()/auditType() for friendlier wording.
 */
trait Auditable
{
    /** Fields that never count as a change on their own. */
    private static array $auditAlwaysIgnore = ['updated_at', 'created_at', 'remember_token'];

    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            if ($actor = Audit::actor()) {
                Audit::record('created', "Created {$model->auditType()} “{$model->auditLabel()}”", $actor, $model);
            }
        });

        static::updated(function ($model) {
            if (! $actor = Audit::actor()) {
                return;
            }

            $ignored = array_merge(self::$auditAlwaysIgnore, $model->auditIgnore ?? []);
            $changes = [];

            foreach ($model->getChanges() as $field => $new) {
                if (in_array($field, $ignored, true)) {
                    continue;
                }

                $changes[$field] = [
                    Audit::displayValue($field, $model->getRawOriginal($field)),
                    Audit::displayValue($field, $new),
                ];
            }

            if ($changes === []) {
                return;
            }

            Audit::record('updated', "Updated {$model->auditType()} “{$model->auditLabel()}”: ".$model->auditSummary($changes), $actor, $model, $changes);
        });

        static::deleted(function ($model) {
            if ($actor = Audit::actor()) {
                Audit::record('deleted', "Deleted {$model->auditType()} “{$model->auditLabel()}”", $actor, $model);
            }
        });
    }

    /** Human name of the kind of record, e.g. "Product", "Flash sale". */
    public function auditType(): string
    {
        return match (class_basename($this)) {
            'Faq' => 'FAQ',
            'PageMeta' => 'Page SEO',
            'NavLink' => 'Navigation link',
            'PageContent' => 'Page content',
            default => Str::headline(class_basename($this)),
        };
    }

    /** What the record is called, e.g. the product name or order number. */
    public function auditLabel(): string
    {
        foreach (['name', 'title', 'order_number', 'code', 'label', 'question', 'page_slug', 'heading', 'email', 'key'] as $attribute) {
            $value = $this->getAttribute($attribute);

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return '#'.$this->getKey();
    }

    /**
     * "price: 100 → 200; stock: 5 → 4" — the first few changed fields, for the one-line description.
     *
     * @param  array<string, array{0: mixed, 1: mixed}>  $changes
     */
    public function auditSummary(array $changes): string
    {
        $parts = [];

        foreach (array_slice($changes, 0, 4, true) as $field => [$old, $new]) {
            $parts[] = $field === 'password'
                ? 'password changed'
                : Str::headline($field).': '.$this->auditShort($old).' → '.$this->auditShort($new);
        }

        $more = count($changes) - count($parts);

        return implode('; ', $parts).($more > 0 ? " (+{$more} more)" : '');
    }

    private function auditShort(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'yes' : 'no';
        }

        return Str::limit((string) $value, 60, '…');
    }
}
