<template>
	<div class="echarts">
		<IEcharts :option="bing" :loading="loading" @ready="onReady" @click="onClick"></IEcharts>
		<div class="title">调用日志</div>
		<el-table
		:data="tableData3"
		border
		style="width: 100%"
		height="800">
			<el-table-column
			prop="created_at"
			label="日期"
			width="180">
			</el-table-column>
			<el-table-column
			prop="names"
			label="接口名称"
			width="200">
			</el-table-column>
			<el-table-column
			prop="api_names"
			label="method"
			width="260">
			</el-table-column>
			<el-table-column
			prop="ip"
			label="ip"
			width="160">
			</el-table-column>
			<el-table-column
			prop="app_id"
			label="调用接口的应用"
			width="160">
			</el-table-column>
			<el-table-column
			prop="note"
			label="说明"
			width="140">
			</el-table-column>
		</el-table>
		<div style="text-align: center">
			<el-button type="primary" :loading="loadData" @click="loadMore()">点击加载更多...</el-button>
		</div>
	</div>
</template>

<script type="text/babel">
  import IEcharts from 'vue-echarts-v3/src/full.vue';
  import axios from '../api/http'
  import apiUrl from '../api/index'
  export default {
    name: 'view',
    components: {
      IEcharts
    },
    props: {},
    data: () => ({
	    loading: false,
      bing: {
        title: {
          text: '拓源API平台今日访问统计',
          subtext: '',
          x: 'center'
        },
        tooltip: {
          trigger: 'item',
          formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        toolbox: {
          show: true,

        },
        legend: {
          orient: 'vertical',
          left: 'left',
          data: []
        },
        series: [
          {
            name:'访问来源',
            type:'pie',
            radius: ['50%', '70%'],
            avoidLabelOverlap: false,
            label: {
              normal: {
                show: false,
                position: 'center'
              },
              emphasis: {
                show: true,
                textStyle: {
                  fontSize: '30',
                  fontWeight: 'bold'
                }
              }
            },
            labelLine: {
              normal: {
                show: false
              }
            },
            data:[]
          }
        ]
      },
      tableData3: [],
	    nextPageUrl: apiUrl.applogs,
      loadData: false
    }),
    created () {
      axios.default.get(apiUrl.appcount).then( (res) => {
        console.log(res)
	      if(res.data.status == 2000){
          let legend_data = []
		      
		      for(let i in res.data.success.methods) {
            legend_data.push(res.data.success.methods[i])
		      }
		      
          this.bing.legend.data = legend_data
		      this.bing.series[0].data = res.data.success.data
		      
	      }
      } ).catch( err => {
        console.log(err)
      } )

      this.loadLogs(true)
    },
    methods: {
      doRandom() {
        const that = this;
        let data = [];
        for (let i = 0, min = 5, max = 99; i < 6; i++) {
          data.push(Math.floor(Math.random() * (max + 1 - min) + min));
        }
        that.loading = !that.loading;
        that.bar.series[0].data = data;
      },
      onReady(instance) {
        console.log(instance);
      },
      onClick(event, instance, echarts) {
        console.log(arguments);
      },
      loadLogs(first){
        axios.default.get(this.nextPageUrl).then( (res) => {
          console.log(res)
					if(res.data.status == 2000){
            if(first){
              this.tableData3 = res.data.success.data
            }else{
              this.tableData3 = this.tableData3.concat(res.data.success.data)
            }
            this.nextPageUrl = res.data.success.next_page_url
            
          }else{
					  
					}
          this.loadData = false
        } ).catch( err => {
          console.log(err)
        } )
	    },
      loadMore () {
	      this.loadData = true
	      this.loadLogs(false)
      }
    }
  };
</script>

<style scoped>
	.echarts {
		width: 1100px;
		height: 400px;
		margin: 50px auto;
	}
	.title{
		height: 46px;
		line-height: 45px;
		font-size: 18px;
		font-weight: 600;
		background: #eee;
		padding: 5px;
		margin-bottom: 15px;
	}
</style>
