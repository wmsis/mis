<?php

namespace App\Http\Models\SIS\Equipment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Http\Models\SIS\Equipment\EquipmentParam
 *
 * @property int $id
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $name 参数名
 * @property string|null $value 参数值
 * @property int|null $equipment_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam whereEquipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\EquipmentParam withoutTrashed()
 * @mixin \Eloquent
 */
class EquipmentParam extends Model
{
    use softDeletes;

    protected $table = 'equipment_param';
    protected $fillable = ['name', 'value', 'equipment_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}

/**
 * @SWG\Definition(
 *     definition="EquipmentParam",
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
 *         property="value",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="equipment_id",
 *         type="integer"
 *     ),
 * )
 */

