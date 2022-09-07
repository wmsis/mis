<template>
    <div>
        <div class="demo-upload-list" v-for="item in uploadList">
            <template v-if="item.status === 'finished'">
                <img :src="item.url">
                <div class="demo-upload-list-cover">
                    <Icon type="ios-eye-outline" @click.native="handleView(item)"></Icon>
                    <Icon type="ios-trash-outline" @click.native="handleRemove(item)"></Icon>
                </div>
            </template>
            <template v-else>
                <Progress v-if="item.showProgress" :percent="item.percentage" hide-info></Progress>
            </template>
        </div>
        <Upload
            v-show="uploadList.length < maxImageCount"
            ref="uploadRef"
            :show-upload-list="false"
            :default-file-list="defaultList"
            :on-success="handleSuccess"
            :format="format"
            :max-size="2048"
            :on-format-error="handleFormatError"
            :on-exceeded-size="handleMaxSize"
            :before-upload="handleBeforeUpload"
            :multiple="false"
            type="drag"
            :headers="headers"
            :action="uploadUrl"
            name ="file"
            style="display: inline-block;width:58px;">
            <div style="width: 58px;height:58px;line-height: 58px;">
                <Icon type="ios-camera" size="20"></Icon>
            </div>
        </Upload>
        <Modal title="大图" v-model="visible">
            <img :src="bigImgUrl" v-if="visible" style="width: 100%">
        </Modal>
    </div>
</template>
<script>
    import Vue from 'vue';
    import { mapGetters, mapState } from 'vuex';
    import {
        Upload,
        Modal,
        Icon
    } from 'iview';

    Vue.component('Upload', Upload);
    Vue.component('Modal', Modal);
    Vue.component('Icon', Icon);

    export default {
        computed: {
            ...mapState([
                'host',
                'token'
            ])
        },
        props: {
            uploadUrl: {
                required: true,
            },
            maxImageCount: {
                required: false,
                default: 1
            },
            format: {
                required: false,
                default: ()=>{
                    return ['jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'gif', 'GIF'];
                }
            },
            id: {
                required: false,
                default: '',
            },
            initialData: {
                required: false,
                default: ''
            }
        },
        data () {
            return {
                defaultList: [
                ],
                bigImgUrl: '',
                visible: false,
                uploadList: [
                ],
                headers: {}
            }
        },
        mounted () {
            this.headers = {
                Authorization: `Bearer ${this.token.access_token}`
            };
            this.uploadList = this.$refs.uploadRef.fileList;

            if (this.initialData != '') {
                this.init(this.initialData);
            }
        },
        methods: {
            getImageNames: function() {
                const fileList = this.uploadList;
                let imageNames = "";
                for (let i = 0; i < fileList.length; i++) {
                    imageNames += fileList[i].name;
                    if (i < fileList.length - 1) {
                        imageNames += ",";
                    }
                }
                return imageNames;
            },
            init: function(data) {
                if (!data) {
                    return;
                }
                if (Array.isArray(data)) {
                    for (let item of data) {
                        this.uploadList.push({
                            status: 'finished',
                            name: item,
                            url: this.host + item
                        });
                    }
                    return;
                }

                this.uploadList.push({
                    status: 'finished',
                    name: data,
                    url: this.host + data
                });
            },
            reset: function() {
                this.$refs.uploadRef.fileList.splice(0, this.$refs.uploadRef.fileList.length);
            },
            handleView (item) {
                this.bigImgUrl = item.url;
                this.visible = true;
            },
            handleRemove (file) {
                this.uploadList.splice(this.uploadList.indexOf(file), 1);
                this.$emit("listenChange", this.getImageNames(), this.id);
            },
            handleSuccess (response, file) {
                let code = response.code;
                if (code != 0) {
                    this.showMessage(response.message);
                    return;
                }
                let fileName = response.data.path;
                file.url = this.host + fileName;
                file.name = fileName;
                if (this.uploadList.length < 1) {
                    this.uploadList.push(file);
                }
                this.$emit("listenChange", this.getImageNames(), this.id);
            },
            handleFormatError (file) {
                let str = this.format.join("、")
                this.showMessage("文件格式不正确, 请上传" + str + "类型的图片");
            },
            handleMaxSize (file) {
                this.showMessage("上传文件不能大于2M, " + file.name);
            },
            handleBeforeUpload () {
                const check = this.uploadList.length < this.maxImageCount;
                if (!check) {
                    this.showMessage("最多上传五张照片");
                }
                return check;
            }
        }
    }
</script>
<style>
    .demo-upload-list{
        display: inline-block;
        width: 60px;
        height: 60px;
        text-align: center;
        line-height: 60px;
        border: 1px solid transparent;
        border-radius: 4px;
        overflow: hidden;
        background: #fff;
        position: relative;
        box-shadow: 0 1px 1px rgba(0,0,0,.2);
        margin-right: 4px;
    }
    .demo-upload-list img{
        width: 100%;
        height: 100%;
    }
    .demo-upload-list-cover{
        display: none;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,.6);
    }
    .demo-upload-list:hover .demo-upload-list-cover{
        display: block;
    }
    .demo-upload-list-cover i{
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        margin: 0 2px;
    }
</style>
