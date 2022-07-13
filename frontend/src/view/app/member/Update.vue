<template>
    <Modal
        v-model="shows.update"
        title="推送设置"
        :width="width"
        :mask-closable="false"
        :footer-hide="true"
        @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :label-width="80">
            <Row>
                <Col span="11" >
                    <FormItem  label="微信昵称">
                        <Input v-model="memberData.nickname" placeholder="请输入用户名称" disabled></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11" >
                    <FormItem prop="member_id"  label="微信ID">
                        <Input v-model="formData.member_id"  disabled></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
              <Col span="24">
                  <FormItem prop="boiler_check_ids" label="推送指标">
                    <Transfer :data="allTags" :render-format="renderTitle" :list-style="check_list_style" :titles="transferTitles"  :target-keys="formData.boiler_check_ids" filterable :filter-method="tagFilterd" @on-change="tagChange"></Transfer>
                  </FormItem>
              </Col>
            </Row>

            <FormItem :style="{'text-align': 'right'}">
                <Button type="primary" @click="handleSubmit('formRef')" :loading="loading">提交</Button>
                <Button @click="handleReset('formRef')" style="margin-left: 8px">重置</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script>
    import Vue from 'vue'
    import {
        Form,
        FormItem,
        Input,
        Button,
        Modal,
        Row,
        Col,
        Select,
        Option,
        Transfer,
        Tooltip
    } from 'iview';

    Vue.component('Form', Form);
    Vue.component('Input', Input);
    Vue.component('FormItem', FormItem);
    Vue.component('Button', Button);
    Vue.component('Modal', Modal);
    Vue.component('Row', Row);
    Vue.component('Col', Col);
    Vue.component('Select',Select);
    Vue.component('Option',Option);
    Vue.component('Transfer',Transfer);
    Vue.component('Tooltip',Tooltip);
    import {unixtimefromat} from '@/utils/utils';
    import membUtils from "./js/membUtils";

    export default {
        props: ['shows', 'dev','allTags'],
        data () {
            return {
                model: true,
                width: '880px',
                loading: false,
                memberData:{},
                transferTitles:['待推指标','已推指标'],
                formData: {
                    member_id:'',
                    boiler_check_ids:[]
                },
                check_list_style:{
                  width:'45%'
                }
            }
        },
      methods: {
            visibleChange: function(visible) {
                if (visible) {
                    let self = this;
                    let checks = self.dev.checks;
                    let checkIds = [];
                    self.$_.map(checks,function (ck) {
                        checkIds.push(ck.boiler_check_id.toString());
                    });
                    self.formData.boiler_check_ids=checkIds;
                    let wechat = self.dev.wechats[0];
                    self.formData.member_id=wechat.member_id.toString();
                    self.memberData=wechat;
                } else {
                    this.shows.update = false;
                }
            },
            tagFilterd(data,query){
              return data.label.indexOf(query) > -1;
            },
            tagChange(targetKeys, direction, moveKeys){
              this.formData.boiler_check_ids=targetKeys;
            },
            renderTitle(item){
              return `<span title="${item.label}">${item.label}</span>`
            },
            handleSubmit: function(name) {
                let vm = this;
                vm.$refs[name].validate((valid) => {
                    if (!valid) {
                        return;
                    }
                    let idsStr =vm.formData.boiler_check_ids.join(',');
                    let rq_params = {member_id:vm.formData.member_id,boiler_check_ids:idsStr};
                    vm.loading = true;
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.check.setCheckNotifi,
                        params: rq_params,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.update = false;
                            vm.loading = false;
                        },
                        fail(){
                            vm.loading = false;
                        }
                    });
                });
            },
            handleReset (name) {
                this.$refs[name].resetFields();
            }
        }
    }
</script>

<style>

</style>
