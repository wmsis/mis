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
use App\Models\Member\Member;
use App\Models\Member\TakeMoney;
use App\Models\Wechat\MemberPicTxt;
use App\Models\SIS\CheckNotification;
use App\Models\User;
use App\Http\Requests\Member\TakemoneyCheckRequest;
use App\Http\Requests\Member\PageRequest;
use Illuminate\Support\Facades\Auth;
use UtilService;
use WxpayService;

class MemberController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/member/lists",
     *     tags={"微信会员member"},
     *     operationId="memberLists",
     *     summary="用户列表",
     *     description="使用说明：获取用户列表",
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
     *         description="当前分页",
     *         in="query",
     *         name="page",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="每页获取数量",
     *         in="query",
     *         name="num",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="称搜索关键字",
     *         in="query",
     *         name="search",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized action."
     *     ),
     *     @OA\Response(
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
     * @OA\Get(
     *     path="/api/member/{id}/info",
     *     tags={"微信会员member"},
     *     operationId="memberInfo",
     *     summary="用户详情",
     *     description="使用说明：获取用户详情",
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
     *         description="用户id",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="unauthorized action."
     *     ),
     *     @OA\Response(
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
}
