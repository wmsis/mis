<template>
    <Modal
        v-model="shows.modifyPassword"
        title="修改密码"
        :width="width"
        :mask-closable="false"
        :footer-hide="true"
        @on-visible-change="visibleChange">
        <Form ref="formRef"
              :model="formData"
              :rules="ruleValidate"
              :label-width="80">
            <FormItem prop="originalPassword" label="原密码">
                <Input type="password" v-model="formData.originalPassword" placeholder="原密码">
                </Input>
            </FormItem>
            <FormItem prop="newPassword" label="新密码">
                <Input type="password" v-model="formData.newPassword" placeholder="新密码">
                </Input>
            </FormItem>
            <FormItem prop="repeatedPassword" label="重复密码">
                <Input type="password" v-model="formData.repeatedPassword" placeholder="重复密码">
                </Input>
            </FormItem>

            <FormItem :style="{'text-align': 'right'}">
                <Button type="primary" @click="handleSubmit('formRef')">提交</Button>
                <Button @click="handleReset('formRef')" style="margin-left: 8px">重置</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script>
    import Vue from 'vue';
    import { mapGetters, mapState } from 'vuex';
    import {
        Modal,
        Form,
        FormItem,
        Input,
        Button
    } from 'iview';

    Vue.component('Modal', Modal);
    Vue.component('Form', Form);
    Vue.component('FormItem', FormItem);
    Vue.component('Input', Input);
    Vue.component('Button', Button);

    export default {
        props: ['shows'],
        data () {
            let checkRepeatedPassword = (rule, value, callback) => {
                let newPassword = this.formData.newPassword;
                let repeatedPassword = this.formData.repeatedPassword;
                if (newPassword != repeatedPassword) {
                    callback(new Error('密码不一致'));
                    return;
                }
                callback();
            };

            return {
                model: true,
                width: 400,
                formData: {
                    originalPassword: '',
                    newPassword: '',
                    repeatedPassword: ''
                },
                ruleValidate: {
                    originalPassword: [
                        { required: true, message: '原密码不能为空', trigger: 'blur' }
                    ],
                    newPassword: [
                        { required: true, message: '新密码不能为空', trigger: 'blur' }
                    ],
                    repeatedPassword: [
                        { required: true, message: '重复密码不能为空', trigger: 'blur'},
                        { validator: checkRepeatedPassword, trigger: 'blur'}
                    ]
                }
            }
        },
        methods: {
            visibleChange: function (visible) {
                if (visible) {

                } else {
                    this.shows.modifyPassword = false;
                }
            },
            handleSubmit: function(name) {
                let vm = this;
                vm.$refs[name].validate((valid) => {
                    if (!valid) {
                        return;
                    }

                    vm.ajax({
                        method: 'POST',
                        url: vm.$request.users.changePassword,
                        params: {
                            oldpwd: vm.formData.originalPassword,
                            newpwd: vm.formData.newPassword
                        },
                        success: function () {
                            vm.handleReset(name);
                            vm.shows.modifyPassword = false;
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
