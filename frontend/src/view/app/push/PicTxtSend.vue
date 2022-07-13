<template>
    <div>
        <BaseTable
                ref="table"
                :columns="columns"
                :pageUrl="pageUrl"
                :shows="shows"
                :buttons="[]"
                :height="true"
                @listenBtnClick="btnClick"
                @selectionChange="selectionChange"
                :showSearchBtn="false">
            <template slot="info" slot-scope="{ row }">
                <div class="wx-info">
                    <img :src="row.wechats.length > 0 ? row.wechats[0].headimgurl : defalutImg"  width="50" height="50"/>
                    <span>{{row.wechats.length > 0 ? row.wechats[0].nickname : '无名'}}</span>
                </div>
            </template>
            <template slot="attention" slot-scope="{ row }">
                <div>
                    <Tag color="green" v-if="row.wechats.length > 0 && row.wechats[0].subscribe">已关注</Tag>
                    <Tag color="red" v-else>未关注</Tag>
                </div>
            </template>
            <div slot="rightSide">
                <h2>图文内容</h2>
                <div class="input-row">
                    <Select v-model="formData.type">
                        <Option v-for="item in types" :value="item.value" :key="item.value">{{ item.label }}</Option>
                    </Select>
                </div>
                <div class="input-row" v-if="formData.type == 'text'">
                    <Input v-model="formData.text" type="textarea" placeholder="请输入内容" :rows="5"/>
                </div>
                <div class="input-row" v-if="formData.type == 'pictxt'">
                    <Select placeholder="请选择图文" v-model="formData.pic_txt_id">
                        <Option v-for="item in pictxtList" :value="item.id" :key="item.id">{{ item.name }}</Option>
                    </Select>
                </div>
                <div class="input-row">
                    <Button type="primary" long @click.stop="onSend" v-if="isButtonExist('user-message-send')">发送</Button>
                </div>
                <div class="input-row">
                    <Button type="error" long @click.stop="onSendAll" v-if="isButtonExist('user-message-send')">全部发送</Button>
                </div>
                <div class="tip-box mt15">
                    <p>触发交互的时机：</p>
                    <p>1、用户发送信息</p>
                    <p>2、点击自定义菜单（仅有点击推事件、扫码推事件、扫码推事件且弹出“消息接收中”提示框这3种菜单类型是会触发客服接口的）</p>
                    <p>3、关注公众号</p>
                    <p>4、扫描二维码</p>
                    <p>5、支付成功</p>
                    <p>6、开启公众号上报地理位置，打开公众号即交互</p>
                    <p>推送建议：</p> <p>每天1次推送最多不要超过3次，以免造成用户反感。</p>
                </div>
            </div>
        </BaseTable>
    </div>
</template>
<script>
    import Vue from 'vue';
    import {
        Select,
        Option,
        Button,
        Tag
    } from 'iview';

    Vue.component('Select', Select);
    Vue.component('Option', Option);
    Vue.component('Button', Button);
    Vue.component('Tag', Tag);

    import BaseTable from '@/components/baseTable';

    export default {
        components: { BaseTable },
        data() {
            return {
                pageUrl: this.$request.member.page,
                shows: {},
                defalutImg: require('@/assets/img/default@2x.jpg'),
                pictxtList: [],
                rows: [],
                types: [
                    {
                        label: '文本',
                        value: 'text'
                    },
                    {
                        label: '图文',
                        value: 'pictxt'
                    }
                ],
                formData: {
                    type: 'text',
                    text: undefined,
                    pic_txt_id: undefined,
                },
                columns: [
                    {
                        type: 'selection',
                        fixed: 'left',
                        width: 34
                    },
                    {
                        title: '微信信息',
                        slot: 'info',
                        align: 'left',
                        fixed: 'left',
                        minWidth: 150
                    },
                    {
                        title: '用户名',
                        key: 'username',
                        minWidth: 100
                    },
                    {
                        title: '联系方式',
                        key: 'mobile',
                        minWidth: 115
                    },
                    {
                        title: '最后一次交互',
                        key: 'last_interaction_time',
                        minWidth: 160
                    },
                    {
                        title: '是否关注',
                        slot: 'attention',
                        width: 90
                    }
                ]
            }
        },
        created() {
            this.all();
        },
        methods: {
            all(){
                this.ajax({
                    method: 'get',
                    url: this.$request.picTxt.all,
                    success: (data) => {
                        this.pictxtList = data;
                    }
                });
            },
            btnClick(method) {

            },
            onSend() {
                if (this.rows.length == 0) {
                    return this.showMessage('请选择要推送的用户');
                }
                var ids = this.rows.map(item => item.id);
                this._sendData(ids);
            },
            onSendAll() {
                var rows = this.$refs.table.pager.data || [];
                var ids = rows.map(item => item.id);
                this._sendData(ids);
            },
            _sendData(ids = []) {
                var params = this._validateForm();
                if (params) {
                    let vm = this;
                    this.ajax({
                        method: 'post',
                        data: { ...params, member_ids: ids.join(',')},
                        url: this.$request.picTxt.sendPicText,
                        success: (data) => {
                            vm.showMessage('操作成功', 'success');
                        }
                    });
                }
            },
            _validateForm() {
                var { type, text, pic_txt_id } = this.formData;
                var error = '';
                if (type == 'text') {
                    if (!!text) {
                        return { text, type };
                    }
                    error = '请添加推送内容';
                } else if ( type == 'pictxt') {
                    if (!!pic_txt_id) {
                        return { type, pic_txt_id };
                    }
                    error = '请选择要推送的图文内容';
                }
                this.showMessage(error);
                return false;

            },
            selectionChange(rows) {
                this.rows = [...rows];
            }
        }
    }
</script>
<style scoped  lang="scss">
    .mt15 {
        margin-top: 15px;
    }
    .input-row {
        padding-top: 10px;
    }
    .tip-box {
        background-color: #fff;
        font-size: 14px;
        margin-bottom: 15px;
        color: #606266;
        border-radius: 4px;
        border: 1px solid #ebeef5;
        transition: .3s;
        padding: 20px;
        & p {
            line-height: 25px;
            font-size: 14px;
        }
    }
    .wx-info {
        padding: 8px 0;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        & img{
            display: inline-block;
            height: 30px;
            width: 30px;
            border-radius: 3px;
        }
        span {
            margin-left: 5px;
        }
    }
</style>
