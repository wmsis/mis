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
use UtilService;
use App\Models\User;
use App\Models\SIS\Orgnization;
use App\Models\SIS\ConfigHistorianDB;
use App\Models\Factory\DcsData;
use MongoDB\BSON\UTCDateTime;
use Log;

class HistorianTagController extends Controller
{
    public $HistorianTag;

    public function __construct()
    {
        parent::__construct();
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

        $searchTagName = $request->input('searchTagName');
        $factory = $request->input('factory');
        if(!$this->validate_factory($factory)){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }
        $tb = 'historian_tag_' . $factory;
        $this->HistorianTag = (new HistorianTag())->setTable($tb);

        $params = [];
        if ($searchTagName) {
            $params['tag_name'] = $searchTagName;
        }
        $params['offset'] = ($page - 1) * $perPage;
        $params['limit'] = $perPage;
        $data = $this->HistorianTag->findByPage($params);

        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data));
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
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data);
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
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $tag));
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
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $tags));
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
                'tag_name' => 'Name'
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
            return response()->json(UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['updateOrCreateCount' => $updateOrCreateCount, 'errorCount' => $errorCount]));
        } else {
            return $tagslist;
        }
    }

    /**
     * @OA\Get(
     *     path="/api/historian-tag/load-mongo",
     *     tags={"历史数据库标签historian tag"},
     *     operationId="historian-tag-load-mongo",
     *     summary="从 MongoDB 加载 tags",
     *     description="使用说明：从 MongoDB 加载 tags, 保存到数据库",
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
    public function loadMongo(Request $request)
    {
        $factory = $request->input('factory');
        if(!$this->validate_factory($factory)){
            return UtilService::format_data(self::AJAX_FAIL, 'factory参数错误', '');
        }

        set_time_limit(0);
        $org = Orgnization::where('code', $factory)->first()->toArray();
        $cfg = ConfigHistorianDB::where('orgnization_id', $org['id'])->first()->toArray(); //电厂的数据库配置信息
        $begin = date('Y-m-d H:i:s', time()-10*60);
        $end = date('Y-m-d H:i:s', time());
        $start = new UTCDateTime(strtotime($begin)*1000);
        $stop = new UTCDateTime(strtotime($end)*1000);
        $factory_dcs_db_conn = 'historian_' . $this->tenement['id'] . '_' . $cfg['id']; //电厂MongoDB数据连接
        $tb = 'historian_tag_' . $factory;
        $historianTag = (new HistorianTag())->setTable($tb);  //保存tag的MongoDB集合
        $dcsData = (new DcsData())->setConnection($factory_dcs_db_conn); //电厂本地MongoDB数据集合
        $dcsData->select(['tag_name'])
                ->whereBetween('datetime', array($start, $stop))
                ->groupBy('tag_name')
                ->chunk(100, function ($tagslist) use ($historianTag) {
                    foreach ($tagslist as $key => $tag) {
                        $row = $historianTag->where('tag_name', $tag->tag_name)->first();
                        if(!$row) {
                            $historianTag->create([
                                'tag_name'=>$tag->tag_name
                            ]);
                        }
                    }
                });

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, '');
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
            return response()->json(UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, ''));
        }
        return response()->json(UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ''));
    }

    private function validate_factory($factory){
        $tb_list = [];
        $datalist = Orgnization::where('level', 2)->get();
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
