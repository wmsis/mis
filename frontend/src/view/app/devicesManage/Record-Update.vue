<template>
    <Modal
            v-model="shows.update"
            title="修改检修记录"
            :width="width"
            :mask-closable="false"
            :footer-hide="true"
            @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="80">
            <Row>
                <Col span="11">
                    <FormItem label="日期">
                        <DatePicker v-model="selectedDate" type="date" @on-change="dateTimeChange" style="width: 100%;"
                                    clearable></DatePicker>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">
                    <FormItem prop="kind" label="性质">
                        <Select placeholder="缺陷性质" v-model="formData.kind" filterable @on-change="chgKind">
                            <Option value="normal">设备正常</Option>
                            <Option value="general">一类缺陷（一般）</Option>
                            <Option value="worse">二类缺陷（较严重）</Option>
                            <Option value="serious">三类缺陷（重大）</Option>
                        </Select>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="11">
                    <FormItem prop="supervisor" label="负责人">
                        <Input v-model="formData.supervisor" placeholder="请输入负责人"></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">
                    <FormItem prop="members" label="工作组成员">
                        <Input v-model="formData.members" placeholder="请输入工作组成员"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="24">
                    <FormItem prop="prev_status" label="检修前状况">
                        <Input v-model="formData.prev_status" placeholder="请输入检修前状况"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="24">
                    <FormItem prop="maintenance_content" label="检修内容">
                        <Input v-model="formData.maintenance_content" placeholder="请输入检修内容"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                <Col span="24">
                    <FormItem prop="test_status" label="试运情况">
                        <Input v-model="formData.test_status" placeholder="请输入试运情况"></Input>
                    </FormItem>
                </Col>
            </Row>
            <Row>
                  <Col span="24">
                      <FormItem prop="after_status" label="检修后状况">
                          <Input v-model="formData.after_status" placeholder="请输入检修后状况"></Input>
                      </FormItem>
                  </Col>
              </Row>
            <Row>
                <Col span="11">
                    <FormItem prop="recorder" label="记录人">
                        <Input v-model="formData.recorder" placeholder="请输入记录人"></Input>
                    </FormItem>
                </Col>
                <Col span="2">
                    <div :style="{padding: '5px 5px 5px 5px'}"></div>
                </Col>
                <Col span="11">
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
        RadioGroup,
        Radio,
        Select,
        Option
    } from 'iview';

    Vue.component('Form', Form);
    Vue.component('Input', Input);
    Vue.component('FormItem', FormItem);
    Vue.component('Button', Button);
    Vue.component('Modal', Modal);
    Vue.component('Row', Row);
    Vue.component('Col', Col);
    Vue.component('RadioGroup', RadioGroup);
    Vue.component('Radio', Radio);
    Vue.component('Select', Select);
    Vue.component('Option', Option);

    export default {
        props: ['shows', 'dev'],
        data() {
            return {
                model: true,
                width: '880px',
                selectedDate: new Date(),
                loading: false,
                formData: {
                    id: '',
                    date: '',
                    kind: 'normal',
                    supervisor: '',
                    evaluation: '',
                    members: '',
                    prev_status: '',
                    maintenance_content: '',
                    test_status: '',
                    after_status: '',
                    recorder: '',
                    equipment_id: ''
                },
                ruleValidate: {
                    equipment_id: [
                        {required: true, message: '设备id不能为空', trigger: 'blur'}
                    ]
                }
            }
        },
        methods: {
            visibleChange: function (visible) {
                if (visible) {
                    this.selectedDate = new Date(this.dev.date);
                    this.formData.id = String(this.dev.id);
                    this.formData.date = this.dev.date;
                    this.formData.kind = this.dev.kind;
                    this.formData.supervisor = this.dev.supervisor;
                    this.formData.evaluation = this.dev.evaluation;
                    this.formData.members = this.dev.members;
                    this.formData.prev_status = this.dev.prev_status;
                    this.formData.maintenance_content = this.dev.maintenance_content;
                    this.formData.test_status = this.dev.test_status;
                    this.formData.after_status = this.dev.after_status;
                    this.formData.recorder = this.dev.recorder;
                    this.formData.equipment_id = String(this.dev.equipment_id);
                } else {
                    this.shows.update = false;
                }
            },
            handleSubmit: function (name) {
                let vm = this;
                vm.$refs[name].validate((valid) => {
                    if (!valid) {
                        return;
                    }
                    vm.loading = true;
                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.equipments.equipmentRecordUpdate.replace('{equipment_id}', vm.formData.equipment_id).replace('{id}', vm.formData.id),
                        params: vm.formData,
                        success: function () {
                            vm.handleReset('formRef');
                            vm.$emit("listenChildClose");
                            vm.shows.update = false;
                            vm.loading = false;
                        },
                        fail() {
                            vm.loading = false;
                        }
                    });
                });
            },
            handleReset(name) {
                this.$refs[name].resetFields();
            },
            chgKind(){

            },
            dateTimeChange(datetime, type) {
                this.formData.date = datetime;
            }
        }
    }
</script>

<style>

</style>
