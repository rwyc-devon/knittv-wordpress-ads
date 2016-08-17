(function(){
	var listener=function(e) {
		if(!this.value) {
			this.parentNode.removeChild(this);
		}
	};
	var getValues=function(widget) {
		var selects=widget.getElementsByTagName("select");
		var values=[];
		for(i=0; i<selects.length; i++) {
			if(selects[i].value) {
				values.push(selects[i].value);
			}
		}
		return values;
	}
	var appendClone=function(select) {
		//make clone
		var c=select.cloneNode(true);
		//reset value
		c.value="";
		//make a new name attribute (most fragile part of this whole thing!)
		var matches=c.name.match('^(.+)\\[ad(\\d\\d)\\]$');
		var n=(("000"+(parseInt(matches[2], 10)+1))).substr(-2);
		c.name=matches[1]+"[ad"+(n)+"]";
		c.onchange=lastListener;
		return c;
	};
	var lastListener=function(e) {
		if(this.value) {
			this.onkeypress=listener;
			this.onpaste=listener;
			this.onchange=listener;
			this.parentNode.appendChild(appendClone(this));
		}
	};
	var addListeners=function() {
		var widgets=document.querySelectorAll("[id*=knittvads] .adschooser");
		for(i=0; i<widgets.length; i++) {
			var widget=widgets[i];
			var inputs=widgets[i].getElementsByTagName("input");
			for(ii=0; ii<inputs.length-1; ii++) {
				inputs[ii].onfocus=function(){this.select()};
				inputs[ii].onkeypress=listener;
				inputs[ii].onpaste=listener;
				inputs[ii].onchange=listener;
			}
			inputs[ii].onkeypress=lastListener;
			inputs[ii].onpaste=lastListener;
			inputs[inputs.length-1].onchange=lastListener;
		}
	};
	setInterval(addListeners, 500); //TODO: call this only when the form reloads.
})();
