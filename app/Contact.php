<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Contact
 *
 * @property int $id
 * @property int $supplier_id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $email
 * @property string $role
 * @property string $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Supplier $supplier
 * @method static Builder|Contact newModelQuery()
 * @method static Builder|Contact newQuery()
 * @method static Builder|Contact query()
 * @method static Builder|Contact whereCreatedAt($value)
 * @method static Builder|Contact whereEmail($value)
 * @method static Builder|Contact whereFirstName($value)
 * @method static Builder|Contact whereId($value)
 * @method static Builder|Contact whereLastName($value)
 * @method static Builder|Contact whereNotes($value)
 * @method static Builder|Contact wherePhone($value)
 * @method static Builder|Contact whereRole($value)
 * @method static Builder|Contact whereSupplierId($value)
 * @method static Builder|Contact whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Contact extends Model
{
    protected $fillable = [
        'supplier_id', 'first_name', 'last_name',
        'phone', 'email', 'role', 'notes',
    ];

    /**
     * Relations
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
