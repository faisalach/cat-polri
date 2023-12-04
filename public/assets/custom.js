let base = '/assets';
function notification(text,type,layout) {
	$.getScript( base + "/js/jquery.jgrowl.min.js", function( data, textStatus, jqxhr ) {
		var alert_type = 'bg-info';
		if(typeof type!= 'undefined'){
			if(type == 'error'){
				alert_type = 'bg-danger';
			}
		}
		$.jGrowl(text, {
			header: type.ucwords(),
			theme: 'alert-styled-'+layout+' '+alert_type
		});
	});
}

String.prototype.ucwords = function() {
	str = this.toLowerCase();
	return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
	function(s){
		return s.toUpperCase();
	});
};

function confirmation_alert(url, remove_id, title, text, id_refresh, customConfirmButtonText) {
	if(typeof customConfirmButtonText != 'undefined'){
		if(customConfirmButtonText == ''){
			customConfirmButtonText = 'Submit';
		}
	}
	$.getScript( base + "/js/sweetalert2.min.js", function( data, textStatus, jqxhr ) {
		Swal.fire({
			title: title,
			text: text,
			icon: "warning",
			showCancelButton: true,
			confirmButtonColor: "#EF5350",
			confirmButtonText: customConfirmButtonText,
			cancelButtonText: "Cancel",
			showLoaderOnConfirm : true,
			allowOutsideClick: () => !Swal.isLoading(),
			preConfirm : function(){
				Swal.showLoading(Swal.getConfirmButton());
				Swal.showLoading(Swal.getCancelButton());
				return $.ajax({
					url: url,
					type: "POST",
					dataType: "json",
					data: {
						_token 	: $("[name=_token]").val(),
						remove_id:remove_id,
					},
				}).done(function(data_return) {
					return data_return;
				});
			}
		}).then((result) => {
			if (result.isConfirmed) {
				var data_return = result.value;
				if(data_return.status == 'success'){
					Swal.fire({
						title: "Success!",
						text: data_return.message,
						confirmButtonColor: "#66BB6A",
						icon: "success"
					});
					if(typeof id_refresh != 'undefined'){
						if(id_refresh != ''){
							$("#"+id_refresh).trigger('click');
						}
					}
					if(typeof data_return.redirect_url != 'undefined'){
						if(data_return.redirect_url != ''){
							window.location.href = data_return.redirect_url;
						}
					}
					if(typeof data_return.remove_content_id != 'undefined'){
						if(data_return.remove_content_id != ''){
							$("#"+data_return.remove_content_id ).slideUp();
							$("#"+data_return.remove_content_id ).remove();
						}
					}
				} else {
					Swal.fire({
						title: "Error",
						text: data_return.message,
						confirmButtonColor: "#2196F3",
						icon: "error"
					});
				}
			}
		});
	});
}


function alert(title, text,type = 'info') {
	$.getScript( base + "/js/sweetalert2.min.js", function( data, textStatus, jqxhr ) {
		Swal.fire({
			title: title,
			text: text,
			// confirmButtonColor: "#2196F3",
			icon: type
		});
	});
}