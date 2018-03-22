<template>
	<div>
	<el-card class="box-card">
		<div class="plu-tool" style="cursor: pointer" @click="dialogFormVisible = true">
			<span class="add-icon el-icon-plus"></span>
			<span class="add-text">添加应用</span>
		</div>
		<div class="plu-tool plu-list" v-for="(item, index) in appLists">
			<div class="imgs">
				<img :src="item.app_thumb" alt="">
			</div>
			<div class="cont">
				<el-form label-width="120px">
					<el-form-item label="应用名称：">
						{{item.app_name}}
						<span class="el-icon-edit" style="display:inline; margin-left: 12px; cursor: pointer"></span>
					</el-form-item>
					<el-form-item label="APPID：">
						{{item.app_id}}
					</el-form-item>
					<el-form-item label="AppSecret：">
						<span class="el-icon-time" style="height: 36px; line-height: 36px;display:block;color: #20A0FF;cursor: pointer" @click="showAppSecret(item.app_secret)">查看</span>
					</el-form-item>
					<el-form-item label="创建时间：">
						{{item.created_at}}
					</el-form-item>
				</el-form>
				<el-button-group style="float: right">
					<el-button type="primary" icon="edit" @click="updateInfo(index)"></el-button>
					<el-button type="primary" icon="delete" @click="deleteApps(item.id, index)"></el-button>
				</el-button-group>
			</div>
		</div>
	</el-card>
	<el-dialog title="添加应用" :visible.sync="dialogFormVisible">
		<el-form :model="form" :rules="rules" ref="ruleForm">
			<el-form-item label="应用名称" :label-width="formLabelWidth">
				<el-input v-model="form.app_name" auto-complete="off"></el-input>
			</el-form-item>
			<el-form-item label="应用图标" :label-width="formLabelWidth">
				<el-upload
					class="avatar-uploader"
					action="/api/upload/?dir=app_icons"
					:show-file-list="false"
					:on-success="handleAvatarSuccess"
					:on-preview="handlePreview"
					:before-upload="beforeAvatarUpload"
					:headers="uploadHeaders">
						<img v-if="imageUrl" :src="imageUrl" class="avatar">
						<i v-else class="el-icon-plus avatar-uploader-icon"></i>
				</el-upload>
				<el-input type="hidden" v-model="form.app_thumb"></el-input>
			</el-form-item>
			<el-form-item label="应用描述" :label-width="formLabelWidth">
				<el-input type="textarea" v-model="form.app_desc"></el-input>
			</el-form-item>
			<el-form-item label="AppSecret" :label-width="formLabelWidth">
				<el-input v-model="form.app_secret" readOnly="true" auto-complete="off"></el-input>
				<el-button type="primary" size="mini" @click="createSecret()">重新生成</el-button>
			</el-form-item>
			<el-form-item label="应用状态" :label-width="formLabelWidth">
				<el-switch
					v-model="form.status"
					on-color="#13ce66"
					off-color="#ff4949">
				</el-switch>
			</el-form-item>
		</el-form>
		<div slot="footer" class="dialog-footer">
			<el-button @click="dialogFormVisible = false">取 消</el-button>
			<el-button type="primary" @click="_submit('ruleForm')">确 定</el-button>
		</div>
	</el-dialog>
		
		<el-dialog title="添加应用" :visible.sync="updatedialogFormVisible">
			<el-form :model="updateData" :rules="rules" ref="ruleFormupdate">
				<el-form-item label="应用名称" :label-width="formLabelWidth">
					<el-input v-model="updateData.app_name" auto-complete="off"></el-input>
				</el-form-item>
				<el-form-item label="应用图标" :label-width="formLabelWidth">
					<el-upload
					class="avatar-uploader"
					action="/api/upload/?dir=app_icons"
					:show-file-list="false"
					:on-success="handleUpdateAvatarSuccess"
					:on-preview="handleUpdatePreview"
					:before-upload="beforeUpdateAvatarUpload"
					:headers="uploadHeaders">
						<img v-if="updateData.app_thumb" :src="updateData.app_thumb" class="avatar">
						<i v-else class="el-icon-plus avatar-uploader-icon"></i>
					</el-upload>
					<el-input type="hidden" v-model="updateData.app_thumb"></el-input>
				</el-form-item>
				<el-form-item label="应用描述" :label-width="formLabelWidth">
					<el-input type="textarea" v-model="updateData.app_desc"></el-input>
				</el-form-item>
				<el-form-item label="AppSecret" :label-width="formLabelWidth">
					<el-input v-model="updateData.app_secret" readOnly="true" auto-complete="off"></el-input>
					<el-button type="primary" size="mini" @click="createSecretUpdate()">重新生成</el-button>
				</el-form-item>
				<el-form-item label="应用状态" :label-width="formLabelWidth">
					<el-switch
					v-model="updateData.status"
					on-color="#13ce66"
					off-color="#ff4949">
					</el-switch>
				</el-form-item>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button @click="updatedialogFormVisible = false">取 消</el-button>
				<el-button type="primary" @click="_submitUpdate('ruleFormupdate')">确 定</el-button>
			</div>
		</el-dialog>
		
		<el-dialog
		title="AppSerret"
		:visible.sync="dialogVisible"
		size="small"
		:before-close="handleClose">
			<span>{{app_secret}}</span>
		</el-dialog>
		<el-dialog
		title="提示"
		:visible.sync="deltedialogVisible"
		size="tiny"
		:before-close="handleClose">
			<span>是否确定要删除这个应用？</span>
			<span slot="footer" class="dialog-footer">
    <el-button @click="deltedialogVisible = false">取 消</el-button>
    <el-button type="primary" @click="confirmDelete()">确 定</el-button>
  </span>
		</el-dialog>
	</div>
</template>
<script>
	import apiUrl from '../api'
	import { mapGetters } from 'vuex'
	import axios from '../api/http'
	import { randomWord } from '../utils'
	import * as types from '../store/types'
  export default {
    data() {
      return {
        dialogFormVisible: false,
        dialogVisible: false,
        deltedialogVisible: false,
        updatedialogFormVisible: false,
        imageUrl:'',
        appLists: [],
        value2: true,
        app_secret: '',
        uploadHeaders: {},
        deleteId: 0,
        deleteIndex: 0,
        form: {
          app_name: '',
          app_thumb: '',
          app_secret: '',
          app_desc: '',
          status: 1
        },
        updateData:{
        
        },
        rules:{
          app_name: [
            { required: true, message: '请输入应用名称', trigger: 'blur' },
            { min: 3, max: 5, message: '长度在 3 到 5 个字符', trigger: 'blur' }
          ],
          app_secret: [
            { required: true, message: '请生成AppSecret', trigger: 'blur' },
          ]
        },
        formLabelWidth: '120px'
      };
    },
    mounted(){
      //console.log(this.getuserInfo)
    },
    created () {
      let _this = this
	    this.uploadHeaders = {
        Authorization: 'Bearer ' + this.$store.state.token
      }
      if(!this.getUserInfo){
        axios.default.post(apiUrl.userdetail).then(res=>{
          this.$store.commit(types.USER, res.data.success)
        }).catch(err => {
          console.log(err)
        })
      }
	    axios.default.post(apiUrl.apps, {'userId': this.$store.state.user.id}).then( res => {
	      if(res.data.status == 2000){
          _this.appLists = res.data.success
	      }
	    }).catch( err => {
	      console.log(err)
	    })
    },
    methods: {
      handleAvatarSuccess(res, file) {
        if(res.status == 2000){
          this.imageUrl = '/'+res.success;
          this.form.app_thumb = '/'+res.success;
        }
      },
      handlePreview(file){
      
      },
      beforeAvatarUpload(file) {
        const isJPG = file.type === 'image/jpeg';
        const isLt2M = file.size / 1024 / 1024 < 2;

        if (!isLt2M) {
          this.$message.error('上传头像图片大小不能超过 2MB!');
        }
        return isJPG && isLt2M;
      },
      handleClose (){
        this.dialogVisible = false
      },
      handleUpdateAvatarSuccess(res, file) {
        if(res.status == 2000){
          this.imageUrl = '/'+res.success;
          this.form.app_thumb = '/'+res.success;
        }
      },
      handleUpdatePreview(file){

      },
      beforeUpdateAvatarUpload(file) {
        const isJPG = file.type === 'image/jpeg';
        const isLt2M = file.size / 1024 / 1024 < 2;

        if (!isLt2M) {
          this.$message.error('上传头像图片大小不能超过 2MB!');
        }
        return isJPG && isLt2M;
      },
      handleUpdateClose (){
        this.updatedialogFormVisible = false
      },
      updateInfo(index){
        console.log(index)
        this.updatedialogFormVisible = true
        this.updateData = this.appLists[index]
      },
      showAppSecret (secret) {
        this.dialogVisible = true
	      this.app_secret = secret
	    },
	    createSecret(){
        let secret = randomWord(false, 64);
        this.form.app_secret = secret.toUpperCase()
	    },
      createSecretUpdate(){
        let secret = randomWord(false, 64);
        this.updateData.app_secret = secret.toUpperCase()
      },
	    _submit(formName){
        this.dialogFormVisible = false  // 关闭弹框
        this.$refs[formName].validate((valid) => {
          let data = this.form
	        data['user_id'] = this.getuserInfo['id']
          if (valid) {
            axios.default.post(apiUrl.saveApps, data).then( res => {
              if(res.data.status == 2000){
                this.appLists.splice(0, 0, data)
              }
            } ).catch( err => {
              console.log(err)
            })
          } else {
            console.log('error submit!!');
            return false;
          }
        });
	    },
      _submitUpdate(formName){
        this.updatedialogFormVisible = false  // 关闭弹框
        this.$refs[formName].validate((valid) => {
          let data = this.updateData
          data['user_id'] = this.getuserInfo['id']
          if (valid) {
            axios.default.post(apiUrl.saveApps, data).then( res => {
              if(res.data.status == 2000){
                this.$message({
                  message: '更新成功！',
                  type: 'success'
                });
              }
            } ).catch( err => {
              console.log(err)
            })
          } else {
            console.log('error submit!!');
            return false;
          }
        });
      },
      deleteApps(id, index){
	      this.deltedialogVisible = true;
	      this.deleteId = id
	      this.deleteIndex = index
      },
      confirmDelete(){
        let data = {'id': this.deleteId}

        axios.default.post(apiUrl.deleteApps, data).then( res => {
          if(res.data.success == 1){
            this.appLists.splice(this.deleteIndex, 1)
            this.deltedialogVisible = false;
          }
        }).catch( err => {
          console.log(err)
        })
      }
    },
    computed: mapGetters({
      getuserInfo: 'getUser'
    })
  };
</script>
<style lang="scss">
	.box-card{
	margin-top: 15px;
	}
	.plu-tool{
		width: 320px;
		min-height: 388px;
		overflow: hidden;
		border: 1px solid #ddd;
		float: left;
		margin-left: 15px;
		margin-bottom: 15px;
		border-radius: 8px;
		padding: 15px;
		.imgs{
			height: 120px;
			width: 100%;
			overflow: hidden;
			img{
				width: 100%;
			}
		}
		span{
			display: block;
			text-align: center;
		}
		.add-icon{
			height: 200px;
			width: 320px;
			line-height: 200px;
			text-align: center;
			font-size: 30px;
		}
	}
	
	.avatar-uploader .el-upload {
		border: 1px dashed #d9d9d9;
		border-radius: 6px;
		cursor: pointer;
		position: relative;
		overflow: hidden;
	}
	.avatar-uploader .el-upload:hover {
		border-color: #20a0ff;
	}
	.avatar-uploader-icon {
		font-size: 28px;
		color: #8c939d;
		width: 178px;
		height: 178px;
		line-height: 178px;
		text-align: center;
	}
	.avatar {
		width: 178px;
		height: 178px;
		display: block;
	}
</style>
