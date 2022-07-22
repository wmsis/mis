<?php
/**
* historian tag控制器
*
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Models\SIS\HistorianTag;
use HistorianService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Swagger\Annotations as SWG;
use UtilService;
use App\User;
use Log;

class HistorianTagController extends Controller
{
    public $HistorianTag;

    public function __construct()
    {
        $this->HistorianTag = new HistorianTag();
    }

    /**
     * @SWG\GET(
     *     path="/api/historian-tag/index",
     *     tags={"historian tag api"},
     *     operationId="",
     *     summary="获取 tag 列表",
     *     description="使用说明：获取 tag 列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="每页数据量",
     *     in="query",
     *     name="num",
     *     required=false,
     *     type="integer",
     *     default=20,
     * ),
     * @SWG\Parameter(
     *     description="页数",
     *     in="query",
     *     name="page",
     *     required=false,
     *     type="integer",
     *     default=1,
     * ),
     * @SWG\Parameter(
     *     description="alias 搜索",
     *     in="query",
     *     name="searchAlias",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="tag_name 搜索",
     *     in="query",
     *     name="searchTagName",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="historianTags",
     *              description="historianTags",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/HistorianTags")
     *              }
     * )
     * )
     * ),
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
     * @SWG\GET(
     *     path="/api/historian-tag/all",
     *     tags={"historian tag api"},
     *     operationId="",
     *     summary="获取 tag 所有列表",
     *     description="使用说明：获取 tag 所有列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Response(
     *     response=200,
     *     description="succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="historianTags",
     *              description="historianTags",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/HistorianTags")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function all(Request $request)
    {
        $data = HistorianTag::select(['id', 'tag_name', 'description', 'alias'])->get();
        return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $data);
    }

    /**
     * @SWG\GET(
     *     path="/api/historian-tag/listdata",
     *     tags={"historian tag api"},
     *     operationId="",
     *     summary="获取 tag 列表（包含当前值）",
     *     description="使用说明：获取 tag 列表（包含当前值）",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="每页数据量",
     *     in="query",
     *     name="num",
     *     required=false,
     *     type="integer",
     *     default=20,
     * ),
     * @SWG\Parameter(
     *     description="页数",
     *     in="query",
     *     name="page",
     *     required=false,
     *     type="integer",
     *     default=1,
     * ),
     * @SWG\Parameter(
     *     description="alias 搜索",
     *     in="query",
     *     name="searchAlias",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="tag_name 搜索",
     *     in="query",
     *     name="searchTagName",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="historianTags",
     *              description="historianTags",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/HistorianTags")
     *              }
     * )
     * )
     * ),
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
            $cd = HistorianService::currentData($tagnames);
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
     * @SWG\GET(
     *     path="/api/historian-tag/show/{id}",
     *     tags={"historian tag api"},
     *     operationId="",
     *     summary="获取 tag 详细信息",
     *     description="使用说明：获取 tag 详细信息",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="tag 主键",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="integer",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="historianTag",
     *              description="historianTag",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/HistorianTag")
     *              }
     *     )
     * )
     * ),
     * )
     */
    public function show($id)
    {
        $tag = $this->HistorianTag->findByID($id);
        if (!$tag) {
            return response()->json(UtilService::format_data(self::AJAX_NO_DATA, '该Tag不存在', ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $tag));
    }

    /**
     * @SWG\POST(
     *     path="/api/historian-tag/show-many",
     *     tags={"historian tag api"},
     *     operationId="",
     *     summary="批量获取 historan tag",
     *     description="使用说明：批量获取 historan tag",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="config ids string，使用','分隔，example: ids=1,2,3",
     *     in="formData",
     *     name="ids",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="HistorianTags",
     *              description="HistorianTags",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/HistorianTags")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function showMany(Request $request)
    {
        $idsStr = $request->input('ids');
        if (!$idsStr) {
            return response()->json(UtilService::format_data(self::AJAX_FAIL, '未提供ids', ''));
        }
        $ids = explode(',', $idsStr);
        $tags = HistorianTag::whereIn('id', $ids)->get();
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '操作成功', $tags));
    }

    /**
     * @SWG\GET(
     *     path="/api/historian-tag/load",
     *     tags={"historian tag api"},
     *     operationId="",
     *     summary="从 Historian 加载 tags",
     *     description="使用说明：从 Historian 加载 tags, 保存到数据库",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Response(
     *     response=200,
     *     description="succeed",
     * ),
     * )
     */
    public function load()
    {
        $tagslist = HistorianService::tags();
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
                $row = HistorianTag::where('tag_name', $tagName)->first();
                if(!$row) {
                    $datas = HistorianService::properties($tagName, $properties)['data'];
                    $dataDict = [];
                    array_walk($propertyNames, function ($value, $key) use (&$dataDict, $datas) {
                        if ($key == 'tag_id') {
                            $dataDict[$key] = strtolower($datas[$value]);
                        } else {
                            $dataDict[$key] = key_exists($value, $datas) ? $datas[$value] : null;
                        }
                    });
                    try {
                        HistorianTag::updateOrCreate(['tag_id' => $dataDict['tag_id']], $dataDict);
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
     * @SWG\POST(
     *     path="/api/historian-tag/update/{id}",
     *     tags={"historian tag api"},
     *     operationId="",
     *     summary="修改 tag",
     *     description="使用说明：修改 tag",
     *     consumes={"application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="tag 主键",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="alias",
     *     in="formData",
     *     name="alias",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="description",
     *     in="formData",
     *     name="description",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="单位",
     *     in="formData",
     *     name="measure",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="上限值",
     *     in="formData",
     *     name="upper_limit",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="下限值",
     *     in="formData",
     *     name="lower_limit",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="update succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="historianTag",
     *              description="historianTag",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/HistorianTag")
     *              }
     *     )
     * )
     * ),
     * )
     */
    public function update(Request $request, $id)
    {
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
     * @SWG\DELETE(
     *     path="/api/historian-tag/destroy/{id}",
     *     tags={"historian tag api"},
     *     operationId="",
     *     summary="删除 tag",
     *     description="使用说明：删除 tag",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="tag 主键",
     *     in="path",
     *     name="id",
     *     required=true,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="detroy succeed",
     * ),
     * )
     */
    public function destroy($id)
    {
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


    /**
     * @SWG\GET(
     *     path="/api/historian-tag/user",
     *     tags={"historian tag api"},
     *     operationId="",
     *     summary="获取 用户tag 列表",
     *     description="使用说明：获取 用户tag 列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     * @SWG\Parameter(
     *     description="alias 搜索",
     *     in="query",
     *     name="searchAlias",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="tag_name 搜索",
     *     in="query",
     *     name="searchTagName",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Parameter(
     *     description="uid",
     *     in="query",
     *     name="uid",
     *     required=false,
     *     type="string",
     * ),
     * @SWG\Response(
     *     response=200,
     *     description="succeed",
     *     @SWG\Schema(
     *          @SWG\Property(
     *              property="historianTags",
     *              description="historianTags",
     *              allOf={
     *                  @SWG\Schema(ref="#/definitions/HistorianTags")
     *              }
     * )
     * )
     * ),
     * )
     */
    public function userTags(Request $request)
    {
        $userObj = auth()->user();
        $searchName = $request->input('searchAlias');
        $searchTagName = $request->input('searchTagName');
        $uid = $request->input('uid');

        if ($uid == 'all') {
            $tags = HistorianTag::select(['id', 'alias', 'tag_name']);
        }
        elseif($uid && $uid != 'all'){
            $user = User::find($uid);
            if(!$user){
                return response()->json(UtilService::format_data(self::AJAX_FAIL, '用户不存在', ''));
            }
            $tags = $user->tags();
        }
        else {
            $tags = $userObj->tags();
        }


        if ($searchName) {
            $tags = $tags->where('alias', 'like', "%{$searchName}%");
        }
        if ($searchTagName) {
            $tags = $tags->where('tag_name', 'like', "%{$searchTagName}%");
        }
        $tags = $tags->get();

        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $tags));
    }
}

/**
 * @SWG\Definition(
 *     definition="HistorianTags",
 *     type="array",
 *     @SWG\Items(ref="#/definitions/HistorianTag")
 * )
 */
