<?php

namespace App\Http\Models\SIS\Equipment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord
 *
 * @property int $id
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $name 参数名
 * @property string|null $value 参数值
 * @property int|null $equipment_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord whereEquipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord withoutTrashed()
 * @mixin \Eloquent
 */
class EquipmentMaintenanceRecord extends Model
{
    use softDeletes;

    protected $table = 'equipment_maintenance_record';
    protected $fillable = ['date', 'kind', 'supervisor', 'evaluation', 'members', 'prev_status', 'maintenance_content',
        'test_status', 'after_status', 'recorder', 'record_time', 'equipment_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'record_time' => 'datetime:Y-m-d H:i:s'
    ];

    public function equipment()
    {
        return $this->belongsTo('App\Http\Models\SIS\Equipment\Equipment');
    }
}

/**
 * @SWG\Definition(
 *     definition="EquipmentMaintenanceRecord",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="date",
 *         type="string",
 *         format="date"
 *     ),
 *     @SWG\Property(
 *         property="kind",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="supervisor",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="evaluation",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="members",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="prev_status",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="maintenance_content",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="test_status",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="after_status",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="recorder",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="record_time",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="equipment_id",
 *         type="integer"
 *     ),
 * )
 */

