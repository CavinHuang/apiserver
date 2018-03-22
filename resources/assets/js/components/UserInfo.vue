<template>
	<div>
		<el-form ref="form" :model="getUserInfo" label-width="80px" class="userForm">
			<el-form-item label="用户名">
				<el-input v-model="getUserInfo.name"></el-input>
			</el-form-item>
			<el-form-item label="邮箱">
				<el-input v-model="getUserInfo.email"></el-input>
			</el-form-item>
			<el-form-item label="头像">
				<el-upload
					class="avatar-uploader"
					action="/api/upload/?dir=avatar"
					:show-file-list="false"
					:on-success="handleAvatarSuccess"
					:headers="uploadHeaders">
						<img v-if="getUserInfo.userimg" :src="getUserInfo.userimg" class="avatar">
						<i v-else class="el-icon-plus avatar-uploader-icon"></i>
				</el-upload>
				<el-dialog v-model="dialogVisible" size="tiny">
					<img width="100%" :src="imageUrl" alt="">
				</el-dialog>
				<el-input type="hidden" v-model="getUserInfo.userimg"></el-input>
			</el-form-item>
			<el-form-item label="创建时间">
				{{getUserInfo.created_at}}
			</el-form-item>
			<el-form-item>
				<el-button type="primary" @click="onSubmit">立即保存</el-button>
			</el-form-item>
		</el-form>
		
	</div>
</template>
<script>
	import {mapGetters} from 'vuex'
	import axios from '../api/http'
	import apiUrl from '../api/index'
  export default {
    data() {
      return {
        imageUrl: '',
        uploadHeaders: {},
        dialogVisible: false,
        deltedialogVisible: false
      }
    },
    mounted(){
    },
    created(){
      this.uploadHeaders = {
        Authorization: 'Bearer ' + this.$store.state.token
      }
      if(!this.getUserInfo.hasOwnProperty('name')){
        axios.default.post(apiUrl.userdetail).then( res => {
          console.log(res)
        } ).catch( err => {
          console.log(err)
        })
      }
    },
    methods: {
      onSubmit() {
        let data = this.getUserInfo
        axios.default.post(apiUrl.saveUserInfo, data).then( res => {
          console.log(res)
        } ).catch( err => {
          console.log(err)
        })
      },
      handleAvatarSuccess(res, file) {
        if(res.status == 2000){
          this.imageUrl = '/'+res.success;
          this.getUserInfo.userimg = '/'+res.success;
        }
      },
    },
    computed: mapGetters({
      getUserInfo: 'getUser'
    })
  }
</script>
<style lang="scss">
	.userForm{
		width: 60%;
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
		width: 140px;
		height: 140px;
		line-height: 140px;
		text-align: center;
	}
	.avatar {
		width: 140px;
		height: 140px;
		display: block;
	}
</style>
