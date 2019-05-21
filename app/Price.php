<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Item|null item
 * @property DateTime created_at
 * @property integer id
 * @property integer is_active
 * @property float second_value
 * @property DateTime updated_at
 * @property float value
 */
class Price extends Model
{

    protected $fillable = ['value', 'second_value', 'is_active'];
    ##
    # Relationships
    ##

    /**
     * @return BelongsTo
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    ##
    # Functions
    ##

    /**
     * @return void
     */
    public function activate()
    {
        $this->isActive() ?: $this->update(['is_active' => true]);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return boolval($this->is_active);
    }

    /**
     * @return void
     */
    public function deactivate()
    {
        !$this->isActive() ?: $this->update(['is_active' => false]);
    }
}
