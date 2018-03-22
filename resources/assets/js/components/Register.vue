<template>
	<el-form :model="ruleForm2" :rules="rules2" ref="ruleForm2" label-position="left" label-width="80px" class="demo-ruleForm login-container">
		<h3 class="title">API平台注册</h3>
		<el-form-item prop="name" label="用户名">
			<el-input type="text" v-model="ruleForm2.name" auto-complete="off" placeholder="用户名"></el-input>
		</el-form-item>
		<el-form-item prop="email" label="邮箱">
			<el-input type="text" v-model="ruleForm2.email" auto-complete="off" placeholder="email"></el-input>
		</el-form-item>
		<el-form-item prop="password" label="密码">
			<el-input type="password" v-model="ruleForm2.password" auto-complete="off" placeholder="密码"></el-input>
		</el-form-item>
		<el-form-item prop="c_password" label="确认密码">
			<el-input type="password" v-model="ruleForm2.password_confirmation" auto-complete="off" placeholder="密码"></el-input>
		</el-form-item>
		<el-form-item style="width:100%;">
			<el-button type="primary" style="width:100%;" @click.native.prevent="handleSubmit2" :loading="logining">注册</el-button>
			<!--<el-button @click.native.prevent="handleReset2">重置</el-button>-->
			<router-link to="/login" class="register">立即登录！</router-link>
		</el-form-item>
	</el-form>
</template>
<script>
	import axios from '../api/http'
	import apiUrl from '../api/index'
	export default {
    data() {
      var validatePass2 = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请再次输入密码'));
        } else if (value !== this.ruleForm2.password) {
          callback(new Error('两次输入密码不一致!'));
        } else {
          callback();
        }
      };
      return {
        logining: false,
        ruleForm2: {
          name: '',
          email: '',
          password: '',
          password_confirmation: ''
        },
        rules2: {
          name: [
            { required: true, message: '请输入账号', trigger: 'blur' },
            //{ validator: validaePass }
          ],
          email: [
            { required: true, message: '请输入邮箱', trigger: 'blur' },
            //{ validator: validaePass }
          ],
          password: [
            { required: true, message: '请输入密码', trigger: 'blur' },
            //{ validator: validaePass2 }
          ],
          password_confirmation: [
            { validator: validatePass2, trigger: 'blur' }
	        ]
        },
        checked: true
      };
    },
		methods: {
      handleReset2() {
        this.$refs.ruleForm2.resetFields();
      },
      handleSubmit2 () {
        var _this = this;
        this.$refs.ruleForm2.validate((valid) => {
          console.log(valid)
          if (valid) {
            this.logining = true;
            axios.default.post(apiUrl.register, this.ruleForm2).then( res => {
              if(res.data.status == 2000){
                this.$router.push({
                  path: '/login'
                })
              }
            }).catch( err => {
              console.log(err)
            })
            //NProgress.start();
          } else {
            console.log('error submit!!');
            return false;
          }
        });
      }
		}
	}
</script>
<style lang="scss" scoped>
	.login-container {
		/*box-shadow: 0 0px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);*/
		-webkit-border-radius: 5px;
		border-radius: 5px;
		-moz-border-radius: 5px;
		background-clip: padding-box;
		margin: 180px auto;
		width: 350px;
		padding: 35px 35px 15px 35px;
		background: #fff;
		border: 1px solid #eaeaea;
		box-shadow: 0 0 25px #cac6c6;
		.title {
			margin: 0px auto 40px auto;
			text-align: center;
			color: #505458;
		}
		.remember {
			margin: 0px 0px 35px 0px;
		}
	}
	.register{
		text-decoration: none;
		color: #333;
		display: block;
		text-align: right;
	}
</style>
