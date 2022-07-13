<template>
    <Modal
            v-model="shows.add"
            title="新增标签关联"
            :width="width"
            :footer-hide="true"
            class="tag-assign-model"
            @on-visible-change="visibleChange">
        <Spin size="small" fix v-if="isLoading"></Spin>
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              label-position="top">
            <br/>
            <FormItem prop="front_tagId" label="前端标识" style="text-align: left;">
                <Input v-model="formData.front_tagId" placeholder="请输入前端标识ID"></Input>
            </FormItem>
            <FormItem prop="historian_tagId" label="后端标识" style="text-align: left;">
                <Select placeholder="请输入搜索TAG关键字" filterable multiple v-if="!tagLoading"
                        remote
                        :remote-method="remoteTagAll"
                        :loading="loadingTagAll" v-model="formData.historian_tagId">
                    <Option v-for="(item, index) in tagOptionAll" :value="item.id"
                            v-if="item.tag_name != 'fake_historian_tag'"
                            :key="index">{{ item.tag_name }}
                    </Option>
                </Select>
            </FormItem>
            <FormItem label="是否函数" style="text-align: left;">
                <i-switch v-model="isFunc"/>
            </FormItem>
            <FormItem prop="func" label="计算函数" style="text-align: left;" v-if="isFunc">
                <Input v-model="formData.func" placeholder="请输入计算函数"></Input>
            </FormItem>
            <FormItem label="是否颜色变换" style="text-align: left;">
                <i-switch v-model="isColor"/>
            </FormItem>
            <FormItem prop="color_list" label="值映射" v-if="isColor"
                      style="text-align: left; position: relative;">
                <Button type="primary" class="add-row" size="small" @click="addRow()">新增行</Button>
                <Table stripe border :columns="columns" :data="datalist" size="small">
                    <template slot-scope="{ row, index }" slot="min">
                        <Input size="small" v-model="datalist[index].min" placeholder="请输入最小值"
                               style="width: 50px;"></Input>
                    </template>
                    <template slot-scope="{ row, index }" slot="max">
                        <Input size="small" v-model="datalist[index].max" placeholder="请输入最大值"
                               style="width: 50px;"></Input>
                    </template>
                    <template slot-scope="{ row, index }" slot="color_list">
                        <div v-for="(item, idx) in datalist[index].color_list" :key="idx" class="color-list">
                            <div :style="{backgroundColor: item.val}" class="color-block"></div>
                            <div class="color-input">
                                <Input size="small" v-model="item.val" placeholder="请输入颜色值"></Input>
                            </div>
                            <Icon size="16" type="ios-trash-outline" class="delete" @click="destroyColor(index, idx)"/>
                        </div>
                    </template>
                    <template slot-scope="{ row, index }" slot="action">
                        <Button type="primary" size="small" @click="addColor(index)">增加颜色</Button>
                        <Button type="error" size="small" @click="deleteRow(index)">删除该行</Button>
                    </template>
                </Table>
            </FormItem>
            <FormItem prop="name" label="名称" v-if="isFunc" style="text-align: left;">
                <Input v-model="formData.name" placeholder="请输入名称"></Input>
            </FormItem>
            <FormItem prop="description" label="描述" style="text-align: left;">
                <Input v-model="formData.description" placeholder="请输入描述信息"></Input>
            </FormItem>
            <FormItem :style="{'text-align': 'left'}">
                <Button type="primary" @click="handleSubmit('formRef')">提交</Button>
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
        Table,
        Select,
        Option,
        Spin,
        Switch
    } from 'iview';

    Vue.component('Form', Form);
    Vue.component('Input', Input);
    Vue.component('FormItem', FormItem);
    Vue.component('Button', Button);
    Vue.component('Modal', Modal);
    Vue.component('Table', Table);
    Vue.component('Select', Select);
    Vue.component('Option', Option);
    Vue.component('Spin', Spin);
    Vue.component('iSwitch', Switch);

    export default {
        props: ['shows'],
        data() {
            return {
                model: true,
                width: '880px',
                isLoading: true,
                tagLoading: false,
                loadingTagAll: false,
                tagOptionAll: [],
                isFunc: false,
                isColor: false,
                columns: [
                    {
                        title: '最小值',
                        slot: 'min'
                    },
                    {
                        title: '最大值',
                        slot: 'max'
                    },
                    {
                        title: '颜色值',
                        slot: 'color_list',
                        minWidth: 100,
                    },
                    {
                        title: '操作',
                        slot: 'action',
                        minWidth: 120,
                        align: 'center'
                    }
                ],
                datalist: [],
                formData: {
                    front_tagId: '',
                    description: '',
                    func: '',
                    historian_tagId: [],
                    name: '',
                    measure: ''
                },
                tagList: [],
                ruleValidate: {
                    front_tagId: [
                        {required: true, message: '前端标识id不能为空', trigger: 'blur'}
                    ],
                    historian_tagId: [
                        {required: true, message: 'historian id不能为空', trigger: 'blur'}
                    ]
                }
            }
        },
        methods: {
            visibleChange: function (visible) {
                if (!visible) {
                    this.shows.add = false;
                } else {
                    let SELF = this;
                    SELF.tagLoading = true;
                    SELF.ajax({
                        method: 'GET',
                        url: SELF.$request.setting.alltag,
                        params: {},
                        success: function (data) {
                            SELF.tagList = data;
                            SELF.isLoading = false;
                            SELF.tagLoading = false;
                        },
                        fail() {
                            SELF.tagLoading = false;
                        }
                    })
                }
            },
            initTagAll() {
                let arr = [];
                if (this.formData.historian_tagId.length > 0) {
                    this.tagList.map(item => {
                        for (let id of this.formData.historian_tagId) {
                            if (item.id == id) {
                                arr.push({
                                    id: item.id,
                                    tag_name: item.tag_name
                                });
                                break;
                            }
                        }
                    });
                }
                return arr;
            },
            remoteTagAll(query) {
                if (query !== '') {
                    this.loadingTagAll = true;
                    setTimeout(() => {
                        this.loadingTagAll = false;
                        const list = this.tagList.map(item => {
                            return {
                                id: item.id,
                                tag_name: item.tag_name
                            };
                        });
                        this.tagOptionAll = list.filter(item => item.tag_name.toLowerCase().indexOf(query.toLowerCase()) > -1);
                    }, 200);
                } else {
                    this.tagOptionAll = this.initTagAll();
                }
            },
            addColor(index) {
                let datalist = JSON.parse(JSON.stringify(this.datalist));
                datalist[index].color_list.push({
                    val: '#ffff00'
                });
                this.datalist = datalist;
            },
            destroyColor(index1, index2) {
                let datalist = JSON.parse(JSON.stringify(this.datalist));
                datalist[index1].color_list.splice(index2, 1);
                this.datalist = datalist;
            },
            addRow() {
                let datalist = JSON.parse(JSON.stringify(this.datalist));
                let row = {
                    min: 0,
                    max: 0,
                    color_list: [
                        {
                            val: "#ff0000"
                        }
                    ]
                };
                datalist.push(row);
                this.datalist = datalist;
            },
            deleteRow(index) {
                let datalist = JSON.parse(JSON.stringify(this.datalist));
                datalist.splice(index, 1);
                this.datalist = datalist;
            },
            handleSubmit: function (name) {
                let vm = this;
                vm.$refs[name].validate((valid) => {
                    if (!valid) {
                        return;
                    }
                    let rqData = {};
                    rqData.front_tag_id = vm.formData.front_tagId;
                    let tagArr = vm.formData.historian_tagId;
                    rqData.historian_tag_id = tagArr.join(',');
                    rqData.func = vm.formData.func;
                    rqData.name = vm.formData.name;
                    rqData.measure = vm.formData.measure;
                    if (vm.formData.description.length > 0) {
                        rqData.description = vm.formData.description;
                    }
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.Boiler.FrontAdd,
                        data: rqData,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.add = false;
                        }
                    });
                });
            },
            handleReset(name) {
                this.$refs[name].resetFields();
            }
        }
    }
</script>

<style scoped lang="scss">
    .tag-assign-model /deep/ .ivu-form-item {
        margin-bottom: 12px !important;
    }

    .color-list {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        position: relative;

        & .color-block {
            width: 50px;
            height: 20px;
        }

        .color-input {
            width: 150px;
            margin-left: 15px;
        }

        & .delete {
            cursor: pointer;
            position: absolute;
            right: -7px;
            top: 50%;
            transform: translateY(-50%);
        }
    }

    .add-row {
        position: absolute;
        top: -29px;
        right: 0px;
    }
</style>
