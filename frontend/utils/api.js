import appConfig from '../nuxt.config'
import axios from 'axios'
import qs from 'querystring'

const api = {

	call: function(method, apiMethod, data){

		let config = {
			method: method,
			url: appConfig.apiUrl + apiMethod,
			data: (method == 'post') ? qs.stringify(data) : null,
			withCredentials: true,
		}

		if (method == 'post') {
			config.headers = {'Content-Type': 'application/x-www-form-urlencoded'}
		}

		return axios(config);
	},

	auth: function(login, password, auth){
		return this.call('post', 'Login', {
			login,
			password,
		}).then((response)=>{
			if (!response.data.success) {
	          return response.data.message
	        } else {
	          auth.setUser(response.data)
	          return true
	        }
		})
	},
	checkAuth: function(auth){
		this.call('get', 'CheckLogin', false).then((response)=>{
			if (response.data.auth) {
				auth.setUser(response.data)	
			} else {
				auth.setUser(false)
			}
		})
	},
	getProjects: function(){
		return this.call('get', 'GetProjects', false)
	},
	createProject: function(data){
		return this.call('post', 'CreateProject', data).then((response)=>{
			if (!response.data.success) {
	          return response.data.message
	        } else {
	          return true
	        }
		})
	},
	deleteProject: function(id){
		return this.call('post', 'DeleteProject', {
			project_id: id
		})
	},
	getProject: function(id){
		return this.call('post', 'GetProject', {
			project_id: id
		})
	},
	updateProject: function(data){
		return this.call('post', 'UpdateProject', data)
	},
	getRecords: function(data){
		return this.call('post', 'GetRecords', data)
	}

}

export default api