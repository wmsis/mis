<?php
/**
* IEC104取得的电表数据控制器
*
* @author      叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use UtilService;
use App\Http\Models\SIS\Electricity;
use App\Http\Requests\API\StoreElectricityRequest;
use Illuminate\Database\QueryException;
use Log;

class ElectricityController extends Controller
{
    /**
     * @SWG\GET(
     *     path="/api/electricity/index",
     *     tags={"electricity api"},
     *     operationId="",
     *     summary="获取电表数据列表",
     *     description="使用说明：获取电表数据列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="formData",
     *         name="factory",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="每页数据量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         type="integer",
     *         default=20,
     *     ),
     *      @SWG\Parameter(
     *          description="页数",
     *          in="query",
     *          name="page",
     *          required=false,
     *          type="integer",
     *          default=1,
     *      ),
     *      @SWG\Parameter(
     *          description="中文名称搜索",
     *          in="query",
     *          name="cn_name",
     *          required=false,
     *          type="string",
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="succeed",
     *          @SWG\Schema(
     *               @SWG\Property(
     *                   property="Electricities",
     *                   description="Electricities",
     *                   allOf={
     *                       @SWG\Schema(ref="#/definitions/Electricity")
     *                   }
     *                )
     *           )
     *      ),
     * )
     */
    public function index(Request $request)
    {
        $factory = $request['factory'];
        if(!$this->validate_factory($factory)){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }

        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $cn_name = $request->input('cn_name');
        $tb = 'electricity_' . $factory;
        $electricity = (new Electricity())->setTable($tb);

        $rows = $electricity->select(['*']);
        if ($cn_name) {
            $rows = $rows->where('cn_name', 'like', "%{$cn_name}%");
        }
        $total = $rows->count();
        $rows = $rows->offset(($page - 1) * $perPage)->limit($perPage)->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', ['data' => $rows, 'total' => $total]);
    }

    /**
     * @SWG\POST(
     *     path="/api/electricity/store_multi",
     *     tags={"electricity api"},
     *     operationId="",
     *     summary="批量新增",
     *     description="使用说明：批量新增",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="formData",
     *         name="factory",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="input",
     *         in="formData",
     *         name="input",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="store succeed",
     *     ),
     * )
     */
    public function store_multi(Request $request)
    {
        $factory = $request['factory'];
        if(!$this->validate_factory($request['factory'])){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }

        $params = $request->only(['input']);
        if (!isset($params['input']) || !$params['input']) {
            return UtilService::format_data(self::AJAX_FAIL, 'input参数错误', '');
        }
        else{
            $datalist = json_decode($params['input'], true);
            try {
                $tb = 'electricity_' . $factory;
                $electricity = (new Electricity())->setTable($tb);

                foreach ($datalist as $key => $value) {
                    $datalist[$key]['created_at'] = date('Y-m-d H:i:s');
                    $datalist[$key]['updated_at'] = date('Y-m-d H:i:s');
                    // $local_row = $electricity->findById($item['id']);
                    // if(!$local_row){
                    //     unset($datalist[$key]);
                    // }
                }
                $res = $electricity->insertMany($datalist);
            } catch (QueryException $e) {
                return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
            }
            return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $res);
        }
    }

    private function validate_factory($factory){
        $tb_list = config('factory');
        if(!$factory || ($factory && !in_array($factory, $tb_list))){
            return false;
        }
        else{
            return true;
        }
    }
}
