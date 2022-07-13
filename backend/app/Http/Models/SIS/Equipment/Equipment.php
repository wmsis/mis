<?php

namespace App\Http\Models\SIS\Equipment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Http\Models\SIS\Equipment\Equipment
 *
 * @property int $id
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $name 设备名称
 * @property string|null $model 设备型号
 * @property string|null $manufacturer 生产厂家
 * @property string|null $serial_number 出厂编号
 * @property string|null $production_date 投产日期
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\SIS\HistorianTag[] $equipment_params
 * @property-read int|null $equipment_params_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\Equipment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\Equipment newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\Equipment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\Equipment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\Equipment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\Equipment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\Equipment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\Equipment whereManufacturer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\Equipment whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\Equipment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\Equipment whereProductionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\Equipment whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\Equipment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\Equipment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\Equipment withoutTrashed()
 * @mixin \Eloquent
 */
class Equipment extends Model
{
    use softDeletes;

    protected $table = 'equipment';
    protected $fillable = ['name', 'model', 'manufacturer', 'serial_number', 'production_date', 'status', 'charge_person_name', 'charge_person_phone', 'work_location'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function equipment_params()
    {
        return $this->hasMany('App\Http\Models\SIS\Equipment\EquipmentParam');
    }

    public function equipment_change_records()
    {
        return $this->hasMany('App\Http\Models\SIS\Equipment\EquipmentChangeRecord');
    }

    public function equipment_spare_parts()
    {
        return $this->hasMany('App\Http\Models\SIS\Equipment\EquipmentSparePart');
    }

    public function equipment_maintenance_records()
    {
        return $this->hasMany('App\Http\Models\SIS\Equipment\EquipmentMaintenanceRecord');
    }
}

/**
 * @SWG\Definition(
 *     definition="Equipment",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="name",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="model",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="manufacturer",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="serial_number",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="production_date",
 *         type="string",
 *         format="date"
 *     ),
 *     @SWG\Property(
 *         property="status",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="charge_person_name",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="charge_person_phone",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="work_location",
 *         type="string"
 *     )
 * )
 */

