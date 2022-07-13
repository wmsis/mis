<?php

namespace App\Http\Models\SIS\Equipment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * App\Http\Models\SIS\Equipment\EquipmentSparePart
 *
 * @property int $id
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $serial_number 序号
 * @property string $name 备品名称
 * @property string|null $model 规格型号
 * @property int|null $equipped_quantity 在装数量
 * @property string|null $manufacturer 生产厂家
 * @property string|null $remark 备注
 * @property int|null $equipment_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart whereEquipmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart whereEquippedQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart whereManufacturer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\Equipment\EquipmentSparePart withoutTrashed()
 * @mixin \Eloquent
 */
class EquipmentSparePart extends Model
{
    use softDeletes;

    protected $table = 'equipment_spare_part';
    protected $fillable = ['serial_number', 'name', 'model', 'equipped_quantity', 'remark', 'equipment_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];
}

/**
 * @SWG\Definition(
 *     definition="EquipmentSparePart",
 *     type="object",
 *     @SWG\Property(
 *         property="id",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="serial_number",
 *         type="string",
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
 *         property="equipped_quantity",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *         property="remark",
 *         type="string"
 *     ),
 *     @SWG\Property(
 *         property="equipment_id",
 *         type="integer"
 *     ),
 * )
 */

