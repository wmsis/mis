<?php
/**
* 地磅分类关系控制器
*
* @author      cat 叶文华
* @version     1.0 版本号
*/

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SIS\WeighbridgeCateBig;
use App\Models\SIS\WeighbridgeCateSmall;
use App\Models\SIS\WeighBridgeFormat;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BaseExport;
use UtilService;
use Log;

class WeighbridgeCategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/weighbridge-category/lists-big",
     *     tags={"地磅垃圾分类weighbridge-category"},
     *     operationId="weighbridge-category-lists-big",
     *     summary="获取所有大类垃圾数据",
     *     description="使用说明：获取所有大类垃圾数据",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="WeighbridgeCateBigs",
     *                  description="WeighbridgeCateBigs",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/WeighbridgeCateBigs")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function listsBig(Request $request)
    {
        $data = WeighbridgeCateBig::all();
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data);
    }

    /**
     * @OA\Get(
     *     path="/api/weighbridge-category/page-big",
     *     tags={"地磅垃圾分类weighbridge-category"},
     *     operationId="weighbridge-category-page-big",
     *     summary="分页获取垃圾大类数据列表",
     *     description="使用说明：分页获取垃圾大类数据列表",
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
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="WeighbridgeCateBigs",
     *                  description="WeighbridgeCateBigs",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/WeighbridgeCateBigs")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function pageBig(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $name = $request->input('name');

        $rows = WeighbridgeCateBig::select(['*']);
        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total]);
    }

    /**
     * @OA\Post(
     *     path="/api/weighbridge-category/store-big",
     *     tags={"地磅垃圾分类weighbridge-category"},
     *     operationId="weighbridge-category-store-big",
     *     summary="新增单条大类数据",
     *     description="使用说明：新增单条大类数据",
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
     *         description="名字",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="描述",
     *         in="query",
     *         name="description",
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
     *                  property="WeighbridgeCateBig",
     *                  description="WeighbridgeCateBig",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/WeighbridgeCateBig")
     *                  }
     *               )
     *          )
     *     ),
     * )
     */
    public function storeBig(Request $request)
    {
        $input = $request->only(['name', 'description']);
        try {
            $res = WeighbridgeCateBig::create($input);
        } catch (QueryException $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
    }

    /**
     * @OA\Get(
     *     path="/api/weighbridge-category/show-big/{id}",
     *     tags={"地磅垃圾分类weighbridge-category"},
     *     operationId="weighbridge-category-show",
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
     *         description="WeighbridgeCateBig主键",
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
     *                  property="WeighbridgeCateBig",
     *                  description="WeighbridgeCateBig",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/WeighbridgeCateBig")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function showBig($id)
    {
        $row = WeighbridgeCateBig::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Post(
     *     path="/api/weighbridge-category/update-big/{id}",
     *     tags={"地磅垃圾分类weighbridge-category"},
     *     operationId="weighbridge-category-update",
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
     *         description="WeighbridgeCateBig主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="中文名字",
     *         in="query",
     *         name="name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="描述",
     *         in="query",
     *         name="description",
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
     *                  property="WeighbridgeCateBig",
     *                  description="WeighbridgeCateBig",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/WeighbridgeCateBig")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function updateBig(Request $request, $id)
    {
        $row = WeighbridgeCateBig::find($id);
        if (!$row) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, ''));
        }
        $input = $request->input();
        $allowField = ['name', 'description'];
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
     *     path="/api/weighbridge-category/destroy-big/{id}",
     *     tags={"地磅垃圾分类weighbridge-category"},
     *     operationId="weighbridge-category-destroy-big",
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
     *         description="WeighbridgeCateBig主键",
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
    public function destroyBig($id)
    {
        $row = WeighbridgeCateBig::find($id);
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

    /**
     * @OA\Post(
     *     path="/api/weighbridge-category/store-small-multi",
     *     tags={"地磅垃圾分类weighbridge-category"},
     *     operationId="weighbridge-category-small-multi",
     *     summary="批量新增小类",
     *     description="使用说明：批量新增小类",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="垃圾小类名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="store succeed",
     *     ),
     * )
     */
    public function storeSmallMulti(Request $request)
    {
        $params = $request->only(['input']);
        if (!isset($params['input']) || !$params['input']) {
            return UtilService::format_data(self::AJAX_FAIL, 'input参数错误', '');
        }
        else{
            $datalist = json_decode($params['input'], true);
            foreach ($datalist[0] as $key => $value) {
                if(!in_array($key, $fillable)){
                    return UtilService::format_data(self::AJAX_FAIL, '参数错误', $key);
                }
            }

            //查询数据是否存在，不存在则增加
            foreach ($datalist as $key => $item) {
                $datalist[$key]['created_at'] = date('Y-m-d H:i:s');
                $datalist[$key]['updated_at'] = date('Y-m-d H:i:s');
                $local_row = $WeighbridgeCateSmall::where('name', $item['name'])->first();
                if($local_row){
                    unset($datalist[$key]);
                }
            }

            DB::beginTransaction();
            try {
                $WeighbridgeCateSmallObj = new WeighbridgeCateSmall();
                $WeighbridgeCateSmallObj->insertMany($datalist);
                DB::commit();
            } catch (QueryException $e) {
                DB::rollback();
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, $e->getMessage());
            }
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/weighbridge-category/page-small",
     *     tags={"地磅垃圾分类weighbridge-category"},
     *     operationId="weighbridge-category-page-small",
     *     summary="分页获取垃圾小类数据列表",
     *     description="使用说明：分页获取垃圾小类数据列表",
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
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="WeighbridgeCateBigs",
     *                  description="WeighbridgeCateBigs",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/WeighbridgeCateBigs")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function pageSmall(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $name = $request->input('name');

        $rows = WeighbridgeCateSmall::select(['*']);
        if ($name) {
            $rows = $rows->where('name', 'like', "%{$name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['data' => $rows, 'total' => $total]);
    }

    /**
     * @OA\Post(
     *     path="/api/weighbridge-category/bind-relation",
     *     tags={"地磅垃圾分类weighbridge-category"},
     *     operationId="weighbridge-category-bind-relation",
     *     summary="绑定大小类关系",
     *     description="使用说明：绑定大小类关系",
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
     *         description="大类ID",
     *         in="query",
     *         name="cate_big_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         description="小类ID列表，英文逗号隔开",
     *         in="query",
     *         name="cate_small_ids",
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
    public function bindRelation(Request $request)
    {
        $input = $request->only(['cate_big_id', 'cate_small_ids']);
        DB::beginTransaction();
        try {
            $big = WeighbridgeCateBig::find($input['cate_big_id']);
            $final_samll_arr = explode(',', $input['cate_small_ids']); //新的最终小类ID列表
            $already_in_arr = [];  //已经存在关联关系的小类列表
            $old_small_names = $big->small_names;

            //解除旧的关联
            if($old_small_names){
                foreach ($old_small_names as $key => $small) {
                    if(!in_array($small->id, $final_samll_arr)){
                        //解除旧的关联
                        $small->weighbridge_cate_big()->dissociate($big->id);  //解除时用belongsTo方使用dissociate方法
                        $small->save();
                    }
                    else{
                        //已经存在关联关系的小类列表
                        $already_in_arr[] = $small->id;
                    }
                }
            }

            //绑定新的关联
            foreach ($final_samll_arr as $key => $cate_small_id) {
                if($cate_small_id && !in_array($cate_small_id, $already_in_arr)){
                    $small = WeighbridgeCateSmall::find($cate_small_id);
                    $big->small_names()->save($small);  //或在hasMany端使用attach()添加 $big->small_names()->attach($small->id);  $small->weighbridge_cate_big()->associate($big->id);
                    $big->save();
                }
            }
            DB::commit();
        } catch (QueryException $e) {
            DB::rollback();
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
    }

    /**
     * @OA\Get(
     *     path="/api/weighbridge-category/show-relation/{id}",
     *     tags={"地磅垃圾分类weighbridge-category"},
     *     operationId="weighbridge-category-show-relation",
     *     summary="获取详细大类及关联信息",
     *     description="使用说明：获取详细大类及关联信息",
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
     *         description="WeighbridgeCateBig主键",
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
    public function showRelation($id)
    {
        $row = WeighbridgeCateBig::find($id);
        if (!$row) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_NO_DATA_MSG, '');
        }
        $row->small_names;
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $row);
    }

    /**
     * @OA\Post(
     *     path="/api/weighbridge-category/import",
     *     tags={"地磅垃圾分类weighbridge-category"},
     *     operationId="weighbridge-category-import",
     *     summary="导入多条数据",
     *     description="使用说明：导入多条数据",
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
     *         description="JSON文件",
     *         in="query",
     *         name="json",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function import(Request $request)
    {
        $json = $request->input('json');
        $arr = json_decode($json, true);
        $header = $arr['header'];
        $results = $arr['results'];
        $obj = new WeighbridgeCateBig();

        try {
            $params = [];
            foreach ($results as $key => $item) {
                $row = WeighbridgeCateBig::where('name', $item['name'])->first();
                if($row && $row->id){
                    continue;
                }

                if(isset($item['index'])){
                    unset($item['index']);
                }
                $temp = $item;
                $temp['created_at'] = date('Y-m-d H:i:s');
                $temp['updated_at'] = date('Y-m-d H:i:s');
                $params[] = $temp;
            }

            if(!empty($params)){
                $obj->insertMany($params);
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
            }
            else{
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
            }
        } catch (Exception $e) {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/weighbridge-category/download",
     *     tags={"地磅垃圾分类weighbridge-category"},
     *     operationId="weighbridge-category-download",
     *     summary="导出多条数据",
     *     description="使用说明：导出多条数据",
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
     *         description="ID列表 多个英文逗号隔开  所有传all",
     *         in="query",
     *         name="ids",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function download(Request $request)
    {
        ini_set('memory_limit', -1);
        $ids = $request->input('ids');
        if($ids == 'all' || $ids == ''){
            $final_data = WeighbridgeCateBig::select(['name', 'description'])->get()->toArray();
        }
        else{
            $id_arr = explode(',', $ids);
            $final_data = WeighbridgeCateBig::select(['name', 'description'])->whereIn('id', $id_arr)->get()->toArray();
        }
        $headings = ['名称', '描述'];
        array_unshift($final_data, $headings);
        $excel = new BaseExport($final_data, $author='猫小鱼', $sheetname='垃圾分类');

        return Excel::download($excel, '统一字段名_' . date('YmdHis') . '.xlsx');
    }
}


/**
 * @OA\Definition(
 *     definition="WeighbridgeCateBigs",
 *     type="array",
 *     @OA\Items(ref="#/definitions/WeighbridgeCateBig")
 * )
 */
