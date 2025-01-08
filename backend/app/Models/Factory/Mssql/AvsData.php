<?php

namespace App\Models\Factory\Mssql;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvsData extends Model
{
    use HasFactory;
    public $timestamps = FALSE;
    protected $table = 'WeightRecord';
    protected $fillable = ['Id', 'RecordNo', 'FlowNo', 'Facility', 'Source', 'Driver', 'VehicleNo', 'VehicleType', 'TransDept', 'ShiftType', 'ComputerName', 'WeightChecker', 'InstrumentNo', 'ImportStatus', 'WeightGross', 'WeightTare', 'WeightNet', 'WeightDeduction', 'BillingUnit', 'BillingTotal', 'TimeLoading', 'TimeWeightingG', 'TimeWeightingT', 'TimeWeightingN', 'Memo', 'RecordStatus', 'RecordCreatedTime', 'RecordCreatorId', 'FieldNo', 'InputType', 'GarbageType', 'RecordVersion', 'VehicleDoorNo', 'DepartmentSTA', 'TimePhoto', 'RfidTag', 'Region'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function insertOne($params){
        return self::create($params);
    }

    public function insertMany($params){
        return self::insert($params);
    }

    public function findRowBySn($sn){
        return self::where('Id', $sn)->first();
    }
}
