

let masterPassword = "";
let cache = {

}

chrome.browserAction.setBadgeText({text: 'L'})

chrome.runtime.onMessage.addListener(
  function(request, sender, sendResponse) {
    console.log(request);

   	if (request.action == 'password') {
   		masterPassword = request.password
   		
      	sendResponse({message: "Acknowledged"});

      	chrome.browserAction.setBadgeText({text: '...'})

      	chrome.tabs.query({active: true}, function(tabs) {
		  chrome.tabs.sendMessage(tabs[0].id, {action: "ReloadContentScript"});
		});
   	}

   	if (request.action == 'ClearCache') {
   		cache = {}
   		sendResponse({message: "Acknowledged"});
   	}

      if (request.action == 'GetPassword') {
         if (sender.tab) {
            sendResponse({password: false})
            return true
         }

         sendResponse({password: masterPassword})
         return true
      }

   	if (request.action == 'GetRecords') {

   		if (!masterPassword) {
   			sendResponse({response: []})
   			return true
   		}

   		let url = request.url.replace('https://', '').replace('http://', '').split('/')[0]

   		if (cache[url] && new Date().getTime() - cache[url].time < 10*60*60*1000) {
            chrome.browserAction.setBadgeText({text: `${cache[url].data.length}`, tabId: sender.tab.id})
   			sendResponse({data: cache[url].data})
   			return true
   		} else {
   			cache[url] = null
   		}

   		fetch("http://127.0.0.1:8000/api/GetRecords", {
   			"credentials":"include",
   			"headers": {
				"Content-Type": "application/x-www-form-urlencoded;charset=UTF-8"
			},
   			"body":"master_password=" + encodeURIComponent(masterPassword) + "&url=" + encodeURIComponent(url),
   			"method":"POST",
   			"mode":"cors"
   		})
   		.then((response) => { 
   			return response.json()
   		})
   		.then((data)=>{
   			chrome.browserAction.setBadgeText({text: `${data.length}`, tabId: sender.tab.id})
   			cache[url] = {
   				data,
   				time: new Date().getTime()
   			}
   			sendResponse({data})
   		})
   		return true
   	}
  });