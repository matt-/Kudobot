

Kudo = {
	
	selected_users: new Array(),
	
	init:function() {
			
	} ,
	
	kudo_user:function(id,name) {
		this.id = id;
		this.name = name;
	},
	
	append_user:function(obj,li) {
 
		$("selected").innerHTML = "<img src='" + li.childNodes[0].getAttribute("src") + "'>";
		$("selected").style.visibility = "visible";
		$("selected_user").value = li.id;
		Kudo.cache_list(li);
		Kudo.render_user_list();
	} ,
	
	rekudo:function(id,name) {
		Kudo.selected_users.push(new Kudo.kudo_user(id,name));
		
	}
	,
	cache_list : function(li) {
		add = true;
		for(i=0;i < Kudo.selected_users.length; i++) {
			if(Kudo.selected_users[i].id == li.id) {
				add = false;
				break;
			}
		}
		if(add) {
			Kudo.selected_users.push(new Kudo.kudo_user(li.id,$("user_name_" + li.id).value));
		}
	}
	,
	
	remove:function(index) {
		temp = new Array();
		for(i=0;i < Kudo.selected_users.length; i++) {
			if(i != index) {
				temp.push(Kudo.selected_users[i]);
			}
		}
		Kudo.selected_users = temp;
		$("user_" + index).remove();
	}
	,
	render_user_list:function() {
		$("user_list").innerHTML = "";

		for(i=0;i < Kudo.selected_users.length; i++) {
			
			a = document.createElement('a');
			a.setAttribute("id","user_" + i);
	 		
			img = document.createElement('img');
			img.setAttribute("src","/images/users/" + Kudo.selected_users[i].id + ".png");
			a.appendChild(img);
			
			txt = document.createElement('span')
			name = Kudo.selected_users[i].name
			if(name.length > 12) {
				name = Kudo.selected_users[i].name.substr(0,11) + "...";
			}
			txt.innerHTML = " " + name
			a.appendChild(txt);
			
			div = document.createElement('div');
			div.setAttribute("class","x");
			div.setAttribute("title","Remove");
			div.setAttribute("onclick","Kudo.remove(" + i + ")");
			div.innerHTML = "x";
			a.appendChild(div);
			
			
			hidden = document.createElement('input');
			hidden.setAttribute('type','hidden');
			hidden.setAttribute('name','selected_users[]');
			hidden.setAttribute('value',Kudo.selected_users[i].id)
				
			a.appendChild(hidden);
			
			$("user_list").appendChild(a);

		}
	
		$("user_list").scrollTop = $("user_list").scrollHeight;
		$("alias").value = '';
	} ,
	
	validate:function() {
		if(Kudo.selected_users.length < 1) {
			alert('KudoBot Says: "You did not select anyone to send your Kudos to."');
			return false;
		}
		if($("kudo_reason").value == '') {
			alert('KudoBot Says: "You must enter a reason for the Kudo."');
			return false;
		}
		
		
	}
	
	
	
}


// <a id="user_1" class="user">
// 	<img src="/images/users/2.png"> Gregg Spiridellis
// 	<div class="x" title="Remove">x</div>
// </a>
// <div class="sep">&nbsp;</div>