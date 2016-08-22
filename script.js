(function() {
	var f=function() {
		setTimeout(function() {
			var ads=document.getElementsByClassName("adsbox");
			if(ads[0].offsetHeight===0) {
				document.body.className=document.body.className+" adblock";
			}
		}, 0);
	};
	window.addEventListener("DOMContentLoaded", f);
})();
