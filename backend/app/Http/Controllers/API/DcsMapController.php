<?php
/**
* DCS映射关系控制器
*
* @author      cat 叶文华
* @version     1.0 版本号
*/

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\SIS\DcsMap;
use App\Models\SIS\Orgnization;
use App\Models\SIS\DcsStandard;
use App\Models\SIS\HistorianTag;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\API\DcsMapStoreRequest;
use UtilService;

class DcsMapController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dcs-map",
     *     tags={"DCS映射关系dcs-map"},
     *     operationId="dcs-map-index",
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
     *         name="cn_name",
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
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="DcsMaps",
     *                  description="DcsMaps",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/DcsMaps")
     *                  }
     *             )
     *         )
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

        $name = $request->input('cn_name');

        $rows = DB::table('dcs_map')
            ->leftJoin('dcs_standard', 'dcs_map.dcs_standard_id', '=', 'dcs_standard.id')
            ->select('dcs_map.*', 'dcs_standard.cn_name')
            ->where('dcs_map.orgnization_id', $orgnization_id)
            ->whereNull('dcs_map.deleted_at')
            ->whereNull('dcs_standard.deleted_at');

        if ($name) {
            $rows = $rows->where('dcs_standard.cn_name', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        foreach ($rows as $key => $item) {
            $org = Orgnization::find($item->orgnization_id);
            $dcs_standard = DcsStandard::find($item->dcs_standard_id);
            $tag_id_arr = explode(',', $item->tag_ids);
            $tb = 'historian_tag_' . $org['code'];
            $tags = (new HistorianTag())->setTable($tb)->whereIn('id', $tag_id_arr)->get()->toArray();
            $rows[$key]->orgnization = $org;
            $rows[$key]->dcs_standard = $dcs_standard;
            $rows[$key]->tags = $tags;
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total, 'page' => $page, 'num' => $perPage]);
    }

    /**
     * @OA\Post(
     *     path="/api/dcs-map",
     *     tags={"DCS映射关系dcs-map"},
     *     operationId="dcs-map-store",
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
     *         description="historian id列表",
     *         in="query",
     *         name="tag_ids",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="标准dcs名称ID",
     *         in="query",
     *         name="dcs_standard_id",
     *         required=false,
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
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="DcsMap",
     *                  description="DcsMap",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/DcsMap")
     *                  }
     *               )
     *          )
     *     ),
     * )
     */
    public function store(DcsMapStoreRequest $request)
    {
        $input = $request->only(['tag_ids', 'dcs_standard_id', 'func', 'orgnization_id']);
        try {
            $res = DcsMap::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
    }

    /**
     * @OA\Get(
     *     path="/api/dcs-map/{id}",
     *     tags={"DCS映射关系dcs-map"},
     *     operationId="dcs-map-show",
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
     *         description="DcsMap主键",
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
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="DcsMap",
     *                  description="DcsMap",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/DcsMap")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function show($id)
    {
        $row = DcsMap::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }

        $org = Orgnization::find($row->orgnization_id);
        $dcs_standard = DcsStandard::find($row->dcs_standard_id);
        $tag_id_arr = explode(',', $row->tag_ids);
        $tb = 'historian_tag_' . $org['code'];
        $tags = (new HistorianTag())->setTable($tb)->whereIn('id', $tag_id_arr)->get()->toArray();
        $row['orgnization'] = $org;
        $row['dcs_standard'] = $dcs_standard;
        $row['tags'] = $tags;
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Put(
     *     path="/api/dcs-map/{id}",
     *     tags={"DCS映射关系dcs-map"},
     *     operationId="dcs-map-update",
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
     *         description="DcsMap主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="historian id列表",
     *         in="query",
     *         name="tag_ids",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="标准dcs名称ID",
     *         in="query",
     *         name="dcs_standard_id",
     *         required=false,
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
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="DcsMap",
     *                  description="DcsMap",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/DcsMap")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        $row = DcsMap::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, ''));
        }
        $input = $request->input();
        $allowField = ['tag_ids', 'dcs_standard_id', 'func', 'orgnization_id'];
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
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $ex->getMessage());
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Delete(
     *     path="/api/dcs-map/{id}",
     *     tags={"DCS映射关系dcs-map"},
     *     operationId="dcs-map-destroy",
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
     *         description="DcsMap主键",
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
        $row = DcsMap::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        try {
            $row->delete();
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
    }
}


/**
 * @OA\Definition(
 *     definition="DcsMaps",
 *     type="array",
 *     @OA\Items(ref="#/definitions/DcsMap")
 * )
 */
