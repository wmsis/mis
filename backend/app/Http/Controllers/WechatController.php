<?php
/**
* 微信公众号控制器
*
* 微信公众号相关接口
* @author      alvin 叶文华
* @version     1.0 版本号
*/
namespace App\Http\Controllers;

use App\Http\Requests\Wechat\StoreMaterialRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Wechat\PicTxt;
use App\Models\Wechat\Material;
use App\Models\Wechat\MemberPicTxt;
use App\Models\Wechat\AutoReply;
use App\Models\Wechat\Menu;
use App\Models\Member\Member;
use App\Models\Member\Wechat;
use App\Requests\Wechat\StorePicTxtRequest;
use App\Http\Requests\Wechat\PicTxtPageRequest;
use App\Http\Requests\Wechat\StoreMemberPicTxtRequest;
use App\Http\Requests\Wechat\StoreAutoReplyRequest;
use App\Http\Requests\Wechat\KeywordsPageRequest;
use App\Http\Requests\Wechat\DeletePicTxtRequest;
use WechatService;
use UtilService;
use App\Jobs\SendCustomMsg;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Events\WechatScanLogin;
use Illuminate\Support\Facades\Auth;
use App\Models\SIS\OfflineData;
use App\Models\SIS\EconomyDaily;
use App\Models\SIS\DailyData;
use Log;

class WechatController extends Controller
{
    private $_USERDATA = array();

    /**
     * 微信接口认证与执行...
     */
    public function main(Request $request){
        //初始化时
        $str = $request->input('echostr');
        if(isset($str)){
            if($this->checkSignature($request)){
                echo $str;
            }
            else{
                echo '验证失败';
            }
            exit;
        }
        else{
            //非初始化，已经认证过时
            $this->run($request);
        }
    }

    /**
     * 微信主运行方法...
     */
     private function run($request) {
         $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : $request->getContent();
         if (!empty($postStr)) {
             $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
             $this->_USERDATA['fromusername'] = $postObj->FromUserName;
             $this->_USERDATA['tousername'] = $postObj->ToUserName;
             $this->_USERDATA['msgtype'] = $postObj->MsgType;
             $openid = (string)$postObj->FromUserName;
             $param = $this->objectToArray($postObj);
             switch ($this->_USERDATA['msgtype']) {
                 case 'event':
                     $this->_USERDATA['event'] = strtolower($postObj->Event);

                     if ($this->_USERDATA['event'] == 'subscribe') {
                         $eventKey = $postObj->EventKey;
                         if($eventKey && strpos($eventKey, 'qrscene_') !== FALSE){
                             //扫码带参数的二维码关注
                             $start = strpos($eventKey, 'qrscene_') + 8;
                             $scene = substr($eventKey, $start);
                             $this->_USERDATA['scene'] = $scene;
                         }
                         $this->subscribe();
                     }
                     elseif ($this->_USERDATA['event'] == 'unsubscribe') {
                         //取消关注事件
                         $this->unsubscribe();
                     }
                     elseif ($this->_USERDATA['event'] == 'scan') {
                         //用户已关注时的扫码事件推送
                         $scene = $postObj->EventKey;
                         $this->_USERDATA['scene'] = $scene;
                         $this->scan($param);
                     }
                     elseif ($this->_USERDATA['event'] == 'location') {
                         //上报地理位置事件，每次进入公众号会话时，微信公众号后台开启该功能
                         $this->_USERDATA['latitude'] = $postObj->Latitude;
                         $this->_USERDATA['longitude'] = $postObj->Longitude;
                         $key = md5($openid.'open');
                         if (!Cache::has($key)) {
                             $autoObj = new AutoReply();
                             $msg = $autoObj->findByCategory('open');
                             if($msg){
                                 $params = array(
                                     "key" => $key
                                 );
                                 Cache::add($key, $params, $msg->interval_time * 60);  //秒数
                             }
                             $this->AiReply($msg);
                         }

                         echo "success";
                         exit;
                     }
                     elseif ($this->_USERDATA['event'] == 'view') {
                         //点击菜单跳转链接时的事件推送
                     }
                     elseif ($this->_USERDATA['event'] == 'click') {
                         //点击菜单拉取消息时的事件推送
                         $this->_USERDATA['event_key'] = $postObj->EventKey;
                         $mObj = new Menu();
                         $clicks = $mObj->keywords();
                         if($clicks){
                             foreach ($clicks as $click){
                                 $k = substr(md5($click->id), 0, 5);
                                 if($k == $this->_USERDATA['event_key']){
                                     if(strpos($click->keyword, '今日生产') !== FALSE){
                                         $data = $this->dateData();
                                         $this->datetempmsg($openid, $data);
                                     }
                                     elseif(strpos($click->keyword, '本月生产') !== FALSE){
                                         $data = $this->monthData();
                                         $this->monthtempmsg($openid, $data);
                                     }
                                     else{
                                         $autoObj = new AutoReply();
                                         $msg = $autoObj->findByKeyword($click->keyword);
                                         $this->AiReply($msg);
                                     }
                                     echo "success";
                                 }
                             }
                         }
                     }
                     elseif ($this->_USERDATA['event'] == 'templatesendjobfinish') {
                         //模版消息发送任务完成
                         echo "success";
                         exit;
                     }
                     break;
                 case 'transfer_customer_service':
                     echo "success";
                     break;
                 case 'image':
                     echo "success";
                     break;
                 case 'link':
                     echo "success";
                     break;
                 case 'voice':
                     echo "success";
                     break;
                 case 'video':
                     echo "success";
                     break;
                 case 'text':
                     $keyword = $postObj->Content;
                     $autoObj = new AutoReply();
                     $keylist = $autoObj->keywords();
                     if($keylist && $keyword) {
                         if(strpos($keyword, '今日生产') !== FALSE){
                             $data = $this->dateData();
                             $this->datetempmsg($openid, $data);
                         }
                         elseif(strpos($keyword, '本月生产') !== FALSE){
                             $data = $this->monthData();
                             $this->monthtempmsg($openid, $data);
                         }
                         else{
                             $msg = null;
                             foreach($keylist as $word){
                                 if($word->keyword == $keyword){
                                     $msg = $word;
                                     break;
                                 }
                             }

                             $this->AiReply($msg);
                         }
                         echo "success";
                     }
                     else{
                         echo "success";
                     }
                     break;
                 case 'location':
                     //消息+号里面发送位置地图信息时，会触发该事件，即接收地理位置消息
                     $this->_USERDATA['Location_X'] = $postObj->Location_X;
                     $this->_USERDATA['Location_Y'] = $postObj->Location_Y;
                     $this->_USERDATA['Scale'] = $postObj->Scale;
                     $this->_USERDATA['Label'] = $postObj->Label;
                     echo "success";
                     break;
                 default:
                     echo "success";
                     break;
             }
         } else {
             echo "";
             exit;
         }
         exit;
     }

    /**
     * 微信接口验证...
     */
    private function checkSignature($request)
    {
        $signature = $request->input('signature');
        $timestamp = $request->input('timestamp');
        $nonce = $request->input('nonce');

        $token = config('wechat.token');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function objectToArray($e){
        $e=(array)$e;
        foreach($e as $k=>$v){
            if( gettype($v)=='resource' ) return;
            if( gettype($v)=='object' || gettype($v)=='array' )
                $e[$k]=(array)($this->objectToArray($v));
        }
        return $e;
    }

    /**
     * 微信返回文本信息...
     */
    private function returnTxt($string) {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>";
        $msgType = "text";
        $contentStr = $string;
        $resultStr = sprintf($textTpl, $this->_USERDATA['fromusername'], $this->_USERDATA['tousername'], time(), $msgType, $contentStr);
        echo $resultStr;
        exit;
    }

    /**
     * 微信图片信息...
     */
    private function returnImage($media_id) {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Image>
                    <MediaId ><![CDATA[%s]]></MediaId >
                    </Image>
                    <FuncFlag>0</FuncFlag>
                    </xml>";
        $msgType = "image";
        $resultStr = sprintf($textTpl, $this->_USERDATA['fromusername'], $this->_USERDATA['tousername'], time(), $msgType, $media_id);
        echo $resultStr;
        exit;
    }

    /**
     * 微信返回图文信息...
     */
    private function returnNews($array) {
        if (!empty($array) && $array!= null) {
            $str = '';
            $title = $array['title'];
            $desc = $array['desc'];
            $picurl = $array['picurl'];
            $url = $array['url'];
            $str .= "<item>
                     <Title><![CDATA[".$title."]]></Title>
                     <Description><![CDATA[".$desc."]]></Description>
                     <PicUrl><![CDATA[".$picurl."]]></PicUrl>
                     <Url><![CDATA[".$url."]]></Url>
                     </item>";
        } else {
            exit;
        }

        $textTpl = " <xml>
                    <ToUserName><![CDATA[" . $this->_USERDATA['fromusername'] . "]]></ToUserName>
                    <FromUserName><![CDATA[" . $this->_USERDATA['tousername'] . "]]></FromUserName>
                    <CreateTime>" . time() . "</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>

                    <ArticleCount>1</ArticleCount>

                    <Articles>

                            " . $str . "

                    </Articles>
                    </xml> ";
        echo $textTpl;
        exit;
    }

    /**
     * 微信返回多图文信息...
     */
    private function returnManyNews($array) {
        $count = 0;
        if (!empty($array) && $array!= null) {
            $str = '';
            foreach($array as $item){
                $title = $item['title'];
                $desc = $item['desc'];
                $picurl = $item['picurl'];
                $url = $item['url'];
                $str .= "<item>
	                     <Title><![CDATA[".$title."]]></Title>
	                     <Description><![CDATA[".$desc."]]></Description>
	                     <PicUrl><![CDATA[".$picurl."]]></PicUrl>
	                     <Url><![CDATA[".$url."]]></Url>
	                     </item>";
            }
            $count = count($array);
        } else {
            exit;
        }

        $textTpl = " <xml>
                    <ToUserName><![CDATA[" . $this->_USERDATA['fromusername'] . "]]></ToUserName>
                    <FromUserName><![CDATA[" . $this->_USERDATA['tousername'] . "]]></FromUserName>
                    <CreateTime>" . time() . "</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>

                    <ArticleCount>" . $count . "</ArticleCount>

                    <Articles>

                            " . $str . "

                    </Articles>
                    </xml> ";
        echo $textTpl;
        exit;
    }

    private function scan($param){
        $scene = $this->_USERDATA['scene'];
        if($scene) {
            //扫码关注广播
            $wxObj = new Wechat();
            $wechatObj = $wxObj->findByOpenid($this->_USERDATA['fromusername']);
            $mObj = $wechatObj->member()->first();
            $mObj->channel = $scene;
            $mObj->save();
            $mObj->wechats;
            event(new WechatScanLogin($mObj));
        }
    }

    private function subscribe(){
        $this->updateUserBySubscribe();
        $obj = new AutoReply();
        $msg = $obj->findByCategory('subscribe');
        $this->AiReply($msg);
    }

    private function unsubscribe(){
        $openid = (string)$this->_USERDATA['fromusername'];
        $wxObj = new Wechat();
        $wx_info = $wxObj->findByOpenid($openid);
        if($wx_info){
            $wx_info->subscribe = false;
            $wx_info->save();
        }
    }

    private function AiReply($msg){
        if($msg){
            if($msg->type == 'text' && $msg->text){
                if(strpos($msg->text, '[$name]') !== false){
                    $wxObj = new Wechat();
                    $wechatObj = $wxObj->findByOpenid($this->_USERDATA['fromusername']);
                    if($wechatObj){
                        //替换[$name]变量
                        $msg->text = str_replace('[$name]', $wechatObj->nickname, $msg->text);
                    }
                }
                if(strpos($msg->text, '[$time]') !== false){
                    //替换[$time]变量
                    $msg->text = str_replace('[$time]', date('Y-m-d H:i:s'), $msg->text);
                }

                if(isset($this->_USERDATA['latitude']) && $this->_USERDATA['latitude'] && isset($this->_USERDATA['longitude']) &&  $this->_USERDATA['longitude'] && (strpos($msg->text, '[$province]') !== false  || strpos($msg->text, '[$city]') !== false || strpos($msg->text, '[$district]') !== false)){
                    $location_url = "https://apis.map.qq.com/ws/geocoder/v1/?location=".$this->_USERDATA['latitude'].",".$this->_USERDATA['longitude']."&key=".config('domain.location_key');
                    $result = UtilService::curl_get($location_url);
                    if($result && $result['status'] == 0 && $result['result'] && $result['result']['address_component']){
                        $msg->text = str_replace('[$province]', $result['result']['address_component']['province'], $msg->text);
                        $msg->text = str_replace('[$city]', $result['result']['address_component']['city'], $msg->text);
                        $msg->text = str_replace('[$district]', $result['result']['address_component']['district'], $msg->text);
                    }
                }

                $news = '{"content":"' . $msg->text . '"}';
                $news = json_decode($news, true);
                $this->returnTxt($news['content']);
            }
            elseif($msg->type == 'pic_txt'){
                $picTxtObj = PicTxt::find($msg->pic_txt_id);
                $materials = $picTxtObj->materials;
                if($materials && count($materials) > 0 && $materials) {
                    $news = array();
                    foreach ($materials as $item) {
                        $picurl = config('domain.api_url').$item->img;
                        $news[] = array(
                            'title' => $item->title,
                            'desc' => $item->description,
                            'picurl' => $picurl,
                            'url' => $item->url
                        );
                    }

                    $this->returnManyNews($news);
                }
                else{
                    $news = '{"content":"你好，欢迎光临"}';
                    $news = json_decode($news, true);
                    $this->returnTxt($news['content']);
                }
            }
            elseif($msg->type == 'img' && $msg->img){
                //使用客服消息发送图片，因为上传文件花时间，5秒内不一定能成功
                $openid = (string)$this->_USERDATA['fromusername'];
                $file = substr($msg->img, 1);
                $media_path = public_path($file);
                $wxObj = new Wechat();
                $wx_info = $wxObj->findByOpenid($openid);
                if($wx_info) {
                    $memberPicTxt = MemberPicTxt::create([
                        "openid" => $openid,
                        "member_id" => $wx_info->member_id,
                        "media_path" => $media_path,
                        "type" => 'image'
                    ]);
                    dispatch(new SendCustomMsg($memberPicTxt));
                }

                echo "success";
                exit;
            }
            else{
                $news = '{"content":"您好，欢迎光临"}';
                $news = json_decode($news, true);
                $this->returnTxt($news['content']);
            }
        }
        else{
            $autoObj = new AutoReply();
            $automsg = $autoObj->findByCategory('default');
            if($automsg){
                $this->AiReply($automsg);
            }
            else{
                $news = '{"content":"您好，欢迎光临"}';
                $news = json_decode($news, true);
                $this->returnTxt($news['content']);
            }
        }
        exit;
    }

    private function updateUserBySubscribe(){
        $appid = config('wechat.appid');
        $appsecret = config('wechat.appsecret');
        $platform = config('wechat.platform');
        $openid = (string)$this->_USERDATA['fromusername'];
        $scene = isset($this->_USERDATA['scene']) ? $this->_USERDATA['scene'] : '';
        $userInfo = WechatService::userInfoByOpenid($appid, $appsecret, $openid);
        if($userInfo){
            $mObj = new Member();
            $m_info = $mObj->findByUnionId($userInfo['unionid']);
            if($m_info){
                //该用户名下有使用平台应用
                $member_id = $m_info->id;
                $wxObj = new Wechat();
                $wx_info = $wxObj->findByOpenid($openid);
                if(!$wx_info){
                    //未使用过该公众号
                    $res = Wechat::create([
                        "openid"=> $openid,
                        "unionid"=> $userInfo['unionid'],
                        "nickname"=> $userInfo['nickname'],
                        "subscribe"=> $userInfo['subscribe'],
                        "gender"=> $userInfo['sex'] ? 'man' : 'woman',
                        "city"=> $userInfo['city'],
                        "province"=> $userInfo['province'],
                        "country"=> $userInfo['country'],
                        "headimgurl"=> $userInfo['headimgurl'],
                        "member_id"=> $member_id,
                        "platform"=> $platform
                    ]);
                }
                else{
                    //更新微信信息
                    $res = Wechat::where('openid', $openid)
                        ->update([
                            "nickname"=> $userInfo['nickname'],
                            "subscribe"=> $userInfo['subscribe'],
                            "gender"=> $userInfo['sex'] ? 'man' : 'woman',
                            "city"=> $userInfo['city'],
                            "province"=> $userInfo['province'],
                            "country"=> $userInfo['country'],
                            "headimgurl"=> $userInfo['headimgurl']
                        ]);
                }
            }
            else{
                //该用户名下未使用平台应用，生成一个全新的用户
                $nmObj = new Member();
                $userInfo['platform'] = $platform;
                $member_id = $nmObj->generateMember($userInfo);
            }

            if($scene) {
                //扫码关注广播
                $mObj = Member::find($member_id);
                $mObj->channel = $scene;
                $mObj->save();
                $mObj->wechats;
                event(new WechatScanLogin($mObj));
            }
        }
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/pictxtlist",
     *     tags={"微信wechat"},
     *     operationId="pictxtlist",
     *     summary="图文列表（分页）",
     *     description="使用说明：获取图文列表（分页）",
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
     *         description="搜索关键词",
     *         in="query",
     *         name="search",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function picTxtList(PicTxtPageRequest $request){
        $page = $request->input('page');
        $limit = $request->input('num');
        $limit = $limit ? $limit : 10;
        $search = $request->input('search');
        $offset = ($page - 1) * $limit;
        $search_like = '%'.$search.'%';

        $total = PicTxt::select(['id']);
        $lists = PicTxt::select(['*']);
        if($search){
            $total = $total->where('name', 'like', $search_like);
            $lists= $lists->where('name', 'like', $search_like);
        }

        $total = $total->count();
        $lists= $lists->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        if($lists){
            foreach ($lists as $key=>$item){
                $lists[$key]['materials'] = $item->materials;
            }
            $res = array(
                'data'=>$lists,
                'total'=>$total
            );
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/pictxtlistall",
     *     tags={"微信wechat"},
     *     operationId="pictxtlistall",
     *     summary="图文列表（所有）",
     *     description="使用说明：获取图文列表（所有）",
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
     *         description="successful operation",
     *     )
     * )
     */
    public function picTxtListAll(Request $request){
        $lists = PicTxt::select(['*']);
        $lists = $lists->orderBy('id', 'desc') ->get();

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $lists ? $lists : []);
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/pictxt/{pictxt}/materials",
     *     tags={"微信wechat"},
     *     operationId="materials",
     *     summary="某个图文素材列表",
     *     description="使用说明：获取某个图文素材列表",
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
     *         description="ID",
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
     *     )
     * )
     */
    public function picTxtMaterialList(PicTxt $pictxt){
        if($pictxt) {
            $materials = $pictxt->materials;
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $materials);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/wechat/storepictxt",
     *     tags={"微信wechat"},
     *     operationId="storepictxt",
     *     summary="保存图文",
     *     description="使用说明：保存图文",
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
     *         description="图文id（修改时需要）",
     *         in="query",
     *         name="id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="图文名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function storePicTxt(StorePicTxtRequest $request){
        $id = $request->input('id');
        $params = request(['name']);

        if($id){
            $obj = PicTxt::find($id);
            $obj->name = $params['name'];
            $res = $obj->save();
        }
        else{
            $res = PicTxt::create($params);
        }

        if($res){
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/wechat/deletepictxt",
     *     tags={"微信wechat"},
     *     operationId="APIList",
     *     summary="删除图文",
     *     description="使用说明：删除图文",
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
     *         description="图文ID",
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function deletePicTxt(DeletePicTxtRequest $request){
        $id = $request->input('id');
        $obj = PicTxt::find($id);
        if($obj) {
            $res = $obj->delete();
            if ($res) {
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
            } else {
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '数据不存在', '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/wechat/storematerial",
     *     tags={"微信wechat"},
     *     operationId="storematerial",
     *     summary="保存微信素材",
     *     description="使用说明：保存微信素材",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="微信素材id（修改时需要）",
     *         in="query",
     *         name="id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="图片",
     *         in="query",
     *         name="img",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="标题",
     *         in="query",
     *         name="title",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="描述",
     *         in="query",
     *         name="description",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="链接",
     *         in="query",
     *         name="url",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="排序号",
     *         in="query",
     *         name="sort",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="发送数",
     *         in="query",
     *         name="send_num",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="阅读数",
     *         in="query",
     *         name="read_num",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="购买数",
     *         in="query",
     *         name="buy_num",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="素材类型",
     *         in="query",
     *         name="type",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="图文id",
     *         in="query",
     *         name="pic_txt_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function storeMaterial(StoreMaterialRequest $request){
        $id = $request->input('id');
        $params = request(['img', 'title', 'description', 'url', 'sort', 'send_num', 'read_num', 'buy_num', 'type', 'pic_txt_id']);

        if($id){
            $obj = Material::find($id);
            $obj->img = $params['img'] ? $params['img'] : null;
            $obj->title = $params['title'] ? $params['title'] : null;
            $obj->description = $params['description'] ? $params['description'] : null;
            $obj->url = $params['url'] ? $params['url'] : null;
            $obj->sort = $params['sort'] ? $params['sort'] : null;
            $obj->send_num = isset($params['send_num']) && $params['send_num'] ? $params['send_num'] : null;
            $obj->read_num = isset($params['read_num']) && $params['read_num'] ? $params['read_num'] : null;
            $obj->buy_num = isset($params['buy_num']) && $params['buy_num'] ? $params['buy_num'] : null;
            $obj->type = $params['type'] ? $params['type'] : null;
            $obj->pic_txt_id = $params['pic_txt_id'] ? $params['pic_txt_id'] : null;
            $res = $obj->save();
        }
        else{
            $res = Material::create($params);
        }

        if($res){
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/material/{material}",
     *     tags={"微信wechat"},
     *     operationId="material-id",
     *     summary="某个图文素材详情",
     *     description="使用说明：获取某个图文素材详情",
     *     @OA\Parameter(
     *         description="token",
     *         in="query",
     *         name="token",
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="素材ID",
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
     *     )
     * )
     */
    public function materialDetail(Material $material){
        if($material) {
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $material);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/wechat/deletematerial",
     *     tags={"微信wechat"},
     *     operationId="deletematerial",
     *     summary="删除素材",
     *     description="使用说明：删除素材",
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
     *         description="素材ID",
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function deleteMaterial(Request $request){
        $id = $request->input('id');
        $obj = Material::find($id);
        if($obj) {
            $res = $obj->delete();
            if ($res) {
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
            }
            else {
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '数据不存在', '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/wechat/upload",
     *     tags={"微信wechat"},
     *     operationId="wechat-upload",
     *     summary="上传文件",
     *     description="使用说明：上传文件",
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
     *         description="上传文件名",
     *         in="query",
     *         name="file",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $name = 'wechat/'.date('Ymd');  //指定的是目录名，而不是文件名 storage/app下
        $path = $file->store($name, 'uploads');    //返回文件的路径和文件名，第二个参数为指定磁盘
        $path = '/uploads/'.$path;

        $bool = true;
        if($bool) {
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, [
                "path" => $path
            ]);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, []);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/material/{material}/members",
     *     tags={"微信wechat"},
     *     operationId="wechat-material-member",
     *     summary="素材用户",
     *     description="使用说明：获取素材用户",
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
     *         description="素材ID",
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
     *     )
     * )
     */
    public function materialMember(Material $material){
        $members = $material->members; //带括号的是返回关联对象实例，不带括号是返回动态属性
        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $members);
    }

    /**
     * @OA\Post(
     *     path="/api/wechat/storememberpictxt",
     *     tags={"微信wechat"},
     *     operationId="storememberpictxt",
     *     summary="用户图文推送",
     *     description="户图文推送",
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
     *         description="推送用户id列表",
     *         in="query",
     *         name="member_ids",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="推送类型",
     *         in="query",
     *         name="type",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="图文id",
     *         in="query",
     *         name="pic_txt_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="文本信息",
     *         in="query",
     *         name="text",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function storeMemberPicTxt(StoreMemberPicTxtRequest $request){
        $member_ids = $request->input('member_ids');
        $params = request(['type', 'pic_txt_id', 'member_ids', 'text']);
        if(($params['type'] == 'pictxt' && $params['pic_txt_id']) || ($params['type'] == 'text' && $params['text'])){
            $member_arr = array();
            if(strpos($member_ids, ',') !== false){
                $member_arr = explode(',', $member_ids);
            }
            else{
                $member_arr = [$member_ids];
            }

            $success = 0;
            $fail = 0;
            foreach($member_arr as $id){
                $member = Member::find($id);
                if($member) {
                    $wechats = $member->wechats;
                    $p = array(
                        "type" => $params['type'],
                        "pic_txt_id" => isset($params['pic_txt_id']) && $params['pic_txt_id'] ? $params['pic_txt_id'] : null,
                        "openid" => $wechats && count($wechats) > 0 ? $wechats[0]['openid'] : null,
                        "text" => isset($params['text']) && $params['text'] ? $params['text'] : null,
                        "member_id" => $id
                    );
                    $memberPicTxt = MemberPicTxt::create($p);
                    if($memberPicTxt){
                        $success++;
                        dispatch(new SendCustomMsg($memberPicTxt));
                    }
                    else{
                        $fail++;
                    }
                }
                else{
                    $fail++;
                }
            }
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG.$success.'条，失败'.$fail.'条', '');
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '参数错误', '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/pictxtqueue",
     *     tags={"微信wechat"},
     *     summary="图文消息队列",
     *     description="使用说明：获取图文消息队列",
     *     @OA\Parameter(
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
     *         description="搜索关键词",
     *         in="query",
     *         name="search",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function picTxtQueue(PicTxtPageRequest $request){
        $page = $request->input('page');
        $limit = $request->input('num');
        $limit = $limit ? $limit : 10;
        $search = $request->input('search');
        $offset = ($page - 1) * $limit;
        $search_like = '%'.$search.'%';
        $typeArr = array('text', 'pictxt');

        $total = DB::table('member_pic_txt')
            ->leftJoin('wx_pic_txt', 'member_pic_txt.pic_txt_id', '=', 'wx_pic_txt.id')
            ->select('member_pic_txt.id')
            ->where('member_pic_txt.type', $typeArr)
            ->whereNull('member_pic_txt.deleted_at');
        $lists = DB::table('member_pic_txt')
            ->leftJoin('wx_pic_txt', 'member_pic_txt.pic_txt_id', '=', 'wx_pic_txt.id')
            ->select('member_pic_txt.*', 'wx_pic_txt.name')
            ->where('member_pic_txt.type', $typeArr)
            ->whereNull('member_pic_txt.deleted_at');

        if($search){
            $total = $total->where('wx_pic_txt.name', 'like', $search_like);
            $lists= $lists->where('wx_pic_txt.name', 'like', $search_like);
        }

        $total = $total->count();
        $lists = $lists->orderBy('member_pic_txt.id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        if($lists){
            foreach ($lists as $key=>$item){
                $member = Member::find($item->member_id);
                if($member){
                    $member->wechats;
                }
                $lists[$key]->member = $member;
            }
            $res = array(
                'data'=>$lists,
                'total'=>$total
            );
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**@OA\Schema(
     *             type="integer"
     *         ),
     * @OA\Post(
     *     path="/api/wechat/deletepictxtqueue",
     *     tags={"微信wechat"},
     *     operationId="deletepictxtqueue",
     *     summary="删除图文消息队列",
     *     description="使用说明：删除图文消息队列",
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
     *         description="图文消息队列ID",
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function deletePictxtQueue(Request $request){
        $id = $request->input('id');
        $obj = MemberPicTxt::find($id);
        if($obj) {
            $res = $obj->delete();
            if ($res) {
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
            }
            else {
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '数据不存在', '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/wechat/pictxtqueue/batchdelete",
     *     tags={"微信wechat"},
     *     operationId="pictxtqueue-batchdelete",
     *     summary="删除图文消息队列（批量）",
     *     description="使用说明：删除图文消息队列（批量）",
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
     *         description="图文消息队列id列表",
     *         in="query",
     *         name="idstring",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function batchDeletePicTxtQueue(Request $request){
        $idstring = $request->input('idstring');
        $idarray = explode(',', $idstring);
        $res = MemberPicTxt::whereIn('id', $idarray)->delete();
        if ($res) {
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
        } else {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/wechat/storeautoreply",
     *     tags={"微信wechat"},
     *     operationId="storeautoreply",
     *     summary="自动回复",
     *     description="使用说明：自动回复",
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
     *         description="id（修改时需要）",
     *         in="query",
     *         name="id",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="回复类型",
     *         in="query",
     *         name="category",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="消息类型",
     *         in="query",
     *         name="type",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="文本信息",
     *         in="query",
     *         name="text",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="图文id",
     *         in="query",
     *         name="pic_txt_id",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="图片路径",
     *         in="query",
     *         name="img",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="关键词回复",
     *         in="query",
     *         name="keyword",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="间隔时间",
     *         in="query",
     *         name="interval_time",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function storeAutoReply(StoreAutoReplyRequest $request){
        $id = $request->input('id');
        $params = request(['category', 'type', 'text', 'pic_txt_id', 'img', 'keyword', 'interval_time']);
        if($id){
            $obj = AutoReply::find($id);
            $obj->category = $params['category'];
            $obj->type = $params['type'];
            $obj->text = isset($params['text']) && $params['text'] ? $params['text'] : null;
            $obj->pic_txt_id = isset($params['pic_txt_id']) && $params['pic_txt_id'] ? $params['pic_txt_id'] : null;
            $obj->img = isset($params['img']) && $params['img'] ? $params['img'] : null;
            $obj->keyword = isset($params['keyword']) && $params['keyword'] ? $params['keyword'] : null;
            $obj->interval_time = isset($params['interval_time']) && $params['interval_time'] ? $params['interval_time'] : null;
            $res = $obj->save();
        }
        else{
            $res = AutoReply::create($params);
        }

        if($res){
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/wechat/deleteautoreply",
     *     tags={"微信wechat"},
     *     operationId="deleteautoreply",
     *     summary="删除关键字自动回复",
     *     description="使用说明：删除关键字自动回复",
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
     *         description="关键字自动回复ID",
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function deleteAutoReply(Request $request){
        $id = $request->input('id');
        $obj = AutoReply::find($id);
        if($obj) {
            $res = $obj->delete();
            if ($res) {
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
            }
            else {
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '数据不存在', '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/autoreply",
     *     tags={"微信wechat"},
     *     operationId="autoreply",
     *     summary="获取自动回复",
     *     description="使用说明：获取自动回复",
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
     *         description="category",
     *         in="query",
     *         name="category",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function autoReply(Request $request){
        $category = $request->input('category');
        if($category) {
            $data = AutoReply::where('category', $category)
                ->orderBy('id', 'desc')->first();

            if ($data) {
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data);
            }
            else {
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '参数缺失', '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/keywords",
     *     tags={"微信wechat"},
     *     operationId="wechat-keywords",
     *     summary="关键字列表（分页）",
     *     description="使用说明：获取关键字列表（分页）",
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
     *         description="搜索关键词",
     *         in="query",
     *         name="search",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function keywords(KeywordsPageRequest $request){
        $page = $request->input('page');
        $limit = $request->input('num');
        $limit = $limit ? $limit : 10;
        $search = $request->input('search');
        $offset = ($page - 1) * $limit;
        $search_like = '%'.$search.'%';

        $total = AutoReply::select(['id'])->where('category', 'keyword');
        $lists = AutoReply::select(['*'])->where('category', 'keyword');
        if($search){
            $total = $total->where('keyword', 'like', $search_like);
            $lists= $lists->where('keyword', 'like', $search_like);
        }

        $total = $total->count();
        $lists= $lists->orderBy('id', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        if($lists){
            $res = array(
                'data'=>$lists,
                'total'=>$total
            );
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $res);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/keylists",
     *     tags={"微信wechat"},
     *     operationId="wechat-keylists",
     *     summary="关键字列表（不分页）",
     *     description="使用说明：获取关键字列表（不分页）",
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
     *         description="successful operation",
     *     )
     * )
     */
    public function keylists(Request $request){
        $lists = AutoReply::where('category', 'keyword')
                 ->orderBy('id', 'desc')->get();

        return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $lists && count($lists) > 0 ? $lists : []);
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/menus",
     *     tags={"微信wechat"},
     *     operationId="wechat-menus",
     *     summary="自定义菜单列表",
     *     description="使用说明：自定义菜单列表",
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
     *         description="successful operation",
     *     )
     * )
     */
    public function menus(){
        $obj = new Menu();
        $data = $obj->lists();
        if ($data) {
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/wechat/insertmenu",
     *     tags={"微信wechat"},
     *     operationId="insertmenu",
     *     summary="插入菜单",
     *     description="使用说明：获取插入菜单",
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
     *         description="菜单节点名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="菜单父节点path",
     *         in="query",
     *         name="parent_path",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="菜单排序号",
     *         in="query",
     *         name="sort",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *      @OA\Parameter(
     *         description="菜单类型",
     *         in="query",
     *         name="type",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="关键字",
     *         in="query",
     *         name="keyword",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="小程序appid",
     *         in="query",
     *         name="appid",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="小程序路径",
     *         in="query",
     *         name="pagepath",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="菜单链接",
     *         in="query",
     *         name="url",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="是否开启",
     *         in="query",
     *         name="is_open",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="是否根节点",
     *         in="query",
     *         name="is_root",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function insertmenu(Request $request){
        $categoryObj = new Menu();

        $name = $request->input('name');
        $parent_path = $request->input('parent_path');
        $sort = $request->input('sort');
        $type = $request->input('type');
        $keyword = $request->input('keyword');
        $appid = $request->input('appid');
        $pagepath = $request->input('pagepath');
        $url = $request->input('url');
        $is_open = $request->input('is_open');
        $is_root = $request->input('is_root');

        if($is_root){
            $level = 1;
        }
        elseif($parent_path && strpos($parent_path, '/') !== false){
            $pathArray = explode('/', $parent_path);
            $level = count($pathArray) + 1;
        }
        elseif($parent_path && strpos($parent_path, '/') === false){
            $level = 2;
        }

        $p = [
            'name' => $name,
            'level' => $level,
            'type' => $type,
            'is_root' => $is_root,
            'is_open' => $is_open,
            'sort' => $sort
        ];

        if($type == 'view'){
            $p['url'] = $url;
        }
        elseif($type == 'miniprogram') {
            $p['url'] = $url;
            $p['appid'] = $appid;
            $p['pagepath'] = $pagepath;
        }
        elseif($type == 'click') {
            $p['keyword'] = $keyword;
        }

        $id = $categoryObj->insert($p);
        if($id){
            $params = [];
            $params['updated_at'] = date('Y-m-d H:i:s', time());
            if($is_root){
                $path = $id;
            }
            else{
                $path = $parent_path."/".$id;
            }

            $row = $categoryObj->rowById($id);
            if($row){
                $row->path = $path;
                $res = $row->save();
                if($res){
                    return UtilService::format_data(self::AJAX_SUCCESS, '插入成功', ['id'=>$id]);
                }
                else{
                    return UtilService::format_data(self::AJAX_FAIL, '插入失败', '');
                }
            }
            else{
                return UtilService::format_data(self::AJAX_FAIL, '插入失败', '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '插入失败', '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/wechat/updatemenu",
     *     tags={"微信wechat"},
     *     operationId="updatemenu",
     *     summary="修改菜单",
     *     description="使用说明：获取修改菜单",
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
     *         description="菜单节点名称",
     *         in="query",
     *         name="name",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="节点ID",
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="菜单排序号",
     *         in="query",
     *         name="sort",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="菜单类型",
     *         in="query",
     *         name="type",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="关键字",
     *         in="query",
     *         name="keyword",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="小程序appid",
     *         in="query",
     *         name="appid",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="小程序路径",
     *         in="query",
     *         name="pagepath",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="菜单链接",
     *         in="query",
     *         name="url",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Parameter(
     *         description="是否开启",
     *         in="query",
     *         name="is_open",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function updatemenu(Request $request){
        $categoryObj = new Menu();
        $name = $request->input('name');
        $sort = $request->input('sort');
        $id = $request->input('id');
        $type = $request->input('type');
        $url = $request->input('url');
        $keyword = $request->input('keyword');
        $appid = $request->input('appid');
        $pagepath = $request->input('pagepath');
        $is_open = $request->input('is_open');
        $row = $categoryObj->rowById($id);
        if($row){
            $row->name = $name;
            $row->sort = $sort;
            $row->is_open = $is_open;
            $row->type = $type;

            if($type == 'view'){
                $row->url = $url;
            }
            elseif($type == 'miniprogram') {
                $row->url = $url;
                $row->appid = $appid;
                $row->pagepath = $pagepath;
            }
            elseif($type == 'click') {
                $row->keyword = $keyword;
            }

            $res = $row->save();
            if($res){
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['id'=>$id]);
            }
            else{
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '数据出错', '');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/wechat/deletemenu",
     *     tags={"微信wechat"},
     *     operationId="deletemenu",
     *     summary="删除菜单",
     *     description="使用说明：删除菜单",
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
     *         description="菜单ID",
     *         in="query",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function deletemenu(Request $request){
        $categoryObj = new Menu();
        $id = $request->input('id');
        $row = $categoryObj->rowById($id);
        if($row){
            $res = $row->delete();
            if($res){
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, ['id'=>$id]);
            }
            else{
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '数据出错', '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/menuchildren",
     *     tags={"微信wechat"},
     *     operationId="menuchildren",
     *     summary="菜单子节点",
     *     description="使用说明：菜单子节点",
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
     *         description="菜单path",
     *         in="query",
     *         name="path",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     */
    public function menuchildren(Request $request){
        $path = $request->input('path');
        $obj = new Menu();
        $lists = $obj->childmenu($path);
        if($lists){
            return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $lists);
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/publishmenu",
     *     tags={"微信wechat"},
     *     operationId="publishmenu",
     *     summary="发布菜单",
     *     description="使用说明：发布菜单",
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
     *         description="successful operation",
     *     )
     * )
     */
    public function publishmenu(Request $request){
        $obj = new Menu();
        $topmenus = $obj->topmenu();
        if ($topmenus) {
            $menu = array(
                'button'=>array()
            );
            foreach ($topmenus as $item){
                $childObj = new Menu();
                $data = array();
                $data['name'] = $item->name;
                $children = $childObj->childmenu($item->path);
                if($children && count($children) > 0){
                    $sub_button = array();
                    foreach ($children as $child) {
                        $v = array();
                        $v['name'] = $child->name;
                        if($child->type == 'click'){
                            $v['type'] = $child->type;
                            $v['key'] = substr(md5($child->id), 0, 5);
                        }
                        elseif($child->type == 'view'){
                            $v['type'] = $child->type;
                            $v['url'] = $child->url;
                        }
                        elseif($child->type == 'miniprogram') {
                            $v['type'] = $child->type;
                            $v['appid'] = $child->appid;
                            $v['pagepath'] = $child->pagepath;
                            $v['url'] = $child->url;
                        }
                        $sub_button[] = $v;
                    }
                    $data['sub_button'] = $sub_button;
                }
                elseif($item->type == 'click'){
                    $data['type'] = $item->type;
                    $data['key'] = substr(md5($item->id), 0, 5);
                }
                elseif($item->type == 'view'){
                    $data['type'] = $item->type;
                    $data['url'] = $item->url;
                }
                elseif($item->type == 'miniprogram') {
                    $data['type'] = $item->type;
                    $data['appid'] = $item->appid;
                    $data['pagepath'] = $item->pagepath;
                    $data['url'] = $item->url;
                }
                $menu['button'][] = $data;
            }

            $appid = config('wechat.appid');
            $appsecret = config('wechat.appsecret');
            $rtn = WechatService::createMenu($appid, $appsecret, $menu);
            return $rtn;
        }
        else {
            return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/wechat/qrcode",
     *     tags={"微信wechat"},
     *     operationId="wechat-qrcode",
     *     summary="扫码登录二维码(SASS端用户使用)",
     *     description="使用说明：获取扫码登录二维码",
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
     *         description="successful operation",
     *     )
     * )
     */
    public function qrcode()
    {
        $user = auth()->user();
        $appid = config('wechat.appid');
        $appsecret = config('wechat.appsecret');
        $access_token = WechatService::getAccessToken($appid, $appsecret);
        if($access_token) {
            $wechat = '';
            $member = $user->member;
            if($member) {
                $wechats = $member->wechats;
                if ($wechats) {
                    foreach ($wechats as $item) {
                        $platform = config('wechat.platform');
                        if ($platform == $item->platform) {
                            $wechat = $item;
                            break;
                        }
                    }
                }
            }

            $scene_str = UtilService::random_str(6);
            $img = WechatService::limit_qrcode($access_token, $type='limit', $scene_str);
            if ($img) {
                $data = array(
                    "qrcode" => $img,
                    "channel" => $scene_str,
                    "wechat" => $wechat
                );
                return UtilService::format_data(self::AJAX_SUCCESS, self::AJAX_SUCCESS_MSG, $data);
            } else {
                return UtilService::format_data(self::AJAX_FAIL, self::AJAX_FAIL_MSG, '');
            }
        }
        else{
            return UtilService::format_data(self::AJAX_FAIL, '获取token失败', '');
        }
    }

    public function ilovethisgame(){
        Cache::flush();
        dd('success');
    }

    public function phpinfo(){
        phpinfo();
    }

    private function dateData(){
        $electricity_produce = 0;    //发电量
        $electricity_supply = 0;     //供电量
        $enter_factory_rubbish = 0;  //垃圾入库量
        $incineration_rubbish = 0;   //垃圾入炉量
        $produce_leachate = 0;       //渗沥液产生量
        $handle_leachate = 0;        //渗沥液处理量

        $data = OfflineData::where('time', date('Y-m-d'))->get();
        if($data && count($data) > 0){
            foreach ($data as $key => $item) {
                if(strpos($item->en_name, 'electric_energy') !== false && strpos($item->en_name, 'online') === false){
                    $electricity_produce += (int)$item->value;
                }
                elseif(strpos($item->en_name, 'online_electric_energy') !== false){
                    $electricity_supply += (int)$item->value;
                }
                elseif(strpos($item->en_name, 'enter_factory_rubbish') !== false){
                    $enter_factory_rubbish += (int)$item->value;
                }
                elseif(strpos($item->en_name, 'incineration_rubbish') !== false){
                    $incineration_rubbish += (int)$item->value;
                }
                elseif(strpos($item->en_name, 'produce_leachate') !== false){
                    $produce_leachate += (int)$item->value;
                }
                elseif(strpos($item->en_name, 'handle_leachate') !== false){
                    $handle_leachate += (int)$item->value;
                }
            }
        }

        return array(
            "electricity_produce" => $electricity_produce,
            "electricity_supply" => $electricity_supply,
            "enter_factory_rubbish" => $enter_factory_rubbish,
            "incineration_rubbish" => $incineration_rubbish,
            "produce_leachate" => $produce_leachate,
            "handle_leachate" => $handle_leachate
        );
    }

    private function monthData(){
        $electricity_produce = 0;    //发电量
        $electricity_supply = 0;     //供电量
        $enter_factory_rubbish = 0;  //垃圾入库量
        $incineration_rubbish = 0;   //垃圾入炉量
        $ton_rubbish_electricity_supply = 0;       //吨垃圾供电量
        $ton_rubbish_electricity_produce = 0;        //吨垃圾发电量
        $water_consume = 0;          //自来水消耗量（吨）
        $auxiliary_power_ratio = 0;  //厂用电率
        $supply_water_rate = 0;      //厂补水率
        $desalted_water = 0;         //厂除盐水量

        $data = OfflineData::where('time', '>=', date('Y-m') . '-01')->get();
        if($data && count($data) > 0){
            foreach ($data as $key => $item) {
                if(strpos($item->en_name, 'electric_energy') !== false && strpos($item->en_name, 'online') === false){
                    $electricity_produce += (int)$item->value;
                }
                elseif(strpos($item->en_name, 'online_electric_energy') !== false){
                    $electricity_supply += (int)$item->value;
                }
                elseif(strpos($item->en_name, 'enter_factory_rubbish') !== false){
                    $enter_factory_rubbish += (int)$item->value;
                }
                elseif(strpos($item->en_name, 'incineration_rubbish') !== false){
                    $incineration_rubbish += (int)$item->value;
                }
                elseif(strpos($item->en_name, 'water_consume') !== false){
                    $water_consume += (int)$item->value;
                }
                elseif(strpos($item->en_name, 'desalted_water') !== false){
                    $desalted_water += (int)$item->value;
                }
            }
        }

        $dailyD = $this->daiyDataList();
        return array(
            "electricity_produce" => $electricity_produce,
            "electricity_supply" => $electricity_supply,
            "enter_factory_rubbish" => $enter_factory_rubbish,
            "incineration_rubbish" => $incineration_rubbish,
            "ton_rubbish_electricity_supply" => $incineration_rubbish ? round($ton_rubbish_electricity_supply/$incineration_rubbish, 2) : 0,
            "ton_rubbish_electricity_produce" => $incineration_rubbish ? round($ton_rubbish_electricity_produce/$incineration_rubbish, 2) : 0,
            "water_consume" => $water_consume,
            "auxiliary_power_ratio" => $dailyD ? $dailyD['factory_use_electricity_rate_total'] : 0,
            "supply_water_rate" => $dailyD ? $dailyD['supply_water_rate_total'] : 0,
            "desalted_water" => $desalted_water
        );
    }

    private function datetempmsg($openid, $data){
        $appid = config('wechat.appid');
        $appsecret = config('wechat.appsecret');
        $postData = array(
            "touser" => (string)$openid,
            "template_id" => config('template.date_data'),
            "url" => "",
            "data" => array(
                "first" => array(
                    "value" => '今日生产数据奉上',
                    "color" => "#173177"
                ),
                "keyword1" => array(
                    "value" => date('Y-m-d'),
                    "color" => "#173177"
                ),
                "keyword2" => array(
                    "value" => $data['electricity_produce'],
                    "color" => "#173177"
                ),
                "keyword3" => array(
                    "value" => $data['electricity_supply'],
                    "color" => "#173177"
                ),
                "keyword4" => array(
                    "value" => $data['enter_factory_rubbish'],
                    "color" => "#173177"
                ),
                "keyword5" => array(
                    "value" => $data['incineration_rubbish'],
                    "color" => "#173177"
                ),
                "keyword6" => array(
                    "value" => $data['produce_leachate'],
                    "color" => "#173177"
                ),
                "keyword7" => array(
                    "value" => $data['handle_leachate'],
                    "color" => "#173177"
                ),
                "remark" => array(
                    "value" => "数据怎样，想办法提升提升吧",
                    "color" => "#173177"
                )
            )
        );

        $res = WechatService::sendTemplateMessage($appid, $appsecret, $postData);
        if(!$res){
            $news = '{"content":"抱歉，发送失败"}';
            $news = json_decode($news, true);
            $this->returnTxt($news['content']);
        }
    }

    private function monthtempmsg($openid, $data){
        $appid = config('wechat.appid');
        $appsecret = config('wechat.appsecret');
        $postData = array(
            "touser" => (string)$openid,
            "template_id" => config('template.month_data'),
            "url" => "",
            "data" => array(
                "first" => array(
                    "value" => "本月生产数据奉上",
                    "color" => "#173177"
                ),
                "keyword1" => array(
                    "value" => date('Y-m-d'),
                    "color" => "#173177"
                ),
                "keyword2" => array(
                    "value" => $data['electricity_produce'],
                    "color" => "#173177"
                ),
                "keyword3" => array(
                    "value" => $data['electricity_supply'],
                    "color" => "#173177"
                ),
                "keyword4" => array(
                    "value" => $data['enter_factory_rubbish'],
                    "color" => "#173177"
                ),
                "keyword5" => array(
                    "value" => $data['incineration_rubbish'],
                    "color" => "#173177"
                ),
                "keyword6" => array(
                    "value" => $data['water_consume'],
                    "color" => "#173177"
                ),
                "keyword7" => array(
                    "value" => $data['ton_rubbish_electricity_produce'],
                    "color" => "#173177"
                ),
                "keyword8" => array(
                    "value" => $data['ton_rubbish_electricity_supply'],
                    "color" => "#173177"
                ),
                "keyword9" => array(
                    "value" => $data['auxiliary_power_ratio'],
                    "color" => "#173177"
                ),
                "keyword10" => array(
                    "value" => $data['supply_water_rate'],
                    "color" => "#173177"
                ),
                "keyword11" => array(
                    "value" => $data['desalted_water'],
                    "color" => "#173177"
                ),
                "remark" => array(
                    "value" => "数据怎样，想办法提升提升吧",
                    "color" => "#173177"
                )
            )
        );
        $res = WechatService::sendTemplateMessage($appid, $appsecret, $postData);
        if(!$res){
            $news = '{"content":"抱歉，发送失败"}';
            $news = json_decode($news, true);
            $this->returnTxt($news['content']);
        }
    }

    private function daiyDataList(){
        $time = date('Y-m-d');
        $period = 'date';
        $params = [];
        $params['time'] = $time;
        $params['period'] = $period;
        $economyDaily = new EconomyDaily();

        //数据初始化
        $input_val = array();
        $factory_use_electricity = 0; //厂用电量
        $factory_use_electricity_total = 0; //厂用电量累计
        $out_buy_electricity = 0; //外购电量
        $out_buy_electricity_total = 0; //外购电量累计
        $no1_electric_energy = 0; //3#机发电量
        $no1_electric_energy_total = 0; //3#机发电量累计
        $no2_electric_energy = 0; //4#机发电量
        $no2_electric_energy_total = 0; //4#机发电量累计
        $no1_online_electric_energy = 0; //3#机上网电量
        $no1_online_electric_energy_total = 0; //3#机上网电量累计
        $no2_online_electric_energy = 0; //4#机上网电量
        $no2_online_electric_energy_total = 0; //4#机上网电量累计
        $rubbish_incineration = 0; //垃圾焚烧量
        $rubbish_incineration_total = 0; //累计垃圾焚烧量
        $boiler_use_water = 0;     //锅炉用水
        $boiler_use_water_total = 0;     //锅炉累计用水
        $use_oil = 0;           //燃油耗量
        $use_oil_total = 0;     //累计燃油耗量
        $use_cement = 0;        //水泥用量
        $use_cement_total = 0;  //累计水泥用量

        $no1boiler_steam_flow = 0; //1#炉蒸汽流量
        $no2boiler_steam_flow = 0; //2#炉蒸汽流量
        $no3boiler_steam_flow = 0; //3#炉蒸汽流量
        $no1boiler_steam_flow_total = 0; //1#炉蒸汽累计流量
        $no2boiler_steam_flow_total = 0; //2#炉蒸汽累计流量
        $no3boiler_steam_flow_total = 0; //3#炉蒸汽累计流量

        //获取人工输入数据
        $inputList = $economyDaily->findByParams($params)->toArray();
        if(!$inputList || empty($inputList)){
            $taglist = config('economydaily.taglist');
            foreach ($taglist as $key => $value) {
                $input_val[$value['en_name']] = array(
                    "en_name"=>$value['en_name'],
                    "cn_name"=>$value['cn_name'],
                    "value"=>0,
                    "time"=>$time
                );
            }
        }
        else{
            foreach ($inputList as $key => $value) {
                unset($inputList[$key]['created_at']);
                unset($inputList[$key]['updated_at']);
                unset($inputList[$key]['deleted_at']);
                unset($inputList[$key]['period']);

                $input_val[$value['en_name']] = $value;
                if($value['en_name'] == 'factory_use_electricity'){
                    $factory_use_electricity = $value['value'];
                }
                elseif($value['en_name'] == 'out_buy_electricity'){
                    $out_buy_electricity = $value['value'];
                }
                elseif($value['en_name'] == 'no1_electric_energy'){
                    $no1_electric_energy = $value['value'];
                }
                elseif($value['en_name'] == 'no2_electric_energy'){
                    $no2_electric_energy = $value['value'];
                }
                elseif($value['en_name'] == 'no1_online_electric_energy'){
                    $no1_online_electric_energy = $value['value'];
                }
                elseif($value['en_name'] == 'no2_online_electric_energy'){
                    $no2_online_electric_energy = $value['value'];
                }
                elseif($value['en_name'] == 'rubbish_incineration'){
                    $rubbish_incineration = $value['value'];
                }
                elseif($value['en_name'] == 'boiler_use_water'){
                    $boiler_use_water = $value['value'];
                }
                elseif($value['en_name'] == 'use_oil'){
                    $use_oil = $value['value'];
                }
                elseif($value['en_name'] == 'use_cement'){
                    $use_cement = $value['value'];
                }
            }
        }

        //获取日常运行数据
        $dataCompute = DailyData::where('date', $time)->get()->toArray();
        $dataComputeTemp = array();
        foreach ($dataCompute as $key => $value) {
            unset($dataCompute[$key]['_id']);
            unset($dataCompute[$key]['created_at']);
            unset($dataCompute[$key]['updated_at']);
            $dataComputeTemp[$value['en_name']] = $dataCompute[$key];

            if($value['value'] < 10){
                $dataComputeTemp[$value['en_name']]['value'] = round($value['value'], 2);
            }

            if(strpos($value['en_name'], 'run_status') !== false){
                $dataComputeTemp[$value['en_name']]['value'] = round($value['value']/60, 2);
            }

            if($value['en_name'] == 'no1boiler@steam_flow'){
                $no1boiler_steam_flow = round($value['value'], 2);
            }
            elseif($value['en_name'] == 'no2boiler@steam_flow'){
                $no2boiler_steam_flow = round($value['value'], 2);
            }
            elseif($value['en_name'] == 'no3boiler@steam_flow'){
                $no3boiler_steam_flow = round($value['value'], 2);
            }
            elseif($value['en_name'] == 'no1boiler@steam_flow@total'){
                $no1boiler_steam_flow_total = $value['value'];
            }
            elseif($value['en_name'] == 'no2boiler@steam_flow@total'){
                $no2boiler_steam_flow_total = $value['value'];
            }
            elseif($value['en_name'] == 'no3boiler@steam_flow@total'){
                $no3boiler_steam_flow_total = $value['value'];
            }
            elseif($value['en_name'] == 'boiler_use_water@total'){
                $boiler_use_water_total = $value['value'];
            }
            elseif($value['en_name'] == 'rubbish_incineration@total'){
                $rubbish_incineration_total = $value['value'];
            }
            elseif($value['en_name'] == 'no1_electric_energy@total'){
                $no1_electric_energy_total = $value['value'];
            }
            elseif($value['en_name'] == 'no2_electric_energy@total'){
                $no2_electric_energy_total = $value['value'];
            }
            elseif($value['en_name'] == 'no1_online_electric_energy@total'){
                $no1_online_electric_energy_total = $value['value'];
            }
            elseif($value['en_name'] == 'no2_online_electric_energy@total'){
                $no2_online_electric_energy_total = $value['value'];
            }
            elseif($value['en_name'] == 'factory_use_electricity@total'){
                $factory_use_electricity_total = $value['value'];
            }
            elseif($value['en_name'] == 'out_buy_electricity@total'){
                $out_buy_electricity_total = $value['value'];
            }
            elseif($value['en_name'] == 'use_oil@total'){
                $use_oil_total = $value['value'];
            }
            elseif($value['en_name'] == 'use_cement@total'){
                $use_cement_total = $value['value'];
            }
        }

        //计算值
        $func_val = array();
        $factory_use_electricity_rate = ($no1_electric_energy + $no2_electric_energy) > 0 ? round((100 * $factory_use_electricity + $out_buy_electricity)/($no1_electric_energy + $no2_electric_energy), 2) : 0;
        $factory_use_electricity_rate_total = ($no1_electric_energy_total + $no2_electric_energy_total) > 0 ? round((100 * $factory_use_electricity_total + $out_buy_electricity_total)/($no1_electric_energy_total + $no2_electric_energy_total), 2) : 0;
        $ton_rubbish_electricity = $rubbish_incineration ? round(100 * ($no1_electric_energy + $no2_electric_energy)/$rubbish_incineration, 2) : 0;
        $ton_rubbish_electricity_total = $rubbish_incineration_total ? round(100 * ($no1_electric_energy_total + $no2_electric_energy_total)/$rubbish_incineration_total, 2) : 0;
        $ton_rubbish_online_electricity = $rubbish_incineration ? round(100 * ($no1_online_electric_energy + $no2_online_electric_energy)/$rubbish_incineration, 2) : 0;
        $ton_rubbish_online_electricity_total = $rubbish_incineration_total ? round(100 * ($no1_online_electric_energy_total + $no2_online_electric_energy_total)/$rubbish_incineration_total, 2) : 0;
        $supply_water_rate = ($no1boiler_steam_flow + $no2boiler_steam_flow + $no3boiler_steam_flow) > 0 ? round(100 * $boiler_use_water/($no1boiler_steam_flow + $no2boiler_steam_flow + $no3boiler_steam_flow), 2) : 0;
        $supply_water_rate_total = ($no1boiler_steam_flow_total + $no2boiler_steam_flow_total + $no3boiler_steam_flow_total) > 0 ? round(100 * $boiler_use_water_total/($no1boiler_steam_flow_total + $no2boiler_steam_flow_total + $no3boiler_steam_flow_total), 2) : 0;
        $lime_use = $rubbish_incineration ? round($rubbish_incineration * 0.006, 3) : 0;
        $active_carbon = $rubbish_incineration ? round($rubbish_incineration * 0.0005, 3) : 0;
        $lime_use_total = $rubbish_incineration_total ? round($rubbish_incineration_total * 0.006, 3) : 0;
        $active_carbon_total = $rubbish_incineration_total ? round($rubbish_incineration_total * 0.0005, 3) : 0;
        $no1turbine_steam_rate = $no1_electric_energy && isset($dataComputeTemp['no1turbine@entry_steam_flow']['value']) ? round(1000*$dataComputeTemp['no1turbine@entry_steam_flow']['value']/$no1_electric_energy, 2) : 0;
        $no2turbine_steam_rate = $no2_electric_energy && isset($dataComputeTemp['no2turbine@entry_steam_flow']['value']) ? round(1000*$dataComputeTemp['no2turbine@entry_steam_flow']['value']/$no2_electric_energy, 2) : 0;

        $func_val['factory_use_electricity_rate'] = array("value"=>$factory_use_electricity_rate);
        $func_val['factory_use_electricity_rate_total'] = array("value"=>$factory_use_electricity_rate_total);
        $func_val['ton_rubbish_electricity'] = array("value"=>$ton_rubbish_electricity);
        $func_val['ton_rubbish_electricity_total'] = array("value"=>$ton_rubbish_electricity_total);
        $func_val['ton_rubbish_online_electricity'] = array("value"=>$ton_rubbish_online_electricity);
        $func_val['ton_rubbish_online_electricity_total'] = array("value"=>$ton_rubbish_online_electricity_total);
        $func_val['supply_water_rate'] = array("value"=>$supply_water_rate);
        $func_val['supply_water_rate_total'] = array("value"=>$supply_water_rate_total);
        $func_val['lime_use'] = array("value"=>$lime_use);
        $func_val['active_carbon'] = array("value"=>$active_carbon);
        $func_val['lime_use_total'] = array("value"=>$lime_use_total);
        $func_val['active_carbon_total'] = array("value"=>$active_carbon_total);
        $func_val['no1turbine_steam_rate'] = array("value"=>$no1turbine_steam_rate);
        $func_val['no2turbine_steam_rate'] = array("value"=>$no2turbine_steam_rate);

        $final = array_merge($input_val, $func_val, $dataComputeTemp);
        //$final['summary']['value'] = nl2br($final['summary']['value']);

        return $final;
    }
}
