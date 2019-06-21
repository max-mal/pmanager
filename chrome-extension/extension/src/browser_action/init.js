window.addEventListener("error", function() {
    var e = document.getElementById("nuxt-loading");
    e && (e.className += " error")
})