

var pm = {
	vue: false,
	interval: false,
	addTogglers(){

		var hasPassword = (document.querySelectorAll("input[type='password']").length > 0)? true: false

			if (!hasPassword) {
				return false
			}

		$('input[type="password"], input[type="text"], input[type="email"]').each(function(){
			  	if (!$(this).hasClass('pmanager__input')) {
			  		$(this).addClass('pmanager__input')
			  		$(`<div class="pwd-toggler">Insert</div>`).insertAfter($(this))
			  	}
			  	
			  })

			$('.pwd-toggler').off().click(function(){
				window.this_input = $(this).prev()
				var offsets = $(this).offset()
				$('#pmanager').css({
					display: 'block',
					top: offsets.top,
					left: offsets.left
				})
			})

	},
	initManager(response){
		$('body').append(`

			<div id="pmanager">
			    <ul id="example-1" v-if="!current_variant">
				  <li v-for="item in variants" @click="current_variant = item; autoInsert(item)">
				    {{ item.data.label }}<br>
				    <i>{{ item.data.username }} {{ item.data.email }}</i>
				  </li>
				</ul>

				<div class="current_variant" v-if="current_variant">
					<div v-if="current_variant.data.email">
						<p>{{ current_variant.data.email }}</p>
						<button @click="insert(current_variant.data.email)">Insert Email</button>
					</div>

					<div v-if="current_variant.data.username">
						<p>{{ current_variant.data.username }}</p>
						<button @click="insert(current_variant.data.username)">Insert username</button>
					</div>

					<div v-if="current_variant.data.password">
						
						<button @click="insert(current_variant.data.password)">Insert password</button>
					</div>

					<a href="#" @click="current_variant = false">Назад</a>
				</div>

				<a href="#" @click="close">Закрыть</a>
			</div>

		`)

		this.vue = new Vue({
		  el: '#pmanager',
		  data: {
		    variants: response.data,
		    current_variant: false,
		  },
		  methods: {
		  	close(e=false){
		  		if (e) e.preventDefault()
		  		$('#pmanager').hide()
		  	},
		  	insert(text){
		  		$(window.this_input).val(text)
		  		this.close()
		  	},
		  	autoInsert(variant){
		  		
		  		var found = false

		  		$container = $(window.this_input).closest('form')

		  		if (!$container.length) {
		  			$container = $(window.this_input).parent()
		  		}

		  		//Email
		  		if (variant.data.email) {
		  			if ($container.find('[type="email"]').length) found = true
		  			$container.find('[type="email"]').val(variant.data.email)
		  		}

		  		if (variant.data.username) {
		  			$container.find('[type="text"]').each(function(){
		  				var name = $(this).attr('name')
		  				if (name.includes('login') || name.includes('username')) {
		  					$(this).val(variant.data.username)
		  					found = true
		  				}
		  			})
		  		}

		  		if (variant.data.password) {
		  			if ($container.find('[type="password"]').length) found = true
		  			$container.find('[type="password"]').val(variant.data.password)
		  		}

		  		if (found) this.close()

		  	}
		  }
		})
	},
	init(){
		var that = this
		
		chrome.runtime.sendMessage({action: "GetRecords", url: location.href,}, function(response, a, b) {
		  	if (!response || !response.data || !response.data.length) return false
		  	that.initManager(response)

			that.interval = setInterval(function(){
		  		that.addTogglers();
		  	}, 1000)
		});
		
	},
	reload(){
		
		if (this.vue)
			this.vue.$destroy();
		if (this.interval)
			clearInterval(this.interval)

		this.init()
	}

}




chrome.extension.sendMessage({}, function(response) {
	var readyStateCheckInterval = setInterval(function() {
	if (document.readyState === "complete") {
		clearInterval(readyStateCheckInterval);		

		pm.init()

		chrome.runtime.onMessage.addListener(
		  function(request, sender, sendResponse) {
		   
		    if (request.action == "ReloadContentScript") {
		    	pm.reload()
		    }
		});
		// ----------------------------------------------------------

	}
	}, 10);
});