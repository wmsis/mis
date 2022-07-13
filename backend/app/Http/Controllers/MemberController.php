<?php
/**
* 前台微信用户控制器
*
* 前台微信用户相关接口
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Member\Member;
use App\Http\Models\Member\TakeMoney;
use App\Http\Models\Wechat\MemberPicTxt;
use App\Http\Models\SIS\CheckNotification;
use App\User;
use App\Http\Requests\Member\TakemoneyCheckRequest;
use App\Http\Requests\Member\PageRequest;
use Illuminate\Support\Facades\Auth;
use UtilService;
use WxpayService;

class MemberController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/api/member/lists",
     *     tags={"member api"},
     *     operationId="memberLists",
     *     summary="用户列表",
     *     description="使用说明：获取用户列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="当前分页",
     *         in="query",
     *         name="page",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="每页获取数量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="按名称搜索关键字",
     *         in="query",
     *         name="search",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized action."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="not found."
     *     )
     * )
     */
    public function lists(Request $request){
        $page = $request->input('page');
        $num = $request->input('num');
        $num = $num ? $num : 10;
        $search = $request->input('search');
        $offset = ($page - 1) * $num;
        $like = '%' . $search . '%';

        $total = Member::select(['id']);
        $users = Member::select(['*']);

        if($search){
            $total = $total->where('username', 'like', $like);
            $users = $users->where('username', 'like', $like);
        }

        $total = $total->orderBy('id', 'desc')
            ->count();

        $users = $users->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($num)
            ->get();

        if ($users) {
            foreach ($users as $key=>$item){
                $wechats = $item->wechats;
                $users[$key]['wechats'] = $wechats;
                $users[$key]['money'] = rtrim(rtrim($item->money, '0'), '.');
                $users[$key]['consume_money'] = rtrim(rtrim($item->consume_money, '0'), '.');
                $users[$key]['today_income'] = rtrim(rtrim($item->today_income, '0'), '.');
                $users[$key]['total_income'] = rtrim(rtrim($item->total_income, '0'), '.');
                $users[$key]['vip_save_money'] = rtrim(rtrim($item->vip_save_money, '0'), '.');

                $memberPicTxt = MemberPicTxt::where('member_id', $item->id)->orderBy('created_at', 'DESC')->first();
                $users[$key]['last_interaction_time'] = $memberPicTxt ? $memberPicTxt->created_at : $item->created_at;

                $checks = DB::table('check_notification')
                    ->join('boiler_check', 'boiler_check.id', '=', 'check_notification.boiler_check_id')
                    ->join('historian_module', 'historian_module.id', '=', 'boiler_check.module_id')
                    ->select('check_notification.*', 'boiler_check.tag_cn_name', 'historian_module.name')
                    ->where('check_notification.member_id', $item->id)
                    ->whereNull('check_notification.deleted_at')
                    ->orderBy('boiler_check.module_id', 'ASC')
                    ->get();
                $users[$key]['checks'] = $checks;
            }
            $res = array(
                'data' => $users,
                'total' => $total
            );
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $res);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', '');
        }
    }

    /**
     * @SWG\Get(
     *     path="/api/member/{id}/info",
     *     tags={"member api"},
     *     operationId="memberInfo",
     *     summary="用户详情",
     *     description="使用说明：获取用户详情",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="用户id",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="unauthorized action."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="not found."
     *     )
     * )
     */
    public function info(Request $request, Member $member){
        if($member){
            $member->money = rtrim(rtrim($member->money, '0'), '.');
            $member->consume_money = rtrim(rtrim($member->consume_money, '0'), '.');
            $member->today_income = rtrim(rtrim($member->today_income, '0'), '.');
            $member->total_income = rtrim(rtrim($member->total_income, '0'), '.');
            $member->vip_save_money = rtrim(rtrim($member->vip_save_money, '0'), '.');

            $member->wechats;
            $member->minis;
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $member);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', '');
        }
    }

    /**
     * @SWG\Post(
     *     path="/api/member/takemoney/check",
     *     tags={"member api"},
     *     operationId="takemoneyCheck",
     *     summary="用户提现审核",
     *     description="使用说明：用户提现审核",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="id",
     *         in="query",
     *         name="id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="管理员id",
     *         in="query",
     *         name="admin_id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="审核后的状态（agree/disagree）",
     *         in="query",
     *         name="status",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function takemoneyCheck(TakemoneyCheckRequest $request){
        $userObj = auth()->user();
        $id = $request->input('id');
        $status = $request->input('status');
        $obj = TakeMoney::find($id);
        if($obj && !$obj->status) {
            $admin_id = $userObj->id;
            $obj->admin_id = $admin_id;
            if ($status == 'agree') {
                //审核通过
                $obj->status = 'freezen';
                $s = $obj->save();
                if($s) {
                    if ($obj->pay_type == 'mini') {
                        $appid = config('mini.appid');
                    }
                    else {
                        $appid = config('wechat.appid');
                    }
                    $true_name = '';
                    $desc = '提现';
                    $res = WxpayService::transfers($appid, $obj->openid, $true_name, $obj->money, $desc);
                    if ($res) {
                        $obj->status = $status;
                        $obj->save();
                        return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', '');
                    }
                    else {
                        return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
                    }
                }
                else{
                    return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
                }
            }
            else {
                //审核不通过
                DB::beginTransaction();
                try {
                    $obj->status = $status;
                    $obj->save();

                    //退回余额
                    $m = Member::where('id', $obj->member_id)->first();
                    if($m) {
                        $money = $m->money + $obj->money;
                        Member::where('id', $obj->member_id)
                        ->update([
                            "money" => $money
                        ]);
                    }

                    DB::commit();
                    return UtilService::format_data(self::AJAX_SUCCESS, '操作成功', '');
                }
                catch (QueryException $ex) {
                    DB::rollback();
                    return UtilService::format_data(self::AJAX_FAIL, '操作失败', '');
                }
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '数据错误', '');
        }
    }

    /**
     * @SWG\Get(
     *     path="/api/member/takemoney/lists",
     *     tags={"member api"},
     *     operationId="takemoneyLists",
     *     summary="申请提现列表",
     *     description="使用说明：申请提现列表",
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="当前分页",
     *         in="query",
     *         name="page",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="每页获取数量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="搜索关键词",
     *         in="query",
     *         name="search",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="状态",
     *         in="query",
     *         name="status",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function takemoneyLists(PageRequest $request){
        $page = $request->input('page');
        $num = $request->input('num');
        $num = $num ? $num : 10;
        $search = $request->input('search');
        $status = $request->input('status');
        $offset = ($page - 1) * $num;
        $like = '%' . $search . '%';

        $total = DB::table('takemoney')
            ->join('member', 'member.id', '=', 'takemoney.member_id')
            ->select('takemoney.id');
        $lists = DB::table('takemoney')
            ->join('member', 'member.id', '=', 'takemoney.member_id')
            ->select('takemoney.*', 'member.username');

        if($search){
            $total = $total->where('member.name', 'like', $like);
            $lists= $lists->where('member.name', 'like', $like);
        }

        if($status != 'all'){
            $total = $total->where('takemoney.status', $status);
            $lists= $lists->where('takemoney.status', $status);
        }

        $total = $total->count();
        $lists= $lists->orderBy('takemoney.id', 'asc')
            ->offset($offset)
            ->limit($num)
            ->get();

        if ($lists) {
            foreach ($lists as $key=>$item){
                $member = Member::find($item->member_id);
                $wechats = $member->wechats;
                $lists[$key]->member = $member;
            }
            $res = array(
                'data' => $lists,
                'total' => $total
            );
            return UtilService::format_data(self::AJAX_SUCCESS, '获取成功', $res);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, '获取失败', '');
        }
    }
}
