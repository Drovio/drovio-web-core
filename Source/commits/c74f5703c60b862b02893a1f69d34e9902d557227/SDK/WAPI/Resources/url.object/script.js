jq = jQuery.noConflict();

url = {
	getVar : function(name) {
		return url.getUrlVar(window.location.href, name);
	},
	getUrlVar : function(url, name) {
		var vars = {};
		var parts = url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
			if (key in vars){
				if (typeof vars[key] == "string")
					vars[key] = [vars[key]];
				vars[key] = jq.merge(vars[key],[value]);
			}else
				vars[key] = value;
		});
		
		if (typeof(name) != "undefined")
			return vars[name];
		
		return vars;
	},
	removeVar : function(hrf, vrb) {
		// If URL has no variables, return it
		if (hrf.indexOf("?") == -1)
			return hrf;
		
		// Split variables from URI
		var hr_splitted = hrf.split("?");
		var prefix = hr_splitted[0];
		var vrbles_sec = "?" + hr_splitted[1];
		
		// Remove variable using patterns
		var var_patt = new RegExp(vrb+"=(?=[^&]*)[^&]*[&*]|[&*]"+vrb+"=(?=[^&]*)[^&]*|^\\?"+vrb+"=(?=[^&]*)[^&]*$", "i");
		vrbles_sec = vrbles_sec.replace(var_patt, "");
		var result = prefix + vrbles_sec;
		
		return result;
	},
	redirect : function(url) {
		if (false) {// if site is not trusted, prompt user
			
		}
		else {// If site is trusted
			window.location = url;
			window.location.href = url;
		}
	},
	getPathname : function() {
		return encodeURIComponent(window.location.pathname);
	}
}