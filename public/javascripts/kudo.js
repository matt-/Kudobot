document.observe("dom:loaded", function() {  });

Kudo = {
	
	init:function() {

	} ,
	
	append_user:function(obj,li) {
	
		$("selected").innerHTML = "<img src='" + li.childNodes[0].getAttribute("src") + "'>";
		$("selected").style.visibility = "visible";
		$("selected_user").value = li.id;
	}
}
