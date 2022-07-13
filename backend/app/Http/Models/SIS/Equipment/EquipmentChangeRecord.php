<?php

namespace App\Http\Models\SIS\Equipment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Http\Models\SIS\Equipment\EquipmentChangeRecord
 *
 * @property int $id
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $date 记录时间
 * @property string|null $record 投产更改记录
 * @property int|null $equipment_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord whereEquipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord whereRecord($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\EquipmentChangeRecord withoutTrashed()
 * @mixin \Eloquent
 */
class EquipmentChangeRecord extends Model
{
    use softDeletes;

    protected $table = 'equipment_change_record';
    protected $fillable = ['date', 'record', 'equipment_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}

/**
 * @SWG\Definition(
 *     definition="EquipmentChangeRecord",
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
 *         property="record",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="equipment_id",
 *         type="integer"
 *     ),
 * )
 */

