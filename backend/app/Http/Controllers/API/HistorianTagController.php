<?php
/**
* historian tag控制器
*
* @author      cat 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SIS\HistorianTag;
use HistorianService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Swagger\Annotations as SWG;
use UtilService;
use App\Models\User;
use App\Models\SIS\Orgnization;
use App\Models\SIS\ConfigHistorianDB;
use Log;

class HistorianTagController extends Controller
{
    public $HistorianTag;

    public function __construct()
    {

    }

    /**
     * @OA\Get(
     *     path="/api/historian-tag/index",
     *     tags={"历史数据库标签historian tag"},
     *     operationId="historian-tag-index",
     *     summary="获取 tag 列表",
     *     description="使用说明：获取 tag 列表",
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
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="query",
     *         name="factory",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="alias 搜索",
     *         in="query",
     *         name="searchAlias",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="tag_name 搜索",
     *         in="query",
     *         name="searchTagName",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="historianTags",
     *                  description="historianTags",
     *                  allOf={
     *                     @OA\Schema(ref="#/definitions/HistorianTags")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $searchName = $request->input('searchAlias');
        $searchTagName = $request->input('searchTagName');
        $factory = $request->input('factory');
        if(!$this->validate_factory($factory)){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }
        $tb = 'historian_tag_' . $factory;
        $this->HistorianTag = (new HistorianTag())->setTable($tb);

        $params = [];
        if ($searchName) {
            $params['alias'] = $searchName;
        }
        if ($searchTagName) {
            $params['tag_name'] = $searchTagName;
        }
        $params['offset'] = ($page - 1) * $perPage;
        $params['limit'] = $perPage;
        $data = $this->HistorianTag->findByPage($params);

        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data));
    }

    /**
     * @OA\Get(
     *     path="/api/historian-tag/all",
     *     tags={"历史数据库标签historian tag"},
     *     operationId="historian-tag-all",
     *     summary="获取 tag 所有列表",
     *     description="使用说明：获取 tag 所有列表",
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
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="query",
     *         name="factory",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="tag_name 搜索",
     *         in="query",
     *         name="tag_name",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="historianTags",
     *                  description="historianTags",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/HistorianTags")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function all(Request $request)
    {
        $tag_name = $request->input('tag_name');
        $factory = $request->input('factory');
        $table = 'historian_tag_' . $factory;
        $obj_historian_tag = (new HistorianTag())->setTable($table);
        if($tag_name){
            $data = $obj_historian_tag->select(['id', 'tag_name'])->where('tag_name', 'like', "%{$tag_name}%")->get();
        }
        else{
            $data = $obj_historian_tag->select(['id', 'tag_name'])->get();
        }
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data);
    }

    /**
     * @OA\Get(
     *     path="/api/historian-tag/listdata",
     *     tags={"历史数据库标签historian tag"},
     *     operationId="historian-tag-listdata",
     *     summary="获取 tag 列表（包含当前值）",
     *     description="使用说明：获取 tag 列表（包含当前值）",
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
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="query",
     *         name="factory",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
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
     *              type="integer",
     *              default=1,
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="alias 搜索",
     *         in="query",
     *         name="searchAlias",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="tag_name 搜索",
     *         in="query",
     *         name="searchTagName",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="historianTags",
     *                  description="historianTags",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/HistorianTags")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function listWithData(Request $request)
    {
        $perPage = $request->input('num');
        $perPage = $perPage ? $perPage : 20;
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $searchName = $request->input('searchAlias');
        $searchTagName = $request->input('searchTagName');
        $factory = $request->input('factory');
        if(!$this->validate_factory($factory)){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }
        $tb = 'historian_tag_' . $factory;
        $this->HistorianTag = (new HistorianTag())->setTable($tb);

        $params = [];
        if ($searchName) {
            $params['alias'] = $searchName;
        }
        if ($searchTagName) {
            $params['tag_name'] = $searchTagName;
        }
        $params['offset'] = ($page - 1) * $perPage;
        $params['limit'] = $perPage;
        $data = $this->HistorianTag->findByPage($params);
        if(count($data['data']) > 0){
            $tagnames = '';
            foreach ($data['data'] as $key=>$item){
                if($tagnames){
                    $tagnames .= ';' . $item->tag_name;
                }
                else{
                    $tagnames = $item->tag_name;
                }
            }
            $org = Orgnization::where('code', $factory)->first()->toArray();
            $cfg = ConfigHistorianDB::where('orgnization_id', $org['id'])->first()->toArray();
            $cd = HistorianService::currentData($cfg, $tagnames);
            $curr_data_list = array();
            if ($cd && $cd['code'] === self::AJAX_SUCCESS && $cd['data']['ErrorCode'] === 0) {
                $curr_data_list = $cd['data']['Data'];
            }

            foreach ($data['data'] as $key=>$item){
                $data['data'][$key]['Value'] = '';
                $data['data'][$key]['TimeStamp'] = '';
                $data['data'][$key]['Quality'] = '';
                foreach($curr_data_list as $k2=>$val){
                    if($val['ErrorCode'] == 0 && $val['TagName'] == $item->tag_name && $val['Samples'] && count($val['Samples']) > 0){
                        $data['data'][$key]['Value'] = $val['Samples'][0]['Value'];
                        $data['data'][$key]['TimeStamp'] = $val['Samples'][0]['TimeStamp'];
                        $data['data'][$key]['Quality'] = $val['Samples'][0]['Quality'];
                        break;
                    }
                }
            }
        }

        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data));
    }

    /**
     * @OA\Get(
     *     path="/api/historian-tag/show/{id}",
     *     tags={"历史数据库标签historian tag"},
     *     operationId="historian-tag-show",
     *     summary="获取 tag 详细信息",
     *     description="使用说明：获取 tag 详细信息",
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
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="query",
     *         name="factory",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="tag 主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="historianTag",
     *                  description="historianTag",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/HistorianTag")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function show(Request $request, $id)
    {
        $factory = $request->input('factory');
        if(!$this->validate_factory($factory)){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }
        $tb = 'historian_tag_' . $factory;
        $this->HistorianTag = (new HistorianTag())->setTable($tb);

        $tag = $this->HistorianTag->findByID($id);
        if (!$tag) {
            return response()->json(UtilService::format_data(self::AJAX_NO_DATA, '该Tag不存在', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $tag));
    }

    /**
     * @OA\Post(
     *     path="/api/historian-tag/show-many",
     *     tags={"历史数据库标签historian tag"},
     *     operationId="historian-tag-many",
     *     summary="批量获取 historan tag",
     *     description="使用说明：批量获取 historan tag",
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
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="query",
     *         name="factory",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="config ids string，使用','分隔，example: ids=1,2,3",
     *         in="query",
     *         name="ids",
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
     *                  property="HistorianTags",
     *                  description="HistorianTags",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/HistorianTags")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function showMany(Request $request)
    {
        $factory = $request->input('factory');
        if(!$this->validate_factory($factory)){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }
        $tb = 'historian_tag_' . $factory;
        $this->HistorianTag = (new HistorianTag())->setTable($tb);

        $idsStr = $request->input('ids');
        if (!$idsStr) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '未提供ids', ''));
        }
        $ids = explode(',', $idsStr);
        $tags = $this->HistorianTag->whereIn('id', $ids)->get();
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $tags));
    }

    /**
     * @OA\Get(
     *     path="/api/historian-tag/load",
     *     tags={"历史数据库标签historian tag"},
     *     operationId="historian-tag-load",
     *     summary="从 Historian 加载 tags",
     *     description="使用说明：从 Historian 加载 tags, 保存到数据库",
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
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="query",
     *         name="factory",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="succeed",
     *     ),
     * )
     */
    public function load(Request $request)
    {
        $factory = $request->input('factory');
        if(!$this->validate_factory($factory)){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }
        $tb = 'historian_tag_' . $factory;
        $this->HistorianTag = (new HistorianTag())->setTable($tb);

        $org = Orgnization::where('code', $factory)->first()->toArray();
        $cfg = ConfigHistorianDB::where('orgnization_id', $org['id'])->first()->toArray();
        $tagslist = HistorianService::tags($cfg);
        if ($tagslist['code'] === self::AJAX_SUCCESS) {
            $tagNames = $tagslist['data']['Tags'];

            $updateOrCreateCount = 0;
            $errorCount = 0;
            $propertyNames = [
                'tag_id' => 'Id',
                'tag_name' => 'Name',
                'description' => 'Description',
                'origin_upper_limit' => 'HiEngineeringUnits',
                'origin_lower_limit' => 'LoEngineeringUnits',
            ];
            $properties = [];
            array_walk($propertyNames, function ($value, $key) use (&$properties) {
                $properties[$value] = 1;
            });

            foreach ($tagNames as $tagName) {
                $row = $this->HistorianTag->where('tag_name', $tagName)->first();
                if(!$row) {
                    $org = Orgnization::where('code', $factory)->first()->toArray();
                    $cfg = ConfigHistorianDB::where('orgnization_id', $org['id'])->first()->toArray();
                    $datas = HistorianService::properties($cfg, $tagName, $properties)['data'];
                    $dataDict = [];
                    array_walk($propertyNames, function ($value, $key) use (&$dataDict, $datas) {
                        if ($key == 'tag_id') {
                            $dataDict[$key] = strtolower($datas[$value]);
                        } else {
                            $dataDict[$key] = key_exists($value, $datas) ? $datas[$value] : null;
                        }
                    });
                    try {
                        $this->HistorianTag->updateOrCreate(['tag_id' => $dataDict['tag_id']], $dataDict);
                        $updateOrCreateCount += 1;
                    } catch (QueryException $e) {
                        $errorCount += 1;
                    }
                }
            }
            return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', ['updateOrCreateCount' => $updateOrCreateCount, 'errorCount' => $errorCount]));
        } else {
            return $tagslist;
        }
    }

    /**
     * @OA\Post(
     *     path="/api/historian-tag/update/{id}",
     *     tags={"历史数据库标签historian tag"},
     *     operationId="historian-tag-update",
     *     summary="修改 tag",
     *     description="使用说明：修改 tag",
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
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="query",
     *         name="factory",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="tag 主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="alias",
     *         in="query",
     *         name="alias",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="description",
     *         in="query",
     *         name="description",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="单位",
     *         in="query",
     *         name="measure",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="上限值",
     *         in="query",
     *         name="upper_limit",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="下限值",
     *         in="query",
     *         name="lower_limit",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="update succeed",
     *         @OA\Schema(
     *              @OA\Property(
     *                  property="historianTag",
     *                  description="historianTag",
     *                  allOf={
     *                      @OA\Schema(ref="#/definitions/HistorianTag")
     *                  }
     *             )
     *         )
     *     ),
     * )
     */
    public function update(Request $request, $id)
    {
        $factory = $request->input('factory');
        if(!$this->validate_factory($factory)){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }
        $tb = 'historian_tag_' . $factory;
        $this->HistorianTag = (new HistorianTag())->setTable($tb);

        $tag = $this->HistorianTag->findByID($id);
        if (!$tag) {
            return response()->json(UtilService::format_data(self::AJAX_NO_DATA, '该Tag不存在', ''));
        }
        $input = $request->input();
        $allowField = ['alias', 'description', 'measure', 'upper_limit', 'lower_limit'];
        foreach ($allowField as $field) {
            if (key_exists($field, $input)) {
                $inputValue = $input[$field];
                $tag[$field] = $inputValue;
            }
        }

        try {
            $tag->save();
            $tag->refresh();

            //删除缓存
            $this->HistorianTag->updateCache(['id'=>$id]);
        } catch (QueryException $ex) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '修改失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '修改成功', $tag));
    }

    /**
     * @OA\Delete(
     *     path="/api/historian-tag/destroy/{id}",
     *     tags={"历史数据库标签historian tag"},
     *     operationId="historian-tag-destroy",
     *     summary="删除 tag",
     *     description="使用说明：删除 tag",
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
     *         description="电厂英文名称  如永强二期：yongqiang2",
     *         in="query",
     *         name="factory",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="tag 主键",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="detroy succeed",
     *     ),
     * )
     */
    public function destroy(Request $request, $id)
    {
        $factory = $request->input('factory');
        if(!$this->validate_factory($factory)){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }
        $tb = 'historian_tag_' . $factory;
        $this->HistorianTag = (new HistorianTag())->setTable($tb);

        $tag = $this->HistorianTag->findByID($id);
        if (!$tag) {
            return response()->json(UtilService::format_data(self::AJAX_NO_DATA, '该Tag不存在', ''));
        }
        try {
            $tag->delete();
            //删除缓存
            $this->HistorianTag->updateCache(['id'=>$id]);
        } catch (QueryException $e) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '删除失败', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '删除成功', ''));
    }

    private function validate_factory($factory){
        $tb_list = [];
        $datalist = Orgnization::where('level', 3)->get();
        foreach ($datalist as $key => $item) {
            $tb_list[] = $item->code;
        }
        if(!$factory || ($factory && !in_array($factory, $tb_list))){
            return false;
        }
        else{
            return true;
        }
    }

}

/**
 * @OA\Definition(
 *     definition="HistorianTags",
 *     type="array",
 *     @OA\Items(ref="#/definitions/HistorianTag")
 * )
 */
