<?php
/**
* 电量映射关系控制器
*
* @author      cat 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\SIS\PowerMap;
use App\Models\SIS\ElectricityMap;
use App\Models\SIS\Orgnization;
use App\Models\SIS\DcsStandard;
use Illuminate\Support\Facades\DB;
use UtilService;

class PowerMapController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/power-map",
     *     tags={"计算电量映射关系power-map"},
     *     operationId="power-map-index",
     *     summary="分页获取数据列表",
     *     description="使用说明：分页获取数据列表",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *        )
     *     ),
     *     @OA\Parameter(
     *         description="每页数据量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=20,
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="页数",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1,
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="关键字中文名搜索",
     *         in="query",
     *         name="name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="组织ID",
     *         in="query",
     *         name="orgnization_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $orgnization_id = $request->input('orgnization_id');
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $name = $request->input('name');

        $rows = DB::table('power_map')
            ->join('dcs_standard', 'power_map.dcs_standard_id', '=', 'dcs_standard.id')
            ->select('power_map.*', 'dcs_standard.cn_name')
            ->where('power_map.orgnization_id', $orgnization_id)
            ->whereNull('power_map.deleted_at');

        if ($name) {
            $rows = $rows->where('dcs_standard.cn_name', 'like', "%{$name}%");
        }

        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $org = Orgnization::find($item->orgnization_id);
            $dcs_standard = DcsStandard::find($item->dcs_standard_id);
            $id_arr = explode(',', $item->electricity_map_ids);
            $maps = (new ElectricityMap())->whereIn('id', $id_arr)->get();
            $rows[$key]->orgnization = $org;
            $rows[$key]->dcs_standard = $dcs_standard;
            $rows[$key]->electricity_map = $maps;
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $rows, 'total' => $total]);
    }

    /**
     * @OA\Post(
     *     path="/api/power-map",
     *     tags={"计算电量映射关系power-map"},
     *     operationId="power-map-store",
     *     summary="新增单条数据",
     *     description="使用说明：新增单条数据",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="electricity_map id列表",
     *         in="query",
     *         name="electricity_map_ids",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="标准名称表主键",
     *         in="query",
     *         name="dcs_standard_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="函数",
     *         in="query",
     *         name="func",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="组织ID",
     *         in="query",
     *         name="orgnization_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $input = $request->only(['electricity_map_ids', 'dcs_standard_id', 'func', 'orgnization_id']);
        try {
            $res = PowerMap::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
    }

    /**
     * @OA\Get(
     *     path="/api/power-map/{id}",
     *     tags={"计算电量映射关系power-map"},
     *     operationId="power-map-show",
     *     summary="获取详细信息",
     *     description="使用说明：获取详细信息",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="PowerMap主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function show($id)
    {
        $row = PowerMap::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, '该数据不存在', '');
        }

        $org = Orgnization::find($row->orgnization_id);
        $id_arr = explode(',', $row->electricity_map_ids);
        $dcs_standard = DcsStandard::find($row->dcs_standard_id);
        $maps = (new ElectricityMap())->whereIn('id', $id_arr)->get();
        $row['orgnization'] = $org;
        $row['dcs_standard'] = $dcs_standard;
        $row['electricity_map'] = $maps;
        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $row);
    }

    /**
     * @OA\Put(
     *     path="/api/power-map/{id}",
     *     tags={"计算电量映射关系power-map"},
     *     operationId="power-map-update",
     *     summary="修改",
     *     description="使用说明：修改单条数据",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="PowerMap主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="electricity_map id列表",
     *         in="query",
     *         name="electricity_map_ids",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="标准名称表主键",
     *         in="query",
     *         name="dcs_standard_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="函数",
     *         in="query",
     *         name="func",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="组织ID",
     *         in="query",
     *         name="orgnization_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="update succeed",
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        $row = PowerMap::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '该数据不存在', ''));
        }
        $input = $request->input();
        $allowField = ['electricity_map_ids', 'dcs_standard_id', 'func', 'orgnization_id'];
        foreach ($allowField as $field) {
            if (key_exists($field, $input)) {
                $inputValue = $input[$field];
                $row[$field] = $inputValue;
            }
        }
        try {
            $row->save();
            $row->refresh();
        } catch (Exception $ex) {
            return UtilService::format_data(self::AJAX_FAIL, '修改失败', $ex->getMessage());
        }

        $org = Orgnization::find($row->orgnization_id);
        $id_arr = explode(',', $row->electricity_map_ids);
        $dcs_standard = DcsStandard::find($row->dcs_standard_id);
        $maps = (new ElectricityMap())->whereIn('id', $id_arr)->get();
        $row['orgnization'] = $org;
        $row['dcs_standard'] = $dcs_standard;
        $row['electricity_map'] = $maps;
        return UtilService::format_data(self::AJAX_SUCCESS, '修改成功', $row);
    }

    /**
     * @OA\Delete(
     *     path="/api/power-map/{id}",
     *     tags={"计算电量映射关系power-map"},
     *     operationId="power-map-destroy",
     *     summary="删除单条数据",
     *     description="使用说明：删除单条数据",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="PowerMap主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="detroy succeed",
     *     ),
     * )
     */
    public function destroy($id)
    {
        $row = PowerMap::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, '该数据不存在', '');
        }
        try {
            $row->delete();
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, '删除失败', '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '删除成功', '');
    }
}


/**
 * @OA\Definition(
 *     definition="PowerMaps",
 *     type="array",
 *     @OA\Items(ref="#/definitions/PowerMap")
 * )
 */
