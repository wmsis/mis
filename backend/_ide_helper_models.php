<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Http\Models\Historian{
/**
 * App\Http\Models\Historian\AlarmRecord
 *
 * @property int $id
 * @property int|null $tag_id
 * @property string|null $start_time 报警开始时间
 * @property string|null $end_time 报警结束时间
 * @property string|null $upper_limit 设计时的上限
 * @property string|null $lower_limit 设计时的下限
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Historian\AlarmRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Historian\AlarmRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Historian\AlarmRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Historian\AlarmRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Historian\AlarmRecord whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Historian\AlarmRecord whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Historian\AlarmRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Historian\AlarmRecord whereLowerLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Historian\AlarmRecord whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Historian\AlarmRecord whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Historian\AlarmRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Historian\AlarmRecord whereUpperLimit($value)
 */
	class AlarmRecord extends \Eloquent {}
}

namespace App\Http\Models\Member{
/**
 * App\Http\Models\Member\Address
 *
 * @property int $id
 * @property int|null $mini_id
 * @property int|null $wechat_id
 * @property string|null $userName
 * @property string|null $postalCode
 * @property string|null $provinceName
 * @property string|null $cityName
 * @property string|null $countyName
 * @property string|null $detailInfo
 * @property string|null $nationalCode
 * @property string|null $telNumber
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Http\Models\Member\Mini|null $mini
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereCityName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereCountyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereDetailInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereMiniId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereNationalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereProvinceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereTelNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereUserName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Address whereWechatId($value)
 * @mixin \Eloquent
 */
	class Address extends \Eloquent {}
}

namespace App\Http\Models\Member{
/**
 * App\Http\Models\Member\Member
 *
 * @property int $id
 * @property string|null $unionid
 * @property string|null $password
 * @property string|null $username
 * @property string|null $mobile
 * @property int|null $consume_num 总消费次数
 * @property float|null $consume_money 总消费金额
 * @property string|null $last_consume_time 上次消费时间
 * @property bool|null $is_vip 是否会员
 * @property string|null $card_no
 * @property bool|null $is_distribute 是否分销
 * @property float|null $money 余额
 * @property float $freezen_money 冻结余额
 * @property float|null $total_income
 * @property float|null $today_income
 * @property int|null $point 积分
 * @property string|null $code
 * @property int|null $parent_id
 * @property string|null $router
 * @property string|null $level
 * @property float|null $vip_save_money
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Wechat\Material[] $materials
 * @property-read int|null $materials_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Member\Mini[] $minis
 * @property-read int|null $minis_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Member\Wechat[] $wechats
 * @property-read int|null $wechats_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Member\Member onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereCardNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereConsumeMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereConsumeNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereFreezenMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereIsDistribute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereIsVip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereLastConsumeTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member wherePoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereRouter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereTodayIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereTotalIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereVipSaveMoney($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Member\Member withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Member\Member withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $channel
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Member whereChannel($value)
 */
	class Member extends \Eloquent {}
}

namespace App\Http\Models\Member{
/**
 * App\Http\Models\Member\Mini
 *
 * @property int $id
 * @property string|null $unionid
 * @property string|null $openid
 * @property string|null $nickname
 * @property string|null $avatarurl
 * @property string $gender
 * @property string|null $city
 * @property string|null $province
 * @property string|null $country
 * @property string|null $platform
 * @property int|null $member_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Member\Address[] $addresses
 * @property-read int|null $addresses_count
 * @property-read \App\Http\Models\Member\Member|null $member
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereAvatarurl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Mini whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Mini extends \Eloquent {}
}

namespace App\Http\Models\Member{
/**
 * App\Http\Models\Member\TakeMoney
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\TakeMoney newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\TakeMoney newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\TakeMoney query()
 * @mixin \Eloquent
 */
	class TakeMoney extends \Eloquent {}
}

namespace App\Http\Models\Member{
/**
 * App\Http\Models\Member\Wechat
 *
 * @property int $id
 * @property string|null $headimgurl
 * @property string|null $nickname
 * @property string|null $openid
 * @property int|null $subscribe
 * @property string|null $city
 * @property string|null $province
 * @property string|null $country
 * @property string|null $unionid
 * @property string|null $gender
 * @property string|null $platform
 * @property int|null $member_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Member\Address[] $addresses
 * @property-read int|null $addresses_count
 * @property-read \App\Http\Models\Member\Member|null $member
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereHeadimgurl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereSubscribe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereUnionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Member\Wechat whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Wechat extends \Eloquent {}
}

namespace App\Http\Models{
/**
 * App\Http\Models\OperateLog
 *
 * @property int $id
 * @property int $user_id
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\OperateLog onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\OperateLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\OperateLog withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\OperateLog withoutTrashed()
 * @mixin \Eloquent
 */
	class OperateLog extends \Eloquent {}
}

namespace App\Http\Models\SIS{
/**
 * App\Http\Models\SIS\FrontConfig
 *
 * @property int $id
 * @property string $front_tag_id 前端标签ID
 * @property string|null $description 说明
 * @property int|null $historian_tag_id Historian Tag ID
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\FrontConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\FrontConfig newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\FrontConfig onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\FrontConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\FrontConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\FrontConfig whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\FrontConfig whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\FrontConfig whereFrontTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\FrontConfig whereHistorianTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\FrontConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\FrontConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\FrontConfig withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\FrontConfig withoutTrashed()
 * @mixin \Eloquent
 */
	class FrontConfig extends \Eloquent {}
}

namespace App\Http\Models\SIS{
/**
 * App\Http\Models\SIS\HistorianModule
 *
 * @property int $id
 * @property string $name 模块名
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\SIS\HistorianTag[] $historian_tags
 * @property-read int|null $historian_tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\HistorianModule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\HistorianModule withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\HistorianModule withoutTrashed()
 * @mixin \Eloquent
 * @property string|null $description
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianModule whereDescription($value)
 */
	class HistorianModule extends \Eloquent {}
}

namespace App\Http\Models\SIS{
/**
 * App\Http\Models\SIS\HistorianTag
 *
 * @property int $id
 * @property string $tag_id tag id
 * @property string $tag_name historian中的标签名
 * @property string|null $description
 * @property string|null $alias 别名
 * @property string|null $measure 量程
 * @property float|null $upper_limit 上限
 * @property float|null $lower_limit 下限
 * @property int|null $historian_module_id tag所属模块外键约束
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\HistorianTag onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag whereHistorianModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag whereLowerLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag whereMeasure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag whereTagId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag whereTagName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\SIS\HistorianTag whereUpperLimit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\HistorianTag withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\SIS\HistorianTag withoutTrashed()
 * @mixin \Eloquent
 */
	class HistorianTag extends \Eloquent {}
}

namespace App\Http\Models\Wechat{
/**
 * App\Http\Models\Wechat\AutoReply
 *
 * @property int $id
 * @property string|null $category
 * @property string|null $type
 * @property string|null $text
 * @property int|null $pic_txt_id
 * @property string|null $img
 * @property string|null $keyword 关键词回复时需要
 * @property int|null $interval_time 间隔时间（分钟），打开回复时需要
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Http\Models\Wechat\PicTxt|null $picTxt
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\AutoReply onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereIntervalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply wherePicTxtId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\AutoReply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\AutoReply withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\AutoReply withoutTrashed()
 * @mixin \Eloquent
 */
	class AutoReply extends \Eloquent {}
}

namespace App\Http\Models\Wechat{
/**
 * App\Http\Models\Wechat\Material
 *
 * @property int $id
 * @property string|null $img
 * @property string|null $title
 * @property string $description
 * @property string|null $url
 * @property int|null $sort
 * @property int|null $send_num
 * @property int|null $read_num
 * @property int|null $buy_num
 * @property string|null $type
 * @property int|null $pic_txt_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Member\Member[] $members
 * @property-read int|null $members_count
 * @property-read \App\Http\Models\Wechat\PicTxt $pictxt
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\Material onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereBuyNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material wherePicTxtId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereReadNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereSendNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Material whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\Material withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\Material withoutTrashed()
 * @mixin \Eloquent
 */
	class Material extends \Eloquent {}
}

namespace App\Http\Models\Wechat{
/**
 * App\Http\Models\Wechat\MemberPicTxt
 *
 * @property int $id
 * @property string|null $openid
 * @property string|null $type
 * @property int|null $pic_txt_id
 * @property int|null $member_id
 * @property string|null $text
 * @property string|null $media_path
 * @property string|null $status
 * @property string|null $result
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\MemberPicTxt onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereMediaPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt wherePicTxtId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\MemberPicTxt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\MemberPicTxt withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\MemberPicTxt withoutTrashed()
 * @mixin \Eloquent
 */
	class MemberPicTxt extends \Eloquent {}
}

namespace App\Http\Models\Wechat{
/**
 * App\Http\Models\Wechat\Menu
 *
 * @property int $id
 * @property string|null $path 路径
 * @property string|null $name 名称
 * @property int|null $level 层级
 * @property bool|null $is_root 是否根节点
 * @property int|null $sort 排序号
 * @property bool|null $is_open 是否开启
 * @property string|null $type
 * @property string|null $keyword
 * @property string|null $appid
 * @property string|null $pagepath
 * @property string|null $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\Menu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereAppid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereIsOpen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereIsRoot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereKeyword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu wherePagepath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\Menu whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\Menu withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\Menu withoutTrashed()
 * @mixin \Eloquent
 */
	class Menu extends \Eloquent {}
}

namespace App\Http\Models\Wechat{
/**
 * App\Http\Models\Wechat\PicTxt
 *
 * @property int $id
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Http\Models\Wechat\Material[] $materials
 * @property-read int|null $materials_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\PicTxt onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\PicTxt whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\PicTxt withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\PicTxt withoutTrashed()
 * @mixin \Eloquent
 */
	class PicTxt extends \Eloquent {}
}

namespace App\Http\Models\Wechat{
/**
 * App\Http\Models\Wechat\WechatSetting
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $appid
 * @property string|null $appsecret
 * @property string|null $token
 * @property string|null $platform 公众号名称
 * @property string|null $mchid
 * @property string|null $wx_pay_key 支付秘钥
 * @property string $apiclient_key
 * @property string $apiclient_cert
 * @property string|null $verify_file_txt 验证文件内容
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\WechatSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereApiclientCert($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereApiclientKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereAppid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereAppsecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereMchid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereVerifyFileTxt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Http\Models\Wechat\WechatSetting whereWxPayKey($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\WechatSetting withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Http\Models\Wechat\WechatSetting withoutTrashed()
 * @mixin \Eloquent
 */
	class WechatSetting extends \Eloquent {}
}

namespace App{
/**
 * App\Permission
 *
 * @property int $id
 * @property string|null $path
 * @property string|null $name
 * @property string|null $icon
 * @property string|null $color
 * @property string|null $page_url
 * @property int|null $is_root
 * @property int|null $level
 * @property int|null $sort
 * @property string|null $api_name
 * @property string|null $api_url 接口列表
 * @property string|null $type
 * @property string|null $relations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Permission onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereApiName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereApiUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereIsRoot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission wherePageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereRelations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Permission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Permission withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Permission withoutTrashed()
 * @mixin \Eloquent
 */
	class Permission extends \Eloquent {}
}

namespace App{
/**
 * App\Role
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $desc
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Role onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Role withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Role withoutTrashed()
 * @mixin \Eloquent
 */
	class Role extends \Eloquent {}
}

namespace App{
/**
 * App\User
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $desc
 * @property string|null $mobile
 * @property string|null $area
 * @property string|null $address
 * @property bool $isopen
 * @property string|null $password
 * @property string|null $type
 * @property string|null $preferences
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereIsopen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePreferences($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\User withoutTrashed()
 * @mixin \Eloquent
 */
	class User extends \Eloquent implements \Tymon\JWTAuth\Contracts\JWTSubject {}
}

