<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Supplier
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property Carbon|null $supplier_since
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Contact[] $contacts
 * @property-read int|null $contacts_count
 * @method static Builder|Supplier newModelQuery()
 * @method static Builder|Supplier newQuery()
 * @method static Builder|Supplier query()
 * @method static Builder|Supplier whereAddress($value)
 * @method static Builder|Supplier whereCreatedAt($value)
 * @method static Builder|Supplier whereDescription($value)
 * @method static Builder|Supplier whereEmail($value)
 * @method static Builder|Supplier whereId($value)
 * @method static Builder|Supplier whereName($value)
 * @method static Builder|Supplier wherePhone($value)
 * @method static Builder|Supplier whereSupplierSince($value)
 * @method static Builder|Supplier whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Supplier extends Model
{
    protected $fillable = [
        'name', 'description', 'address', 'phone', 'email', 'supplier_since',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'supplier_since',
    ];

    /**
     * Relations
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
